<?php
defined( 'ABSPATH' ) || exit;

/**
 * Booking-gated review token system.
 *
 * On each new booking a 32-char random token is generated and a WP cron event
 * is scheduled for 5 days later. The cron callback emails the customer a
 * one-time review link: /jobs/{slug}/?review_token=TOKEN
 *
 * Token expires 30 days after generation. One use only.
 *
 * Booking meta added by this class:
 *   _booking_review_token         (string) random alphanumeric token
 *   _booking_review_token_expires (int)    unix timestamp
 *   _booking_review_token_used    (int)    0 | 1
 */
class Nekoko_Review_Token {

	const CRON_EVENT   = 'nekoko_send_review_request';
	const TOKEN_META   = '_booking_review_token';
	const USED_META    = '_booking_review_token_used';
	const EXPIRES_META = '_booking_review_token_expires';
	const EXPIRY       = 30 * DAY_IN_SECONDS;

	public static function init() {
		add_action( 'save_post_' . Nekoko_Booking_CPT::POST_TYPE, [ __CLASS__, 'on_booking_saved' ], 10, 3 );
		add_action( self::CRON_EVENT, [ __CLASS__, 'send_review_email' ] );
	}

	/**
	 * Fires on save_post_nekoko_booking. For new posts only: generates token and
	 * schedules the 5-day review-request email.
	 */
	public static function on_booking_saved( $post_id, $post, $update ) {
		if ( $update ) {
			return;
		}
		if ( get_post_meta( $post_id, self::TOKEN_META, true ) ) {
			return;
		}

		$token = wp_generate_password( 32, false, false );

		update_post_meta( $post_id, self::TOKEN_META,   $token );
		update_post_meta( $post_id, self::EXPIRES_META, time() + self::EXPIRY );
		update_post_meta( $post_id, self::USED_META,    0 );

		wp_schedule_single_event(
			time() + ( 5 * DAY_IN_SECONDS ),
			self::CRON_EVENT,
			[ $post_id ]
		);
	}

	/**
	 * Cron callback — sends review request email 5 days after booking.
	 *
	 * @param int $booking_id
	 */
	public static function send_review_email( $booking_id ) {
		$booking_id = (int) $booking_id;
		$token      = get_post_meta( $booking_id, self::TOKEN_META, true );
		$used       = (int) get_post_meta( $booking_id, self::USED_META, true );
		$email      = get_post_meta( $booking_id, '_booking_customer_email', true );
		$job_id     = (int) get_post_meta( $booking_id, '_booking_job_id', true );
		$customer   = get_post_meta( $booking_id, '_booking_customer_name', true );

		if ( ! $token || $used || ! $email || ! $job_id ) {
			return;
		}

		$job = get_post( $job_id );
		if ( ! $job ) {
			return;
		}

		$review_url = add_query_arg( 'review_token', $token, get_permalink( $job_id ) );

		Nekoko_Emails::send_review_request( $email, $customer, $job->post_title, $review_url );
	}

	/**
	 * Validate a review token.
	 * Returns the booking post ID on success, or WP_Error.
	 *
	 * Checks: token exists, not used, not expired, booking status = completed.
	 *
	 * @param string $token Raw token from $_GET.
	 * @return int|WP_Error Booking post ID if valid.
	 */
	public static function validate( $token ) {
		$token = sanitize_text_field( $token );

		if ( ! $token ) {
			return new WP_Error( 'no_token', 'Token nije naveden.' );
		}

		$bookings = get_posts(
			[
				'post_type'   => Nekoko_Booking_CPT::POST_TYPE,
				'post_status' => 'publish',
				'numberposts' => 1,
				'meta_query'  => [
					[
						'key'     => self::TOKEN_META,
						'value'   => $token,
						'compare' => '=',
					],
				],
			]
		);

		if ( empty( $bookings ) ) {
			return new WP_Error( 'invalid_token', 'Link za recenziju nije validan.' );
		}

		$booking_id = $bookings[0]->ID;
		$used       = (int) get_post_meta( $booking_id, self::USED_META, true );
		$expires    = (int) get_post_meta( $booking_id, self::EXPIRES_META, true );
		$status     = get_post_meta( $booking_id, '_booking_status', true );

		if ( $used ) {
			return new WP_Error( 'token_used', 'Recenzija za ovu rezervaciju je već ostavljena.' );
		}

		if ( $expires && time() > $expires ) {
			return new WP_Error( 'token_expired', 'Link za recenziju je istekao.' );
		}

		if ( $status !== 'completed' ) {
			return new WP_Error( 'not_completed', 'Recenzija se može ostaviti samo za završene rezervacije.' );
		}

		return $booking_id;
	}

	/**
	 * Mark token as used. Call immediately after a review is successfully saved.
	 *
	 * @param int $booking_id
	 */
	public static function mark_used( $booking_id ) {
		update_post_meta( (int) $booking_id, self::USED_META, 1 );
	}
}
