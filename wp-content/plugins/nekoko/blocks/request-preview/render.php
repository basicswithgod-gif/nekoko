<?php
/**
 * Block render: acf/nekoko-request-preview
 * Left: Potražnja job cards (or placeholders). Right: heading + paragraph + CTA.
 * Mirror layout of offer-preview.
 */
defined( 'ABSPATH' ) || exit;

$heading    = get_field( 'request_heading' )   ?: 'Recite šta vam treba';
$paragraph  = get_field( 'request_paragraph' ) ?: 'Kada tražite nešto van standardnih oglasa, važni su jasnoća, poverenje i dobar opis. Zato je potražnja na NekoKo jednostavna i pregledna.';
$btn_label  = get_field( 'request_btn_label' ) ?: 'Objavite potražnju';
$btn_url    = get_field( 'request_btn_url' )   ?: '/postavi-potraznju/';
$card_count = max( 1, min( 6, intval( get_field( 'request_card_count' ) ?: 3 ) ) );

$potraznja_term = get_term_by( 'name', 'Potražnja', Nekoko_Taxonomies::TYPE );
$args = [
	'post_type'      => Nekoko_CPT::POST_TYPE,
	'post_status'    => 'publish',
	'posts_per_page' => $card_count,
	'orderby'        => 'date',
	'order'          => 'DESC',
];
if ( $potraznja_term ) {
	$args['tax_query'] = [ [
		'taxonomy' => Nekoko_Taxonomies::TYPE,
		'field'    => 'term_id',
		'terms'    => $potraznja_term->term_id,
	] ];
}
$query           = new WP_Query( $args );
$use_placeholders = ! $query->have_posts();

$placeholders = [
	[ 'title' => 'Tražim pomoć oko preseljenja biljaka',      'city' => 'Beograd', 'price' => 3000, 'category' => 'Pomoć'    ],
	[ 'title' => 'Potrebna osoba za vožnju do veterinara',     'city' => 'Zaječar', 'price' => 1800, 'category' => 'Ljubimci' ],
	[ 'title' => 'Tražim nekoga da organizuje mini radionicu', 'city' => 'Online',  'price' => 0,    'category' => 'Zdravlje' ],
];
?>
<section class="nekoko-hp-preview nekoko-hp-preview--request">
	<div class="nekoko-hp-preview__inner">

		<div class="nekoko-hp-preview__cards">
			<div class="nekoko-hp-preview__type-badge">
				<span class="nekoko-pill nekoko-pill--blue">Potražnja</span>
			</div>

			<?php if ( ! $use_placeholders ) : ?>
				<?php while ( $query->have_posts() ) :
					$query->the_post(); ?>
					<?php include NEKOKO_PATH . 'templates/shortcodes/job-card.php'; ?>
				<?php endwhile; ?>
				<?php wp_reset_postdata(); ?>
			<?php else : ?>
				<?php foreach ( array_slice( $placeholders, 0, $card_count ) as $ph ) : ?>
					<article class="nekoko-job-card nekoko-job-card--ph">
						<div class="nekoko-job-card__body">
							<div class="nekoko-job-card__meta-row">
								<span class="nekoko-job-card__category"><?php echo esc_html( $ph['category'] ); ?></span>
								<span class="nekoko-job-card__city">📍 <?php echo esc_html( $ph['city'] ); ?></span>
							</div>
							<h3 class="nekoko-job-card__title"><?php echo esc_html( $ph['title'] ); ?></h3>
							<div class="nekoko-job-card__price">
								<?php echo $ph['price'] > 0
									? esc_html( number_format( $ph['price'], 0, '.', '.' ) ) . ' RSD'
									: 'Po dogovoru'; ?>
							</div>
						</div>
					</article>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>

		<div class="nekoko-hp-preview__content">
			<h2 class="nekoko-hp-preview__heading"><?php echo esc_html( $heading ); ?></h2>
			<p class="nekoko-hp-preview__paragraph"><?php echo esc_html( $paragraph ); ?></p>
			<a href="<?php echo esc_url( $btn_url ); ?>" class="nekoko-btn nekoko-btn--yellow">
				<?php echo esc_html( $btn_label ); ?>
			</a>
		</div>

	</div>
</section>
