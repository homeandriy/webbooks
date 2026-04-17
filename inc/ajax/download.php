<?php

use Webbooks\Book\DownloadLinks;

add_action( 'wp_ajax_return_link_to_book', array( DownloadLinks::class, 'returnLinkToBook' ) );
add_action( 'wp_ajax_nopriv_return_link_to_book', array( DownloadLinks::class, 'returnLinkToBook' ) );

add_filter( 'get_download_link', 'get_download_link', 10, 2 );
function get_download_link( WP_Post $post, int $category_id = 0 ): string {
	$download_sources = array(
		'download_pcloud' => esc_html__( 'Download from pCloud', 'webbooks' ),
		'download_hubic'  => esc_html__( 'Download from Cloud Webbooks', 'webbooks' ),
		'download_mega'   => esc_html__( 'Download from Mega', 'webbooks' ),
		'download'        => esc_html__( 'Download from Cloud Mail.ru', 'webbooks' ),
	);
	$buttons          = array();

	foreach ( $download_sources as $meta_key => $label ) {
		$link_to_download = trim( (string) get_post_meta( $post->ID, $meta_key, true ) );
		if ( empty( $link_to_download ) ) {
			continue;
		}

		$link_to_download_key_path = parse_url( $link_to_download, PHP_URL_PATH );
		if ( ! is_string( $link_to_download_key_path ) || '' === $link_to_download_key_path ) {
			continue;
		}

		$buttons[] = sprintf(
			'<a href="%s?key=%s&count=%d&cat=%d" class="%s" target="_blank" rel="noopener noreferrer">%s</a>',
			home_url( '/download' ),
			rawurlencode( $link_to_download_key_path ),
			$post->ID,
			$category_id,
			'btn btn-primary btn-sm',
			$label
		);
	}

	if ( empty( $buttons ) ) {
		$buttons[] = sprintf(
			'<a href="%s?key=%s&count=%d&cat=%d" class="%s" target="_blank" rel="noopener noreferrer">%s</a>',
			home_url( '/download' ),
			rawurlencode( $post->post_name ),
			$post->ID,
			$category_id,
			'btn btn-primary btn-sm',
			esc_html_x( 'Download', 'button', 'webbooks' )
		);
	}

	$buy_link = trim( (string) get_post_meta( $post->ID, 'buy', true ) );
	if ( ! empty( $buy_link ) ) {
		$buy_link_with_utm = add_query_arg(
			array(
				'utm_source'   => 'webbooks',
				'utm_medium'   => 'button',
				'utm_campaign' => 'book_' . $post->ID,
				'utm_content'  => 'buy_button',
			),
			$buy_link
		);

		$buttons[] = sprintf(
			'<a href="%s" class="%s" target="_blank" rel="noopener noreferrer"><span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span> %s</a>',
			esc_url( $buy_link_with_utm ),
			'btn btn-success btn-sm',
			esc_html_x( 'Buy', 'button', 'webbooks' )
		);
	}

	return implode( ' ', $buttons );
}
