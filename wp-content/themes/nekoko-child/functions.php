<?php
defined( "ABSPATH" ) || exit;

// US 4.1 - Security Hardening
add_filter( "the_generator", "__return_empty_string" );
add_filter( "xmlrpc_enabled", "__return_false" );
add_filter( "wp_headers", function( $h ) { unset( $h["X-Pingback"] ); return $h; } );
add_action( "wp_head", function() { remove_action( "wp_head", "wp_generator" ); remove_action( "wp_head", "wlwmanifest_link" ); remove_action( "wp_head", "rsd_link" ); }, 1 );

// US 4.2 - Enqueue Styles & Fonts
add_action( "wp_enqueue_scripts", "nekoko_enqueue_assets" );
function nekoko_enqueue_assets() {
    wp_enqueue_style( "inter-font", "https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap", [], null );
    wp_enqueue_style( "astra-parent", get_template_directory_uri() . "/style.css", [], wp_get_theme( "astra" )->get( "Version" ) );
    wp_enqueue_style( "nekoko-child", get_stylesheet_uri(), [ "astra-parent" ], wp_get_theme()->get( "Version" ) );
    if ( file_exists( get_stylesheet_directory() . "/js/nekoko.js" ) ) {
        wp_enqueue_script( "nekoko-main", get_stylesheet_directory_uri() . "/js/nekoko.js", [ "jquery" ], "1.0.0", true );
        wp_localize_script( "nekoko-main", "nekokoData", [ "ajaxUrl" => admin_url( "admin-ajax.php" ), "nonce" => wp_create_nonce( "nekoko_nonce" ), "userId" => get_current_user_id(), "isLoggedIn" => is_user_logged_in() ] );
    }
}

// US 5.1 - Nav Menus
add_action( "after_setup_theme", "nekoko_register_menus" );
function nekoko_register_menus() {
    register_nav_menus( [ "primary" => "Primary Navigation", "footer" => "Footer Links" ] );
}


// US 4.3 - Custom Roles
add_action( "init", "nekoko_register_roles" );
function nekoko_register_roles() {
    if ( ! get_role( "provider" ) ) { add_role( "provider", "Provider", [ "read" => true, "edit_posts" => true, "publish_posts" => true, "delete_posts" => true, "upload_files" => true ] ); }
    if ( ! get_role( "customer" ) ) { add_role( "customer", "Customer", [ "read" => true ] ); }
}

// US 4.3 - CPT: service_listing
add_action( "init", "nekoko_register_cpt" );
function nekoko_register_cpt() {
    register_post_type( "service_listing", [
        "labels" => [ "name" => "Service Listings", "singular_name" => "Service Listing", "add_new" => "Add New", "add_new_item" => "Add New Listing", "edit_item" => "Edit Listing", "view_item" => "View Listing", "search_items" => "Search", "not_found" => "No listings found", "menu_name" => "Service Listings" ],
        "public" => true, "has_archive" => true, "rewrite" => [ "slug" => "listing" ],
        "supports" => [ "title", "editor", "thumbnail", "author", "custom-fields" ],
        "show_in_rest" => true, "menu_icon" => "dashicons-store", "capability_type" => "post", "map_meta_cap" => true,
    ] );
}

// US 4.3 - Taxonomy: service_category
add_action( "init", "nekoko_register_taxonomy" );
function nekoko_register_taxonomy() {
    register_taxonomy( "service_category", "service_listing", [
        "labels" => [ "name" => "Service Categories", "singular_name" => "Service Category", "menu_name" => "Categories" ],
        "hierarchical" => true, "show_ui" => true, "show_in_rest" => true,
        "rewrite" => [ "slug" => "service-category" ], "public" => true,
    ] );
    foreach ( [ "Umetnost", "Lepota", "Zdravlje", "Životinje", "Zabava", "Ostalo" ] as $cat ) {
        if ( ! term_exists( $cat, "service_category" ) ) { wp_insert_term( $cat, "service_category" ); }
    }
}

// Force non-admins listings to pending
add_filter( "wp_insert_post_data", "nekoko_force_listing_pending", 10, 2 );
function nekoko_force_listing_pending( $data, $postarr ) {
    if ( $data["post_type"] === "service_listing" && ! current_user_can( "manage_options" ) ) {
        if ( in_array( $data["post_status"], [ "publish", "future" ], true ) ) { $data["post_status"] = "pending"; }
    }
    return $data;
}


