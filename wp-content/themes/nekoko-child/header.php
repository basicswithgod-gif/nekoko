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
			Neko<span>Ko</span>.rs
		</a>
		<?php if ( has_nav_menu( 'primary' ) ) : ?>
			<nav class="nekoko-site-header__nav" aria-label="<?php esc_attr_e( 'Primary Navigation', 'nekoko-child' ); ?>">
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
	</div>
</header>
