<?php
defined( 'ABSPATH' ) || exit;

/**
 * [nekoko_hp_search] — compact homepage search form.
 * Renders a GET form with dynamic job_category dropdown → /pretraga/
 * Parameters match class-shortcode-search-filter.php: kw= and kat=
 */
class Nekoko_Shortcode_Hp_Search {

	public static function init() {
		add_shortcode( 'nekoko_hp_search', [ __CLASS__, 'render' ] );
	}

	public static function render() {
		$terms = get_terms( [
			'taxonomy'   => Nekoko_Taxonomies::CATEGORY,
			'hide_empty' => false,
			'orderby'    => 'name',
			'order'      => 'ASC',
		] );

		ob_start();
		?>
		<form action="/pretraga/" method="GET" class="nekoko-search-form">
			<input type="text" name="kw" placeholder="<?php esc_attr_e( 'Šta tražite?', 'nekoko' ); ?>" class="nekoko-search-input">
			<select name="kat" class="nekoko-search-select">
				<option value=""><?php esc_html_e( 'Sve kategorije', 'nekoko' ); ?></option>
				<?php if ( ! is_wp_error( $terms ) ) : ?>
					<?php foreach ( $terms as $term ) : ?>
						<option value="<?php echo esc_attr( $term->slug ); ?>"><?php echo esc_html( $term->name ); ?></option>
					<?php endforeach; ?>
				<?php endif; ?>
			</select>
			<button type="submit" class="nekoko-search-btn"><?php esc_html_e( 'Pretraži', 'nekoko' ); ?></button>
		</form>
		<?php
		return ob_get_clean();
	}
}
