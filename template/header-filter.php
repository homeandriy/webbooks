<div class="row">
    <a href="#get-books" class="col-sm-12 col-md-6 button-menu blue" role="button" data-toggle="collapse"
       aria-controls="get-books">
        <i class="fa fa-fw fa-arrow-circle-o-right" aria-hidden="true"></i><?php esc_html_e( 'Читать книги', 'webbooks' ); ?>
        <h5><?php esc_html_e( 'Каталог книг для программирования', 'webbooks' ); ?></h5>
    </a>
    <a href="#filter-search" class="col-sm-12 col-md-6  button-menu yellow" role="button" data-toggle="collapse"
       aria-expanded="false" aria-controls="filter-search">
        <i class="fa fa-fw fa-arrow-circle-o-down" aria-hidden="true"></i><?php esc_html_e( 'Искать книги', 'webbooks' ); ?>
        <h5><?php esc_html_e( 'Фильтр подбора книг для Вас', 'webbooks' ); ?></h5>
    </a>
    <div class="collapse" id="get-books">
        <div class="container-fluid">
            <div class="row">
	            <?php
	            $book_sections = [
		            [ 'slug' => 'javascript', 'class' => 'grey', 'label' => 'Javascript' ],
		            [ 'slug' => 'phpmysql', 'class' => 'yellow-green', 'label' => 'PHP & MySQL' ],
		            [ 'slug' => 'htmlcss', 'class' => 'yellow', 'label' => 'HTML & CSS' ],
		            [ 'slug' => 'seo', 'class' => 'blue', 'label' => 'SEO' ],
		            [ 'slug' => 'laravel-book', 'class' => 'grey', 'label' => 'LARAVEL' ],
		            [ 'slug' => 'knigi-po-wp', 'class' => 'yellow', 'label' => 'WORDPRESS' ],
		            [ 'slug' => 'yii', 'class' => 'grey', 'label' => 'YII' ],
		            [ 'slug' => 'dizajn', 'class' => 'blue', 'label' => 'Дизайн' ],
		            [ 'slug' => 'drugie', 'class' => 'yellow-green', 'label' => 'Другие тематики' ],
	            ];

	            $current_lang = function_exists( 'pll_current_language' ) ? pll_current_language( 'slug' ) : '';

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
		            ?>
                    <a href="<?php echo esc_url( $term_link ); ?>" class="col-xs-12 col-sm-6 col-md-4 link-block <?php echo esc_attr( $section['class'] ); ?>"><?php echo esc_html__( $section['label'], 'webbooks' ); ?></a>
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
