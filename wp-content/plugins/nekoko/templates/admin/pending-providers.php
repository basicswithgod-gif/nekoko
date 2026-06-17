<?php
/**
 * Expects: $users (array of WP_User)
 */
defined( 'ABSPATH' ) || exit;
?>
<div class="wrap">
	<h1>Provajderi pending</h1>
	<table class="widefat striped">
		<thead>
			<tr><th>Ime</th><th>Email</th><th>Opis usluga</th><th>Registrovan</th><th>Status</th><th>Akcije</th></tr>
		</thead>
		<tbody>
		<?php foreach ( $users as $u ) :
			$status      = get_user_meta( $u->ID, '_nekoko_provider_status', true ) ?: 'pending';
			$description = get_user_meta( $u->ID, '_nekoko_service_description', true ) ?: '-';
			$approve_url = wp_nonce_url( admin_url( 'admin.php?page=nekoko-pending-providers&action=approve-provider&user_id=' . $u->ID ), 'nekoko_provider_action' );
			$reject_url  = wp_nonce_url( admin_url( 'admin.php?page=nekoko-pending-providers&action=reject-provider&user_id=' . $u->ID ), 'nekoko_provider_action' );
			?>
			<tr>
				<td><?php echo esc_html( $u->display_name ); ?></td>
				<td><?php echo esc_html( $u->user_email ); ?></td>
				<td style="max-width:220px;"><?php echo esc_html( mb_substr( $description, 0, 100 ) ) . ( mb_strlen( $description ) > 100 ? '…' : '' ); ?></td>
				<td><?php echo esc_html( date_i18n( 'd.m.Y', strtotime( $u->user_registered ) ) ); ?></td>
				<td><?php echo esc_html( $status ); ?></td>
				<td>
					<a href="<?php echo esc_url( $approve_url ); ?>" class="button button-primary" style="margin-right:8px;">Odobri</a>
					<a href="<?php echo esc_url( $reject_url ); ?>" class="button">Odbij</a>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>
