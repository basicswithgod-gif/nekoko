<?php
/**
 * Expects: $job, $provider, $job_id
 */
defined( 'ABSPATH' ) || exit;
?>
<div class="nekoko-form nekoko-booking-form" data-job-id="<?php echo esc_attr( $job_id ); ?>">
	<h3>Pošalji zahtev za rezervaciju</h3>

	<div class="nekoko-booking-step" data-step="1">
		<form method="post">
			<?php wp_nonce_field( 'nekoko_booking_' . $job_id ); ?>
			<input type="hidden" name="nekoko_job_id" value="<?php echo esc_attr( $job_id ); ?>">
			<div class="form-group">
				<label>Ime i prezime *</label>
				<input type="text" data-field="name" name="nekoko_name" required placeholder="Tvoje ime">
			</div>
			<div class="form-group">
				<label>Email adresa *</label>
				<input type="email" data-field="email" name="nekoko_email" required placeholder="tvoj@email.com">
			</div>
			<div class="form-group">
				<label>Željeni datum</label>
				<input type="date" data-field="date" name="nekoko_date">
			</div>
			<div class="form-group">
				<label>Poruka *</label>
				<textarea data-field="message" name="nekoko_message" rows="5" required placeholder="Opiši šta ti je potrebno..."></textarea>
			</div>
			<button type="button" class="nekoko-btn nekoko-booking-review" style="width:100%;">Pregled zahteva &#8594;</button>
			<input type="submit" name="nekoko_booking_submit" class="nekoko-booking-submit" style="display:none;">
		</form>
	</div>

	<div class="nekoko-booking-step" data-step="2" style="display:none;">
		<div style="background:#f5f7fa;border-radius:10px;padding:20px;margin-bottom:20px;">
			<h4 style="margin-top:0;color:#004682;">Pregled zahteva</h4>
			<table style="width:100%;border-collapse:collapse;font-size:.95rem;">
				<tr><td style="padding:6px 0;color:#666;width:38%;">Usluga:</td><td style="padding:6px 0;"><strong><?php echo esc_html( $job->post_title ); ?></strong></td></tr>
				<tr><td style="padding:6px 0;color:#666;">Pružalac:</td><td style="padding:6px 0;"><?php echo esc_html( $provider ? $provider->display_name : '-' ); ?></td></tr>
				<tr><td style="padding:6px 0;color:#666;">Vaše ime:</td><td style="padding:6px 0;" data-review="name"></td></tr>
				<tr><td style="padding:6px 0;color:#666;">Email:</td><td style="padding:6px 0;" data-review="email"></td></tr>
				<tr data-review-row="date"><td style="padding:6px 0;color:#666;">Željeni datum:</td><td style="padding:6px 0;" data-review="date"></td></tr>
				<tr><td style="padding:6px 0;color:#666;vertical-align:top;">Poruka:</td><td style="padding:6px 0;font-style:italic;" data-review="message"></td></tr>
			</table>
		</div>
		<p style="font-size:.85rem;color:#555;background:#fff3cd;padding:12px;border-radius:8px;margin-bottom:16px;">&#9888; NekoKo ne posreduje u plaćanju niti u sporovima. Sve dogovore obavljate direktno sa pružaocem.</p>
		<div style="display:flex;gap:12px;">
			<button type="button" class="nekoko-btn nekoko-booking-back" style="background:#6c757d;flex:1;">&#8592; Izmeni</button>
			<button type="button" class="nekoko-btn nekoko-booking-confirm" style="flex:2;">Potvrdi i pošalji zahtev</button>
		</div>
	</div>
</div>
