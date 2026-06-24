<?php
/**
 * Expects: $errors, $vals, $tos_url, $privacy_url
 */
defined( 'ABSPATH' ) || exit;
?>
<div class="nekoko-form" style="max-width:600px;margin:0 auto;">
	<?php if ( $errors ) : ?>
		<div style="background:#f8d7da;padding:16px;border-radius:8px;margin-bottom:24px;">
			<ul style="margin:0;padding-left:20px;">
				<?php foreach ( $errors as $error ) : ?>
					<li><?php echo esc_html( $error ); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>
	<form method="post">
		<?php wp_nonce_field( 'nekoko_provider_reg' ); ?>
		<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
			<div class="form-group">
				<label>Ime *</label>
				<input type="text" name="nekoko_first_name" value="<?php echo esc_attr( $vals['first_name'] ); ?>" required placeholder="Vaše ime">
			</div>
			<div class="form-group">
				<label>Prezime *</label>
				<input type="text" name="nekoko_last_name" value="<?php echo esc_attr( $vals['last_name'] ); ?>" required placeholder="Vaše prezime">
			</div>
		</div>
		<div class="form-group">
			<label>Email adresa *</label>
			<input type="email" name="nekoko_email" value="<?php echo esc_attr( $vals['email'] ); ?>" required placeholder="vas@email.com">
		</div>
		<div class="form-group">
			<label>Grad / lokacija *</label>
			<input type="text" name="nekoko_location" value="<?php echo esc_attr( $vals['location'] ); ?>" required placeholder="npr. Beograd, Novi Sad, Niš...">
		</div>
		<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
			<div class="form-group">
				<label>Lozinka * <span style="font-size:.8rem;color:#666;">(min. 8 karaktera)</span></label>
				<input type="password" name="nekoko_password" required placeholder="••••••••" autocomplete="new-password">
			</div>
			<div class="form-group">
				<label>Potvrda lozinke *</label>
				<input type="password" name="nekoko_password2" required placeholder="••••••••" autocomplete="new-password">
			</div>
		</div>
		<div class="form-group">
			<label>Opis usluga koje nudite *</label>
			<textarea name="nekoko_description" rows="5" required placeholder="Opišite koje usluge nudite, vaše iskustvo i šta vas izdvaja..."><?php echo esc_textarea( $vals['description'] ); ?></textarea>
			<span style="font-size:.8rem;color:#666;">Vidljivo samo administratoru pre odobrenja profila.</span>
		</div>
		<div style="margin:20px 0;font-size:14px;line-height:1.6;">
			<label style="display:flex;gap:10px;align-items:flex-start;margin-bottom:12px;cursor:pointer;">
				<input type="checkbox" name="nekoko_accept_terms" value="1" style="margin-top:3px;flex-shrink:0;" <?php checked( ! empty( $_POST['nekoko_accept_terms'] ), '1' ); ?>>
				<span>Prihvatam <a href="<?php echo esc_url( $tos_url ); ?>" target="_blank">Uslove korišćenja</a> i <a href="<?php echo esc_url( $privacy_url ); ?>" target="_blank">Politiku privatnosti</a> platforme NekoKo.rs.</span>
			</label>
			<label style="display:flex;gap:10px;align-items:flex-start;cursor:pointer;">
				<input type="checkbox" name="nekoko_accept_disclaimer" value="1" style="margin-top:3px;flex-shrink:0;" <?php checked( ! empty( $_POST['nekoko_accept_disclaimer'] ), '1' ); ?>>
				<span>Razumem da NekoKo.rs ne proverava kvalifikacije provajdera, ne posreduje u sporovima i ne procesira plaćanja. Nudim usluge kao nezavisni izvođač.</span>
			</label>
		</div>
		<button type="submit" name="nekoko_provider_submit" class="nekoko-btn" style="width:100%;padding:14px;">Pošalji zahtev za registraciju</button>
	</form>
	<p style="text-align:center;margin-top:16px;font-size:.9rem;color:#666;">Već imate nalog? <a href="<?php echo esc_url( wp_login_url() ); ?>">Prijavite se</a></p>
</div>
