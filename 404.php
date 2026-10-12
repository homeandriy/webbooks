<?php
/** A focused recovery page for missing URLs. WordPress retains the HTTP 404 status. */
get_header();
?>
<main class="not-found" aria-labelledby="not-found-title">
	<div class="not-found__layout">
		<div class="not-found__art" aria-hidden="true">
			<img src="<?php echo esc_url( WEBBOOKS_URL . '/assets/images/404-books.svg' ); ?>" width="560" height="440" alt="" decoding="async">
		</div>
		<div class="not-found__copy">
			<p class="not-found__code">404</p>
			<h1 id="not-found-title"><?php esc_html_e( 'Nothing found', 'webbooks' ); ?></h1>
			<p class="not-found__description"><?php esc_html_e( 'This page may have moved, or the link may be incorrect. Your next good read is still waiting.', 'webbooks' ); ?></p>
			<a class="not-found__home" href="<?php echo esc_url( \Webbooks\Localization\Polylang::homeUrl() ); ?>">
				<?php esc_html_e( 'Start from the homepage', 'webbooks' ); ?>
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
			</a>
		</div>
	</div>
</main>
<?php get_footer(); ?>
