<?php
/**
 * Expects: $provider_name, $job_title, $reason
 */
defined( 'ABSPATH' ) || exit;
?>
<p>Zdravo, <strong><?php echo esc_html( $provider_name ); ?></strong>!</p>
<p>Usluga <strong><?php echo esc_html( $job_title ); ?></strong> nije odobrena.<?php echo $reason ? ' Razlog: ' . esc_html( $reason ) : ''; ?></p>
<p>Možeš urediti i ponovo poslati.</p>
