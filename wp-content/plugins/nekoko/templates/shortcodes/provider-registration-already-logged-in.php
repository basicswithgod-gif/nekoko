<?php
/**
 * Expects: $is_provider, $dashboard_url, $logout_url
 */
defined( 'ABSPATH' ) || exit;
?>
<?php if ( $is_provider ) : ?>
	<div style="background:#d1e7dd;padding:24px;border-radius:8px;">
		<p style="margin:0;">Već ste prijavljeni kao pružalac usluga. <a href="<?php echo esc_url( $dashboard_url ); ?>">Idite na dashboard</a>.</p>
	</div>
<?php else : ?>
	<div style="background:#fff3cd;padding:24px;border-radius:8px;">
		<p style="margin:0;">Već ste prijavljeni. <a href="<?php echo esc_url( $logout_url ); ?>">Odjavite se</a> da biste kreirali provajder nalog.</p>
	</div>
<?php endif; ?>
