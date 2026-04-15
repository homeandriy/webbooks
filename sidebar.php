<?php
/**
 * Шаблон сайдбара (sidebar.php)
 * @package WordPress
 * @subpackage webbooks
 */
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
						'child_of'           => 18,
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
						'child_of'         => 19,
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
					&#169; 2015-<?php echo esc_html( gmdate( 'Y' ) ); ?> <a href="/portfolio/"><strong>Andrii Beznosko</strong></a>
				</p>
			</li>
			<li>
				<a
						href='https://cityhost.ua/?partner=user28504'
						title='<?php echo esc_attr__( 'Hosting CityHost.ua', 'webbooks' ); ?>'
						target='_blank'
				>
					<img src='https://cityhost.ua/upload_img/ref_banners/banner_240x400.jpg'
						loading="lazy"
						title='<?php echo esc_attr__( 'Hosting CityHost', 'webbooks' ); ?>'
						alt='<?php echo esc_attr__( 'Hosting CityHost', 'webbooks' ); ?>'
					/>
				</a>
			</li>
		</ul>
		<?php dynamic_sidebar( 'left-sidebar' ); ?>
		<!-- /. Sidebar Menu -->
	</section>
	<!-- /. Sidebar Section -->
</aside>
