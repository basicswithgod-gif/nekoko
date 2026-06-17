<?php
defined( 'ABSPATH' ) || exit;

/**
 * [nekoko_booking_form job_id=X] - 2-step booking request flow (fill -> review -> confirm).
 */
class Nekoko_Shortcode_Booking_Form {

	public static function init() {
		add_shortcode( 'nekoko_booking_form', [ __CLASS__, 'render' ] );
		add_action( 'wp_enqueue_scripts', [ __CLASS__, 'enqueue' ] );
	}

	public static function enqueue() {
		wp_register_script( 'nekoko-booking-form', NEKOKO_URL . 'assets/js/booking-form.js', [], NEKOKO_VERSION, true );
	}

	public static function render( $atts ) {
		$atts = shortcode_atts( [ 'job_id' => 0, 'listing_id' => 0 ], $atts );
		$job  = get_post( intval( $atts['job_id'] ?: $atts['listing_id'] ) ?: get_the_ID() );

		if ( ! $job || $job->post_type !== Nekoko_CPT::POST_TYPE ) {
			return '';
		}

		wp_enqueue_script( 'nekoko-booking-form' );

		$provider = get_user_by( 'id', $job->post_author );
		$job_id   = $job->ID;

		if ( isset( $_POST['nekoko_booking_submit'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'nekoko_booking_' . $job_id ) ) {
			$name    = sanitize_text_field( wp_unslash( $_POST['nekoko_name'] ?? '' ) );
			$email   = sanitize_email( wp_unslash( $_POST['nekoko_email'] ?? '' ) );
			$message = sanitize_textarea_field( wp_unslash( $_POST['nekoko_message'] ?? '' ) );
			$date    = sanitize_text_field( wp_unslash( $_POST['nekoko_date'] ?? '' ) );

			if ( $name && $email && $message ) {
				Nekoko_Emails::send_booking_emails( $job, $name, $email, $message, $date );
				ob_start();
				include NEKOKO_PATH . 'templates/shortcodes/booking-form-success.php';
				return ob_get_clean();
			}
		}

		ob_start();
		include NEKOKO_PATH . 'templates/shortcodes/booking-form.php';
		return ob_get_clean();
	}
}
