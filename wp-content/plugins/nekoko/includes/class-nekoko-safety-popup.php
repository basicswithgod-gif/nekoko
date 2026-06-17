<?php
defined( 'ABSPATH' ) || exit;

/**
 * Safety warning popup shown to logged-in users every 90 days.
 */
class Nekoko_Safety_Popup {

	const META_LAST_SHOWN = '_nekoko_safety_last_shown';
	const META_ACK         = '_nekoko_safety_ack';

	public static function init() {
		add_action( 'wp_footer', [ __CLASS__, 'render' ] );
		add_action( 'wp_enqueue_scripts', [ __CLASS__, 'enqueue' ] );
		add_action( 'wp_ajax_nekoko_safety_ack', [ __CLASS__, 'handle_ack' ] );
	}

	private static function should_show() {
		if ( ! is_user_logged_in() ) {
			return false;
		}
		$last = (int) get_user_meta( get_current_user_id(), self::META_LAST_SHOWN, true );
		return ! ( $last && ( time() - $last ) < 90 * DAY_IN_SECONDS );
	}

	public static function enqueue() {
		if ( ! self::should_show() ) {
			return;
		}
		wp_enqueue_script( 'nekoko-safety-popup', NEKOKO_URL . 'assets/js/safety-popup.js', [], NEKOKO_VERSION, true );
		wp_localize_script(
			'nekoko-safety-popup',
			'nekokoSafetyData',
			[
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'nekoko_safety' ),
			]
		);
	}

	public static function render() {
		if ( ! self::should_show() ) {
			return;
		}
		$kontakt_url = home_url( '/kontakt/' );
		include NEKOKO_PATH . 'templates/shortcodes/safety-popup.php';
	}

	public static function handle_ack() {
		check_ajax_referer( 'nekoko_safety', 'nonce' );
		update_user_meta( get_current_user_id(), self::META_LAST_SHOWN, time() );
		update_user_meta( get_current_user_id(), self::META_ACK, 1 );
		wp_die( 'ok' );
	}
}
