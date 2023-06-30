<?php
/**
 * Шаблон обычной страницы (page.php)
 * @package WordPress
 * @subpackage your-clean-template
 * Template Name: portfolio-good
 */
get_header('portfolio');

?>
<body>
	<!-- Header -->
	<div id="header">
		<div class="top">
		<!-- Logo -->
			<div id="logo">
				<span class="image avatar48"><img src="<?= get_template_directory_uri()  ?>/portfolio/images/avatar.jpg" alt="" /></span>
				<h1 id="title">Андрей</h1>
				<p>Web-разрабочик</p>
			</div>
			<!-- Nav -->
			<nav id="nav">
				<ul>
					<li><a href="#top" id="top-link" class="skel-layers-ignoreHref"><span class="icon fa-home">Intro</span></a></li>
					<li><a href="#portfolio" id="portfolio-link" class="skel-layers-ignoreHref"><span class="icon fa-th">Портфолио</span></a></li>
					<li><a href="#about" id="about-link" class="skel-layers-ignoreHref"><span class="icon fa-user">Обо мне</span></a></li>
					<li><a href="#contact" id="contact-link" class="skel-layers-ignoreHref"><span class="icon fa-envelope">Обратная связь</span></a></li>
				</ul>
			</nav>
		</div>

		<div class="bottom">
			<!-- Social Icons -->
				<ul class="icons">
					<li><a href="https://twitter.com/homeandriy" class="icon fa-twitter"><span class="label">Twitter</span></a></li>
					<li><a href="https://www.facebook.com/homeandriy" class="icon fa-facebook"><span class="label">Facebook</span></a></li>
					<li><a href="https://ua.linkedin.com/pub/andriy-beznosko/a0/105/612" class="icon fa-linkedin"><span class="label">linkedin</span></a></li>							
					<li><a href="skype:#homeandriy?chat" class="icon fa fa-skype"><span class="label">Skype : Homeandriy</span></a></li>
				</ul>
		</div>
	</div>
	<!-- Main -->
	<div id="main">
		<!-- Intro -->
		<section id="top" class="one dark cover">
			<div class="container">
				<header>
					<h2 class="alt">Доброго времени суток,  я  <strong>Wordpress-Разработчик</strong></h2>
					<p>Работаю на чистом Wordpress. Владею переносом и оптимизацией верстки, работа с формами, Ajax и многое другое.</p>
				</header>
				<footer>
					<a href="#portfolio" class="button scrolly">Мои работы</a>
				</footer>
			</div>
		</section>
		<!-- Portfolio -->
		<section id="portfolio" class="two">
			<div class="container">
				<header>
					<h2>Портфолио</h2>
				</header>

				<p></p>

				<div class="row">
					<div class="4u 12u$(mobile)">
						<article class="item">
							<a href=" http://webbooks.com.ua/webbooks-com-ua/" class="image fit"><img src="http://webbooks.com.ua/wp-content/uploads/2016/03/shot-20160316-1941-1nkv3se.png" alt="" /></a>
							<header>
								<h3>Сайт поиска книг для web с ajax-фильтром поиска</h3>
							</header>
						</article>
						<article class="item">
							<a href=" http://webbooks.com.ua/vinkniga-com-ua/" class="image fit"><img src="http://webbooks.com.ua/wp-content/uploads/2016/03/shot-20160316-1941-6xwfzp.png" alt="" /></a>
							<header>
								<h3>Интернет магазин книг</h3>
							</header>
						</article>
					</div>
					<div class="4u 12u$(mobile)">
						<article class="item">
							<a href="http://skinnyandstrong.com/" class="image fit"><img src="<?= get_template_directory_uri()  ?>/portfolio/images/skinnystrong.png" alt="" /></a>
							<header>
								<h3>Спортивный клуб закрытого формата для девушек</h3>
							</header>
						</article>									
					</div>
					<div class="4u 12u$(mobile)">
						<article class="item">
							<a href="http://webbooks.com.ua/pozhelaju-ru/" class="image fit"><img src="http://webbooks.com.ua/wp-content/uploads/2016/03/shot-20160316-1941-c03ztf.jpeg" alt="" /></a>
							<header>
								<h3>Поздравления и пожелания на все случаи в жизни pozhelaju.ru</h3>
							</header>
						</article>									
					</div>
					<div class="4u 12u$(mobile)">
						<article class="item">
							<a href=" http://webbooks.com.ua/stanislava-lisovska-com/" class="image fit"><img src="http://webbooks.com.ua/wp-content/uploads/2016/03/shot-20160316-1941-1jtpr72.png" alt="" /></a>
							<header>
								<h3>stanislava-lisovska.com</h3>
							</header>
						</article>									
					</div>
					<div class="4u 12u$(mobile)">
						<article class="item">
							<a href=" http://webbooks.com.ua/httptwodayweb-ru/" class="image fit"><img src="http://webbooks.com.ua/wp-content/uploads/2016/03/shot-20160316-1941-1ionlil.png" alt="" /></a>
							<header>
								<h3>Небольшой блог своего производства. Подготавливается как площадка для массового импорта статей с рунета (пишется парсер статей с рунета, для массового импорта)</h3>
							</header>
						</article>									
					</div>
				</div>
			</div>
		</section>
			<!-- About Me -->
		<section id="about" class="three">
			<div class="container">
				<header>
					<h2>Обо мне</h2>
				</header>
				<a href="#" class="image featured"><img src="<?= get_template_directory_uri()  ?>/portfolio/images/pic08.jpg" alt="" /></a>
				<p class="no-bpttom"> Работаю на <a href="https://goo.gl/mcaxFL">Wordpress</a> с 2011 года. Опыт работы с фрейморками <a href="http://getbootstrap.com/">Bootstrap</a>, <a href="http://materializecss.com/">Materialize</a>, JQuery. 
				  В разделе портфолио показаны только коммерческие работы.</p> <p class="no-bpttom t-left"> <i class="fa fa-code"></i> Навыки работы:<i class="fa fa-code"></i></p> 
				<ul class="list-group profit">
					<li class="list-group"><i class="fa fa-check-square-o fa-fw"></i>Перенос верстки с <a href="http://www.templatemonster.com/">templatemonster</a> и других ресурсов на Wordpress без покупки шаблона;</li>
					<li class="list-group"><i class="fa fa-check-square-o fa-fw"></i>Создание форм на сайте (обратная связь, заказы и т. д.);</li>
					<li class="list-group"><i class="fa fa-check-square-o fa-fw"></i>Кастомизация Вашей темы по Вашим потребностям;</li>
					<li class="list-group"><i class="fa fa-check-square-o fa-fw"></i>Верстка несложных проектов на <a href="http://getbootstrap.com/">Bootstrap</a>, <a href="http://materializecss.com/">Materialize</a>;</li>
					<li class="list-group"><i class="fa fa-check-square-o fa-fw"></i>Сопровождения проектов созданых мною;</li>
					<li class="list-group"><i class="fa fa-check-square-o fa-fw"></i>Разный спектр услуг по работе с Wordpress (доработка тем, поиск и исправления ошибок)</li>
				</ul>
				<table class="default">
					<caption>Цены на написания сайтов при условии готовой верстки или наличия LiveDemo</caption>
					<tr>
					    <td>Тип сайта</td>
					    <td>Цена</td>
				  	</tr>
				  	<tr>
					    <td>Визитка (5-7 страниц) + форма обратной связи</td>
					    <td>от $80 (без дизайна и SEO)</td>
				  	</tr>
				  	<tr>
					    <td>Персональный блог (форма обратной связи, комментарии, регистрация и многое другое)</td>
					    <td>от $100$ (Ваш дизайн)</td>
				  	</tr>
				  	<tr>
					    <td>Интернет магазин (корзина, форма заказов, категории, несложный фильтр поиска товара)</td>
					    <td>от $250</td>
				  	</tr>
				  	<tr>
					    <td>Несложная верстка под Wordpress сайт</td>
					    <td>от $10</td>
				  	</tr>
				  	<tr>
					    <td>Сопровождения проекта (в мес)</td>
					    <td>от $20</td>
				  	</tr>
				</table>
				<p>
					Точную цену узнавайте задавая вопрсы в разделе "Обратная связь" или отправив письмо на homeandriy@gmail.com
				</p>
			</div>
		</section>
		<!-- Contact -->
		<section id="contact" class="four">
			<div class="container">
				<header>
					<h2>Обратная связь</h2>
				</header>
				<p>Все вопросы задавайте в этой форме</p>
				<?php  echo do_shortcode( '[contact-form-7 id="848" title="portfolio_contact_form"]' );	?>
			</div>
		</section>
	</div>
<?php 

get_footer('portfolio');
 ?>
