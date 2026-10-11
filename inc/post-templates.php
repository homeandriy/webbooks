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
