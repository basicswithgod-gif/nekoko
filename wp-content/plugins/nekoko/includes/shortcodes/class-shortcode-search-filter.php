<?php
defined( 'ABSPATH' ) || exit;

/**
 * [nekoko_search_filter] - keyword/category/city/price/rating filter + sort for Jobs.
 */
class Nekoko_Shortcode_Search_Filter {

	public static function init() {
		add_shortcode( 'nekoko_search_filter', [ __CLASS__, 'render' ] );
	}

	public static function render() {
		$kw        = sanitize_text_field( wp_unslash( $_GET['kw'] ?? '' ) );
		$kat       = sanitize_text_field( wp_unslash( $_GET['kat'] ?? '' ) );
		$grad      = sanitize_text_field( wp_unslash( $_GET['grad'] ?? '' ) );
		$cena_min  = isset( $_GET['cena_min'] ) && $_GET['cena_min'] !== '' ? intval( $_GET['cena_min'] ) : '';
		$cena_max  = isset( $_GET['cena_max'] ) && $_GET['cena_max'] !== '' ? intval( $_GET['cena_max'] ) : '';
		$min_ocena = intval( $_GET['min_ocena'] ?? 0 );
		$sortiraj  = sanitize_key( $_GET['sortiraj'] ?? 'newest' );

		$args = [ 'post_type' => Nekoko_CPT::POST_TYPE, 'post_status' => 'publish', 'posts_per_page' => 12 ];

		if ( $kw ) {
			$args['s'] = $kw;
		}
		if ( $kat ) {
			$args['tax_query'] = [ [ 'taxonomy' => Nekoko_Taxonomies::CATEGORY, 'field' => 'slug', 'terms' => $kat ] ];
		}
		if ( $grad ) {
			$args['tax_query']   = $args['tax_query'] ?? [];
			$args['tax_query'][] = [ 'taxonomy' => Nekoko_Taxonomies::CITY, 'field' => 'slug', 'terms' => $grad ];
		}

		$meta_query = [];
		if ( $cena_min !== '' ) {
			$meta_query[] = [ 'key' => '_nekoko_price', 'value' => $cena_min, 'type' => 'NUMERIC', 'compare' => '>=' ];
		}
		if ( $cena_max !== '' ) {
			$meta_query[] = [ 'key' => '_nekoko_price', 'value' => $cena_max, 'type' => 'NUMERIC', 'compare' => '<=' ];
		}
		if ( $min_ocena > 0 ) {
			$meta_query[] = [ 'key' => '_nekoko_avg_rating', 'value' => $min_ocena, 'type' => 'DECIMAL(3,1)', 'compare' => '>=' ];
		}
		if ( $meta_query ) {
			$args['meta_query'] = array_merge( [ 'relation' => 'AND' ], $meta_query );
		}

		switch ( $sortiraj ) {
			case 'cena_asc':
				$args['meta_key'] = '_nekoko_price';
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'ASC';
				break;
			case 'cena_desc':
				$args['meta_key'] = '_nekoko_price';
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'DESC';
				break;
			case 'ocena':
				$args['meta_key'] = '_nekoko_avg_rating';
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'DESC';
				break;
			default:
				$args['orderby'] = 'date';
				$args['order']   = 'DESC';
		}

		$query      = new WP_Query( $args );
		$categories = get_terms( [ 'taxonomy' => Nekoko_Taxonomies::CATEGORY, 'hide_empty' => false ] );
		$cities     = get_terms( [ 'taxonomy' => Nekoko_Taxonomies::CITY, 'hide_empty' => false ] );
		$page_url   = get_permalink();
		$sorts      = [ 'newest' => 'Najnovije', 'cena_asc' => 'Cena ↑', 'cena_desc' => 'Cena ↓', 'ocena' => 'Ocena ↓' ];

		ob_start();
		include NEKOKO_PATH . 'templates/shortcodes/search-filter.php';
		return ob_get_clean();
	}
}
