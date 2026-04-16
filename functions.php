<?php

const WEBBOOKS_VERSION       = '1.8.4';
const WEBBOOKS_DOWNLOAD_NONCE = 'webbooks-download-nonce';
const WEBBOOKS_AJAX_NONCE     = 'webbooks-request-nonce';

define( 'WEBBOOKS_PATH', get_stylesheet_directory() );
define( 'WEBBOOKS_URL', get_stylesheet_directory_uri() );

require_once WEBBOOKS_PATH . '/options_page.php';
require_once WEBBOOKS_PATH . '/src/Domain/Book/Language.php';
require_once WEBBOOKS_PATH . '/src/Domain/Book/Complexity.php';

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
	'/inc/setup.php',
	'/inc/assets.php',
	'/inc/helpers.php',
	'/inc/seo.php',
	'/inc/structured-data.php',
	'/inc/ajax/search.php',
	'/inc/ajax/download.php',
	'/inc/comments-security.php',
);

foreach ( $modules as $module ) {
	require_once WEBBOOKS_PATH . $module;
}
