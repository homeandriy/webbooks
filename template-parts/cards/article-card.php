<?php
/**
 * Article card used by archives and article search results.
 *
 * @package Webbooks
 */

declare(strict_types=1);

$article_post_id = get_the_ID();
$permalink       = get_permalink( $article_post_id );
$article_title   = get_the_title( $article_post_id );
$fallback_image  = get_template_directory_uri() . '/img/article-news-placeholder.png';
$has_thumbnail   = has_post_thumbnail( $article_post_id );
$article_excerpt = \Webbooks\Content\Excerpt::forPost( $article_post_id );
?>
<div class="col-12 col-md-6 col-xl-4 content_block article-card-grid-item">
	<article class="article-card">
		<a class="article-card__media" href="<?php echo esc_url( $permalink ); ?>" aria-label="<?php echo esc_attr( $article_title ); ?>">
			<?php if ( $has_thumbnail ) : ?>
				<?php
				echo wp_get_attachment_image(
					get_post_thumbnail_id( $article_post_id ),
					'large',
					false,
					array(
						'class'    => 'article-card__image',
						'alt'      => '',
						'loading'  => 'lazy',
						'decoding' => 'async',
					)
				);
				?>
			<?php else : ?>
				<img class="article-card__image" src="<?php echo esc_url( $fallback_image ); ?>" alt="" width="1672" height="941" loading="lazy" decoding="async">
			<?php endif; ?>
		</a>
		<div class="article-card__body">
			<h2 class="article-card__title">
				<a href="<?php echo esc_url( $permalink ); ?>">
					<?php echo esc_html( $article_title ); ?>
				</a>
			</h2>
			<?php if ( '' !== $article_excerpt ) : ?>
				<p class="article-card__excerpt">
					<?php echo esc_html( $article_excerpt ); ?>
				</p>
			<?php endif; ?>
			<a class="btn btn-info article-card__action" href="<?php echo esc_url( $permalink ); ?>">
				<?php esc_html_e( 'Read more', 'webbooks' ); ?>
				<i class="fa fa-arrow-right" aria-hidden="true"></i>
			</a>
		</div>
	</article>
</div>