// US 4.3 - Provider Dashboard Shortcode
add_shortcode( "nekoko_provider_dashboard", "nekoko_provider_dashboard_sc" );
function nekoko_provider_dashboard_sc() {
    if ( ! is_user_logged_in() ) return "<p><a href=" . esc_url( wp_login_url( get_permalink() ) ) . ">Prijavi se</a> da bi pristupio/la dashboardu.</p>";
    $user = wp_get_current_user();
    if ( ! in_array( "provider", (array) $user->roles, true ) ) return "<p>Ova stranica je dostupna samo pružaocima usluga.</p>";
    $listings = get_posts( [ "post_type" => "service_listing", "author" => $user->ID, "post_status" => [ "publish", "pending", "draft" ], "numberposts" => -1 ] );
    ob_start();
    echo "<div class=\"nekoko-dashboard\"><div class=\"dashboard-grid\">";
    echo "<div class=\"dashboard-sidebar\"><ul>";
    echo "<li><a href=\"#listings\" class=\"active\">Moje usluge</a></li>";
    echo "<li><a href=\"" . esc_url( get_edit_user_link( $user->ID ) ) . "\">Moj profil</a></li>";
    echo "</ul></div>";
    echo "<div class=\"dashboard-main\"><h2>Moje usluge (" . count( $listings ) . ")</h2>";
    if ( empty( $listings ) ) {
        echo "<p>Još nisi dodao/la nijednu uslugu.</p>";
    } else {
        echo "<table style=\"width:100%;border-collapse:collapse;\"><thead><tr style=\"background:var(--nekoko-light);\"><th style=\"padding:12px;\">Naziv</th><th style=\"padding:12px;\">Status</th><th style=\"padding:12px;\">Akcije</th></tr></thead><tbody>";
        foreach ( $listings as $l ) {
            $st  = get_post_meta( $l->ID, "_nekoko_listing_status", true ) ?: $l->post_status;
            $cls = "badge-" . ( $st === "publish" ? "approved" : ( $st === "pending" ? "pending" : "rejected" ) );
            echo "<tr style=\"border-bottom:1px solid var(--nekoko-light);\"><td style=\"padding:12px;\">" . esc_html( $l->post_title ) . "</td><td style=\"padding:12px;\"><span class=\"badge " . esc_attr( $cls ) . "\">" . esc_html( ucfirst( $st ) ) . "</span></td><td style=\"padding:12px;\"><a href=\"" . esc_url( get_permalink( $l->ID ) ) . "\">Pogledaj</a> | <a href=\"" . esc_url( get_edit_post_link( $l->ID ) ) . "\">Uredi</a></td></tr>";
        }
        echo "</tbody></table>";
    }
    echo "</div></div></div>";
    return ob_get_clean();
}

// US 4.3 - Booking Form Shortcode
add_shortcode( "nekoko_booking_form", "nekoko_booking_form_sc" );
function nekoko_booking_form_sc( $atts ) {
    $atts = shortcode_atts( [ "listing_id" => 0 ], $atts );
    $listing = get_post( intval( $atts["listing_id"] ) ?: get_the_ID() );
    if ( ! $listing || $listing->post_type !== "service_listing" ) return "";
    if ( isset( $_POST["nekoko_booking_submit"] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST["_wpnonce"] ) ), "nekoko_booking_" . $listing->ID ) ) {
        $name = sanitize_text_field( wp_unslash( $_POST["nekoko_name"] ?? "" ) );
        $email = sanitize_email( wp_unslash( $_POST["nekoko_email"] ?? "" ) );
        $message = sanitize_textarea_field( wp_unslash( $_POST["nekoko_message"] ?? "" ) );
        $date = sanitize_text_field( wp_unslash( $_POST["nekoko_date"] ?? "" ) );
        if ( $name && $email && $message ) {
            nekoko_send_booking_emails( $listing, $name, $email, $message, $date );
            return "<div style=\"background:#d1e7dd;padding:20px;border-radius:8px;\"><p><strong>Zahtev je poslat!</strong> Pružalac će te kontaktirati u roku od 48 sati.</p></div>";
        }
    }
    ob_start(); ?>
    <div class="nekoko-form">
    <h3>Pošalji zahtev za rezervaciju</h3>
    <form method="post">
        <?php wp_nonce_field( "nekoko_booking_" . $listing->ID ); ?>
        <input type="hidden" name="nekoko_listing_id" value="<?php echo esc_attr( $listing->ID ); ?>">
        <div class="form-group"><label>Ime i prezime *</label><input type="text" name="nekoko_name" required placeholder="Tvoje ime"></div>
        <div class="form-group"><label>Email adresa *</label><input type="email" name="nekoko_email" required placeholder="tvoj@email.com"></div>
        <div class="form-group"><label>Željeni datum</label><input type="date" name="nekoko_date"></div>
        <div class="form-group"><label>Poruka *</label><textarea name="nekoko_message" rows="5" required placeholder="Opiši šta ti je potrebno..."></textarea></div>
        <button type="submit" name="nekoko_booking_submit" class="nekoko-btn">Pošalji zahtev</button>
    </form></div>
    <?php return ob_get_clean();
}


