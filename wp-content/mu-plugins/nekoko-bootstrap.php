<?php
/**
 * NekoKo Bootstrap MU-Plugin
 * Runs once on first WordPress load after setup:
 * - Activates all required plugins
 * - Sets permalink structure
 * - Sets child theme as active
 * Self-destructs after successful run.
 */
defined( "ABSPATH" ) || exit;

function nekoko_bootstrap_run() {
    $done_key = "nekoko_bootstrap_done";
    if ( get_option( $done_key ) ) return;

    // Activate all required plugins
    $plugins = [
        "wordfence/wordfence.php",
        "wp-mail-smtp/wp_mail_smtp.php",
        "user-role-editor/user-role-editor.php",
        "advanced-custom-fields/acf.php",
        "wpforms-lite/wpforms.php",
        "wordpress-seo/wp-seo.php",
        "wp-super-cache/wp-cache.php",
    ];
    $active = get_option( "active_plugins", [] );
    foreach ( $plugins as $plugin ) {
        if ( ! in_array( $plugin, $active, true ) ) {
            $active[] = $plugin;
        }
    }
    update_option( "active_plugins", $active );

    // Set child theme
    update_option( "stylesheet", "nekoko-child" );
    update_option( "template", "astra" );

    // Set permalink structure
    global $wp_rewrite;
    $wp_rewrite->set_permalink_structure( "/%postname%/" );
    $wp_rewrite->flush_rules( true );

    // Set blogname and tagline
    update_option( "blogname", "NekoKo.rs" );
    update_option( "blogdescription", "Platforma za nisne usluge u Srbiji" );

    // Set default timezone
    update_option( "timezone_string", "Europe/Belgrade" );

    // Allow registration
    update_option( "users_can_register", 1 );

    // Set default role to customer
    update_option( "default_role", "customer" );

    // Disable comments on posts by default
    update_option( "default_comment_status", "closed" );

    // Mark done
    update_option( $done_key, time() );
}
add_action( "init", "nekoko_bootstrap_run", 1 );


// US 4.5 - Configure WP Mail SMTP on first run
function nekoko_configure_smtp() {
    if ( get_option( "nekoko_smtp_configured" ) ) return;

    $smtp_options = [
        "mail_from"      => "support@nekoko.rs",
        "mail_from_name" => "NekoKo",
        "mailer"         => "other_smtp",
        "smtp_host"      => "smtp.hostinger.com",   // TODO: confirm with Marko before deploy
        "smtp_port"      => 465,
        "smtp_encryption"=> "ssl",
        "smtp_auth"      => true,
        "smtp_user"      => "support@nekoko.rs",    // TODO: Marko to fill in before deploy
        "smtp_pass"      => "",                     // TODO: Marko to fill in password before deploy
    ];
    update_option( "wp_mail_smtp", $smtp_options );
    update_option( "nekoko_smtp_configured", time() );
}
add_action( "init", "nekoko_configure_smtp", 2 );
