<?php
/**
 * Шаблон обычной страницы (page.php)
 * @package WordPress
 * @subpackage your-clean-template
 * Template Name: download-book
 */
get_header();

$post_id = isset( $_GET['count'] ) && is_numeric( $_GET['count'] ) ? $_GET['count'] : 0;
?>
<?php get_sidebar(); ?>
<aside class="right-section">
    <!-- Main content - Includes Featured Listings + Latest Listings -->
    <section class="content">
        <!-- Start Latest Listings Section -->
        <div class="container-fluid mrg-tb white-bg">
            <div class="row">
                <div class="col-md-12 section-title">
                    <h1 class="post-title entry-title">Скачивание <strong>"<?= get_the_title( $post_id ) ?>
                            "</strong>. Пожалуйста, ждите, скоро появится ссылка:</h1>
                </div>
                <div class="container-fluid">
                    <!-- seconds -->
                    <h2 class="post-title entry-title text-center text-uppercase" id="countdown">Осталось секунд:
                        <span></span></h2>
                    <h2>
                        <div id="countdown"></div>
                    </h2>
                    <div class="panel-body"></div>
                    <div class="text-center">
                        <a href='http://citydomain.com.ua/?partner=user28504' title='Хостинг CityHost.ua'
                           target='_blank'><img
                                    src='https://cityhost.ua/upload_img/ref_banners/cityhost_ua_970x120.jpg'
                                    title='Хостинг СитиХост' border='0' alt='Hosting CityHost'/></a>
                    </div>
                </div>
                <div>
                    <h2 id="link" class="text-center"></h2>
                </div>
                <div class="col-md-12 section-title ">
                    <div class="list-group">
                        <a href="<?= get_the_permalink( $post_id ); ?>" class="list-group-item active"
                           target="_blank">
                            <div class="row">
                                <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                                    <?php
                                    $url = wp_get_attachment_url( get_post_thumbnail_id( $post_id ) );
                                    ?>
                                    <img width="128" height="180" class="media-object" src="<?= $url; ?>"
                                         alt="...">
                                </div>

                                <div class="col-xs-12 col-sm-10 col-md-10 col-lg-10">
                                    <h4 class="list-group-item-heading"><?= get_the_title( $post_id ) ?></h4>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-md-12 section-title ">
                    <h3 class="post-title entry-title">Также вам должно понравится: (откроется в новой вкладке)</h3>
                    <?php
                        // Поулчить текущую категорию, для виборки
                        $send_category_id = $_GET['cat']??69;
                        $query_arguments  = [
                            'posts_per_page' => 5,
                            'category__in'   => $send_category_id,
                            'post_status'    => 'publish',
                            'orderby'        => 'rand',
                        ];
                    $related_books_for_downloads = new WP_Query( $query_arguments );
                    ?>

                    <?php if ( $related_books_for_downloads->have_posts() ) : ?>
                        <?php while ( $related_books_for_downloads->have_posts() ) : ?>
                            <?php $related_books_for_downloads->the_post();?>
                            <?php $url = wp_get_attachment_url( get_post_thumbnail_id( $post->ID ) ); ?>
                            <div class="list-group">
                                <a href="<?= get_the_permalink(); ?>" class="list-group-item active" target="_blank">
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                                            <img width="128" height="180" class="media-object" src="<?= $url; ?>" alt="<?php the_title(); ?>">
                                        </div>
                                        <div class="col-xs-12 col-sm-10 col-md-10 col-lg-10">
                                            <h4 class="list-group-item-heading"><?= get_the_title() ?></h4>
                                            <p class="list-group-item-text text-white">
		                                        <?= wp_trim_words( get_the_content(), 40, '... ' ) ?>
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        </div>
    </section>
    <!-- right col -->
</aside>


<?php get_footer();  ?>