// US 4.5 - Email Functions
function nekoko_send_booking_emails( $listing, $customer_name, $customer_email, $message, $date ) {
    $provider = get_user_by( "id", $listing->post_author );
    $headers  = [ "Content-Type: text/html; charset=UTF-8", "From: NekoKo <support@nekoko.rs>" ];
    if ( $provider ) {
        wp_mail( $provider->user_email, "Nov zahtev za rezervaciju — " . $listing->post_title,
            nekoko_email_template( "booking-alert", [ "provider_name" => $provider->display_name, "customer_name" => $customer_name, "customer_email" => $customer_email, "listing_title" => $listing->post_title, "message" => nl2br( esc_html( $message ) ), "date" => $date ?: "Nije navedeno" ] ), $headers );
    }
    wp_mail( $customer_email, "Zahtev je primljen — " . $listing->post_title,
        nekoko_email_template( "booking-confirmation", [ "customer_name" => $customer_name, "listing_title" => $listing->post_title, "listing_url" => get_permalink( $listing->ID ) ] ), $headers );
}

function nekoko_email_template( $type, $vars = [] ) {
    $logo  = "<h1 style=\"color:#fff;font-family:Arial,sans-serif;margin:0;\">Neko<span style=\"color:#F5B335;\">Ko</span></h1>";
    $start = "<div style=\"font-family:Arial,sans-serif;max-width:600px;margin:0 auto;background:#fff;\"><div style=\"background:#004682;padding:24px 32px;\">" . $logo . "</div><div style=\"padding:32px;\">";
    $end   = "</div><div style=\"background:#2E2E2E;padding:16px 32px;text-align:center;font-size:12px;color:#999;\"><p>NekoKo.rs &mdash; Platforma za nišne usluge u Srbiji</p></div></div>";
    switch ( $type ) {
        case "booking-alert":
            $body = "<p>Zdravo, <strong>" . esc_html( $vars["provider_name"] ) . "</strong>!</p><p>Nov zahtev za <strong>" . esc_html( $vars["listing_title"] ) . "</strong>:</p><table style=\"background:#f5f5f5;padding:20px;border-radius:8px;width:100%;\"><tr><td><strong>Ime:</strong></td><td>" . esc_html( $vars["customer_name"] ) . "</td></tr><tr><td><strong>Email:</strong></td><td>" . esc_html( $vars["customer_email"] ) . "</td></tr><tr><td><strong>Datum:</strong></td><td>" . esc_html( $vars["date"] ) . "</td></tr><tr><td><strong>Poruka:</strong></td><td>" . $vars["message"] . "</td></tr></table><p style=\"color:#666;font-size:13px;\">NekoKo ne posreduje u plaćanju. Sve dogovore obavljate direktno.</p>";
            break;
        case "booking-confirmation":
            $body = "<p>Zdravo, <strong>" . esc_html( $vars["customer_name"] ) . "</strong>!</p><p>Zahtev za <strong>" . esc_html( $vars["listing_title"] ) . "</strong> je poslat. Pružalac će te kontaktirati u roku od 48 sati.</p><p><a href=\"" . esc_url( $vars["listing_url"] ) . "\" style=\"display:inline-block;background:#004682;color:#fff;padding:12px 24px;border-radius:8px;text-decoration:none;\">Pogledaj uslugu</a></p>";
            break;
        case "listing-approved":
            $body = "<p>Zdravo, <strong>" . esc_html( $vars["provider_name"] ) . "</strong>!</p><p>Tvoja usluga <strong>" . esc_html( $vars["listing_title"] ) . "</strong> je <strong style=\"color:#0f5132;\">odobrena</strong>!</p><p><a href=\"" . esc_url( $vars["listing_url"] ) . "\" style=\"display:inline-block;background:#004682;color:#fff;padding:12px 24px;border-radius:8px;text-decoration:none;\">Pogledaj</a></p>";
            break;
        case "listing-rejected":
            $body = "<p>Zdravo, <strong>" . esc_html( $vars["provider_name"] ) . "</strong>!</p><p>Usluga <strong>" . esc_html( $vars["listing_title"] ) . "</strong> nije odobrena." . ( ! empty( $vars["reason"] ) ? " Razlog: " . esc_html( $vars["reason"] ) : "" ) . "</p><p>Možeš urediti i ponovo poslati.</p>";
            break;
        case "welcome":
            $body = "<p>Zdravo, <strong>" . esc_html( $vars["user_name"] ) . "</strong>!</p><p>Dobrodošao/la na NekoKo.rs!</p><p><a href=\"" . esc_url( home_url() ) . "\" style=\"display:inline-block;background:#004682;color:#fff;padding:12px 24px;border-radius:8px;text-decoration:none;\">Idi na platformu</a></p><p style=\"color:#666;font-size:13px;\">NekoKo ne procesira plaćanja i ne garantuje usluge trećih strana.</p>";
            break;
        default: $body = "";
    }
    return $start . $body . $end;
}

