<?php
defined( 'ABSPATH' ) || exit;
?>
<footer class="nekoko-footer" itemtype="https://schema.org/WPFooter" itemscope>
	<div class="nekoko-footer__inner">

		<div class="nekoko-footer__brand">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="nekoko-footer__logo">
				<img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/images/profile-nekoko.png' ); ?>"
				     alt="NekoKo.rs" height="56" width="56">
			</a>
			<?php if ( is_active_sidebar( 'footer-tagline' ) ) : ?>
				<div class="nekoko-footer__tagline">
					<?php dynamic_sidebar( 'footer-tagline' ); ?>
				</div>
			<?php else : ?>
				<p class="nekoko-footer__tagline-default">Portal zaneobičnih poslove, male potrebe i ljude koji umeju da pomognu kada standardna rešenja nisu dovoljna</p>
			<?php endif; ?>
		</div>

		<div class="nekoko-footer__col">
			<h4 class="nekoko-footer__col-heading">Ponuda</h4>
			<?php
			wp_nav_menu( [
				'theme_location' => 'footer-ponuda',
				'container'      => false,
				'menu_class'     => 'nekoko-footer__links',
				'depth'          => 1,
				'fallback_cb'    => false,
			] );
			?>
		</div>

		<div class="nekoko-footer__col">
			<h4 class="nekoko-footer__col-heading">Potražnja</h4>
			<?php
			wp_nav_menu( [
				'theme_location' => 'footer-potraznja',
				'container'      => false,
				'menu_class'     => 'nekoko-footer__links',
				'depth'          => 1,
				'fallback_cb'    => false,
			] );
			?>
		</div>

		<div class="nekoko-footer__col">
			<h4 class="nekoko-footer__col-heading">Informacije</h4>
			<?php
			wp_nav_menu( [
				'theme_location' => 'footer-informacije',
				'container'      => false,
				'menu_class'     => 'nekoko-footer__links',
				'depth'          => 1,
				'fallback_cb'    => false,
			] );
			?>
		</div>

	</div>

	<div class="nekoko-footer__bottom">
		<div class="nekoko-footer__bottom-inner">
			<p>2026 NekoKO &middot; Sva prava zadržana</p>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
