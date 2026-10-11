<?php

const WEBBOOKS_VERSION       = '1.9.5';




define( 'WEBBOOKS_PATH', get_template_directory() );
define( 'WEBBOOKS_URL', get_template_directory_uri() );

const WEBBOOKS_AJAX_NONCE = 'webbooks-request-nonce';
const WEBBOOKS_DOWNLOAD_NONCE = 'webbooks-download-nonce';
require_once WEBBOOKS_PATH . '/options_page.php';

if ( file_exists( WEBBOOKS_PATH . '/vendor/autoload.php' ) ) {
	require_once WEBBOOKS_PATH . '/vendor/autoload.php';
} else {
		spl_autoload_register(
			static function ( string $class_name ): void {
			$prefix = 'Webbooks\\';
				if ( strpos( $class_name, $prefix ) !== 0 ) {
				return;
			}

				$relative = substr( $class_name, strlen( $prefix ) );
			$file     = WEBBOOKS_PATH . '/src/' . str_replace( '\\', '/', $relative ) . '.php';
			if ( file_exists( $file ) ) {
				require_once $file;
			}
		}
	);
}

$modules = array(
	'/inc/core.php',
	'/inc/setup.php',
	'/inc/assets.php',
	'/inc/helpers.php',
	'/inc/related.php',
	'/inc/post-templates.php',
	'/inc/seo.php',
	'/inc/structured-data.php',
	'/inc/ajax/search.php',


);

foreach ( $modules as $module ) {
	require_once WEBBOOKS_PATH . $module;
}
