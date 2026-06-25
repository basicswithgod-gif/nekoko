<?php
/**
 * Block render: acf/nekoko-hero
 * Outer wrapper only. All editable content lives in InnerBlocks so the user
 * can click any element directly in Gutenberg to edit it.
 */
defined( 'ABSPATH' ) || exit;

$stock = get_stylesheet_directory_uri() . '/assets/images/stock-photos.png';

$template = wp_json_encode( [
	[ 'core/group', [ 'className' => 'nekoko-hp-hero__content', 'layout' => [ 'type' => 'default' ] ], [
		[ 'core/paragraph', [ 'className' => 'nekoko-hp-hero__eyebrow', 'content' => 'Start-up za neobične poslove' ] ],
		[ 'core/heading',   [ 'level' => 1, 'className' => 'nekoko-hp-hero__heading', 'content' => 'Mesto gde neobični poslovi dobijaju svoje ljude' ] ],
		[ 'core/paragraph', [ 'className' => 'nekoko-hp-hero__paragraph', 'content' => 'NekoKo spaja ljude koji nude ili traže specifične usluge, pomoć i male zadatke koje klasični oglasi često ne prepoznaju.' ] ],
		[ 'core/buttons', [ 'className' => 'nekoko-hp-hero__buttons' ], [
			[ 'core/button', [ 'text' => 'Pretražite oglase', 'url' => '/pretraga/', 'className' => 'nekoko-btn' ] ],
			[ 'core/button', [ 'text' => 'Kako funkcioniše', 'url' => '/kako-funkcionise/', 'className' => 'nekoko-btn--outline' ] ],
		] ],
	] ],
	[ 'core/group', [ 'className' => 'nekoko-hp-hero__images', 'layout' => [ 'type' => 'default' ] ], [
		[ 'core/group', [ 'className' => 'nekoko-hp-hero__grid', 'layout' => [ 'type' => 'default' ] ], [
			[ 'core/image', [ 'className' => 'nekoko-hp-hero__img-wrap', 'url' => $stock, 'alt' => '' ] ],
			[ 'core/image', [ 'className' => 'nekoko-hp-hero__img-wrap', 'url' => $stock, 'alt' => '' ] ],
			[ 'core/image', [ 'className' => 'nekoko-hp-hero__img-wrap', 'url' => $stock, 'alt' => '' ] ],
			[ 'core/image', [ 'className' => 'nekoko-hp-hero__img-wrap', 'url' => $stock, 'alt' => '' ] ],
		] ],
		[ 'core/group', [ 'className' => 'nekoko-hp-hero__badge', 'layout' => [ 'type' => 'default' ] ], [
			[ 'core/paragraph', [ 'className' => 'nekoko-hp-hero__badge-number', 'content' => '1.240+' ] ],
			[ 'core/paragraph', [ 'className' => 'nekoko-hp-hero__badge-label',  'content' => 'aktivnih oglasa' ] ],
		] ],
	] ],
] );
?>
<section class="nekoko-hp-hero">
	<div class="nekoko-hp-hero__inner">
		<InnerBlocks template='<?php echo esc_attr( $template ); ?>' templateLock="false" />
	</div>
</section>
