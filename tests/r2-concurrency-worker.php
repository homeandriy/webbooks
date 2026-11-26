<?php
/** Independent SDK process used only by the local concurrent-create test. */

declare(strict_types=1);
$_SERVER['HTTP_HOST'] = 'webbooks.test';
$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
require '/var/www/html/wp-load.php';
if ( DB_NAME !== 'webbooks_tests' ) { exit( 1 ); }
require __DIR__ . '/r2-fixtures.php';

use AsyncAws\S3\S3Client;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Webbooks\Media\ImageUpload\UploadedImage;
use Webbooks\Media\ImageUpload\Configuration;
use Webbooks\Media\ImageUpload\R2Storage;

$input = json_decode( file_get_contents( $argv[1] ), true, 512, JSON_THROW_ON_ERROR );
$image = new UploadedImage( ...$input );
$config = new Configuration( 'https://' . str_repeat( 'a', 32 ) . '.r2.cloudflarestorage.com', 'isolated-bucket', 'isolated-access', 'isolated-secret' );
$state_path = $argv[2];
$http = new MockHttpClient( static function ( string $method, string $url, array $options ) use ( $state_path ): MockResponse {
	$headers = array();
	foreach ( $options['headers'] as $line ) {
		$pair = explode( ':', $line, 2 ); $headers[ strtolower( $pair[0] ) ] = trim( $pair[1] ?? '' );
	}
	$key = parse_url( $url, PHP_URL_PATH );
	$handle = fopen( $state_path, 'c+' );
	flock( $handle, LOCK_EX );
	try {
		$state = json_decode( stream_get_contents( $handle ), true, 512, JSON_THROW_ON_ERROR );
		$state['requests'][] = $method;
		if ( 'PUT' === $method ) {
			if ( ( $headers['if-none-match'] ?? '' ) !== '*' ) { throw new RuntimeException( 'Missing condition' ); }
			if ( isset( $state['objects'][ $key ] ) ) {
				$response = new MockResponse( '<Error><Code>PreconditionFailed</Code></Error>', array( 'http_code' => 412 ) );
			} else {
				$body = $options['body']; $bytes = '';
				if ( is_resource( $body ) ) { $bytes = stream_get_contents( $body ); }
				elseif ( is_string( $body ) ) { $bytes = $body; }
				elseif ( is_callable( $body ) ) { while ( '' !== ( $chunk = $body( 65536 ) ) ) { $bytes .= $chunk; } }
				else { foreach ( $body as $chunk ) { $bytes .= $chunk; } }
				$state['objects'][ $key ] = array( 'bytes' => strlen( $bytes ), 'sha256' => $headers['x-amz-meta-sha256'], 'mime' => $headers['content-type'], 'actual_sha256' => hash( 'sha256', $bytes ) );
				usleep( 50000 );
				$response = new MockResponse( '', array( 'http_code' => 200 ) );
			}
		} elseif ( 'HEAD' === $method && isset( $state['objects'][ $key ] ) ) {
			$object = $state['objects'][ $key ];
			$response = new MockResponse( '', array( 'http_code' => 200, 'response_headers' => array( 'content-length: ' . $object['bytes'], 'content-type: ' . $object['mime'], 'x-amz-meta-sha256: ' . $object['sha256'] ) ) );
		} else {
			$response = new MockResponse( '', array( 'http_code' => 404 ) );
		}
		rewind( $handle ); ftruncate( $handle, 0 ); fwrite( $handle, json_encode( $state ) ); fflush( $handle );
		return $response;
	} finally {
		flock( $handle, LOCK_UN ); fclose( $handle );
	}
} );
$result = ( new R2Storage( $config, new S3Client( $config->clientOptions(), null, $http ) ) )->store( $image );
if ( is_wp_error( $result ) ) { fwrite( STDERR, $result->get_error_code() ); exit( 1 ); }
echo json_encode( $image->payload( $result ) );
