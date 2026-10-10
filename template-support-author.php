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
<aside class="right-section">
	<section class="content author-support-page">
		<div class="container-fluid">
			<article class="panel panel-default author-support-page__panel">
				<div class="panel-body">
					<h1><?php esc_html_e( 'Support the author', 'webbooks' ); ?></h1>
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
						<button type="button" class="btn btn-outline-secondary" data-webbooks-telegram>
							<i class="fa fa-telegram" aria-hidden="true"></i>
							<?php esc_html_e( 'Contact on Telegram', 'webbooks' ); ?>
						</button>
					</div>
				</div>
			</article>
		</div>
	</section>
</aside>
<?php get_footer(); ?>
