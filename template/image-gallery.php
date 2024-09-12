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
$image_gallery_s3 = class_exists( 'S3_Service' ) ? json_decode( get_post_meta( $post->ID, S3_Service::IMAGES_META_KEY, true ) ): [];

if ( ! empty( $image_gallery_in ) ) : ?>
    <img id="zoom_03"
         src="<?=$image_gallery_in[0]['sizes']['thumbnail']?>"
         class="first"
         data-zoom-image="<?=$image_gallery_in[0]['url']?>"
         alt="<?=$image_gallery_in[0]['alt']?>"
         loading="lazy"
    />
    <div id="gallery_01">
		<?php foreach ( $image_gallery_in as $image ): ?>
            <div>
                <a
                    href="#"
                    data-image="<?=$image['sizes']['thumbnail']; ?>"
                    data-zoom-image="<?= $image['url']; ?>"
                >
                    <img
                        id="zoom_03"
                        src="<?=$image['sizes']['small-thumb']; ?>"
                        alt="<?=$image['alt']; ?>"
                        loading="lazy"
                    />
                </a>
            </div>
		<?php endforeach; ?>
    </div>
<?php elseif ( ! empty( $image_gallery_s3 ) ): ?>
    <img
        id="zoom_03"
        src="<?=$image_gallery_s3[0]?>"
        class="first"
        data-zoom-image="<?=$image_gallery_s3[0]?>"
        alt="<?=esc_attr( $post->post_title )?>"
        loading="lazy"
    />
    <div id="gallery_01">
		<?php foreach ( $image_gallery_s3 as $image ): ?>
            <div>
                <a
                    href="#"
                    data-image="<?=$image?>"
                    data-zoom-image="<?=$image?>"
                    >
                    <img
                        id="zoom_03"
                        src="<?=$image?>"
                        alt="<?=esc_attr( $post->post_title )?>"
                        loading="lazy"
                    />
                </a>
            </div>
		<?php endforeach; ?>
    </div>
<?php else : ?>
    <img
        id="zoom_03"
        src="<?=get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' )?>"
        data-zoom-image="<?=get_the_post_thumbnail_url( get_the_ID(), 'full' )?>"
        alt="<?=$post->post_title?>"
        loading="lazy"
    >
    <div id="gallery_01">
        <div>
            <a href="#"
               data-image="<?=get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' )?>"
               data-zoom-image="<?=get_the_post_thumbnail_url( get_the_ID(), 'full' )?>">
                <img id="zoom_03"
                     src="<?=get_the_post_thumbnail_url( get_the_ID(), 'small-thumb' )?>"
                     alt="<?=get_the_title( get_the_ID() ); ?>"
                />
            </a>
        </div>
    </div>
<?php endif;
