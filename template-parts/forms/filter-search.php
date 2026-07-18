<?php
/**
 * Catalog filter form.
 *
 * @package Webbooks
 */

$category_select = wp_dropdown_categories(
	array(
		'show_option_none' => __( 'Category', 'webbooks' ),
		'orderby'          => 'slug',
		'value_field'      => 'slug',
		'child_of'         => \Webbooks\Localization\Polylang::translatedTermId( 18 ),
		'class'            => 'form-control',
		'id'               => 'category-main',
		'echo'             => false,
	)
);
?>
<form class="form-horizontal" id="main-search">
	<div class="row pd-15">
		<div class="col-12 mrg-t">
			<label for="category-main"><?php esc_html_e( 'Category', 'webbooks' ); ?></label>
			<?php echo $category_select; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_dropdown_categories() generates the select markup. ?>
		</div>
		<div class="col-12">
			<label for="status-book"><?php esc_html_e( 'Select skill level', 'webbooks' ); ?></label>
			<select id="status-book" class="form-control choose-complexity" disabled>
				<option value=""><?php esc_html_e( 'Select', 'webbooks' ); ?></option>
				<?php foreach ( \Webbooks\Domain\Book\Complexity::cases() as $complexity ) : ?>
					<option value="<?php echo esc_attr( $complexity->value ); ?>">
						<?php echo esc_html( $complexity->label() ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="col-12">
			<label for="language"><?php esc_html_e( 'Language', 'webbooks' ); ?></label>
			<select id="language" class="form-control choose-complexity" disabled>
				<option value=""><?php esc_html_e( 'Select', 'webbooks' ); ?></option>
				<?php foreach ( \Webbooks\Domain\Book\Language::cases() as $language ) : ?>
					<option value="<?php echo esc_attr( $language->value ); ?>">
						<?php echo esc_html( $language->label() ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="col-12 mrg-b mrg-t">
			<button type="submit" id="send-data-button" class="btn btn-primary" disabled>
				<?php esc_html_e( 'Search', 'webbooks' ); ?>
			</button>
		</div>
	</div>
</form>
