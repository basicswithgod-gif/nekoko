<?php
/**
 * Block render: acf/nekoko-search-bar
 * Search heading + form with text input, category dropdown, submit button.
 * Navigates to /pretraga/?s={q}&job_category={slug}
 */
defined( 'ABSPATH' ) || exit;

$heading     = get_field( 'search_heading' )     ?: 'Pretražite';
$subheading  = get_field( 'search_subheading' )  ?: 'Pronađite neobičan posao, specifičnu pomoć ili osobu za poseban zadatak.';
$placeholder = get_field( 'search_placeholder' ) ?: 'Šta tražite?';
$btn_label   = get_field( 'search_btn_label' )   ?: 'Pretraži';

$categories = get_terms( [ 'taxonomy' => Nekoko_Taxonomies::CATEGORY, 'hide_empty' => false ] );
?>
<section class="nekoko-hp-search">
	<div class="nekoko-hp-search__inner">

		<div class="nekoko-hp-search__text">
			<h2 class="nekoko-hp-search__heading"><?php echo esc_html( $heading ); ?></h2>
			<p class="nekoko-hp-search__sub"><?php echo esc_html( $subheading ); ?></p>
		</div>

		<form class="nekoko-hp-search__form" method="GET"
		      action="<?php echo esc_url( home_url( '/pretraga/' ) ); ?>">
			<input type="text" name="s"
			       class="nekoko-hp-search__input"
			       placeholder="<?php echo esc_attr( $placeholder ); ?>">
			<div class="nekoko-hp-search__select-wrap">
				<select name="job_category" class="nekoko-hp-search__select">
					<option value="">Sve kategorije</option>
					<?php if ( ! is_wp_error( $categories ) && $categories ) : ?>
						<?php foreach ( $categories as $cat ) : ?>
							<option value="<?php echo esc_attr( $cat->slug ); ?>">
								<?php echo esc_html( $cat->name ); ?>
							</option>
						<?php endforeach; ?>
					<?php endif; ?>
				</select>
			</div>
			<button type="submit" class="nekoko-btn nekoko-btn--red">
				<?php echo esc_html( $btn_label ); ?>
			</button>
		</form>

	</div>
</section>
