<?php
/**
 * Expects: $jobs (array of WP_Post)
 */
defined( 'ABSPATH' ) || exit;
?>
<div class="wrap">
	<h1>Jobs pending</h1>
	<table class="widefat striped">
		<thead>
			<tr><th>Naziv</th><th>Provajder</th><th>Kategorija</th><th>Datum</th><th>Akcije</th></tr>
		</thead>
		<tbody>
		<?php foreach ( $jobs as $job ) :
			$provider    = get_user_by( 'id', $job->post_author );
			$categories  = get_the_terms( $job->ID, Nekoko_Taxonomies::CATEGORY );
			$category    = $categories && ! is_wp_error( $categories ) ? implode( ', ', wp_list_pluck( $categories, 'name' ) ) : '-';
			$approve_url = wp_nonce_url( admin_url( 'admin.php?page=nekoko-pending-jobs&action=approve-job&post_id=' . $job->ID ), 'nekoko_job_action' );
			$reject_url  = wp_nonce_url( admin_url( 'admin.php?page=nekoko-pending-jobs&action=reject-job&post_id=' . $job->ID ), 'nekoko_job_action' );
			?>
			<tr>
				<td><a href="<?php echo esc_url( get_edit_post_link( $job->ID ) ); ?>"><?php echo esc_html( $job->post_title ); ?></a></td>
				<td><?php echo esc_html( $provider ? $provider->display_name : '-' ); ?></td>
				<td><?php echo esc_html( $category ); ?></td>
				<td><?php echo esc_html( date_i18n( 'd.m.Y', strtotime( $job->post_date ) ) ); ?></td>
				<td>
					<a href="<?php echo esc_url( $approve_url ); ?>" class="button button-primary" style="margin-right:8px;">Odobri</a>
					<a href="<?php echo esc_url( $reject_url ); ?>" class="button">Odbij</a>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>
