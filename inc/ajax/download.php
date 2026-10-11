<?php
use Webbooks\Book\DownloadLinks;
use Webbooks\Book\BookMeta;
add_action( 'wp_ajax_return_link_to_book', array( DownloadLinks::class, 'returnLinkToBook' ) );
add_action( 'wp_ajax_nopriv_return_link_to_book', array( DownloadLinks::class, 'returnLinkToBook' ) );
add_filter( 'get_download_link', 'get_download_link', 10, 2 );
function get_download_link( WP_Post $post, int $category_id = 0 ): string {
	if ( ! \Webbooks\Statistics\Views::isPublic( $post->ID ) ) {
		return '';
	}
	$buttons = array();
	foreach ( DownloadLinks::sources( $post->ID ) as $source ) {
		$url = add_query_arg( array( 'count' => $post->ID, 'cat' => $category_id ), webbooks_download_page_url() );
		$buttons[] = '<a href="' . esc_url( $url ) . '" class="btn btn-primary btn-sm" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Download', 'webbooks' ) . '</a>';
	}
	// All providers are displayed by the download page; one entry point suffices.
	$buttons = array_slice( $buttons, 0, 1 );
	$buy = esc_url_raw( BookMeta::getText( $post->ID, 'buy' ), array( 'http', 'https' ) );
	if ( $buy !== '' ) {
		$buttons[] = '<a href="' . esc_url( $buy ) . '" class="btn btn-success btn-sm" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Buy', 'webbooks' ) . '</a>';
	}
	return implode( ' ', $buttons );
}
