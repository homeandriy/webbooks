<form class="form-horizontal" id="main-search" aria-describedby="filter-help">
	<div class="row pd-15">
		<p class="col-xs-12 filter-help" id="filter-help"><?php esc_html_e( 'Choose any filters. Skill level and language are optional.', 'webbooks' ); ?></p>
		<div class="col-xs-12 col-sm-12 mrg-t">
			<label for="category-main"><?php esc_html_e( 'Category', 'webbooks' ); ?></label>
			<?php
			wp_dropdown_categories( array( 'show_option_none' => __( 'All categories', 'webbooks' ), 'orderby' => 'slug', 'value_field' => 'slug', 'child_of' => webbooks_category_root( 'books' ), 'option_none_value' => '', 'id' => 'category-main', 'class' => 'form-control', 'name' => 'category' ) );
			?>
		</div>
		<div class="col-xs-12 col-sm-12"><label for="status-book"><?php esc_html_e( 'Select skill level', 'webbooks' ); ?></label><select id="status-book" class="form-control choose-complexity" ><option value=""><?php esc_html_e( 'Any', 'webbooks' ); ?></option>
		<?php
		foreach ( \Webbooks\Domain\Book\Complexity::cases() as $complexity ) :
			?>
			<option value="<?php echo esc_attr( $complexity->value ); ?>"><?php echo esc_html( $complexity->label() ); ?></option><?php endforeach; ?></select></div>
		<div class="col-xs-12 col-sm-12"><label for="language"><?php esc_html_e( 'Language', 'webbooks' ); ?></label><select id="language" class="form-control choose-complexity" ><option value=""><?php esc_html_e( 'Any', 'webbooks' ); ?></option>
		<?php
		foreach ( \Webbooks\Domain\Book\Language::cases() as $language ) :
			?>
			<option value="<?php echo esc_attr( $language->value ); ?>"><?php echo esc_html( $language->label() ); ?></option><?php endforeach; ?></select></div>
		<div class="col-xs-12 col-sm-12 mrg-b mrg-t"><button type="submit" id="send-data-button" class="btn btn-primary" ><?php esc_html_e( 'Search', 'webbooks' ); ?></button> <button type="reset" class="btn btn-default"><?php esc_html_e( 'Reset filters', 'webbooks' ); ?></button></div>
	</div>
</form>
