<?php
defined( 'ABSPATH' ) || exit;

/**
 * Registers the "Jobs" custom post type.
 * Replaces the previous "service_listing" CPT (US 4.3 rebuild after design review).
 */
class Nekoko_CPT {

	const POST_TYPE = 'job';

	public static function init() {
		add_action( 'init', [ __CLASS__, 'register' ] );
		add_filter( 'wp_insert_post_data', [ __CLASS__, 'force_pending_for_non_admins' ], 10, 2 );
	}

	public static function register() {
		register_post_type(
			self::POST_TYPE,
			[
				'labels'        => [
					'name'          => 'Jobs',
					'singular_name' => 'Job',
					'add_new'       => 'Add New',
					'add_new_item'  => 'Add New Job',
					'edit_item'     => 'Edit Job',
					'view_item'     => 'View Job',
					'search_items'  => 'Search Jobs',
					'not_found'     => 'No jobs found',
					'menu_name'     => 'Jobs',
				],
				'public'        => true,
				'has_archive'   => true,
				'rewrite'       => [ 'slug' => 'jobs' ],
				'supports'      => [ 'title', 'editor', 'thumbnail', 'author', 'custom-fields' ],
				'show_in_rest'  => true,
				'menu_icon'     => 'dashicons-portfolio',
				'capability_type' => 'post',
				'map_meta_cap'  => true,
			]
		);
	}

	/**
	 * Non-admin authored jobs are forced to "pending" so they go through moderation.
	 */
	public static function force_pending_for_non_admins( $data, $postarr ) {
		if ( $data['post_type'] === self::POST_TYPE && ! current_user_can( 'manage_options' ) ) {
			if ( in_array( $data['post_status'], [ 'publish', 'future' ], true ) ) {
				$data['post_status'] = 'pending';
			}
		}
		return $data;
	}
}
