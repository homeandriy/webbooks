<?php
/**
 * Article template without book downloads or gallery.
 *
 * Template Name Posts: single-post(NOT BOOK)
 * Template Name: Article
 * Template Post Type: post
 *
 * @package Webbooks
 */

get_header();
get_sidebar();
?>
<aside class="right-section">
	<section class="content">
		<?php while ( have_posts() ) : ?>
			<?php
			the_post();
			$article_id         = get_the_ID();
			$article_categories = wp_get_post_categories( $article_id );
			$article_source_url = webbooks_article_source_url( $article_id );
			?>
			<header class="section bg-brown-lighten">
				<div class="container-fluid">
					<h1 class="post-title entry-title"><?php the_title(); ?></h1>
					<p class="article-published">
						<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
					</p>
				</div>
			</header>
			<div class="main-section article-main">
				<div class="container-fluid">
					<div class="row">
						<div class="col-12 book-reading-column">
							<article class="card wb-panel">
								<div class="card-body book-reading-content">
									<?php the_content(); ?>
									<?php if ( '' !== $article_source_url ) : ?>
										<?php get_template_part( 'template-parts/article/source', null, array( 'url' => $article_source_url ) ); ?>
									<?php endif; ?>
									<?php if ( function_exists( 'evc_buttons_code' ) ) : ?>
										<div class="article-share">
											<h2 class="h5"><?php esc_html_e( 'Did you like this article or book? Share it with your friends:', 'webbooks' ); ?></h2>
											<?php echo wp_kses_post( evc_buttons_code() ); ?>
										</div>
									<?php endif; ?>
								</div>
							</article>
							<section class="card wb-panel article-comments" aria-labelledby="article-comments-heading">
								<div class="card-header">
									<h2 id="article-comments-heading" class="h4 mb-0"><?php esc_html_e( 'Comments', 'webbooks' ); ?></h2>
								</div>
								<div class="card-body">
									<?php comments_template(); ?>
								</div>
							</section>
						</div>
						<?php
						$related_posts = webbooks_related_query(
							array(
								'posts_per_page' => 6,
								'category__in'   => $article_categories ?: array( webbooks_category_root( 'articles' ) ),
								'post__not_in'   => array( $article_id ),
							)
						);
						?>
						<?php if ( $related_posts->have_posts() ) : ?>
							<section class="col-12 article-related" aria-labelledby="article-related-heading">
								<h2 id="article-related-heading"><?php esc_html_e( 'See also:', 'webbooks' ); ?></h2>
								<div class="row g-4">
									<?php while ( $related_posts->have_posts() ) : ?>
										<?php
										$related_posts->the_post();
										get_template_part( 'template-parts/cards/article-card', null, array( 'context' => 'related' ) );
										?>
									<?php endwhile; ?>
								</div>
							</section>
						<?php endif; ?>
						<?php wp_reset_postdata(); ?>
					</div>
				</div>
			</div>
		<?php endwhile; ?>
	</section>
</aside>
<?php get_footer(); ?>
