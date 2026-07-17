<?php
/**
 * Book metadata normalization and JSON-LD helpers.
 *
 * @package Webbooks
 */

namespace Webbooks\Book;

/**
 * Provides presentation-ready book metadata.
 */
class BookMeta {

	/**
	 * Resolve a language label from its slug.
	 *
	 * @param string $slug Language slug.
	 * @return string Language label.
	 */
	public static function getLanguage( string $slug ): string {
		return \Webbooks\Domain\Book\Language::fromNullable( $slug )?->label() ?? __( 'Not specified', 'webbooks' );
	}

	/**
	 * Resolve a complexity label from its slug.
	 *
	 * @param string $slug Complexity slug.
	 * @return string Complexity label.
	 */
	public static function getComplexity( string $slug ): string {
		return \Webbooks\Domain\Book\Complexity::fromNullable( $slug )?->label() ?? __( 'Not specified', 'webbooks' );
	}

	/**
	 * Get normalized metadata for a book post.
	 *
	 * @param int $post_id Book post ID.
	 * @return array<string, mixed> Normalized book metadata.
	 */
	public static function getNormalizedMeta( int $post_id ): array {
		$title        = get_the_title( $post_id );
		$author       = trim( (string) get_post_meta( $post_id, 'autor', true ) );
		$year_raw     = trim( (string) get_post_meta( $post_id, 'year', true ) );
		$format       = trim( (string) get_post_meta( $post_id, 'format', true ) );
		$language_raw = function_exists( 'get_field' ) ? trim( (string) get_field( 'language', $post_id ) ) : '';
		$pages_raw    = trim( (string) get_post_meta( $post_id, 'number_page', true ) );

		return array(
			'title'    => $title,
			'author'   => $author,
			'year'     => preg_match( '/^\d{4}$/', $year_raw ) ? $year_raw : '',
			'format'   => $format,
			'language' => self::getLanguage( $language_raw ),
			'pages'    => is_numeric( $pages_raw ) ? (int) $pages_raw : null,
			'image'    => self::getBookImage( $post_id ),
		);
	}

	/**
	 * Build the Book JSON-LD entity for a post.
	 *
	 * @param int $post_id Book post ID.
	 * @return array<string, mixed> JSON-LD entity.
	 */
	public static function getBookSchema( int $post_id ): array {
		$meta = self::getNormalizedMeta( $post_id );

		return self::filterSchema(
			array(
				'@context'      => 'https://schema.org',
				'@type'         => 'Book',
				'name'          => $meta['title'],
				'author'        => '' !== $meta['author'] ? array(
					'@type' => 'Person',
					'name'  => $meta['author'],
				) : null,
				'inLanguage'    => $meta['language'],
				'bookFormat'    => $meta['format'],
				'numberOfPages' => $meta['pages'],
				'image'         => $meta['image'],
				'datePublished' => $meta['year'],
			)
		);
	}

	/**
	 * Remove empty values from a schema entity recursively.
	 *
	 * @param array<string, mixed> $data Schema data.
	 * @return array<string, mixed> Filtered schema data.
	 */
	public static function filterSchema( array $data ): array {
		$filtered = array();

		foreach ( $data as $key => $value ) {
			if ( is_array( $value ) ) {
				$value = self::filterSchema( $value );
				if ( array() === $value ) {
					continue;
				}
			}

			if ( null === $value || '' === $value ) {
				continue;
			}

			$filtered[ $key ] = $value;
		}

		return $filtered;
	}

	/**
	 * Resolve the book cover image URL.
	 *
	 * @param int $post_id Book post ID.
	 * @return string Image URL.
	 */
	private static function getBookImage( int $post_id ): string {
		$image = get_the_post_thumbnail_url( $post_id, 'full' );

		return $image ? $image : get_template_directory_uri() . '/screenshot.png';
	}
}
