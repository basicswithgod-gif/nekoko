<?php
/**
 * NekoKo Seed Data Script — US 5.2
 * Run via WP-CLI: wp eval-file wp-content/nekoko-seed.php
 * Creates 5 providers, 3 customers, 30 listings (5 per category).
 */
defined( "ABSPATH" ) || exit;

echo "Starting NekoKo seed data...\n";

$password = wp_hash_password( "TestPass123!" );

// Create test providers
$providers = [];
$provider_data = [
    [ "login" => "marija.petrovic", "email" => "marija.test@nekoko.rs", "name" => "Marija Petrovic", "bio" => "Umetnica i ilustratorka sa 5 godina iskustva." ],
    [ "login" => "nikola.jovic",    "email" => "nikola.test@nekoko.rs",  "name" => "Nikola Jovic",    "bio" => "Fotograf i video producent." ],
    [ "login" => "ana.stojanovic",  "email" => "ana.test@nekoko.rs",     "name" => "Ana Stojanovic",  "bio" => "Kozmeticar i stilista." ],
    [ "login" => "stefan.milic",    "email" => "stefan.test@nekoko.rs",  "name" => "Stefan Milic",    "bio" => "Terapeut masaze i wellness instruktor." ],
    [ "login" => "jovana.ilic",     "email" => "jovana.test@nekoko.rs",  "name" => "Jovana Ilic",     "bio" => "Trener za ljubimce i ponasanje zivotinja." ],
];

foreach ( $provider_data as $d ) {
    $existing = get_user_by( "login", $d["login"] );
    if ( $existing ) {
        $providers[] = $existing->ID;
        echo "Provider exists: {$d["login"]}\n";
        continue;
    }
    $uid = wp_insert_user([
        "user_login"   => $d["login"],
        "user_email"   => $d["email"],
        "display_name" => $d["name"],
        "user_pass"    => "TestPass123!",
        "role"         => "provider",
        "description"  => $d["bio"],
    ]);
    if ( ! is_wp_error( $uid ) ) {
        update_user_meta( $uid, "_nekoko_provider_status", "approved" );
        $providers[] = $uid;
        echo "Created provider: {$d["name"]} (ID: $uid)\n";
    } else {
        echo "Error creating {$d["name"]}: " . $uid->get_error_message() . "\n";
    }
}

// Create test customers
$customer_data = [
    [ "login" => "petar.test",  "email" => "petar.test@nekoko.rs",  "name" => "Petar Rankovic" ],
    [ "login" => "milena.test", "email" => "milena.test@nekoko.rs", "name" => "Milena Savic" ],
    [ "login" => "dejan.test",  "email" => "dejan.test@nekoko.rs",  "name" => "Dejan Popovic" ],
];
foreach ( $customer_data as $d ) {
    if ( ! get_user_by( "login", $d["login"] ) ) {
        $uid = wp_insert_user([ "user_login" => $d["login"], "user_email" => $d["email"], "display_name" => $d["name"], "user_pass" => "TestPass123!", "role" => "customer" ]);
        if ( ! is_wp_error( $uid ) ) echo "Created customer: {$d["name"]} (ID: $uid)\n";
    } else { echo "Customer exists: {$d["login"]}\n"; }
}

