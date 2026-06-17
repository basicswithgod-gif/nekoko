<?php
defined( 'ABSPATH' ) || exit;

/**
 * [nekoko_provider_registration] - self-service provider sign-up form.
 */
class Nekoko_Shortcode_Provider_Registration {

	public static function init() {
		add_shortcode( 'nekoko_provider_registration', [ __CLASS__, 'render' ] );
	}

	public static function render() {
		if ( is_user_logged_in() ) {
			return self::render_already_logged_in();
		}

		$errors  = [];
		$success = false;
		$vals    = [ 'first_name' => '', 'last_name' => '', 'email' => '', 'description' => '' ];

		if ( isset( $_POST['nekoko_provider_submit'] ) ) {
			list( $errors, $vals, $success ) = self::handle_submission();
		}

		if ( $success ) {
			ob_start();
			include NEKOKO_PATH . 'templates/shortcodes/provider-registration-success.php';
			return ob_get_clean();
		}

		$tos_url     = home_url( '/uslovi-koriscenja/' );
		$privacy_url = home_url( '/politika-privatnosti/' );

		ob_start();
		include NEKOKO_PATH . 'templates/shortcodes/provider-registration.php';
		return ob_get_clean();
	}

	private static function render_already_logged_in() {
		$user         = wp_get_current_user();
		$is_provider  = in_array( 'provider', (array) $user->roles, true );
		$dashboard_url = home_url( '/provider-dashboard/' );
		$logout_url    = wp_logout_url( get_permalink() );

		ob_start();
		include NEKOKO_PATH . 'templates/shortcodes/provider-registration-already-logged-in.php';
		return ob_get_clean();
	}

	private static function handle_submission() {
		$errors = [];

		if ( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'nekoko_provider_reg' ) ) {
			return [ [ 'Greška pri verifikaciji forme. Pokušaj ponovo.' ], [ 'first_name' => '', 'last_name' => '', 'email' => '', 'description' => '' ], false ];
		}

		$first = sanitize_text_field( wp_unslash( $_POST['nekoko_first_name'] ?? '' ) );
		$last  = sanitize_text_field( wp_unslash( $_POST['nekoko_last_name'] ?? '' ) );
		$email = sanitize_email( wp_unslash( $_POST['nekoko_email'] ?? '' ) );
		$pass  = wp_unslash( $_POST['nekoko_password'] ?? '' );
		$pass2 = wp_unslash( $_POST['nekoko_password2'] ?? '' );
		$desc  = sanitize_textarea_field( wp_unslash( $_POST['nekoko_description'] ?? '' ) );
		$vals  = [ 'first_name' => $first, 'last_name' => $last, 'email' => $email, 'description' => $desc ];

		if ( ! $first ) {
			$errors[] = 'Ime je obavezno.';
		}
		if ( ! $last ) {
			$errors[] = 'Prezime je obavezno.';
		}
		if ( ! is_email( $email ) ) {
			$errors[] = 'Unesite ispravnu email adresu.';
		} elseif ( email_exists( $email ) ) {
			$errors[] = 'Nalog sa ovom email adresom već postoji.';
		}
		if ( strlen( $pass ) < 8 ) {
			$errors[] = 'Lozinka mora imati najmanje 8 karaktera.';
		}
		if ( $pass !== $pass2 ) {
			$errors[] = 'Lozinke se ne poklapaju.';
		}
		if ( ! $desc ) {
			$errors[] = 'Opis usluga je obavezan.';
		}
		if ( empty( $_POST['nekoko_accept_terms'] ) ) {
			$errors[] = 'Morate prihvatiti Uslove korišćenja i Politiku privatnosti.';
		}
		if ( empty( $_POST['nekoko_accept_disclaimer'] ) ) {
			$errors[] = 'Morate potvrditi da razumete uslove korišćenja platforme.';
		}

		if ( ! empty( $errors ) ) {
			return [ $errors, $vals, false ];
		}

		$base_username = sanitize_user( strtolower( $first . '.' . $last ), true );
		$username      = $base_username;
		for ( $i = 1; username_exists( $username ); $i++ ) {
			$username = $base_username . $i;
		}

		$user_id = wp_create_user( $username, $pass, $email );
		if ( is_wp_error( $user_id ) ) {
			return [ [ $user_id->get_error_message() ], $vals, false ];
		}

		$user = new WP_User( $user_id );
		$user->set_role( 'provider' );
		wp_update_user( [ 'ID' => $user_id, 'display_name' => $first . ' ' . $last ] );
		update_user_meta( $user_id, 'first_name', $first );
		update_user_meta( $user_id, 'last_name', $last );
		update_user_meta( $user_id, '_nekoko_provider_status', 'pending' );
		update_user_meta( $user_id, '_nekoko_service_description', $desc );
		update_user_meta( $user_id, '_nekoko_accepted_terms', current_time( 'mysql' ) );
		update_user_meta( $user_id, '_nekoko_accepted_disclaimer', current_time( 'mysql' ) );

		return [ [], $vals, true ];
	}
}
