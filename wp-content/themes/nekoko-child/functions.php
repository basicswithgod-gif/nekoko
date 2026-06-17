<?php
defined( 'ABSPATH' ) || exit;

/**
 * NekoKo child theme - asset enqueuing only.
 * All business logic (CPT, taxonomies, shortcodes, blocks, roles, email, moderation)
 * lives in the "nekoko" plugin. This file's only job is CSS/JS + nav menus.
 */

add_action( 'wp_enqueue_scripts', 'nekoko_child_enqueue_assets' );
function nekoko_child_enqueue_assets() {
	wp_enqueue_style( 'hello-elementor-parent', get_template_directory_uri() . '/style.css', [], wp_get_theme( 'hello-elementor' )->get( 'Version' ) );

	wp_enqueue_style(
		'nekoko-fonts',
		'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Crimson+Pro:wght@600;700;800&display=swap',
		[],
		null
	);

	$css_path = get_stylesheet_directory() . '/assets/css/style.min.css';
	wp_enqueue_style(
		'nekoko-child',
		get_stylesheet_directory_uri() . '/assets/css/style.min.css',
		[ 'hello-elementor-parent' ],
		file_exists( $css_path ) ? filemtime( $css_path ) : wp_get_theme()->get( 'Version' )
	);

	$js_path = get_stylesheet_directory() . '/assets/js/main.js';
	if ( file_exists( $js_path ) ) {
		wp_enqueue_script( 'nekoko-main', get_stylesheet_directory_uri() . '/assets/js/main.js', [], filemtime( $js_path ), true );
	}
}

add_action( 'after_setup_theme', 'nekoko_child_register_menus' );
function nekoko_child_register_menus() {
	register_nav_menus(
		[
			'primary' => 'Primary Navigation',
			'footer'  => 'Footer Links',
		]
	);
}

add_action( 'after_setup_theme', 'nekoko_child_theme_supports' );
function nekoko_child_theme_supports() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
}
