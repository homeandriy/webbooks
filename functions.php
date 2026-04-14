<?php

/**
 * Constants
 */
const WEBBOOKS_VERSION = '1.5.1';
const DOWNLOAD_BOOK_NONCE = 'download_book_nonce';
const GENERAL_NONCE = 'myajax-nonce';
define('WEBBOOKS_PATH', get_stylesheet_directory());
define('WEBBOOKS_URL', get_stylesheet_directory_uri());

/**
 * Include files
 */
require_once WEBBOOKS_PATH . '/options_page.php';
require_once WEBBOOKS_PATH . '/Classes/class-book.php';
require_once WEBBOOKS_PATH . '/Classes/class-search.php';
require_once WEBBOOKS_PATH . '/Classes/class-clean_comments_constructor.php';
require_once WEBBOOKS_PATH . '/Classes/class-disable-api-users.php';

/**
 * Class instances
 */
new Class_disable_api_users();

/**
 * More functions
 */

add_action( 'wp_footer', 'add_scripts' );
if ( ! function_exists( 'add_scripts' ) ) {
	function add_scripts() {
		if ( is_admin() ) {
			return;
		}

		if ( ! is_page( 846 ) ) {
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'bootstrapjs_4', get_template_directory_uri() . '/assets/js/jquery.mCustomScrollbar.concat.min.js', '', WEBBOOKS_VERSION, true );
			wp_enqueue_script( 'jquery_lazy_load', get_stylesheet_directory_uri() . '/assets/js/jquery.lazyload.min.js', array( 'jquery' ), WEBBOOKS_VERSION, true );
			wp_enqueue_script( 'bootstrapjs', get_template_directory_uri() . '/assets/js/bootstrap.min.js', '', WEBBOOKS_VERSION, true );
			wp_enqueue_script( 'bootstrapjs_2', get_template_directory_uri() . '/assets/js/jquery.fs.roller.min.js', '', WEBBOOKS_VERSION, true );
			wp_enqueue_script( 'countdown360', get_template_directory_uri() . '/assets/js/jquery.elevatezoom.js', '', WEBBOOKS_VERSION, true );
			wp_enqueue_script( 'bootstrapjs_3', get_template_directory_uri() . '/assets/js/custom.js', '', WEBBOOKS_VERSION, true );
			wp_enqueue_script( 'bootstrapjs_1', get_template_directory_uri() . '/assets/js/theme.js', '', WEBBOOKS_VERSION, true );
			wp_enqueue_script( 'fancybox', get_template_directory_uri() . '/assets/js/jquery.fancybox.js', '', WEBBOOKS_VERSION, true );
			wp_enqueue_script( 'slick', get_template_directory_uri() . '/assets/js/slick.js', '', WEBBOOKS_VERSION, true );

			wp_enqueue_script( 'load-filter' );
			wp_enqueue_script( 'ajax-filter' );
		} else {
			wp_enqueue_script( 'bootstrapjs-14', get_template_directory_uri() . '/portfolio/assets/js/ie/respond.min.js', '', WEBBOOKS_VERSION, true );
			wp_enqueue_script( 'bootstrapjs-13', get_template_directory_uri() . '/portfolio/assets/js/util.js', '', WEBBOOKS_VERSION, true );
			wp_enqueue_script( 'bootstrapjs-12', get_template_directory_uri() . '/portfolio/assets/js/skel.min.js', '', WEBBOOKS_VERSION, true );
			wp_enqueue_script( 'bootstrapjs-11', get_template_directory_uri() . '/portfolio/assets/js/jquery.scrollzer.min.js', '', WEBBOOKS_VERSION, true );
			wp_enqueue_script( 'bootstrapjs-10', get_template_directory_uri() . '/portfolio/assets/js/jquery.scrolly.min.js', '', WEBBOOKS_VERSION, true );
			wp_enqueue_script( 'bootstrapjs-15', get_template_directory_uri() . '/portfolio/assets/js/main.js', '', WEBBOOKS_VERSION,
				true );
		}
	}
}

