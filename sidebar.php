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
<aside class="left-section sidebar-offcanvas">
	<section class="sidebar">
		<!-- Start Sidebar Menu -->
		<ul class="sidebar-menu">
			<li class="divider"><i class="fa fa-book fa-2x fa-fw"></i><?php esc_html_e( 'Books', 'webbooks' ); ?></li>
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
			<li class="divider"><i class="fa fa-list fa-2x fa-fw"></i><?php esc_html_e( 'Articles', 'webbooks' ); ?></li>
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
			<li>
				<p class="copyright">
					&#169; 2015-<?php echo esc_html( gmdate( 'Y' ) ); ?> <a href="<?php echo esc_url( (string) webbooks_site_setting( 'copyright_url', home_url( '/' ) ) ); ?>"><strong><?php echo esc_html( get_bloginfo( 'name' ) ); ?></strong></a>
				</p>
			</li>
			<li>
				<?php echo webbooks_partner_banner(); ?>
			</li>
		</ul>
		<?php dynamic_sidebar( 'left-sidebar' ); ?>
		<!-- /. Sidebar Menu -->
	</section>
	<!-- /. Sidebar Section -->
</aside>
