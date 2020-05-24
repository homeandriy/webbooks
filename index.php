<?php
/**
 * Главная страница (index.php)
 * @package WordPress
 * @subpackage your-clean-template
 */
$post_on_page = get_option('posts_per_page');
$count_posts = wp_count_posts();
$max_page = ceil($count_posts->publish / $post_on_page);
get_header(); ?>

<?php get_sidebar();?>
<aside class="right-section">
	<!-- Main content - Includes Featured Listings + Latest Listings -->
	<section class="content">
		<div class="container-fluid mrg-tb">
			<div class="row">
				<div class="col-md-12 section-title">
                    <?php get_template_part('header-filter') // подключаем  три кнопки сверху?>
                </div>
				<div class="content-loop">
					<div class="row">
						<?php if (have_posts()): while (have_posts()): the_post(); // если посты есть - запускаем цикл wp ?>
							<?php get_template_part('loop'); // для отображения каждой записи берем шаблон loop.php ?>
							<?php endwhile; // конец цикла
						else:echo '<h2>Нет записей.</h2>';endif; // если записей нет, напишим "простите" ?>
						<div class="load-more">
						</div>
					</div>
				</div>
			</div>
			<button id="load-more" class="btn btn-success text-center" data-page="1" data-product_on_page="<?php echo $post_on_page;?>" data-max_page="<?php echo $max_page; ?>" 
					style="width: 100%; height: 75px; font-size: 40px;">
                Еще книги
            </button>
		</div>
		<!-- ./ Latest Listings Section -->
	</section>
	<!-- right col -->
</aside>
<!-- ./ Featured Listings Slider -->
<!-- Start Latest Listings Section -->
<?php get_footer(); // подключаем footer.php ?>


