<?php
/**
 * Download links error template.
 *
 * @package Webbooks
 *
 * @var array<string, mixed> $args Template arguments.
 */

$message = (string) ( $args['message'] ?? '' );
?>
<div class="container-fluid mrg-tb">
	<div class="row">
		<div class="alert alert-danger" role="alert">
			<?php echo esc_html( $message ); ?>
		</div>
	</div>
</div>
