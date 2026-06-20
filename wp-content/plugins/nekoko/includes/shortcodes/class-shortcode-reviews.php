<?php
defined( 'ABSPATH' ) || exit;

/**
 * [nekoko_reviews job_id=X] - review display and token-gated submission.
 *
 * Reviews are stored as nekoko_review CPT posts (not postmeta).
 * Submission requires a valid review token from $_GET['review_token'],
 * which is delivered via the 5-day post-booking email (Nekoko_Review_Token).
 * No login required.
 */
class Nekoko_Shortcode_Reviews {

	public static function init() {
		add_shortcode( 'nekoko_reviews', [ __CLASS__, 'render' ] );
	}

	public static function render( $atts ) {
		$atts   = shortcode_atts( [ 'job_id' => 0, 'listing_id' => 0 ], $atts );
		$job_id = intval( $atts['job_id'] ?: $atts['listing_id'] ) ?: get_the_ID();
		$job    = get_post( $job_id );

		if ( ! $job || $job->post_type !== Nekoko_CPT::POST_TYPE ) {
			return '';
		}

		$token      = sanitize_text_field( wp_unslash( $_GET['review_token'] ?? '' ) );
		$message    = '';
		$can_review = false;
		$booking_id = 0;

		if ( $token ) {
			$validation = Nekoko_Review_Token::validate( $token );
			if ( is_wp_error( $validation ) ) {
				$message = $validation->get_error_message();
			} else {
				$booking_id = (int) $validation;
				$can_review = true;
			}
		}

		if ( $can_review
			&& isset( $_POST['nekoko_review_submit'] )
			&& wp_verify_nonce(
				sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ?? '' ) ),
				'nekoko_review_' . $job_id . '_' . $booking_id
			)
		) {
			$result = self::save_review( $job, $booking_id );
			if ( is_wp_error( $result ) ) {
				$message = $result->get_error_message();
			} else {
				Nekoko_Review_Token::mark_used( $booking_id );
				$can_review = false;
				$message    = 'Hvala! Vaša recenzija je objavljena.';
			}
		}

		$reviews = Nekoko_Review_CPT::get_for_job( $job_id );
		$average = (float) get_post_meta( $job_id, '_nekoko_avg_rating', true );

		ob_start();
		include NEKOKO_PATH . 'templates/shortcodes/reviews.php';
		return ob_get_clean();
	}

	private static function save_review( $job, $booking_id ) {
		$rating        = min( 5, max( 1, intval( wp_unslash( $_POST['nekoko_rating'] ?? 5 ) ) ) );
		$comment       = sanitize_textarea_field( wp_unslash( $_POST['nekoko_review_text'] ?? '' ) );
		$reviewer_name = get_post_meta( $booking_id, '_booking_customer_name', true ) ?: 'Anonimni korisnik';

		return Nekoko_Review_CPT::save(
			(int) $job->post_author,
			$job->ID,
			$booking_id,
			$reviewer_name,
			$rating,
			$comment
		);
	}
}
