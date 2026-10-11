<?php

/** Default cover for books without an uploaded image. */
function webbooks_book_placeholder_url(): string {
	return get_template_directory_uri() . '/assets/images/book-cover-default.webp';
}

function get_short_description( string $content, int $words_count ): string {
	return wp_trim_words( $content, $words_count, '...' );
}

function webbooks_render_book_gallery( WP_Post $book ): string {
	ob_start();
	$args = array( 'book' => $book );
	include WEBBOOKS_PATH . '/template/image-gallery.php';
	return ob_get_clean();
}

function get_banner_src(): string {
	return (string) webbooks_site_setting( 'partner_image' );
}

function webbooks_partner_banner(): string {
	$url = (string) webbooks_site_setting( 'partner_url' );
	$image = get_banner_src();
	return $url !== '' && $image !== '' ? '<a href="' . esc_url( $url ) . '" target="_blank" rel="sponsored noopener noreferrer"><img src="' . esc_url( $image ) . '" loading="lazy" alt="' . esc_attr__( 'Our partner', 'webbooks' ) . '"></a>' : '';
}

add_action( 'wp_head', static function (): void {
	foreach ( array( 'google' => 'google-site-verification', 'bing' => 'msvalidate.01', 'yandex' => 'yandex-verification' ) as $key => $name ) {
		$value = (string) webbooks_site_setting( 'verification_' . $key );
		if ( $value !== '' ) {
			echo '<meta name="' . esc_attr( $name ) . '" content="' . esc_attr( $value ) . '">' . "\n";
		}
	}
} );

/** Render sidebar widgets without duplicating the header search. */
function webbooks_render_sidebar_widgets(): void {
	$hide_search = static function ( $instance, $widget ) {
		if ( $instance === false || $widget->id_base === 'search' ) {
			return false;
		}
		if ( $widget->id_base === 'block' && isset( $instance['content'] ) ) {
			$blocks = array_values( array_filter( parse_blocks( $instance['content'] ), static function ( $block ): bool {
				return $block['blockName'] !== null || trim( $block['innerHTML'] ) !== '';
			} ) );
			if ( count( $blocks ) === 1 && $blocks[0]['blockName'] === 'core/search' ) {
				return false;
			}
		}
		return $instance;
	};
	add_filter( 'widget_display_callback', $hide_search, 10, 2 );
	add_filter( 'render_block_core/search', '__return_empty_string' );
	try {
		dynamic_sidebar( 'left-sidebar' );
	} finally {
		remove_filter( 'widget_display_callback', $hide_search, 10 );
		remove_filter( 'render_block_core/search', '__return_empty_string' );
	}
}
