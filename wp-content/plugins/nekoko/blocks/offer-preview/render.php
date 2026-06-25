<?php
/**
 * Block render: acf/nekoko-offer-preview
 * Left: heading + paragraph + CTA. Right: Ponuda job cards (or placeholders).
 */
defined( 'ABSPATH' ) || exit;

$heading    = get_field( 'offer_heading' )   ?: 'Istaknite ono što nudite';
$paragraph  = get_field( 'offer_paragraph' ) ?: 'Od čuvanja egzotičnih ljubimaca do kreativnih radionica, NekoKo daje prostor uslugama koje su korisne, neobične i ljudima zaista trebaju.';
$btn_label  = get_field( 'offer_btn_label' ) ?: 'Objavite ponudu';
$btn_url    = get_field( 'offer_btn_url' )   ?: '/postavi-oglas/';
$card_count = max( 1, min( 6, intval( get_field( 'offer_card_count' ) ?: 3 ) ) );

$ponuda_term = get_term_by( 'name', 'Ponuda', Nekoko_Taxonomies::TYPE );
$args = [
	'post_type'      => Nekoko_CPT::POST_TYPE,
	'post_status'    => 'publish',
	'posts_per_page' => $card_count,
	'orderby'        => 'date',
	'order'          => 'DESC',
];
if ( $ponuda_term ) {
	$args['tax_query'] = [ [
		'taxonomy' => Nekoko_Taxonomies::TYPE,
		'field'    => 'term_id',
		'terms'    => $ponuda_term->term_id,
	] ];
}
$query            = new WP_Query( $args );
$use_placeholders = ! $query->have_posts();

$placeholders = [
	[ 'title' => 'Čuvanje i nega egzotičnih kućnih ljubimaca tokom vikenda',        'city' => 'Beograd',  'price' => 2500, 'category' => 'Ljubimci' ],
	[ 'title' => 'Privatni čas ishrane i mršavljenja', 'city' => 'Novi Sad', 'price' => 4200, 'category' => 'Ishrana'  ],
	[ 'title' => 'Oslikavanje zida u dečjoj sobi', 'city' => 'Niš',    'price' => 8000, 'category' => 'Umetnost' ],
];
?>
<section class="nekoko-hp-preview nekoko-hp-preview--offer">
	<div class="nekoko-hp-preview__inner">

		<div class="nekoko-hp-preview__content">
			<span class="nekoko-hp-preview__eyebrow">PONUDA</span>
			<h2 class="nekoko-hp-preview__heading"><?php echo esc_html( $heading ); ?></h2>
			<p class="nekoko-hp-preview__paragraph"><?php echo esc_html( $paragraph ); ?></p>
			<a href="<?php echo esc_url( $btn_url ); ?>" class="nekoko-btn">
				<?php echo esc_html( $btn_label ); ?>
			</a>
		</div>

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
					<article class="nekoko-hp-card">
						<div class="nekoko-hp-card__body">
							<span class="nekoko-hp-card__category"><?php echo esc_html( $cat_name ); ?></span>
							<h3 class="nekoko-hp-card__title"><?php the_title(); ?></h3>
							<div class="nekoko-hp-card__footer">
								<span class="nekoko-hp-card__city">📍 <?php echo esc_html( $city ); ?></span>
								<span class="nekoko-hp-card__price nekoko-hp-card__price--blue">
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
					<article class="nekoko-hp-card">
						<div class="nekoko-hp-card__body">
							<span class="nekoko-hp-card__category"><?php echo esc_html( $ph['category'] ); ?></span>
							<h3 class="nekoko-hp-card__title"><?php echo esc_html( $ph['title'] ); ?></h3>
							<div class="nekoko-hp-card__footer">
								<span class="nekoko-hp-card__city">📍 <?php echo esc_html( $ph['city'] ); ?></span>
								<span class="nekoko-hp-card__price nekoko-hp-card__price--blue">
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

	</div>
</section>
