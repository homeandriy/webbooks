<?php
/**
 * Главная страница (index.php)
 * @package WordPress
 * @subpackage your-clean-template
 */
get_header(); // подключаем header.php ?>
<!-- <a href="#" id="<?php the_ID();?>" class="ajax-post"><?php the_title();?></a> -->


<?php get_sidebar();?>
<aside class="right-section">
	<!-- Main content - Includes Featured Listings + Latest Listings -->
	<section class="content" <?php if ( ! isset( $content_width ) ) $content_width = 900; ?> >
	
		<div class="container-fluid mrg-tb">
			<div class="row">
				<a class="btn btn-primary filter-seach" role="button" data-toggle="collapse" href="#filter-seach" aria-expanded="false" aria-controls="filter-seach">
				  <i class="fa fa-fw fa-search-plus"></i>Фильтр поиска книг
				</a>				
				<div class="collapse" id="filter-seach">
					<form class="form-horizontal" id="main-seach">
						<div class="row pd-15">						
							
							<div class="col-xs-12 col-sm-12">
								
								<label for="category-main">Раздел</label>
								    <?php
								    $select = wp_dropdown_categories('show_option_none=Раздел&orderby=slug&value_field=slug&echo=0&child_of=18');
								    $select = preg_replace("#<select([^>]*)>#", "<select class='form-control' id='category-main'   $1 >", $select);
								    echo $select;
								    ?>
								    <noscript><input  id='category-main' type="submit" value="View" /></noscript>
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
				<div class="col-md-12 section-title ">
					<h4>Последние книги<a class="btn btn-default btn-sm pull-right" href="<?php get_home_url();?>/allpost">Смотреть по категориям&raquo;</a></h4>
				</div>
				<div class="contetnt-loop">
								<?php if (have_posts()): while (have_posts()): the_post(); // если посты есть - запускаем цикл wp ?>
											
											<?php get_template_part('loop'); // для отображения каждой записи берем шаблон loop.php ?>
											
												<?php endwhile; // конец цикла
				else:echo '<h2>Нет записей.</h2>';endif; // если записей нет, напишим "простите" ?>

				</div>
			</div>
		</div>
		<!-- ./ Latest Listings Section -->




	</section>
	<!-- right col -->
</aside>



<!-- ./ Featured Listings Slider -->
<!-- Start Latest Listings Section -->





<?php get_footer(); // подключаем footer.php ?>