add_action( "user_register", "nekoko_send_welcome_email" );
function nekoko_send_welcome_email( $user_id ) {
    $user = get_user_by( "id", $user_id );
    wp_mail( $user->user_email, "Dobrodošao/la na NekoKo.rs!", nekoko_email_template( "welcome", [ "user_name" => $user->display_name ] ), [ "Content-Type: text/html; charset=UTF-8", "From: NekoKo <support@nekoko.rs>" ] );
}


// US 4.4 - Safety Warning Popup (every 90 days)
add_action( "wp_footer", "nekoko_safety_popup" );
function nekoko_safety_popup() {
    if ( ! is_user_logged_in() ) return;
    $uid  = get_current_user_id();
    $last = (int) get_user_meta( $uid, "_nekoko_safety_last_shown", true );
    if ( $last && ( time() - $last ) < 90 * DAY_IN_SECONDS ) return;
    $ajax_url = esc_url( admin_url( "admin-ajax.php" ) );
    $nonce    = esc_js( wp_create_nonce( "nekoko_safety" ) );
    $kontakt  = esc_url( home_url( "/kontakt/" ) );
    echo <<<HTML
<div id="nekoko-safety-overlay" role="dialog" aria-modal="true">
    <div id="nekoko-safety-modal">
        <div class="modal-header"><span class="modal-icon">&#9888;&#65039;</span><h2>Važno obaveštenje o bezbednosti</h2></div>
        <div class="modal-body">
            <p>NekoKo.rs je platforma koja spaja korisnike sa nezavisnim pružaocima usluga. <strong>NekoKo nije poslodavac niti agent pružalaca usluga</strong> i ne snosi odgovornost za njihove usluge.</p>
            <ul>
                <li>NekoKo <strong>ne procesira plaćanja</strong> — sve finansijske transakcije su direktno između tebe i pružaoca.</li>
                <li>Pre angažmana pružaoca, <strong>proveri reference</strong> i komuniciraj jasno.</li>
                <li>Zabranjene su ilegalne, nebezbedne, diskriminatorne i obmanjujuće usluge.</li>
                <li>Ako uočiš sumnjivo ponašanje, <a href="{$kontakt}">prijavi nam</a>.</li>
            </ul>
        </div>
        <label class="modal-checkbox"><input type="checkbox" id="nekoko-safety-agree"><span>Razumem i prihvatam uslove korišćenja platforme NekoKo.rs.</span></label>
        <button id="nekoko-safety-confirm" class="nekoko-btn" disabled style="width:100%;opacity:.5;cursor:not-allowed;">Razumem, nastavi</button>
    </div>
</div>
<script>
(function(){
    var cb=document.getElementById("nekoko-safety-agree"),btn=document.getElementById("nekoko-safety-confirm"),ov=document.getElementById("nekoko-safety-overlay");
    cb.addEventListener("change",function(){ btn.disabled=!cb.checked; btn.style.opacity=cb.checked?"1":".5"; btn.style.cursor=cb.checked?"pointer":"not-allowed"; });
    btn.addEventListener("click",function(){ if(!cb.checked)return; fetch("{$ajax_url}",{method:"POST",headers:{"Content-Type":"application/x-www-form-urlencoded"},body:"action=nekoko_safety_ack&nonce={$nonce}"}).then(function(){ ov.remove(); }); });
})();
</script>
HTML;
}

