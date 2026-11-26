<?php
/**
 * Validate original multipart image files before allocating a decoded raster.
 *
 * @package Webbooks
 */

declare(strict_types=1);

namespace Webbooks\Media\ImageUpload;

final class ImageValidator {
	public const MAX_BYTES     = 8388608;
	public const MAX_DIMENSION = 8192;
	public const MAX_PIXELS    = 20000000;

	private readonly \Closure $uploaded_file_check;

	/** The injected file check supports isolated tests; the REST route always uses PHP's upload check. */
	public function __construct( ?\Closure $uploaded_file_check = null ) {
		$this->uploaded_file_check = $uploaded_file_check ?? \Closure::fromCallable( 'is_uploaded_file' );
	}

	public function validate( array $files, string $content_type, int $content_length = 0 ): UploadedImage|\WP_Error {
		if ( strtolower( trim( explode( ';', $content_type, 2 )[0] ) ) !== 'multipart/form-data' ) {
			return UploadError::UNSUPPORTED_TYPE->response();
		}
		if ( array() === $files && $content_length > self::MAX_BYTES ) {
			// PHP can discard all upload fields after post_max_size is exceeded.
			return UploadError::TOO_LARGE->response();
		}
		if ( count( $files ) !== 1 || ! isset( $files['file'] ) || ! is_array( $files['file'] ) ) {
			return UploadError::INVALID_REQUEST->response();
		}
		$file = $files['file'];
		foreach ( array( 'name', 'type', 'tmp_name', 'error', 'size' ) as $key ) {
			if ( ! array_key_exists( $key, $file ) || ! is_scalar( $file[ $key ] ) ) {
				return UploadError::INVALID_REQUEST->response();
			}
		}
		if ( ! is_int( $file['error'] ) || ! is_int( $file['size'] ) || ! is_string( $file['tmp_name'] )
			|| ! is_string( $file['name'] ) || ! is_string( $file['type'] ) ) {
			return UploadError::INVALID_REQUEST->response();
		}
		if ( in_array( $file['error'], array( UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE ), true ) ) {
			return UploadError::TOO_LARGE->response();
		}
		$path = $file['tmp_name'];
		if ( UPLOAD_ERR_OK !== $file['error'] || ! is_file( $path ) || ! is_readable( $path ) || ! ( $this->uploaded_file_check )( $path ) ) {
			return UploadError::INVALID_REQUEST->response();
		}
		clearstatcache( true, $path );
		$bytes = filesize( $path );
		if ( false === $bytes || 0 === $bytes ) {
			return UploadError::INVALID_REQUEST->response();
		}
		if ( $bytes > self::MAX_BYTES ) {
			return UploadError::TOO_LARGE->response();
		}
		$stream = @fopen( $path, 'rb' );
		if ( false === $stream ) {
			return UploadError::INVALID_REQUEST->response();
		}
		try {
			$signature = fread( $stream, 12 );
			$format = match ( true ) {
				str_starts_with( $signature, "\x89PNG\r\n\x1a\n" ) => array( 'image/png', 'png', IMAGETYPE_PNG, 'imagecreatefrompng' ),
				str_starts_with( $signature, "\xff\xd8\xff" ) => array( 'image/jpeg', 'jpg', IMAGETYPE_JPEG, 'imagecreatefromjpeg' ),
				substr( $signature, 0, 4 ) === 'RIFF' && substr( $signature, 8, 4 ) === 'WEBP' => array( 'image/webp', 'webp', IMAGETYPE_WEBP, 'imagecreatefromwebp' ),
				default => null,
			};
			if ( null === $format ) {
				return UploadError::UNSUPPORTED_TYPE->response();
			}
			if ( ! function_exists( $format[3] ) || ! class_exists( \finfo::class ) ) {
				return UploadError::STORAGE_UNAVAILABLE->response();
			}
			$warning = false;
			set_error_handler( static function () use ( &$warning ): bool { $warning = true; return true; } );
			try {
				$dimensions = getimagesize( $path );
				$mime = ( new \finfo( FILEINFO_MIME_TYPE ) )->file( $path );
			} finally {
				restore_error_handler();
			}
			if ( false === $dimensions || $warning || $dimensions[2] !== $format[2] || $mime !== $format[0] ) {
				return UploadError::INVALID_DATA->response();
			}
			[ $width, $height ] = $dimensions;
			if ( $width < 1 || $height < 1 ) {
				return UploadError::INVALID_DATA->response();
			}
			if ( $width > self::MAX_DIMENSION || $height > self::MAX_DIMENSION || $width * $height > self::MAX_PIXELS ) {
				return UploadError::TOO_LARGE->response();
			}
			if ( ! $this->completeContainer( $stream, $format[1], $bytes ) ) {
				return UploadError::INVALID_DATA->response();
			}
			$memory_limit = wp_convert_hr_to_bytes( (string) ini_get( 'memory_limit' ) );
			if ( $memory_limit > 0 && memory_get_usage( true ) + $width * $height * 8 + 16777216 > $memory_limit ) {
				return UploadError::STORAGE_UNAVAILABLE->response();
			}
			$warning = false;
			set_error_handler( static function () use ( &$warning ): bool { $warning = true; return true; } );
			try {
				$decoded = ( $format[3] )( $path );
			} finally {
				restore_error_handler();
			}
			if ( ! $decoded instanceof \GdImage || $warning ) {
				return UploadError::INVALID_DATA->response();
			}
			if ( imagesx( $decoded ) !== $width || imagesy( $decoded ) !== $height ) {
				return UploadError::INVALID_DATA->response();
			}
			unset( $decoded );
			$sha256 = @hash_file( 'sha256', $path );
			if ( false === $sha256 ) {
				return UploadError::INVALID_DATA->response();
			}
			return new UploadedImage( $path, $sha256, $format[0], $format[1], $bytes, $width, $height );
		} catch ( \Throwable ) {
			return UploadError::INVALID_DATA->response();
		} finally {
			fclose( $stream );
		}
	}

