<?php
/**
 * Шаблон подвала (footer.php)
 * @package WordPress
 * @subpackage webbooks
 */
?>
            <script>
                (function($){
                    jQuery(document).ready(function($) {
                        $("img.lazy").lazyload({effect:"fadeIn"});
                    });
                })(jQuery);
            </script>;
            <div id="wptime-plugin-preloader"></div>
            <?php get_template_part( 'template-parts/footer/modals' ); ?>
        </div>

        <!-- End Google Tag Manager -->
		<?php wp_footer();?>
	</body>
</html>