add_action( 'wp_print_styles', 'add_styles' );
if ( ! function_exists( 'add_styles' ) ) {
	function add_styles() {
		if ( is_admin() ) {
			return;
		}
		if ( ! is_page( 846 ) ) {
			wp_enqueue_style( 'sparkling-bootstrap1', get_template_directory_uri() . '/style.css' );
			wp_enqueue_style( 'sparkling-bootstrap2',
				get_template_directory_uri() . '/assets/css/material-design-icons.min.css' );
			wp_enqueue_style( 'sparkling-bootstrap3',
				get_template_directory_uri() . '/assets/css/jquery.fs.roller.min.css' );
			wp_enqueue_style( 'sparkling-bootstrap4',
				get_template_directory_uri() . '/assets/css/jquery.mCustomScrollbar.min.css' );
			wp_enqueue_style( 'sparkling-bootstrap5',
				get_template_directory_uri() . '/assets/css/font-awesome.min.css' );
			wp_enqueue_style( 'fancybox', get_template_directory_uri() . '/assets/css/jquery.fancybox.css' );
			wp_enqueue_style( 'slick-theme', get_template_directory_uri() . '/assets/css/slick-theme.css' );
			wp_enqueue_style( 'slick', get_template_directory_uri() . '/assets/css/slick.css' );

			// wp_enqueue_style('sparkling-bootstrap5', get_template_directory_uri() . '/assets/css/materialize.min.css');
			// wp_enqueue_style('sparkling-bootstrap6', get_template_directory_uri() . '/assets/css/bootstrap.css');
		} else {
			wp_enqueue_style( 'sparkling-bootstrap25252',
				get_template_directory_uri() . '/portfolio/assets/css/main.css' );
		}
	}
}

/** Register Scripts. */
add_action( 'wp_enqueue_scripts', 'theme_register_scripts', 1 );

function theme_register_scripts() {
	wp_register_script( 'functions-js', esc_url( trailingslashit( get_template_directory_uri() ) . '/assets/js/functions.js' ), ['jquery'], WEBBOOKS_VERSION, true );
	$front_params = array(
		'admin_ajax' => admin_url( 'admin-ajax.php' ),
		'nonce'      => wp_create_nonce( GENERAL_NONCE )
	);
	wp_localize_script( 'functions-js', 'php_array', $front_params );
}

add_action( 'wp_enqueue_scripts', 'additional_theme_scripts', 1 );
function additional_theme_scripts() {
    wp_register_script( 'ajax-filter', esc_url( trailingslashit( get_template_directory_uri() ) . '/assets/js/ajax-filter.js' ), ['jquery'] , WEBBOOKS_VERSION, true );
    $js_attributes = [
        'admin_ajax' => admin_url( 'admin-ajax.php' ),
        'nonce'      => wp_create_nonce( GENERAL_NONCE ),
        'download_nonce' => wp_create_nonce( DOWNLOAD_BOOK_NONCE ),
        'home_url'   => home_url()
    ];
    wp_localize_script( 'ajax-filter', 'js_attributes', $js_attributes );
}

add_action( 'wp_enqueue_scripts', 'theme_enqueue_scripts' );
function theme_enqueue_scripts() {
	wp_enqueue_script( 'functions-js' );
}

register_nav_menus(
    [
        'top'    => 'Верхнее',
        'bottom' => 'Внизу',
    ]
);


add_theme_support( 'post-thumbnails' );
add_theme_support( 'title-tag' );
set_post_thumbnail_size( 250, 150 );
add_image_size( 'big-thumb', 390, 440, false );
add_image_size( 'big-thumb-main', 390, 440, false );
add_image_size( 'small-thumb', 100, 100, true );

register_sidebar(
	[
		'name'          => 'Колонка слева',
		'id'            => "left-sidebar",
		'description'   => 'Обычная колонка в сайдбаре',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => "</div>\n",
		'before_title'  => '<span class="widgettitle">',
		'after_title'   => "</span>\n",
	]
);

function pagination() {
	global $wp_query;
	$big = 999999999;
	echo paginate_links(
		[
			'base'               => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
			'format'             => '?paged=%#%',
			'current'            => max( 1, get_query_var( 'paged' ) ),
			'type'               => 'list',
			'prev_text'          => 'Назад',
			'next_text'          => 'Вперед',
			'total'              => $wp_query->max_num_pages,
			'show_all'           => false,
			'end_size'           => 15,
			'mid_size'           => 15,
			'add_args'           => false,
			'add_fragment'       => '',
			'before_page_number' => '',
			'after_page_number'  => '',
		]
	);
}

