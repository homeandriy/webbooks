<?php
/** Classic catalog tag template. */
get_header();
get_sidebar();
?>
<aside class="right-section">
    <section class="content">
        <div class="container-fluid mrg-tb">
            <div class="row">
                <div class="col-12 section-title"><h1><?php single_tag_title(); ?></h1></div>
            </div>
            <div class="content-loop"><div class="row">
                <?php if ( have_posts() ) : ?>
                    <?php while ( have_posts() ) : the_post(); ?>
                        <?php get_template_part( 'template/loop' ); ?>
                    <?php endwhile; ?>
                <?php else : ?>
                    <h2><?php esc_html_e( 'This section is currently empty.', 'webbooks' ); ?></h2>
                <?php endif; ?>
            </div></div>
            <?php pagination(); ?>
        </div>
    </section>
</aside>
<?php get_footer(); ?>
