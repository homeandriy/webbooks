<?php
/**
 * Функции шаблона (function.php)
 * @package WordPress
 * @subpackage your-clean-template
 */

define( 'WEBBOOKS_VERSION', '1.1.0' );

include 'options_page.php';

add_action('wp_footer', 'add_scripts');
if (!function_exists('add_scripts')) {
	function add_scripts() {
	    if(is_admin()) return false;
	    // wp_enqueue_script('jquery');
	    if (!is_page(846)) 
		{
		    wp_deregister_script('jquery');		    
		    wp_enqueue_script('jquery','//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js','','',true);

			wp_enqueue_script('bootstrapjs_4', get_template_directory_uri() .'/assets/js/jquery.mCustomScrollbar.concat.min.js', '','',true);
			wp_enqueue_script('jquery_lazy_load', get_stylesheet_directory_uri().'/assets/js/jquery.lazyload.min.js', array('jquery'), '1.9.1', true);
		    wp_enqueue_script('bootstrapjs',   get_template_directory_uri().'/assets/js/bootstrap.min.js', '','',true);
		    wp_enqueue_script('bootstrapjs_2', get_template_directory_uri().'/assets/js/jquery.fs.roller.min.js', '','',true);
		    wp_enqueue_script('countdown360',  get_template_directory_uri().'/assets/js/jquery.elevatezoom.js', '','',true);
		    wp_enqueue_script('bootstrapjs_3', get_template_directory_uri().'/assets/js/custom.js', '','',true);		
		    wp_enqueue_script('bootstrapjs_1', get_template_directory_uri().'/assets/js/theme.js', '','',true);
		    wp_enqueue_script('fancybox', get_template_directory_uri().'/assets/js/jquery.fancybox.js', '','',true);
		    wp_enqueue_script('slick', get_template_directory_uri().'/assets/js/slick.js', '','',true);
		    wp_enqueue_script('load-filter');
		    wp_enqueue_script('ajax-filter');
	    }
	    else
	    {
	    	wp_enqueue_script('bootstrapjs-14', get_template_directory_uri() . '/portfolio/assets/js/ie/respond.min.js', '','',true);
	    	wp_enqueue_script('bootstrapjs-13', get_template_directory_uri() . '/portfolio/assets/js/util.js', '','',true);
	    	wp_enqueue_script('bootstrapjs-12', get_template_directory_uri() . '/portfolio/assets/js/skel.min.js', '','',true);
	    	wp_enqueue_script('bootstrapjs-11', get_template_directory_uri() . '/portfolio/assets/js/jquery.scrollzer.min.js', '','',true);
	    	wp_enqueue_script('bootstrapjs-10', get_template_directory_uri() . '/portfolio/assets/js/jquery.scrolly.min.js', '','',true);
	    	wp_enqueue_script('bootstrapjs-15', get_template_directory_uri() . '/portfolio/assets/js/main.js', '','',true);
	    }
	}

}

add_action('wp_print_styles', 'add_styles');
if (!function_exists('add_styles')) {
	function add_styles() {
	    if(is_admin()) return false;
	    if (!is_page(846)) 
		{
		    wp_enqueue_style('sparkling-bootstrap1', get_template_directory_uri() . '/style.css');		
		    wp_enqueue_style('sparkling-bootstrap2', get_template_directory_uri() . '/assets/css/material-design-icons.min.css');
		    wp_enqueue_style('sparkling-bootstrap3', get_template_directory_uri() . '/assets/css/jquery.fs.roller.min.css');
		    wp_enqueue_style('sparkling-bootstrap4', get_template_directory_uri() . '/assets/css/jquery.mCustomScrollbar.min.css');
		    wp_enqueue_style('sparkling-bootstrap5', get_template_directory_uri() . '/assets/css/font-awesome.min.css');
		    wp_enqueue_style('fancybox', get_template_directory_uri() . '/assets/css/jquery.fancybox.css');
		    wp_enqueue_style('slick-theme', get_template_directory_uri() . '/assets/css/slick-theme.css');
		    wp_enqueue_style('slick', get_template_directory_uri() . '/assets/css/slick.css');

		    // wp_enqueue_style('sparkling-bootstrap5', get_template_directory_uri() . '/assets/css/materialize.min.css');
		    // wp_enqueue_style('sparkling-bootstrap6', get_template_directory_uri() . '/assets/css/bootstrap.css');
		}
		else
		{
			wp_enqueue_style('sparkling-bootstrap25252', get_template_directory_uri() . '/portfolio/assets/css/main.css');
		}

	}
}