add_action( "wp_ajax_nekoko_safety_ack", "nekoko_handle_safety_ack" );
function nekoko_handle_safety_ack() {
    check_ajax_referer( "nekoko_safety", "nonce" );
    update_user_meta( get_current_user_id(), "_nekoko_safety_last_shown", time() );
    update_user_meta( get_current_user_id(), "_nekoko_safety_ack", 1 );
    wp_die( "ok" );
}

// US 4.4 - Reviews Shortcode
add_shortcode( "nekoko_reviews", "nekoko_reviews_sc" );
function nekoko_reviews_sc( $atts ) {
    $atts = shortcode_atts( [ "listing_id" => 0 ], $atts );
    $lid  = intval( $atts["listing_id"] ) ?: get_the_ID();
    if ( isset( $_POST["nekoko_review_submit"] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST["_wpnonce"] ) ), "nekoko_review_" . $lid ) && is_user_logged_in() ) {
        $rating  = min( 5, max( 1, intval( wp_unslash( $_POST["nekoko_rating"] ?? 5 ) ) ) );
        $comment = sanitize_textarea_field( wp_unslash( $_POST["nekoko_review_text"] ?? "" ) );
        $user    = wp_get_current_user();
        $reviews = get_post_meta( $lid, "_nekoko_reviews", true ) ?: [];
        $reviews[] = [ "user_id" => $user->ID, "name" => $user->display_name, "rating" => $rating, "comment" => $comment, "date" => current_time( "mysql" ) ];
        update_post_meta( $lid, "_nekoko_reviews", $reviews );
        update_post_meta( $lid, "_nekoko_avg_rating", round( array_sum( array_column( $reviews, "rating" ) ) / count( $reviews ), 1 ) );
        update_post_meta( $lid, "_nekoko_review_count", count( $reviews ) );
    }
    $reviews = get_post_meta( $lid, "_nekoko_reviews", true ) ?: [];
    $avg     = (float) get_post_meta( $lid, "_nekoko_avg_rating", true );
    ob_start();
    echo "<div class=\"nekoko-reviews\"><h3>Ocene i recenzije";
    if ( $reviews ) { echo " <span class=\"star-rating\" style=\"font-size:1rem;margin-left:8px;\">"; for($i=1;$i<=5;$i++) echo "<span class=\"star ".($i<=round($avg)?"filled":"")."\">&#9733;</span>"; echo "<span style=\"color:#666;font-size:.9rem;\">(".count($reviews).")</span></span>"; }
    echo "</h3>";
    foreach ( array_reverse( $reviews ) as $rev ) {
        echo "<div style=\"border-bottom:1px solid var(--nekoko-light);padding:16px 0;\"><div style=\"display:flex;align-items:center;gap:8px;margin-bottom:8px;\"><strong>".esc_html($rev["name"])."</strong><span class=\"star-rating\">";
        for($i=1;$i<=5;$i++) echo "<span class=\"star ".($i<=$rev["rating"]?"filled":"")."\">&#9733;</span>";
        echo "</span><span style=\"color:#999;font-size:.8rem;\">".esc_html(date_i18n("d.m.Y",strtotime($rev["date"])))."</span></div><p style=\"margin:0;\">".esc_html($rev["comment"])."</p></div>";
    }
    if ( is_user_logged_in() ) {
        echo "<div style=\"margin-top:24px;\"><h4>Ostavi recenziju</h4><form method=\"post\" class=\"nekoko-form\">";
        wp_nonce_field( "nekoko_review_" . $lid );
        echo "<div class=\"form-group\"><label>Ocena</label><select name=\"nekoko_rating\"><option value=\"5\">&#9733;&#9733;&#9733;&#9733;&#9733; Odlično</option><option value=\"4\">&#9733;&#9733;&#9733;&#9733; Dobro</option><option value=\"3\">&#9733;&#9733;&#9733; Prosečno</option><option value=\"2\">&#9733;&#9733; Loše</option><option value=\"1\">&#9733; Veoma loše</option></select></div>";
        echo "<div class=\"form-group\"><label>Recenzija</label><textarea name=\"nekoko_review_text\" rows=\"4\" placeholder=\"Napiši svoja iskustva...\"></textarea></div>";
        echo "<button type=\"submit\" name=\"nekoko_review_submit\" class=\"nekoko-btn\">Objavi recenziju</button></form></div>";
    }
    echo "</div>";
    return ob_get_clean();
}


