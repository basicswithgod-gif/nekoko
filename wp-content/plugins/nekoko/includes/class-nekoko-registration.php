<?php
defined( 'ABSPATH' ) || exit;

/**
 * Mandatory Terms + Disclaimer checkboxes on wp-login.php registration,
 * and saving the user's consent timestamps.
 */
class Nekoko_Registration {

	public static function init() {
		add_action( 'register_form', [ __CLASS__, 'render_checkboxes' ] );
		add_filter( 'registration_errors', [ __CLASS__, 'validate_checkboxes' ], 10, 3 );
		add_action( 'user_register', [ __CLASS__, 'save_consent' ] );
	}

	public static function render_checkboxes() {
		include NEKOKO_PATH . 'templates/shortcodes/registration-checkboxes.php';
	}

	public static function validate_checkboxes( $errors, $sanitized_user_login, $user_email ) {
		if ( empty( $_POST['nekoko_reg_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nekoko_reg_nonce'] ) ), 'nekoko_registration' ) ) {
			$errors->add( 'checkbox_error', __( 'Greška pri verifikaciji forme. Pokušaj ponovo.', 'nekoko' ) );
			return $errors;
		}

		if ( empty( $_POST['nekoko_accept_terms'] ) ) {
			$errors->add( 'terms_error', __( 'Morate prihvatiti Uslove korišćenja i Politiku privatnosti.', 'nekoko' ) );
		}

		if ( empty( $_POST['nekoko_accept_disclaimer'] ) ) {
			$errors->add( 'disclaimer_error', __( 'Morate potvrditi da razumete uslove korišćenja platforme.', 'nekoko' ) );
		}

		return $errors;
	}

	public static function save_consent( $user_id ) {
		if ( ! empty( $_POST['nekoko_accept_terms'] ) ) {
			update_user_meta( $user_id, '_nekoko_accepted_terms', current_time( 'mysql' ) );
		}
		if ( ! empty( $_POST['nekoko_accept_disclaimer'] ) ) {
			update_user_meta( $user_id, '_nekoko_accepted_disclaimer', current_time( 'mysql' ) );
		}
	}
}
