<?php
defined( 'ABSPATH' ) || exit;

/**
 * Booking / moderation transactional emails.
 * Each email type has its own HTML template in templates/emails/.
 */
class Nekoko_Emails {

	public static function init() {
		add_action( 'user_register', [ __CLASS__, 'send_welcome_email' ] );
	}

	/**
	 * Renders an email body by including its template with $vars extracted into scope.
	 */
	public static function render( $type, $vars = [] ) {
		$template = NEKOKO_PATH . 'templates/emails/' . $type . '.php';
		if ( ! file_exists( $template ) ) {
			return '';
		}

		extract( $vars, EXTR_SKIP );

		ob_start();
		include NEKOKO_PATH . 'templates/emails/layout-start.php';
		include $template;
		include NEKOKO_PATH . 'templates/emails/layout-end.php';
		return ob_get_clean();
	}

	public static function headers() {
		return [ 'Content-Type: text/html; charset=UTF-8', 'From: NekoKo <support@nekoko.rs>' ];
	}

	public static function send_booking_emails( $job, $customer_name, $customer_email, $message, $date ) {
		$provider = get_user_by( 'id', $job->post_author );

		if ( $provider ) {
			wp_mail(
				$provider->user_email,
				'Nov zahtev za rezervaciju — ' . $job->post_title,
				self::render(
					'booking-alert',
					[
						'provider_name'  => $provider->display_name,
						'customer_name'  => $customer_name,
						'customer_email' => $customer_email,
						'job_title'      => $job->post_title,
						'message'        => $message,
						'date'           => $date ?: 'Nije navedeno',
					]
				),
				self::headers()
			);
		}

		wp_mail(
			$customer_email,
			'Zahtev je primljen — ' . $job->post_title,
			self::render(
				'booking-confirmation',
				[
					'customer_name' => $customer_name,
					'job_title'     => $job->post_title,
					'job_url'       => get_permalink( $job->ID ),
				]
			),
			self::headers()
		);
	}

	public static function send_welcome_email( $user_id ) {
		$user = get_user_by( 'id', $user_id );
		if ( ! $user ) {
			return;
		}
		wp_mail(
			$user->user_email,
			'Dobrodošao/la na NekoKo.rs!',
			self::render( 'welcome', [ 'user_name' => $user->display_name ] ),
			self::headers()
		);
	}
}
