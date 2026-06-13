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


// US 4.4 - Mandatory registration checkboxes
add_action( "register_form", "nekoko_registration_checkboxes" );
function nekoko_registration_checkboxes() {
    $tos_url     = esc_url( home_url( "/uslovi-koriscenja/" ) );
    $privacy_url = esc_url( home_url( "/politika-privatnosti/" ) );
    echo '<div style="margin:16px 0;font-size:14px;line-height:1.6;">';
    echo '<label style="display:flex;gap:10px;align-items:flex-start;margin-bottom:12px;">';
    echo '<input type="checkbox" name="nekoko_accept_terms" value="1" style="margin-top:3px;flex-shrink:0;">';
    echo '<span>Prihvatam <a href="' . $tos_url . '" target="_blank">Uslove korišćenja</a> i <a href="' . $privacy_url . '" target="_blank">Politiku privatnosti</a> platforme NekoKo.rs.</span>';
    echo '</label>';
    echo '<label style="display:flex;gap:10px;align-items:flex-start;">';
    echo '<input type="checkbox" name="nekoko_accept_disclaimer" value="1" style="margin-top:3px;flex-shrink:0;">';
    echo '<span>Razumem da NekoKo.rs ne proverava kvalifikacije provajdera, ne posreduje u sporovima i ne procesira plaćanja.</span>';
    echo '</label>';
    echo '</div>';
    wp_nonce_field( "nekoko_registration", "nekoko_reg_nonce" );
}

add_filter( "registration_errors", "nekoko_validate_registration_checkboxes", 10, 3 );
function nekoko_validate_registration_checkboxes( $errors, $sanitized_user_login, $user_email ) {
    if ( empty( $_POST["nekoko_reg_nonce"] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST["nekoko_reg_nonce"] ) ), "nekoko_registration" ) ) {
        $errors->add( "checkbox_error", __( "Greška pri verifikaciji forme. Pokušaj ponovo.", "nekoko-child" ) );
        return $errors;
    }
    if ( empty( $_POST["nekoko_accept_terms"] ) ) {
        $errors->add( "terms_error", __( "Morate prihvatiti Uslove korišćenja i Politiku privatnosti.", "nekoko-child" ) );
    }
    if ( empty( $_POST["nekoko_accept_disclaimer"] ) ) {
        $errors->add( "disclaimer_error", __( "Morate potvrditi da razumete uslove korišćenja platforme.", "nekoko-child" ) );
    }
    return $errors;
}

