<div class="modal fade" id="mobile-search-modal" tabindex="-1" role="dialog" aria-labelledby="mobileSearchModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="mobileSearchModalLabel"><?php esc_html_e( 'Search', 'webbooks' ); ?></h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php esc_attr_e( 'Close', 'webbooks' ); ?>"></button>
			</div>
			<div class="modal-body">
				<form class="mobile-search-form">
					<input
						type="search" maxlength="200"
						aria-label="<?php esc_attr_e( 'Search', 'webbooks' ); ?>"
						aria-describedby="mobile-search-help"
						class="form-control trans form-control-lg main-search"
						data-webbooks-mobile-search-input
						data-idres="search-result-mobile-modal"
						placeholder="<?php esc_attr_e( 'Search...', 'webbooks' ); ?>"
						autofocus
					>
					<button type="submit" class="btn btn-primary"><?php esc_html_e( 'Search', 'webbooks' ); ?></button>
					<p id="mobile-search-help" class="search-help"><?php esc_html_e( 'Type at least 3 characters to search.', 'webbooks' ); ?></p>
					<p id="mobile-search-feedback" class="visually-hidden search-feedback" role="status" aria-live="polite"></p>
					<div class="dropdown-menu mCustomScrollbar custom-search" data-mcs-theme="dark" id="search-result-mobile-modal-wrap">
						<div class="search-loader" aria-hidden="true">
							<i class="fa fa-spinner fa-spin"></i>
						</div>
						<div class="search-results" id="search-result-mobile-modal"></div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
