<?php
/**
 * Expects: $user_name
 */
defined( 'ABSPATH' ) || exit;
?>
<p>Zdravo, <strong><?php echo esc_html( $user_name ); ?></strong>!</p>
<p>Dobrodošao/la na NekoKo.rs!</p>
<p><a href="<?php echo esc_url( home_url() ); ?>" style="display:inline-block;background:#004682;color:#fff;padding:12px 24px;border-radius:8px;text-decoration:none;">Idi na platformu</a></p>
<p style="color:#666;font-size:13px;">NekoKo ne procesira plaćanja i ne garantuje usluge trećih strana.</p>
