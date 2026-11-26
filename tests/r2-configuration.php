<?php
/** Server-only configuration parsing with all environment values restored. */

use Webbooks\Media\ImageUpload\Configuration;

$settings = array(
	'WEBBOOKS_R2_ENDPOINT' => 'https://' . str_repeat( 'a', 32 ) . '.r2.cloudflarestorage.com',
	'WEBBOOKS_R2_BUCKET' => 'isolated-bucket',
	'WEBBOOKS_R2_ACCESS_KEY_ID' => 'isolated-access',
	'WEBBOOKS_R2_SECRET_ACCESS_KEY' => 'isolated-secret',
	'WEBBOOKS_R2_PUBLIC_BASE_URL' => 'https://data.webbooks.com.ua',
);
$saved = array();
foreach ( $settings as $name => $value ) { $saved[ $name ] = getenv( $name ); putenv( $name . '=' . $value ); }
try {
	$config = Configuration::fromEnvironment();
	r2_test_assert( $config instanceof Configuration, 'Complete HTTPS R2 server configuration is accepted' );
	r2_test_assert( ! str_contains( print_r( $config, true ), 'isolated-secret' ), 'Configuration debug output redacts credentials' );
	foreach ( array(
		'WEBBOOKS_R2_ENDPOINT' => 'http://' . str_repeat( 'a', 32 ) . '.r2.cloudflarestorage.com',
		'WEBBOOKS_R2_PUBLIC_BASE_URL' => 'https://another-host.example.test',
		'WEBBOOKS_R2_BUCKET' => '../another-bucket',
		'WEBBOOKS_R2_SECRET_ACCESS_KEY' => '',
	) as $name => $invalid ) {
		putenv( $name . '=' . $invalid );
		r2_test_error( Configuration::fromEnvironment(), 503, 'webbooks_image_storage_unavailable' );
		putenv( $name . '=' . $settings[ $name ] );
	}
} finally {
	foreach ( $saved as $name => $value ) { putenv( false === $value ? $name : $name . '=' . $value ); }
}
