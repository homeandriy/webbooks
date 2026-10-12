<?php
/**
 * Helpful empty state for a content collection.
 *
 * @package Webbooks
 */

declare(strict_types=1);

$kind         = (string) ( $args['kind'] ?? 'content' );
$title        = (string) ( $args['title'] ?? __( 'Nothing here yet', 'webbooks' ) );
$description  = (string) ( $args['description'] ?? __( 'Choose another section or come back later for new publications.', 'webbooks' ) );
$action_url   = (string) ( $args['action_url'] ?? '' );
$action_label = (string) ( $args['action_label'] ?? '' );
$icon_class   = $kind === 'books' ? 'fa-book' : ( $kind === 'articles' ? 'fa-file-text-o' : 'fa-folder-open-o' );
?>
<div class="webbooks-empty-state" role="status">
	<div class="webbooks-empty-state__icon" aria-hidden="true"><i class="fa <?php echo esc_attr( $icon_class ); ?>"></i></div>
	<div class="webbooks-empty-state__content">
		<h2 class="webbooks-empty-state__title"><?php echo esc_html( $title ); ?></h2>
		<p class="webbooks-empty-state__description"><?php echo esc_html( $description ); ?></p>
		<?php if ( $action_url !== '' && $action_label !== '' ) : ?>
			<a class="btn btn-outline-primary webbooks-empty-state__action" href="<?php echo esc_url( $action_url ); ?>">
				<?php echo esc_html( $action_label ); ?>
				<i class="fa fa-arrow-right" aria-hidden="true"></i>
			</a>
		<?php endif; ?>
	</div>
</div>
