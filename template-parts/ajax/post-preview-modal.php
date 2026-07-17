<?php
/**
 * AJAX post preview modal content.
 *
 * @package WordPress
 * @subpackage webbooks
 */

?>
<div class="modal-header">
	<h5 class="modal-title">
		<?php the_title(); ?>
	</h5>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php esc_attr_e( 'Close', 'webbooks' ); ?>"></button>
</div>
<div class="modal-body modal-body--scrollable" data-mcs-theme="dark">
	<?php the_content(); ?>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
		<?php esc_html_e( 'Close', 'webbooks' ); ?>
	</button>
	<a href="<?php the_permalink(); ?>" class="btn btn-primary">
		<?php esc_html_e( 'Read full', 'webbooks' ); ?>
	</a>
</div>
