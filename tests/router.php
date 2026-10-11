<?php
/** Local preview only; core and fixtures live in the isolated Docker volume. */
$path = '/var/www/html' . parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );
$host = $_SERVER['HTTP_HOST'] ?? 'localhost:8895';
$host = preg_match( '/^localhost:[0-9]+$/', $host ) ? $host : 'localhost:8895';
define( 'WP_HOME', 'http://' . $host );
define( 'WP_SITEURL', 'http://' . $host );
if ( is_file( $path ) ) {
	if ( pathinfo( $path, PATHINFO_EXTENSION ) === 'php' ) {
		require $path;
		return true;
	}
	return false;
}
require '/var/www/html/index.php';
