<?php
/**
 * Temlate Image gallery
 *
 * @package Webbooks
 */

/**
 * Helper
 *
 * @var WP_Post $post
 */
global $post;
$image_gallery_in = get_field( 'gallery', $post->ID );
$image_gallery_s3 = class_exists( 'S3_Service' ) ? json_decode( get_post_meta( $post->ID, S3_Service::IMAGES_META_KEY, true ) ) : array();

if ( ! empty( $image_gallery_in ) ) : ?>
	<div class="book-gallery js-book-gallery">
		<a class="book-gallery__main js-book-gallery-main" href="#" aria-label="<?php esc_attr_e( 'Open image gallery', 'webbooks' ); ?>" data-current-index="0">
			<img
				src="<?php echo esc_url( $image_gallery_in[0]['sizes']['thumbnail'] ); ?>"
				class="first"
				alt="<?php echo esc_attr( $image_gallery_in[0]['alt'] ); ?>"
			/>
		</a>
		<div id="gallery_01">
		<?php foreach ( $image_gallery_in as $image ) : ?>
			<div>
				<a
					class="book-gallery__thumbnail js-book-gallery-item"
					href="<?php echo esc_url( $image['url'] ); ?>"
					data-preview-src="<?php echo esc_url( $image['sizes']['thumbnail'] ); ?>"
					data-caption="<?php echo esc_attr( $image['alt'] ); ?>"
				>
					<img
						src="<?php echo esc_url( $image['sizes']['small-thumb'] ); ?>"
						alt="<?php echo esc_attr( $image['alt'] ); ?>"
						loading="lazy"
					/>
				</a>
			</div>
			<?php endforeach; ?>
		</div>
	</div>
<?php elseif ( ! empty( $image_gallery_s3 ) ) : ?>
	<div class="book-gallery js-book-gallery">
		<a class="book-gallery__main js-book-gallery-main" href="#" aria-label="<?php esc_attr_e( 'Open image gallery', 'webbooks' ); ?>" data-current-index="0">
			<img
				src="<?php echo esc_url( $image_gallery_s3[0] ); ?>"
				class="first"
				alt="<?php echo esc_attr( $post->post_title ); ?>"
			/>
		</a>
		<div id="gallery_01">
		<?php foreach ( $image_gallery_s3 as $image ) : ?>
			<div>
				<a
					class="book-gallery__thumbnail js-book-gallery-item"
					href="<?php echo esc_url( $image ); ?>"
					data-preview-src="<?php echo esc_url( $image ); ?>"
					data-caption="<?php echo esc_attr( $post->post_title ); ?>"
					>
					<img
						src="<?php echo esc_url( $image ); ?>"
						alt="<?php echo esc_attr( $post->post_title ); ?>"
						loading="lazy"
					/>
				</a>
			</div>
			<?php endforeach; ?>
		</div>
	</div>
<?php else : ?>
	<div class="book-gallery js-book-gallery">
		<a class="book-gallery__main js-book-gallery-main" href="#" aria-label="<?php esc_attr_e( 'Open image gallery', 'webbooks' ); ?>" data-current-index="0">
			<img
				src="<?php echo esc_url( get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' ) ); ?>"
				alt="<?php echo esc_attr( $post->post_title ); ?>"
			/>
		</a>
		<div id="gallery_01">
		<div>
			<a class="book-gallery__thumbnail js-book-gallery-item"
				href="<?php echo esc_url( get_the_post_thumbnail_url( get_the_ID(), 'full' ) ); ?>"
				data-preview-src="<?php echo esc_url( get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' ) ); ?>"
				data-caption="<?php echo esc_attr( get_the_title( get_the_ID() ) ); ?>">
				<img
					src="<?php echo esc_url( get_the_post_thumbnail_url( get_the_ID(), 'small-thumb' ) ); ?>"
					alt="<?php echo esc_attr( get_the_title( get_the_ID() ) ); ?>"
				/>
			</a>
		</div>
	</div>
	</div>
	<?php
endif;
