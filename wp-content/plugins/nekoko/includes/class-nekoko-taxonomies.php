<?php
defined( 'ABSPATH' ) || exit;

/**
 * Registers the 5 custom taxonomies for the Jobs CPT and enforces
 * case-insensitive uniqueness across all of them.
 */
class Nekoko_Taxonomies {

	/** Taxonomy keys. */
	const CATEGORY    = 'job_category';
	const SUBCATEGORY = 'job_subcategory';
	const TAG         = 'job_tag';
	const CITY        = 'job_city';
	const TYPE        = 'job_type';

	/** All taxonomy keys that must enforce case-insensitive uniqueness. */
	const ALL = [ self::CATEGORY, self::SUBCATEGORY, self::TAG, self::CITY, self::TYPE ];

	public static function init() {
		add_action( 'init', [ __CLASS__, 'register' ] );
		add_action( 'init', [ __CLASS__, 'seed_terms' ], 20 );
		add_filter( 'pre_insert_term', [ __CLASS__, 'enforce_case_insensitive_uniqueness' ], 10, 2 );
	}

	public static function register() {
		register_taxonomy(
			self::CATEGORY,
			Nekoko_CPT::POST_TYPE,
			[
				'labels'       => [ 'name' => 'Categories', 'singular_name' => 'Category', 'menu_name' => 'Categories' ],
				'hierarchical' => true,
				'show_ui'      => true,
				'show_in_rest' => true,
				'rewrite'      => [ 'slug' => 'job-category' ],
				'public'       => true,
			]
		);

		register_taxonomy(
			self::SUBCATEGORY,
			Nekoko_CPT::POST_TYPE,
			[
				'labels'       => [ 'name' => 'Subcategories', 'singular_name' => 'Subcategory', 'menu_name' => 'Subcategories' ],
				'hierarchical' => true,
				'show_ui'      => true,
				'show_in_rest' => true,
				'rewrite'      => [ 'slug' => 'job-subcategory' ],
				'public'       => true,
			]
		);

		register_taxonomy(
			self::TAG,
			Nekoko_CPT::POST_TYPE,
			[
				'labels'       => [ 'name' => 'Tags', 'singular_name' => 'Tag', 'menu_name' => 'Tags' ],
				'hierarchical' => false,
				'show_ui'      => true,
				'show_in_rest' => true,
				'rewrite'      => [ 'slug' => 'job-tag' ],
				'public'       => true,
			]
		);

		register_taxonomy(
			self::CITY,
			Nekoko_CPT::POST_TYPE,
			[
				'labels'       => [ 'name' => 'Cities', 'singular_name' => 'City', 'menu_name' => 'Cities' ],
				'hierarchical' => false,
				'show_ui'      => true,
				'show_in_rest' => true,
				'rewrite'      => [ 'slug' => 'job-city' ],
				'public'       => true,
			]
		);

		register_taxonomy(
			self::TYPE,
			Nekoko_CPT::POST_TYPE,
			[
				'labels'       => [ 'name' => 'Types', 'singular_name' => 'Type', 'menu_name' => 'Type' ],
				'hierarchical' => false,
				'show_ui'      => true,
				'show_in_rest' => true,
				'rewrite'      => [ 'slug' => 'job-type' ],
				'public'       => true,
			]
		);
	}

	/**
	 * Seed default terms once on init (idempotent - checked via term_exists each run).
	 */
	public static function seed_terms() {
		foreach ( [ 'Umetnost', 'Lepota', 'Zdravlje', 'Životinje', 'Zabava', 'Ostalo' ] as $term ) {
			if ( ! term_exists( $term, self::CATEGORY ) ) {
				wp_insert_term( $term, self::CATEGORY );
			}
		}
		foreach ( [ 'Ponuda', 'Potražnja' ] as $term ) {
			if ( ! term_exists( $term, self::TYPE ) ) {
				wp_insert_term( $term, self::TYPE );
			}
		}
	}

	/**
	 * Blocks inserting a term whose name matches an existing term in the same
	 * taxonomy with different letter case (e.g. "Beograd" vs "beograd").
	 * WordPress core uniqueness check is case-sensitive, so it lets "beograd"
	 * through even when "Beograd" already exists - this closes that gap.
	 *
	 * @param string $term     Term name being inserted.
	 * @param string $taxonomy Taxonomy slug.
	 * @return string|WP_Error
	 */
	public static function enforce_case_insensitive_uniqueness( $term, $taxonomy ) {
		if ( ! in_array( $taxonomy, self::ALL, true ) ) {
			return $term;
		}

		$existing = get_terms(
			[
				'taxonomy'   => $taxonomy,
				'hide_empty' => false,
				'fields'     => 'names',
			]
		);

		if ( is_wp_error( $existing ) ) {
			return $term;
		}

		foreach ( $existing as $existing_name ) {
			if ( mb_strtolower( $existing_name ) === mb_strtolower( $term ) && $existing_name !== $term ) {
				return new WP_Error(
					'term_exists_case_insensitive',
					sprintf( 'A term named "%s" already exists in this taxonomy (case-insensitive match).', $existing_name )
				);
			}
		}

		return $term;
	}
}
