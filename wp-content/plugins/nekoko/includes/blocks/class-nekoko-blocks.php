<?php
defined( 'ABSPATH' ) || exit;

/**
 * Custom Gutenberg blocks for NekoKo. Registered as ACF Blocks — no JS build
 * step required. All render markup lives in blocks/{slug}/render.php.
 *
 * Blocks:
 *  - acf/nekoko-featured-jobs    (existing)
 *  - acf/nekoko-job-categories   (existing)
 *  - acf/nekoko-hero             (homepage)
 *  - acf/nekoko-search-bar       (homepage)
 *  - acf/nekoko-offer-preview    (homepage)
 *  - acf/nekoko-top-categories   (homepage, reused for Ponuda + Potražnja)
 *  - acf/nekoko-request-preview  (homepage)
 */
class Nekoko_Blocks {

	public static function init() {
		add_action( 'acf/init', [ __CLASS__, 'register_blocks' ] );
		add_action( 'acf/init', [ __CLASS__, 'register_fields' ] );
	}

	public static function register_blocks() {
		if ( ! function_exists( 'acf_register_block_type' ) ) {
			return;
		}

		// — existing blocks —
		acf_register_block_type( [
			'name'            => 'nekoko-featured-jobs',
			'title'           => 'Featured Jobs',
			'description'     => 'Grid of published Jobs, optionally filtered by category.',
			'render_template' => NEKOKO_PATH . 'blocks/featured-jobs/render.php',
			'category'        => 'widgets',
			'icon'            => 'portfolio',
			'keywords'        => [ 'jobs', 'listings', 'grid' ],
			'mode'            => 'preview',
		] );

		acf_register_block_type( [
			'name'            => 'nekoko-job-categories',
			'title'           => 'Job Categories',
			'description'     => 'Grid of Job category links with icons.',
			'render_template' => NEKOKO_PATH . 'blocks/job-categories/render.php',
			'category'        => 'widgets',
			'icon'            => 'category',
			'keywords'        => [ 'jobs', 'categories' ],
			'mode'            => 'preview',
		] );

		// — homepage blocks —
		acf_register_block_type( [
			'name'            => 'nekoko-hero',
			'title'           => 'Hero — Početna',
			'description'     => 'Hero sekcija početne stranice: eyebrow, H1, paragraf, 2 CTA dugmeta, 2×2 grid slika, badge.',
			'render_template' => NEKOKO_PATH . 'blocks/hero/render.php',
			'category'        => 'nekoko',
			'icon'            => 'cover-image',
			'keywords'        => [ 'hero', 'homepage', 'banner' ],
			'mode'            => 'edit',
			'supports'        => [ 'align' => false, 'jsx' => false ],
		] );

		acf_register_block_type( [
			'name'            => 'nekoko-search-bar',
			'title'           => 'Search Bar — Početna',
			'description'     => 'Pretraga: naslov, podnaslov, text input, dropdown kategorija, submit dugme.',
			'render_template' => NEKOKO_PATH . 'blocks/search-bar/render.php',
			'category'        => 'nekoko',
			'icon'            => 'search',
			'keywords'        => [ 'search', 'pretraga' ],
			'mode'            => 'edit',
			'supports'        => [ 'align' => false ],
		] );

		acf_register_block_type( [
			'name'            => 'nekoko-offer-preview',
			'title'           => 'Offer Preview — Početna',
			'description'     => 'Istaknite ponudu: levo tekst+CTA, desno job kartice (Ponuda tip).',
			'render_template' => NEKOKO_PATH . 'blocks/offer-preview/render.php',
			'category'        => 'nekoko',
			'icon'            => 'list-view',
			'keywords'        => [ 'offer', 'ponuda', 'jobs' ],
			'mode'            => 'edit',
			'supports'        => [ 'align' => false ],
		] );

		acf_register_block_type( [
			'name'            => 'nekoko-top-categories',
			'title'           => 'Top Categories — Početna',
			'description'     => 'Mreža kategorija sa ikonama. Koristiti dva puta: jednom za Ponudu, jednom za Potražnju.',
			'render_template' => NEKOKO_PATH . 'blocks/top-categories/render.php',
			'category'        => 'nekoko',
			'icon'            => 'grid-view',
			'keywords'        => [ 'categories', 'kategorije' ],
			'mode'            => 'edit',
			'supports'        => [ 'align' => false ],
		] );

		acf_register_block_type( [
			'name'            => 'nekoko-request-preview',
			'title'           => 'Request Preview — Početna',
			'description'     => 'Recite šta vam treba: levo job kartice (Potražnja tip), desno tekst+CTA.',
			'render_template' => NEKOKO_PATH . 'blocks/request-preview/render.php',
			'category'        => 'nekoko',
			'icon'            => 'list-view',
			'keywords'        => [ 'request', 'potraznja', 'jobs' ],
			'mode'            => 'edit',
			'supports'        => [ 'align' => false ],
		] );
	}

	public static function register_fields() {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}

		// Dynamically populate tc_categories checkbox choices from job_category terms.
		add_filter( 'acf/load_field/key=field_nekoko_tc_categories', [ __CLASS__, 'load_tc_categories_choices' ] );

