<?php
/**
 * Book card used by the catalog and AJAX filter results.
 *
 * @package Webbooks
 */

$select_to_link = (bool) ( ( $args ?? array() )['selectToLink'] ?? false );

$thumb_url = get_the_post_thumbnail_url( $post->ID, 'medium' );
if ( empty( $thumb_url ) ) {
	$thumb_url = get_template_directory_uri() . '/screenshot.png';
}
?>
<div class="col-12 col-md-6 content_block book-card-grid-item">
	<div class="list-group book-card">
		<div class="list-group-item book-card-item">
			<div class="row book-card-row">
				<div class="col-12 col-lg-6 book-card-image-col">
					<div class="book-card-image">
						<img width="390" height="440" class="media-object" src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" loading="lazy" decoding="async">
					</div>
				</div>
				<div class="col-12 col-lg-6 next-reed-column book-card-content-col">
					<h4 class="list-group-item-heading book-card-title"><a href="<?php echo esc_url( get_the_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a></h4>
					<div class="next-reed-content">
						<p class="list-group-item-text next-reed-description"><?php echo wp_kses_post( wp_trim_words( get_the_content(), 50, '...' ) ); ?></p>
						<table class="table book-card-meta"><tbody>
						<tr><td><?php esc_html_e( 'Author:', 'webbooks' ); ?></td><td><?php echo esc_html( get_post_meta( $post->ID, 'autor', true ) ); ?></td></tr>
						<tr><td><?php esc_html_e( 'Publication year:', 'webbooks' ); ?></td><td><?php echo esc_html( get_post_meta( $post->ID, 'year', true ) ); ?></td></tr>
						<tr><td><?php esc_html_e( 'Publisher:', 'webbooks' ); ?></td><td><?php echo esc_html( get_post_meta( $post->ID, 'create', true ) ); ?></td></tr>
						<tr><td><?php esc_html_e( 'Language:', 'webbooks' ); ?></td><td><?php echo esc_html( \Webbooks\Book\BookMeta::getLanguage( (string) get_field( 'language' ) ) ); ?></td></tr>
						<tr><td><?php esc_html_e( 'Status:', 'webbooks' ); ?></td><td><?php echo esc_html( \Webbooks\Book\BookMeta::getComplexity( trim( (string) get_field( 'complexity' ) ) ) ); ?></td></tr>
						<tr><td><?php esc_html_e( 'Format:', 'webbooks' ); ?></td><td><?php echo esc_html( get_post_meta( $post->ID, 'format', true ) ); ?></td></tr>
						</tbody></table>
					</div>
					<div class="next-reed book-card-footer">
						<p>
						<?php
						if ( $select_to_link ) :
							echo wp_kses_post( apply_filters( 'get_download_link', $post, 0 ) ); else :
								?>
							<a href="<?php echo esc_url( get_the_permalink() ); ?>" class="btn btn-info book-card-footer__link">
								<?php esc_html_e( 'More', 'webbooks' ); ?>
								<i class="fa fa-arrow-right" aria-hidden="true"></i>
							</a>
						<?php endif; ?>
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
