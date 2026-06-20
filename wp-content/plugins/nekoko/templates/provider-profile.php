<?php
/**
 * Provider profile page template.
 * Served for /provajder/{user_nicename}/ via Nekoko_Provider_Profile routing.
 */
defined( 'ABSPATH' ) || exit;

$provider_slug = get_query_var( 'nekoko_provider_slug' );
$provider      = get_user_by( 'slug', $provider_slug );

if ( ! $provider || ! in_array( 'provider', (array) $provider->roles, true ) ) {
	global $wp_query;
	$wp_query->set_404();
	status_header( 404 );
	nocache_headers();
	get_template_part( '404' );
	exit;
}

$provider_id = $provider->ID;
$location    = get_user_meta( $provider_id, '_nekoko_location', true );
$bio         = get_user_meta( $provider_id, '_nekoko_service_description', true );
$categories  = Nekoko_Review_CPT::get_categories_for_provider( $provider_id );

$active_jobs = get_posts(
	[
		'post_type'   => Nekoko_CPT::POST_TYPE,
		'post_status' => 'publish',
		'author'      => $provider_id,
		'numberposts' => 6,
	]
);

get_header();
?>
<main class="nekoko-provider-profile" style="max-width:960px;margin:0 auto;padding:32px 16px;">

	<div class="nekoko-provider-profile__header" style="display:flex;align-items:flex-start;gap:24px;flex-wrap:wrap;margin-bottom:40px;padding-bottom:32px;border-bottom:2px solid var(--nekoko-light,#eee);">
		<div style="flex-shrink:0;"><?php echo get_avatar( $provider_id, 96 ); ?></div>
		<div style="flex:1;min-width:200px;">
			<h1 style="margin:0 0 6px;font-size:1.75rem;"><?php echo esc_html( $provider->display_name ); ?></h1>
			<?php if ( $location ) : ?>
				<div style="color:#666;margin-bottom:8px;">📍 <?php echo esc_html( $location ); ?></div>
			<?php endif; ?>
			<?php if ( $bio ) : ?>
				<p style="margin:12px 0 0;color:#444;"><?php echo esc_html( $bio ); ?></p>
			<?php endif; ?>
		</div>
	</div>

	<?php if ( $categories ) : ?>
		<section class="nekoko-provider-profile__reviews" style="margin-bottom:48px;">
			<h2 style="margin-bottom:20px;">Recenzije</h2>

			<div class="nekoko-provider-profile__tabs" style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:24px;">
				<?php foreach ( $categories as $i => $cat ) : ?>
					<button
						class="nekoko-btn <?php echo $i > 0 ? 'nekoko-btn--outline' : ''; ?>"
						style="font-size:.85rem;"
						onclick="nekoko_switch_tab(this, 'cat-<?php echo esc_attr( $cat->term_id ); ?>')"
					><?php echo esc_html( $cat->name ); ?></button>
				<?php endforeach; ?>
			</div>

			<?php foreach ( $categories as $i => $cat ) :
				$cat_reviews = Nekoko_Review_CPT::get_for_provider( $provider_id, $cat->term_id );
			?>
				<div id="cat-<?php echo esc_attr( $cat->term_id ); ?>"
				     class="nekoko-provider-profile__tab-panel"
				     <?php echo $i > 0 ? 'style="display:none;"' : ''; ?>>
					<?php if ( $cat_reviews ) : ?>
						<?php foreach ( $cat_reviews as $review ) :
							$rname  = esc_html( get_post_meta( $review->ID, '_review_reviewer_name', true ) ?: 'Korisnik' );
							$rating = (int) get_post_meta( $review->ID, '_review_rating', true );
						?>
							<div style="border-bottom:1px solid #eee;padding:16px 0;">
								<div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
									<strong><?php echo $rname; ?></strong>
									<span class="star-rating">
										<?php for ( $s = 1; $s <= 5; $s++ ) : ?>
											<span class="star <?php echo $s <= $rating ? 'filled' : ''; ?>">&#9733;</span>
										<?php endfor; ?>
									</span>
									<span style="color:#999;font-size:.8rem;"><?php echo esc_html( date_i18n( 'd.m.Y', strtotime( $review->post_date ) ) ); ?></span>
								</div>
								<p style="margin:0;"><?php echo esc_html( $review->post_content ); ?></p>
							</div>
						<?php endforeach; ?>
					<?php else : ?>
						<p style="color:#999;">Nema recenzija u kategoriji <?php echo esc_html( $cat->name ); ?>.</p>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</section>
	<?php else : ?>
		<p style="color:#999;margin-bottom:48px;">Ovaj pružalac usluga još nema recenzija.</p>
	<?php endif; ?>

	<?php if ( $active_jobs ) : ?>
		<section class="nekoko-provider-profile__listings">
			<h2 style="margin-bottom:20px;">Aktivne usluge</h2>
			<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:24px;">
				<?php
				global $post;
				foreach ( $active_jobs as $listing ) {
					$post = $listing;
					setup_postdata( $post );
					include NEKOKO_PATH . 'templates/shortcodes/job-card.php';
				}
				wp_reset_postdata();
				?>
			</div>
		</section>
	<?php endif; ?>

</main>

<script>
function nekoko_switch_tab( btn, tabId ) {
	document.querySelectorAll( '.nekoko-provider-profile__tab-panel' ).forEach( function( p ) {
		p.style.display = 'none';
	} );
	document.querySelectorAll( '.nekoko-provider-profile__tabs .nekoko-btn' ).forEach( function( b ) {
		b.classList.add( 'nekoko-btn--outline' );
	} );
	var panel = document.getElementById( tabId );
	if ( panel ) panel.style.display = '';
	btn.classList.remove( 'nekoko-btn--outline' );
}
</script>

<?php get_footer(); ?>
