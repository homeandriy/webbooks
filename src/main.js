import jQuery from 'jquery';

window.jQuery = jQuery;
window.$ = jQuery;

import '../assets/css/material-design-icons.min.css';
import '../assets/css/jquery.fs.roller.min.css';
import '../assets/css/jquery.mCustomScrollbar.min.css';
import '../assets/css/font-awesome.min.css';
import '../assets/css/jquery.fancybox.css';
import '../assets/css/slick-theme.css';
import '../assets/css/slick.css';
import '../style.css';

import '../assets/js/bootstrap.min.js';
import '../assets/js/jquery.mCustomScrollbar.concat.min.js';
import '../assets/js/jquery.fs.roller.min.js';
import '../assets/js/jquery.elevatezoom.js';
import '../assets/js/jquery.fancybox.js';
import '../assets/js/slick.js';
import '../assets/js/jquery.lazyload.min.js';
import '../assets/js/compat-layer.js';
import '../assets/js/ajax-client.js';
import '../assets/js/custom.js';
import '../assets/js/theme.js';

(function ($) {
    $(document).ready(function () {
        const hydrateLazyImages = function () {
            $('img.lazy[data-original]').each(function () {
                const $img = $(this);
                const original = $img.attr('data-original');

                if (!original || $img.attr('src') === original) {
                    return;
                }

                $img.attr('src', original);
                $img.removeAttr('data-original');
            });
        };

        if ($.fn.lazyload) {
            $('img.lazy').lazyload({
                effect: 'fadeIn',
                load: function () {
                    $(this).removeAttr('data-original');
                }
            });
        }

        hydrateLazyImages();
    });
})(jQuery);
