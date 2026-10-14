<?php
/**
 * Шаблон обычной страницы (page.php)
 *
 * @package WordPress
 * @subpackage webbooks
 * Template Name: portfolio-good
 */

get_header( 'portfolio' );
$author_support_url = \Webbooks\Support\AuthorSupport::publishedPageUrl();
?>
<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>
	<!-- Header -->
	<div id="header">
		<div class="top">
		<!-- Logo -->
			<div id="logo">
				<span class="image avatar48"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/portfolio/avatar.jpg' ); ?>" loading="lazy" alt="Андрій Безносько" /></span>
				<p id="title">Андрій</p>
				<p><?php esc_html_e( 'PHP Full Stack Developer', 'webbooks' ); ?></p>
			</div>
			<!-- Nav -->
			<nav id="nav">
				<ul>
					<li><a href="#top" id="top-link" class="skel-layers-ignoreHref"><span class="icon fa-home"><?php esc_html_e( 'Intro', 'webbooks' ); ?></span></a></li>
					<li><a href="#portfolio" id="portfolio-link" class="skel-layers-ignoreHref"><span class="icon fa-th"><?php esc_html_e( 'Portfolio', 'webbooks' ); ?></span></a></li>
					<li><a href="#about" id="about-link" class="skel-layers-ignoreHref"><span class="icon fa-user"><?php esc_html_e( 'About me', 'webbooks' ); ?></span></a></li>
					<li><a href="#contact" id="contact-link" class="skel-layers-ignoreHref"><span class="icon fa-envelope"><?php esc_html_e( 'Contact', 'webbooks' ); ?></span></a></li>
					<?php if ( '' !== $author_support_url ) : ?>
						<li>
							<a href="<?php echo esc_url( $author_support_url ); ?>" id="author-support-link" class="portfolio-support-link">
								<span class="icon fa-heart portfolio-support-link__label"><?php esc_html_e( 'Support the project', 'webbooks' ); ?></span>
							</a>
						</li>
					<?php endif; ?>
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
	<main id="main">
		<!-- Intro -->
		<section id="top" class="one dark cover">
			<div class="container">
				<div class="portfolio-hero__content">
					<header>
						<p class="portfolio-eyebrow"><?php esc_html_e( 'Resume', 'webbooks' ); ?></p>
						<h1><?php esc_html_e( 'I am a Full Stack developer.', 'webbooks' ); ?></h1>
						<p class="portfolio-hero__lead"><?php esc_html_e( 'I write code with:', 'webbooks' ); ?></p>
						<ul class="portfolio-stack">
							<li>WordPress + WooCommerce</li>
							<li>Laravel</li>
							<li>Symfony</li>
							<li>React</li>
							<li>Docker</li>
							<li>Redis</li>
							<li>RabbitMQ</li>
							<li>MySQL</li>
							<li>Next.js</li>
							<li><?php esc_html_e( 'Custom PHP applications', 'webbooks' ); ?></li>
						</ul>
					</header>
					<footer class="portfolio-hero__actions">
						<a href="#portfolio" class="button scrolly"><?php esc_html_e( 'My work', 'webbooks' ); ?></a>
						<a href="#contact" class="portfolio-text-link scrolly"><?php esc_html_e( 'Contact', 'webbooks' ); ?><span aria-hidden="true"> →</span></a>
					</footer>
				</div>
			</div>
		</section>
		<!-- Portfolio -->
		<section id="portfolio" class="two">
			<div class="container">
				<header>
					<h2><?php esc_html_e( 'Portfolio', 'webbooks' ); ?></h2>
				</header>
				<div class="portfolio-projects">
					<article class="item portfolio-project">
						<a href="https://webbooks.com.ua/webbooks-com-ua/" class="portfolio-project__link">
							<span class="portfolio-project__image image fit"><img src="https://webbooks.com.ua/wp-content/uploads/2016/03/shot-20160316-1941-1nkv3se.png" loading="lazy" alt="<?php echo esc_attr__( 'A book site for developers', 'webbooks' ); ?>" /></span>
							<span class="portfolio-project__content"><span class="portfolio-project__number" aria-hidden="true">01</span><span class="portfolio-project__tags">WordPress · PHP · SEO</span><span class="portfolio-project__title"><?php esc_html_e( 'A book site for developers', 'webbooks' ); ?></span><span class="portfolio-project__arrow" aria-hidden="true">↗</span></span>
						</a>
					</article>
					<article class="item portfolio-project">
						<a href="https://webbooks.com.ua/vinkniga-com-ua/" class="portfolio-project__link">
							<span class="portfolio-project__image image fit"><img src="https://webbooks.com.ua/wp-content/uploads/2016/03/shot-20160316-1941-6xwfzp.png" loading="lazy" alt="<?php echo esc_attr__( 'An online store for used books', 'webbooks' ); ?>" /></span>
							<span class="portfolio-project__content"><span class="portfolio-project__number" aria-hidden="true">02</span><span class="portfolio-project__tags">WooCommerce · PHP · Search</span><span class="portfolio-project__title"><?php esc_html_e( 'An online store for used books', 'webbooks' ); ?></span><span class="portfolio-project__arrow" aria-hidden="true">↗</span></span>
						</a>
					</article>
					<article class="item portfolio-project">
						<a href="https://webbooks.com.ua/pozhelaju-ru/" class="portfolio-project__link">
							<span class="portfolio-project__image image fit"><img src="https://webbooks.com.ua/wp-content/uploads/2016/03/shot-20160316-1941-c03ztf.jpeg" loading="lazy" alt="<?php echo esc_attr__( 'Greetings and wishes for every occasion — pozhelaju.ru', 'webbooks' ); ?>" /></span>
							<span class="portfolio-project__content"><span class="portfolio-project__number" aria-hidden="true">03</span><span class="portfolio-project__tags">WordPress · Content · Performance</span><span class="portfolio-project__title"><?php esc_html_e( 'Greetings and wishes for every occasion — pozhelaju.ru', 'webbooks' ); ?></span><span class="portfolio-project__arrow" aria-hidden="true">↗</span></span>
						</a>
					</article>
					<article class="item portfolio-project">
						<a href="https://webbooks.com.ua/pan-sirko-com-ua/" class="portfolio-project__link">
							<span class="portfolio-project__image image fit"><img src="https://webbooks.com.ua/wp-content/uploads/2016/03/shot-20160316-1941-1jtpr72.png" loading="lazy" alt="<?php echo esc_attr__( 'The official website of the PAN SIRKO brand', 'webbooks' ); ?>" /></span>
							<span class="portfolio-project__content"><span class="portfolio-project__number" aria-hidden="true">04</span><span class="portfolio-project__tags">WordPress · E-commerce · UI</span><span class="portfolio-project__title"><?php esc_html_e( 'The official website of the PAN SIRKO brand', 'webbooks' ); ?></span><span class="portfolio-project__arrow" aria-hidden="true">↗</span></span>
						</a>
					</article>
					<article class="item portfolio-project portfolio-project--resource">
						<a href="https://falcor.com.ua/" class="portfolio-project__link" target="_blank" rel="noopener noreferrer">
							<span class="portfolio-project__content"><span class="portfolio-project__number" aria-hidden="true">05</span><span class="portfolio-project__tags">Laravel · MySQL · Redis</span><span class="portfolio-project__title">falcor.com.ua</span><span class="portfolio-project__arrow" aria-hidden="true">↗</span></span>
						</a>
					</article>
					<article class="item portfolio-project portfolio-project--resource portfolio-project--resource-salo">
						<a href="https://salo.in.ua/" class="portfolio-project__link" target="_blank" rel="noopener noreferrer">
							<span class="portfolio-project__content"><span class="portfolio-project__number" aria-hidden="true">06</span><span class="portfolio-project__tags">Next.js · Docker · RabbitMQ</span><span class="portfolio-project__title">salo.in.ua</span><span class="portfolio-project__arrow" aria-hidden="true">↗</span></span>
						</a>
					</article>
				</div>
			</div>
		</section>
			<!-- About Me -->
		<section id="about" class="three">
			<div class="container">
				<header>
					<h2><?php esc_html_e( 'About me', 'webbooks' ); ?></h2>
				</header>
				<div class="portfolio-about">
					<figure class="image featured portfolio-about__image">
						<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/portfolio/pic08.jpg' ); ?>" loading="lazy" alt="sublime-text-code">
					</figure>
					<div class="portfolio-about__copy">
						<p>
							<?php esc_html_e( 'I have more than five years of experience in the Ukrainian PHP development market. I have worked with WordPress, Laravel, and custom PHP applications.', 'webbooks' ); ?>
							<?php esc_html_e( 'My work includes configuring search, optimizing database queries, setting up Redis caching, and more.', 'webbooks' ); ?>
							<?php esc_html_e( 'I have independently led projects from agreeing a plan with the business, through creating Jira tasks, to delivering them using an MVP approach.', 'webbooks' ); ?>
							<?php esc_html_e( 'I have also worked in Scrum teams.', 'webbooks' ); ?>
						</p>
						<ul class="portfolio-about__skills">
							<li>PHP 8+</li><li>WordPress</li><li>Laravel</li><li>Symfony</li><li>React</li><li>Docker</li><li>Redis</li><li>RabbitMQ</li><li>MySQL</li><li>Next.js</li>
						</ul>
					</div>
				</div>
			</div>
		</section>
		<!-- Contact -->
		<section id="contact" class="four">
			<div class="container">
				<header>
					<h2><?php esc_html_e( 'Contact', 'webbooks' ); ?></h2>
				</header>
				<div class="portfolio-contact-action">
					<button
						type="button"
						class="button portfolio-telegram-contact"
						data-webbooks-telegram
					>
						<span class="icon fa-telegram" aria-hidden="true"></span>
						<span><?php esc_html_e( 'Contact on Telegram', 'webbooks' ); ?></span>
					</button>
				</div>
			</div>
		</section>
	</main>
<?php
get_footer( 'portfolio' );
