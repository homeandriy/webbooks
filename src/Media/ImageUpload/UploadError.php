<?php
/**
 * Stable integration errors without upstream diagnostics or credentials.
 *
 * @package Webbooks
 */

declare(strict_types=1);

namespace Webbooks\Media\ImageUpload;

enum UploadError: string {
	case INVALID_REQUEST     = 'webbooks_image_invalid_request';
	case TOO_LARGE           = 'webbooks_image_too_large';
	case UNSUPPORTED_TYPE    = 'webbooks_image_unsupported_type';
	case INVALID_DATA        = 'webbooks_image_invalid_data';
	case OBJECT_CONFLICT     = 'webbooks_image_object_conflict';
	case STORAGE_UNAVAILABLE = 'webbooks_image_storage_unavailable';

	public function response(): \WP_Error {
		[ $status, $message ] = match ( $this ) {
			self::INVALID_REQUEST     => array( 400, 'Send exactly one non-empty binary file in the multipart file field.' ),
			self::TOO_LARGE           => array( 413, 'The image exceeds the permitted byte size or pixel dimensions.' ),
			self::UNSUPPORTED_TYPE    => array( 415, 'Use multipart/form-data with a PNG, JPEG or WebP image.' ),
			self::INVALID_DATA        => array( 422, 'The image is damaged, incomplete or cannot be decoded.' ),
			self::OBJECT_CONFLICT     => array( 409, 'The existing object does not match the expected image metadata.' ),
			self::STORAGE_UNAVAILABLE => array( 503, 'Image storage is temporarily unavailable or not configured.' ),
		};

		return new \WP_Error( $this->value, $message, array( 'status' => $status ) );
	}
}
