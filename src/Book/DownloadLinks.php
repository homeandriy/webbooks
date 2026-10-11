<?php
namespace Webbooks\Book;

final class DownloadLinks {
	/** Only validated HTTP(S) URLs from public, unprotected posts are exposed. */
	public static function sources( int $id ): array {
		if ( ! \Webbooks\Statistics\Views::isPublic( $id ) ) {
			return array();
		}
		$labels = array( 'download_pcloud' => 'pCloud', 'download_hubic' => __( 'Cloud storage', 'webbooks' ), 'download_mega' => 'Mega', 'download' => 'Cloud Mail.ru' );
		$result = array();
		foreach ( $labels as $key => $label ) {
			$url = esc_url_raw( BookMeta::getText( $id, $key ), array( 'http', 'https' ) );
			if ( $url !== '' && in_array( wp_parse_url( $url, PHP_URL_SCHEME ), array( 'http', 'https' ), true ) && wp_parse_url( $url, PHP_URL_HOST ) ) {
				$result[] = array( 'link' => $url, 'name' => $label );
			}
		}
		return $result;
	}

	public static function returnLinkToBook(): void {
		check_ajax_referer( WEBBOOKS_DOWNLOAD_NONCE, '_nonce' );
		$raw = $_POST['parameters'] ?? array();
		$parameters = is_array( $raw ) ? wp_unslash( $raw ) : ( is_string( $raw ) ? json_decode( wp_unslash( $raw ), true ) : array() );
		$id = is_array( $parameters ) && is_scalar( $parameters['id'] ?? null ) ? absint( $parameters['id'] ) : 0;
		if ( ! \Webbooks\Statistics\Views::isPublic( $id ) ) {
			wp_send_json_error( array( 'message' => __( 'The requested book is unavailable.', 'webbooks' ) ), 404 );
		}
		$sources = self::sources( $id );
		ob_start();
		include WEBBOOKS_PATH . '/template-parts/download-links.php';
		wp_send_json_success( array( 'html' => ob_get_clean() ) );
	}
}
