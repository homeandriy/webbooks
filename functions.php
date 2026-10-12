<?php
/** Classic theme bootstrap; each WordPress hook has one owner. */
const WEBBOOKS_VERSION = '2.1.3';
const WEBBOOKS_AJAX_NONCE = 'webbooks-request-nonce';
const WEBBOOKS_DOWNLOAD_NONCE = 'webbooks-download-nonce';
const WEBBOOKS_PORTFOLIO_PAGE_ID = 846;
define( 'WEBBOOKS_PATH', get_template_directory() );
define( 'WEBBOOKS_URL', get_template_directory_uri() );
if ( file_exists( WEBBOOKS_PATH . '/vendor/autoload.php' ) ) {
 require_once WEBBOOKS_PATH . '/vendor/autoload.php';
} else {
 spl_autoload_register( static function ( string $class_name ): void {
  $prefix = 'Webbooks\\';
  if ( ! str_starts_with( $class_name, $prefix ) ) { return; }
  $file = WEBBOOKS_PATH . '/src/' . str_replace( '\\', '/', substr( $class_name, strlen( $prefix ) ) ) . '.php';
  if ( file_exists( $file ) ) { require_once $file; }
 } );
}
require_once WEBBOOKS_PATH . '/inc/admin/options-page.php';
$modules = array(
 '/inc/core.php', '/inc/setup.php', '/inc/assets.php', '/inc/helpers.php',
 '/inc/related.php', '/inc/post-templates.php', '/inc/seo.php',
 '/inc/structured-data.php', '/inc/ajax/search.php',
);
foreach ( $modules as $module ) { require_once WEBBOOKS_PATH . $module; }
// Verified inc modules own all asset, AJAX, comment security, SEO, setup and schema hooks.
\Webbooks\Support\AuthorSupport::register();
\Webbooks\Telegram\SupportBot::register();
\Webbooks\Localization\Polylang::register();
add_filter( 'redirect_canonical', array( \Webbooks\Theme\Setup::class, 'preserveAllPostsPagination' ) );
