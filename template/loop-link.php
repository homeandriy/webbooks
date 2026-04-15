<?php
/**
 * Запись в цикле (loop.php)
 * @package WordPress
 * @subpackage webbooks
 */

?>
<div class="list-group">
	<div class="list-group-item ">
		<div class="row">
			<div class="col-xs-12 col-sm-4 col-md-4 col-lg-3">
				<img width="390"
					height="440"
					class="media-object"
					src="<?php echo wp_get_attachment_url( get_post_thumbnail_id( $post->ID ) ); ?>"
					alt="<?php echo get_the_title(); ?>"
					loading="lazy"
				>
			</div>
			<div class="col-xs-12 col-sm-8 col-md-8 col-lg-9">
				<h4 class="list-group-item-heading"><a href="<?php echo get_the_permalink(); ?>"><?php echo get_the_title(); ?></a>
				</h4>
				<p class="list-group-item-text">
					<?php echo wp_trim_words( get_the_content(), 25, '...' ); ?>
				</p>
			</div>
			<div class="next-reed">
				<p>
					<a href="<?php echo get_the_permalink(); ?>" class="btn navbar-btn btn-info navbar-right"><?php esc_html_e( 'Read more', 'webbooks' ); ?></a>
				</p>
			</div>
		</div>
	</div>
</div>
