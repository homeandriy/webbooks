<?php
/**
 * Функции шаблона (function.php)
 * @package WordPress
 * @subpackage your-clean-template
 */

define( 'WEBBOOKS_VERSION', '1.2.0' );
define( 'WEBBOOKS_PATH', get_stylesheet_directory());
define( 'WEBBOOKS_URL', get_stylesheet_directory_uri());

require_once WEBBOOKS_PATH . '/options_page.php';
require_once WEBBOOKS_PATH . '/Classes/class-book.php';
require_once WEBBOOKS_PATH . '/Classes/class-search.php';
require_once WEBBOOKS_PATH . '/Classes/class-clean_comments_constructor.php';

add_action( 'wp_footer', 'add_scripts' );
if ( ! function_exists( 'add_scripts' ) ) {
	function add_scripts() {
		if ( is_admin() ) {
			return;
		}
		// wp_enqueue_script('jquery');
		if ( ! is_page( 846 ) ) {
			wp_deregister_script( 'jquery' );
			wp_enqueue_script( 'jquery', '//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js', '', '', true );

			wp_enqueue_script( 'bootstrapjs_4',
				get_template_directory_uri() . '/assets/js/jquery.mCustomScrollbar.concat.min.js', '', '', true );
			wp_enqueue_script( 'jquery_lazy_load', get_stylesheet_directory_uri() . '/assets/js/jquery.lazyload.min.js',
				array( 'jquery' ), '1.9.1', true );
			wp_enqueue_script( 'bootstrapjs', get_template_directory_uri() . '/assets/js/bootstrap.min.js', '', '',
				true );
			wp_enqueue_script( 'bootstrapjs_2', get_template_directory_uri() . '/assets/js/jquery.fs.roller.min.js', '',
				'', true );
			wp_enqueue_script( 'countdown360', get_template_directory_uri() . '/assets/js/jquery.elevatezoom.js', '',
				'', true );
			wp_enqueue_script( 'bootstrapjs_3', get_template_directory_uri() . '/assets/js/custom.js', '', '', true );
			wp_enqueue_script( 'bootstrapjs_1', get_template_directory_uri() . '/assets/js/theme.js', '', '', true );
			wp_enqueue_script( 'fancybox', get_template_directory_uri() . '/assets/js/jquery.fancybox.js', '', '',
				true );
			wp_enqueue_script( 'slick', get_template_directory_uri() . '/assets/js/slick.js', '', '', true );
			wp_enqueue_script( 'load-filter' );
			wp_enqueue_script( 'ajax-filter' );
		} else {
			wp_enqueue_script( 'bootstrapjs-14',
				get_template_directory_uri() . '/portfolio/assets/js/ie/respond.min.js', '', '', true );
			wp_enqueue_script( 'bootstrapjs-13', get_template_directory_uri() . '/portfolio/assets/js/util.js', '', '',
				true );
			wp_enqueue_script( 'bootstrapjs-12', get_template_directory_uri() . '/portfolio/assets/js/skel.min.js', '',
				'', true );
			wp_enqueue_script( 'bootstrapjs-11',
				get_template_directory_uri() . '/portfolio/assets/js/jquery.scrollzer.min.js', '', '', true );
			wp_enqueue_script( 'bootstrapjs-10',
				get_template_directory_uri() . '/portfolio/assets/js/jquery.scrolly.min.js', '', '', true );
			wp_enqueue_script( 'bootstrapjs-15', get_template_directory_uri() . '/portfolio/assets/js/main.js', '', '',
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
	wp_register_script( 'functions-js', esc_url( trailingslashit( get_template_directory_uri() ) . 'functions.js' ),
		array( 'jquery' ), '1.0', true );
	$php_array = array(
		'admin_ajax' => admin_url( 'admin-ajax.php' ),
		'nonce'      => wp_create_nonce( 'myajax-nonce' ),
	);
	wp_localize_script( 'functions-js', 'php_array', $php_array );
}

add_action( 'wp_enqueue_scripts', 'theme_enqueue_scripts' );
function theme_enqueue_scripts() {
	wp_enqueue_script( 'functions-js' );
}

register_nav_menus( array(
	'top'    => 'Верхнее',
	'bottom' => 'Внизу',
) );


add_theme_support( 'post-thumbnails' );
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

	$args             = array( 'p' => $_POST['id'] );
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
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <a href="<?php the_permalink(); ?>" type="button" class="btn btn-primary">Читать полностью</a>
        </div>
	<?php
	endwhile;
    echo ob_get_clean();
	wp_die();
}

add_action( 'wp_enqueue_scripts', 'theme_register_scripts_2', 1 );
function theme_register_scripts_2() {
	wp_register_script( 'ajax-filter', esc_url( trailingslashit( get_template_directory_uri() ) . '/assets/js/ajax-filter.js' ), array( 'jquery' ), '1.0', true );
	$php_array1 = array(
		'admin_ajax' => admin_url( 'admin-ajax.php' )
	);
	wp_localize_script( 'ajax-filter', 'php_array1', $php_array1 );
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
	$args = array(
		'posts_per_page' => - 1,
		'post_status'    => 'publish',
		'orderby'        => 'comment_count',
		'category_name'  => $cat,

		'meta_query' => array(
			'relation' => 'AND',
			array(
				'key'     => 'complexity',
				'value'   => $statusbook,
				'compare' => '='
			),
			array(
				'key'     => 'language',
				'value'   => $language,
				'compare' => '='
			)
		)
	);
	// Сам цикл
	$query = new WP_Query( $args );
    ob_start();
	// Цикл
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			// Проверяем вигружать с ссилкамы на скачивание или нет
			if ( ! $selectToLink ) {
				get_template_part( 'template/loop' );
			} else {
				get_template_part( 'template/loop', 'link' );
			}
		}
	} else {
		echo "<h2> Ничего не найдено по заданим критериям</h2>";
	}
	/* Возвращаем оригинальные данные поста. Сбрасываем $post. */
	wp_reset_postdata();
    return ob_get_clean();
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

function get_count_post_view() {
	$post_id        = (int)$_POST['post_id'];
	$views_count = get_post_meta( $post_id, '_views_count', true );
	echo $views_count;
	wp_die();

}

add_action( 'wp_ajax_postview_count_get', 'get_count_post_view' );
add_action( 'wp_ajax_nopriv_postview_count_get', 'get_count_post_view' );

function _remove_script_version( $src ) {
	$parts = explode( '?', $src );

	return $parts[0];
}
add_filter( 'script_loader_src', '_remove_script_version', 15, 1 );
add_filter( 'style_loader_src', '_remove_script_version', 15, 1 );

// Получение ссилки нга скачивание книги
add_action( 'wp_ajax_return_link_to_book', [ Class_Book::class, 'return_link_to_book_int' ] );
add_action( 'wp_ajax_nopriv_return_link_to_book', [ Class_Book::class, 'return_link_to_book_int' ] );

function get_short_description( $content, $length ):string
{
	$raw_content = $content;
	$raw_content = strip_tags( $raw_content );
	$raw_content = substr( $raw_content, 0, $length );
	$raw_content = rtrim( $raw_content, "!,.-" );
	$raw_content = substr( $raw_content, 0, strrpos( $raw_content, ' ' ) );

	return $raw_content . '… ';
}

add_filter( 'get_download_link', 'get_download_link', 10, 2 );

function get_download_link( WP_Post $post, int $category_id = 0 ): string {
	$link_to_download = get_post_meta( $post->ID, 'download', true );
	if ( ! empty( $link_to_download ) ) {
		$link_to_download_array    = parse_url( $link_to_download, PHP_URL_PATH );
		$link_to_download_key_path = $link_to_download_array[ count( $link_to_download_array ) - 1 ];
	} else {
		$link_to_download_key_path = $post->post_name;
	}

	return "<a
        href='" . home_url( '/download' ) . "?key=" . $link_to_download_key_path . "&count=" . $post->ID . "&cat=" . $category_id . "'
        class='btn btn-primary btn-sm'
        target='_blank'>
            Скачать
        </a>";
}

add_filter('post_gallery', 'get_image_gallery', 10 , 1);

function get_image_gallery (WP_Post $post) : string {
   ob_start();
   include_once WEBBOOKS_PATH . '/template/image-gallery.php';
   $content = ob_get_contents();
   ob_clean();
   return $content;
}
