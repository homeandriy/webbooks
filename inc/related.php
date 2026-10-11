<?php
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
	$key = md5( wp_json_encode( $arguments ) . wp_cache_get_last_changed( 'posts' ) . wp_cache_get_last_changed( 'terms' ) );
	$ids = wp_cache_get( $key, 'webbooks_related' );
	if ( $ids === false ) {
		$ids = ( new WP_Query( $arguments ) )->posts;
		wp_cache_set( $key, $ids, 'webbooks_related', 600 );
	}
	return new WP_Query( array( 'post_type' => 'post', 'post_status' => 'publish', 'has_password' => false, 'post__in' => $ids ?: array( 0 ), 'orderby' => 'post__in', 'posts_per_page' => count( $ids ) ?: 1, 'no_found_rows' => true ) );
}
