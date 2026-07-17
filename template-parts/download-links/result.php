<?php
/**
 * Download links result template.
 *
 * @package Webbooks
 *
 * @var array<string, mixed> $args
 */

$book_id = absint( $args['book_id'] ?? 0 );
$links   = is_array( $args['links'] ?? null ) ? $args['links'] : array();
?>
<div class="container-fluid mrg-tb">
	<div class="row g-4">
<?php if ( empty( $links ) ) : ?>
		<div class="col-12">
			<div class="alert alert-danger" role="alert">
				<p>
					<strong><?php esc_html_e( 'Error!', 'webbooks' ); ?></strong>
					<?php esc_html_e( 'Download links were not found.', 'webbooks' ); ?>
				</p>
				<p class="text-muted text-white">
					<?php esc_html_e( 'Contact', 'webbooks' ); ?>
					<a href="mailto:homeandriy@gmail.com" data-id="<?php echo esc_attr( (string) $book_id ); ?>">homeandriy@gmail.com</a>
				</p>
			</div>
		</div>
<?php else : ?>
		<?php foreach ( $links as $download_link ) : ?>
			<div class="col-12 col-md-6 col-lg-4">
				<div class="thumbnail h-100">
					<img src="<?php echo esc_url( $download_link['img'] ?? '' ); ?>" alt="<?php echo esc_attr( $download_link['name'] ?? '' ); ?>" loading="lazy">
					<div class="caption">
						<h3><?php echo esc_html( $download_link['name'] ?? '' ); ?></h3>
						<p><?php echo esc_html( $download_link['description'] ?? '' ); ?></p>
						<p>
							<a href="<?php echo esc_url( $download_link['link'] ?? '' ); ?>" class="btn btn-primary" role="button" target="_blank" rel="noopener noreferrer">
								<?php esc_html_e( 'Download', 'webbooks' ); ?>
							</a>
						</p>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
<?php endif; ?>
	</div>
</div>
