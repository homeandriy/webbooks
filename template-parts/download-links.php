<?php
/** @var array $sources */
?>
<div class="container-fluid mrg-tb"><div class="row">
	<?php if ( ! $sources ) : ?>
		<div class="alert alert-warning" role="alert"><?php esc_html_e( 'Download links are unavailable.', 'webbooks' ); ?></div>
	<?php else : ?>
		<?php foreach ( $sources as $source ) : ?>
			<div class="col-md-6 col-lg-4"><div class="card theme-thumbnail"><div class="card-body">
				<h3><?php echo esc_html( $source['name'] ); ?></h3>
				<p><a href="<?php echo esc_url( $source['link'] ); ?>" class="btn btn-primary" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Download', 'webbooks' ); ?></a></p>
			</div></div></div>
		<?php endforeach; ?>
	<?php endif; ?>
</div></div>
