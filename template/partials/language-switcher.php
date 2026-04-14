<?php
/**
 * Desktop language switcher.
 *
 * @package WordPress
 * @subpackage webbooks
 */

if ( function_exists( 'pll_the_languages' ) ) :
    ?>
    <div class="navbar-right language-switcher language-switcher--desktop hidden-xs hidden-sm">
        <?php
        pll_the_languages(
            [
                'show_flags' => 1,
                'show_names' => 1,
            ]
        );
        ?>
    </div>
<?php endif; ?>
