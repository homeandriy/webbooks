<?php
/**
 * Шаблон сайдбара (sidebar.php)
 *
 * @package WordPress
 * @subpackage webbooks
 */

?>
<?php
$resolve_sidebar_root_category = static function ( int $fallback_term_id ): int {
	if ( ! function_exists( 'pll_get_term' ) || ! function_exists( 'pll_current_language' ) ) {
		return $fallback_term_id;
	}

	$current_language = pll_current_language( 'slug' );
	if ( empty( $current_language ) ) {
		return $fallback_term_id;
	}

	$translated_term_id = (int) pll_get_term( $fallback_term_id, (string) $current_language );

	return $translated_term_id > 0 ? $translated_term_id : $fallback_term_id;
};

$books_root_category_id    = $resolve_sidebar_root_category( 18 );
$articles_root_category_id = $resolve_sidebar_root_category( 19 );
$portfolio_url             = add_query_arg(
	array(
		'utm_source'   => 'webbooks',
		'utm_medium'   => 'sidebar',
		'utm_campaign' => 'portfolio',
	),
	home_url( '/portfolio/' )
);
?>
<aside class="left-section sidebar-offcanvas" id="webbooks-mobile-sidebar" aria-label="<?php esc_attr_e( 'Main navigation', 'webbooks' ); ?>">
	<div class="sidebar-offcanvas__header">
		<a class="sidebar-offcanvas__brand" href="<?php echo esc_url( home_url() ); ?>"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></a>
		<button type="button" class="sidebar-offcanvas__close" data-webbooks-close-offcanvas aria-label="<?php esc_attr_e( 'Close', 'webbooks' ); ?>">
			<span aria-hidden="true">&times;</span>
		</button>
	</div>
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
					&copy; 2015-<?php echo esc_html( gmdate( 'Y' ) ); ?>
					<a href="<?php echo esc_url( $portfolio_url ); ?>">
						<strong>Andrii Beznosko</strong>
					</a>
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
