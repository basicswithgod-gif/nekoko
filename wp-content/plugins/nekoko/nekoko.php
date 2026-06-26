<?php
/**
 * Plugin Name:       NekoKo Core
 * Plugin URI:        https://nekoko.rs
 * Description:       Core functionality for NekoKo.rs - Jobs CPT, taxonomies, roles, shortcodes, blocks, booking, moderation.
 * Version:           1.0.0
 * Requires PHP:      8.0
 * Author:            NekoKo
 * Text Domain:       nekoko
 */

defined( 'ABSPATH' ) || exit;

define( 'NEKOKO_VERSION', '1.1.0' );
define( 'NEKOKO_PATH', plugin_dir_path( __FILE__ ) );
define( 'NEKOKO_URL', plugin_dir_url( __FILE__ ) );

require_once NEKOKO_PATH . 'includes/class-nekoko-cpt.php';
require_once NEKOKO_PATH . 'includes/class-nekoko-booking-cpt.php';
require_once NEKOKO_PATH . 'includes/class-nekoko-taxonomies.php';
require_once NEKOKO_PATH . 'includes/class-nekoko-roles.php';
require_once NEKOKO_PATH . 'includes/class-nekoko-emails.php';
require_once NEKOKO_PATH . 'includes/class-nekoko-registration.php';
require_once NEKOKO_PATH . 'includes/class-nekoko-safety-popup.php';
require_once NEKOKO_PATH . 'includes/class-nekoko-moderation.php';
require_once NEKOKO_PATH . 'includes/blocks/class-nekoko-blocks.php';
require_once NEKOKO_PATH . 'includes/shortcodes/class-shortcode-booking-form.php';
require_once NEKOKO_PATH . 'includes/shortcodes/class-shortcode-reviews.php';
require_once NEKOKO_PATH . 'includes/shortcodes/class-shortcode-search-filter.php';
require_once NEKOKO_PATH . 'includes/shortcodes/class-shortcode-provider-registration.php';
require_once NEKOKO_PATH . 'includes/shortcodes/class-shortcode-provider-dashboard.php';
require_once NEKOKO_PATH . 'includes/shortcodes/class-shortcode-hp-search.php';
require_once NEKOKO_PATH . 'includes/class-nekoko-review-cpt.php';
require_once NEKOKO_PATH . 'includes/class-nekoko-review-token.php';
require_once NEKOKO_PATH . 'includes/class-nekoko-provider-profile.php';

Nekoko_CPT::init();
Nekoko_Booking_CPT::init();
Nekoko_Taxonomies::init();
Nekoko_Roles::init();
Nekoko_Emails::init();
Nekoko_Registration::init();
Nekoko_Safety_Popup::init();
Nekoko_Moderation::init();
Nekoko_Blocks::init();
Nekoko_Shortcode_Booking_Form::init();
Nekoko_Shortcode_Reviews::init();
Nekoko_Shortcode_Search_Filter::init();
Nekoko_Shortcode_Provider_Registration::init();
Nekoko_Shortcode_Provider_Dashboard::init();
Nekoko_Shortcode_Hp_Search::init();
Nekoko_Review_CPT::init();
Nekoko_Review_Token::init();
Nekoko_Provider_Profile::init();

add_action(
	'init',
	function () {
		if ( get_option( 'nekoko_rewrite_version' ) !== NEKOKO_VERSION ) {
			flush_rewrite_rules();
			update_option( 'nekoko_rewrite_version', NEKOKO_VERSION );
		}
	},
	999
);

add_filter( 'hello_elementor_page_title', function ( $show ) {
	return is_front_page() ? false : $show;
} );
