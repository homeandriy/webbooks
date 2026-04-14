<?php
/**
 * Шаблон обычной страницы (page.php)
 *
 * @package WordPress
 * @subpackage webbooks
 */

get_header();
get_sidebar();
?>
<aside class="right-section page-layout">
    <section class="content page-content">
        <div class="container-fluid page-container mrg-tb">
            <?php if (have_posts()) : ?>
                <?php while (have_posts()) : the_post(); ?>
                    <header class="page-hero white-bg">
                        <h1 class="page-hero__title"><?php the_title(); ?></h1>
                    </header>

                    <article <?php post_class('page-content-panel white-bg'); ?>>
                        <div class="entry-content page-entry-content">
                            <?php the_content(); ?>
                        </div>
                    </article>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </section>
</aside>
<?php get_footer();
