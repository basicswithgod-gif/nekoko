<?php
/**
 * Expects: $tos_url, $privacy_url
 */
defined( 'ABSPATH' ) || exit;
?>
<div style="margin:16px 0;font-size:14px;line-height:1.6;">
	<label style="display:flex;gap:10px;align-items:flex-start;margin-bottom:12px;">
		<input type="checkbox" name="nekoko_accept_terms" value="1" style="margin-top:3px;flex-shrink:0;">
		<span>Prihvatam <a href="<?php echo esc_url( $tos_url ); ?>" target="_blank">Uslove korišćenja</a> i <a href="<?php echo esc_url( $privacy_url ); ?>" target="_blank">Politiku privatnosti</a> platforme NekoKo.rs.</span>
	</label>
	<label style="display:flex;gap:10px;align-items:flex-start;">
		<input type="checkbox" name="nekoko_accept_disclaimer" value="1" style="margin-top:3px;flex-shrink:0;">
		<span>Razumem da NekoKo.rs ne proverava kvalifikacije provajdera, ne posreduje u sporovima i ne procesira plaćanja.</span>
	</label>
</div>
<?php wp_nonce_field( 'nekoko_registration', 'nekoko_reg_nonce' ); ?>
