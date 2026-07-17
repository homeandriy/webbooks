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
<div class="container-fluid mrg-tb"><div class="row">
<?php if ( empty( $links ) ) : ?>
	<div class="col-sm-12 col-md-12 col-lg-12"><div class="alert alert-danger" role="alert"><p><strong><?php esc_html_e( 'Error!', 'webbooks' ); ?></strong> <?php esc_html_e( 'Download links were not found.', 'webbooks' ); ?></p><p class="text-muted text-white"><?php esc_html_e( 'Contact', 'webbooks' ); ?> <a href="mailto:homeandriy@gmail.com" data-id="<?php echo esc_attr( (string) $book_id ); ?>">homeandriy@gmail.com</a></p></div></div>
<?php else : ?>
	<div class="col-sm-6 col-md-4 col-lg-4">
		<?php foreach ( $links as $link ) : ?>
			<div class="thumbnail"><img src="<?php echo esc_url( $link['img'] ?? '' ); ?>" alt="<?php echo esc_attr( $link['name'] ?? '' ); ?>" loading="lazy"><div class="caption"><h3><?php echo esc_html( $link['name'] ?? '' ); ?></h3><p><?php echo esc_html( $link['description'] ?? '' ); ?></p><p><a href="<?php echo esc_url( $link['link'] ?? '' ); ?>" class="btn btn-primary" role="button" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Download', 'webbooks' ); ?></a></p></div></div>
		<?php endforeach; ?>
	</div>
<?php endif; ?>
</div></div>
