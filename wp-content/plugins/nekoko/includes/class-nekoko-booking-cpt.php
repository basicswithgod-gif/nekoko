<?php
defined( 'ABSPATH' ) || exit;

/**
 * Booking CPT — stores each booking request submitted via [nekoko_booking_form].
 * Consistent with the Jobs CPT pattern already in this codebase.
 *
 * Meta fields:
 *   _booking_job_id         (int)
 *   _booking_provider_id    (int)
 *   _booking_customer_name  (string)
 *   _booking_customer_email (string)
 *   _booking_date           (string Y-m-d)
 *   _booking_message        (string)
 *   _booking_status         (string: pending | confirmed | completed)
 *   _booking_created        (string mysql datetime)
 */
class Nekoko_Booking_CPT {

	const POST_TYPE = 'nekoko_booking';

	public static function init() {
		add_action( 'init', [ __CLASS__, 'register' ] );
	}

	public static function register() {
		register_post_type(
			self::POST_TYPE,
			[
				'labels'          => [
					'name'          => 'Bookings',
					'singular_name' => 'Booking',
					'menu_name'     => 'Bookings',
					'all_items'     => 'All Bookings',
					'view_item'     => 'View Booking',
					'search_items'  => 'Search Bookings',
					'not_found'     => 'No bookings found',
				],
				'public'          => false,
				'show_ui'         => true,
				'show_in_menu'    => true,
				'supports'        => [ 'title', 'custom-fields' ],
				'show_in_rest'    => false,
				'menu_icon'       => 'dashicons-calendar-alt',
				'capability_type' => 'post',
				'map_meta_cap'    => true,
			]
		);
	}

	/**
	 * Persist a booking request. Returns new post ID or WP_Error.
	 *
	 * @param WP_Post $job
	 * @param string  $customer_name
	 * @param string  $customer_email
	 * @param string  $message
	 * @param string  $date  Y-m-d or empty string
	 * @return int|WP_Error
	 */
	public static function save( $job, $customer_name, $customer_email, $message, $date ) {
		$post_id = wp_insert_post(
			[
				'post_type'   => self::POST_TYPE,
				'post_title'  => sprintf( 'Booking — %s', sanitize_text_field( $job->post_title ) ),
				'post_status' => 'publish',
				'post_author' => get_current_user_id(),
			],
			true
		);

		if ( is_wp_error( $post_id ) ) {
			return $post_id;
		}

		update_post_meta( $post_id, '_booking_job_id',         $job->ID );
		update_post_meta( $post_id, '_booking_provider_id',    (int) $job->post_author );
		update_post_meta( $post_id, '_booking_customer_name',  $customer_name );
		update_post_meta( $post_id, '_booking_customer_email', $customer_email );
		update_post_meta( $post_id, '_booking_date',           $date );
		update_post_meta( $post_id, '_booking_message',        $message );
		update_post_meta( $post_id, '_booking_status',         'pending' );
		update_post_meta( $post_id, '_booking_created',        current_time( 'mysql' ) );

		return $post_id;
	}

	/**
	 * Upcoming bookings for a provider: status pending or confirmed.
	 *
	 * @param int $provider_id
	 * @return WP_Post[]
	 */
	public static function get_upcoming( $provider_id ) {
		return get_posts(
			[
				'post_type'   => self::POST_TYPE,
				'post_status' => 'publish',
				'numberposts' => -1,
				'meta_query'  => [
					'relation' => 'AND',
					[
						'key'     => '_booking_provider_id',
						'value'   => (int) $provider_id,
						'type'    => 'NUMERIC',
						'compare' => '=',
					],
					[
						'key'     => '_booking_status',
						'value'   => [ 'pending', 'confirmed' ],
						'compare' => 'IN',
					],
				],
				'meta_key'    => '_booking_date',
				'orderby'     => 'meta_value',
				'order'       => 'ASC',
			]
		);
	}

	/**
	 * Completed bookings for a provider: status completed.
	 *
	 * @param int $provider_id
	 * @return WP_Post[]
	 */
	public static function get_completed( $provider_id ) {
		return get_posts(
			[
				'post_type'   => self::POST_TYPE,
				'post_status' => 'publish',
				'numberposts' => -1,
				'meta_query'  => [
					'relation' => 'AND',
					[
						'key'     => '_booking_provider_id',
						'value'   => (int) $provider_id,
						'type'    => 'NUMERIC',
						'compare' => '=',
					],
					[
						'key'     => '_booking_status',
						'value'   => 'completed',
						'compare' => '=',
					],
				],
				'orderby' => 'date',
				'order'   => 'DESC',
			]
		);
	}
}
