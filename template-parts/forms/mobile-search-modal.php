<?php
/**
 * Mobile search modal.
 *
 * @package WordPress
 * @subpackage webbooks
 */

?>
<div class="modal fade" id="mobile-search-modal" tabindex="-1" role="dialog" aria-labelledby="mobileSearchModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="mobileSearchModalLabel"><?php esc_html_e( 'Search', 'webbooks' ); ?></h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php esc_attr_e( 'Close', 'webbooks' ); ?>"></button>
			</div>
			<div class="modal-body">
				<div class="input-group">
					<label class="visually-hidden" for="mobile-search-input"><?php esc_html_e( 'Search', 'webbooks' ); ?></label>
					<input
						id="mobile-search-input"
						type="text"
						class="form-control trans input-lg main-search"
						data-idres="search-result-mobile-modal"
						placeholder="<?php esc_attr_e( 'Search...', 'webbooks' ); ?>"
						autofocus
					>
					<div class="dropdown-menu custom-search" id="search-result-mobile-modal-wrap">
						<div class="search-loader" aria-hidden="true">
							<i class="fa fa-spinner fa-spin"></i>
						</div>
						<div class="search-results" id="search-result-mobile-modal"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
