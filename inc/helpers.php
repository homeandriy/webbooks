<?php

/**
 * Render a template part and return its markup.
 *
 * @param string               $slug Template part slug.
 * @param array<string, mixed> $args Template data.
 * @return string Rendered markup.
 */
function webbooks_render_template_part( string $slug, array $args = array() ): string {
	ob_start();
	get_template_part( $slug, null, $args );

	return (string) ob_get_clean();
}

add_filter( 'post_gallery', 'get_image_gallery', 10, 1 );
function get_image_gallery( WP_Post|string $post ): ?string {
	if ( is_string( $post ) ) {
		return null;
	}

	ob_start();
	include_once WEBBOOKS_PATH . '/template/image-gallery.php';
	return (string) ob_get_clean();
}

function get_banner_src(): string {
	$links = array(
		'https://gmhost.ua/wp-content/uploads/2023/02/baner_8.jpg',
		'https://gmhost.ua/wp-content/uploads/2023/02/baner_6.jpg',
		'https://gmhost.ua/wp-content/uploads/2023/02/baner_5.jpg',
		'https://gmhost.ua/wp-content/uploads/2023/02/baner_4.jpg',
		'https://gmhost.ua/wp-content/uploads/2023/02/baner_2.jpg',
		'https://gmhost.ua/wp-content/uploads/2023/02/baner_1.jpg',
	);

	return $links[ array_rand( $links, 1 ) ];
}