/** Register Scripts. */
add_action('wp_enqueue_scripts', 'theme_register_scripts', 1);

function theme_register_scripts() {

	/** Register JavaScript Functions File */
	wp_register_script('functions-js', esc_url(trailingslashit(get_template_directory_uri()) . 'functions.js'), array('jquery'), '1.0', true);

	/** Localize Scripts */
	$php_array = array(
		'admin_ajax' => admin_url('admin-ajax.php'),
		'nonce' => wp_create_nonce('myajax-nonce'),
		);
	wp_localize_script('functions-js', 'php_array', $php_array);

}

/** Enqueue Scripts. */
add_action('wp_enqueue_scripts', 'theme_enqueue_scripts');
function theme_enqueue_scripts() {

	/** Enqueue JavaScript Functions File */
	wp_enqueue_script('functions-js');

}




function typical_title() {
	// функция вывода тайтла
	global $page, $paged; // переменные пагинации должны быть глобыльными
	wp_title('|', true, 'right'); // вывод стандартного заголовка с разделителем "|"
	bloginfo('name'); // вывод названия сайта
	$site_description = get_bloginfo('description', 'display'); // получаем описание сайта
	if ($site_description && (is_home() || is_front_page())) //если описание сайта есть и мы на главной
	{
		echo " | $site_description";
	}
	// выводим описание сайта с "|" разделителем
	if ($paged >= 2 || $page >= 2) // если пагинация была использована
	{
		echo ' | ' . sprintf(__('Страница %s'), max($paged, $page));
	}
	// покажем номер страницы с "|" разделителем
}

register_nav_menus(array( // Регистрируем 2 меню
	'top' => 'Верхнее', // Верхнее
	'bottom' => 'Внизу', // Внизу
));



add_theme_support('post-thumbnails'); // включаем поддержку миниатюр
set_post_thumbnail_size(250, 150); // задаем размер миниатюрам 250x150
add_image_size('big-thumb', 390, 440, false);
add_image_size('big-thumb-main', 390, 440, false); // добавляем еще один размер картинкам 400x400 с обрезкой
add_image_size('small-thumb', 100, 100, true);

register_sidebar(array( // регистрируем левую колонку, этот кусок можно повторять для добавления новых областей для виджитов
	'name' => 'Колонка слева', // Название в админке
	'id' => "left-sidebar", // идентификатор для вызова в шаблонах
	'description' => 'Обычная колонка в сайдбаре', // Описалово в админке
	'before_widget' => '<div id="%1$s" class="widget %2$s">', // разметка до вывода каждого виджета
	'after_widget' => "</div>\n", // разметка после вывода каждого виджета
	'before_title' => '<span class="widgettitle">', //  разметка до вывода заголовка виджета
	'after_title' => "</span>\n", //  разметка после вывода заголовка виджета
));



