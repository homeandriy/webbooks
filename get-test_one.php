<?php
/**
 * Шаблон обычной страницы (page.php)
 * @package WordPress
 * @subpackage webbooks
 * Template Name: Test-page
 */
get_header();

?>
<!-- <a href="#" id="<?php the_ID();?>" class="ajax-post"><?php the_title();?></a> -->


<?php get_sidebar();?>
<aside class="right-section">
	<!-- Main content - Includes Featured Listings + Latest Listings -->
	<section class="content" <?php if ( ! isset( $content_width ) ) $content_width = 900; ?> >
	
		<div class="container-fluid mrg-tb">
			<div class="row">
				<a href="#get-books" class="col-sm-12 col-md-4 button-menu blue" role="button" data-toggle="collapse" aria-controls="get-books">
				<i class="fa fa-fw fa-arrow-circle-o-right" aria-hidden="true"></i>Читать книги
				<h5>Каталог книг для программирования</h5>
				</a>
				<a href="#filter-search" class="col-sm-12 col-md-4  button-menu yellow" role="button" data-toggle="collapse" href="#filter-search" aria-expanded="false" aria-controls="filter-search">
					<i class="fa fa-fw fa-arrow-circle-o-down" aria-hidden="true"></i>Искать книги
					<h5>Фильтр подбора книг для Вас</h5>
				</a>
				<a href="/post-main/" class="col-sm-12 col-md-4 button-menu grey">
				<i class="fa fa-fw fa-graduation-cap" aria-hidden="true"></i>Полезные статьи
				<h5>Читайте материал на разные темы</h5>
				</a>
								
					<div class="collapse" id="filter-search">
						<form class="form-horizontal" id="main-search">
							<div class="row pd-15">	
								<div class="col-xs-12 col-sm-12">								
									<label for="category-main">Раздел</label>
									    <?php
									    $select = wp_dropdown_categories('show_option_none=Раздел&orderby=slug&value_field=slug&echo=0&child_of=18');
									    $select = preg_replace("#<select([^>]*)>#", "<select class='form-control' id='category-main'   $1 >", $select);
									    echo $select;
									    ?>
									    <input  id='category-main' type="submit" value="View" />
									    </select>							 
								</div>
								<div class="col-xs-12 col-sm-12">
									<label for="status-book">Выберите раздел</label>
									<select id="status-book" class="form-control choose-complexity" disabled="disabled">
									  <option>Выберите.....</option>
									  <option value="beginner">Новичок</option>
									  <option value="professional">Профессионал</option>								  
									</select>
								</div>
								
								<div class="col-xs-12 col-sm-12">
									<label for="language">Язык</label>
									<select id="language" class="form-control choose-complexity" disabled="disabled" >
									  <option>Выберите.....</option>
									  <option value="ru">Русский</option>
									  <option value="en">Английский</option>								  								  
									</select>
								</div>							
								<div class="col-xs-12 col-sm-12">
									<label class="checkbox-inline">
									  <input type="checkbox" id="send-links" value="option1">Загрузить книги с ссылками на скачивание*
									</label>
								</div>
								<div class="col-xs-12 col-sm-12">
									<button type="submit" id="send-data-button" class="btn btn-primary" disabled="disabled">Мне повезет!</button>
								</div>
								<div class="col-xs-12 col-sm-12">
								*-Ссылка будет доступна после перехода по ней через 10сек.
								</div>
							</div>
						</form>
					</div>
					<div class="collapse" id="get-books">
						<div class="container-fluid">
							<div class="row">
								<a href ="/books-main/javascript/" class="col-xs-12 col-sm-6 col-md-4 link-block grey">Javascript</a>
								<a href ="/books-main/phpmysql/" class="col-xs-12 col-sm-6 col-md-4 link-block yellow-green">php & MySql</a>
								<a href ="/books-main/htmlcss/" class="col-xs-12 col-sm-6 col-md-4 link-block yellow">HTML & CSS</a>
								<a href ="/books-main/seo/" class="col-xs-12 col-sm-6 col-md-4 link-block blue">SEO</a>
								<a href ="/books-main/laravel-book/" class="col-xs-12 col-sm-6 col-md-4 link-block grey">LARAVEL</a>
								<a href ="/books-main/knigi-po-wp/" class="col-xs-12 col-sm-6 col-md-4 link-block yellow">WORDPRESS</a>
								<a href ="/books-main/yii/" class="col-xs-12 col-sm-6 col-md-4 link-block grey">YII</a>
								<a href ="/books-main/dizajn/" class="col-xs-12 col-sm-6 col-md-4 link-block blue">Дизайн</a>
								<a href ="/books-main/drugie/" class="col-xs-12 col-sm-6 col-md-4 link-block yellow-green">Другие тематики</a>
							</div>
						</div>									
					</div>
					<div class="col-md-12 section-title ">
						<h4>Последние книги<a class="btn btn-default btn-sm pull-right" href="<?php get_home_url();?>/allpost">Смотреть по категориям&raquo;</a></h4>
					</div>
					<div class="content-loop">
							

					</div>
				</div>
			</div>
		<!-- ./ Latest Listings Section -->




	</section>
	<!-- right col -->
</aside>



<!-- ./ Featured Listings Slider -->
<!-- Start Latest Listings Section -->



	<?php
get_footer();
 ?>
