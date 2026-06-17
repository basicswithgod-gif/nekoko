<?php
/**
 * Expects: $provider_name, $customer_name, $customer_email, $job_title, $message, $date
 */
defined( 'ABSPATH' ) || exit;
?>
<p>Zdravo, <strong><?php echo esc_html( $provider_name ); ?></strong>!</p>
<p>Nov zahtev za <strong><?php echo esc_html( $job_title ); ?></strong>:</p>
<table style="background:#f5f5f5;padding:20px;border-radius:8px;width:100%;">
	<tr><td><strong>Ime:</strong></td><td><?php echo esc_html( $customer_name ); ?></td></tr>
	<tr><td><strong>Email:</strong></td><td><?php echo esc_html( $customer_email ); ?></td></tr>
	<tr><td><strong>Datum:</strong></td><td><?php echo esc_html( $date ); ?></td></tr>
	<tr><td><strong>Poruka:</strong></td><td><?php echo nl2br( esc_html( $message ) ); ?></td></tr>
</table>
<p style="color:#666;font-size:13px;">NekoKo ne posreduje u plaćanju. Sve dogovore obavljate direktno.</p>
