<?php
/** Keep existing Custom Post Templates assignments working during migration. */
add_filter( 'single_template', static function ( string $template ): string {
	$id = get_queried_object_id();
	$native = get_post_meta( $id, '_wp_page_template', true );
	$legacy = get_post_meta( $id, 'custom_post_template', true );
	if ( ( $native === '' || $native === 'default' ) && $legacy === 'single-post-type.php' ) {
		return locate_template( 'single-post-type.php' ) ?: $template;
	}
	return $template;
} );

/** Return a real external article source, never a book download redirect. */
function webbooks_article_source_url( int $post_id ): string {
	$source = \Webbooks\Book\BookMeta::getFieldText( $post_id, 'download' );
	$parts  = wp_parse_url( $source );

	if ( ! is_array( $parts ) || empty( $parts['host'] )
		|| ! in_array( strtolower( $parts['scheme'] ?? '' ), array( 'http', 'https' ), true ) ) {
		return '';
	}

	$source_host = preg_replace( '/^www\./i', '', $parts['host'] );
	$site_host   = preg_replace( '/^www\./i', '', (string) wp_parse_url( home_url(), PHP_URL_HOST ) );

	return strcasecmp( $source_host, $site_host ) === 0 ? '' : esc_url_raw( $source, array( 'http', 'https' ) );
}
