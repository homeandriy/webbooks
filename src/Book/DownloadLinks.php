<?php
/**
 * AJAX endpoint that returns download-link markup for a book.
 *
 * @package Webbooks
 */

namespace Webbooks\Book;

/**
 * Handles download-link AJAX responses.
 */
class DownloadLinks {

	/**
	 * Return the rendered download links for a public book.
	 */
	public static function returnLinkToBook(): void {
		$parameters = filter_input( INPUT_POST, 'parameters', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		if ( ! is_array( $parameters ) ) {
			$raw_parameters = filter_input( INPUT_POST, 'parameters' );
			if ( is_string( $raw_parameters ) && '' !== $raw_parameters ) {
				$decoded_parameters = json_decode( wp_unslash( $raw_parameters ), true );
				$parameters         = is_array( $decoded_parameters ) ? $decoded_parameters : array();
			} else {
				$parameters = array();
			}
		}
		$id               = absint( $parameters['id'] ?? 0 );
		$nonce            = sanitize_text_field( filter_input( INPUT_POST, '_nonce' ) ?? '' );
		$link_to_download = array();

		if ( ! wp_verify_nonce( $nonce, WEBBOOKS_DOWNLOAD_NONCE ) ) {
			wp_send_json_error(
				array(
					'html'    => webbooks_render_template_part( 'template-parts/download-links/error', array( 'message' => __( 'Invalid security token.', 'webbooks' ) ) ),
					'message' => esc_html__( 'Invalid security token.', 'webbooks' ),
				),
				403
			);
		}

		if ( ! $id ) {
			wp_send_json_error(
				array(
					'html'    => webbooks_render_template_part( 'template-parts/download-links/error', array( 'message' => __( 'Invalid book ID.', 'webbooks' ) ) ),
					'message' => esc_html__( 'Invalid book ID.', 'webbooks' ),
				),
				400
			);
		}

		$post = get_post( $id );
		if ( ! $post instanceof \WP_Post || 'publish' !== $post->post_status ) {
			wp_send_json_error(
				array( 'message' => esc_html__( 'The requested book is unavailable.', 'webbooks' ) ),
				404
			);
		}

		if ( ! empty( get_post_meta( $id, 'download', true ) ) ) {
			$link_to_download['cloud_mail_ru'] = array(
				'link'        => get_post_meta( $id, 'download', true ),
				'name'        => __( 'Download from Cloud Mail.ru', 'webbooks' ),
				'description' => __( 'Download the file from Cloud Mail.ru storage.', 'webbooks' ),
				'img'         => '/wp-content/uploads/2017/06/cloud_mail_ru.png',
			);
		}
		if ( ! empty( get_post_meta( $id, 'download_pcloud', true ) ) ) {
			$link_to_download['pcloud'] = array(
				'link'        => get_post_meta( $id, 'download_pcloud', true ),
				'name'        => __( 'Download from pCloud', 'webbooks' ),
				'description' => __( 'Your documents stay with you wherever you go.', 'webbooks' ),
				'img'         => '/wp-content/uploads/2017/06/pcloud-logo.png',
			);
		}
		if ( ! empty( get_post_meta( $id, 'download_hubic', true ) ) ) {
			$link_to_download['hubic'] = array(
				'link'        => get_post_meta( $id, 'download_hubic', true ),
				'name'        => __( 'Download from Webbooks Cloud', 'webbooks' ),
				'description' => __( 'Webbooks.com.ua cloud storage.', 'webbooks' ),
				'img'         => '/wp-content/uploads/2018/08/touchIcon-core.png',
			);
		}
		if ( ! empty( get_post_meta( $id, 'download_mega', true ) ) ) {
			$link_to_download['mega'] = array(
				'link'        => get_post_meta( $id, 'download_mega', true ),
				'name'        => __( 'Download from Mega', 'webbooks' ),
				'description' => __( 'Your documents stay with you on every device.', 'webbooks' ),
				'img'         => '/wp-content/uploads/2017/06/logo-facebook-e1498420140798.png',
			);
		}

		wp_send_json_success(
			array(
				'html' => webbooks_render_template_part(
					'template-parts/download-links/result',
					array(
						'book_id' => $id,
						'links'   => $link_to_download,
					)
				),
			)
		);
	}
}
