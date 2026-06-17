<?php
/**
 * Expects: $query, $categories, $cities, $page_url, $kw, $kat, $grad,
 *          $cena_min, $cena_max, $min_ocena, $sortiraj, $sorts
 */
defined( 'ABSPATH' ) || exit;
?>
<div class="nekoko-search-wrap">
	<form method="get" action="<?php echo esc_url( $page_url ); ?>" class="nekoko-search-bar">
		<div class="nekoko-search-bar__grid">
			<div class="nekoko-search-field">
				<label>Pretraži</label>
				<input type="text" name="kw" value="<?php echo esc_attr( $kw ); ?>" placeholder="Npr. masaža, fotograf...">
			</div>
			<div class="nekoko-search-field">
				<label>Kategorija</label>
				<select name="kat">
					<option value="">Sve kategorije</option>
					<?php foreach ( $categories as $c ) : ?>
						<option value="<?php echo esc_attr( $c->slug ); ?>" <?php selected( $kat, $c->slug ); ?>><?php echo esc_html( $c->name ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="nekoko-search-field">
				<label>Grad</label>
				<select name="grad">
					<option value="">Svi gradovi</option>
					<?php foreach ( $cities as $city ) : ?>
						<option value="<?php echo esc_attr( $city->slug ); ?>" <?php selected( $grad, $city->slug ); ?>><?php echo esc_html( $city->name ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="nekoko-search-field">
				<label>Cena (RSD)</label>
				<div class="nekoko-search-field__pair">
					<input type="number" name="cena_min" value="<?php echo esc_attr( $cena_min ); ?>" placeholder="Od" min="0">
					<input type="number" name="cena_max" value="<?php echo esc_attr( $cena_max ); ?>" placeholder="Do" min="0">
				</div>
			</div>
			<div class="nekoko-search-field">
				<label>Min. ocena</label>
				<select name="min_ocena">
					<option value="0">Sve ocene</option>
					<?php for ( $i = 1; $i <= 5; $i++ ) : ?>
						<option value="<?php echo $i; ?>" <?php selected( $min_ocena, $i ); ?>><?php echo str_repeat( '★', $i ); ?></option>
					<?php endfor; ?>
				</select>
			</div>
			<div class="nekoko-search-field nekoko-search-field--submit">
				<button type="submit" class="nekoko-btn">Pretraži</button>
			</div>
		</div>
		<div class="nekoko-search-bar__sort">
			<span>Sortiraj:</span>
			<?php foreach ( $sorts as $val => $label ) :
				$active = $sortiraj === $val;
				$url    = add_query_arg(
					array_filter(
						[
							'kw'        => $kw,
							'kat'       => $kat,
							'grad'      => $grad,
							'cena_min'  => $cena_min,
							'cena_max'  => $cena_max,
							'min_ocena' => $min_ocena ? $min_ocena : null,
							'sortiraj'  => $val,
						]
					),
					$page_url
				);
				?>
				<a href="<?php echo esc_url( $url ); ?>" class="nekoko-sort-pill<?php echo $active ? ' nekoko-sort-pill--active' : ''; ?>"><?php echo esc_html( $label ); ?></a>
			<?php endforeach; ?>
			<?php if ( $kw || $kat || $grad || $cena_min !== '' || $cena_max !== '' || $min_ocena ) : ?>
				<a href="<?php echo esc_url( $page_url ); ?>" class="nekoko-search-reset">× Resetuj filtere</a>
			<?php endif; ?>
		</div>
	</form>

	<div class="nekoko-search-results-meta">
		<p>Pronađeno: <strong><?php echo (int) $query->found_posts; ?></strong> usluga<?php if ( $kw ) echo ' za "<em>' . esc_html( $kw ) . '</em>"'; ?></p>
	</div>

	<?php if ( $query->have_posts() ) : ?>
		<div class="nekoko-job-grid nekoko-job-grid--search">
			<?php
			while ( $query->have_posts() ) :
				$query->the_post();
				include NEKOKO_PATH . 'templates/shortcodes/job-card.php';
			endwhile;
			wp_reset_postdata();
			?>
		</div>
	<?php else : ?>
		<div class="nekoko-no-results">
			<p>Nema rezultata za ove filtere.</p>
			<a href="<?php echo esc_url( $page_url ); ?>" class="nekoko-btn">Resetuj pretragu</a>
		</div>
	<?php endif; ?>
</div>
