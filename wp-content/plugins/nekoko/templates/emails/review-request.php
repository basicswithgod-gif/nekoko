<?php
/**
 * Email template: review request (sent 5 days after booking via WP cron).
 * Variables: $customer_name, $job_title, $review_url
 */
defined( 'ABSPATH' ) || exit;
?>
<h2 style="color:#004682;margin:0 0 16px;">Kako je prošlo?</h2>
<p>Zdravo <?php echo esc_html( $customer_name ); ?>,</p>
<p>Pre pet dana si koristio/la uslugu <strong><?php echo esc_html( $job_title ); ?></strong> na NekoKo.rs.</p>
<p>Tvoje iskustvo je dragoceno — pomozi drugima da pronađu pravog pružaoca usluge ostavivši kratku recenziju.</p>
<p style="margin:32px 0;text-align:center;">
	<a href="<?php echo esc_url( $review_url ); ?>"
	   style="background:#004682;color:#fff;padding:14px 32px;border-radius:6px;text-decoration:none;font-weight:600;display:inline-block;font-size:1rem;">
		Ostavi recenziju →
	</a>
</p>
<p style="color:#999;font-size:.85rem;">Link je validan 30 dana i može se koristiti samo jednom.</p>
