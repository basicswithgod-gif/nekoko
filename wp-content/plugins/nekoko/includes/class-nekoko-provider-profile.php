<?php
defined( 'ABSPATH' ) || exit;

/**
 * Provider profile URL routing.
 *
 * Registers /provajder/{user_nicename}/ rewrite rule and serves the profile
 * template from the plugin's templates directory.
 */
class Nekoko_Provider_Profile {

	public static function init() {
		add_action( 'init',             [ __CLASS__, 'add_rewrite_rule' ] );
		add_filter( 'query_vars',       [ __CLASS__, 'register_query_var' ] );
		add_filter( 'template_include', [ __CLASS__, 'maybe_load_template' ] );
	}

	public static function add_rewrite_rule() {
		add_rewrite_rule(
			'^provajder/([^/]+)/?$',
			'index.php?nekoko_provider_slug=$matches[1]',
			'top'
		);
	}

	public static function register_query_var( $vars ) {
		$vars[] = 'nekoko_provider_slug';
		return $vars;
	}

	public static function maybe_load_template( $template ) {
		if ( get_query_var( 'nekoko_provider_slug' ) ) {
			return NEKOKO_PATH . 'templates/provider-profile.php';
		}
		return $template;
	}
}
