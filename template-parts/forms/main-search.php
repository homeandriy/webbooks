<form class="navbar-form navbar-left hidden-xs hidden-sm pos-rel">
	<input type="text" class="form-control trans input-lg main-search" placeholder="<?php esc_attr_e( 'Search...', 'webbooks' ); ?>">
	<button type="submit" class="btn trans btn-lg " id="search-button"><i class="load-search fa fa-search"></i></button>
	<div class="dropdown-menu mCustomScrollbar custom-search" data-mcs-theme="dark">
		<div class="search-loader" aria-hidden="true">
			<i class="fa fa-spinner fa-spin"></i>
		</div>
		<div class="search-results" id="search-result"></div>
	</div>
</form>
