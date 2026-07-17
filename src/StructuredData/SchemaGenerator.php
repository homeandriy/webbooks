<?php
/**
 * JSON-LD schema generation for public theme pages.
 *
 * @package Webbooks
 */

declare(strict_types=1);

namespace Webbooks\StructuredData;

use WP_Term;
use Webbooks\Book\BookMeta;

/**
 * Builds page-level JSON-LD graphs.
 */
final class SchemaGenerator {

	/**
	 * Register structured-data output hooks.
	 */
	public static function register(): void {
		add_action( 'wp_head', array( self::class, 'output' ), 20 );
	}

	/**
	 * Output page-level structured data graphs.
	 */
	public static function output(): void {
		$graphs = array(
			self::website(),
			self::organization(),
			is_page_template( 'download.php' )
				? self::downloadPage()
				: ( is_search() ? self::searchResultsPage() : self::webpage() ),
		);

		if ( is_category() || is_archive() ) {
			$graphs[] = self::collectionPage();
			$graphs[] = self::breadcrumbList();
		}

		if ( is_page_template( 'download.php' ) ) {
			$graphs[] = self::breadcrumbList();
		}

		foreach ( $graphs as $graph ) {
			if ( ! is_array( $graph ) || array() === $graph ) {
				continue;
			}

			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON-LD template encodes the graph safely.
			echo webbooks_render_template_part( 'template-parts/structured-data/json-ld', array( 'graph' => $graph ) );
		}
	}

	/**
	 * Build the WebSite JSON-LD entity.
	 */
	private static function website(): array {
		$url                 = home_url( '/' );
		$search_url_template = get_search_link( '{search_term_string}' );

		return BookMeta::filterSchema(
			array(
				'@context'        => 'https://schema.org',
				'@type'           => 'WebSite',
				'@id'             => trailingslashit( $url ) . '#website',
				'url'             => $url,
				'name'            => get_bloginfo( 'name' ),
				'potentialAction' => array(
					'@type'       => 'SearchAction',
					'target'      => array(
						'@type'       => 'EntryPoint',
						'urlTemplate' => $search_url_template,
					),
					'query-input' => 'required name=search_term_string',
				),
			)
		);
	}

	/**
	 * Build the Organization JSON-LD entity.
	 */
	private static function organization(): array {
		$url      = home_url( '/' );
		$logo_id  = (int) get_theme_mod( 'custom_logo' );
		$logo_url = $logo_id > 0 ? wp_get_attachment_image_url( $logo_id, 'full' ) : '';

		return BookMeta::filterSchema(
			array(
				'@context' => 'https://schema.org',
				'@type'    => 'Organization',
				'@id'      => trailingslashit( $url ) . '#organization',
				'name'     => get_bloginfo( 'name' ),
				'url'      => $url,
				'logo'     => $logo_url,
			)
		);
	}

	/**
	 * Build the generic WebPage JSON-LD entity.
	 */
	private static function webpage(): array {
		$url   = self::currentUrl();
		$title = wp_get_document_title();

		return BookMeta::filterSchema(
			array(
				'@context' => 'https://schema.org',
				'@type'    => 'WebPage',
				'@id'      => trailingslashit( $url ) . '#webpage',
				'url'      => $url,
				'name'     => $title,
				'isPartOf' => array(
					'@id' => trailingslashit( home_url( '/' ) ) . '#website',
				),
			)
		);
	}

	/**
	 * Build the CollectionPage JSON-LD entity for archives.
	 */
	private static function collectionPage(): array {
		$url  = self::currentUrl();
		$name = wp_get_document_title();

		return BookMeta::filterSchema(
			array(
				'@context' => 'https://schema.org',
				'@type'    => 'CollectionPage',
				'@id'      => trailingslashit( $url ) . '#collection',
				'url'      => $url,
				'name'     => $name,
				'isPartOf' => array(
					'@id' => trailingslashit( home_url( '/' ) ) . '#website',
				),
			)
		);
	}

	/**
	 * Build the JSON-LD entity for the download page.
	 */
	private static function downloadPage(): array {
		$url = self::currentUrl();

		return BookMeta::filterSchema(
			array(
				'@context' => 'https://schema.org',
				'@type'    => 'WebPage',
				'@id'      => trailingslashit( $url ) . '#download-page',
				'url'      => $url,
				'name'     => wp_get_document_title(),
				'isPartOf' => array(
					'@id' => trailingslashit( home_url( '/' ) ) . '#website',
				),
			)
		);
	}

	/**
	 * Build the SearchResultsPage JSON-LD entity.
	 */
	private static function searchResultsPage(): array {
		$url   = self::currentUrl();
		$query = (string) get_search_query();

		return BookMeta::filterSchema(
			array(
				'@context'   => 'https://schema.org',
				'@type'      => 'SearchResultsPage',
				'@id'        => trailingslashit( $url ) . '#search-results',
				'url'        => $url,
				'name'       => wp_get_document_title(),
				'isPartOf'   => array(
					'@id' => trailingslashit( home_url( '/' ) ) . '#website',
				),
				'about'      => $query,
				'mainEntity' => array(
					'@type' => 'SearchAction',
					'query' => $query,
				),
			)
		);
	}

	/**
	 * Build the BreadcrumbList JSON-LD entity.
	 */
	private static function breadcrumbList(): array {
		$items    = array();
		$position = 1;

		$items[] = array(
			'@type'    => 'ListItem',
			'position' => $position++,
			'name'     => get_bloginfo( 'name' ),
			'item'     => home_url( '/' ),
		);

		if ( is_category() ) {
			$term = get_queried_object();
			if ( $term instanceof WP_Term ) {
				$ancestors = array_reverse( get_ancestors( $term->term_id, 'category' ) );
				foreach ( $ancestors as $ancestor_id ) {
					$ancestor = get_term( $ancestor_id, 'category' );
					if ( ! $ancestor instanceof WP_Term ) {
						continue;
					}

					$items[] = array(
						'@type'    => 'ListItem',
						'position' => $position++,
						'name'     => $ancestor->name,
						'item'     => get_term_link( $ancestor ),
					);
				}

				$items[] = array(
					'@type'    => 'ListItem',
					'position' => $position++,
					'name'     => $term->name,
					'item'     => get_term_link( $term ),
				);
			}
		} elseif ( is_archive() ) {
			$archive_title = post_type_archive_title( '', false );
			$items[]       = array(
				'@type'    => 'ListItem',
				'position' => $position++,
				'name'     => '' !== $archive_title ? $archive_title : wp_get_document_title(),
				'item'     => self::currentUrl(),
			);
		}

		if ( is_page_template( 'download.php' ) ) {
			$items[] = array(
				'@type'    => 'ListItem',
				'position' => $position++,
				'name'     => _x( 'Download', 'noun', 'webbooks' ),
				'item'     => self::currentUrl(),
			);
		}

		return BookMeta::filterSchema(
			array(
				'@context'        => 'https://schema.org',
				'@type'           => 'BreadcrumbList',
				'itemListElement' => $items,
			)
		);
	}

	/**
	 * Get the current public URL without an untrusted query string.
	 */
	private static function currentUrl(): string {
		if ( ! empty( $_SERVER['REQUEST_URI'] ) ) {
			$request_uri = sanitize_url( wp_unslash( $_SERVER['REQUEST_URI'] ) );

			return home_url( $request_uri );
		}

		return home_url( '/' );
	}
}
