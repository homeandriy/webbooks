<?php
/**
 * Validated original bytes and their content-addressed identity.
 *
 * @package Webbooks
 */

declare(strict_types=1);

namespace Webbooks\Media\ImageUpload;

final readonly class UploadedImage {
	public function __construct(
		public string $path,
		public string $sha256,
		public string $mime_type,
		public string $extension,
		public int $bytes,
		public int $width,
		public int $height
	) {}

	public function key(): string {
		return 'articles/images/sha256/' . substr( $this->sha256, 0, 2 ) . '/' . $this->sha256 . '.' . $this->extension;
	}

	/** Explicit allowlist: never serialize the temporary path or storage configuration. */
	public function payload( bool $created ): array {
		return array(
			'url'       => Configuration::PUBLIC_BASE_URL . '/' . $this->key(),
			'sha256'    => $this->sha256,
			'mime_type' => $this->mime_type,
			'bytes'     => $this->bytes,
			'width'     => $this->width,
			'height'    => $this->height,
			'created'   => $created,
		);
	}
}
