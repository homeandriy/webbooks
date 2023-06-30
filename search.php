<?php
/**
 * Шаблон поиска (search.php)
 * @package WordPress
 * @subpackage your-clean-template
 */
get_header();  ?> 
<?php get_sidebar();?>
<aside class="right-section">
	<!-- Main content - Includes Featured Listings + Latest Listings -->
	<section class="content">

		<!-- Start Latest Listings Section -->
		<div class="container-fluid mrg-tb">
			<div class="row">
				<div class="col-md-12 section-title ">
					<h4><?php printf('Поиск по строке: %s', get_search_query());?> <a class="btn btn-default btn-sm pull-right" href="">See All Listings &raquo;</a></h4>
				</div>
			<div class="content-loop">
							<?php if (have_posts()): while (have_posts()): the_post(); // если посты есть - запускаем цикл wp ?>
									
										<?php get_template_part('template/loop'); // для отображения каждой записи берем шаблон loop.php ?>
										
											<?php endwhile; // конец цикла
			else:echo '<h2>Нет записей.</h2>';endif; // если записей нет, напишим "простите" ?>
				

			</div>

			</section>
			<?php pagination(); // пагинация, функция нах-ся в function.php ?>
			<!-- right col -->
		</aside>
	<?php get_footer();  ?>
