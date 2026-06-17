<?php
/**
 * Expects: $provider_name, $job_title, $job_url
 */
defined( 'ABSPATH' ) || exit;
?>
<p>Zdravo, <strong><?php echo esc_html( $provider_name ); ?></strong>!</p>
<p>Tvoja usluga <strong><?php echo esc_html( $job_title ); ?></strong> je <strong style="color:#0f5132;">odobrena</strong>!</p>
<p><a href="<?php echo esc_url( $job_url ); ?>" style="display:inline-block;background:#004682;color:#fff;padding:12px 24px;border-radius:8px;text-decoration:none;">Pogledaj</a></p>
