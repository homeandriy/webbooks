<div class="catalog-navigation">
	<div class="catalog-actions">
	<button type="button" class="col-md-12 col-lg-6 button-menu blue" data-bs-toggle="collapse" data-bs-target="#get-books"
		aria-controls="get-books" aria-expanded="false">
		<span class="button-menu__title">
			<i class="fa fa-fw fa-arrow-circle-o-right" aria-hidden="true"></i>
			<?php esc_html_e( 'Read books', 'webbooks' ); ?>
		</span>
		<h5 class="button-menu__subtitle"><?php esc_html_e( 'Programming books catalog', 'webbooks' ); ?></h5>
	</button>
	<button type="button" class="col-md-12 col-lg-6 button-menu yellow" data-bs-toggle="collapse" data-bs-target="#filter-search"
		aria-expanded="false" aria-controls="filter-search">
		<span class="button-menu__title">
			<i class="fa fa-fw fa-arrow-circle-o-down" aria-hidden="true"></i>
			<?php esc_html_e( 'Search books', 'webbooks' ); ?>
		</span>
		<h5 class="button-menu__subtitle"><?php esc_html_e( 'Book selection filter for you', 'webbooks' ); ?></h5>
	</button>
	</div>
	<div class="collapse" id="get-books">
		<div class="container-fluid">
			<div class="row">
				<?php
				$book_sections = get_categories(
					array(
						'child_of'     => webbooks_category_root( 'books' ),
						'orderby'      => 'count',
						'order'        => 'DESC',
						'hide_empty'   => false,
						'hierarchical' => true,
					)
				);
				$section_classes = array( 'grey', 'yellow-green', 'yellow', 'blue' );

				foreach ( $book_sections as $index => $section ) {
					$term_link = get_term_link( $section );
					if ( is_wp_error( $term_link ) ) {
						continue;
					}
					?>
					<a href="<?php echo esc_url( $term_link ); ?>" class="col-12 col-md-6 col-lg-4 link-block <?php echo esc_attr( $section_classes[ $index % count( $section_classes ) ] ); ?>"><?php echo esc_html( $section->name ); ?></a>
					<?php
				}
				?>
			</div>
		</div>
	</div>
	<div class="collapse" id="filter-search">
		<?php get_template_part( 'template-parts/forms/filter-search' ); ?>
	</div>
</div>
