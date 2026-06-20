<?php
defined( 'ABSPATH' ) || exit;
get_header();
?>
<main class="nekoko-single-job">
	<?php while ( have_posts() ) : the_post();
		$categories = get_the_terms( get_the_ID(), 'job_category' );
		$cities     = get_the_terms( get_the_ID(), 'job_city' );
		$average    = (float) get_post_meta( get_the_ID(), '_nekoko_avg_rating', true );
		$count      = (int) get_post_meta( get_the_ID(), '_nekoko_review_count', true );
		$price      = (int) get_post_meta( get_the_ID(), '_nekoko_price', true );
		$provider   = get_user_by( 'id', get_the_author_meta( 'ID' ) );
		?>
		<div class="nekoko-single-job__layout">
			<div class="nekoko-single-job__main">
				<?php if ( has_post_thumbnail() ) : ?>
					<img class="nekoko-single-job__image" src="<?php the_post_thumbnail_url( 'large' ); ?>" alt="<?php the_title_attribute(); ?>">
				<?php endif; ?>

				<div class="nekoko-single-job__meta-row">
					<?php if ( $categories && ! is_wp_error( $categories ) ) : ?>
						<span class="badge badge-approved"><?php echo esc_html( $categories[0]->name ); ?></span>
					<?php endif; ?>
					<?php if ( $cities && ! is_wp_error( $cities ) ) : ?>
						<span class="nekoko-single-job__city">📍 <?php echo esc_html( $cities[0]->name ); ?></span>
					<?php endif; ?>
					<?php if ( $average ) : ?>
						<span class="star-rating">
							<?php for ( $i = 1; $i <= 5; $i++ ) : ?>
								<span class="star <?php echo $i <= round( $average ) ? 'filled' : ''; ?>">&#9733;</span>
							<?php endfor; ?>
						</span>
						<span class="nekoko-single-job__rating-count">(<?php echo $count; ?> recenzija)</span>
					<?php endif; ?>
				</div>

				<h1><?php the_title(); ?></h1>

				<?php if ( $price ) : ?>
					<div class="nekoko-single-job__price"><?php echo number_format( $price, 0, '.', '.' ); ?> <span>RSD</span></div>
				<?php endif; ?>

				<div class="nekoko-single-job__description"><?php the_content(); ?></div>

				<hr>
				<?php echo do_shortcode( '[nekoko_reviews job_id=' . get_the_ID() . ']' ); ?>
			</div>

			<div class="nekoko-single-job__sidebar">
				<div class="nekoko-single-job__provider-card">
					<?php if ( $provider ) : ?>
						<div class="nekoko-single-job__provider">
							<?php echo get_avatar( $provider->ID, 56 ); ?>
							<div>
								<strong><?php echo esc_html( $provider->display_name ); ?></strong><br>
								<span>Pružalac usluge</span><br>
								<a href="<?php echo esc_url( home_url( '/provajder/' . $provider->user_nicename . '/' ) ); ?>" style="font-size:.85rem;">Pogledaj profil →</a>
							</div>
						</div>
					<?php endif; ?>
					<?php echo do_shortcode( '[nekoko_booking_form job_id=' . get_the_ID() . ']' ); ?>
				</div>
			</div>
		</div>
	<?php endwhile; ?>
</main>
<?php get_footer(); ?>
