<?php
/**
 * Download actions and button markup for books.
 *
 * @package Webbooks
 */

declare(strict_types=1);

namespace Webbooks\Ajax;

use WP_Post;
use Webbooks\Book\DownloadLinks;

/**
 * Registers AJAX and filter handlers for book downloads.
 */
final class DownloadController {

	/**
	 * Register WordPress hooks.
	 */
	public static function register(): void {
		add_action( 'wp_ajax_return_link_to_book', array( DownloadLinks::class, 'returnLinkToBook' ) );
		add_action( 'wp_ajax_nopriv_return_link_to_book', array( DownloadLinks::class, 'returnLinkToBook' ) );
		add_filter( 'get_download_link', array( self::class, 'getDownloadLink' ), 10, 2 );
	}

	/**
	 * Build safe download and purchase buttons for a book.
	 *
	 * @param WP_Post $post        Book post.
	 * @param int     $category_id Optional category ID.
	 * @return string Button markup.
	 */
	public static function getDownloadLink( WP_Post $post, int $category_id = 0 ): string {
		$download_sources = array(
			'download_pcloud' => esc_html__( 'Download from pCloud', 'webbooks' ),
			'download_hubic'  => esc_html__( 'Download from Cloud Webbooks', 'webbooks' ),
			'download_mega'   => esc_html__( 'Download from Mega', 'webbooks' ),
			'download'        => esc_html__( 'Download from Cloud Mail.ru', 'webbooks' ),
		);
		$buttons          = array();

		foreach ( $download_sources as $meta_key => $label ) {
			$link_to_download = trim( (string) get_post_meta( $post->ID, $meta_key, true ) );
			if ( '' === $link_to_download ) {
				continue;
			}

			$link_path = wp_parse_url( $link_to_download, PHP_URL_PATH );
			if ( ! is_string( $link_path ) || '' === $link_path ) {
				continue;
			}

			$buttons[] = sprintf(
				'<a href="%s?key=%s&count=%d&cat=%d" class="%s" target="_blank" rel="noopener noreferrer">%s</a>',
				home_url( '/download' ),
				rawurlencode( $link_path ),
				$post->ID,
				$category_id,
				'btn btn-primary btn-sm',
				$label
			);
		}

		if ( array() === $buttons ) {
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
		if ( '' !== $buy_link ) {
			$buttons[] = sprintf(
				'<a href="%s" class="%s" target="_blank" rel="noopener noreferrer"><span class="fa fa-shopping-cart" aria-hidden="true"></span> %s</a>',
				esc_url(
					add_query_arg(
						array(
							'utm_source'   => 'webbooks',
							'utm_medium'   => 'button',
							'utm_campaign' => 'book_' . $post->ID,
							'utm_content'  => 'buy_button',
						),
						$buy_link
					)
				),
				'btn btn-success btn-sm',
				esc_html_x( 'Buy', 'button', 'webbooks' )
			);
		}

		return implode( ' ', $buttons );
	}
}
