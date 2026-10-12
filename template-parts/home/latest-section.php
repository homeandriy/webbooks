<?php
/**
 * Latest content section for the homepage.
 *
 * @package Webbooks
 */

declare(strict_types=1);

$query         = $args['query'] ?? null;
$kind          = (string) ( $args['kind'] ?? 'articles' );
$title         = (string) ( $args['title'] ?? '' );
$archive_url   = (string) ( $args['archive_url'] ?? '' );
$archive_label = (string) ( $args['archive_label'] ?? '' );
$heading_tag   = ( $args['heading_tag'] ?? 'h2' ) === 'h1' ? 'h1' : 'h2';
$is_books      = $kind === 'books';

if ( ! $query instanceof WP_Query ) {
	return;
}
?>
<section class="home-latest-section home-latest-section--<?php echo esc_attr( $kind ); ?>" aria-labelledby="home-<?php echo esc_attr( $kind ); ?>-heading">
	<header class="home-latest-section__header">
		<div>
			<p class="home-latest-section__eyebrow"><i class="fa <?php echo esc_attr( $is_books ? 'fa-book' : 'fa-file-text-o' ); ?>" aria-hidden="true"></i> <?php echo esc_html( $is_books ? __( 'New in the catalogue', 'webbooks' ) : __( 'New publications', 'webbooks' ) ); ?></p>
			<?php if ( $heading_tag === 'h1' ) : ?>
				<h1 id="home-<?php echo esc_attr( $kind ); ?>-heading" class="home-latest-section__title"><?php echo esc_html( $title ); ?></h1>
			<?php else : ?>
				<h2 id="home-<?php echo esc_attr( $kind ); ?>-heading" class="home-latest-section__title"><?php echo esc_html( $title ); ?></h2>
			<?php endif; ?>
		</div>
		<?php if ( $archive_url !== '' && $archive_label !== '' ) : ?>
			<a class="home-latest-section__link" href="<?php echo esc_url( $archive_url ); ?>"><?php echo esc_html( $archive_label ); ?> <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
		<?php endif; ?>
	</header>

	<?php if ( $query->have_posts() ) : ?>
		<div class="row home-latest-section__grid">
			<?php while ( $query->have_posts() ) : $query->the_post(); ?>
				<?php
				if ( $is_books ) {
					get_template_part( 'template-parts/cards/book-card', null, array( 'book' => get_post() ) );
				} else {
					get_template_part( 'template-parts/cards/article-card' );
				}
				?>
			<?php endwhile; ?>
		</div>
	<?php else : ?>
		<?php
		get_template_part(
			'template-parts/content/empty-state',
			null,
			array(
				'kind'         => $kind,
				'title'        => $is_books ? __( 'No books have been added yet', 'webbooks' ) : __( 'No articles have been published yet', 'webbooks' ),
				'description'  => __( 'Choose another section or come back later for new publications.', 'webbooks' ),
				'action_url'   => $archive_url,
				'action_label' => $archive_label,
			)
		);
		?>
	<?php endif; ?>
</section>
<?php wp_reset_postdata(); ?>
