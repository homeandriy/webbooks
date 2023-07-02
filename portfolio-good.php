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
					<li><a href="https://ua.linkedin.com/pub/andriy-beznosko/a0/105/612" class="icon fa-linkedin"><span class="label">Linkedin</span></a></li>
				</ul>
		</div>
	</div>
	<!-- Main -->
	<div id="main">
		<!-- Intro -->
		<section id="top" class="one dark cover">
			<div class="container">
				<header>
					<h2 class="alt">Резюме</h2>
					<p>На сьогодні я являюсь Full-stack розробником</p>
                    <p>Пишук код на:</p>
                    <ul>
                        <li>Wordpress + Woocommerce</li>
                        <li>Laravel</li>
                        <li>Symfony</li>
                        <li>Працював з React</li>
                        <li>Працював з ElasticSearch</li>
                        <li>Працював з Next js</li>
                        <li>Працював з багатьма "самописами" на PHP</li>
                    </ul>
				</header>
				<footer>
					<a href="#portfolio" class="button scrolly">Мої роботи</a>
				</footer>
			</div>
		</section>
		<!-- Portfolio -->
		<section id="portfolio" class="two">
			<div class="container">
				<header>
					<h2>Портфоліо</h2>
				</header>
				<div class="row">
					<div class="4u 12u$(mobile)">
						<article class="item">
							<a href="http://webbooks.com.ua/webbooks-com-ua/" class="image fit"><img src="http://webbooks.com.ua/wp-content/uploads/2016/03/shot-20160316-1941-1nkv3se.png" alt="Звичайний сайт з книгами для розробників" /></a>
							<header>
								<h3>Звичайний сайт з книгами для розробників</h3>
							</header>
						</article>
						<article class="item">
							<a href="http://webbooks.com.ua/vinkniga-com-ua/" class="image fit"><img src="http://webbooks.com.ua/wp-content/uploads/2016/03/shot-20160316-1941-6xwfzp.png" alt="Інтернет магазин вживаних книг" /></a>
							<header>
								<h3>Інтернет магазин вживаних книг</h3>
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
							<a href="http://webbooks.com.ua/pan-sirko-com-ua/" class="image fit"><img src="http://webbooks.com.ua/wp-content/uploads/2016/03/shot-20160316-1941-1jtpr72.png" alt="Офіційний сайт бренду ПАН СІРКО" /></a>
							<header>
								<h3>sОфіційний сайт бренду ПАН СІРКО</h3>
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
					<h2>Про мене</h2>
				</header>
				<a href="#" class="image featured"><img src="<?= get_template_directory_uri()?>/portfolio/images/pic08.jpg" alt="" /></a>
				<p>
                    Маю досвід роботи більше 5 років на українському ринку розробки на PHP. Працював з Wordpress, Laravel та багатьма "самописами" на PHP.
                    Налаштовував пошук, оптимізував запити в БД, налаштовував кеш на Redis, та багато іншого.
                    Деякі проєкти вів сам від погодження плану з бізнесом, до заведення задач в Jira, та потім викладання їх по моделі MVP.
                    В деяких командах працював по Scrum.
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

