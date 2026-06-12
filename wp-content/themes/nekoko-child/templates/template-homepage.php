<?php
/**
 * Template Name: NekoKo Početna
 */
get_header();
?>

<!-- Hero Section -->
<section class="nekoko-hero">
    <h1>Pronađi savršenu uslugu u Srbiji</h1>
    <p>NekoKo povezuje ljude sa davaocima nišnih usluga — od umetnosti do zdravlja. Brzo, lako, pouzdano.</p>
    <a href="<?php echo esc_url( home_url( '/usluge/' ) ); ?>" class="btn-primary">Istraži usluge</a>
</section>

<!-- Categories -->
<section class="nekoko-section">
    <div class="container">
        <h2>Kategorije usluga</h2>
        <div class="nekoko-categories">
            <?php
            $categories = [
                [ 'name' => 'Umetnost',   'icon' => '🎨', 'slug' => 'umetnost'  ],
                [ 'name' => 'Lepota',     'icon' => '💅', 'slug' => 'lepota'    ],
                [ 'name' => 'Zdravlje',   'icon' => '🏥', 'slug' => 'zdravlje'  ],
                [ 'name' => 'Životinje',  'icon' => '🐾', 'slug' => 'zivotinje' ],
                [ 'name' => 'Zabava',     'icon' => '🎭', 'slug' => 'zabava'    ],
                [ 'name' => 'Ostalo',     'icon' => '✨', 'slug' => 'ostalo'    ],
            ];
            foreach ( $categories as $cat ) : ?>
            <a href="<?php echo esc_url( home_url( '/usluge/?kategorija=' . $cat['slug'] ) ); ?>" class="nekoko-category-card">
                <span class="category-icon"><?php echo $cat['icon']; ?></span>
                <h3><?php echo esc_html( $cat['name'] ); ?></h3>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured Listings -->
<section class="nekoko-section nekoko-section--gray">
    <div class="container">
        <h2>Istaknuti oglasi</h2>
        <?php echo do_shortcode( '[nekoko_listings limit="6"]' ); ?>
        <div style="text-align:center;margin-top:2rem">
            <a href="<?php echo esc_url( home_url( '/usluge/' ) ); ?>" class="btn btn-primary">Pogledaj sve oglase</a>
        </div>
    </div>
</section>

<!-- How It Works -->
<section class="nekoko-section">
    <div class="container">
        <h2>Kako funkcioniše</h2>
        <div class="nekoko-steps">
            <div class="nekoko-step">
                <span class="step-num">1</span>
                <h3>Pronađi uslugu</h3>
                <p>Pregledaj oglase u kategorijama ili pretraži po ključnim rečima.</p>
            </div>
            <div class="nekoko-step">
                <span class="step-num">2</span>
                <h3>Kontaktiraj davalaca</h3>
                <p>Pošalji zahtev za rezervaciju sa detaljima o tome šta ti treba.</p>
            </div>
            <div class="nekoko-step">
                <span class="step-num">3</span>
                <h3>Ugovorite detalje</h3>
                <p>Dogovorite cenu, termin i način plaćanja direktno sa davaocem usluge.</p>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
