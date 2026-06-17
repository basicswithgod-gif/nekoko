<?php
/**
 * Dynamic block: Featured Jobs grid.
 * Expects ACF fields on $block: limit, category, columns.
 *
 * @var array $block
 */
defined( 'ABSPATH' ) || exit;

$limit    = get_field( 'limit' ) ?: 6;
$category = get_field( 'category' );
$columns  = get_field( 'columns' ) ?: 3;

$args = [
	'post_type'      => Nekoko_CPT::POST_TYPE,
	'post_status'    => 'publish',
	'posts_per_page' => intval( $limit ),
];

if ( $category ) {
	$args['tax_query'] = [ [ 'taxonomy' => Nekoko_Taxonomies::CATEGORY, 'field' => 'term_id', 'terms' => $category ] ];
}

$query = new WP_Query( $args );
?>
<div class="nekoko-block-featured-jobs" style="--nekoko-grid-columns:<?php echo intval( $columns ); ?>;">
	<?php if ( ! $query->have_posts() ) : ?>
		<p>Nema dostupnih usluga.</p>
	<?php else : ?>
		<div class="nekoko-job-grid">
			<?php
			while ( $query->have_posts() ) :
				$query->the_post();
				include NEKOKO_PATH . 'templates/shortcodes/job-card.php';
			endwhile;
			wp_reset_postdata();
			?>
		</div>
	<?php endif; ?>
</div>
