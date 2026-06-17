<?php
/**
 * Expects: $jobs (array of WP_Post), $profile_url
 */
defined( 'ABSPATH' ) || exit;
?>
<div class="nekoko-dashboard">
	<div class="dashboard-grid">
		<div class="dashboard-sidebar">
			<ul>
				<li><a href="#jobs" class="active">Moje usluge</a></li>
				<li><a href="<?php echo esc_url( $profile_url ); ?>">Moj profil</a></li>
			</ul>
		</div>
		<div class="dashboard-main">
			<h2>Moje usluge (<?php echo count( $jobs ); ?>)</h2>
			<?php if ( empty( $jobs ) ) : ?>
				<p>Još nisi dodao/la nijednu uslugu.</p>
			<?php else : ?>
				<table class="nekoko-dashboard-table">
					<thead>
						<tr><th>Naziv</th><th>Status</th><th>Akcije</th></tr>
					</thead>
					<tbody>
					<?php foreach ( $jobs as $job ) :
						$status = get_post_meta( $job->ID, '_nekoko_listing_status', true ) ?: $job->post_status;
						$badge  = 'badge-' . ( $status === 'publish' ? 'approved' : ( $status === 'pending' ? 'pending' : 'rejected' ) );
						?>
						<tr>
							<td><?php echo esc_html( $job->post_title ); ?></td>
							<td><span class="badge <?php echo esc_attr( $badge ); ?>"><?php echo esc_html( ucfirst( $status ) ); ?></span></td>
							<td><a href="<?php echo esc_url( get_permalink( $job->ID ) ); ?>">Pogledaj</a> | <a href="<?php echo esc_url( get_edit_post_link( $job->ID ) ); ?>">Uredi</a></td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
		</div>
	</div>
</div>
