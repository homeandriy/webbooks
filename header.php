<?php
/**
* Шаблон шапки (header.php)
* @package WordPress
* @subpackage webbooks
*/
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
		<meta charset="UTF-8">
		<meta name="theme-color" content="#434350">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<?php wp_head(); ?>
	</head>
	<body <?php body_class(); ?> itemscope itemtype="https://schema.org/WebPage">
		<?php wp_body_open(); ?>
		<header class="header" itemscope itemtype="https://schema.org/WPHeader">
			<a href="<?php echo esc_url( \Webbooks\Localization\Polylang::homeUrl() ); ?>" itemprop="headline"  class="logo"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></a>
			<!-- Start Main Navigation -->
			<nav class="navbar " role="navigation">
				<!-- Sidebar toggle button-->
				<a href="#webbooks-sidebar" class="header-button sidebar-toggle d-lg-none" data-bs-toggle="offcanvas" aria-controls="webbooks-sidebar" role="button">
					<span class="visually-hidden"><?php esc_html_e( 'Toggle navigation', 'webbooks' ); ?></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</a>
				<a
					href="#mobile-search-modal"
					class="header-button d-lg-none"
					data-bs-toggle="modal"
					aria-label="<?php esc_attr_e( 'Search', 'webbooks' ); ?>"
				>
					<i class="fa fa-search" aria-hidden="true"></i>
				</a>
				<?php get_template_part( 'template/partials/language-switcher-mobile' ); ?>
				<!-- Start Navbar-Left - Includes Search Form -->
				<?php get_template_part( 'template-parts/forms/main-search' ); ?>
				<!-- /. Navbar-Left -->
				<!-- Start Navbar-Right - Includes Nav Links -->
				<div class="header-actions">
					<?php if ( has_nav_menu( 'top' ) ) : ?>
					<div class="navbar-menu d-none d-xl-block">
						<?php wp_nav_menu( array( 'theme_location' => 'top', 'container' => false, 'menu_class' => 'nav flex-row webbooks-top-menu', 'menu_id' => 'webbooks-top-menu', 'depth' => 2, 'fallback_cb' => false ) ); ?>
					</div>
					<?php endif; ?>
					<ul class="nav navbar-nav flex-row">
						<!-- Register -->
						<?php if ( ! is_user_logged_in() ) : ?>
							<li class="dropdow messages-menu d-none d-md-block"></li>
						<?php else : ?>
							<li class="dropdow messages-menu d-none d-md-block">
								<a href=""><?php esc_html_e( 'You are logged in as:', 'webbooks' ); ?></a>
							</li>
						<?php endif; ?>
						<?php if ( is_user_logged_in() ) : ?>
								<li class="dropdown messages-menu d-none d-md-block">
									<a id="user-menu-toggle" class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
										<?php
											global $current_user;
											echo esc_html( $current_user->user_login );
										?>
									</a>
									<ul class="dropdown-menu" aria-labelledby="user-menu-toggle">
										<li><a class="dropdown-item" href=""><?php esc_html_e( 'Info', 'webbooks' ); ?></a></li>
										<li><a class="dropdown-item" href="<?php echo esc_url( wp_logout_url( \Webbooks\Localization\Polylang::homeUrl() ) ); ?>" title="<?php esc_attr_e( 'Logout', 'webbooks' ); ?>"><?php esc_html_e( 'Logout', 'webbooks' ); ?></a></li>
										</ul>
								</li>
						<?php endif; ?>
					</ul>
				</div>
				<?php get_template_part( 'template/partials/language-switcher' ); ?>
				<?php $support_url = \Webbooks\Support\AuthorSupport::publishedPageUrl(); ?>
                <?php if ( $support_url !== '' ) : ?>
                <a class="btn btn-support d-none d-lg-inline-flex" href="<?php echo esc_url( $support_url ); ?>" aria-label="<?php esc_attr_e( 'Support the project', 'webbooks' ); ?>"><i class="fa fa-heart" aria-hidden="true"></i></a>
                <?php endif; ?>
                <button type="button" id="write" class="btn header-button btn-info d-none d-lg-inline-flex header-actions" data-webbooks-telegram><i class="fa fa-telegram" aria-hidden="true"></i><?php esc_html_e( 'Contact us', 'webbooks' ); ?></button>
				<!-- End Navbar-Right  -->
			</nav>
			<!-- /. Main Navigation -->
		</header>
		<?php get_template_part( 'template-parts/forms/mobile-search-modal' ); ?>
		<!-- /. Header Section  -->
		<!-- Start Left Content Section - Includes Offcanvas Menu, Search Form & Sidenav -->
		<div class="wrapper" >
		<!-- sidebar -->
