<?php

namespace Webbooks\Book;

class BookMeta {

	/** ACF is optional; scalar metadata remains usable when the plugin is disabled. */
	public static function getText( int $postId, string $key ): string {
		$value = get_post_meta( $postId, $key, true );
		return is_scalar( $value ) ? trim( (string) $value ) : '';
	}

	public static function getFieldText( int $postId, string $key ): string {
		$value = function_exists( 'get_field' ) ? get_field( $key, $postId ) : null;
		return is_scalar( $value ) ? trim( (string) $value ) : self::getText( $postId, $key );
	}

	public static function getLanguage( string $slug ): string {
		return \Webbooks\Domain\Book\Language::fromNullable( $slug )?->label() ?? __( 'Not specified', 'webbooks' );
	}

	public static function getComplexity( string $slug ): string {
		return \Webbooks\Domain\Book\Complexity::fromNullable( $slug )?->label() ?? __( 'Not specified', 'webbooks' );
	}

	public static function getNormalizedMeta( int $postId ): array {
		$title       = get_the_title( $postId );
		$author      = self::getText( $postId, 'autor' );
		$yearRaw     = self::getText( $postId, 'year' );
		$format      = self::getText( $postId, 'format' );
		$languageRaw = self::getFieldText( $postId, 'language' );
		$pagesRaw    = self::getText( $postId, 'number_page' );

		return array(
			'title'    => $title,
			'author'   => $author,
			'year'     => preg_match( '/^\d{4}$/', $yearRaw ) ? $yearRaw : '',
			'format'   => $format,
			'language' => self::getLanguage( $languageRaw ),
			'language_code' => $languageRaw === 'ua' ? 'uk' : ( in_array( $languageRaw, array( 'ru', 'en' ), true ) ? $languageRaw : '' ),
			'complexity' => self::getComplexity( self::getFieldText( $postId, 'complexity' ) ),
			'publisher' => self::getText( $postId, 'create' ),
			'pages'    => is_numeric( $pagesRaw ) ? (int) $pagesRaw : null,
			'image'    => self::getBookImage( $postId ),
		);
	}

	public static function getBookSchema( int $postId ): array {
		$meta = self::getNormalizedMeta( $postId );

		return self::filterSchema(
			array(
				'@context'      => 'https://schema.org',
				'@type'         => 'Book',
				'name'          => $meta['title'],
				'author'        => $meta['author'] !== '' ? array(
					'@type' => 'Person',
					'name'  => $meta['author'],
				) : null,
				'inLanguage'    => $meta['language_code'],
				'bookFormat'    => $meta['format'],
				'numberOfPages' => $meta['pages'],
				'image'         => $meta['image'],
				'datePublished' => $meta['year'],
			)
		);
	}

	public static function filterSchema( array $data ): array {
		$filtered = array();

		foreach ( $data as $key => $value ) {
			if ( is_array( $value ) ) {
				$value = self::filterSchema( $value );
				if ( $value === array() ) {
					continue;
				}
			}

			if ( $value === null || $value === '' ) {
				continue;
			}

			$filtered[ $key ] = $value;
		}

		return $filtered;
	}

	private static function getBookImage( int $postId ): string {
		$image = get_the_post_thumbnail_url( $postId, 'full' );

		return $image ? $image : '';
	}
}
