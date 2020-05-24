<?php
/**
 * Шаблон сайдбара (sidebar.php)
 * @package WordPress
 * @subpackage your-clean-template
 */
?>
<aside class="left-section sidebar-offcanvas">
	<section class="sidebar">
		<!-- Start Sidebar Search Form - Only visible on Small & Extra Small Devices -->
        <form action="#" method="get" class="user-panel form-rounded hidden-lg hidden-md">
            <div class="input-group">
                <input type="text" class="form-control trans input-lg main-seach" data-idres="seach-result-mobile"
                       data-toggle="dropdown" onKeyup="searchGlobal(event)" placeholder="Поищем...">

                <div class="dropdown-menu mCustomScrollbar custom-seach" data-mcs-theme="dark"
                     id="seach-result-mobile-wrap">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <tbody id="seach-result-mobile"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </form>
		<style>
			#seach-result-mobile-wrap tr td h5 a {
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
                    // 'taxonomy' => 'category',
                ]);
            ?>
            <li class="cat-item"><a href="http://twodayweb.ru/" target="_blank">Еще..</a></li>
			<li>
				<p class="copyright">
					&#169; 2015- <?php echo date('Y') ?> <a href="/portfolio/"><strong>Andrii Beznosko</strong></a>
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
	<!--LiveInternet counter--><script type="text/javascript"><!--
	document.write("<a href='//www.liveinternet.ru/click' "+
	"target=_blank><img src='//counter.yadro.ru/hit?t14.13;r"+
	escape(document.referrer)+((typeof(screen)=="undefined")?"":
	";s"+screen.width+"*"+screen.height+"*"+(screen.colorDepth?
	screen.colorDepth:screen.pixelDepth))+";u"+escape(document.URL)+
	";h"+escape(document.title.substring(0,80))+";"+Math.random()+
	"' alt='' title='LiveInternet: показано число просмотров за 24"+
	" часа, посетителей за 24 часа и за сегодня' "+
	"border='0' width='88' height='31'><\/a>")
	//--></script><!--/LiveInternet-->
	<div id='Rambler-counter'>
	<!-- Внимание! В этом div'е не нельзя размещать пользовательский контент: он будет затерт! -->
	<noscript>
	<a href="http://top100.rambler.ru/navi/4384792/">
	  <img src="http://counter.rambler.ru/top100.cnt?4384792" alt="Rambler's Top100" border="0" />
	</a>
	</noscript>
	</div>

	<!-- Код скрипта должен быть размещен строго ниже контейнера для логотипа (div c id='Rambler-counter') -->
	<script type="text/javascript">
	var _top100q = _top100q || [];
	_top100q.push(['setAccount', '4384792']);
	_top100q.push(['trackPageviewByLogo', document.getElementById('Rambler-counter')]);

	(function(){
	  var pa = document.createElement("script"); 
	  pa.type = "text/javascript"; 
	  pa.async = true;
	  pa.src = ("https:" == document.location.protocol ? "https:" : "http:") + "//st.top100.ru/top100/top100.js";
	  var s = document.getElementsByTagName("script")[0]; 
	  s.parentNode.insertBefore(pa, s);
	})();
	</script>
</aside>
