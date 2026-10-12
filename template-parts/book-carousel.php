<?php
/** Shared deterministic post carousel for categories and archives. */
$query = webbooks_related_query( array( 'posts_per_page' => WEBBOOKS_RELATED_MAXIMUM_POSTS ) );
if ( ! $query->have_posts() ) { return; }
?>
<div class="book-carousel" data-book-carousel role="region" aria-label="<?php esc_attr_e( 'Latest books', 'webbooks' ); ?>">
  <div class="swiper">
    <div class="swiper-wrapper">
      <?php while ( $query->have_posts() ) : $query->the_post(); ?>
        <div class="swiper-slide">
          <a class="book-carousel-card" href="<?php the_permalink(); ?>">
            <?php if ( has_post_thumbnail() ) : ?>
              <?php the_post_thumbnail( 'medium', array( 'loading' => 'lazy' ) ); ?>
            <?php else : ?>
              <img src="<?php echo esc_url( webbooks_book_placeholder_url() ); ?>" alt="" loading="lazy" width="240" height="320">
            <?php endif; ?>
            <h4><?php the_title(); ?></h4>
          </a>
        </div>
      <?php endwhile; wp_reset_postdata(); ?>
    </div>
  </div>
  <div class="book-carousel-controls">
    <button type="button" data-carousel-prev aria-label="<?php esc_attr_e( 'Previous', 'webbooks' ); ?>">&larr;</button>
    <button type="button" data-carousel-pause data-pause="<?php esc_attr_e( 'Pause slideshow', 'webbooks' ); ?>" data-play="<?php esc_attr_e( 'Play slideshow', 'webbooks' ); ?>"><?php esc_html_e( 'Pause slideshow', 'webbooks' ); ?></button>
    <button type="button" data-carousel-next aria-label="<?php esc_attr_e( 'Next', 'webbooks' ); ?>">&rarr;</button>
  </div>
</div>
