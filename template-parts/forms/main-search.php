<?php
/**
 * Desktop search form.
 *
 * @package WordPress
 * @subpackage webbooks
 */

?>
<form class="navbar-form navbar-left d-none d-lg-block pos-rel">
	<label class="visually-hidden" for="main-search-input"><?php esc_html_e( 'Search', 'webbooks' ); ?></label>
	<input id="main-search-input" type="text" class="form-control trans input-lg main-search" placeholder="<?php esc_attr_e( 'Search...', 'webbooks' ); ?>">
	<button type="submit" class="btn trans btn-lg" id="search-button" aria-label="<?php esc_attr_e( 'Search', 'webbooks' ); ?>"><i class="load-search fa fa-search" aria-hidden="true"></i></button>
	<div class="dropdown-menu custom-search">
		<div class="search-loader" aria-hidden="true">
			<i class="fa fa-spinner fa-spin"></i>
		</div>
		<div class="search-results" id="search-result"></div>
	</div>
</form>
