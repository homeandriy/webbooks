<?php
/**
 * Theme bootstrap and shared constants.
 *
 * @package WordPress
 * @subpackage webbooks
 */

const WEBBOOKS_VERSION           = '1.10.3';
const WEBBOOKS_DOWNLOAD_NONCE    = 'webbooks-download-nonce';
const WEBBOOKS_AJAX_NONCE        = 'webbooks-request-nonce';
const WEBBOOKS_PORTFOLIO_PAGE_ID = 846;

define( 'WEBBOOKS_PATH', get_stylesheet_directory() );
define( 'WEBBOOKS_URL', get_stylesheet_directory_uri() );

require_once WEBBOOKS_PATH . '/inc/admin/options-page.php';

if ( file_exists( WEBBOOKS_PATH . '/vendor/autoload.php' ) ) {
	require_once WEBBOOKS_PATH . '/vendor/autoload.php';
} else {
	spl_autoload_register(
		static function ( string $class_name ): void {
			$prefix = 'Webbooks\\';
			if ( ! str_starts_with( $class_name, $prefix ) ) {
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
	'/inc/helpers.php',
);

foreach ( $modules as $module ) {
	require_once WEBBOOKS_PATH . $module;
}

\Webbooks\Assets\AssetManager::register();
\Webbooks\Ajax\DownloadController::register();
\Webbooks\Ajax\SearchController::register();
\Webbooks\Comment\CommentSecurity::register();
\Webbooks\Seo\MetaTags::register();
\Webbooks\Theme\Setup::register();
\Webbooks\StructuredData\SchemaGenerator::register();
