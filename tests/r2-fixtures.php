<?php
/**
 * Isolated image and signed-S3-transport fixtures. Never loads real R2 credentials.
 */

declare(strict_types=1);

use AsyncAws\S3\S3Client;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpClient\Exception\TransportException;
use Webbooks\Media\ImageUpload\Configuration;
use Webbooks\Media\ImageUpload\R2Storage;

function r2_test_assert( bool $condition, string $message ): void {
	if ( ! $condition ) {
		throw new RuntimeException( $message );
	}
	echo "PASS: $message\n";
}

function r2_test_error( mixed $result, int $status, string $code ): void {
	r2_test_assert( is_wp_error( $result ) && $result->get_error_code() === $code && $result->get_error_data()['status'] === $status, "$code returns $status" );
}

function r2_test_file( string $path ): array {
	return array( 'file' => array( 'name' => 'untrusted-name.html', 'type' => 'text/html', 'tmp_name' => $path, 'error' => UPLOAD_ERR_OK, 'size' => filesize( $path ) ) );
}

function r2_test_png_chunk( string $type, string $data ): string {
	return pack( 'N', strlen( $data ) ) . $type . $data . hash( 'crc32b', $type . $data, true );
}

function r2_test_dimensions( string $png, int $width, int $height ): string {
	$ihdr = pack( 'NN', $width, $height ) . substr( $png, 24, 5 );
	return substr( $png, 0, 8 ) . r2_test_png_chunk( 'IHDR', $ihdr ) . substr( $png, 33 );
}

/** A real SDK client over a deterministic transport; the conditional header is checked on the wire. */
final class R2TestTransport {
	public array $objects = array();
	public array $requests = array();
	public string $mode = 'normal';

	public function storage(): R2Storage {
		$config = new Configuration( 'https://' . str_repeat( 'a', 32 ) . '.r2.cloudflarestorage.com', 'isolated-bucket', 'isolated-access', 'isolated-secret' );
		$http = new MockHttpClient( array( $this, 'request' ) );
		return new R2Storage( $config, new S3Client( $config->clientOptions(), null, $http ) );
	}

	public function request( string $method, string $url, array $options ): MockResponse {
		$key = (string) wp_parse_url( $url, PHP_URL_PATH );
		$this->requests[] = array( 'method' => $method, 'key' => $key );
		$headers = array();
		foreach ( $options['headers'] as $header ) {
			$pair = explode( ':', $header, 2 );
			$headers[ strtolower( $pair[0] ) ] = trim( $pair[1] ?? '' );
		}
		if ( 'offline' === $this->mode ) {
			throw new TransportException( 'Dummy upstream diagnostics isolated-secret must never reach the client.' );
		}
		if ( 'PUT' === $method ) {
			if ( ( $headers['if-none-match'] ?? '' ) !== '*' ) {
				throw new RuntimeException( 'Missing atomic create condition' );
			}
			$body = $options['body'];
			$bytes = '';
			if ( is_resource( $body ) ) {
				$bytes = stream_get_contents( $body );
			} elseif ( is_string( $body ) ) {
				$bytes = $body;
			} elseif ( is_callable( $body ) ) {
				while ( '' !== ( $chunk = $body( 65536 ) ) ) { $bytes .= $chunk; }
			} else {
				foreach ( $body as $chunk ) { $bytes .= $chunk; }
			}
			if ( isset( $this->objects[ $key ] ) ) {
				return new MockResponse( '<Error><Code>PreconditionFailed</Code></Error>', array( 'http_code' => 412 ) );
			}
			$this->objects[ $key ] = array(
				'body' => $bytes,
				'mime' => $headers['content-type'] ?? '',
				'sha256' => $headers['x-amz-meta-sha256'] ?? '',
				'cache' => $headers['cache-control'] ?? '',
			);
			if ( 'timeout-after-commit' === $this->mode ) {
				throw new TransportException( 'Response lost after commit' );
			}
			return new MockResponse( '', array( 'http_code' => 200, 'response_headers' => array( 'etag: "not-a-sha256"' ) ) );
		}
		if ( 'HEAD' === $method && isset( $this->objects[ $key ] ) ) {
			$object = $this->objects[ $key ];
			return new MockResponse( '', array(
				'http_code' => 200,
				'response_headers' => array(
					'content-length: ' . strlen( $object['body'] ),
					'content-type: ' . $object['mime'],
					'x-amz-meta-sha256: ' . $object['sha256'],
					'etag: "' . $object['sha256'] . '"',
				),
			) );
		}
		return new MockResponse( '', array( 'http_code' => 404 ) );
	}
}
