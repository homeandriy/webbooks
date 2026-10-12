<form class="webbooks-search-form me-auto d-none d-lg-flex pos-rel">
	<input type="search" maxlength="<?php echo esc_attr( (string) WEBBOOKS_SEARCH_MAXIMUM_LENGTH ); ?>" aria-describedby="desktop-search-help desktop-search-feedback" class="form-control trans form-control-lg main-search" aria-label="<?php esc_attr_e( 'Search', 'webbooks' ); ?>" placeholder="<?php esc_attr_e( 'Search...', 'webbooks' ); ?>">
	<button type="submit" class="btn trans btn-lg " id="search-button" aria-label="<?php esc_attr_e( 'Search', 'webbooks' ); ?>"><i class="load-search fa fa-search" aria-hidden="true"></i></button>
	<p id="desktop-search-help" class="search-help"><?php printf( esc_html__( 'Type at least %d characters to search.', 'webbooks' ), WEBBOOKS_SEARCH_MINIMUM_LENGTH ); ?></p>
	<p id="desktop-search-feedback" class="search-feedback" role="status" aria-live="polite" hidden></p>
	<div class="dropdown-menu mCustomScrollbar custom-search" data-mcs-theme="dark">
		<div class="search-loader" aria-hidden="true">
			<i class="fa fa-spinner fa-spin"></i>
		</div>
		<div class="search-results" id="search-result"></div>
	</div>
</form>
