<?php
defined( 'ABSPATH' ) || exit;
get_header();
?>
<main class="nekoko-job-archive">
	<div class="nekoko-job-archive__layout">
		<aside class="nekoko-job-archive__sidebar">
			<h3>Kategorije</h3>
			<ul class="nekoko-job-archive__category-list">
				<?php foreach ( get_terms( [ 'taxonomy' => 'job_category', 'hide_empty' => false ] ) as $category ) : ?>
					<li>
						<a href="<?php echo esc_url( get_term_link( $category ) ); ?>">
							<?php echo esc_html( $category->name ); ?>
							<span><?php echo (int) $category->count; ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
			<hr>
			<a href="<?php echo esc_url( home_url( '/pretraga-usluga/' ) ); ?>" class="nekoko-btn nekoko-btn--block">Napredno filtriranje</a>
		</aside>

		<section class="nekoko-job-archive__results">
			<h1><?php echo the_archive_title( '', false ) ?: 'Sve usluge'; ?></h1>

			<?php if ( have_posts() ) : ?>
				<div class="nekoko-job-grid">
					<?php
					while ( have_posts() ) :
						the_post();
						include NEKOKO_PATH . 'templates/shortcodes/job-card.php';
					endwhile;
					?>
				</div>
				<?php the_posts_pagination(); ?>
			<?php else : ?>
				<div class="nekoko-no-results">
					<p>Nema usluga u ovoj kategoriji.</p>
					<a href="<?php echo esc_url( home_url() ); ?>" class="nekoko-btn">Nazad na početnu</a>
				</div>
			<?php endif; ?>
		</section>
	</div>
</main>
<?php get_footer(); ?>
