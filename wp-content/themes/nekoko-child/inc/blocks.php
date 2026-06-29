<?php
/**
 * NekoKo Dynamic Blocks
 * Registers Featured Categories and Featured Jobs blocks
 */

// ── Allowed SVG tags for wp_kses ─────────────────────────
function nekoko_allowed_svg_tags() {
    return [
        'svg'  => [ 'xmlns' => [], 'viewbox' => [], 'width' => [], 'height' => [], 'fill' => [], 'class' => [], 'aria-hidden' => [] ],
        'path' => [ 'd' => [], 'fill' => [], 'stroke' => [], 'stroke-width' => [] ],
        'g'    => [ 'fill' => [], 'stroke' => [] ],
        'circle' => [ 'cx' => [], 'cy' => [], 'r' => [], 'fill' => [] ],
        'rect'   => [ 'x' => [], 'y' => [], 'width' => [], 'height' => [], 'fill' => [] ],
    ];
}

// ── Register term meta for SVG icon ──────────────────────
function nekoko_register_term_meta() {
    register_term_meta( 'job_category', 'category_icon', [
        'type'              => 'string',
        'single'            => true,
        'show_in_rest'      => true,
        'sanitize_callback' => 'wp_kses_post',
    ] );
}
add_action( 'init', 'nekoko_register_term_meta' );

// ── Register dynamic blocks ───────────────────────────────
function nekoko_register_blocks() {

    // Featured Categories Block
    register_block_type( 'nekoko/featured-categories', [
        'render_callback' => 'nekoko_render_featured_categories',
        'attributes'      => [
            'jobType'     => [ 'type' => 'string',  'default' => 'ponuda' ],
            'categoryIds' => [ 'type' => 'array',   'default' => [],
                               'items' => [ 'type' => 'integer' ] ],
            'title'       => [ 'type' => 'string',  'default' => 'Top kategorije' ],
            'subtitle'    => [ 'type' => 'string',  'default' => '' ],
        ],
    ] );

    // Featured Jobs Block
    register_block_type( 'nekoko/featured-jobs', [
        'render_callback' => 'nekoko_render_featured_jobs',
        'attributes'      => [
            'jobType' => [ 'type' => 'string', 'default' => 'ponuda' ],
            'postIds' => [ 'type' => 'array',  'default' => [],
                           'items' => [ 'type' => 'integer' ] ],
            'title'   => [ 'type' => 'string', 'default' => 'Istaknuti oglasi' ],
        ],
    ] );
}
add_action( 'init', 'nekoko_register_blocks' );

// ── Render: Featured Categories ───────────────────────────
function nekoko_render_featured_categories( $attributes ) {
    $job_type     = sanitize_text_field( $attributes['jobType'] ?? 'ponuda' );
    $category_ids = array_map( 'intval', $attributes['categoryIds'] ?? [] );
    $title        = esc_html( $attributes['title'] ?? 'Top kategorije' );
    $subtitle     = esc_html( $attributes['subtitle'] ?? '' );

    // If no IDs selected, get all job_category terms
    if ( empty( $category_ids ) ) {
        $terms = get_terms( [ 'taxonomy' => 'job_category', 'hide_empty' => false ] );
    } else {
        // Respect editor order
        $terms = array_filter( array_map( function( $id ) {
            return get_term( $id, 'job_category' );
        }, $category_ids ), function( $t ) {
            return $t && ! is_wp_error( $t );
        } );
    }

    $terms = array_slice( $terms, 0, 6 );

    ob_start();
    ?>
    <div class="wp-block-group alignfull nk-section nk-categories-section nk-featured-categories">
        <div class="nk-section-inner">
            <?php if ( $title ) : ?><h2 class="has-text-align-center"><?php echo $title; ?></h2><?php endif; ?>
            <?php if ( $subtitle ) : ?><p class="has-text-align-center nk-section-subtitle"><?php echo $subtitle; ?></p><?php endif; ?>
            <hr class="wp-block-separator nk-separator" />
            <div class="nk-cat-grid">
                <?php foreach ( $terms as $term ) : ?>
                    <?php require get_template_directory() . '/inc/partials/category-card.php'; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

// ── Render: Featured Jobs ─────────────────────────────────
function nekoko_render_featured_jobs( $attributes ) {
    $job_type = sanitize_text_field( $attributes['jobType'] ?? 'ponuda' );
    $post_ids = array_map( 'intval', $attributes['postIds'] ?? [] );
    $title    = esc_html( $attributes['title'] ?? 'Istaknuti oglasi' );

    if ( empty( $post_ids ) ) {
        // Fallback: latest 5 jobs of selected type
        $args = [
            'post_type'      => 'job',
            'posts_per_page' => 5,
            'post_status'    => 'publish',
            'tax_query'      => [ [
                'taxonomy' => 'job_type',
                'field'    => 'slug',
                'terms'    => $job_type,
            ] ],
        ];
        $posts = get_posts( $args );
    } else {
        // Respect editor order
        $posts = array_filter( array_map( function( $id ) {
            return get_post( $id );
        }, $post_ids ), function( $p ) {
            return $p && $p->post_status === 'publish';
        } );
        $posts = array_slice( $posts, 0, 5 );
    }

    ob_start();
    ?>
    <div class="wp-block-group alignfull nk-section nk-listings-section nk-featured-jobs">
        <div class="nk-section-inner">
            <?php if ( $title ) : ?><h2 class="has-text-align-center"><?php echo $title; ?></h2><?php endif; ?>
            <div class="nk-jobs-grid">
                <?php foreach ( $posts as $post ) : ?>
                    <?php require get_template_directory() . '/inc/partials/job-card.php'; ?>
                <?php endforeach; ?>
            </div>
            <div class="wp-block-buttons" style="justify-content:center;margin-top:2.5rem">
                <div class="wp-block-button is-style-outline">
                    <a class="wp-block-button__link" href="/<?php echo esc_attr( $job_type ); ?>">Pogledaj sve oglase</a>
                </div>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