/** Ajax Post */
add_action( 'wp_ajax_theme_post_example', 'theme_post_example_init' );
add_action( 'wp_ajax_nopriv_theme_post_example', 'theme_post_example_init' );
function theme_post_example_init() {
	$nonce = sanitize_text_field( filter_input( INPUT_POST, 'nonce' ) ?? '' );
	if ( ! wp_verify_nonce( $nonce, GENERAL_NONCE ) ) {
		wp_send_json_error( [ 'message' => esc_html__( 'Невірний токен безпеки.', 'webbooks' ) ], 403 );
	}

	$post_id = absint( filter_input( INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT ) );
	if ( ! $post_id ) {
		wp_send_json_error( [ 'message' => esc_html__( 'Невірний ідентифікатор поста.', 'webbooks' ) ], 400 );
	}

	$args             = [ 'p' => $post_id ];
	$theme_post_query = new WP_Query( $args );
    ob_start();
	while ( $theme_post_query->have_posts() ): $theme_post_query->the_post();
		?>
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            <h4 class="modal-title mCustomScrollbar" id="myModalLabel"><?php the_title(); ?></h4>
        </div>
        <div class="modal-body" style="height:400px; overflow-y:scroll;" data-mcs-theme="dark">
			<?php the_content(); ?>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Закрить</button>
            <a href="<?php the_permalink(); ?>" type="button" class="btn btn-primary">Читать полностью</a>
        </div>
	<?php
	endwhile;
	wp_send_json_success(
		[
			'html' => ob_get_clean(),
		]
	);
}

add_action( 'wp_ajax_main_search_on_site', 'main_search_on_site' );
add_action( 'wp_ajax_nopriv_main_search_on_site', 'main_search_on_site' );

function main_search_on_site() {
	$nonce = sanitize_text_field( filter_input( INPUT_POST, 'nonce' ) ?? '' );
	if ( ! wp_verify_nonce( $nonce, GENERAL_NONCE ) ) {
		wp_send_json_error( [ 'message' => esc_html__( 'Невірний токен безпеки.', 'webbooks' ) ], 403 );
	}

	$param        = filter_input( INPUT_POST, 'var', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY ) ?? [];
	$cat          = sanitize_text_field( $param['category'] ?? '' );
	$status_book  = sanitize_text_field( $param['statusbook'] ?? '' );
	$language     = sanitize_text_field( $param['language'] ?? '' );
	$selectToLink = ! empty( $param['selectToLink'] );

	wp_send_json_success(
		[
			'html' => category_query( $cat, $status_book, $language, $selectToLink ),
		]
	);
}

function category_query( string $cat, string $statusbook, string $language, bool $selectToLink ):string
{
    $current_lang = '';
    if ( function_exists( 'pll_current_language' ) ) {
        $requested_lang = isset( $_REQUEST['lang'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['lang'] ) ) : '';
        $current_lang   = $requested_lang ?: pll_current_language( 'slug' );
    }

    $request_var = isset( $_REQUEST['var'] ) && is_array( $_REQUEST['var'] ) ? wp_unslash( $_REQUEST['var'] ) : [];
    $paged       = isset( $request_var['paged'] ) ? max( 1, (int) $request_var['paged'] ) : 1;
    $per_page    = 12;

    $args = [
        'posts_per_page' => $per_page,
        'paged'          => $paged,
        'post_status'    => 'publish',
        'orderby'        => 'comment_count',
        'category_name'  => $cat,
        'ignore_sticky_posts' => true,
        'meta_query'     => [
            'relation' => 'AND',
            [
                'key'     => 'complexity',
                'value'   => $statusbook,
                'compare' => '='
            ],
            [
                'key'     => 'language',
                'value'   => $language,
                'compare' => '='
            ]
        ]
    ];

    if ( ! empty( $current_lang ) ) {
        $args['lang'] = $current_lang;
    }

    $cache_key = sprintf(
        'webbooks_cat_query_%s',
        md5(
            wp_json_encode(
                [
                    'category'   => $cat,
                    'complexity' => $statusbook,
                    'language'   => $language,
                    'site_lang'  => $current_lang,
                    'page'       => $paged,
                ]
            )
        )
    );

    $cached_output = get_transient( $cache_key );
    if ( false !== $cached_output ) {
        return (string) $cached_output;
    }

	$query = new WP_Query( $args );
    ob_start();
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
            get_template_part( 'template/loop' );
		}
        echo webbooks_render_ajax_pagination( $query->max_num_pages, $paged, 'main_search_on_site' );
	} else {
		echo '<h2>' . esc_html__( 'Ничего не найдено по заданим критериям', 'webbooks' ) . '</h2>';
	}
	wp_reset_postdata();
    $output = ob_get_clean();
    set_transient( $cache_key, $output, 15 * MINUTE_IN_SECONDS );
    return $output;
}

