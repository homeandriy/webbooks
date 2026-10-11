<?php
$select_to_link = (bool) ( ( $args ?? array() )['selectToLink'] ?? false );

$book = $args['book'] ?? get_post();
if ( ! $book instanceof WP_Post ) {
	return;
}
$book_meta = \Webbooks\Book\BookMeta::getNormalizedMeta( (int) $book->ID );
$thumb_id = get_post_thumbnail_id( $book );
?>
<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 content_block book-card-grid-item">
	<div class="list-group book-card" itemscope itemtype="https://schema.org/Book">
		<div class="list-group-item book-card-item"><div class="row book-card-row">
			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 book-card-image-col">
				<a class="book-card-image" href="<?php echo esc_url( get_permalink( $book ) ); ?>">
					<?php if ( $thumb_id ) : ?>
						<?php echo wp_get_attachment_image( $thumb_id, 'medium', false, array( 'class' => 'media-object', 'itemprop' => 'image', 'alt' => $book_meta['title'] ) ); ?>
					<?php else : ?>
						<img width="300" height="400" class="media-object" src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/book-placeholder.svg' ); ?>" alt="<?php echo esc_attr( $book_meta['title'] ); ?>" loading="lazy" decoding="async" itemprop="image">
					<?php endif; ?>
				</a>
			</div>
			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 next-reed-column book-card-content-col">
				<h4 class="list-group-item-heading book-card-title"><a href="<?php echo esc_url( get_permalink( $book ) ); ?>" itemprop="name"><?php echo esc_html( $book_meta['title'] ); ?></a></h4>
				<div class="next-reed-content">
					<table class="table book-card-meta"><tbody>
					<tr><td><?php esc_html_e( 'Author:', 'webbooks' ); ?></td><td itemprop="author"><?php echo esc_html( $book_meta['author'] ); ?></td></tr>
					<tr><td><?php esc_html_e( 'Language:', 'webbooks' ); ?></td><td itemprop="inLanguage"><?php echo esc_html( $book_meta['language'] ); ?></td></tr>
					<?php if ( $book_meta['format'] !== '' ) : ?>
					<tr><td><?php esc_html_e( 'Format:', 'webbooks' ); ?></td><td><?php echo esc_html( $book_meta['format'] ); ?></td></tr>
					<?php endif; ?>
					</tbody></table>
				</div>
				<div class="next-reed book-card-footer"><p><?php if ( $select_to_link ) : echo apply_filters( 'get_download_link', $book, 0 ); else : ?><a href="<?php echo esc_url( get_permalink( $book ) ); ?>" class="btn navbar-btn btn-info"><?php esc_html_e( 'More', 'webbooks' ); ?></a><?php endif; ?></p></div>
			</div>
		</div></div>
	</div>
</div>
