<?php
/**
 * Expects: $jobs (WP_Post[]), $upcoming_bookings (WP_Post[]),
 *          $completed_bookings (WP_Post[]), $profile_url (string)
 */
defined( 'ABSPATH' ) || exit;
?>
<div class="nekoko-dashboard">
	<div class="dashboard-grid">
		<div class="dashboard-sidebar">
			<ul>
				<li><a href="#jobs" class="active">Moje usluge</a></li>
				<li><a href="#upcoming">Nadolazeći</a></li>
				<li><a href="#completed">Završeni</a></li>
				<li><a href="<?php echo esc_url( $profile_url ); ?>">Moj profil</a></li>
			</ul>
		</div>

		<div class="dashboard-main">

			<section id="jobs">
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
								<td>
									<a href="<?php echo esc_url( get_permalink( $job->ID ) ); ?>">Pogledaj</a>
									| <a href="<?php echo esc_url( get_edit_post_link( $job->ID ) ); ?>">Uredi</a>
								</td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				<?php endif; ?>
			</section>

			<hr style="margin:40px 0;">

			<section id="upcoming">
				<h2>Nadolazeći zahtevi (<?php echo count( $upcoming_bookings ); ?>)</h2>
				<?php if ( empty( $upcoming_bookings ) ) : ?>
					<p>Nema novih zahteva za rezervaciju.</p>
				<?php else : ?>
					<table class="nekoko-dashboard-table">
						<thead>
							<tr><th>Usluga</th><th>Klijent</th><th>Datum</th><th>Poruka</th><th>Status</th></tr>
						</thead>
						<tbody>
						<?php foreach ( $upcoming_bookings as $booking ) :
							$bdate    = get_post_meta( $booking->ID, '_booking_date', true );
							$bname    = get_post_meta( $booking->ID, '_booking_customer_name', true );
							$bemail   = get_post_meta( $booking->ID, '_booking_customer_email', true );
							$bmsg     = get_post_meta( $booking->ID, '_booking_message', true );
							$bstatus  = get_post_meta( $booking->ID, '_booking_status', true );
							$bjob_id  = (int) get_post_meta( $booking->ID, '_booking_job_id', true );
							$job_link = $bjob_id ? get_permalink( $bjob_id ) : '#';
							$job_name = $bjob_id ? get_the_title( $bjob_id ) : '—';
							$badge    = 'badge-' . ( $bstatus === 'confirmed' ? 'approved' : 'pending' );
							?>
							<tr>
								<td><a href="<?php echo esc_url( $job_link ); ?>"><?php echo esc_html( $job_name ); ?></a></td>
								<td><?php echo esc_html( $bname ); ?><br><small><?php echo esc_html( $bemail ); ?></small></td>
								<td><?php echo $bdate ? esc_html( date_i18n( 'd.m.Y', strtotime( $bdate ) ) ) : '—'; ?></td>
								<td style="max-width:200px;word-break:break-word;"><?php echo esc_html( wp_trim_words( $bmsg, 15, '…' ) ); ?></td>
								<td><span class="badge <?php echo esc_attr( $badge ); ?>"><?php echo esc_html( ucfirst( $bstatus ) ); ?></span></td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				<?php endif; ?>
			</section>

			<hr style="margin:40px 0;">

			<section id="completed">
				<h2>Završeni zahtevi (<?php echo count( $completed_bookings ); ?>)</h2>
				<?php if ( empty( $completed_bookings ) ) : ?>
					<p>Nema završenih zahteva.</p>
				<?php else : ?>
					<table class="nekoko-dashboard-table">
						<thead>
							<tr><th>Usluga</th><th>Klijent</th><th>Datum</th></tr>
						</thead>
						<tbody>
						<?php foreach ( $completed_bookings as $booking ) :
							$bdate   = get_post_meta( $booking->ID, '_booking_date', true );
							$bname   = get_post_meta( $booking->ID, '_booking_customer_name', true );
							$bemail  = get_post_meta( $booking->ID, '_booking_customer_email', true );
							$bjob_id = (int) get_post_meta( $booking->ID, '_booking_job_id', true );
							$job_link = $bjob_id ? get_permalink( $bjob_id ) : '#';
							$job_name = $bjob_id ? get_the_title( $bjob_id ) : '—';
							?>
							<tr>
								<td><a href="<?php echo esc_url( $job_link ); ?>"><?php echo esc_html( $job_name ); ?></a></td>
								<td><?php echo esc_html( $bname ); ?><br><small><?php echo esc_html( $bemail ); ?></small></td>
								<td><?php echo $bdate ? esc_html( date_i18n( 'd.m.Y', strtotime( $bdate ) ) ) : '—'; ?></td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				<?php endif; ?>
			</section>

		</div>
	</div>
</div>
