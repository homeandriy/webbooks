<form class="navbar-form navbar-left hidden-xs hidden-sm pos-rel">
	<input type="search" maxlength="200" aria-describedby="desktop-search-help" class="form-control trans input-lg main-search" aria-label="<?php esc_attr_e( 'Search', 'webbooks' ); ?>" placeholder="<?php esc_attr_e( 'Search...', 'webbooks' ); ?>">
	<button type="submit" class="btn trans btn-lg " id="search-button" aria-label="<?php esc_attr_e( 'Search', 'webbooks' ); ?>"><i class="load-search fa fa-search" aria-hidden="true"></i></button>
	<p id="desktop-search-help" class="search-help"><?php esc_html_e( 'Type 4 characters for suggestions, or press Enter to search a shorter term.', 'webbooks' ); ?></p>
	<p id="desktop-search-feedback" class="sr-only search-feedback" role="status" aria-live="polite"></p>
	<div class="dropdown-menu mCustomScrollbar custom-search" data-mcs-theme="dark">
		<div class="search-loader" aria-hidden="true">
			<i class="fa fa-spinner fa-spin"></i>
		</div>
		<div class="search-results" id="search-result"></div>
	</div>
</form>
