<?php
/**
 * Run with the isolated tests/compose.yml PHP service, never a production database.
 */

declare(strict_types=1);

define( 'REST_REQUEST', true );
$_SERVER['HTTP_HOST'] = 'webbooks.test';
$_SERVER['SERVER_NAME'] = 'webbooks.test';
$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
$_SERVER['REQUEST_URI'] = '/wp-json/webbooks-ai/v1/images';
$_SERVER['HTTPS'] = 'on';
require '/var/www/html/wp-load.php';
set_exception_handler( static function ( Throwable $error ): void { fwrite( STDERR, (string) $error . PHP_EOL ); exit( 1 ); } );
if ( DB_NAME !== 'webbooks_tests' ) { throw new RuntimeException( 'Isolated database only.' ); }
require __DIR__ . '/r2-fixtures.php';

use Webbooks\Media\ImageUpload\ImageValidator;
use Webbooks\Media\ImageUpload\UploadedImage;
use Webbooks\Media\ImageUpload\RestController;
use Webbooks\Media\ImageUpload\Configuration;

$directory = sys_get_temp_dir() . '/webbooks-r2-tests-' . bin2hex( random_bytes( 8 ) );
mkdir( $directory, 0700 );
$files = array();
$write = static function ( string $name, string $bytes ) use ( $directory, &$files ): string {
	$path = $directory . '/' . $name; file_put_contents( $path, $bytes ); $files[] = $path; return $path;
};
$validator = new ImageValidator( static fn( string $path ): bool => is_file( $path ) );
$native_validator = new ImageValidator();
$user_ids = array();
try {
	$raster = imagecreatetruecolor( 1200, 675 );
	imagefill( $raster, 0, 0, imagecolorallocate( $raster, 40, 110, 150 ) );
	foreach ( array( 'png' => 'imagepng', 'jpg' => 'imagejpeg', 'webp' => 'imagewebp' ) as $extension => $encoder ) {
		ob_start(); $encoder( $raster ); $bytes = ob_get_clean();
		$path = $write( 'sample.' . $extension, $bytes );
		$image = $validator->validate( r2_test_file( $path ), 'multipart/form-data; boundary=qa' );
		r2_test_assert( $image instanceof UploadedImage && $image->width === 1200 && $image->height === 675 && $image->sha256 === hash( 'sha256', $bytes ), "$extension is decoded and hashed from the original bytes, ignoring filename and client MIME" );
		$transport = new R2TestTransport(); $storage = $transport->storage();
		r2_test_assert( $storage->store( $image ) === true, "$extension first conditional PUT creates the object" );
		r2_test_assert( $storage->store( $image ) === false && count( $transport->objects ) === 1, "$extension duplicate confirms the same object via HEAD" );
		$object = reset( $transport->objects );
		r2_test_assert( $object['body'] === $bytes && $object['sha256'] === $image->sha256 && $object['mime'] === $image->mime_type && $object['cache'] === 'public, max-age=31536000, immutable', "$extension stored bytes, content type, metadata and immutable cache policy match" );
	}
	unset( $raster );
	$png_path = $directory . '/sample.png';
	$png = file_get_contents( $png_path );
	$valid = $validator->validate( r2_test_file( $png_path ), 'multipart/form-data' );
	r2_test_error( $native_validator->validate( r2_test_file( $png_path ), 'multipart/form-data' ), 400, 'webbooks_image_invalid_request' );
	r2_test_error( $validator->validate( array(), 'application/json' ), 415, 'webbooks_image_unsupported_type' );
	r2_test_error( $validator->validate( array(), 'multipart/form-data', ImageValidator::MAX_BYTES + 1000 ), 413, 'webbooks_image_too_large' );
	r2_test_error( $validator->validate( array(), 'multipart/form-data' ), 400, 'webbooks_image_invalid_request' );
	$multiple = r2_test_file( $png_path ); $multiple['extra'] = $multiple['file'];
	r2_test_error( $validator->validate( $multiple, 'multipart/form-data' ), 400, 'webbooks_image_invalid_request' );
	$array_file = r2_test_file( $png_path ); $array_file['file']['name'] = array( 'file.png' );
	r2_test_error( $validator->validate( $array_file, 'multipart/form-data' ), 400, 'webbooks_image_invalid_request' );
	foreach ( array( 'empty' => '', 'html' => '<html>not an image</html>', 'gif' => base64_decode( 'R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7' ), 'svg' => '<svg xmlns="http://www.w3.org/2000/svg"></svg>', 'truncated' => substr( $png, 0, -12 ), 'bad-crc' => substr( $png, 0, -1 ) . chr( ord( substr( $png, -1 ) ) ^ 1 ) ) as $name => $bytes ) {
		$result = $validator->validate( r2_test_file( $write( $name . '.png', $bytes ) ), 'multipart/form-data' );
		$status = match ( $name ) { 'empty' => 400, 'html', 'svg', 'gif' => 415, default => 422 };
		$code = match ( $status ) { 400 => 'webbooks_image_invalid_request', 415 => 'webbooks_image_unsupported_type', default => 'webbooks_image_invalid_data' };
		r2_test_error( $result, $status, $code );
	}
	$jpeg = file_get_contents( $directory . '/sample.jpg' );
	r2_test_error( $validator->validate( r2_test_file( $write( 'truncated.jpg', substr( $jpeg, 0, -2 ) ) ), 'multipart/form-data' ), 422, 'webbooks_image_invalid_data' );
	$webp = file_get_contents( $directory . '/sample.webp' );
	r2_test_error( $validator->validate( r2_test_file( $write( 'truncated.webp', substr( $webp, 0, -1 ) ) ), 'multipart/form-data' ), 422, 'webbooks_image_invalid_data' );
	foreach ( array( array( 8193, 1 ), array( 1, 8193 ), array( 5000, 4001 ) ) as [ $width, $height ] ) {
		$path = $write( "dimensions-$width-$height.png", r2_test_dimensions( $png, $width, $height ) );
		r2_test_error( $validator->validate( r2_test_file( $path ), 'multipart/form-data' ), 413, 'webbooks_image_too_large' );
	}
	foreach ( array( array( 1, 1 ), array( 8192, 1 ), array( 1, 8192 ), array( 5000, 4000 ) ) as [ $width, $height ] ) {
		$raster = imagecreatetruecolor( $width, $height );
		ob_start(); imagepng( $raster ); $limit_bytes = ob_get_clean(); unset( $raster );
		$path = $write( "valid-dimensions-$width-$height.png", $limit_bytes );
		$limit_image = $validator->validate( r2_test_file( $path ), 'multipart/form-data' );
		r2_test_assert( $limit_image instanceof UploadedImage && $limit_image->width === $width && $limit_image->height === $height, "Allowed dimension boundary $width x $height is accepted" );
	}
	$upload_error = r2_test_file( $png_path ); $upload_error['file']['error'] = UPLOAD_ERR_INI_SIZE;
	r2_test_error( $validator->validate( $upload_error, 'multipart/form-data' ), 413, 'webbooks_image_too_large' );
	$upload_error['file']['error'] = UPLOAD_ERR_PARTIAL;
	r2_test_error( $validator->validate( $upload_error, 'multipart/form-data' ), 400, 'webbooks_image_invalid_request' );
	// A standards-compliant PNG exactly 8 MiB, using bounded ancillary chunks.
	$padded = substr( $png, 0, -12 ); $padding = ImageValidator::MAX_BYTES - strlen( $png );
	while ( $padding > 0 ) {
		$chunk_bytes = min( 65536, $padding );
		if ( $padding - $chunk_bytes > 0 && $padding - $chunk_bytes < 16 ) { $chunk_bytes -= 16; }
		$padded .= r2_test_png_chunk( 'tEXt', "qa\0" . str_repeat( 'a', $chunk_bytes - 15 ) );
		$padding -= $chunk_bytes;
	}
	$padded .= substr( $png, -12 );
	$boundary_path = $write( 'boundary.png', $padded );
	$boundary = $validator->validate( r2_test_file( $boundary_path ), 'multipart/form-data' );
	r2_test_assert( $boundary instanceof UploadedImage && $boundary->bytes === 8388608, 'Exactly 8 MiB is accepted after full PNG validation' );
	r2_test_error( $validator->validate( r2_test_file( $write( 'over.png', $padded . 'x' ) ), 'multipart/form-data' ), 413, 'webbooks_image_too_large' );

	$changed_raster = imagecreatefrompng( $png_path );
	imagesetpixel( $changed_raster, 0, 0, imagecolorallocate( $changed_raster, 255, 0, 0 ) );
	ob_start(); imagepng( $changed_raster ); $changed_bytes = ob_get_clean(); unset( $changed_raster );
	$different = $validator->validate( r2_test_file( $write( 'changed.png', $changed_bytes ) ), 'multipart/form-data' );
	$distinct_transport = new R2TestTransport(); $distinct_storage = $distinct_transport->storage();
	r2_test_assert( $distinct_storage->store( $valid ) === true && $distinct_storage->store( $different ) === true && count( $distinct_transport->objects ) === 2 && $valid->key() !== $different->key(), 'Different original bytes under the same client filename create distinct URLs' );

	$transport = new R2TestTransport(); $transport->mode = 'timeout-after-commit';
	r2_test_assert( $transport->storage()->store( $valid ) === false && count( $transport->objects ) === 1, 'A lost PUT response is reconciled via HEAD and returned as an existing object' );
	$key = array_key_first( $transport->objects );
	$transport->objects[ $key ]['sha256'] = str_repeat( '0', 64 ); $transport->mode = 'normal';
	r2_test_error( $transport->storage()->store( $valid ), 409, 'webbooks_image_object_conflict' );
	$conflict_object = $transport->objects[ $key ];
	foreach ( array( 'mime' => 'image/jpeg', 'body' => 'different-size' ) as $field => $bad_value ) {
		$transport->objects[ $key ] = $conflict_object;
		$transport->objects[ $key ]['sha256'] = $valid->sha256;
		$transport->objects[ $key ][ $field ] = $bad_value;
		r2_test_error( $transport->storage()->store( $valid ), 409, 'webbooks_image_object_conflict' );
		r2_test_assert( $transport->objects[ $key ][ $field ] === $bad_value, 'A conflicting existing object is never overwritten' );
	}
	$transport->mode = 'offline';
	$unavailable = $transport->storage()->store( $valid );
	r2_test_error( $unavailable, 503, 'webbooks_image_storage_unavailable' );
	r2_test_assert( ! str_contains( wp_json_encode( $unavailable ), 'isolated-secret' ), 'Upstream diagnostics and secrets are not returned' );
	r2_test_assert( count( array_filter( $transport->requests, static fn( array $r ): bool => ! in_array( $r['method'], array( 'PUT', 'HEAD' ), true ) ) ) === 0, 'Storage performs only conditional PUT and exact-key HEAD operations' );

	require __DIR__ . '/r2-concurrency.php';
	require __DIR__ . '/r2-configuration.php';
	require __DIR__ . '/r2-rest.php';
} finally {
	foreach ( $files as $path ) { unlink( $path ); }
	rmdir( $directory );
	if ( $user_ids ) {
		require_once ABSPATH . 'wp-admin/includes/user.php';
		foreach ( $user_ids as $id ) { wp_delete_user( $id ); }
	}
}
echo "R2 upload tests passed on PHP " . PHP_VERSION . "\n";
