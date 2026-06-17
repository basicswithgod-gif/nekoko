<?php
/**
 * Expects: $reviews (array), $average (float), $can_review (bool), $job_id
 */
defined( 'ABSPATH' ) || exit;
?>
<div class="nekoko-reviews">
	<h3>Ocene i recenzije
		<?php if ( $reviews ) : ?>
			<span class="star-rating" style="font-size:1rem;margin-left:8px;">
				<?php for ( $i = 1; $i <= 5; $i++ ) : ?>
					<span class="star <?php echo $i <= round( $average ) ? 'filled' : ''; ?>">&#9733;</span>
				<?php endfor; ?>
				<span style="color:#666;font-size:.9rem;">(<?php echo count( $reviews ); ?>)</span>
			</span>
		<?php endif; ?>
	</h3>

	<?php foreach ( array_reverse( $reviews ) as $review ) : ?>
		<div style="border-bottom:1px solid var(--nekoko-light);padding:16px 0;">
			<div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
				<strong><?php echo esc_html( $review['name'] ); ?></strong>
				<span class="star-rating">
					<?php for ( $i = 1; $i <= 5; $i++ ) : ?>
						<span class="star <?php echo $i <= $review['rating'] ? 'filled' : ''; ?>">&#9733;</span>
					<?php endfor; ?>
				</span>
				<span style="color:#999;font-size:.8rem;"><?php echo esc_html( date_i18n( 'd.m.Y', strtotime( $review['date'] ) ) ); ?></span>
			</div>
			<p style="margin:0;"><?php echo esc_html( $review['comment'] ); ?></p>
		</div>
	<?php endforeach; ?>

	<?php if ( $can_review ) : ?>
		<div style="margin-top:24px;">
			<h4>Ostavi recenziju</h4>
			<form method="post" class="nekoko-form">
				<?php wp_nonce_field( 'nekoko_review_' . $job_id ); ?>
				<div class="form-group">
					<label>Ocena</label>
					<select name="nekoko_rating">
						<option value="5">&#9733;&#9733;&#9733;&#9733;&#9733; Odlično</option>
						<option value="4">&#9733;&#9733;&#9733;&#9733; Dobro</option>
						<option value="3">&#9733;&#9733;&#9733; Prosečno</option>
						<option value="2">&#9733;&#9733; Loše</option>
						<option value="1">&#9733; Veoma loše</option>
					</select>
				</div>
				<div class="form-group">
					<label>Recenzija</label>
					<textarea name="nekoko_review_text" rows="4" placeholder="Napiši svoja iskustva..."></textarea>
				</div>
				<button type="submit" name="nekoko_review_submit" class="nekoko-btn">Objavi recenziju</button>
			</form>
		</div>
	<?php endif; ?>
</div>
