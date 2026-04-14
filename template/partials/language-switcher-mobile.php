<?php
/**
 * Mobile/tablet language switcher.
 *
 * @package WordPress
 * @subpackage webbooks
 */

if ( ! function_exists( 'pll_the_languages' ) ) {
    return;
}

$languages = pll_the_languages(
    [
        'raw' => 1,
    ]
);

if ( empty( $languages ) || ! is_array( $languages ) ) {
    return;
}
?>
<div class="language-switcher language-switcher--mobile hidden-lg hidden-md" aria-label="<?php esc_attr_e( 'Language switcher', 'webbooks' ); ?>">
    <ul class="language-switcher__list list-inline">
        <?php foreach ( $languages as $language ) : ?>
            <?php
            $is_current = ! empty( $language['current_lang'] );
            $short_name = '';

            if ( ! empty( $language['slug'] ) ) {
                $short_name = strtoupper( (string) $language['slug'] );
            } elseif ( ! empty( $language['locale'] ) ) {
                $short_name = strtoupper( substr( (string) $language['locale'], 0, 2 ) );
            }
            ?>
            <li class="language-switcher__item<?php echo $is_current ? ' is-current' : ''; ?>">
                <a
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
                    <span class="language-switcher__label"><?php echo esc_html( $short_name ?: ( $language['name'] ?? '' ) ); ?></span>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
