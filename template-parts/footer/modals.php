<div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content" id="container_for_post"></div>
	</div>
</div>
<div class="modal fade" id="write-me" tabindex="-1" aria-labelledby="writeMeModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="writeMeModalLabel"><?php esc_html_e( 'Contact us', 'webbooks' ); ?></h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
<div class="modal fade bs-webbooks-modal-lg" tabindex="-1" aria-labelledby="webbooksPremiumModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="webbooksPremiumModalLabel"><?php esc_html_e( '- WP Star Premium themes and plugins for free -', 'webbooks' ); ?></h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
		</div>
	</div>
</div>