// US 4.6 - Admin Moderation Menu
add_action( "admin_menu", "nekoko_admin_moderation_menu" );
function nekoko_admin_moderation_menu() {
    add_menu_page( "NekoKo Moderation", "Moderacija", "manage_options", "nekoko-moderation", "nekoko_moderation_page", "dashicons-shield", 25 );
    add_submenu_page( "nekoko-moderation", "Pending Providers", "Provajderi pending", "manage_options", "nekoko-pending-providers", "nekoko_pending_providers_page" );
    add_submenu_page( "nekoko-moderation", "Pending Listings", "Usluge pending", "manage_options", "nekoko-pending-listings", "nekoko_pending_listings_page" );
}

function nekoko_moderation_page() {
    $pp = get_users( [ "role" => "provider", "meta_key" => "_nekoko_provider_status", "meta_value" => "pending" ] );
    $pl = get_posts( [ "post_type" => "service_listing", "post_status" => "pending", "numberposts" => -1 ] );
    echo "<div class=\"wrap\"><h1>NekoKo Moderacija</h1><div style=\"display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-top:24px;\">";
    echo "<div style=\"background:#fff3cd;padding:24px;border-radius:8px;\"><h2 style=\"margin-top:0;\">Provajderi pending</h2><p style=\"font-size:2rem;font-weight:800;margin:0;\">".count($pp)."</p><a href=\"".esc_url(admin_url("admin.php?page=nekoko-pending-providers"))."\" class=\"button button-primary\" style=\"margin-top:16px;\">Pregledaj</a></div>";
    echo "<div style=\"background:#d1e7dd;padding:24px;border-radius:8px;\"><h2 style=\"margin-top:0;\">Usluge pending</h2><p style=\"font-size:2rem;font-weight:800;margin:0;\">".count($pl)."</p><a href=\"".esc_url(admin_url("admin.php?page=nekoko-pending-listings"))."\" class=\"button button-primary\" style=\"margin-top:16px;\">Pregledaj</a></div>";
    echo "</div></div>";
}

function nekoko_pending_providers_page() {
    if ( isset( $_GET["action"], $_GET["user_id"], $_GET["_wpnonce"] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET["_wpnonce"] ) ), "nekoko_provider_action" ) ) {
        $uid = intval( $_GET["user_id"] );
        $act = sanitize_text_field( wp_unslash( $_GET["action"] ) );
        if ( $act === "approve-provider" ) {
            update_user_meta( $uid, "_nekoko_provider_status", "approved" );
            $u = get_user_by( "id", $uid );
            wp_mail( $u->user_email, "Vas nalog je odobren - NekoKo", nekoko_email_template( "listing-approved", [ "provider_name" => $u->display_name, "listing_title" => "nalog provajdera", "listing_url" => home_url() ] ), [ "Content-Type: text/html; charset=UTF-8", "From: NekoKo <support@nekoko.rs>" ] );
            echo "<div class=\"notice notice-success\"><p>Provajder odobren!</p></div>";
        } elseif ( $act === "reject-provider" ) {
            update_user_meta( $uid, "_nekoko_provider_status", "rejected" );
            echo "<div class=\"notice notice-error\"><p>Provajder odbijen.</p></div>";
        }
    }
    $users = get_users( [ "role" => "provider", "meta_query" => [ [ "key" => "_nekoko_provider_status", "value" => "approved", "compare" => "!=" ] ] ] );
    echo "<div class=\"wrap\"><h1>Provajderi pending</h1><table class=\"widefat striped\"><thead><tr><th>Ime</th><th>Email</th><th>Registrovan</th><th>Status</th><th>Akcije</th></tr></thead><tbody>";
    foreach ( $users as $u ) {
        $st  = get_user_meta( $u->ID, "_nekoko_provider_status", true ) ?: "pending";
        $app = wp_nonce_url( admin_url( "admin.php?page=nekoko-pending-providers&action=approve-provider&user_id=" . $u->ID ), "nekoko_provider_action" );
        $rej = wp_nonce_url( admin_url( "admin.php?page=nekoko-pending-providers&action=reject-provider&user_id=" . $u->ID ), "nekoko_provider_action" );
        echo "<tr><td>".esc_html($u->display_name)."</td><td>".esc_html($u->user_email)."</td><td>".esc_html(date_i18n("d.m.Y",strtotime($u->user_registered)))."</td><td>".esc_html($st)."</td><td><a href=\"".esc_url($app)."\" class=\"button button-primary\" style=\"margin-right:8px;\">Odobri</a> <a href=\"".esc_url($rej)."\" class=\"button\">Odbij</a></td></tr>";
    }
    echo "</tbody></table></div>";
}

