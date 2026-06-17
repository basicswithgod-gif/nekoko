<?php
/**
 * Expects: $pending_providers_count, $pending_jobs_count, $providers_url, $jobs_url
 */
defined( 'ABSPATH' ) || exit;
?>
<div class="wrap">
	<h1>NekoKo Moderacija</h1>
	<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-top:24px;">
		<div style="background:#fff3cd;padding:24px;border-radius:8px;">
			<h2 style="margin-top:0;">Provajderi pending</h2>
			<p style="font-size:2rem;font-weight:800;margin:0;"><?php echo (int) $pending_providers_count; ?></p>
			<a href="<?php echo esc_url( $providers_url ); ?>" class="button button-primary" style="margin-top:16px;">Pregledaj</a>
		</div>
		<div style="background:#d1e7dd;padding:24px;border-radius:8px;">
			<h2 style="margin-top:0;">Jobs pending</h2>
			<p style="font-size:2rem;font-weight:800;margin:0;"><?php echo (int) $pending_jobs_count; ?></p>
			<a href="<?php echo esc_url( $jobs_url ); ?>" class="button button-primary" style="margin-top:16px;">Pregledaj</a>
		</div>
	</div>
</div>
