<?php
defined( 'ABSPATH' ) || exit;

/**
 * Admin moderation menu: dashboard, pending providers, pending jobs.
 */
class Nekoko_Moderation {

	public static function init() {
		add_action( 'admin_menu', [ __CLASS__, 'register_menu' ] );
	}

	public static function register_menu() {
		add_menu_page( 'NekoKo Moderation', 'Moderacija', 'manage_options', 'nekoko-moderation', [ __CLASS__, 'render_dashboard' ], 'dashicons-shield', 25 );
		add_submenu_page( 'nekoko-moderation', 'Pending Providers', 'Provajderi pending', 'manage_options', 'nekoko-pending-providers', [ __CLASS__, 'render_pending_providers' ] );
		add_submenu_page( 'nekoko-moderation', 'Pending Jobs', 'Jobs pending', 'manage_options', 'nekoko-pending-jobs', [ __CLASS__, 'render_pending_jobs' ] );
	}

	public static function render_dashboard() {
		$pending_providers_count = count( self::get_pending_providers() );
		$pending_jobs_count      = count( self::get_pending_jobs() );
		$providers_url           = admin_url( 'admin.php?page=nekoko-pending-providers' );
		$jobs_url                = admin_url( 'admin.php?page=nekoko-pending-jobs' );
		include NEKOKO_PATH . 'templates/admin/moderation-dashboard.php';
	}

	private static function get_pending_providers() {
		return get_users(
			[
				'role'       => 'provider',
				'meta_query' => [ [ 'key' => '_nekoko_provider_status', 'value' => 'approved', 'compare' => '!=' ] ],
			]
		);
	}

	private static function get_pending_jobs() {
		return get_posts(
			[
				'post_type'      => Nekoko_CPT::POST_TYPE,
				'post_status'    => [ 'pending', 'draft' ],
				'numberposts'    => -1,
			]
		);
	}

	public static function render_pending_providers() {
		if ( isset( $_GET['action'], $_GET['user_id'], $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'nekoko_provider_action' ) ) {
			$user_id = intval( $_GET['user_id'] );
			$action  = sanitize_text_field( wp_unslash( $_GET['action'] ) );

			if ( $action === 'approve-provider' ) {
				update_user_meta( $user_id, '_nekoko_provider_status', 'approved' );
				$user = get_user_by( 'id', $user_id );
				wp_mail(
					$user->user_email,
					'Vas nalog je odobren - NekoKo',
					Nekoko_Emails::render( 'listing-approved', [ 'provider_name' => $user->display_name, 'job_title' => 'nalog provajdera', 'job_url' => home_url() ] ),
					Nekoko_Emails::headers()
				);
				echo '<div class="notice notice-success"><p>Provajder odobren!</p></div>';
			} elseif ( $action === 'reject-provider' ) {
				update_user_meta( $user_id, '_nekoko_provider_status', 'rejected' );
				echo '<div class="notice notice-error"><p>Provajder odbijen.</p></div>';
			}
		}

		$users = self::get_pending_providers();
		include NEKOKO_PATH . 'templates/admin/pending-providers.php';
	}

	public static function render_pending_jobs() {
		if ( isset( $_GET['action'], $_GET['post_id'], $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'nekoko_job_action' ) ) {
			$post_id = intval( $_GET['post_id'] );
			$action  = sanitize_text_field( wp_unslash( $_GET['action'] ) );
			$job     = get_post( $post_id );

			if ( $action === 'approve-job' ) {
				wp_update_post( [ 'ID' => $post_id, 'post_status' => 'publish' ] );
				update_post_meta( $post_id, '_nekoko_listing_status', 'approved' );
				$provider = get_user_by( 'id', $job->post_author );
				if ( $provider ) {
					wp_mail(
						$provider->user_email,
						'Usluga odobrena - ' . $job->post_title,
						Nekoko_Emails::render( 'listing-approved', [ 'provider_name' => $provider->display_name, 'job_title' => $job->post_title, 'job_url' => get_permalink( $post_id ) ] ),
						Nekoko_Emails::headers()
					);
				}
				echo '<div class="notice notice-success"><p>Usluga odobrena!</p></div>';
			} elseif ( $action === 'reject-job' ) {
				wp_update_post( [ 'ID' => $post_id, 'post_status' => 'draft' ] );
				update_post_meta( $post_id, '_nekoko_listing_status', 'rejected' );
				$provider = get_user_by( 'id', $job->post_author );
				if ( $provider ) {
					wp_mail(
						$provider->user_email,
						'Usluga odbijena - ' . $job->post_title,
						Nekoko_Emails::render( 'listing-rejected', [ 'provider_name' => $provider->display_name, 'job_title' => $job->post_title, 'reason' => '' ] ),
						Nekoko_Emails::headers()
					);
				}
				echo '<div class="notice notice-error"><p>Usluga odbijena.</p></div>';
			}
		}

		$jobs = self::get_pending_jobs();
		include NEKOKO_PATH . 'templates/admin/pending-jobs.php';
	}
}