	/** GD may recover truncated JPEGs; check terminal markers and PNG chunk CRCs as well. */
	private function completeContainer( mixed $stream, string $extension, int $bytes ): bool {
		if ( 'jpg' === $extension ) {
			return fseek( $stream, -2, SEEK_END ) === 0 && fread( $stream, 2 ) === "\xff\xd9";
		}
		if ( 'webp' === $extension ) {
			rewind( $stream );
			$header = fread( $stream, 12 );
			return strlen( $header ) === 12 && unpack( 'Vsize', substr( $header, 4, 4 ) )['size'] + 8 === $bytes;
		}
		if ( fseek( $stream, 8 ) !== 0 ) {
			return false;
		}
		$has_data = false;
		$first = true;
		while ( ftell( $stream ) < $bytes ) {
			$header = fread( $stream, 8 );
			if ( strlen( $header ) !== 8 ) {
				return false;
			}
			$length = unpack( 'Nlength', substr( $header, 0, 4 ) )['length'];
			$type = substr( $header, 4, 4 );
			if ( $length > $bytes - ftell( $stream ) - 4 || ( $first && ( 'IHDR' !== $type || 13 !== $length ) ) ) {
				return false;
			}
			$first = false;
			$crc = hash_init( 'crc32b' );
			hash_update( $crc, $type );
			$remaining = $length;
			while ( $remaining > 0 ) {
				$chunk = fread( $stream, min( 65536, $remaining ) );
				if ( false === $chunk || '' === $chunk ) {
					return false;
				}
				hash_update( $crc, $chunk );
				$remaining -= strlen( $chunk );
			}
			if ( fread( $stream, 4 ) !== hash_final( $crc, true ) ) {
				return false;
			}
			$has_data = $has_data || 'IDAT' === $type;
			if ( 'IEND' === $type ) {
				return 0 === $length && $has_data && ftell( $stream ) === $bytes;
			}
		}
		return false;
	}
}
