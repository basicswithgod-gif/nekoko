<?php
defined( 'ABSPATH' ) || exit;

/**
 * Custom Gutenberg blocks for elements that can't be built with native blocks
 * (dynamic CPT/taxonomy queries). Registered as ACF Blocks - no JS build step
 * required, keeps render markup in plain render.php template files.
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

		acf_register_block_type(
			[
				'name'            => 'nekoko-featured-jobs',
				'title'           => 'Featured Jobs',
				'description'     => 'Grid of published Jobs, optionally filtered by category.',
				'render_template' => NEKOKO_PATH . 'blocks/featured-jobs/render.php',
				'category'        => 'widgets',
				'icon'            => 'portfolio',
				'keywords'        => [ 'jobs', 'listings', 'grid' ],
				'mode'            => 'preview',
			]
		);

		acf_register_block_type(
			[
				'name'            => 'nekoko-job-categories',
				'title'           => 'Job Categories',
				'description'     => 'Grid of Job category links with icons.',
				'render_template' => NEKOKO_PATH . 'blocks/job-categories/render.php',
				'category'        => 'widgets',
				'icon'            => 'category',
				'keywords'        => [ 'jobs', 'categories' ],
				'mode'            => 'preview',
			]
		);
	}

	public static function register_fields() {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}

		acf_add_local_field_group(
			[
				'key'      => 'group_nekoko_featured_jobs',
				'title'    => 'Featured Jobs Settings',
				'fields'   => [
					[
						'key'     => 'field_nekoko_fj_limit',
						'label'   => 'Number of jobs',
						'name'    => 'limit',
						'type'    => 'number',
						'default_value' => 6,
						'min'     => 1,
						'max'     => 24,
					],
					[
						'key'     => 'field_nekoko_fj_category',
						'label'   => 'Category (optional)',
						'name'    => 'category',
						'type'    => 'taxonomy',
						'taxonomy' => Nekoko_Taxonomies::CATEGORY,
						'field_type' => 'select',
						'allow_null' => 1,
						'return_format' => 'id',
					],
					[
						'key'     => 'field_nekoko_fj_columns',
						'label'   => 'Columns',
						'name'    => 'columns',
						'type'    => 'number',
						'default_value' => 3,
						'min'     => 1,
						'max'     => 4,
					],
				],
				'location' => [
					[
						[
							'param'    => 'block',
							'operator' => '==',
							'value'    => 'acf/nekoko-featured-jobs',
						],
					],
				],
			]
		);
	}
}
