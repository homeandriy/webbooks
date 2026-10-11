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
				$book_sections = array(
					array(
						'slug'  => 'javascript',
						'class' => 'grey',
						'label' => __( 'Javascript', 'webbooks' ),
					),
					array(
						'slug'  => 'phpmysql',
						'class' => 'yellow-green',
						'label' => __( 'PHP & MySQL', 'webbooks' ),
					),
					array(
						'slug'  => 'htmlcss',
						'class' => 'yellow',
						'label' => __( 'HTML & CSS', 'webbooks' ),
					),
					array(
						'slug'  => 'seo',
						'class' => 'blue',
						'label' => __( 'SEO', 'webbooks' ),
					),
					array(
						'slug'  => 'laravel-book',
						'class' => 'grey',
						'label' => __( 'LARAVEL', 'webbooks' ),
					),
					array(
						'slug'  => 'knigi-po-wp',
						'class' => 'yellow',
						'label' => __( 'WORDPRESS', 'webbooks' ),
					),
					array(
						'slug'  => 'yii',
						'class' => 'grey',
						'label' => __( 'YII', 'webbooks' ),
					),
					array(
						'slug'  => 'dizajn',
						'class' => 'blue',
						'label' => __( 'Design', 'webbooks' ),
					),
					array(
						'slug'  => 'drugie',
						'class' => 'yellow-green',
						'label' => __( 'Other topics', 'webbooks' ),
					),
				);

				$current_lang = function_exists( 'pll_current_language' ) ? pll_current_language( 'slug' ) : '';

				$rendered_sections = 0;
				foreach ( $book_sections as $section ) {
					$term = get_category_by_slug( $section['slug'] );
					if ( ! $term ) {
						continue;
					}

					$term_id = $term->term_id;
					if ( function_exists( 'pll_get_term' ) && ! empty( $current_lang ) ) {
						$translated_term_id = pll_get_term( $term_id, $current_lang );
						if ( ! empty( $translated_term_id ) ) {
							$term_id = $translated_term_id;
						}
					}

					$term_link = get_term_link( (int) $term_id, 'category' );
					if ( is_wp_error( $term_link ) ) {
						continue;
					}
					++$rendered_sections;
					?>
					<a href="<?php echo esc_url( $term_link ); ?>" class="col-12 col-md-6 col-lg-4 link-block <?php echo esc_attr( $section['class'] ); ?>"><?php echo esc_html( $section['label'] ); ?></a>
					<?php
				}
				if ( $rendered_sections === 0 ) {
					foreach ( get_categories( array( 'child_of' => webbooks_category_root( 'books' ), 'hide_empty' => false ) ) as $category ) {
						$term_link = get_term_link( $category );
						if ( ! is_wp_error( $term_link ) ) {
							printf( '<a href="%s" class="link-block blue">%s</a>', esc_url( $term_link ), esc_html( $category->name ) );
						}
					}
				}
				?>
			</div>
		</div>
	</div>
	<div class="collapse" id="filter-search">
		<?php get_template_part( 'template-parts/forms/filter-search' ); ?>
	</div>
</div>
