<?php
/**
 * Block render: acf/nekoko-top-categories
 * Reused for both Ponuda and Potražnja category grids.
 * tc_type field ('ponuda'|'potraznja') controls the default heading and archive links.
 */
defined( 'ABSPATH' ) || exit;

$tc_type = get_field( 'tc_type' ) ?: 'ponuda';

$default_heading = 'potraznja' === $tc_type
	? 'Top kategorije u potražnji'
	: 'Top kategorije u ponudi';
$default_sub = 'potraznja' === $tc_type
	? 'Brzi pristup do napopularnijih kategorija u potražnji.'
	: 'Jednostavna navigacija kroz najtraženije i najzanimljivije tipove oglasa.';

$heading    = get_field( 'tc_heading' )    ?: $default_heading;
$subheading = get_field( 'tc_subheading' ) ?: $default_sub;
$selected   = get_field( 'tc_categories' ) ?: [];

$term_args = [
	'taxonomy'   => Nekoko_Taxonomies::CATEGORY,
	'hide_empty' => false,
];
if ( ! empty( $selected ) ) {
	$term_args['include'] = array_map( 'intval', (array) $selected );
}
$terms = get_terms( $term_args );

$archive_base = home_url( '/pretraga/?job_type=' . rawurlencode( $tc_type ) . '&job_category=' );
?>
<section class="nekoko-hp-top-cats">
	<div class="nekoko-hp-top-cats__inner">
		<h2 class="nekoko-hp-top-cats__heading"><?php echo esc_html( $heading ); ?></h2>
		<p class="nekoko-hp-top-cats__sub"><?php echo esc_html( $subheading ); ?></p>

		<div class="nekoko-hp-top-cats__grid">
			<?php if ( ! is_wp_error( $terms ) && $terms ) : ?>
				<?php foreach ( $terms as $term ) :
					$image = function_exists( 'get_field' )
						? get_field( 'job_category_image', Nekoko_Taxonomies::CATEGORY . '_' . $term->term_id )
						: null;
					?>
					<a href="<?php echo esc_url( $archive_base . $term->slug ); ?>"
					   class="nekoko-cat-card">
						<div class="nekoko-cat-card__icon">
							<?php if ( $image && ! empty( $image['url'] ) ) : ?>
								<img src="<?php echo esc_url( $image['url'] ); ?>"
								     alt="<?php echo esc_attr( $term->name ); ?>"
								     width="48" height="48">
							<?php else : ?>
								<span class="nekoko-cat-card__icon-emoji">🏷️</span>
							<?php endif; ?>
						</div>
						<span class="nekoko-cat-card__label"><?php echo esc_html( $term->name ); ?></span>
					</a>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</div>
</section>
