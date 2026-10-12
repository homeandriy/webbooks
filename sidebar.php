<?php
/**
 * Шаблон сайдбара (sidebar.php)
 * @package WordPress
 * @subpackage webbooks
 */
?>
<?php
$books_root_category_id = webbooks_category_root( 'books' );
$articles_root_category_id = webbooks_category_root( 'articles' );
?>
<aside class="left-section offcanvas-lg offcanvas-start" id="webbooks-sidebar" tabindex="-1" aria-label="<?php esc_attr_e( 'Toggle navigation', 'webbooks' ); ?>">
<div class="offcanvas-header d-lg-none"><span class="h5 mb-0"><?php esc_html_e( 'Books', 'webbooks' ); ?></span><button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" data-bs-target="#webbooks-sidebar" aria-label="<?php esc_attr_e( 'Close', 'webbooks' ); ?>"></button></div>
<div class="offcanvas-body">
	<section class="sidebar">
		<!-- Start Sidebar Menu -->
		<ul class="sidebar-menu">
			<li class="divider"><i class="fa fa-book fa-fw" aria-hidden="true"></i><span><?php esc_html_e( 'Books', 'webbooks' ); ?></span></li>
			<?php
				wp_list_categories(
					array(
						'show_option_all'    => '',
						'child_of'           => $books_root_category_id,
						'orderby'            => 'count',
						'order'              => 'DESC',
						'use_desc_for_title' => 1,
						'hide_empty'         => 0,
						'title_li'           => '',
						'style'              => 'list',
						'hierarchical'       => 1,
						'echo'               => 1,
						'current_category'   => 1,
					)
				);
				?>
			<li class="divider"><i class="fa fa-list fa-fw" aria-hidden="true"></i><span><?php esc_html_e( 'Articles', 'webbooks' ); ?></span></li>
			<?php
				wp_list_categories(
					array(
						'show_option_all'  => '',
						'child_of'         => $articles_root_category_id,
						'orderby'          => 'count',
						'order'            => 'DESC',
						'hide_empty'       => 0,
						'title_li'         => '',
						'style'            => 'list',
						'hierarchical'     => 1,
						'echo'             => 1,
						'current_category' => 1,
					)
				);
				?>
            <?php $support_url = \Webbooks\Support\AuthorSupport::publishedPageUrl(); ?>
            <?php if ( $support_url !== '' ) : ?>
            <li class="sidebar-support">
				<a class="support-project-link support-project-link--sidebar" href="<?php echo esc_url( $support_url ); ?>">
					<i class="fa fa-heart support-project-link__heart" aria-hidden="true"></i>
					<span><?php esc_html_e( 'Support the project', 'webbooks' ); ?></span>
				</a>
			</li>
            <?php endif; ?>
			<li>
				<p class="copyright">
					&#169; 2015-<?php echo esc_html( gmdate( 'Y' ) ); ?> <a href="<?php echo esc_url( \Webbooks\Localization\Polylang::translatedPostUrl( WEBBOOKS_PORTFOLIO_PAGE_ID ) ); ?>"><strong>Andrii Beznosko</strong></a>
				</p>
			</li>
			<li>
				<?php echo webbooks_partner_banner(); ?>
			</li>
		</ul>
		<div class="sidebar-widgets"><?php webbooks_render_sidebar_widgets(); ?></div>
		<!-- /. Sidebar Menu -->
	</section>
	<!-- /. Sidebar Section -->
</div>
</aside>