		// ── existing: Featured Jobs ──────────────────────────────────────────
		acf_add_local_field_group( [
			'key'      => 'group_nekoko_featured_jobs',
			'title'    => 'Featured Jobs Settings',
			'fields'   => [
				[
					'key'           => 'field_nekoko_fj_limit',
					'label'         => 'Number of jobs',
					'name'          => 'limit',
					'type'          => 'number',
					'default_value' => 6,
					'min'           => 1,
					'max'           => 24,
				],
				[
					'key'           => 'field_nekoko_fj_category',
					'label'         => 'Category (optional)',
					'name'          => 'category',
					'type'          => 'taxonomy',
					'taxonomy'      => Nekoko_Taxonomies::CATEGORY,
					'field_type'    => 'select',
					'allow_null'    => 1,
					'return_format' => 'id',
				],
				[
					'key'           => 'field_nekoko_fj_columns',
					'label'         => 'Columns',
					'name'          => 'columns',
					'type'          => 'number',
					'default_value' => 3,
					'min'           => 1,
					'max'           => 4,
				],
			],
			'location' => [ [ [
				'param'    => 'block',
				'operator' => '==',
				'value'    => 'acf/nekoko-featured-jobs',
			] ] ],
		] );

		// ── nekoko/hero ──────────────────────────────────────────────────────
		acf_add_local_field_group( [
			'key'    => 'group_nekoko_hero',
			'title'  => 'Hero — Početna',
			'fields' => [
				[
					'key'   => 'field_nekoko_hero_eyebrow',
					'label' => 'Eyebrow (mali tekst iznad naslova)',
					'name'  => 'hero_eyebrow',
					'type'  => 'text',
				],
				[
					'key'   => 'field_nekoko_hero_heading',
					'label' => 'Naslov (H1)',
					'name'  => 'hero_heading',
					'type'  => 'textarea',
					'rows'  => 3,
				],
				[
					'key'   => 'field_nekoko_hero_paragraph',
					'label' => 'Paragraf',
					'name'  => 'hero_paragraph',
					'type'  => 'textarea',
					'rows'  => 3,
				],
				[
					'key'           => 'field_nekoko_hero_btn1_label',
					'label'         => 'Primarno dugme — tekst',
					'name'          => 'hero_btn_primary_label',
					'type'          => 'text',
					'default_value' => 'Pretražite oglase',
				],
				[
					'key'  => 'field_nekoko_hero_btn1_url',
					'label' => 'Primarno dugme — URL',
					'name'  => 'hero_btn_primary_url',
					'type'  => 'url',
				],
				[
					'key'           => 'field_nekoko_hero_btn2_label',
					'label'         => 'Sekundarno dugme — tekst',
					'name'          => 'hero_btn_secondary_label',
					'type'          => 'text',
					'default_value' => 'Kako funkcioniše',
				],
				[
					'key'  => 'field_nekoko_hero_btn2_url',
					'label' => 'Sekundarno dugme — URL',
					'name'  => 'hero_btn_secondary_url',
					'type'  => 'url',
				],
				[
					'key'        => 'field_nekoko_hero_images',
					'label'      => 'Slike (2×2 grid, max 4)',
					'name'       => 'hero_images',
					'type'       => 'repeater',
					'max'        => 4,
					'layout'     => 'block',
					'sub_fields' => [
						[
							'key'           => 'field_nekoko_hero_image_item',
							'label'         => 'Slika',
							'name'          => 'image',
							'type'          => 'image',
							'return_format' => 'array',
							'preview_size'  => 'medium',
						],
					],
				],
				[
					'key'           => 'field_nekoko_hero_badge_number',
					'label'         => 'Badge — broj',
					'name'          => 'hero_badge_number',
					'type'          => 'text',
					'default_value' => '1.240+',
				],
				[
					'key'           => 'field_nekoko_hero_badge_label',
					'label'         => 'Badge — label',
					'name'          => 'hero_badge_label',
					'type'          => 'text',
					'default_value' => 'aktivnih oglasa',
				],
			],
			'location' => [ [ [
				'param'    => 'block',
				'operator' => '==',
				'value'    => 'acf/nekoko-hero',
			] ] ],
		] );

		// ── nekoko/search-bar ────────────────────────────────────────────────
		acf_add_local_field_group( [
			'key'    => 'group_nekoko_search_bar',
			'title'  => 'Search Bar — Početna',
			'fields' => [
				[
					'key'   => 'field_nekoko_sb_heading',
					'label' => 'Naslov',
					'name'  => 'search_heading',
					'type'  => 'text',
				],
				[
					'key'   => 'field_nekoko_sb_subheading',
					'label' => 'Podnaslov',
					'name'  => 'search_subheading',
					'type'  => 'text',
				],
				[
					'key'           => 'field_nekoko_sb_placeholder',
					'label'         => 'Placeholder tekst za input',
					'name'          => 'search_placeholder',
					'type'          => 'text',
					'default_value' => 'Šta tražite?',
				],
				[
					'key'           => 'field_nekoko_sb_btn_label',
					'label'         => 'Tekst dugmeta',
					'name'          => 'search_btn_label',
					'type'          => 'text',
					'default_value' => 'Pretraži',
				],
			],
			'location' => [ [ [
				'param'    => 'block',
				'operator' => '==',
				'value'    => 'acf/nekoko-search-bar',
			] ] ],
		] );

