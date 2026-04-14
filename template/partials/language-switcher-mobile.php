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
<div class="language-switcher language-switcher--mobile hidden-lg hidden-md" aria-label="<?php esc_attr_e( 'Language switcher', 'webbooks' ); ?>">
    <button
        type="button"
        class="language-switcher__mobile-toggle"
        data-language-switcher-open
        aria-haspopup="true"
        aria-controls="language-switcher-modal"
        aria-label="<?php esc_attr_e( 'Language switcher', 'webbooks' ); ?>"
    >
        <?php if ( ! empty( $current_language['flag'] ) ) : ?>
            <span class="language-switcher__flag" aria-hidden="true">
                <img src="<?php echo esc_url( $current_language['flag'] ); ?>" alt="" loading="lazy" />
            </span>
        <?php endif; ?>
    </button>
</div>

<div class="language-switcher-modal" id="language-switcher-modal" hidden aria-hidden="true">
    <div class="language-switcher-modal__backdrop" data-language-switcher-close></div>
    <div class="language-switcher-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="language-switcher-modal-label">
        <div class="language-switcher-modal__header">
            <button type="button" class="language-switcher-modal__close" data-language-switcher-close aria-label="<?php esc_attr_e( 'Close', 'webbooks' ); ?>">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="language-switcher-modal__title" id="language-switcher-modal-label"><?php esc_html_e( 'Language', 'webbooks' ); ?></h4>
        </div>
        <div class="language-switcher-modal__body">
            <ul class="language-switcher-modal__list">
                <?php foreach ( $languages as $language ) : ?>
                    <?php $is_current = ! empty( $language['current_lang'] ); ?>
                    <li class="language-switcher-modal__item<?php echo $is_current ? ' is-current' : ''; ?>">
                        <a
                            class="language-switcher-modal__link"
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
                            <span class="language-switcher-modal__name"><?php echo esc_html( $language['name'] ?? '' ); ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
