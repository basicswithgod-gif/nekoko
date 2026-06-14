<?php
defined( 'ABSPATH' ) || exit;
?>
<?php astra_content_bottom(); ?>
	</div> <!-- ast-container -->
	</div><!-- #content -->
<?php
	astra_content_after();
	astra_footer_before();
?>

<footer class="nekoko-footer" itemtype="https://schema.org/WPFooter" itemscope>
    <div class="nekoko-footer__inner">
        <div class="nekoko-footer__brand">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="nekoko-footer__logo">
                Neko<span>Ko</span>.rs
            </a>
            <p>Platforma za nišne usluge u Srbiji.</p>
        </div>

        <?php if ( has_nav_menu( 'footer' ) ) : ?>
        <nav class="nekoko-footer__nav" aria-label="<?php esc_attr_e( 'Footer Navigation', 'nekoko-child' ); ?>">
            <?php wp_nav_menu( [
                'theme_location' => 'footer',
                'container'      => false,
                'menu_class'     => 'nekoko-footer__links',
                'depth'          => 1,
            ] ); ?>
        </nav>
        <?php endif; ?>

        <div class="nekoko-footer__meta">
            <p>&copy; <?php echo esc_html( date( 'Y' ) ); ?> NekoKo.rs</p>
            <p class="nekoko-footer__disclaimer">NekoKo ne procesira plaćanja i ne garantuje usluge trećih strana.</p>
        </div>
    </div>
</footer>

<?php astra_footer_after(); ?>
	</div><!-- #page -->
<?php
	astra_body_bottom();
	wp_footer();
?>
	</body>
</html>