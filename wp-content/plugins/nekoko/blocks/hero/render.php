<?php
/**
 * Block render: acf/nekoko-hero
 * Two-column hero: left = text + CTAs, right = 2×2 image grid + badge.
 */
defined( 'ABSPATH' ) || exit;

$eyebrow      = get_field( 'hero_eyebrow' )             ?: 'Start-up za neobične poslove';
$heading      = get_field( 'hero_heading' )             ?: "Mesto gde neobični poslovi\ndobijaju svoje ljude";
$paragraph    = get_field( 'hero_paragraph' )           ?: 'NekoKo spaja ljude koji nude ili traže specifične usluge, pomoć i male zadatke koje klasični oglasi često ne prepoznaju.';
$btn1_label   = get_field( 'hero_btn_primary_label' )   ?: 'Pretražite oglase';
$btn1_url     = get_field( 'hero_btn_primary_url' )     ?: '/pretraga/';
$btn2_label   = get_field( 'hero_btn_secondary_label' ) ?: 'Kako funkcioniše';
$btn2_url     = get_field( 'hero_btn_secondary_url' )   ?: '/kako-funkcionise/';
$images       = get_field( 'hero_images' )              ?: [];
$badge_number = get_field( 'hero_badge_number' )        ?: '1.240+';
$badge_label  = get_field( 'hero_badge_label' )         ?: 'aktivnih oglasa';
?>
<section class="nekoko-hp-hero">
	<div class="nekoko-hp-hero__inner">

		<div class="nekoko-hp-hero__content">
			<span class="nekoko-hp-hero__eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
			<h1 class="nekoko-hp-hero__heading"><?php echo nl2br( esc_html( $heading ) ); ?></h1>
			<p class="nekoko-hp-hero__paragraph"><?php echo esc_html( $paragraph ); ?></p>
			<div class="nekoko-hp-hero__buttons">
				<a href="<?php echo esc_url( $btn1_url ); ?>" class="nekoko-btn">
					<?php echo esc_html( $btn1_label ); ?>
				</a>
				<a href="<?php echo esc_url( $btn2_url ); ?>" class="nekoko-btn nekoko-btn--outline">
					<?php echo esc_html( $btn2_label ); ?>
				</a>
			</div>
		</div>

		<div class="nekoko-hp-hero__images">
			<div class="nekoko-hp-hero__grid">
				<?php for ( $i = 0; $i < 4; $i++ ) :
					$img = ! empty( $images[ $i ]['image'] ) ? $images[ $i ]['image'] : null;
					?>
					<div class="nekoko-hp-hero__img-wrap">
						<?php if ( $img && ! empty( $img['url'] ) ) : ?>
							<img src="<?php echo esc_url( $img['url'] ); ?>"
							     alt="<?php echo esc_attr( $img['alt'] ?? '' ); ?>"
							     loading="<?php echo $i < 2 ? 'eager' : 'lazy'; ?>">
						<?php else : ?>
							<div class="nekoko-hp-hero__img-placeholder"></div>
						<?php endif; ?>
					</div>
				<?php endfor; ?>
			</div>
			<div class="nekoko-hp-hero__badge">
				<span class="nekoko-hp-hero__badge-number"><?php echo esc_html( $badge_number ); ?></span>
				<span class="nekoko-hp-hero__badge-label"><?php echo esc_html( $badge_label ); ?></span>
			</div>
		</div>

	</div>
</section>
