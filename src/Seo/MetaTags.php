<?php
/**
 * SEO fallbacks for installations without an SEO plugin.
 *
 * @package Webbooks
 */

declare(strict_types=1);

namespace Webbooks\Seo;

use WP_Post;
use WP_Term;

/**
 * Provides SEO fallback metadata when no SEO plugin is active.
 */
final class MetaTags {

	/**
	 * Register SEO hooks.
	 */
	public static function register(): void {
		add_action( 'wp_head', array( self::class, 'addSocialMetaFallback' ), 5 );
		add_action( 'wp_head', array( self::class, 'addArchiveMetaDescription' ), 6 );
		add_action( 'wp_head', array( self::class, 'addDownloadNoindexMeta' ), 2 );
		add_action( 'template_redirect', array( self::class, 'addDownloadRobotsHeader' ), 1 );
	}

	/**
	 * Determine whether a supported SEO plugin is active.
	 */
	private static function isSeoPluginActive(): bool {
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
	private static function isDownloadTemplatePage(): bool {
		if ( ! is_page() ) {
			return false;
		}

		if ( is_page_template( 'download.php' ) ) {
			return true;
		}

		return is_page( 'download' );
	}

	/**
	 * Output Open Graph and Twitter card metadata when no SEO plugin does.
	 */
	public static function addSocialMetaFallback(): void {
		if ( self::isSeoPluginActive() ) {
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

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Template renderer escapes dynamic values internally.
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

	/**
	 * Output an archive description meta tag when no SEO plugin does.
	 */
	public static function addArchiveMetaDescription(): void {
		if ( self::isSeoPluginActive() || ! is_archive() ) {
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

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Template renderer escapes dynamic values internally.
		echo webbooks_render_template_part(
			'template-parts/seo/meta-description',
			array( 'description' => wp_trim_words( $meta_description, 35, '...' ) )
		);
	}

	/**
	 * Mark the download page as non-indexable in HTML metadata.
	 */
	public static function addDownloadNoindexMeta(): void {
		if ( ! self::isDownloadTemplatePage() ) {
			return;
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static template markup is rendered by the theme helper.
		echo webbooks_render_template_part( 'template-parts/seo/robots-meta' );
	}

	/**
	 * Send the noindex robots header for the download page.
	 */
	public static function addDownloadRobotsHeader(): void {
		if ( ! self::isDownloadTemplatePage() || headers_sent() ) {
			return;
		}

		header( 'X-Robots-Tag: noindex, nofollow', true );
	}
}
