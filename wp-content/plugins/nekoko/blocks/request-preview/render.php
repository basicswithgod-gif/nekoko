<?php
/**
 * Block render: acf/nekoko-request-preview
 * Left: live Potražnja cards queried from CPT.
 * Right: InnerBlocks (heading + paragraph + CTA — directly editable in Gutenberg).
 */
defined( 'ABSPATH' ) || exit;

$card_count = max( 1, min( 6, intval( get_field( 'request_card_count' ) ?: 3 ) ) );

$template = wp_json_encode( [
	[ 'core/paragraph', [ 'className' => 'nekoko-hp-preview__eyebrow nekoko-hp-preview__eyebrow--red', 'content' => 'POTRAŽNJA' ] ],
	[ 'core/heading',   [ 'level' => 2, 'className' => 'nekoko-hp-preview__heading', 'content' => 'Recite šta vam treba' ] ],
	[ 'core/paragraph', [ 'className' => 'nekoko-hp-preview__paragraph', 'content' => 'Kada tražite nešto van standardnih oglasa, važni su jasnoća, poverenje i dobar opis. Zato je potražnja na NekoKo jednostavna i pregledna.' ] ],
	[ 'core/buttons', [], [
		[ 'core/button', [ 'text' => 'Objavite potražnju', 'url' => '/postavi-potraznju/', 'className' => 'nekoko-btn nekoko-btn--yellow' ] ],
	] ],
] );

// --- dynamic cards ---
$potraznja_term = get_term_by( 'name', 'Potražnja', Nekoko_Taxonomies::TYPE );
$args = [
	'post_type'      => Nekoko_CPT::POST_TYPE,
	'post_status'    => 'publish',
	'posts_per_page' => $card_count,
	'orderby'        => 'date',
	'order'          => 'DESC',
];
if ( $potraznja_term ) {
	$args['tax_query'] = [ [ [
		'taxonomy' => Nekoko_Taxonomies::TYPE,
		'field'    => 'term_id',
		'terms'    => $potraznja_term->term_id,
	] ] ];
}
$query            = new WP_Query( $args );
$use_placeholders = ! $query->have_posts();

$placeholders = [
	[ 'title' => 'Tražim nekoga za preseljenje biljaka i kućnih rasada u nove saksije', 'city' => 'Beograd', 'price' => 3000, 'category' => 'Pomoć'    ],
	[ 'title' => 'Potrebna osoba za vožnju do veterinara',                               'city' => 'Zaječar', 'price' => 1800, 'category' => 'Ljubimci' ],
	[ 'title' => 'Tražim nekoga za mini radionicu',                                      'city' => 'Online',  'price' => 0,    'category' => 'Zdravlje' ],
];
?>
<section class="nekoko-hp-preview nekoko-hp-preview--request">
	<div class="nekoko-hp-preview__inner">

		<div class="nekoko-hp-preview__cards">
			<?php if ( ! $use_placeholders ) : ?>
				<?php while ( $query->have_posts() ) :
					$query->the_post();
					$cats     = get_the_terms( get_the_ID(), Nekoko_Taxonomies::CATEGORY );
					$cat_name = $cats && ! is_wp_error( $cats ) ? ucfirst( strtolower( $cats[0]->name ) ) : '';
					$cities   = get_the_terms( get_the_ID(), Nekoko_Taxonomies::CITY );
					$city     = $cities && ! is_wp_error( $cities ) ? $cities[0]->name : '';
					$price    = get_post_meta( get_the_ID(), '_nekoko_price', true );
					?>
					<article class="nekoko-hp-card nekoko-hp-card--request">
						<div class="nekoko-hp-card__body">
							<span class="nekoko-hp-card__category"><?php echo esc_html( $cat_name ); ?></span>
							<h3 class="nekoko-hp-card__title"><?php the_title(); ?></h3>
							<div class="nekoko-hp-card__footer">
								<span class="nekoko-hp-card__city">📍 <?php echo esc_html( $city ); ?></span>
								<span class="nekoko-hp-card__price nekoko-hp-card__price--red">
									<?php echo $price > 0
										? esc_html( number_format( $price, 0, '.', '.' ) ) . ' RSD'
										: 'Po dogovoru'; ?>
								</span>
							</div>
						</div>
					</article>
				<?php endwhile; ?>
				<?php wp_reset_postdata(); ?>
			<?php else : ?>
				<?php foreach ( array_slice( $placeholders, 0, $card_count ) as $ph ) : ?>
					<article class="nekoko-hp-card nekoko-hp-card--request">
						<div class="nekoko-hp-card__body">
							<span class="nekoko-hp-card__category"><?php echo esc_html( $ph['category'] ); ?></span>
							<h3 class="nekoko-hp-card__title"><?php echo esc_html( $ph['title'] ); ?></h3>
							<div class="nekoko-hp-card__footer">
								<span class="nekoko-hp-card__city">📍 <?php echo esc_html( $ph['city'] ); ?></span>
								<span class="nekoko-hp-card__price nekoko-hp-card__price--red">
									<?php echo $ph['price'] > 0
										? esc_html( number_format( $ph['price'], 0, '.', '.' ) ) . ' RSD'
										: 'Po dogovoru'; ?>
								</span>
							</div>
						</div>
					</article>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>

		<div class="nekoko-hp-preview__content">
			<InnerBlocks template='<?php echo esc_attr( $template ); ?>' templateLock="false" />
		</div>

	</div>
</section>
