<?php
/**
 * NekoKo Static Pages Setup — US 5.1
 * Run via WP-CLI: wp eval-file wp-content/nekoko-setup-pages.php
 * Creates all static pages and sets up navigation menus.
 */
defined( "ABSPATH" ) || exit;

echo "Setting up NekoKo static pages...\n";

$pages = [
    [ "title" => "O nama",            "slug" => "o-nama",              "content" => "[O nama content - placeholder for Content agent]" ],
    [ "title" => "Kako funkcionise",  "slug" => "kako-funkcionise",    "content" => "[Kako funkcionise content - placeholder for Content agent]" ],
    [ "title" => "Pomoc",             "slug" => "pomoc",               "content" => "[FAQ content - placeholder for Content agent]" ],
    [ "title" => "Kontakt",           "slug" => "kontakt",             "content" => "[Contact form - placeholder for Content agent]" ],
    [ "title" => "Uslovi koriscenja", "slug" => "uslovi-koriscenja",   "content" => "[Terms of Service - placeholder for Content agent - LEGAL REVIEW REQUIRED]" ],
    [ "title" => "Politika privatnosti", "slug" => "politika-privatnosti", "content" => "[Privacy Policy GDPR/LPDP compliant - placeholder for Content agent - LEGAL REVIEW REQUIRED]" ],
    [ "title" => "Bezbednost",        "slug" => "bezbednost",          "content" => "[Safety Disclaimer - placeholder for Content agent]" ],
    [ "title" => "Dashboard",         "slug" => "dashboard",           "content" => "[nekoko_provider_dashboard]" ],
];

$page_ids = [];
foreach ( $pages as $p ) {
    $existing = get_page_by_path( $p["slug"] );
    if ( $existing ) {
        $page_ids[$p["slug"]] = $existing->ID;
        echo "Page exists: {$p["title"]}\n";
        continue;
    }
    $id = wp_insert_post([
        "post_title"   => $p["title"],
        "post_content" => $p["content"],
        "post_status"  => "publish",
        "post_type"    => "page",
        "post_name"    => $p["slug"],
    ]);
    if ( ! is_wp_error( $id ) ) {
        $page_ids[$p["slug"]] = $id;
        echo "Created page: {$p["title"]} (ID: $id)\n";
    }
}

// Set homepage as static page (create if not exists)
$homepage = get_page_by_path( "pocetna" );
if ( ! $homepage ) {
    $hp_id = wp_insert_post([ "post_title" => "Pocetna", "post_content" => "[nekoko_homepage]", "post_status" => "publish", "post_type" => "page", "post_name" => "pocetna" ]);
    update_option( "page_on_front", $hp_id );
    update_option( "show_on_front", "page" );
    echo "Created homepage (ID: $hp_id)\n";
} else {
    update_option( "page_on_front", $homepage->ID );
    update_option( "show_on_front", "page" );
    echo "Homepage exists: {$homepage->ID}\n";
}

// Create primary menu
$menu_name = "Main Navigation";
$menu_exists = wp_get_nav_menu_object( $menu_name );
if ( ! $menu_exists ) {
    $menu_id = wp_create_nav_menu( $menu_name );
    $menu_pages = [ "kako-funkcionise" => "Kako funkcionise", "o-nama" => "O nama", "pomoc" => "Pomoc", "kontakt" => "Kontakt" ];
    foreach ( $menu_pages as $slug => $label ) {
        if ( isset( $page_ids[$slug] ) ) {
            wp_update_nav_menu_item( $menu_id, 0, [ "menu-item-title" => $label, "menu-item-object" => "page", "menu-item-object-id" => $page_ids[$slug], "menu-item-type" => "post_type", "menu-item-status" => "publish" ] );
        }
    }
    set_theme_mod( "nav_menu_locations", [ "primary" => $menu_id ] );
    echo "Created menu: $menu_name (ID: $menu_id)\n";
}

// Create footer menu  
$footer_name = "Footer Links";
$footer_exists = wp_get_nav_menu_object( $footer_name );
if ( ! $footer_exists ) {
    $fmenu_id = wp_create_nav_menu( $footer_name );
    $footer_pages = [ "uslovi-koriscenja" => "Uslovi koriscenja", "politika-privatnosti" => "Politika privatnosti", "bezbednost" => "Bezbednost", "kontakt" => "Kontakt" ];
    foreach ( $footer_pages as $slug => $label ) {
        if ( isset( $page_ids[$slug] ) ) {
            wp_update_nav_menu_item( $fmenu_id, 0, [ "menu-item-title" => $label, "menu-item-object" => "page", "menu-item-object-id" => $page_ids[$slug], "menu-item-type" => "post_type", "menu-item-status" => "publish" ] );
        }
    }
    $menus = get_theme_mod( "nav_menu_locations", [] );
    $menus["footer"] = $fmenu_id;
    set_theme_mod( "nav_menu_locations", $menus );
    echo "Created footer menu (ID: $fmenu_id)\n";
}

// Flush rewrite rules
flush_rewrite_rules( true );
echo "\nStatic pages setup complete!\n";