class clean_comments_constructor extends Walker_Comment {
	// класс, который собирает всю структуру комментов
	public function start_lvl(&$output, $depth = 0, $args = array()) {
		// что выводим перед дочерними комментариями
		$output .= '<ul class="children">' . "\n";
	}
	public function end_lvl(&$output, $depth = 0, $args = array()) {
		// что выводим после дочерних комментариев
		$output .= "</ul><!-- .children -->\n";
	}
	protected function comment($comment, $depth, $args) {
		// разметка каждого комментария, без закрывающего </li>!
		$classes = implode(' ', get_comment_class()) . ($comment->comment_author_email == get_the_author_meta('email') ? ' author-comment' : ''); // берем стандартные классы комментария и если коммент пренадлежит автору поста добавляем класс author-comment
		echo '<li id="li-comment-' . get_comment_ID() . '" class="' . $classes . '">' . "\n"; // родительский тэг комментария с классами выше и уникальным id
		echo '<div id="comment-' . get_comment_ID() . '">' . "\n"; // элемент с таким id нужен для якорных ссылок на коммент
		echo get_avatar($comment, 64) . "\n"; // покажем аватар с размером 64х64
		echo '<p class="meta">Автор: ' . get_comment_author() . "\n"; // имя автора коммента
		echo ' ' . get_comment_author_email(); // email автора коммента
		echo ' ' . get_comment_author_url(); // url автора коммента
		echo ' Добавлено ' . get_the_time('l, F jS, Y') . ' в ' . get_the_time() . '</p>' . "\n"; // дата и время комментирования
		if ('0' == $comment->comment_approved) {
			echo '<em class="comment-awaiting-moderation">Ваш комментарий будет опубликован после проверки модератором.</em>' . "\n";
		}
		// если комментарий должен пройти проверку
		comment_text() . "\n"; // текст коммента
		$reply_link_args = array( // опции ссылки "ответить"
			'depth' => $depth, // текущая вложенность
			'reply_text' => 'Ответить', // текст
			'login_text' => 'Вы должны быть залогинены', // текст если юзер должен залогинеться
		);
		echo get_comment_reply_link(array_merge($args, $reply_link_args)); // выводим ссылку ответить
		echo '</div>' . "\n"; // закрываем див
	}
	public function end_el(&$output, $comment, $depth = 0, $args = array()) {
		// конец каждого коммента
		$output .= "</li><!-- #comment-## -->\n";
	}
}

function pagination() {
	// функция вывода пагинации
	global $wp_query; // текущая выборка должна быть глобальной
	$big = 999999999; // число для замены
	echo paginate_links(array( // вывод пагинации с опциями ниже
		'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))), // что заменяем в формате ниже
		'format' => '?paged=%#%', // формат, %#% будет заменено
		'current' => max(1, get_query_var('paged')), // текущая страница, 1, если $_GET['page'] не определено
		'type' => 'list', // ссылки в ul
		'prev_text' => 'Назад', // текст назад
		'next_text' => 'Вперед', // текст вперед
		'total' => $wp_query->max_num_pages, // общие кол-во страниц в пагинации
		'show_all' => false, // не показывать ссылки на все страницы, иначе end_size и mid_size будут проигнорированны
		'end_size' => 15, //  сколько страниц показать в начале и конце списка (12 ... 4 ... 89)
		'mid_size' => 15, // сколько страниц показать вокруг текущей страницы (... 123 5 678 ...).
		'add_args' => false, // массив GET параметров для добавления в ссылку страницы
		'add_fragment' => '', // строка для добавления в конец ссылки на страницу
		'before_page_number' => '', // строка перед цифрой
		'after_page_number' => '', // строка после цифры
	));
}




/** Ajax Post */
add_action('wp_ajax_theme_post_example', 'theme_post_example_init');
add_action('wp_ajax_nopriv_theme_post_example', 'theme_post_example_init');
function theme_post_example_init() {

	/** Made Query */
	$args = array('p' => $_POST['id']);
	$theme_post_query = new WP_Query($args);
	while ($theme_post_query->have_posts()): $theme_post_query->the_post();
		?>
    <div class="modal-header">
 	  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
 	  <h4 class="modal-title mCustomScrollbar" id="myModalLabel"><?php the_title();?></h4>
 	</div>
 	<div  class="modal-body" style="height:400px; overflow-y:scroll;" data-mcs-theme="dark"  >
				<?php the_content();?>
 	</div>
 	<div class="modal-footer">
 	  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
 	  <a href="<?php the_permalink();?> " type="button" class="btn btn-primary">Читать полностю</a>
 	</div>
														  <?php
endwhile;
	exit;
}

// 2

/** Register Scripts. */
add_action('wp_enqueue_scripts', 'theme_register_scripts_2', 1);
function theme_register_scripts_2() {

	/** Register JavaScript Functions File */
	wp_register_script('ajax-filter', esc_url(trailingslashit(get_template_directory_uri()) . '/assets/js/ajax-filter.js'), array('jquery'), '1.0', true);

	/** Localize Scripts */
	$php_array1 = array(
		'admin_ajax' => admin_url('admin-ajax.php')		
		);
	wp_localize_script('ajax-filter', 'php_array1', $php_array1);
}

