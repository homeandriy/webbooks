<?php

namespace Webbooks\Search;

class SearchService {
	public function filter( string $category, string $complexity, string $language, int $page, string $siteLanguage = '' ): \WP_Query {
		$args = array(
			'post_type' => 'post',
			'post_status' => 'publish',
			'has_password' => false,
			'posts_per_page' => 12,
			'paged' => max( 1, min( 1000, $page ) ),
			'category_name' => sanitize_title( $category ),
			'orderby' => array( 'comment_count' => 'DESC', 'ID' => 'DESC' ),
			'ignore_sticky_posts' => true,
		);
		$conditions = array( 'relation' => 'AND', array( 'key' => 'autor', 'value' => '', 'compare' => '!=' ) );
		foreach ( array( 'complexity' => $complexity, 'language' => $language ) as $key => $value ) {
			if ( $value !== '' ) {
				$enum = $key === 'complexity' ? \Webbooks\Domain\Book\Complexity::fromNullable( $value ) : \Webbooks\Domain\Book\Language::fromNullable( $value );
				if ( $enum === null ) {
					$args['post__in'] = array( 0 );
				} else {
					$conditions[] = array( 'key' => $key, 'value' => $enum->value, 'compare' => '=' );
				}
			}
		}
		if ( count( $conditions ) > 1 ) {
			$args['meta_query'] = $conditions;
		}
		if ( $siteLanguage !== '' ) {
			$args['lang'] = $siteLanguage;
		}
		// WordPress caches query results and invalidates them on content/meta changes.
		// Render HTML afresh so plugin filters and locale changes cannot leak through a transient.
		return new \WP_Query( $args );
	}
	/** Search each group before applying its display limit; author metadata is searchable. */
	public function search( string $term, string $language = '', int $limit = 12 ): array {
		$results = array( 'books' => array(), 'articles' => array(), 'total' => 0 );
		foreach ( array( 'books', 'articles' ) as $group ) {
			$query = $this->queryGroup( $term, $language, $group, $limit );
			$results['total'] += (int) $query->found_posts;
			foreach ( $query->posts as $post ) {
				$meta = \Webbooks\Book\BookMeta::getNormalizedMeta( (int) $post->ID );
				$results[ $group ][] = array(
					'id' => (int) $post->ID,
					'title' => $meta['title'],
					'permalink' => get_permalink( $post ),
					'author' => $meta['author'],
					'publisher' => $meta['publisher'],
					'language' => $meta['language'],
					'thumbnail' => get_the_post_thumbnail_url( $post, 'thumbnail' ) ?: '',
				);
			}
		}
		return $results;
	}

	private function queryGroup( string $term, string $language, string $group, int $limit ): \WP_Query {
		global $wpdb;
		$query = new \WP_Query();
		$like = '%' . $wpdb->esc_like( $term ) . '%';
		// Scope the temporary filter by object identity so nested/plugin queries are unaffected.
		$where_filter = static function ( string $where, \WP_Query $candidate ) use ( $query, $wpdb, $like, $group ): string {
			if ( $candidate !== $query ) {
				return $where;
			}
			$author = "SELECT 1 FROM {$wpdb->postmeta} wb_author WHERE wb_author.post_id = {$wpdb->posts}.ID AND wb_author.meta_key = 'autor' AND TRIM(wb_author.meta_value) <> ''";
			$title = $wpdb->prepare( "{$wpdb->posts}.post_title LIKE %s", $like );
			if ( $group === 'books' ) {
				$match = $wpdb->prepare( "EXISTS ($author AND wb_author.meta_value LIKE %s)", $like );
				return $where . " AND EXISTS ($author) AND ($title OR $match)";
			}
			$content = $wpdb->prepare( "{$wpdb->posts}.post_content LIKE %s", $like );
			return $where . " AND NOT EXISTS ($author) AND ($title OR $content)";
		};
		$args = array(
			'post_type' => 'post',
			'post_status' => 'publish',
			'has_password' => false,
			'posts_per_page' => max( 1, min( 50, $limit ) ),
			'ignore_sticky_posts' => true,
			'orderby' => array( 'date' => 'DESC', 'ID' => 'DESC' ),
			'suppress_filters' => false,
		);
		if ( $language !== '' ) {
			$args['lang'] = $language;
		}
		add_filter( 'posts_where', $where_filter, 10, 2 );
		try {
			$query->query( $args );
		} finally {
			remove_filter( 'posts_where', $where_filter, 10 );
		}
		return $query;
	}
}
