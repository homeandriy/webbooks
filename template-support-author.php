<?php
/**
 * Template Name: Support the author
 *
 * @package Webbooks
 */

get_header();
get_sidebar();

$author_name = \Webbooks\Support\AuthorSupport::authorName();
?>
<aside class="right-section author-support-layout">
	<section class="content author-support-page">
		<div class="container-fluid">
			<article class="card author-support-page__panel">
				<div class="card-body">
					<div class="author-support-page__layout">
						<figure class="author-support-page__media" aria-hidden="true">
							<img
								src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/author-support-hero.png' ); ?>"
								width="1024"
								height="1536"
								alt=""
								fetchpriority="high"
							>
						</figure>
						<div class="author-support-page__content">
							<header class="author-support-page__hero">
								<span class="author-support-page__icon" aria-hidden="true">
									<i class="fa fa-heart"></i>
								</span>
								<h1><?php esc_html_e( 'Support the author', 'webbooks' ); ?></h1>
							</header>
							<p class="author-support-page__lead">
								<?php esc_html_e( 'Your support helps keep WebBooks available and improve it further.', 'webbooks' ); ?>
							</p>
							<div class="author-support-page__actions">
								<button
									type="button"
									class="btn btn-info author-support-page__button"
									data-webbooks-support-cards
									data-bs-toggle="modal"
									data-bs-target="#webbooks-author-support-modal"
								>
									<i class="fa fa-heart" aria-hidden="true"></i>
									<?php
									printf(
										/* translators: %s: Author name. */
										esc_html__( 'Support %s', 'webbooks' ),
										esc_html( $author_name )
									);
									?>
								</button>
								<p class="author-support-page__motivation" data-webbooks-motivation aria-hidden="true"></p>
								<p class="visually-hidden" data-webbooks-motivation-announcer aria-live="polite"></p>
								<button type="button" class="btn btn-outline-secondary" data-webbooks-telegram>
									<i class="fa fa-telegram" aria-hidden="true"></i>
									<?php esc_html_e( 'Contact on Telegram', 'webbooks' ); ?>
								</button>
							</div>
						</div>
					</div>
				</div>
			</article>
		</div>
	</section>
</aside>
<?php get_footer(); ?>