// Listing data per category
$listings_per_category = [
    "Umetnost"  => [
        [ "title" => "Crtanje portreta po narudzbini", "content" => "Crtam portrete olovkom i ugljenom. Rok isporuke 7 dana. Moguca dostava." ],
        [ "title" => "Akvarel slikanje za sve prilike", "content" => "Originalni akvareli kao poklon ili dekoracija. Vise formata." ],
        [ "title" => "Digitalna ilustracija i graficki dizajn", "content" => "Logotipi, ilustracije za knjige, vizuali za drustvene mreze." ],
        [ "title" => "Fotografija na otvorenom i u studiju", "content" => "Portretna i fashion fotografija. Osnovna retusa ukljucena." ],
        [ "title" => "Kurs crtanja za pocetnike", "content" => "Individualni casovi crtanja za decu i odrasle. Online i uzivo." ],
    ],
    "Lepota"    => [
        [ "title" => "Profesionalna sminка za svadbe i proslave", "content" => "10+ godina iskustva. Dolazim na adresu. Probna sminka dostupna." ],
        [ "title" => "Frizura i stilizacija kose", "content" => "Fen, pravljenje pletenica, pundjа i posebnih frizura." ],
        [ "title" => "Manikir i pedikir uz gel lak", "content" => "Klasican i gel manikir, nail art, ojacavanje noktiju." ],
        [ "title" => "Masaza lica i tretmani za kozu", "content" => "Detox, hidratacija, anti-aging tretmani. 60-90 minuta." ],
        [ "title" => "Depilacija voskom i sugaring", "content" => "Prirodni vosak i sugaring. Dolazim na adresu u Beogradu." ],
    ],
    "Zdravlje"  => [
        [ "title" => "Masaza celog tela - relaksaciona i sportska", "content" => "Svedska i sportska masaza. Dolazak na adresu ili kod mene." ],
        [ "title" => "Joga casovi individualno i u grupi", "content" => "Hatha i vinjasa joga za pocetnike i napredne. Online i uzivo." ],
        [ "title" => "Personalizovani plan ishrane i dijetetika", "content" => "Izrada individualizovanih jelovnika. Konsultacija 45 min." ],
        [ "title" => "Fizioterapija i vežbe za rehabilitaciju", "content" => "Specificne vezbe za bol u leddima, povrede i rekonvalescenciju." ],
        [ "title" => "Meditacija i mindfulness treninzi", "content" => "Grupni i individualni treninzi. Pocetni kurs - 4 sesije." ],
    ],
    "Životinje" => [
        [ "title" => "Dresura pasa - osnovna i napredna obuka", "content" => "Pozitivno podsticanje. Svi uzrasti i rase. Dolazak na adresu." ],
        [ "title" => "Setalac i cuvar pasa dok ste odsutni", "content" => "Svakodnevne setnje, hranjenje i briga. Pouzdano i odgovorno." ],
        [ "title" => "Salon za negу ljubimaca - kupanje i sisanje", "content" => "Profesionalna oprema. Psi do 30kg. Rezervacija online." ],
        [ "title" => "Veterinarski savet i preventivna nega", "content" => "Konsultacija sa veterinarom online. Nutrition i preventiva." ],
        [ "title" => "Fotografija ljubimaca", "content" => "Profesionalne fotke vaseg ljubimca u prirodnom okruzenju." ],
    ],
    "Zabava"    => [
        [ "title" => "Muzika uzivo za proslаve i evente", "content" => "Gitarista i vokal. Serenada, svadbe, rodendani, firmska slavlja." ],
        [ "title" => "DJ za svadbe, proslave i clubbing", "content" => "Opremljenost za sve lokacije. Setlista po zelji." ],
        [ "title" => "Karikature i portreti uzivo na eventima", "content" => "Brzi karikaturista za zabavu gostiju. 1-2 min po osobi." ],
        [ "title" => "Organizacija igara i aktivnosti za decu", "content" => "Animatori za rodendane i decije proslave. 1-3h programa." ],
        [ "title" => "Escape room team building za firme", "content" => "Prilagodeni escape room iskustav u vasim prostorijama." ],
    ],
    "Ostalo"    => [
        [ "title" => "Privatni cas matematike i fizike", "content" => "Priprema za maturu i upis. Osnovna i srednja skola." ],
        [ "title" => "Prevod dokumenata srpski-engleski", "content" => "Ovlasceni prevodilac. Rok 24h. PDF ili Word format." ],
        [ "title" => "Popravka racunara i laptopa", "content" => "Reinstalacija, ciscenje od virusa, upgrade komponenti." ],
        [ "title" => "Uredenje i ciscenje stana", "content" => "Generalno ciscenje, organizacija prostora, selidbe." ],
        [ "title" => "Majstor za sve popravke u kuci", "content" => "Vodoinstalateri radovi, elektrika, molovanje, sitne popravke." ],
    ],
];

$cats = get_terms([ "taxonomy" => "service_category", "hide_empty" => false ]);
$cat_map = [];
foreach ($cats as $cat) { $cat_map[$cat->name] = $cat->term_id; }

$listing_count = 0;
$pi = 0;
foreach ( $listings_per_category as $cat_name => $items ) {
    $cat_id = $cat_map[$cat_name] ?? null;
    if ( ! $cat_id ) { echo "Category not found: $cat_name\n"; continue; }
    foreach ( $items as $item ) {
        $provider_id = $providers[ $pi % count($providers) ];
        $pi++;
        $existing = get_posts([ "post_type" => "service_listing", "title" => $item["title"], "post_status" => "any", "numberposts" => 1 ]);
        if ( $existing ) { echo "Listing exists: {$item["title"]}\n"; $listing_count++; continue; }
        remove_filter( "wp_insert_post_data", "nekoko_force_listing_pending", 10 );
        $post_id = wp_insert_post([
            "post_title"   => $item["title"],
            "post_content" => $item["content"],
            "post_status"  => "publish",
            "post_type"    => "service_listing",
            "post_author"  => $provider_id,
        ]);
        add_filter( "wp_insert_post_data", "nekoko_force_listing_pending", 10, 2 );
        if ( ! is_wp_error( $post_id ) ) {
            wp_set_post_terms( $post_id, [ $cat_id ], "service_category" );
            update_post_meta( $post_id, "_nekoko_listing_status", "approved" );
            $listing_count++;
            echo "Created listing: {$item["title"]}\n";
        }
    }
}

echo "\nSeed complete!\n";
echo "Providers: " . count($providers) . "\n";
echo "Listings:  $listing_count / 30\n";
