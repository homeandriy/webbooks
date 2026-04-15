<?php

use Webbooks\Book\BookMeta;

add_action( 'wp_head', 'webbooks_output_structured_data', 20 );
function webbooks_output_structured_data(): void {
	$graphs = array(
		webbooks_schema_website(),
		webbooks_schema_organization(),
		is_page_template( 'download.php' )
			? webbooks_schema_download_page()
			: ( is_search() ? webbooks_schema_search_results_page() : webbooks_schema_webpage() ),
	);

	if ( is_category() || is_archive() ) {
		$graphs[] = webbooks_schema_collection_page();
		$graphs[] = webbooks_schema_breadcrumb_list();
	}

	if ( is_page_template( 'download.php' ) ) {
		$graphs[] = webbooks_schema_breadcrumb_list();
	}

	foreach ( $graphs as $graph ) {
		if ( ! is_array( $graph ) || $graph === array() ) {
			continue;
		}

		echo '<script type="application/ld+json">';
		echo wp_json_encode( $graph, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
		echo '</script>' . PHP_EOL;
	}
}

function webbooks_schema_website(): array {
	$url               = home_url( '/' );
	$searchUrlTemplate = get_search_link( '{search_term_string}' );

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
					'urlTemplate' => $searchUrlTemplate,
				),
				'query-input' => 'required name=search_term_string',
			),
		)
	);
}

function webbooks_schema_organization(): array {
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

function webbooks_schema_webpage(): array {
	$url   = webbooks_get_current_url();
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

function webbooks_schema_collection_page(): array {
	$url  = webbooks_get_current_url();
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

function webbooks_schema_download_page(): array {
	$url = webbooks_get_current_url();

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

function webbooks_schema_search_results_page(): array {
	$url   = webbooks_get_current_url();
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

function webbooks_schema_breadcrumb_list(): array {
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
		$items[] = array(
			'@type'    => 'ListItem',
			'position' => $position++,
			'name'     => '' !== $archive_title ? $archive_title : wp_get_document_title(),
			'item'     => webbooks_get_current_url(),
		);
	}

	if ( is_page_template( 'download.php' ) ) {
		$items[] = array(
			'@type'    => 'ListItem',
			'position' => $position++,
			'name'     => _x( 'Download', 'noun', 'webbooks' ),
			'item'     => webbooks_get_current_url(),
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

function webbooks_get_current_url(): string {
	if ( ! empty( $_SERVER['REQUEST_URI'] ) ) {
		return home_url( wp_unslash( $_SERVER['REQUEST_URI'] ) );
	}

	return home_url( '/' );
}
