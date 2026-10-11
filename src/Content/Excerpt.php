<?php
/**
 * Presentation-safe excerpts for article listings.
 *
 * @package Webbooks
 */

declare(strict_types=1);

namespace Webbooks\Content;

/**
 * Produces clean plain-text excerpts from legacy WordPress content.
 */
final class Excerpt {
	/**
	 * Build a readable excerpt without shortcodes or imported Markdown markup.
	 *
	 * @param int $post_id Post ID.
	 * @param int $word_count Maximum number of words.
	 * @return string Clean excerpt.
	 */
	public static function forPost( int $post_id, int $word_count = 28 ): string {
		$excerpt = trim( (string) get_the_excerpt( $post_id ) );

		if ( '' === $excerpt ) {
			$excerpt = (string) get_post_field( 'post_content', $post_id );
		}

		$excerpt = strip_shortcodes( $excerpt );
		$excerpt = preg_replace( '/\[(?:\/)?caption\b[^\]]*\]/iu', ' ', $excerpt ) ?? $excerpt;
		$excerpt = preg_replace( '/!?\[([^\]]*)\]\([^)]*\)/u', '$1', $excerpt ) ?? $excerpt;
		$excerpt = preg_replace( '/^\s{0,3}#{1,6}\s*/mu', '', $excerpt ) ?? $excerpt;
		$excerpt = str_replace( array( '**', '__', '`' ), '', $excerpt );
		$excerpt = html_entity_decode( wp_strip_all_tags( $excerpt, true ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
		$excerpt = preg_replace( '/\s+/u', ' ', $excerpt ) ?? $excerpt;

		return wp_trim_words( trim( $excerpt ), $word_count, '…' );
	}
}
