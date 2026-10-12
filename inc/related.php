<?php
/** Invalidate all related lists; old transient generations expire within ten minutes. */
function webbooks_invalidate_related_cache(): void {
	update_option( 'webbooks_related_cache_generation', wp_generate_uuid4(), false );
}
add_action( 'clean_post_cache', 'webbooks_invalidate_related_cache' );
add_action( 'clean_term_cache', 'webbooks_invalidate_related_cache' );
add_action( 'set_object_terms', 'webbooks_invalidate_related_cache' );

/** Cache only IDs; hydrate published posts again so removals are never served as stale HTML. */
function webbooks_related_query( array $arguments ): WP_Query {
	$arguments['post_type'] = 'post';
	$arguments['post_status'] = 'publish';
	$arguments['has_password'] = false;
	$arguments['posts_per_page'] = min( 12, max( 1, (int) ( $arguments['posts_per_page'] ?? 6 ) ) );
	$arguments['orderby'] = array( 'date' => 'DESC', 'ID' => 'DESC' );
	$arguments['no_found_rows'] = true;
	$arguments['fields'] = 'ids';
	$arguments['update_post_meta_cache'] = false;
	$arguments['update_post_term_cache'] = false;
	if ( function_exists( 'pll_current_language' ) ) {
		$arguments['lang'] = pll_current_language( 'slug' );
	}
	// A database-backed generation survives requests even without Redis/Memcached.
	$key = 'webbooks_related_' . md5( wp_json_encode( $arguments ) . get_locale() . get_option( 'webbooks_related_cache_generation', '1' ) );
	$ids = get_transient( $key );
	if ( $ids === false ) {
		$ids = ( new WP_Query( $arguments ) )->posts;
		set_transient( $key, $ids, 10 * MINUTE_IN_SECONDS );
	}
	return new WP_Query( array( 'post_type' => 'post', 'post_status' => 'publish', 'has_password' => false, 'post__in' => $ids ?: array( 0 ), 'orderby' => 'post__in', 'posts_per_page' => count( $ids ) ?: 1, 'no_found_rows' => true ) );
}
