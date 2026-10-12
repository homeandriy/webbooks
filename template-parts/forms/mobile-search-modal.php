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
						type="search" maxlength="<?php echo esc_attr( (string) WEBBOOKS_SEARCH_MAXIMUM_LENGTH ); ?>"
						aria-label="<?php esc_attr_e( 'Search', 'webbooks' ); ?>"
						aria-describedby="mobile-search-help mobile-search-feedback"
						class="form-control trans form-control-lg main-search"
						data-webbooks-mobile-search-input
						data-idres="search-result-mobile-modal"
						placeholder="<?php esc_attr_e( 'Search...', 'webbooks' ); ?>"
						autofocus
					>
					<button type="submit" class="btn btn-primary"><?php esc_html_e( 'Search', 'webbooks' ); ?></button>
					<p id="mobile-search-help" class="search-help"><?php printf( esc_html__( 'Type at least %d characters to search.', 'webbooks' ), WEBBOOKS_SEARCH_MINIMUM_LENGTH ); ?></p>
					<p id="mobile-search-feedback" class="search-feedback" role="status" aria-live="polite" hidden></p>
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