function webbooks_render_ajax_pagination( int $max_pages, int $current_page, string $action = 'main_search_on_site' ): string {
    if ( $max_pages <= 1 ) {
        return '';
    }

    $pagination_links = paginate_links(
        [
            'base'      => '#page=%#%',
            'format'    => '',
            'current'   => $current_page,
            'total'     => $max_pages,
            'type'      => 'array',
            'prev_next' => true,
            'prev_text' => '&laquo;',
            'next_text' => '&raquo;',
        ]
    );

    if ( empty( $pagination_links ) || ! is_array( $pagination_links ) ) {
        return '';
    }

    $output = '<nav class="ajax-pagination-wrap"><ul class="pagination ajax-pagination">';
    foreach ( $pagination_links as $link ) {
        $is_current = strpos( $link, 'current' ) !== false;
        $page       = 0;

        if ( preg_match( '/page=([0-9]+)/', $link, $matches ) ) {
            $page = (int) $matches[1];
        }

        $replacement = 'href="#" data-page="' . $page . '" data-ajax-action="' . esc_attr( $action ) . '"';
        $link        = str_replace( 'href=\'#page=' . $page . '\'', $replacement, $link );
        $link        = str_replace( 'href="#page=' . $page . '"', $replacement, $link );

        if ( $is_current ) {
            $output .= '<li class="active">' . $link . '</li>';
        } else {
            $output .= '<li>' . $link . '</li>';
        }
    }
    $output .= '</ul></nav>';

    return $output;
}

function webbooks_is_seo_plugin_active(): bool {
    return defined( 'WPSEO_VERSION' )
        || class_exists( 'WPSEO_Frontend' )
        || defined( 'RANK_MATH_VERSION' )
        || class_exists( 'RankMath' )
        || defined( 'AIOSEO_VERSION' )
        || class_exists( '\\AIOSEO\\Plugin\\Common\\Main' )
        || defined( 'SEOPRESS_VERSION' )
        || class_exists( '\\The_SEO_Framework\\Load' );
}

add_action( 'wp_enqueue_scripts', 'webbooks_enqueue_external_services', 20 );
function webbooks_enqueue_external_services() {
    if ( is_admin() || is_page( 846 ) ) {
        return;
    }

    if ( apply_filters( 'webbooks_enable_recaptcha', true ) ) {
        wp_enqueue_script(
            'google-recaptcha-api',
            'https://www.google.com/recaptcha/api.js',
            [],
            null,
            true
        );
    }

    if ( apply_filters( 'webbooks_enable_google_ads', ! is_user_logged_in() ) ) {
        wp_enqueue_script(
            'google-adsbygoogle',
            'https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js',
            [],
            null,
            false
        );
        wp_script_add_data( 'google-adsbygoogle', 'async', true );

        wp_add_inline_script(
            'google-adsbygoogle',
            '(adsbygoogle=window.adsbygoogle||[]).push({google_ad_client:"ca-pub-1952021322373690",enable_page_level_ads:true});',
            'after'
        );
    }

    if ( apply_filters( 'webbooks_enable_google_analytics', ! is_user_logged_in() ) ) {
        wp_enqueue_script(
            'google-analytics',
            'https://www.google-analytics.com/analytics.js',
            [],
            null,
            true
        );
        wp_script_add_data( 'google-analytics', 'async', true );

        wp_add_inline_script(
            'google-analytics',
            "window.ga=window.ga||function(){(ga.q=ga.q||[]).push(arguments);};ga.l=1*new Date();ga('create','UA-57400123-2','auto');ga('send','pageview');",
            'before'
        );
    }
}

add_action( 'wp_head', 'webbooks_add_hreflang_links', 1 );
function webbooks_add_hreflang_links() {
    if ( webbooks_is_seo_plugin_active() || ! function_exists( 'pll_the_languages' ) ) {
        return;
    }

    $languages = pll_the_languages(
        [
            'raw'           => 1,
            'hide_if_empty' => 0,
            'hide_if_no_translation' => 0,
        ]
    );

    if ( empty( $languages ) || ! is_array( $languages ) ) {
        return;
    }

    foreach ( $languages as $language ) {
        if ( empty( $language['url'] ) || empty( $language['slug'] ) ) {
            continue;
        }

        printf(
            '<link rel="alternate" hreflang="%1$s" href="%2$s" />' . PHP_EOL,
            esc_attr( $language['slug'] ),
            esc_url( $language['url'] )
        );
    }
}

