<?php
/**
 * Шаблон обычной страницы (page.php)
 *
 * @package WordPress
 * @subpackage webbooks
 * Template Name: portfolio-good
 */

get_header( 'portfolio' );
?>
<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>
	<!-- Header -->
	<div id="header">
		<div class="top">
		<!-- Logo -->
			<div id="logo">
				<span class="image avatar48"><img src="<?php echo esc_url( get_template_directory_uri() . '/portfolio/images/avatar.jpg' ); ?>" loading="lazy" alt="Андрій Безносько" /></span>
				<h1 id="title">Андрій</h1>
				<p><?php esc_html_e( 'PHP Full Stack Developer', 'webbooks' ); ?></p>
			</div>
			<!-- Nav -->
			<nav id="nav">
				<ul>
					<li><a href="#top" id="top-link" class="skel-layers-ignoreHref"><span class="icon fa-home"><?php esc_html_e( 'Intro', 'webbooks' ); ?></span></a></li>
					<li><a href="#portfolio" id="portfolio-link" class="skel-layers-ignoreHref"><span class="icon fa-th"><?php esc_html_e( 'Portfolio', 'webbooks' ); ?></span></a></li>
					<li><a href="#about" id="about-link" class="skel-layers-ignoreHref"><span class="icon fa-user"><?php esc_html_e( 'About me', 'webbooks' ); ?></span></a></li>
					<li><a href="#contact" id="contact-link" class="skel-layers-ignoreHref"><span class="icon fa-envelope"><?php esc_html_e( 'Contact', 'webbooks' ); ?></span></a></li>
					<li><a href="<?php echo esc_url( \Webbooks\Localization\Polylang::homeUrl() ); ?>" id="go-to-site" class="skel-layers-ignoreHref"><span class="icon fa-arrow-circle-left"><?php esc_html_e( 'Back to website', 'webbooks' ); ?></span></a></li>
					<?php if ( function_exists( 'pll_the_languages' ) ) : ?>
						<?php
						$portfolio_languages = pll_the_languages(
							array(
								'raw'                    => 1,
								'hide_if_no_translation' => 1,
							)
						);
						?>
						<?php if ( is_array( $portfolio_languages ) ) : ?>
							<?php foreach ( $portfolio_languages as $portfolio_language ) : ?>
								<?php
								$is_current_portfolio_language = ! empty( $portfolio_language['current_lang'] );
								$portfolio_page_id             = get_queried_object_id();
								$portfolio_language_slug       = (string) ( $portfolio_language['slug'] ?? '' );
								$portfolio_language_url        = \Webbooks\Localization\Polylang::translatedPostUrl( $portfolio_page_id, $portfolio_language_slug );
								?>
								<li class="portfolio-language-switcher<?php echo $is_current_portfolio_language ? ' is-current' : ''; ?>">
									<a href="<?php echo esc_url( $portfolio_language_url ); ?>" aria-current="<?php echo $is_current_portfolio_language ? 'page' : 'false'; ?>">
										<span class="icon fa-language"><?php echo esc_html( $portfolio_language['name'] ?? '' ); ?></span>
									</a>
								</li>
							<?php endforeach; ?>
						<?php endif; ?>
					<?php endif; ?>
				</ul>
			</nav>
		</div>

		<div class="bottom">
			<!-- Social Icons -->
				<ul class="icons">
					<li><a href="https://x.com/homeandriy" class="icon fa-twitter"><span class="label">X</span></a></li>
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
					<h2 class="alt"><?php esc_html_e( 'Resume', 'webbooks' ); ?></h2>
					<p><?php esc_html_e( 'I am a Full Stack developer.', 'webbooks' ); ?></p>
					<p><?php esc_html_e( 'I write code with:', 'webbooks' ); ?></p>
					<ul>
						<li>WordPress + Woocommerce</li>
						<li>Laravel</li>
						<li>Symfony</li>
						<li><?php esc_html_e( 'React', 'webbooks' ); ?></li>
						<li><?php esc_html_e( 'Elasticsearch', 'webbooks' ); ?></li>
						<li><?php esc_html_e( 'Next.js', 'webbooks' ); ?></li>
						<li><?php esc_html_e( 'Custom PHP applications', 'webbooks' ); ?></li>
					</ul>
				</header>
				<footer>
					<a href="#portfolio" class="button scrolly"><?php esc_html_e( 'My work', 'webbooks' ); ?></a>
				</footer>
			</div>
		</section>
		<!-- Portfolio -->
		<section id="portfolio" class="two">
			<div class="container">
				<header>
					<h2><?php esc_html_e( 'Portfolio', 'webbooks' ); ?></h2>
				</header>
				<div class="row">
					<div class="4u 12u$(mobile)">
						<article class="item">
							<a href="https://webbooks.com.ua/webbooks-com-ua/" class="image fit"><img src="https://webbooks.com.ua/wp-content/uploads/2016/03/shot-20160316-1941-1nkv3se.png" alt="<?php echo esc_attr__( 'A book site for developers', 'webbooks' ); ?>" /></a>
							<header>
								<h3><?php esc_html_e( 'A book site for developers', 'webbooks' ); ?></h3>
							</header>
						</article>
						<article class="item">
							<a href="https://webbooks.com.ua/vinkniga-com-ua/" class="image fit"><img src="https://webbooks.com.ua/wp-content/uploads/2016/03/shot-20160316-1941-6xwfzp.png" alt="<?php echo esc_attr__( 'An online store for used books', 'webbooks' ); ?>" /></a>
							<header>
								<h3><?php esc_html_e( 'An online store for used books', 'webbooks' ); ?></h3>
							</header>
						</article>
					</div>
					<div class="4u 12u$(mobile)">
						<article class="item">
							<a href="https://webbooks.com.ua/pozhelaju-ru/" class="image fit"><img src="https://webbooks.com.ua/wp-content/uploads/2016/03/shot-20160316-1941-c03ztf.jpeg" alt="" /></a>
							<header>
								<h3><?php esc_html_e( 'Greetings and wishes for every occasion — pozhelaju.ru', 'webbooks' ); ?></h3>
							</header>
						</article>									
					</div>
					<div class="4u 12u$(mobile)">
						<article class="item">
							<a href="https://webbooks.com.ua/pan-sirko-com-ua/" class="image fit"><img src="https://webbooks.com.ua/wp-content/uploads/2016/03/shot-20160316-1941-1jtpr72.png" alt="<?php echo esc_attr__( 'The official website of the PAN SIRKO brand', 'webbooks' ); ?>" /></a>
							<header>
								<h3><?php esc_html_e( 'The official website of the PAN SIRKO brand', 'webbooks' ); ?></h3>
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
					<h2><?php esc_html_e( 'About me', 'webbooks' ); ?></h2>
				</header>
				<figure class="image featured">
					<img src="<?php echo esc_url( get_template_directory_uri() . '/portfolio/images/pic08.jpg' ); ?>" loading="lazy" alt="sublime-text-code">
				</figure>
				<p>
					<?php esc_html_e( 'I have more than five years of experience in the Ukrainian PHP development market. I have worked with WordPress, Laravel, and custom PHP applications.', 'webbooks' ); ?>
					<?php esc_html_e( 'My work includes configuring search, optimizing database queries, setting up Redis caching, and more.', 'webbooks' ); ?>
					<?php esc_html_e( 'I have independently led projects from agreeing a plan with the business, through creating Jira tasks, to delivering them using an MVP approach.', 'webbooks' ); ?>
					<?php esc_html_e( 'I have also worked in Scrum teams.', 'webbooks' ); ?>
				</p>
			</div>
		</section>
		<!-- Contact -->
		<section id="contact" class="four">
			<div class="container">
				<header>
					<h2><?php esc_html_e( 'Contact', 'webbooks' ); ?></h2>
				</header>
				<a href="https://t.me/homeandriy_questions" target="_blank" rel="nofollow, noindex"><?php esc_html_e( 'Write to me on Telegram', 'webbooks' ); ?></a>
			</div>
		</section>
	</div>
<?php
get_footer( 'portfolio' );
