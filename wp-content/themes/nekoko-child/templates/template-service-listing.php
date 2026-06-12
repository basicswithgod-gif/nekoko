<?php
/**
 * Template Name: Oglas — Detalji
 */
get_header();
?>

<div class="container" style="padding:2rem 1rem">
    <?php while ( have_posts() ) : the_post();
        $price    = get_post_meta( get_the_ID(), '_nekoko_price_note', true );
        $location = get_post_meta( get_the_ID(), '_nekoko_location', true );
        $terms    = get_the_terms( get_the_ID(), 'service_category' );
        $cat      = $terms ? $terms[0]->name : '';
        $provider = get_userdata( get_post_field( 'post_author' ) );
        $avg      = $provider ? (float) get_user_meta( $provider->ID, '_nekoko_rating_avg', true ) : 0;
        $count    = $provider ? (int) get_user_meta( $provider->ID, '_nekoko_rating_count', true ) : 0;
    ?>
    <div style="display:grid;grid-template-columns:1fr 380px;gap:2rem;align-items:start">
        <!-- Main content -->
        <div>
            <div style="background:#fff;border-radius:8px;padding:2rem;box-shadow:0 2px 8px rgba(0,0,0,.08);margin-bottom:1.5rem">
                <div style="font-size:0.85rem;color:#888;margin-bottom:0.5rem">
                    <?php echo esc_html( $cat ); ?>
                    <?php if ( $location ) echo ' &bull; ' . esc_html( $location ); ?>
                </div>
                <h1 style="margin:0 0 1rem;font-size:1.75rem;color:#004682"><?php the_title(); ?></h1>
                <?php if ( $price ) : ?>
                <div style="font-size:1.25rem;font-weight:700;color:#004682;margin-bottom:1rem"><?php echo esc_html( $price ); ?></div>
                <?php endif; ?>
                <div style="line-height:1.8;color:#444">
                    <?php the_content(); ?>
                </div>
            </div>

            <!-- Provider info -->
            <?php if ( $provider ) : ?>
            <div style="background:#fff;border-radius:8px;padding:1.5rem;box-shadow:0 2px 8px rgba(0,0,0,.08)">
                <h3 style="margin:0 0 0.75rem;color:#004682">O davaocu usluge</h3>
                <div style="display:flex;align-items:center;gap:1rem">
                    <?php echo get_avatar( $provider->ID, 56, '', '', [ 'style' => 'border-radius:50%' ] ); ?>
                    <div>
                        <strong><?php echo esc_html( $provider->display_name ); ?></strong>
                        <?php if ( $avg ) : ?>
                        <div><?php echo nekoko_get_star_html( $avg ); ?> <?php echo number_format( $avg, 1 ); ?>/5 (<?php echo $count; ?> recenzija)</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Booking sidebar -->
        <div style="position:sticky;top:2rem">
            <div style="background:#fff;border-radius:8px;padding:1.5rem;box-shadow:0 2px 8px rgba(0,0,0,.08)">
                <h3 style="margin:0 0 1rem;color:#004682">Zatraži uslugu</h3>
                <?php echo do_shortcode( '[nekoko_booking_form listing_id="' . get_the_ID() . '"]' ); ?>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
</div>

<?php get_footer(); ?>
