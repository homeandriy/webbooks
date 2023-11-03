<?php
/**
 * Шаблон сайдбара (sidebar.php)
 * @package WordPress
 * @subpackage webbooks
 */
?>
<aside class="left-section sidebar-offcanvas">
	<section class="sidebar">
		<!-- Start Sidebar Search Form - Only visible on Small & Extra Small Devices -->
        <form action="#" method="get" class="user-panel form-rounded hidden-lg hidden-md">
            <div class="input-group">
                <input type="text" class="form-control trans input-lg main-search" data-idres="search-result-mobile"
                       data-toggle="dropdown" onKeyup="searchGlobal(event)" placeholder="Поищем...">
                <div class="dropdown-menu mCustomScrollbar custom-search" data-mcs-theme="dark"
                     id="search-result-mobile-wrap">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <tbody id="search-result-mobile"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </form>
		<style>
			#search-result-mobile-wrap tr td h5 a {
				color : #222;
			}
			.form-rounded input[type="text"] {
				border-bottom: 1px solid;
			}
		</style>
		<!-- /. Sidebar Search Form  -->
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
					&#169; 2015- <?= date('Y') ?> <a href="/portfolio/"><strong>Andriy Beznosko</strong></a>
				</p>
			</li>
			<li>
				<a href='http://citydomain.com.ua/?partner=user28504' title='Хостинг CityHost.ua' target='_blank'><img src='https://cityhost.ua/upload_img/ref_banners/cityhost_ua_240x400.jpg' title='Хостинг СитиХост' border='0' alt='Hosting CityHost'/></a>
			</li>
		</ul>
		<?php dynamic_sidebar('left-sidebar');?>
		<!-- /. Sidebar Menu -->
	</section>
	<!-- /. Sidebar Section -->
</aside>
