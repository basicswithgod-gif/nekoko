<?php
defined( 'ABSPATH' ) || exit;

/**
 * [nekoko_reviews job_id=X] - star ratings + review submission for a job.
 */
class Nekoko_Shortcode_Reviews {

	public static function init() {
		add_shortcode( 'nekoko_reviews', [ __CLASS__, 'render' ] );
	}

	public static function render( $atts ) {
		$atts   = shortcode_atts( [ 'job_id' => 0, 'listing_id' => 0 ], $atts );
		$job_id = intval( $atts['job_id'] ?: $atts['listing_id'] ) ?: get_the_ID();

		if ( isset( $_POST['nekoko_review_submit'] )
			&& wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'nekoko_review_' . $job_id )
			&& is_user_logged_in()
		) {
			self::save_review( $job_id );
		}

		$reviews = get_post_meta( $job_id, '_nekoko_reviews', true ) ?: [];
		$average = (float) get_post_meta( $job_id, '_nekoko_avg_rating', true );
		$can_review = is_user_logged_in();

		ob_start();
		include NEKOKO_PATH . 'templates/shortcodes/reviews.php';
		return ob_get_clean();
	}

	private static function save_review( $job_id ) {
		$rating  = min( 5, max( 1, intval( wp_unslash( $_POST['nekoko_rating'] ?? 5 ) ) ) );
		$comment = sanitize_textarea_field( wp_unslash( $_POST['nekoko_review_text'] ?? '' ) );
		$user    = wp_get_current_user();

		$reviews   = get_post_meta( $job_id, '_nekoko_reviews', true ) ?: [];
		$reviews[] = [
			'user_id' => $user->ID,
			'name'    => $user->display_name,
			'rating'  => $rating,
			'comment' => $comment,
			'date'    => current_time( 'mysql' ),
		];

		update_post_meta( $job_id, '_nekoko_reviews', $reviews );
		update_post_meta( $job_id, '_nekoko_avg_rating', round( array_sum( array_column( $reviews, 'rating' ) ) / count( $reviews ), 1 ) );
		update_post_meta( $job_id, '_nekoko_review_count', count( $reviews ) );
	}
}