		// ── nekoko/offer-preview ─────────────────────────────────────────────
		acf_add_local_field_group( [
			'key'    => 'group_nekoko_offer_preview',
			'title'  => 'Offer Preview — Početna',
			'fields' => [
				[
					'key'   => 'field_nekoko_op_heading',
					'label' => 'Naslov',
					'name'  => 'offer_heading',
					'type'  => 'text',
				],
				[
					'key'   => 'field_nekoko_op_paragraph',
					'label' => 'Paragraf',
					'name'  => 'offer_paragraph',
					'type'  => 'textarea',
					'rows'  => 4,
				],
				[
					'key'           => 'field_nekoko_op_btn_label',
					'label'         => 'Tekst dugmeta',
					'name'          => 'offer_btn_label',
					'type'          => 'text',
					'default_value' => 'Objavite ponudu',
				],
				[
					'key'  => 'field_nekoko_op_btn_url',
					'label' => 'URL dugmeta',
					'name'  => 'offer_btn_url',
					'type'  => 'url',
				],
				[
					'key'           => 'field_nekoko_op_card_count',
					'label'         => 'Broj kartica',
					'name'          => 'offer_card_count',
					'type'          => 'number',
					'default_value' => 3,
					'min'           => 1,
					'max'           => 6,
				],
			],
			'location' => [ [ [
				'param'    => 'block',
				'operator' => '==',
				'value'    => 'acf/nekoko-offer-preview',
			] ] ],
		] );

		// ── nekoko/top-categories ────────────────────────────────────────────
		acf_add_local_field_group( [
			'key'    => 'group_nekoko_top_categories',
			'title'  => 'Top Categories — Početna',
			'fields' => [
				[
					'key'   => 'field_nekoko_tc_heading',
					'label' => 'Naslov',
					'name'  => 'tc_heading',
					'type'  => 'text',
				],
				[
					'key'   => 'field_nekoko_tc_subheading',
					'label' => 'Podnaslov',
					'name'  => 'tc_subheading',
					'type'  => 'text',
				],
				[
					'key'     => 'field_nekoko_tc_type',
					'label'   => 'Tip (kontroliše URL arhive i podrazumevani naslov)',
					'name'    => 'tc_type',
					'type'    => 'select',
					'choices' => [
						'ponuda'   => 'Ponuda',
						'potraznja' => 'Potražnja',
					],
					'default_value' => 'ponuda',
					'allow_null'    => 0,
					'return_format' => 'value',
				],
				[
					'key'     => 'field_nekoko_tc_categories',
					'label'   => 'Kategorije za prikaz (prazno = sve)',
					'name'    => 'tc_categories',
					'type'    => 'checkbox',
					'choices' => [],
					'layout'  => 'vertical',
				],
			],
			'location' => [ [ [
				'param'    => 'block',
				'operator' => '==',
				'value'    => 'acf/nekoko-top-categories',
			] ] ],
		] );

		// ── nekoko/request-preview ───────────────────────────────────────────
		acf_add_local_field_group( [
			'key'    => 'group_nekoko_request_preview',
			'title'  => 'Request Preview — Početna',
			'fields' => [
				[
					'key'   => 'field_nekoko_rp_heading',
					'label' => 'Naslov',
					'name'  => 'request_heading',
					'type'  => 'text',
				],
				[
					'key'   => 'field_nekoko_rp_paragraph',
					'label' => 'Paragraf',
					'name'  => 'request_paragraph',
					'type'  => 'textarea',
					'rows'  => 4,
				],
				[
					'key'           => 'field_nekoko_rp_btn_label',
					'label'         => 'Tekst dugmeta',
					'name'          => 'request_btn_label',
					'type'          => 'text',
					'default_value' => 'Objavite potražnju',
				],
				[
					'key'  => 'field_nekoko_rp_btn_url',
					'label' => 'URL dugmeta',
					'name'  => 'request_btn_url',
					'type'  => 'url',
				],
				[
					'key'           => 'field_nekoko_rp_card_count',
					'label'         => 'Broj kartica',
					'name'          => 'request_card_count',
					'type'          => 'number',
					'default_value' => 3,
					'min'           => 1,
					'max'           => 6,
				],
			],
			'location' => [ [ [
				'param'    => 'block',
				'operator' => '==',
				'value'    => 'acf/nekoko-request-preview',
			] ] ],
		] );
	}

	/**
	 * Dynamically populate checkbox choices from job_category terms.
	 */
	public static function load_tc_categories_choices( $field ) {
		$terms = get_terms( [ 'taxonomy' => Nekoko_Taxonomies::CATEGORY, 'hide_empty' => false ] );
		$field['choices'] = [];
		if ( ! is_wp_error( $terms ) && $terms ) {
			foreach ( $terms as $term ) {
				$field['choices'][ $term->term_id ] = $term->name;
			}
		}
		return $field;
	}
}