function nekoko_pending_listings_page() {
    if ( isset( $_GET["action"], $_GET["post_id"], $_GET["_wpnonce"] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET["_wpnonce"] ) ), "nekoko_listing_action" ) ) {
        $pid  = intval( $_GET["post_id"] );
        $act  = sanitize_text_field( wp_unslash( $_GET["action"] ) );
        $lst  = get_post( $pid );
        $hdrs = [ "Content-Type: text/html; charset=UTF-8", "From: NekoKo <support@nekoko.rs>" ];
        if ( $act === "approve-listing" ) {
            wp_update_post( [ "ID" => $pid, "post_status" => "publish" ] );
            update_post_meta( $pid, "_nekoko_listing_status", "approved" );
            $prov = get_user_by( "id", $lst->post_author );
            if ( $prov ) wp_mail( $prov->user_email, "Usluga odobrena - " . $lst->post_title, nekoko_email_template( "listing-approved", [ "provider_name" => $prov->display_name, "listing_title" => $lst->post_title, "listing_url" => get_permalink($pid) ] ), $hdrs );
            echo "<div class=\"notice notice-success\"><p>Usluga odobrena!</p></div>";
        } elseif ( $act === "reject-listing" ) {
            wp_update_post( [ "ID" => $pid, "post_status" => "draft" ] );
            update_post_meta( $pid, "_nekoko_listing_status", "rejected" );
            $prov = get_user_by( "id", $lst->post_author );
            if ( $prov ) wp_mail( $prov->user_email, "Usluga odbijena - " . $lst->post_title, nekoko_email_template( "listing-rejected", [ "provider_name" => $prov->display_name, "listing_title" => $lst->post_title, "reason" => "" ] ), $hdrs );
            echo "<div class=\"notice notice-error\"><p>Usluga odbijena.</p></div>";
        }
    }
    $listings = get_posts( [ "post_type" => "service_listing", "post_status" => [ "pending", "draft" ], "numberposts" => -1 ] );
    echo "<div class=\"wrap\"><h1>Usluge pending</h1><table class=\"widefat striped\"><thead><tr><th>Naziv</th><th>Provajder</th><th>Kategorija</th><th>Datum</th><th>Akcije</th></tr></thead><tbody>";
    foreach ( $listings as $l ) {
        $prov = get_user_by( "id", $l->post_author );
        $cats = get_the_terms( $l->ID, "service_category" );
        $cat  = $cats && !is_wp_error($cats) ? implode(", ",wp_list_pluck($cats,"name")) : "-";
        $app  = wp_nonce_url( admin_url( "admin.php?page=nekoko-pending-listings&action=approve-listing&post_id=" . $l->ID ), "nekoko_listing_action" );
        $rej  = wp_nonce_url( admin_url( "admin.php?page=nekoko-pending-listings&action=reject-listing&post_id=" . $l->ID ), "nekoko_listing_action" );
        echo "<tr><td><a href=\"".esc_url(get_edit_post_link($l->ID))."\">".esc_html($l->post_title)."</a></td><td>".esc_html($prov?$prov->display_name:"-")."</td><td>".esc_html($cat)."</td><td>".esc_html(date_i18n("d.m.Y",strtotime($l->post_date)))."</td><td><a href=\"".esc_url($app)."\" class=\"button button-primary\" style=\"margin-right:8px;\">Odobri</a> <a href=\"".esc_url($rej)."\" class=\"button\">Odbij</a></td></tr>";
    }
    echo "</tbody></table></div>";
}

