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
	if ( $id && ( $cover_url = wp_get_attachment_image_url( $id, 'full' ) ) ) {
		$images[] = array( 'id' => $id, 'url' => $cover_url, 'alt' => $book->post_title );
	}
}
if ( ! $images ) {
	printf( '<img class="book-cover-default" src="%s" alt="%s" width="600" height="800" decoding="async" style="display:block;width:auto;max-width:100%%;max-height:480px;height:auto;margin:auto">', esc_url( webbooks_book_placeholder_url() ), esc_attr( $book->post_title ) );
	return;
}
$gallery_id = wp_unique_id( 'book-gallery-' );
?>
<div class="book-gallery" data-book-gallery="<?php echo esc_attr( $gallery_id ); ?>" role="region" aria-label="<?php echo esc_attr( $book->post_title ); ?>">
  <div class="swiper book-gallery-main" tabindex="0">
    <div class="swiper-wrapper">
      <?php foreach ( $images as $index => $image ) : ?>
        <div class="swiper-slide">
          <div class="swiper-zoom-container">
            <img src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ?: $book->post_title ); ?>" loading="<?php echo $index === 0 ? 'eager' : 'lazy'; ?>" decoding="async">
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
  <div class="book-gallery-controls">
    <?php if ( count( $images ) > 1 ) : ?>
      <button type="button" data-gallery-prev aria-label="<?php esc_attr_e( 'Previous', 'webbooks' ); ?>">&larr;</button>
    <?php endif; ?>
    <button type="button" data-gallery-zoom aria-pressed="false"><?php esc_html_e( 'Zoom', 'webbooks' ); ?></button>
    <button type="button" data-gallery-expand><?php esc_html_e( 'Open gallery', 'webbooks' ); ?></button>
    <?php if ( count( $images ) > 1 ) : ?>
      <button type="button" data-gallery-next aria-label="<?php esc_attr_e( 'Next', 'webbooks' ); ?>">&rarr;</button>
    <?php endif; ?>
  </div>
  <?php if ( count( $images ) > 1 ) : ?>
    <div class="swiper book-gallery-thumbs">
      <div class="swiper-wrapper">
        <?php foreach ( $images as $index => $image ) : ?>
          <div class="swiper-slide">
            <a href="<?php echo esc_url( $image['url'] ); ?>" data-gallery-index="<?php echo (int) $index; ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Image %d', 'webbooks' ), $index + 1 ) ); ?>">
              <?php if ( $image['id'] ) : ?>
                <?php echo wp_get_attachment_image( $image['id'], 'small-thumb', false, array( 'loading' => 'lazy' ) ); ?>
              <?php else : ?>
                <img src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" loading="lazy" decoding="async">
              <?php endif; ?>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>
  <dialog class="book-gallery-dialog" aria-label="<?php echo esc_attr( $book->post_title ); ?>">
    <button type="button" class="book-gallery-close" data-gallery-close><?php esc_html_e( 'Close', 'webbooks' ); ?></button>
    <div data-gallery-dialog-content></div>
  </dialog>
</div>
