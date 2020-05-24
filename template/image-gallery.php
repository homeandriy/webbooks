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

$image_gallery_in = get_field( 'gallery', $post->ID );

$image_gallery_s3 = class_exists( 'S3_Service' ) ? json_decode( get_post_meta( $post->ID, S3_Service::IMAGES_META_KEY, true ) ): [];

if ( ! empty( $image_gallery_in ) ) : ?>
    <img id="zoom_03" src="<?php echo $image_gallery_in[0]['sizes']['thumbnail']; ?>"
         class="first" data-zoom-image="<?php echo $image_gallery_in[0]['url']; ?>"
         alt="<?php echo $image_gallery_in[0]['alt']; ?>"/>
    <div id="gallery_01">
		<?php foreach ( $image_gallery_in as $image ): ?>
            <div>
                <a href="#" data-image="<?php echo $image['sizes']['thumbnail']; ?>"
                   data-zoom-image="<?php echo $image['url']; ?>">
                    <img id="zoom_03"
                         src="<?php echo $image['sizes']['small-thumb']; ?>"
                         alt="<?php echo $image['alt']; ?>"/>
                </a>
            </div>
		<?php endforeach; ?>
    </div>
<?php elseif ( ! empty( $image_gallery_s3 ) ): ?>
    <img id="zoom_03" src="<?php echo $image_gallery_s3[0]; ?>"
         class="first" data-zoom-image="<?php echo $image_gallery_s3[0]; ?>"
         alt="<?php echo esc_attr( $post->post_title ); ?>"/>
    <div id="gallery_01">
		<?php foreach ( $image_gallery_s3 as $image ): ?>
            <div>
                <a href="#" data-image="<?php echo $image; ?>"
                   data-zoom-image="<?php echo $image; ?>">
                    <img id="zoom_03"
                         src="<?php echo $image; ?>"
                         alt="<?php echo esc_attr( $post->post_title ); ?>"/>
                </a>
            </div>
		<?php endforeach; ?>
    </div>
<?php else : ?>
    <img id="zoom_03"
         src="<?php echo get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' ); ?>"
         data-zoom-image="<?php echo get_the_post_thumbnail_url( get_the_ID(), 'full' ); ?>"/>
    <div id="gallery_01">
        <div>
            <a href="#"
               data-image="<?php echo get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' ); ?>"
               data-zoom-image="<?php echo get_the_post_thumbnail_url( get_the_ID(), 'full' ); ?>">
                <img id="zoom_03"
                     src="<?php echo get_the_post_thumbnail_url( get_the_ID(), 'small-thumb' ); ?>"
                     alt="<?php echo get_the_title( get_the_ID() ); ?>"/>
            </a>
        </div>
    </div>
<?php
endif; ?>