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
        'nonce'      => wp_create_nonce( DOWNLOAD_BOOK_NONCE ),
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
	$args             = ['p' => $_POST['id'] ];
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
    echo ob_get_clean();
	wp_die();
}

add_action( 'wp_ajax_main_search_on_site', 'main_search_on_site' );
add_action( 'wp_ajax_nopriv_main_search_on_site', 'main_search_on_site' );

function main_search_on_site() {
	$param        = $_REQUEST['var'];
	$cat          = $param['category'];
	$status_book  = $param['statusbook'];
	$language     = $param['language'];
	$selectToLink = (bool) $param['selectToLink'];

	echo category_query( $cat, $status_book, $language, $selectToLink);
	wp_die();
}

function category_query( string $cat, string $statusbook, string $language, bool $selectToLink ):string
{
    $current_lang = '';
    if ( function_exists( 'pll_current_language' ) ) {
        $requested_lang = isset( $_REQUEST['lang'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['lang'] ) ) : '';
        $current_lang   = $requested_lang ?: pll_current_language( 'slug' );
    }

    $args = [
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'orderby'        => 'comment_count',
        'category_name'  => $cat,
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

	$query = new WP_Query( $args );
    ob_start();
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
            get_template_part( 'template/loop' );
		}
	} else {
		echo '<h2>' . esc_html__( 'Ничего не найдено по заданим критериям', 'webbooks' ) . '</h2>';
	}
	wp_reset_postdata();
    return ob_get_clean();
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
	$post_param             = $_REQUEST['var'];
	$string_to_search       = $post_param['StrTosearch'];
	$request_arguments      = array(
		's'              => trim( $string_to_search ),
		'posts_per_page' => - 1,
		'post_status'    => 'publish',
		'orderby'        => 'comment_count',
	);
	$query_to_global_search = new WP_Query( $request_arguments );
	ob_start(); ?>
    <tr>
        <td>
            <h5>Найдено результатов:</h5>
        </td>
        <td>
            <h5><?= $query_to_global_search->post_count; ?></h5>
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
	<?php
	wp_reset_postdata();
	echo ob_get_clean();
	wp_die();
}

function set_post_count_view() {
	$id_p        = $_POST['post_id'];
	$views_count = get_post_meta( $id_p, '_views_count', true );
	$views_count ++;
	update_post_meta( $id_p, '_views_count', $views_count );
	wp_die();
}

add_action( 'wp_ajax_postview_count_set', 'set_post_count_view' );
add_action( 'wp_ajax_nopriv_postview_count_set', 'set_post_count_view' );

function get_count_post_view()
{
    $post_id = (int)$_POST['post_id'];
    echo get_post_meta($post_id, '_views_count', true);
    wp_die();
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
