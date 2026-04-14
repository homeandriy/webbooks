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
        <form class="form-horizontal" id="main-search">
            <div class="row pd-15">
                <div class="col-xs-12 col-sm-12 mrg-t">
                    <label for="category-main"><?php esc_html_e( 'Раздел', 'webbooks' ); ?></label>
	                <?php
	                $select = wp_dropdown_categories( 'show_option_none=' . esc_html__( 'Раздел', 'webbooks' ) . '&orderby=slug&value_field=slug&echo=0&child_of=18' );
	                $select = preg_replace( "#<select([^>]*)>#",
		                "<select class='form-control' id='category-main' $1 >", $select );
	                echo wp_kses_post( $select );
	                ?>
                    </select>
                    <input id='category-main' type="submit" class="btn btn-primary" value="<?php echo esc_attr__( 'Перейти в раздел', 'webbooks' ); ?>">
                </div>
                <div class="col-xs-12 col-sm-12">
                    <label for="status-book"><?php esc_html_e( 'Выберите скилл', 'webbooks' ); ?></label>
                    <select id="status-book" class="form-control choose-complexity" disabled="disabled">
                        <option><?php esc_html_e( 'Выбрать', 'webbooks' ); ?></option>
                        <?php foreach ( \Domain\Book\Complexity::cases() as $complexity ) : ?>
                            <option value="<?php echo esc_attr( $complexity->value ); ?>"><?php echo esc_html( $complexity->label() ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-xs-12 col-sm-12">
                    <label for="language"><?php esc_html_e( 'Язык', 'webbooks' ); ?></label>
                    <select id="language" class="form-control choose-complexity" disabled="disabled">
                        <option><?php esc_html_e( 'Выбрать', 'webbooks' ); ?></option>
                        <?php foreach ( \Domain\Book\Language::cases() as $language ) : ?>
                            <option value="<?php echo esc_attr( $language->value ); ?>"><?php echo esc_html( $language->label() ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-xs-12 col-sm-12 mrg-b mrg-t">
                    <button type="submit" id="send-data-button" class="btn btn-primary" disabled="disabled"><?php esc_html_e( 'Поиск', 'webbooks' ); ?></button>
                </div>
            </div>
        </form>
    </div>
</div>
