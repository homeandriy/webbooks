<?php
/** Site services are bundled with the classic theme. */
require_once WEBBOOKS_PATH . '/inc/site-config.php';
require_once WEBBOOKS_PATH . '/inc/comments-security.php';
require_once WEBBOOKS_PATH . '/inc/ajax/download.php';
require_once WEBBOOKS_PATH . '/inc/ajax/views.php';

new Webbooks\Security\DisableApiUsers();
add_action( 'init', array( Webbooks\Statistics\Views::class, 'install' ) );
add_action( 'webbooks_cleanup_views', array( Webbooks\Statistics\Views::class, 'cleanup' ) );
add_action( 'switch_theme', static function (): void {
	wp_clear_scheduled_hook( 'webbooks_cleanup_views' );
} );
add_action( 'deleted_post', static function ( int $post_id ): void {
	global $wpdb;
	$wpdb->delete( $wpdb->prefix . 'webbooks_views', array( 'post_id' => $post_id ), array( '%d' ) );
	$wpdb->delete( $wpdb->prefix . 'webbooks_view_visitors', array( 'post_id' => $post_id ), array( '%d' ) );
} );
