<?php
/**
 * Expects: $customer_name, $job_title, $job_url
 */
defined( 'ABSPATH' ) || exit;
?>
<p>Zdravo, <strong><?php echo esc_html( $customer_name ); ?></strong>!</p>
<p>Zahtev za <strong><?php echo esc_html( $job_title ); ?></strong> je poslat. Pružalac će te kontaktirati u roku od 48 sati.</p>
<p><a href="<?php echo esc_url( $job_url ); ?>" style="display:inline-block;background:#004682;color:#fff;padding:12px 24px;border-radius:8px;text-decoration:none;">Pogledaj uslugu</a></p>
