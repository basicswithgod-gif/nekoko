<?php
/**
 * Dynamic block: Job Categories grid (links to filtered job archive).
 *
 * @var array $block
 */
defined( 'ABSPATH' ) || exit;

$categories = get_terms( [ 'taxonomy' => Nekoko_Taxonomies::CATEGORY, 'hide_empty' => false ] );
$icons      = [
	'Umetnost'  => '🎨',
	'Lepota'    => '💄',
	'Zdravlje'  => '💪',
	'Životinje' => '🐾',
	'Zabava'    => '🎉',
	'Ostalo'    => '⚙️',
];
?>
<div class="nekoko-block-job-categories">
	<?php if ( ! is_wp_error( $categories ) && $categories ) : ?>
		<div class="nekoko-categories-grid">
			<?php foreach ( $categories as $category ) :
				$icon = $icons[ $category->name ] ?? '🔧';
				?>
				<a href="<?php echo esc_url( get_term_link( $category ) ); ?>" class="nekoko-category-card">
					<span class="icon"><?php echo esc_html( $icon ); ?></span>
					<h3><?php echo esc_html( $category->name ); ?></h3>
				</a>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>
