<?php
/**
 * SEO fallbacks for installations without an SEO plugin.
 *
 * @package Webbooks
 */

/**
 * Determine whether a supported SEO plugin is active.
 */
function webbooks_is_seo_plugin_active(): bool {
	return defined( 'WPSEO_VERSION' )
		|| class_exists( 'WPSEO_Frontend' )
		|| defined( 'RANK_MATH_VERSION' )
		|| class_exists( 'RankMath' )
		|| defined( 'AIOSEO_VERSION' )
		|| class_exists( '\\AIOSEO\\Plugin\\Common\\Main' )
		|| defined( 'SEOPRESS_VERSION' )
		|| class_exists( '\\The_SEO_Framework\\Load' );
}

/**
 * Determine whether the current request uses the download page template.
 */
function webbooks_is_download_template_page(): bool {
	if ( ! is_page() ) {
		return false;
	}

	if ( is_page_template( 'download.php' ) ) {
		return true;
	}

	return is_page( 'download' );
}

add_action( 'wp_head', 'webbooks_add_social_meta_fallback', 5 );

/**
 * Output Open Graph and Twitter card metadata when no SEO plugin does.
 */
function webbooks_add_social_meta_fallback(): void {
	if ( webbooks_is_seo_plugin_active() ) {
		return;
	}

	global $post;
	$title       = wp_get_document_title();
	$description = get_bloginfo( 'description' );
	$image       = '';
	$url         = home_url( '/' );

	if ( is_singular() && $post instanceof WP_Post ) {
		$title       = get_the_title( $post );
		$description = has_excerpt( $post ) ? $post->post_excerpt : wp_trim_words( wp_strip_all_tags( $post->post_content ), 30, '...' );
		$url         = get_permalink( $post );
		if ( has_post_thumbnail( $post ) ) {
			$image = get_the_post_thumbnail_url( $post, 'full' );
		}
	}

	if ( empty( $image ) ) {
		$image = get_template_directory_uri() . '/screenshot.png';
	}

	echo webbooks_render_template_part(
		'template-parts/seo/social-meta',
		array(
			'type'        => is_singular() ? 'article' : 'website',
			'title'       => $title,
			'description' => $description,
			'url'         => $url,
			'site_name'   => get_bloginfo( 'name' ),
			'image'       => $image,
		)
	);
}

add_action( 'wp_head', 'webbooks_add_archive_meta_description', 6 );

/**
 * Output an archive description meta tag when no SEO plugin does.
 */
function webbooks_add_archive_meta_description(): void {
	if ( webbooks_is_seo_plugin_active() || ! is_archive() ) {
		return;
	}

	$term = null;
	if ( is_category() || is_tag() || is_tax() ) {
		$term = get_queried_object();
	}

	if ( ! $term instanceof WP_Term ) {
		return;
	}

	$description = trim( wp_strip_all_tags( (string) term_description( $term ) ) );
	if ( '' === $description ) {
		$description = sprintf(
			/* translators: %s: Term name. */
			__( 'Collection of materials in the "%s" section.', 'webbooks' ),
			$term->name
		);
	}

	/* translators: 1: Archive description, 2: Term name, 3: Site name. */
	$template = __(
		'%1$s Read more books and collections in the "%2$s" category on %3$s.',
		'webbooks'
	);

	$meta_description = sprintf(
		$template,
		$description,
		$term->name,
		get_bloginfo( 'name' )
	);

	$paged = (int) get_query_var( 'paged' );
	if ( $paged > 1 ) {
		$meta_description .= ' ' . sprintf(
			/* translators: %d: Current archive page number. */
			__( 'Page %d.', 'webbooks' ),
			$paged
		);
	}

	echo webbooks_render_template_part(
		'template-parts/seo/meta-description',
		array( 'description' => wp_trim_words( $meta_description, 35, '...' ) )
	);
}

add_action( 'wp_head', 'webbooks_add_download_noindex_meta', 2 );

/**
 * Mark the download page as non-indexable in HTML metadata.
 */
function webbooks_add_download_noindex_meta(): void {
	if ( ! webbooks_is_download_template_page() ) {
		return;
	}

	echo webbooks_render_template_part( 'template-parts/seo/robots-meta' );
}

add_action( 'template_redirect', 'webbooks_add_download_robots_header', 1 );

/**
 * Send the noindex robots header for the download page.
 */
function webbooks_add_download_robots_header(): void {
	if ( ! webbooks_is_download_template_page() || headers_sent() ) {
		return;
	}

	header( 'X-Robots-Tag: noindex, nofollow', true );
}