add_action('wp_ajax_my_seach', 'my_seach_super'); 
add_action('wp_ajax_nopriv_my_seach', 'my_seach_super');

function my_seach_super () {


	$name = (isset($_REQUEST['var'])) ? $_REQUEST['var'] : 'something_if_not';

	$param= $_REQUEST['var'];
	$cat = $param['category'];
	$statusbook = $param['statusbook'];
	$language = $param['language'];
	$selectToLink = $param['selectToLink'];

	 // Начало Вибоки

	 function CategoryQuery ($cat, $statusbook, $language,$selectToLink) {
	 	// Входние параметры сортировки
		 $args = array(
		 	'posts_per_page' => -1,
		 	'post_status' => 'publish',
		 	'orderby' => 'comment_count',
		 	'category_name' => $cat,

		 	'meta_query'	=> array(
		 			'relation'		=> 'AND',
		 			array(
		 				'key'		=> 'complexity',
		 				'value'		=> $statusbook,
		 				'compare'	=> '='
		 			),
		 			array(
		 				'key'		=> 'language',
		 				'value'		=> $language,	 				
		 				'compare'	=> '='
		 			)
		 		)
	 	);	
		// Сам цикл
		$query = new WP_Query( $args );
		 
		// Цикл
		if ( $query->have_posts() ) {
		 	while ( $query->have_posts() ) {
		 		$query->the_post();
		 		// Проверяем вигружать с ссилкамы на скачивание или нет
		 		if (!$selectToLink) {
		 			get_template_part('loop');
		 		} else {
		 			get_template_part('loop','link');
		 		}		 		
		 	}
		} else {
		 	echo "<h2> Ничего не найдено по заданим критериям</h2>";
		}	 
		 /* Возвращаем оригинальные данные поста. Сбрасываем $post. */
		wp_reset_postdata();
		wp_die();
	}
	// Конец Виборки

	$PostQuery = CategoryQuery($cat, $statusbook, $language,$selectToLink);
	
	return $PostQuery ;
		    		   
	} 
	
	// add_action('wp_ajax_get_write_form', 'get_write_form_int'); 
	// add_action('wp_ajax_nopriv_get_write_form', 'get_write_form_int');

	// function get_write_form_int() {
	// 	if(wp_doing_ajax () ) {			
	// 		$post = get_post( 1745 );
	// 		$return = apply_filters( 'the_content', $post->post_content );
	// 		echo $return;

	// 	};
	// 	wp_die();
	// }

	add_action('wp_ajax_global_seach', 'global_seach_int'); 
	add_action('wp_ajax_nopriv_global_seach', 'global_seach_int');

    function global_seach_int () {
    	$name1 = (isset($_REQUEST['var'])) ? $_REQUEST['var'] : 'something_if_not';
    	$ReqParam = $_REQUEST['var'];
		$StrtoSeach = $ReqParam['StrToSeach'];
		echo search_in_site($StrtoSeach);
		wp_die();
    }

	function search_in_site ($StrtoSeach) {
		// Входние параметры сортировки
		$argsToSeach = array(
			's' => trim($StrtoSeach),		
			'posts_per_page' => -1,
			'post_status' => 'publish',
			'orderby' => 'comment_count',
		);	
		// Сам цикл
		$html = '';
		$queryGlobalSeach = new WP_Query( $argsToSeach );
		$html .= ('<tr>
					<td>
						<h5>Найдено результатов:</h5>
					</td>
					<td>
						<h5>'.$queryGlobalSeach->post_count.'</h5>
					</td>
				</tr>');
		// Цикл
		if ( $queryGlobalSeach->have_posts() ) {
			while ( $queryGlobalSeach->have_posts() ) {
				$queryGlobalSeach->the_post();
					$html .= '<tr>
								<td style="border-bottom: 2px solid #FF5F00">
									<h5><a href="'. get_the_permalink() .'" class="card-title">'. get_the_title() .'</a></h5>
								</td>
							</tr>';
				}
		} else {
			$html .= "<h2> Ничего не найдено по заданим критериям</h2>";
		}			
		wp_reset_postdata();
		return $html;

	};
// Ajax подсчет статтей
// 
// 
//Ajax для количества проcмотров
//Запись в базу данных
function postview_count_set()
{	
	$id_p = $_POST['post_id'];
	$views_count = get_post_meta($id_p, '_views_count', true);
	$views_count++;
	update_post_meta($id_p, '_views_count', $views_count);
	exit; //чтобы в ответ не попало ничего лишнего
}

add_action('wp_ajax_postview_count_set', 'postview_count_set');
add_action('wp_ajax_nopriv_postview_count_set', 'postview_count_set');

//Чтение из базы
function postview_count_get()
{	
	$id_p = $_POST['post_id'];
	$views_count = get_post_meta($id_p, '_views_count', true);		
	echo $views_count;
	exit;

}

add_action('wp_ajax_postview_count_get', 'postview_count_get');
add_action('wp_ajax_nopriv_postview_count_get', 'postview_count_get');

function _remove_script_version( $src ){
$parts = explode( '?', $src );
return $parts[0];
}

//Убирает версию файла для js
add_filter( 'script_loader_src', '_remove_script_version', 15, 1 );
//Убирает версию файла для css
add_filter( 'style_loader_src', '_remove_script_version', 15, 1 );

// Вернет текущий URL страници 
function getUrl() {
  $url  = @( $_SERVER["HTTPS"] != 'on' ) ? 'http://'.$_SERVER["SERVER_NAME"] :  'https://'.$_SERVER["SERVER_NAME"];
  $url .= ( $_SERVER["SERVER_PORT"] != 80 ) ? ":".$_SERVER["SERVER_PORT"] : "";
  $url .= $_SERVER["REQUEST_URI"];
  return $url;
} 


// Получение ссилки нга скачивание книги

add_action('wp_ajax_return_link_to_book', 'return_link_to_book_int'); 
add_action('wp_ajax_nopriv_return_link_to_book', 'return_link_to_book_int');

function return_link_to_book_int () {
	$name1 = (isset($_REQUEST['count'])) ? $_REQUEST['count'] : 'something_if_not';
	$ReqParam1 = $_REQUEST['count'];
	$id = $ReqParam1['id'];	
	if (!empty($id)) 
	{
		if(!empty(get_post_meta($id, 'download', true)))
		{
			$linkTODownload['cloud_mail_ru']['link'] =  get_post_meta($id, 'download', true);
			$linkTODownload['cloud_mail_ru']['name'] =  "Скачать с Облако Mail.ru";
			$linkTODownload['cloud_mail_ru']['des'] =  "Скачать файл с облачного хранилища Cloud Mail.ru";
			$linkTODownload['cloud_mail_ru']['img'] =  "https://webbooks.com.ua/wp-content/uploads/2017/06/cloud_mail_ru.png";
		}
		if(!empty(get_post_meta($id, 'download_pcloud', true)))
		{
			$linkTODownload['pcloud']['link'] =  get_post_meta($id, 'download_pcloud', true);
			$linkTODownload['pcloud']['name'] =  "Скачать с Облака pCloud";
			$linkTODownload['pcloud']['des'] =  "Все Ваши документы всегда с Вами, куда бы Вы не отправились!";
			$linkTODownload['pcloud']['img'] =  "https://webbooks.com.ua/wp-content/uploads/2017/06/pcloud-logo.png";
		}
		if(!empty(get_post_meta($id, 'download_hubic', true)))
		{
			$linkTODownload['hubic']['link'] =  get_post_meta($id, 'download_hubic', true);
			$linkTODownload['hubic']['name'] =  "Скачать с CLOUD Webbooks";
			$linkTODownload['hubic']['des'] =  "Свое облако от webbooks.com.ua";
			$linkTODownload['hubic']['img'] =  "/wp-content/uploads/2018/08/touchIcon-core.png";
		}
		if(!empty(get_post_meta($id, 'download_mega', true)))
		{
			$linkTODownload['mega']['link'] =  get_post_meta($id, 'download_mega', true);
			$linkTODownload['mega']['name'] =  "Скачать с Облака Mega";
			$linkTODownload['mega']['des'] =  "Your documents stay with you everywhere on every one of your devices!";
			$linkTODownload['mega']['img'] =  "https://webbooks.com.ua/wp-content/uploads/2017/06/logo-facebook-e1498420140798.png";
		}
	}
	echo '<div class="row">';
  foreach ($linkTODownload as $key => $value) 
  	{ 
  		?>
  		<div class="col-sm-6 col-md-4">
  		<div class="thumbnail">
  	      <img src="<?php echo $value['img'] ?>" alt="<?php echo $value['name'] ?>">
  	      <div class="caption">
  	        <h3><?php echo $value['name'] ?></h3>
  	        <p><?php echo $value['des'] ?></p>
  	        <p><a href="<?php echo $value['link'] ?>" class="btn btn-primary" role="button" target='_blank'>Скачать </a>
  	      </div>
  	    </div>
  	    </div>
  	<?php }
	echo '</div></div>';	
	wp_die();

}





function get_language ($slug)
{
	switch ($slug) {
		case 'ru':
			$lang = 'Русский';
			break;
		case 'ua':
			$lang = 'Украинский';
			break;
		case 'en':
			$lang = 'Английский';
			break;
		case 'oth':
			$lang = 'Другой';
			break;
		
		default:
			$lang = 'Русский';
			break;
	}
	return $lang;
}
function get_complexity ($slug)
{
	switch ($slug) {
		case 'beginner':
			$complexity = 'Для начинающих программистов';
			break;
		case 'professional':
			$complexity = 'Для продвинутых программистов';
			break;
		default:
			$complexity = 'Не указано';
			break;
	}
	return $complexity;
}


function return_id_json_microdata ($id)
{
	$data = '
	<script type="application/ld+json">
	{
	  "@context": "http://schema.org",
	  "@type": "Book",	  
	  "copyrightYear": "2007",
	  "description": "NIMAC-sourced textbook",
	  "genre": "Educational Materials",
	  "inLanguage": "en-US",
	  "isFamilyFriendly": "true",
	  "isbn": "9780030426599",
	  "name": "Holt Physical Science",
	  "numberOfPages": "598",
	  "publisher": {
	    "@type": "Organization",
	    "name": "Holt, Rinehart and Winston"
	  }
	}
	</script>';
	return $data;
}

add_action('wp_ajax_loadmore'       , 'load_more');
add_action('wp_ajax_nopriv_loadmore', 'load_more');

function load_more () 
{
	$args_to_load_more = array
	(
		'post_type'      => 'post',
		'post_per_page'  => $_REQUEST['config']['product_on_page'],
		'offset'         => ($_REQUEST['config']['page']-1) * $_REQUEST['config']['product_on_page'],
		'orderby'        => 'date',
		'order'          => 'ASC'
	);
	$query_load_more = new WP_Query( $args_to_load_more );
	// Цикл
	if ( $query_load_more->have_posts() ) {
		while ( $query_load_more->have_posts() ) {
			$query_load_more->the_post();
			?>
				<div class="col-xs-12 col-sm-6 col-md-4 col-lg-6 content_block" >
		         	<div class="list-group" itemscope itemtype="http://schema.org/Book">
		         	  <div  class="list-group-item ">
		         	  	<div class="row">
		         	  		<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
		         	  			<?php 
		         	  				$url = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );						          				
		         	  			?>
		         	  			  <img itemprop="image" src="<?php echo $url; ?>" alt="..."  width="390" height="440" class="media-object">
		         	  		</div>
		         	  	
		         	  		<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
		         	  			<h4 class="list-group-item-heading"> <a href="<?php echo get_the_permalink(); ?>" itemprop="name"><?php echo get_the_title()  ?></a> </h4>
		         	  			<p class="list-group-item-text" itemprop="description">
		         	  			<?php 
		         	                $currentShortNews1 = get_the_content();
		         	                $currentShortNews1 = strip_tags($currentShortNews1);
		         	                $currentShortNews1 = substr($currentShortNews1, 0, 480);
		         	                $currentShortNews1 = rtrim($currentShortNews1, "!,.-");
		         	                $currentShortNews1 = substr($currentShortNews1, 0, strrpos($currentShortNews1, ' '));                                            
		         	                echo $currentShortNews1."… ";
		         	             ?>
		         	             <table class="table">
                     <tbody>
                         <tr>
                             <td>
                                  Автор книги
                             </td>
                             <td itemprop="author">

                                  <?php
                                  $id_current_post = get_the_ID(); 
                                  echo get_post_meta(get_the_ID(), 'autor', true); ?> 
                             </td>
                         </tr>
                         <tr>
                             <td>
                                  Год выхода:
                             </td>
                             <td itemprop="copyrightYear">
                                  <?php echo get_post_meta(get_the_ID(), 'year', true); ?> 
                             </td>
                         </tr>
                         <tr>
                             <td>
                                  Жанр:
                             </td>
                             <td itemprop="genre">
                                  <?php the_category()?>
                             </td>
                         </tr>
                         <tr>
                             <td>
                                  Издательство:
                             </td>
                             <td>
                                  <?php echo get_post_meta(get_the_ID(), 'create', true); ?>
                             </td>
                         </tr>
                         <tr>
                             <td>
                                  Язык:
                             </td>
                             <td itemprop="inLanguage">
                                  <?php
                                  $language = get_language(get_field('language'));
                                  echo $language;                                                                         
                                   ?>
                             </td>
                         </tr>
                         <tr>
                             <td>
                                  Статус:
                             </td>
                             <td>
                                  <?php 
                                  $complexity = trim(get_field( "complexity"));
                                     echo get_complexity($complexity); 
                                 ?>                                                                         
                             </td>
                         </tr>
                         <tr>
                             <td>
                                  Формат:
                             </td>
                             <td>
                                  <?php echo get_post_meta(get_the_ID(), 'format', true); ?>
                             </td>
                         </tr>
                         <tr>
                             <td>
                                  Cтраниц:
                             </td>
                             <td itemprop="numberOfPages">
                                  <?php echo get_post_meta(get_the_ID(), 'number_page', true); ?>
                             </td>
                         </tr>
                     </tbody>
                 </table>
		         	  			</p>          			
		         	  		</div>
		         	  		<div class="next-reed">
		         	  			<p>
		         	  				<a href="<?php echo get_the_permalink(); ?>" class="btn navbar-btn btn-info navbar-right">Дальше</a>
		         	  			</p>
		         	  			
		         	  		</div>
		         	  	</div>					            
		         	  </div>
		         	</div>
	         	</div>
			<?php
		}
	} else 
	{
		// Постов не найдено
		echo "Больше нет книг и постов";
	}
	/* Возвращаем оригинальные данные поста. Сбрасываем $post. */
	wp_reset_postdata();
	// print_r($_REQUEST);
	wp_die();
}

function get_short_description ( $content, $lenght ) {
	$currentShortNews1 = $content;
	$currentShortNews1 = strip_tags($currentShortNews1);
	$currentShortNews1 = substr($currentShortNews1, 0, $lenght);
	$currentShortNews1 = rtrim($currentShortNews1, "!,.-");
	$currentShortNews1 = substr($currentShortNews1, 0, strrpos($currentShortNews1, ' '));                                            
	return $currentShortNews1."… ";
}

function filter_lazyload( $content ) {
	return preg_replace_callback('/(<\s*img[^>]+)(src\s*=\s*"[^"]+")([^>]+>)/i', 'preg_lazyload', $content);
}
	
add_filter('the_content', 'filter_lazyload');

function preg_lazyload( $img_match ) {
	$img_replace = $img_match[1] . 'src="' . get_stylesheet_directory_uri().'/img/grey.gif" data-original'.substr($img_match[2], 3).$img_match[3];
	$img_replace = preg_replace('/class\s*=\s*"/i', 'class="lazy ', $img_replace);
	$img_replace = '<noscript>'.$img_match[0].'</noscript>';
	return $img_replace;
}




?>