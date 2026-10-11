<?php
use Webbooks\Statistics\Views;

function webbooks_ajax_view_id(): int {
	check_ajax_referer( WEBBOOKS_AJAX_NONCE, 'nonce' );
	$id = isset( $_POST['post_id'] ) && is_scalar( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
	if ( ! Views::isPublic( $id ) ) {
		wp_send_json_error( array( 'message' => __( 'The requested post is unavailable.', 'webbooks' ) ), 404 );
	}
	return $id;
}
function set_post_count_view(): void {
	$id = webbooks_ajax_view_id();
	try {
		$count = Views::record( $id, webbooks_get_comment_request_ip() );
		wp_send_json_success( array( 'count' => $count ) );
	} catch ( RuntimeException $exception ) {
		wp_send_json_error( array( 'message' => __( 'Statistics temporarily unavailable.', 'webbooks' ) ), 503 );
	}
}
function get_count_post_view(): void {
	wp_send_json_success( array( 'count' => Views::count( webbooks_ajax_view_id() ) ) );
}
add_action( 'wp_ajax_postview_count_set', 'set_post_count_view' );
add_action( 'wp_ajax_nopriv_postview_count_set', 'set_post_count_view' );
add_action( 'wp_ajax_postview_count_get', 'get_count_post_view' );
add_action( 'wp_ajax_nopriv_postview_count_get', 'get_count_post_view' );
