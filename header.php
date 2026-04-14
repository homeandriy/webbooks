<?php
/**
* Шаблон шапки (header.php)
* @package WordPress
* @subpackage webbooks
*/
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>  style="overflow-x: visible!important;">
	<head>
		<?php
		$content_language = function_exists( 'pll_current_language' ) ? pll_current_language( 'slug' ) : strtolower( str_replace( '_', '-', get_locale() ) );
		?>
		<meta charset="UTF-8">
		<meta name="google-site-verification" content="moLqYp4Ozcbrt4kxF5K-KDMMd7dh-iJdeyjAj49HnQQ" />		
		<meta name='yandex-verification' content='4daea5f781d2eaa6' />
		<meta name='wmail-verification' content='fb72a559db8e3a6009afff9c71ca8e95' />
		<meta name="msvalidate.01" content="D2011B41C4E32109AA355E08C8E7D298" />
		<meta http-equiv="content-language" content="<?php echo esc_attr( $content_language ); ?>">
		<meta name="theme-color" content="#434350">
		<meta name='viewport' content='width=device-width, initial-scale=1,minimum-scale=1, maximum-scale=1, user-scalable=no'>	
		<?php wp_head(); ?>		
	</head>
	<body <?php body_class(); ?> itemscope itemtype="http://schema.org/WebPage">		
		<header class="header" itemscope itemtype="http://schema.org/WPHeader">
			<a href="<?= esc_url( home_url() );?>" itemprop="headline"  class="logo"><?= esc_html( get_bloginfo( 'name' ) );?></a>
			<!-- Start Main Navigation -->
			<nav class="navbar navbar-static-top" role="navigation">
				<!-- Sidebar toggle button-->
				<a href="#" class="navbar-btn sidebar-toggle hidden-lg hidden-md" data-toggle="offcanvas" role="button">
					<span class="sr-only"><?php esc_html_e( 'Развернуть навигацию', 'webbooks' ); ?></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</a>
				<a
					href="#mobile-search-modal"
					class="navbar-btn hidden-lg hidden-md"
					data-toggle="modal"
					aria-label="<?php esc_attr_e( 'Поиск', 'webbooks' ); ?>"
				>
					<i class="fa fa-search" aria-hidden="true"></i>
				</a>
				<!-- Start Navbar-Left - Includes Search Form -->
				<?php get_template_part( 'template-parts/forms/main-search' ); ?>
				<!-- /. Navbar-Left -->
				<!-- Start Navbar-Right - Includes Nav Links -->
				<div class="navbar-right">
					<ul class="nav navbar-nav">
						<!-- Register -->
						<?php if ( ! is_user_logged_in() ) : ?>
							<li class="dropdow messages-menu hidden-xs"></li>
						<?php else : ?>
							<li class="dropdow messages-menu hidden-xs">
								<a href=""><?php esc_html_e( 'Ви вошли как:', 'webbooks' ); ?></a>
							</li>
						<?php endif; ?>
						<?php if ( is_user_logged_in() ) : ?>
								<li class="dropdown messages-menu hidden-xs">
									<a href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <?php
                                            global $current_user;
                                            echo esc_html( $current_user->user_login );
                                         ?>
                                        <span class="caret"></span>
									</a>
									<ul class="dropdown-menu" aria-labelledby="dLabel">
									    <li><a href=""><?php esc_html_e( 'Инфо', 'webbooks' ); ?></a></li>
									    <li><a href="<?= esc_url( wp_logout_url( home_url() ) ); ?>" title="<?php esc_attr_e( 'Выход', 'webbooks' ); ?>"><?php esc_html_e( 'Выход', 'webbooks' ); ?></a></li>
						   			</ul>
								</li>
						<?php else : ?>
							<li class="dropdown messages-menu hidden-xs">
						<?php endif; ?>						
					</ul>
				</div>
				<?php get_template_part( 'template/partials/language-switcher' ); ?>
				<a id="write" class="btn navbar-btn btn-info hidden-sm hidden-xs navbar-right" href="#"><i class="fa fa-pencil"></i><?php esc_html_e( 'Написать нам', 'webbooks' ); ?></a>
				<!-- End Navbar-Right  -->
			</nav>
			<?php get_template_part( 'template-parts/forms/mobile-search-modal' ); ?>
			<!-- /. Main Navigation -->
		</header>
		<!-- /. Header Section  -->
		<!-- Start Left Content Section - Includes Offcanvas Menu, Search Form & Sidenav -->
		<div class="wrapper row-offcanvas row-offcanvas-left" >
		<!-- sidebar -->
