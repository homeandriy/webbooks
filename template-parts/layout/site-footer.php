<?php
/**
 * Primary site footer markup.
 *
 * @package WordPress
 * @subpackage webbooks
 */

?>

			<div id="wptime-plugin-preloader"></div>
			<?php if ( has_nav_menu( 'bottom' ) ) : ?>
				<nav class="site-footer-menu" aria-label="<?php esc_attr_e( 'Footer navigation', 'webbooks' ); ?>">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'bottom',
							'container'      => false,
							'menu_class'     => 'site-footer-menu__list',
							'menu_id'        => 'webbooks-footer-menu',
							'depth'          => 1,
							'fallback_cb'    => false,
						)
					);
					?>
				</nav>
			<?php endif; ?>
			<?php get_template_part( 'template-parts/footer/modals' ); ?>
		</div>

		<!-- End Google Tag Manager -->
		<?php wp_footer(); ?>
	</body>
</html>
