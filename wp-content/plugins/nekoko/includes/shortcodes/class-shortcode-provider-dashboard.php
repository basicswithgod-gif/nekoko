<?php
defined( 'ABSPATH' ) || exit;

/**
 * [nekoko_provider_dashboard] - provider's own jobs list with status badges.
 */
class Nekoko_Shortcode_Provider_Dashboard {

	public static function init() {
		add_shortcode( 'nekoko_provider_dashboard', [ __CLASS__, 'render' ] );
	}

	public static function render() {
		if ( ! is_user_logged_in() ) {
			return '<p><a href="' . esc_url( wp_login_url( get_permalink() ) ) . '">Prijavi se</a> da bi pristupio/la dashboardu.</p>';
		}

		$user = wp_get_current_user();
		if ( ! in_array( 'provider', (array) $user->roles, true ) ) {
			return '<p>Ova stranica je dostupna samo pružaocima usluga.</p>';
		}

		$jobs = get_posts(
			[
				'post_type'   => Nekoko_CPT::POST_TYPE,
				'author'      => $user->ID,
				'post_status' => [ 'publish', 'pending', 'draft' ],
				'numberposts' => -1,
			]
		);

		$upcoming_bookings  = Nekoko_Booking_CPT::get_upcoming( $user->ID );
		$completed_bookings = Nekoko_Booking_CPT::get_completed( $user->ID );
		$profile_url        = get_edit_user_link( $user->ID );

		ob_start();
		include NEKOKO_PATH . 'templates/shortcodes/provider-dashboard.php';
		return ob_get_clean();
	}
}
