<?php
/**
 * Atomic component: Job Card
 *
 * @param WP_Post $post      — job post object
 * @param string  $job_type  — 'ponuda' or 'potraznja' (optional, for display)
 */

$categories = wp_get_post_terms( $post->ID, 'job_category' );
$cities     = wp_get_post_terms( $post->ID, 'job_city' );
$price      = get_post_meta( $post->ID, 'nekoko_price', true );
$cat_name   = ! empty( $categories ) && ! is_wp_error( $categories ) ? $categories[0]->name : '';
$cat_icon   = ! empty( $categories ) && ! is_wp_error( $categories ) ? get_term_meta( $categories[0]->term_id, 'category_icon', true ) : '';
$city_name  = ! empty( $cities ) && ! is_wp_error( $cities ) ? $cities[0]->name : '';
$url        = get_permalink( $post->ID );
?>
<div class="nk-oglas-card">
    <?php if ( has_post_thumbnail( $post->ID ) ) : ?>
        <a href="<?php echo esc_url( $url ); ?>">
            <?php echo get_the_post_thumbnail( $post->ID, 'nekoko-card', [ 'alt' => esc_attr( $post->post_title ) ] ); ?>
        </a>
    <?php endif; ?>
    <div class="nk-oglas-card-body">
        <?php if ( $cat_name ) : ?>
            <p class="nk-card-category">
                <?php if ( $cat_icon ) echo wp_kses( $cat_icon, nekoko_allowed_svg_tags() ); ?>
                <?php echo esc_html( $cat_name ); ?>
            </p>
        <?php endif; ?>
        <h4 class="nk-card-title">
            <a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $post->post_title ); ?></a>
        </h4>
        <p class="nk-card-desc"><?php echo esc_html( wp_trim_words( $post->post_excerpt ?: $post->post_content, 18 ) ); ?></p>
        <p class="nk-card-price">
            <?php if ( $city_name ) : ?>📍 <?php echo esc_html( $city_name ); ?> &nbsp;·&nbsp; <?php endif; ?>
            <?php if ( $price ) : ?><strong>od <?php echo esc_html( number_format( $price, 0, ',', '.' ) ); ?> RSD</strong><?php endif; ?>
        </p>
    </div>
</div>
