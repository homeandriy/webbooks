<?php
/**
 * The upload operation never lists, deletes or overwrites objects.
 *
 * @package Webbooks
 */

declare(strict_types=1);

namespace Webbooks\Media\ImageUpload;

interface ObjectStorage {
	/** Return true for creation, false for a verified duplicate, or a controlled error. */
	public function store( UploadedImage $image ): bool|\WP_Error;
}
