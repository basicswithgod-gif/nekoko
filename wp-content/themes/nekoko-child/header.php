<?php
defined( 'ABSPATH' ) || exit;
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header id="nekoko-site-header" class="nekoko-site-header">
	<div class="nekoko-site-header__inner">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="nekoko-site-header__logo">
			<img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/images/profile-nekoko.png' ); ?>"
				alt="NekoKo.rs" height="52" width="52">
		</a>
		<button class="nekoko-site-header__hamburger"
			aria-expanded="false"
			aria-controls="nekoko-primary-nav"
			aria-label="<?php esc_attr_e( 'Toggle navigation', 'nekoko-child' ); ?>">
			<span class="nekoko-site-header__bar"></span>
			<span class="nekoko-site-header__bar"></span>
			<span class="nekoko-site-header__bar"></span>
		</button>
		<?php if ( has_nav_menu( 'primary' ) ) : ?>
			<nav id="nekoko-primary-nav" class="nekoko-site-header__nav" aria-label="<?php esc_attr_e( 'Primary Navigation', 'nekoko-child' ); ?>">
				<?php
				wp_nav_menu(
					[
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => 'nekoko-site-header__links',
						'depth'          => 1,
					]
				);
				?>
			</nav>
		<?php endif; ?>

		<div class="nekoko-site-header__actions">
			<a href="<?php echo esc_url( home_url( '/prijava/' ) ); ?>"
			   class="nekoko-btn nekoko-btn--outline-white">Prijava</a>
			<a href="<?php echo esc_url( home_url( '/postavi-oglas/' ) ); ?>"
			   class="nekoko-btn nekoko-btn--accent">Postavite oglas</a>
		</div>

	</div>
</header>
