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
			<li class="divider"><i class="fa fa-book fa-2x fa-fw"></i>Книги</li>
			<?php
                wp_list_categories([
                    'show_option_all' => '',
                    'child_of' => 18,
                    'orderby' => 'count',
                    'order' => 'DESC',
                    'use_desc_for_title' => 1,
                    'hide_empty' => 0,
                    'title_li' => '',
                    'style' => 'list',
                    'hierarchical' => 1,
                    'echo' => 1,
                    'current_category' => 1
                ]);
            ?>
			<li class="divider"><i class="fa fa-list fa-2x fa-fw"></i>Статьи</li>
		    <?php
				wp_list_categories([
                    'show_option_all' => '',
                    'child_of' => 19,
                    'orderby' => 'count',
                    'order' => 'DESC',
                    'hide_empty' => 0,
                    'title_li' => '',
                    'style' => 'list',
                    'hierarchical' => 1,
                    'echo' => 1,
                    'current_category' => 1,
                ]);
            ?>
			<li>
				<p class="copyright">
					&#169; 2015-<?= date('Y') ?> <a href="/portfolio/"><strong>Andrii Beznosko</strong></a>
				</p>
			</li>
			<li>
                <a
                        href='https://cityhost.ua/?partner=user28504'
                        title='Хостинг CityHost.ua'
                        target='_blank'
                >
                    <img src='https://cityhost.ua/upload_img/ref_banners/banner_240x400.jpg'
                         loading="lazy"
                         title='Хостинг СитиХост'
                         border='0'
                         alt='Hosting CityHost'
                    />
                </a>
			</li>
		</ul>
		<?php dynamic_sidebar('left-sidebar');?>
		<!-- /. Sidebar Menu -->
	</section>
	<!-- /. Sidebar Section -->
</aside>
