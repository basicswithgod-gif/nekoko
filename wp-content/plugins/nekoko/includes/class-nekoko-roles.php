<?php
defined( 'ABSPATH' ) || exit;

/**
 * Registers the custom "provider" and "customer" roles.
 */
class Nekoko_Roles {

	public static function init() {
		add_action( 'init', [ __CLASS__, 'register' ] );
	}

	public static function register() {
		if ( ! get_role( 'provider' ) ) {
			add_role(
				'provider',
				'Provider',
				[
					'read'         => true,
					'edit_posts'   => true,
					'publish_posts' => true,
					'delete_posts' => true,
					'upload_files' => true,
				]
			);
		}

		if ( ! get_role( 'customer' ) ) {
			add_role( 'customer', 'Customer', [ 'read' => true ] );
		}
	}
}