add_action( "user_register", "nekoko_save_registration_consent" );
function nekoko_save_registration_consent( $user_id ) {
    if ( ! empty( $_POST["nekoko_accept_terms"] ) ) {
        update_user_meta( $user_id, "_nekoko_accepted_terms", current_time( "mysql" ) );
    }
    if ( ! empty( $_POST["nekoko_accept_disclaimer"] ) ) {
        update_user_meta( $user_id, "_nekoko_accepted_disclaimer", current_time( "mysql" ) );
    }
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
    echo "<div class=\"wrap\"><h1>Provajderi pending</h1><table class=\"widefat striped\"><thead><tr><th>Ime</th><th>Email</th><th>Opis usluga</th><th>Registrovan</th><th>Status</th><th>Akcije</th></tr></thead><tbody>";
    foreach ( $users as $u ) {
        $st   = get_user_meta( $u->ID, "_nekoko_provider_status", true ) ?: "pending";
        $desc = get_user_meta( $u->ID, "_nekoko_service_description", true ) ?: "-";
        $app  = wp_nonce_url( admin_url( "admin.php?page=nekoko-pending-providers&action=approve-provider&user_id=" . $u->ID ), "nekoko_provider_action" );
        $rej  = wp_nonce_url( admin_url( "admin.php?page=nekoko-pending-providers&action=reject-provider&user_id=" . $u->ID ), "nekoko_provider_action" );
        echo "<tr><td>".esc_html($u->display_name)."</td><td>".esc_html($u->user_email)."</td><td style=\"max-width:220px;\">".esc_html(mb_substr($desc,0,100)).(mb_strlen($desc)>100?"…":"")."</td><td>".esc_html(date_i18n("d.m.Y",strtotime($u->user_registered)))."</td><td>".esc_html($st)."</td><td><a href=\"".esc_url($app)."\" class=\"button button-primary\" style=\"margin-right:8px;\">Odobri</a> <a href=\"".esc_url($rej)."\" class=\"button\">Odbij</a></td></tr>";
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

// US 4.3 - Search & Filter Shortcode [nekoko_search_filter]
add_shortcode( 'nekoko_search_filter', 'nekoko_search_filter_sc' );
function nekoko_search_filter_sc() {
    $kw        = sanitize_text_field( wp_unslash( $_GET['kw']        ?? '' ) );
    $kat       = sanitize_text_field( wp_unslash( $_GET['kat']       ?? '' ) );
    $grad      = sanitize_text_field( wp_unslash( $_GET['grad']      ?? '' ) );
    $cena_min  = isset( $_GET['cena_min'] ) && $_GET['cena_min'] !== '' ? intval( $_GET['cena_min'] ) : '';
    $cena_max  = isset( $_GET['cena_max'] ) && $_GET['cena_max'] !== '' ? intval( $_GET['cena_max'] ) : '';
    $min_ocena = intval( $_GET['min_ocena'] ?? 0 );
    $sortiraj  = sanitize_key( $_GET['sortiraj'] ?? 'newest' );

    $args = [ 'post_type' => 'service_listing', 'post_status' => 'publish', 'posts_per_page' => 12 ];
    if ( $kw ) { $args['s'] = $kw; }
    if ( $kat ) { $args['tax_query'] = [[ 'taxonomy' => 'service_category', 'field' => 'slug', 'terms' => $kat ]]; }

    $mq = [];
    if ( $grad )    { $mq[] = [ 'key' => '_nekoko_grad',       'value' => $grad ]; }
    if ( $cena_min !== '' ) { $mq[] = [ 'key' => '_nekoko_price', 'value' => $cena_min, 'type' => 'NUMERIC', 'compare' => '>=' ]; }
    if ( $cena_max !== '' ) { $mq[] = [ 'key' => '_nekoko_price', 'value' => $cena_max, 'type' => 'NUMERIC', 'compare' => '<=' ]; }
    if ( $min_ocena > 0 )  { $mq[] = [ 'key' => '_nekoko_avg_rating', 'value' => $min_ocena, 'type' => 'DECIMAL(3,1)', 'compare' => '>=' ]; }
    if ( $mq ) { $args['meta_query'] = array_merge( [ 'relation' => 'AND' ], $mq ); }

    switch ( $sortiraj ) {
        case 'cena_asc':  $args['meta_key'] = '_nekoko_price'; $args['orderby'] = 'meta_value_num'; $args['order'] = 'ASC'; break;
        case 'cena_desc': $args['meta_key'] = '_nekoko_price'; $args['orderby'] = 'meta_value_num'; $args['order'] = 'DESC'; break;
        case 'ocena':     $args['meta_key'] = '_nekoko_avg_rating'; $args['orderby'] = 'meta_value_num'; $args['order'] = 'DESC'; break;
        default:          $args['orderby'] = 'date'; $args['order'] = 'DESC';
    }

    $query   = new WP_Query( $args );
    $cats    = get_terms( [ 'taxonomy' => 'service_category', 'hide_empty' => false ] );
    $gradovi = [ 'Beograd', 'Novi Sad', 'Niš', 'Kragujevac', 'Online' ];
    $page_url = esc_url( get_permalink() );

    ob_start(); ?>
    <div class="nekoko-search-wrap" style="max-width:1200px;margin:0 auto;padding:0 20px;">
    <form method="get" action="<?php echo $page_url; ?>" style="background:#fff;border-radius:12px;padding:24px;box-shadow:var(--nekoko-shadow);margin-bottom:32px;">
        <div style="display:grid;grid-template-columns:2fr 1fr 1fr 1fr 1fr auto;gap:12px;align-items:end;flex-wrap:wrap;">
            <div><label style="font-size:.8rem;font-weight:600;color:#666;display:block;margin-bottom:4px;">Pretraži</label>
                <input type="text" name="kw" value="<?php echo esc_attr($kw); ?>" placeholder="Npr. masaža, fotograf..." style="width:100%;padding:10px 14px;border:2px solid var(--nekoko-light);border-radius:8px;font-size:.9rem;box-sizing:border-box;font-family:var(--nekoko-font);">
            </div>
            <div><label style="font-size:.8rem;font-weight:600;color:#666;display:block;margin-bottom:4px;">Kategorija</label>
                <select name="kat" style="width:100%;padding:10px 14px;border:2px solid var(--nekoko-light);border-radius:8px;font-size:.9rem;box-sizing:border-box;font-family:var(--nekoko-font);">
                    <option value="">Sve kategorije</option>
                    <?php foreach ( $cats as $c ) : ?>
                    <option value="<?php echo esc_attr($c->slug); ?>" <?php selected($kat,$c->slug); ?>><?php echo esc_html($c->name); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div><label style="font-size:.8rem;font-weight:600;color:#666;display:block;margin-bottom:4px;">Grad</label>
                <select name="grad" style="width:100%;padding:10px 14px;border:2px solid var(--nekoko-light);border-radius:8px;font-size:.9rem;box-sizing:border-box;font-family:var(--nekoko-font);">
                    <option value="">Svi gradovi</option>
                    <?php foreach ( $gradovi as $g ) : ?>
                    <option value="<?php echo esc_attr($g); ?>" <?php selected($grad,$g); ?>><?php echo esc_html($g); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div><label style="font-size:.8rem;font-weight:600;color:#666;display:block;margin-bottom:4px;">Cena (RSD)</label>
                <div style="display:flex;gap:6px;">
                    <input type="number" name="cena_min" value="<?php echo esc_attr($cena_min); ?>" placeholder="Od" min="0" style="width:50%;padding:10px 8px;border:2px solid var(--nekoko-light);border-radius:8px;font-size:.85rem;box-sizing:border-box;">
                    <input type="number" name="cena_max" value="<?php echo esc_attr($cena_max); ?>" placeholder="Do" min="0" style="width:50%;padding:10px 8px;border:2px solid var(--nekoko-light);border-radius:8px;font-size:.85rem;box-sizing:border-box;">
                </div>
            </div>
            <div><label style="font-size:.8rem;font-weight:600;color:#666;display:block;margin-bottom:4px;">Min. ocena</label>
                <select name="min_ocena" style="width:100%;padding:10px 14px;border:2px solid var(--nekoko-light);border-radius:8px;font-size:.9rem;box-sizing:border-box;font-family:var(--nekoko-font);">
                    <option value="0">Sve ocene</option>
                    <?php for ( $i = 1; $i <= 5; $i++ ) : ?>
                    <option value="<?php echo $i; ?>" <?php selected($min_ocena,$i); ?>><?php echo str_repeat('★',$i); ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div><button type="submit" class="nekoko-btn" style="white-space:nowrap;padding:10px 20px;">Pretraži</button></div>
        </div>
        <div style="margin-top:12px;display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
            <span style="font-size:.8rem;color:#666;">Sortiraj:</span>
            <?php $sorts = [ 'newest' => 'Najnovije', 'cena_asc' => 'Cena ↑', 'cena_desc' => 'Cena ↓', 'ocena' => 'Ocena ↓' ];
            foreach ( $sorts as $val => $lbl ) :
                $active = $sortiraj === $val;
                $url = add_query_arg( array_filter( ['kw'=>$kw,'kat'=>$kat,'grad'=>$grad,'cena_min'=>$cena_min,'cena_max'=>$cena_max,'min_ocena'=>$min_ocena?$min_ocena:null,'sortiraj'=>$val] ), $page_url ); ?>
            <a href="<?php echo esc_url($url); ?>" style="font-size:.85rem;padding:4px 12px;border-radius:20px;border:1px solid <?php echo $active?'var(--nekoko-blue)':'var(--nekoko-light)'; ?>;background:<?php echo $active?'var(--nekoko-blue)':'transparent'; ?>;color:<?php echo $active?'#fff':'var(--nekoko-dark)'; ?>;text-decoration:none;"><?php echo esc_html($lbl); ?></a>
            <?php endforeach; ?>
            <?php if ( $kw || $kat || $grad || $cena_min !== '' || $cena_max !== '' || $min_ocena ) : ?>
            <a href="<?php echo $page_url; ?>" style="font-size:.85rem;color:var(--nekoko-red);margin-left:auto;">× Resetuj filtere</a>
            <?php endif; ?>
        </div>
    </form>

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <p style="margin:0;color:#666;font-size:.9rem;">Pronađeno: <strong><?php echo $query->found_posts; ?></strong> usluga<?php if($kw) echo ' za "<em>'.esc_html($kw).'</em>"'; ?></p>
    </div>

    <?php if ( $query->have_posts() ) : ?>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:24px;">
        <?php while ( $query->have_posts() ) : $query->the_post();
            $c_cats  = get_the_terms( get_the_ID(), 'service_category' );
            $c_cat   = $c_cats && !is_wp_error($c_cats) ? esc_html($c_cats[0]->name) : '';
            $c_avg   = (float) get_post_meta( get_the_ID(), '_nekoko_avg_rating', true );
            $c_cnt   = (int)   get_post_meta( get_the_ID(), '_nekoko_review_count', true );
            $c_price = (int)   get_post_meta( get_the_ID(), '_nekoko_price', true );
            $c_grad  = get_post_meta( get_the_ID(), '_nekoko_grad', true );
        ?>
        <article class="listing-card">
            <?php if ( has_post_thumbnail() ) : ?>
            <a href="<?php the_permalink(); ?>"><img class="card-image" src="<?php the_post_thumbnail_url('medium'); ?>" alt="<?php the_title_attribute(); ?>"></a>
            <?php else : ?>
            <a href="<?php the_permalink(); ?>"><div class="card-image" style="background:var(--nekoko-light);display:flex;align-items:center;justify-content:center;font-size:3rem;">🏷️</div></a>
            <?php endif; ?>
            <div class="card-body">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                    <?php if($c_cat) : ?><div class="card-category"><?php echo $c_cat; ?></div><?php endif; ?>
                    <?php if($c_grad) : ?><span style="font-size:.75rem;color:#666;">📍 <?php echo esc_html($c_grad); ?></span><?php endif; ?>
                </div>
                <h3><a href="<?php the_permalink(); ?>" style="color:var(--nekoko-dark);"><?php the_title(); ?></a></h3>
                <?php if($c_avg) : ?>
                <div class="star-rating" style="margin-bottom:8px;">
                    <?php for($i=1;$i<=5;$i++) echo '<span class="star '.($i<=round($c_avg)?'filled':'').'">&#9733;</span>'; ?>
                    <span style="color:#666;font-size:.8rem;">(<?php echo $c_cnt; ?>)</span>
                </div>
                <?php endif; ?>
                <?php if($c_price) : ?>
                <div style="font-size:1rem;font-weight:700;color:var(--nekoko-blue);margin-bottom:12px;"><?php echo number_format($c_price,0,'.','.'); ?> RSD</div>
                <?php endif; ?>
                <a href="<?php the_permalink(); ?>" class="nekoko-btn" style="font-size:.85rem;padding:8px 18px;">Pogledaj</a>
            </div>
        </article>
        <?php endwhile; wp_reset_postdata(); ?>
    </div>
    <?php else : ?>
    <div style="text-align:center;padding:80px 0;background:#fff;border-radius:12px;">
        <p style="font-size:1.2rem;color:#666;margin-bottom:20px;">Nema rezultata za ove filtere.</p>
        <a href="<?php echo $page_url; ?>" class="nekoko-btn">Resetuj pretragu</a>
    </div>
    <?php endif; ?>
    </div>
    <?php return ob_get_clean();
}

// US 4.3 - Provider Registration Shortcode [nekoko_provider_registration]
add_shortcode( 'nekoko_provider_registration', 'nekoko_provider_registration_sc' );
function nekoko_provider_registration_sc() {
    if ( is_user_logged_in() ) {
        $user = wp_get_current_user();
        if ( in_array( 'provider', (array) $user->roles, true ) ) {
            return '<div style="background:#d1e7dd;padding:24px;border-radius:8px;"><p style="margin:0;">Već ste prijavljeni kao pružalac usluga. <a href="' . esc_url( home_url( '/provider-dashboard/' ) ) . '">Idite na dashboard</a>.</p></div>';
        }
        return '<div style="background:#fff3cd;padding:24px;border-radius:8px;"><p style="margin:0;">Već ste prijavljeni. <a href="' . esc_url( wp_logout_url( get_permalink() ) ) . '">Odjavite se</a> da biste kreirali provajder nalog.</p></div>';
    }
    $errors  = [];
    $success = false;
    $vals    = [ 'first_name' => '', 'last_name' => '', 'email' => '', 'description' => '' ];
    if ( isset( $_POST['nekoko_provider_submit'] ) ) {
        if ( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'nekoko_provider_reg' ) ) {
            $errors[] = 'Greška pri verifikaciji forme. Pokušaj ponovo.';
        } else {
            $first  = sanitize_text_field( wp_unslash( $_POST['nekoko_first_name']  ?? '' ) );
            $last   = sanitize_text_field( wp_unslash( $_POST['nekoko_last_name']   ?? '' ) );
            $email  = sanitize_email( wp_unslash( $_POST['nekoko_email']            ?? '' ) );
            $pass   = wp_unslash( $_POST['nekoko_password']  ?? '' );
            $pass2  = wp_unslash( $_POST['nekoko_password2'] ?? '' );
            $desc   = sanitize_textarea_field( wp_unslash( $_POST['nekoko_description'] ?? '' ) );
            $vals   = compact( 'first_name', 'last_name', 'email', 'description' ) + [ 'first_name' => $first, 'last_name' => $last, 'email' => $email, 'description' => $desc ];
            if ( ! $first )                   $errors[] = 'Ime je obavezno.';
            if ( ! $last )                    $errors[] = 'Prezime je obavezno.';
            if ( ! is_email( $email ) )       $errors[] = 'Unesite ispravnu email adresu.';
            elseif ( email_exists( $email ) ) $errors[] = 'Nalog sa ovom email adresom već postoji.';
            if ( strlen( $pass ) < 8 )        $errors[] = 'Lozinka mora imati najmanje 8 karaktera.';
            if ( $pass !== $pass2 )           $errors[] = 'Lozinke se ne poklapaju.';
            if ( ! $desc )                    $errors[] = 'Opis usluga je obavezan.';
            if ( empty( $_POST['nekoko_accept_terms'] ) )      $errors[] = 'Morate prihvatiti Uslove korišćenja i Politiku privatnosti.';
            if ( empty( $_POST['nekoko_accept_disclaimer'] ) ) $errors[] = 'Morate potvrditi da razumete uslove korišćenja platforme.';
            if ( empty( $errors ) ) {
                $base_uname = sanitize_user( strtolower( $first . '.' . $last ), true );
                $uname = $base_uname;
                for ( $i = 1; username_exists( $uname ); $i++ ) { $uname = $base_uname . $i; }
                $uid = wp_create_user( $uname, $pass, $email );
                if ( is_wp_error( $uid ) ) {
                    $errors[] = $uid->get_error_message();
                } else {
                    $u = new WP_User( $uid );
                    $u->set_role( 'provider' );
                    wp_update_user( [ 'ID' => $uid, 'display_name' => $first . ' ' . $last ] );
                    update_user_meta( $uid, 'first_name', $first );
                    update_user_meta( $uid, 'last_name', $last );
                    update_user_meta( $uid, '_nekoko_provider_status', 'pending' );
                    update_user_meta( $uid, '_nekoko_service_description', $desc );
                    update_user_meta( $uid, '_nekoko_accepted_terms', current_time( 'mysql' ) );
                    update_user_meta( $uid, '_nekoko_accepted_disclaimer', current_time( 'mysql' ) );
                    $success = true;
                }
            }
        }
    }
    if ( $success ) {
        return '<div style="background:#d1e7dd;padding:32px;border-radius:12px;text-align:center;max-width:600px;margin:0 auto;"><h2 style="color:#0f5132;margin-top:0;">Zahtev primljen!</h2><p>Vaš zahtev za registraciju kao pružalac usluga je primljen i čeka odobrenje administratora.</p><p>Bićete obavešteni čim Vaš profil bude odobren. Možete se prijaviti na NekoKo.rs i početi sa radom.</p></div>';
    }
    $tos_url     = esc_url( home_url( '/uslovi-koriscenja/' ) );
    $privacy_url = esc_url( home_url( '/politika-privatnosti/' ) );
    ob_start(); ?>
    <div class="nekoko-form" style="max-width:600px;margin:0 auto;">
    <?php if ( $errors ) : ?>
    <div style="background:#f8d7da;padding:16px;border-radius:8px;margin-bottom:24px;"><ul style="margin:0;padding-left:20px;"><?php foreach($errors as $e) echo '<li>'.esc_html($e).'</li>'; ?></ul></div>
    <?php endif; ?>
    <form method="post">
        <?php wp_nonce_field( 'nekoko_provider_reg' ); ?>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="form-group"><label>Ime *</label><input type="text" name="nekoko_first_name" value="<?php echo esc_attr($vals['first_name']); ?>" required placeholder="Vaše ime"></div>
            <div class="form-group"><label>Prezime *</label><input type="text" name="nekoko_last_name" value="<?php echo esc_attr($vals['last_name']); ?>" required placeholder="Vaše prezime"></div>
        </div>
        <div class="form-group"><label>Email adresa *</label><input type="email" name="nekoko_email" value="<?php echo esc_attr($vals['email']); ?>" required placeholder="vas@email.com"></div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="form-group"><label>Lozinka * <span style="font-size:.8rem;color:#666;">(min. 8 karaktera)</span></label><input type="password" name="nekoko_password" required placeholder="••••••••" autocomplete="new-password"></div>
            <div class="form-group"><label>Potvrda lozinke *</label><input type="password" name="nekoko_password2" required placeholder="••••••••" autocomplete="new-password"></div>
        </div>
        <div class="form-group">
            <label>Opis usluga koje nudite *</label>
            <textarea name="nekoko_description" rows="5" required placeholder="Opišite koje usluge nudite, vaše iskustvo i šta vas izdvaja..."><?php echo esc_textarea($vals['description']); ?></textarea>
            <span style="font-size:.8rem;color:#666;">Vidljivo samo administratoru pre odobrenja profila.</span>
        </div>
        <div style="margin:20px 0;font-size:14px;line-height:1.6;">
            <label style="display:flex;gap:10px;align-items:flex-start;margin-bottom:12px;cursor:pointer;">
                <input type="checkbox" name="nekoko_accept_terms" value="1" style="margin-top:3px;flex-shrink:0;" <?php checked(!empty($_POST['nekoko_accept_terms']),'1'); ?>>
                <span>Prihvatam <a href="<?php echo $tos_url; ?>" target="_blank">Uslove korišćenja</a> i <a href="<?php echo $privacy_url; ?>" target="_blank">Politiku privatnosti</a> platforme NekoKo.rs.</span>
            </label>
            <label style="display:flex;gap:10px;align-items:flex-start;cursor:pointer;">
                <input type="checkbox" name="nekoko_accept_disclaimer" value="1" style="margin-top:3px;flex-shrink:0;" <?php checked(!empty($_POST['nekoko_accept_disclaimer']),'1'); ?>>
                <span>Razumem da NekoKo.rs ne proverava kvalifikacije provajdera, ne posreduje u sporovima i ne procesira plaćanja. Nudim usluge kao nezavisni izvođač.</span>
            </label>
        </div>
        <button type="submit" name="nekoko_provider_submit" class="nekoko-btn" style="width:100%;padding:14px;">Pošalji zahtev za registraciju</button>
    </form>
    <p style="text-align:center;margin-top:16px;font-size:.9rem;color:#666;">Već imate nalog? <a href="<?php echo esc_url(wp_login_url()); ?>">Prijavite se</a></p>
    </div>
    <?php return ob_get_clean();
}

// US 4.2 - Homepage Shortcode
add_shortcode( "nekoko_homepage", "nekoko_homepage_sc" );
function nekoko_homepage_sc() {
    $cats  = get_terms( [ "taxonomy" => "service_category", "hide_empty" => false ] );
    $icons = [ "Umetnost" => "🎨", "Lepota" => "💄", "Zdravlje" => "💪", "Životinje" => "🐾", "Zivotinje" => "🐾", "Zabava" => "🎉", "Ostalo" => "⚙️" ];
    ob_start();
    echo "<section class=\"nekoko-hero\"><div style=\"max-width:800px;margin:0 auto;\"><h1>Pronadji unikatne usluge u Srbiji</h1><p>NekoKo spaja ljude koji nude nisne talente i usluge sa onima koji ih traze. Sve na jednom mestu.</p><div class=\"hero-cta\"><a href=\"".esc_url(home_url("/listing/"))."\" class=\"nekoko-btn-accent\">Istrazuji usluge</a>";
    if ( ! is_user_logged_in() ) echo " <a href=\"".esc_url(home_url('/postani-provajder/'))."\" class=\"nekoko-btn-outline\" style=\"color:#fff!important;border-color:#fff;\">Postani provajder</a>";
    echo "</div></div></section><section class=\"nekoko-categories\"><div style=\"max-width:1200px;margin:0 auto;padding:0 20px;\"><h2>Kategorije usluga</h2>";
    if ( !is_wp_error($cats) && $cats ) { echo "<div class=\"categories-grid\">"; foreach ($cats as $cat) { $icon = $icons[ $cat->name ] ?? "🔧"; echo "<a href=\"".esc_url(get_term_link($cat))."\" class=\"category-card\"><span class=\"icon\">".esc_html($icon)."</span><h3>".esc_html($cat->name)."</h3></a>"; } echo "</div>"; }
    echo "</div></section>";
    echo "<section class=\"nekoko-listings-section\" style=\"padding:80px 20px;background:#fff;\"><div style=\"max-width:1200px;margin:0 auto;\"><h2 style=\"text-align:center;font-size:2rem;margin-bottom:48px;\">Istaknute usluge</h2>";
    echo nekoko_listings_sc( [ "limit" => 6 ] );
    echo "<div style=\"text-align:center;margin-top:40px;\"><a href=\"".esc_url(home_url("/listing/"))."\" class=\"nekoko-btn\">Pogledaj sve usluge</a></div>";
    echo "</div></section>";
    echo "<section class=\"nekoko-how-it-works\"><div style=\"max-width:1200px;margin:0 auto;padding:0 20px;\"><h2>Kako funkcionise?</h2><div class=\"steps-grid\"><div class=\"step-card\"><div class=\"step-number\">1</div><h3>Pronadji uslugu</h3><p>Pretrazi nisne usluge po kategoriji.</p></div><div class=\"step-card\"><div class=\"step-number\">2</div><h3>Kontaktiraj provajdera</h3><p>Posalji zahtev za rezervaciju direktno provajderu.</p></div><div class=\"step-card\"><div class=\"step-number\">3</div><h3>Dogovorite se direktno</h3><p>Cenu i detalje dogovarate direktno, bez provizije.</p></div></div></div></section>";
    return ob_get_clean();
}
