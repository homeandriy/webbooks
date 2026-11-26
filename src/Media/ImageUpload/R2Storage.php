<?php
/**
 * Atomic, streamed R2 writes and exact-key recovery after an ambiguous result.
 *
 * @package Webbooks
 */

declare(strict_types=1);

namespace Webbooks\Media\ImageUpload;

use AsyncAws\S3\S3Client;
use AsyncAws\S3\Input\PutObjectRequest;
use Psr\Log\NullLogger;
use Symfony\Component\HttpClient\HttpClient;

final class R2Storage implements ObjectStorage {
	private readonly S3Client $client;

	public function __construct( private readonly Configuration $configuration, ?S3Client $client = null ) {
		if ( ! method_exists( PutObjectRequest::class, 'setIfNoneMatch' ) ) {
			throw new \RuntimeException( 'The installed S3 client does not support conditional writes.' );
		}
		// Inject a non-retrying HTTP transport: a failed PUT is reconciled explicitly via HEAD.
		$this->client = $client ?? new S3Client(
			$configuration->clientOptions(),
			null,
			HttpClient::create(
				array(
					'verify_peer'   => true,
					'verify_host'   => true,
					'max_redirects' => 0,
					'timeout'       => 5,
					'max_duration'  => 25,
				)
			),
			new NullLogger()
		);
	}

	public static function fromEnvironment(): self|\WP_Error {
		$config = Configuration::fromEnvironment();
		if ( is_wp_error( $config ) ) {
			return $config;
		}
		if ( ! class_exists( S3Client::class ) || ! extension_loaded( 'simplexml' ) || ! extension_loaded( 'dom' ) ) {
			return UploadError::STORAGE_UNAVAILABLE->response();
		}
		try {
			return new self( $config );
		} catch ( \Throwable ) {
			return UploadError::STORAGE_UNAVAILABLE->response();
		}
	}

	public function store( UploadedImage $image ): bool|\WP_Error {
		$stream = @fopen( $image->path, 'rb' );
		if ( false === $stream ) {
			return UploadError::STORAGE_UNAVAILABLE->response();
		}
		try {
			$input = new PutObjectRequest(
				array(
					'Bucket'        => $this->configuration->bucket,
					'Key'           => $image->key(),
					'Body'          => $stream,
					'ContentLength' => $image->bytes,
					'ContentType'   => $image->mime_type,
					'CacheControl'  => 'public, max-age=31536000, immutable',
					'Metadata'      => array( 'sha256' => $image->sha256 ),
					'IfNoneMatch'   => '*',
				)
			);
			$headers = array_change_key_case( $input->request()->getHeaders(), CASE_LOWER );
			if ( ( $headers['if-none-match'] ?? '' ) !== '*' ) {
				return UploadError::STORAGE_UNAVAILABLE->response();
			}
			$result = $this->client->putObject( $input );
			if ( ! $result->resolve() ) {
				return $this->verifyExisting( $image );
			}
			return true;
		} catch ( \Throwable ) {
			// Covers 412 races, timeouts after commit and upstream failures without leaking diagnostics.
			return $this->verifyExisting( $image );
		} finally {
			fclose( $stream );
		}
	}

	private function verifyExisting( UploadedImage $image ): bool|\WP_Error {
		try {
			$head = $this->client->headObject(
				array(
					'Bucket' => $this->configuration->bucket,
					'Key'    => $image->key(),
				)
			);
			if ( ! $head->resolve() ) {
				return UploadError::STORAGE_UNAVAILABLE->response();
			}
			$metadata = array_change_key_case( $head->getMetadata(), CASE_LOWER );
			if ( ( $metadata['sha256'] ?? '' ) !== $image->sha256
				|| $head->getContentLength() !== $image->bytes
				|| $head->getContentType() !== $image->mime_type ) {
				return UploadError::OBJECT_CONFLICT->response();
			}
			return false;
		} catch ( \Throwable ) {
			// A negative HEAD does not prove an in-flight PUT failed. Never retry with an unconditional write.
			return UploadError::STORAGE_UNAVAILABLE->response();
		}
	}
}
