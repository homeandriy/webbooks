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
			<div class="col-12 col-md-4 col-lg-4 col-xl-3">
				<img width="390"
					height="440"
					class="img-fluid"
					src="<?php echo esc_url( get_the_post_thumbnail_url( $post, 'medium' ) ?: webbooks_book_placeholder_url() ); ?>"
					alt="<?php echo esc_attr( get_the_title() ); ?>"
					loading="lazy"
				>
			</div>
			<div class="col-12 col-md-8 col-lg-8 col-xl-9">
				<h4 class="book-heading"><a href="<?php echo esc_url( get_the_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a>
				</h4>
				<p class="list-group-item-text">
					<?php echo wp_kses_post( wp_trim_words( get_the_content(), 25, '...' ) ); ?>
				</p>
			</div>
			<div class="next-reed">
				<p>
					<a href="<?php echo esc_url( get_the_permalink() ); ?>" class="btn header-button btn-info header-actions"><?php esc_html_e( 'Read more', 'webbooks' ); ?></a>
				</p>
			</div>
		</div>
	</div>
</div>
