<?php
/**
 * Server-only R2 configuration. Never persisted to WordPress options.
 *
 * @package Webbooks
 */

declare(strict_types=1);

namespace Webbooks\Media\ImageUpload;

final readonly class Configuration {
	public const PUBLIC_BASE_URL = 'https://data.webbooks.com.ua';

	public function __construct(
		public string $endpoint,
		public string $bucket,
		#[\SensitiveParameter] private string $access_key_id,
		#[\SensitiveParameter] private string $secret_access_key
	) {}

	public static function fromEnvironment(): self|\WP_Error {
		$read = static function ( string $name ): string {
			$value = defined( $name ) ? constant( $name ) : getenv( $name );
			return is_string( $value ) ? trim( $value ) : '';
		};
		$endpoint = rtrim( $read( 'WEBBOOKS_R2_ENDPOINT' ), '/' );
		$bucket   = $read( 'WEBBOOKS_R2_BUCKET' );
		$access   = $read( 'WEBBOOKS_R2_ACCESS_KEY_ID' );
		$secret   = $read( 'WEBBOOKS_R2_SECRET_ACCESS_KEY' );
		$public   = rtrim( $read( 'WEBBOOKS_R2_PUBLIC_BASE_URL' ), '/' );

		if ( ! preg_match( '~^https://[a-f0-9]{32}(?:\.[a-z0-9-]+)?\.r2\.cloudflarestorage\.com$~D', $endpoint )
			|| ! preg_match( '/^[a-z0-9][a-z0-9.-]{1,61}[a-z0-9]$/D', $bucket )
			|| '' === $access || '' === $secret
			|| ( '' !== $public && self::PUBLIC_BASE_URL !== $public ) ) {
			return UploadError::STORAGE_UNAVAILABLE->response();
		}

		return new self( $endpoint, $bucket, $access, $secret );
	}

	public function clientOptions(): array {
		return array(
			'endpoint'          => $this->endpoint,
			'pathStyleEndpoint' => true,
			'region'            => 'auto',
			'accessKeyId'       => $this->access_key_id,
			'accessKeySecret'   => $this->secret_access_key,
		);
	}

	public function __debugInfo(): array {
		return array( 'configured' => true );
	}
}