add_action( 'wp_head', 'webbooks_add_social_meta_fallback', 5 );
function webbooks_add_social_meta_fallback() {
    if ( webbooks_is_seo_plugin_active() ) {
        return;
    }

    global $post;

    $title       = wp_get_document_title();
    $description = get_bloginfo( 'description' );
    $image       = '';
    $url         = home_url( '/' );

    if ( is_singular() && $post instanceof WP_Post ) {
        $title       = get_the_title( $post );
        $description = has_excerpt( $post ) ? $post->post_excerpt : wp_trim_words( wp_strip_all_tags( $post->post_content ), 30, '...' );
        $url         = get_permalink( $post );

        if ( has_post_thumbnail( $post ) ) {
            $image = get_the_post_thumbnail_url( $post, 'full' );
        }
    }

    if ( empty( $image ) ) {
        $image = get_template_directory_uri() . '/screenshot.png';
    }

    echo "\n";
    printf( '<meta property="og:type" content="%s" />' . "\n", esc_attr( is_singular() ? 'article' : 'website' ) );
    printf( '<meta property="og:title" content="%s" />' . "\n", esc_attr( $title ) );
    printf( '<meta property="og:description" content="%s" />' . "\n", esc_attr( wp_strip_all_tags( $description ) ) );
    printf( '<meta property="og:url" content="%s" />' . "\n", esc_url( $url ) );
    printf( '<meta property="og:site_name" content="%s" />' . "\n", esc_attr( get_bloginfo( 'name' ) ) );
    printf( '<meta property="og:image" content="%s" />' . "\n", esc_url( $image ) );
    printf( '<meta name="twitter:card" content="%s" />' . "\n", esc_attr( $image ? 'summary_large_image' : 'summary' ) );
    printf( '<meta name="twitter:title" content="%s" />' . "\n", esc_attr( $title ) );
    printf( '<meta name="twitter:description" content="%s" />' . "\n", esc_attr( wp_strip_all_tags( $description ) ) );
    printf( '<meta name="twitter:image" content="%s" />' . "\n", esc_url( $image ) );
}

add_action( 'wp_ajax_global_search', 'global_search_int' );
add_action( 'wp_ajax_nopriv_global_search', 'global_search_int' );

function global_search_int() {
	$nonce = sanitize_text_field( filter_input( INPUT_POST, 'nonce' ) ?? '' );
	if ( ! wp_verify_nonce( $nonce, GENERAL_NONCE ) ) {
		wp_send_json_error( [ 'message' => esc_html__( 'Невірний токен безпеки.', 'webbooks' ) ], 403 );
	}

	$post_param             = filter_input( INPUT_POST, 'var', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY ) ?? [];
	$string_to_search       = sanitize_text_field( $post_param['StrTosearch'] ?? '' );
    $paged                  = max( 1, absint( $post_param['paged'] ?? 1 ) );
	$request_arguments      = array(
		's'              => trim( $string_to_search ),
		'posts_per_page' => 24,
        'paged'          => $paged,
		'post_status'    => 'publish',
		'orderby'        => 'comment_count',
        'post_type'      => [ 'post' ],
        'no_found_rows'  => false,
        'ignore_sticky_posts' => true,
	);
	$query_to_global_search = new WP_Query( $request_arguments );
	ob_start(); ?>
    <tr>
        <td>
            <h5>Найдено результатов:</h5>
        </td>
        <td>
            <h5><?= $query_to_global_search->found_posts; ?></h5>
        </td>
    </tr>
	<?php
	if ( $query_to_global_search->have_posts() ): ?>
		<?php
		while ( $query_to_global_search->have_posts() ) : ?>
			<?php
			$query_to_global_search->the_post(); ?>
            <tr>
                <td style="border-bottom: 2px solid #FF5F00">
                    <h5><a href="<?= get_the_permalink(); ?>" class="card-title"><?= get_the_title(); ?></a></h5>
                </td>
            </tr>
		<?php
		endwhile; ?>
	<?php
	else: ?>
        <h2> Ничего не найдено по заданим критериям</h2>
	<?php
	endif; ?>
    <?php if ( (int) $query_to_global_search->found_posts > 0 ) : ?>
        <tr>
            <td colspan="2">
                <?= webbooks_render_ajax_pagination( (int) $query_to_global_search->max_num_pages, $paged, 'global_search' ); ?>
            </td>
        </tr>
    <?php endif; ?>
	<?php
	wp_reset_postdata();
	wp_send_json_success(
		[
			'html' => ob_get_clean(),
		]
	);
}

