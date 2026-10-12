<?php
/**
 * AJAX global search results.
 *
 * @package WordPress
 * @subpackage webbooks
 *
 * @var array<string, mixed> $args Template data.
 */

$books    = is_array( $args['books'] ?? null ) ? $args['books'] : array();
$articles = is_array( $args['articles'] ?? null ) ? $args['articles'] : array();
$total    = (int) ( $args['total'] ?? 0 );
?>
<div class="search-results-total">
	<?php esc_html_e( 'Found results:', 'webbooks' ); ?> <?php echo (int) $total; ?>
</div>

<?php if ( ! empty( $books ) ) : ?>
	<div class="search-section-heading">
		<strong><?php esc_html_e( 'Books', 'webbooks' ); ?></strong>
	</div>
	<?php foreach ( $books as $book_item ) : ?>
		<a class="search-result-item search-result-item--book" href="<?php echo esc_url( $book_item['permalink'] ?? '' ); ?>">
			<span class="search-result-item__media">
				<img src="<?php echo esc_url( $book_item['thumbnail'] ?? '' ); ?>" alt="<?php echo esc_attr( $book_item['title'] ?? '' ); ?>" loading="lazy">
			</span>
			<span class="search-result-item__content">
				<span class="search-result-item__title">
					<?php echo esc_html( $book_item['title'] ?? '' ); ?>
				</span>
				<span class="search-result-item__meta">
					<span>
						<?php esc_html_e( 'Author:', 'webbooks' ); ?> <?php echo esc_html( $book_item['author'] ?? '' ); ?>
					</span>
					<span>
						<?php esc_html_e( 'Publisher:', 'webbooks' ); ?> <?php echo esc_html( $book_item['publisher'] ?? '' ); ?>
					</span>
					<span>
						<?php esc_html_e( 'Language:', 'webbooks' ); ?> <?php echo esc_html( $book_item['language'] ?? '' ); ?>
					</span>
				</span>
			</span>
		</a>
	<?php endforeach; ?>
<?php endif; ?>

<?php if ( ! empty( $articles ) ) : ?>
	<div class="search-section-heading">
		<strong><?php esc_html_e( 'Blog articles', 'webbooks' ); ?></strong>
	</div>
	<?php foreach ( $articles as $article_item ) : ?>
		<a class="search-result-item search-result-item--article" href="<?php echo esc_url( $article_item['permalink'] ?? '' ); ?>">
			<span class="search-result-item__media">
				<img src="<?php echo esc_url( $article_item['thumbnail'] ?? '' ); ?>" alt="<?php echo esc_attr( $article_item['title'] ?? '' ); ?>" loading="lazy">
			</span>
			<span class="search-result-item__content">
				<span class="search-result-item__title">
					<?php echo esc_html( $article_item['title'] ?? '' ); ?>
				</span>
			</span>
		</a>
	<?php endforeach; ?>
<?php endif; ?>

<?php if ( 0 === $total ) : ?>
	<div class="search-result-empty">
		<?php esc_html_e( 'Nothing found', 'webbooks' ); ?>
	</div>
<?php endif; ?>
