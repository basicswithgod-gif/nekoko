<?php
/**
 * Single job card. Must be included inside "the_post()" loop context.
 * Reads current post via get_the_ID() / the_title() etc.
 */
defined( 'ABSPATH' ) || exit;

$categories = get_the_terms( get_the_ID(), Nekoko_Taxonomies::CATEGORY );
$category   = $categories && ! is_wp_error( $categories ) ? esc_html( $categories[0]->name ) : '';
$cities     = get_the_terms( get_the_ID(), Nekoko_Taxonomies::CITY );
$city       = $cities && ! is_wp_error( $cities ) ? esc_html( $cities[0]->name ) : '';
$average    = (float) get_post_meta( get_the_ID(), '_nekoko_avg_rating', true );
$count      = (int) get_post_meta( get_the_ID(), '_nekoko_review_count', true );
$price      = (int) get_post_meta( get_the_ID(), '_nekoko_price', true );
?>
<article class="nekoko-job-card">
	<?php if ( has_post_thumbnail() ) : ?>
		<a href="<?php the_permalink(); ?>"><img class="nekoko-job-card__image" src="<?php the_post_thumbnail_url( 'medium' ); ?>" alt="<?php the_title_attribute(); ?>"></a>
	<?php else : ?>
		<a href="<?php the_permalink(); ?>"><div class="nekoko-job-card__image nekoko-job-card__image--placeholder">🏷️</div></a>
	<?php endif; ?>
	<div class="nekoko-job-card__body">
		<div class="nekoko-job-card__meta-row">
			<?php if ( $category ) : ?><span class="nekoko-job-card__category"><?php echo $category; ?></span><?php endif; ?>
			<?php if ( $city ) : ?><span class="nekoko-job-card__city">📍 <?php echo $city; ?></span><?php endif; ?>
		</div>
		<h3 class="nekoko-job-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<?php if ( $average ) : ?>
			<div class="star-rating">
				<?php for ( $i = 1; $i <= 5; $i++ ) : ?>
					<span class="star <?php echo $i <= round( $average ) ? 'filled' : ''; ?>">&#9733;</span>
				<?php endfor; ?>
				<span class="nekoko-job-card__rating-count">(<?php echo $count; ?>)</span>
			</div>
		<?php endif; ?>
		<?php if ( $price ) : ?>
			<div class="nekoko-job-card__price"><?php echo number_format( $price, 0, '.', '.' ); ?> RSD</div>
		<?php endif; ?>
		<p class="nekoko-job-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 20 ) ); ?></p>
		<a href="<?php the_permalink(); ?>" class="nekoko-btn">Pogledaj uslugu</a>
	</div>
</article>
