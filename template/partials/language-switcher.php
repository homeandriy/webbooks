<?php
/**
 * Desktop language switcher.
 *
 * @package WordPress
 * @subpackage webbooks
 */

if ( ! function_exists( 'pll_the_languages' ) ) {
	return;
}

$languages = pll_the_languages(
	array(
		'raw' => 1,
	)
);

if ( empty( $languages ) || ! is_array( $languages ) ) {
	return;
}

$current_language = null;
foreach ( $languages as $language ) {
	if ( ! empty( $language['current_lang'] ) ) {
		$current_language = $language;
		break;
	}
}

if ( empty( $current_language ) ) {
	$current_language = reset( $languages );
}
?>
<div class="navbar-right language-switcher language-switcher--desktop hidden-xs hidden-sm" aria-label="<?php esc_attr_e( 'Language switcher', 'webbooks' ); ?>">
	<button
		type="button"
		class="language-switcher__current"
		aria-haspopup="true"
		aria-expanded="false"
	>
		<?php if ( ! empty( $current_language['flag'] ) ) : ?>
			<span class="language-switcher__flag" aria-hidden="true">
				<img src="<?php echo esc_url( $current_language['flag'] ); ?>" alt="" loading="lazy" />
			</span>
		<?php endif; ?>
		<span class="language-switcher__name"><?php echo esc_html( $current_language['name'] ?? '' ); ?></span>
		<span class="caret" aria-hidden="true"></span>
	</button>
	<ul class="language-switcher__menu" role="menu">
		<?php foreach ( $languages as $language ) : ?>
			<?php $is_current = ! empty( $language['current_lang'] ); ?>
			<li class="language-switcher__item<?php echo $is_current ? ' is-current' : ''; ?>" role="none">
				<a
					role="menuitem"
					class="language-switcher__link"
					href="<?php echo esc_url( $language['url'] ?? '#' ); ?>"
					lang="<?php echo esc_attr( $language['slug'] ?? '' ); ?>"
					hreflang="<?php echo esc_attr( $language['slug'] ?? '' ); ?>"
					aria-current="<?php echo $is_current ? 'page' : 'false'; ?>"
				>
					<?php if ( ! empty( $language['flag'] ) ) : ?>
						<span class="language-switcher__flag" aria-hidden="true">
							<img src="<?php echo esc_url( $language['flag'] ); ?>" alt="" loading="lazy" />
						</span>
					<?php endif; ?>
					<span class="language-switcher__name"><?php echo esc_html( $language['name'] ?? '' ); ?></span>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
