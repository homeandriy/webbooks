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
            <!-- Modal предпросмотр саттьи -->
            <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="load-content">
                        <div class="modal-content" id="container_for_post"></div>
                    </div>
                </div>
            </div>
            <!-- Modal написать нам -->
            <div class="modal fade" id="write-me" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">Написать нам</h4>
                        </div>
                        <div class="modal-body"><?php  echo do_shortcode( '[ninja_form id=2]'); ?></div>
                    </div>
                </div>
            </div>
            <div class="modal fade bs-webbooks-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">- WP Star Премиум темы и плагины бесплатно -</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
            ga('create', 'UA-57400123-2', 'auto');
            ga('send', 'pageview');
        </script>
        <!-- End Google Tag Manager -->
		<?php wp_footer();?>
	</body>
</html>
