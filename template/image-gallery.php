<?php
/** Book gallery, independent of the WordPress gallery shortcode. */
$book = $args['book'] ?? null;
if ( ! $book instanceof WP_Post ) {
	return;
}
$images = array();
$gallery = function_exists( 'get_field' ) ? get_field( 'gallery', $book->ID ) : get_post_meta( $book->ID, 'gallery', true );
if ( ! is_array( $gallery ) ) { $gallery = get_post_meta( $book->ID, 'gallery', true ); }
if ( is_array( $gallery ) ) {
	foreach ( $gallery as $image ) {
		$id = is_array( $image ) ? absint( is_scalar( $image['ID'] ?? $image['id'] ?? 0 ) ? ( $image['ID'] ?? $image['id'] ?? 0 ) : 0 ) : ( is_numeric( $image ) ? absint( $image ) : 0 );
		if ( $id && wp_attachment_is_image( $id ) ) {
			$images[] = array( 'id' => $id, 'url' => wp_get_attachment_image_url( $id, 'full' ), 'alt' => get_post_meta( $id, '_wp_attachment_image_alt', true ) );
		} elseif ( is_array( $image ) && isset( $image['url'] ) && is_string( $image['url'] ) ) {
			$images[] = array( 'id' => 0, 'url' => $image['url'], 'alt' => is_scalar( $image['alt'] ?? null ) ? (string) $image['alt'] : '' );
		}
	}
}
if ( ! $images && class_exists( 'S3_Service' ) && defined( 'S3_Service::IMAGES_META_KEY' ) ) {
	$raw = get_post_meta( $book->ID, S3_Service::IMAGES_META_KEY, true );
	$s3 = is_string( $raw ) ? json_decode( $raw, true ) : null;
	if ( is_array( $s3 ) ) {
		foreach ( $s3 as $url ) {
			if ( is_string( $url ) && esc_url( $url ) !== '' ) {
				$images[] = array( 'id' => 0, 'url' => $url, 'alt' => $book->post_title );
			}
		}
	}
}
	$images = array_values( array_filter( $images, static function ( array $image ): bool {
		$url = is_string( $image['url'] ) ? $image['url'] : '';
		return in_array( wp_parse_url( $url, PHP_URL_SCHEME ), array( 'http', 'https' ), true ) && (bool) wp_parse_url( $url, PHP_URL_HOST );
	} ) );
if ( ! $images ) {
	$id = get_post_thumbnail_id( $book );
	if ( $id ) {
		$images[] = array( 'id' => $id, 'url' => wp_get_attachment_image_url( $id, 'full' ), 'alt' => $book->post_title );
	}
}
if ( ! $images ) {
	return;
}
$gallery_id = wp_unique_id( 'book-gallery-' );
$first = $images[0];
?>
<div class="book-gallery" data-book-gallery="<?php echo esc_attr( $gallery_id ); ?>">
	<?php
	$first_attrs = array( 'class' => 'book-gallery-main first', 'data-zoom-image' => $first['url'] );
	if ( $first['id'] ) {
		echo wp_get_attachment_image( $first['id'], 'medium', false, $first_attrs );
	} else {
		?>
		<img class="book-gallery-main first" src="<?php echo esc_url( $first['url'] ); ?>" data-zoom-image="<?php echo esc_url( $first['url'] ); ?>" alt="<?php echo esc_attr( $first['alt'] ); ?>" decoding="async">
		<?php
	}
	?>
	<div class="book-gallery-thumbs" id="<?php echo esc_attr( $gallery_id ); ?>">
		<?php foreach ( $images as $image ) : ?>
			<div><a href="<?php echo esc_url( $image['url'] ); ?>" data-image="<?php echo esc_url( $image['id'] ? wp_get_attachment_image_url( $image['id'], 'medium' ) : $image['url'] ); ?>" data-zoom-image="<?php echo esc_url( $image['url'] ); ?>">
				<?php if ( $image['id'] ) : ?>
					<?php echo wp_get_attachment_image( $image['id'], 'small-thumb', false, array( 'loading' => 'lazy' ) ); ?>
				<?php else : ?>
					<img src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" loading="lazy" decoding="async">
				<?php endif; ?>
			</a></div>
		<?php endforeach; ?>
	</div>
</div>