// US 4.3 - Listings Grid Shortcode
add_shortcode( "nekoko_listings", "nekoko_listings_sc" );
function nekoko_listings_sc( $atts ) {
    $atts = shortcode_atts( [ "limit" => 6, "category" => "", "columns" => 3 ], $atts );
    $args = [ "post_type" => "service_listing", "post_status" => "publish", "posts_per_page" => intval( $atts["limit"] ) ];
    if ( $atts["category"] ) {
        $args["tax_query"] = [ [ "taxonomy" => "service_category", "field" => "slug", "terms" => sanitize_text_field( $atts["category"] ) ] ];
    }
    $query = new WP_Query( $args );
    if ( ! $query->have_posts() ) return "<p>Nema dostupnih usluga.</p>";
    ob_start();
    echo "<div class=\"listing-grid\">";
    while ( $query->have_posts() ) {
        $query->the_post();
        $cats  = get_the_terms( get_the_ID(), "service_category" );
        $cat   = $cats && !is_wp_error($cats) ? esc_html($cats[0]->name) : "";
        $avg   = (float) get_post_meta( get_the_ID(), "_nekoko_avg_rating", true );
        $count = (int) get_post_meta( get_the_ID(), "_nekoko_review_count", true );
        echo "<div class=\"listing-card\">";
        if ( has_post_thumbnail() ) echo "<div class=\"listing-image\"><a href=\"".esc_url(get_permalink())."\">".get_the_post_thumbnail( null, "medium" )."</a></div>";
        echo "<div class=\"listing-body\">";
        if ( $cat ) echo "<span class=\"category-badge\">".esc_html($cat)."</span>";
        echo "<h3 class=\"listing-title\"><a href=\"".esc_url(get_permalink())."\">".esc_html(get_the_title())."</a></h3>";
        if ( $avg ) {
            echo "<div class=\"star-rating\">";
            for ( $i = 1; $i <= 5; $i++ ) echo "<span class=\"star ".($i<=round($avg)?"filled":"")."\">&#9733;</span>";
            echo "<span class=\"rating-count\">(".esc_html($count).")</span></div>";
        }
        echo "<p class=\"listing-excerpt\">".esc_html(wp_trim_words(get_the_excerpt(),20))."</p>";
        echo "<a href=\"".esc_url(get_permalink())."\" class=\"nekoko-btn\">Pogledaj uslugu</a>";
        echo "</div></div>";
    }
    wp_reset_postdata();
    echo "</div>";
    return ob_get_clean();
}

// US 4.2 - Homepage Shortcode
add_shortcode( "nekoko_homepage", "nekoko_homepage_sc" );
function nekoko_homepage_sc() {
    $cats  = get_terms( [ "taxonomy" => "service_category", "hide_empty" => false ] );
    $icons = [ "Umetnost" => "A", "Lepota" => "B", "Zdravlje" => "Z", "Zivotinje" => "P", "Zabava" => "E", "Ostalo" => "S" ];
    ob_start();
    echo "<section class=\"nekoko-hero\"><div style=\"max-width:800px;margin:0 auto;\"><h1>Pronadji unikatne usluge u Srbiji</h1><p>NekoKo spaja ljude koji nude nisne talente i usluge sa onima koji ih traze. Sve na jednom mestu.</p><div class=\"hero-cta\"><a href=\"".esc_url(home_url("/listing/"))."\" class=\"nekoko-btn-accent\">Istrazuji usluge</a>";
    if ( ! is_user_logged_in() ) echo " <a href=\"".esc_url(wp_registration_url())."\" class=\"nekoko-btn-outline\" style=\"color:#fff!important;border-color:#fff;\">Postani provajder</a>";
    echo "</div></div></section><section class=\"nekoko-categories\"><div style=\"max-width:1200px;margin:0 auto;padding:0 20px;\"><h2>Kategorije usluga</h2>";
    if ( !is_wp_error($cats) && $cats ) { echo "<div class=\"categories-grid\">"; foreach ($cats as $cat) echo "<a href=\"".esc_url(get_term_link($cat))."\" class=\"category-card\"><span class=\"icon\">".esc_html($cat->name)."</span><h3>".esc_html($cat->name)."</h3></a>"; echo "</div>"; }
    echo "</div></section>";
    echo "<section class=\"nekoko-listings-section\" style=\"padding:80px 20px;background:#fff;\"><div style=\"max-width:1200px;margin:0 auto;\"><h2 style=\"text-align:center;font-size:2rem;margin-bottom:48px;\">Istaknute usluge</h2>";
    echo nekoko_listings_sc( [ "limit" => 6 ] );
    echo "<div style=\"text-align:center;margin-top:40px;\"><a href=\"".esc_url(home_url("/listing/"))."\" class=\"nekoko-btn\">Pogledaj sve usluge</a></div>";
    echo "</div></section>";
    echo "<section class=\"nekoko-how-it-works\"><div style=\"max-width:1200px;margin:0 auto;padding:0 20px;\"><h2>Kako funkcionise?</h2><div class=\"steps-grid\"><div class=\"step-card\"><div class=\"step-number\">1</div><h3>Pronadji uslugu</h3><p>Pretrazi nisne usluge po kategoriji.</p></div><div class=\"step-card\"><div class=\"step-number\">2</div><h3>Kontaktiraj provajdera</h3><p>Posalji zahtev za rezervaciju direktno provajderu.</p></div><div class=\"step-card\"><div class=\"step-number\">3</div><h3>Dogovorite se direktno</h3><p>Cenu i detalje dogovarate direktno, bez provizije.</p></div></div></div></section>";
    return ob_get_clean();
}
