<?php
/**
 * NekoKo Seed Data
 * Creates random job posts per category and subcategory
 *
 * Run with: wp eval-file nekoko-seed.php
 */

if ( ! defined( 'ABSPATH' ) ) {
    // Allow running via WP CLI
    $wp_load = dirname( __FILE__ ) . '/wp-load.php';
    if ( file_exists( $wp_load ) ) require $wp_load;
    else die( 'WordPress not found. Run via WP CLI: wp eval-file nekoko-seed.php' . PHP_EOL );
}

function nekoko_seed_jobs() {

    $categories = get_terms( [ 'taxonomy' => 'job_category', 'hide_empty' => false ] );
    $subcats    = get_terms( [ 'taxonomy' => 'job_subcategory', 'hide_empty' => false ] );
    $cities     = get_terms( [ 'taxonomy' => 'job_city', 'hide_empty' => false ] );
    $types      = get_terms( [ 'taxonomy' => 'job_type', 'hide_empty' => false ] );

    if ( empty( $cities ) || is_wp_error( $cities ) ) {
        echo "ERROR: No job_city terms found. Is the database connected?\n";
        return;
    }

    $all_terms = array_merge(
        is_wp_error( $categories ) ? [] : $categories,
        is_wp_error( $subcats )    ? [] : $subcats
    );

    $titles = [
        'Profesionalne usluge fotografisanja',
        'Izrada veb sajta po meri',
        'Obuka kućnih ljubimaca',
        'Prevod sa engleskog na srpski',
        'Holističke masaže i tretmani',
        'Čuvanje dece vikendima',
        'Montaža video materijala',
        'Izrada logotipa i brendinga',
        'Poduka klavira za početnike',
        'Ručno izrađeni nakit po narudžbini',
        'Organizacija događaja i proslava',
        'Servis bicikla i elektrotrotineta',
        'Priprema za maturu i prijemne ispite',
        'Yoga i meditacija u vašem domu',
        'Šetanje i čuvanje pasa',
    ];

    $descriptions = [
        'Profesionalno i pouzdano. Više od 5 godina iskustva u ovoj oblasti.',
        'Kvalitetna usluga po povoljnoj ceni. Slobodno kontaktirajte za više informacija.',
        'Iskusan stručnjak sa odličnim referencama. Dostupan u Beogradu i okolini.',
        'Brza isporuka i vrhunski rezultati. Zadovoljstvo klijenata je prioritet.',
        'Radim sa strašću i posvećenošću. Svaki posao tretiran individualno.',
    ];

    $created = 0;

    foreach ( $all_terms as $term ) {
        $count = rand( 1, 10 );

        for ( $i = 0; $i < $count; $i++ ) {
            $type = $types[ array_rand( (array) $types ) ];
            $city = $cities[ array_rand( (array) $cities ) ];
            $title = $titles[ array_rand( $titles ) ] . ' — ' . $term->name;
            $desc  = $descriptions[ array_rand( $descriptions ) ];
            $price = rand( 500, 10000 );
            // Round to nearest 500
            $price = round( $price / 500 ) * 500;

            $post_id = wp_insert_post( [
                'post_title'   => $title,
                'post_content' => $desc,
                'post_excerpt' => $desc,
                'post_status'  => 'publish',
                'post_type'    => 'job',
            ] );

            if ( is_wp_error( $post_id ) ) {
                echo "ERROR creating post: " . $post_id->get_error_message() . "\n";
                continue;
            }

            // Assign taxonomy — use correct taxonomy based on term
            wp_set_post_terms( $post_id, [ $term->term_id ], $term->taxonomy );
            wp_set_post_terms( $post_id, [ $type->term_id ], 'job_type' );
            wp_set_post_terms( $post_id, [ $city->term_id ], 'job_city' );

            // Set price meta
            update_post_meta( $post_id, 'nekoko_price', $price );

            $created++;
            echo "Created job #{$post_id}: {$title} [{$type->name}, {$city->name}, {$price} RSD]\n";
        }
    }

    echo "\n✅ Done. Created {$created} job posts.\n";
}

nekoko_seed_jobs();
