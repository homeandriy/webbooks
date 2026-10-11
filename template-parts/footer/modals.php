<?php
/**
 * Footer modal dialogs.
 *
 * @package WordPress
 * @subpackage webbooks
 */

?>
<div class="modal fade" id="webbooks-post-preview-modal" tabindex="-1" aria-labelledby="webbooks-post-preview-modal-label" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<h2 class="visually-hidden" id="webbooks-post-preview-modal-label"><?php esc_html_e( 'Book preview', 'webbooks' ); ?></h2>
			<div id="container_for_post" aria-live="polite"></div>
		</div>
	</div>
</div>
<div class="modal fade" id="write-me" tabindex="-1" aria-labelledby="writeMeModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="writeMeModalLabel"><?php esc_html_e( 'Contact us', 'webbooks' ); ?></h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php esc_attr_e( 'Close', 'webbooks' ); ?>"></button>
			</div>
			<div class="modal-body">
				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Ninja Forms shortcode outputs required form HTML/JS.
				echo do_shortcode( '[ninja_form id=2]' );
				?>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="webbooks-author-support-modal" tabindex="-1" aria-labelledby="webbooks-author-support-modal-label" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h2 class="modal-title fs-5" id="webbooks-author-support-modal-label"><?php esc_html_e( 'Support the author', 'webbooks' ); ?></h2>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php esc_attr_e( 'Close', 'webbooks' ); ?>"></button>
			</div>
			<div class="modal-body">
				<p class="author-support-modal__status" data-webbooks-support-status aria-live="polite"></p>
				<div class="author-support-modal__cards" data-webbooks-support-card-list></div>
				<div class="author-support-modal__payment-links" data-webbooks-support-link-list></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
					<?php esc_html_e( 'Close', 'webbooks' ); ?>
				</button>
			</div>
		</div>
	</div>
</div>
<div class="modal fade bs-webbooks-modal-lg" tabindex="-1" aria-labelledby="webbooksPremiumModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="webbooksPremiumModalLabel"><?php esc_html_e( '- WP Star Premium themes and plugins for free -', 'webbooks' ); ?></h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php esc_attr_e( 'Close', 'webbooks' ); ?>"></button>
			</div>
		</div>
	</div>
</div>
