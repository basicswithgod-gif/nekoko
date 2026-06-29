<?php
/**
 * Atomic component: Category Card
 *
 * @param WP_Term $term     — job_category term object
 * @param string  $job_type — 'ponuda' or 'potraznja'
 */

$icon     = get_term_meta( $term->term_id, 'category_icon', true );
$fallback = '⭐';
$url      = get_term_link( $term, 'job_category' ) . '?job_type=' . esc_attr( $job_type );
?>
<div class="nk-cat-item">
    <a href="<?php echo esc_url( $url ); ?>" class="nk-cat-item-link" aria-label="<?php echo esc_attr( $term->name ); ?>">
        <div class="nk-cat-icon">
            <?php if ( $icon ) : ?>
                <?php echo wp_kses( $icon, nekoko_allowed_svg_tags() ); ?>
            <?php else : ?>
                <?php echo esc_html( $fallback ); ?>
            <?php endif; ?>
        </div>
    </a>
    <p class="nk-cat-label has-text-align-center"><?php echo esc_html( $term->name ); ?></p>
</div>
