<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"><div class="modal-dialog modal-lg"><div class="load-content"><div class="modal-content" id="container_for_post"></div></div></div></div>
<div class="modal fade" id="write-me" tabindex="-1" role="dialog" aria-labelledby="contactModalLabel" aria-hidden="true"><div class="modal-dialog" role="document"><div class="modal-content"><div class="modal-header"><h4 class="modal-title" id="contactModalLabel"><?php esc_html_e( 'Contact us', 'webbooks' ); ?></h4><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body">
<?php
// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Ninja Forms shortcode outputs required form HTML/JS.
	echo do_shortcode( '[ninja_form id=2]' );
?>
</div></div></div></div>
<div class="modal fade bs-webbooks-modal-lg" tabindex="-1" role="dialog" aria-labelledby="premiumModalLabel" aria-hidden="true"><div class="modal-dialog modal-lg" role="document"><div class="modal-content"><div class="modal-header"><h4 class="modal-title" id="premiumModalLabel"><?php esc_html_e( '- WP Star Premium themes and plugins for free -', 'webbooks' ); ?></h4><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div></div></div></div>