function set_post_count_view() {
	$nonce = sanitize_text_field( filter_input( INPUT_POST, 'nonce' ) ?? '' );
	if ( ! wp_verify_nonce( $nonce, GENERAL_NONCE ) ) {
		wp_send_json_error( [ 'message' => esc_html__( 'Невірний токен безпеки.', 'webbooks' ) ], 403 );
	}

	$id_p = absint( filter_input( INPUT_POST, 'post_id', FILTER_SANITIZE_NUMBER_INT ) );
	if ( ! $id_p ) {
		wp_send_json_error( [ 'message' => esc_html__( 'Невірний ідентифікатор поста.', 'webbooks' ) ], 400 );
	}

	$views_count = get_post_meta( $id_p, '_views_count', true );
	$views_count ++;
	update_post_meta( $id_p, '_views_count', $views_count );
	wp_send_json_success(
		[
			'count' => (int) $views_count,
		]
	);
}

add_action( 'wp_ajax_postview_count_set', 'set_post_count_view' );
add_action( 'wp_ajax_nopriv_postview_count_set', 'set_post_count_view' );

function get_count_post_view()
{
	$nonce = sanitize_text_field( filter_input( INPUT_POST, 'nonce' ) ?? '' );
	if ( ! wp_verify_nonce( $nonce, GENERAL_NONCE ) ) {
		wp_send_json_error( [ 'message' => esc_html__( 'Невірний токен безпеки.', 'webbooks' ) ], 403 );
	}

    $post_id = absint( filter_input( INPUT_POST, 'post_id', FILTER_SANITIZE_NUMBER_INT ) );
	if ( ! $post_id ) {
		wp_send_json_error( [ 'message' => esc_html__( 'Невірний ідентифікатор поста.', 'webbooks' ) ], 400 );
	}

	wp_send_json_success(
		[
			'count' => (int) get_post_meta( $post_id, '_views_count', true ),
		]
	);
}

add_action( 'wp_ajax_postview_count_get', 'get_count_post_view' );
add_action( 'wp_ajax_nopriv_postview_count_get', 'get_count_post_view' );

// Получение ссилки на скачивание книги
add_action( 'wp_ajax_return_link_to_book', [ Class_Book::class, 'return_link_to_book_int' ] );
add_action( 'wp_ajax_nopriv_return_link_to_book', [ Class_Book::class, 'return_link_to_book_int' ] );

function get_short_description( string $content, int $words_count ):string
{
	return wp_trim_words($content, $words_count, '...');
}

add_filter( 'get_download_link', 'get_download_link', 10, 2 );

function get_download_link( WP_Post $post, int $category_id = 0 ): string {
	$link_to_download = get_post_meta( $post->ID, 'download', true );
	if ( ! empty( $link_to_download ) ) {
		$link_to_download_array    = parse_url( $link_to_download, PHP_URL_PATH );
        if ( ! is_array( $link_to_download_array ) ) {
            $link_to_download_key_path = $link_to_download_array;
        } else {
            $link_to_download_key_path = $link_to_download_array[ count( $link_to_download_array ) - 1 ];
        }
	} else {
		$link_to_download_key_path = $post->post_name;
	}

	return sprintf('<a href="%s?key=%s&count=%d&cat=%d" class="%s" target="_blank">%s</a>',
        home_url( '/download' ),
        $link_to_download_key_path,
        $post->ID,
        $category_id,
        'btn btn-primary btn-sm',
        __('Скачать', 'webbooks')
    );
}

add_filter('post_gallery', 'get_image_gallery', 10 , 1);

function get_image_gallery (WP_Post|string $post) : ?string {
    if ( is_string( $post ) ) {
        return null;
    }
   ob_start();
   include_once WEBBOOKS_PATH . '/template/image-gallery.php';
   return ob_get_clean();
}

function get_banner_src(): string {
	$links = [
		'https://gmhost.ua/wp-content/uploads/2023/02/baner_8.jpg',
		'https://gmhost.ua/wp-content/uploads/2023/02/baner_6.jpg',
		'https://gmhost.ua/wp-content/uploads/2023/02/baner_5.jpg',
		'https://gmhost.ua/wp-content/uploads/2023/02/baner_4.jpg',
		'https://gmhost.ua/wp-content/uploads/2023/02/baner_2.jpg',
		'https://gmhost.ua/wp-content/uploads/2023/02/baner_1.jpg'
	];

	return $links[ array_rand( $links, 1 ) ];
}
