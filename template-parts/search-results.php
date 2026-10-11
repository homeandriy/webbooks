<?php
$books = $args['books'] ?? array();
$articles = $args['articles'] ?? array();
$total = (int) ( $args['total'] ?? 0 );
?>
	<div class="search-results-total">
		<?php esc_html_e( 'Found results:', 'webbooks' ); ?> <?php echo (int) $total; ?>
	</div>
	<?php if ( ! empty( $books ) ) : ?>
		<div class="search-section-heading"><strong><?php esc_html_e( 'Books', 'webbooks' ); ?></strong></div>
		<?php foreach ( $books as $book_item ) : ?>
			<a class="search-result-item search-result-item--book" href="<?php echo esc_url( $book_item['permalink'] ); ?>">
				<span class="search-result-item__media"><img width="60" height="80" decoding="async" src="<?php echo esc_url( $book_item['thumbnail'] ?: webbooks_book_placeholder_url() ); ?>" alt="<?php echo esc_attr( $book_item['title'] ); ?>" loading="lazy"></span>
				<span class="search-result-item__content">
					<span class="search-result-item__title"><?php echo esc_html( $book_item['title'] ); ?></span>
					<span class="search-result-item__meta">
						<span><?php echo esc_html__( 'Author:', 'webbooks' ); ?> <?php echo esc_html( $book_item['author'] ); ?></span>
						<span><?php echo esc_html__( 'Publisher:', 'webbooks' ); ?> <?php echo esc_html( $book_item['publisher'] ); ?></span>
						<span><?php echo esc_html__( 'Language:', 'webbooks' ); ?> <?php echo esc_html( $book_item['language'] ); ?></span>
					</span>
				</span>
			</a>
		<?php endforeach; ?>
	<?php endif; ?>

	<?php if ( ! empty( $articles ) ) : ?>
		<div class="search-section-heading"><strong><?php esc_html_e( 'Blog articles', 'webbooks' ); ?></strong></div>
		<?php foreach ( $articles as $article_item ) : ?>
			<a class="search-result-item search-result-item--article" href="<?php echo esc_url( $article_item['permalink'] ); ?>">
				<span class="search-result-item__media"><img width="60" height="80" decoding="async" src="<?php echo esc_url( $article_item['thumbnail'] ?: webbooks_book_placeholder_url() ); ?>" alt="<?php echo esc_attr( $article_item['title'] ); ?>" loading="lazy"></span>
				<span class="search-result-item__content">
					<span class="search-result-item__title"><?php echo esc_html( $article_item['title'] ); ?></span>
				</span>
			</a>
		<?php endforeach; ?>
	<?php endif; ?>

	<?php if ( $total === 0 ) : ?>
		<div class="search-result-empty"><?php esc_html_e( 'Nothing found', 'webbooks' ); ?></div>
	<?php endif; ?>
