<?php
$_SERVER['HTTP_HOST'] = 'localhost:8895';
$_SERVER['SERVER_NAME'] = 'localhost';
$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
$_SERVER['REQUEST_URI'] = '/';
require '/var/www/html/wp-load.php';
if ( DB_NAME !== 'webbooks_tests' ) {
	exit( 1 );
}
update_option( 'home', 'http://localhost:8895' );
update_option( 'siteurl', 'http://localhost:8895' );
$post = wp_insert_post( array( 'post_title' => 'Architecture book preview', 'post_content' => '<p>A classic WordPress book page with native content formatting.</p><h2>Reading sample</h2><p>Gallery, search, downloads and related books.</p>', 'post_status' => 'publish' ) );
update_post_meta( $post, 'autor', 'Preview author' );
update_post_meta( $post, 'language', 'en' );
update_post_meta( $post, 'download', 'https://example.test/book.pdf' );
update_post_meta( $post, 'gallery', array( array( 'url' => 'http://localhost:8895/wp-content/themes/webbooks/screenshot.png', 'alt' => 'Preview cover' ) ) );
$page = wp_insert_post( array( 'post_type' => 'page', 'post_title' => 'Download preview', 'post_status' => 'publish' ) );
update_post_meta( $page, '_wp_page_template', 'download.php' );
update_option( 'webbooks_site_settings', array( 'download_page' => $page ) );
echo 'Preview book ID: ' . $post . '; download page ID: ' . $page . "\n";
