<?php
defined( 'ABSPATH' ) || exit;

/**
 * nekoko_review CPT — one post per review.
 *
 * Meta fields:
 *   _review_provider_id   (int)    WP user ID of the reviewed provider
 *   _review_job_id        (int)    Job post ID that was reviewed
 *   _review_booking_id    (int)    Booking post ID that gated this review
 *   _review_reviewer_name (string) Customer display name from the booking
 *   _review_rating        (int)    1-5
 *
 * post_content holds the review text; post_date holds the submission date.
 * job_category taxonomy is attached and stamped from the job at save time.
 */
class Nekoko_Review_CPT {

	const POST_TYPE = 'nekoko_review';

	public static function init() {
		add_action( 'init', [ __CLASS__, 'register' ] );
	}

	public static function register() {
		register_post_type(
			self::POST_TYPE,
			[
				'labels'          => [
					'name'          => 'Reviews',
					'singular_name' => 'Review',
					'menu_name'     => 'Reviews',
					'all_items'     => 'All Reviews',
					'view_item'     => 'View Review',
					'search_items'  => 'Search Reviews',
					'not_found'     => 'No reviews found',
				],
				'public'          => false,
				'show_ui'         => true,
				'show_in_menu'    => true,
				'supports'        => [ 'title', 'editor', 'custom-fields' ],
				'show_in_rest'    => false,
				'menu_icon'       => 'dashicons-star-filled',
				'capability_type' => 'post',
				'map_meta_cap'    => true,
			]
		);

		register_taxonomy_for_object_type( Nekoko_Taxonomies::CATEGORY, self::POST_TYPE );
	}

	/**
	 * Save a new review. Stamps job_category from the job, recalculates cached
	 * avg_rating and review_count on the job post.
	 *
	 * @param int    $provider_id   WP user ID of the provider.
	 * @param int    $job_id        Job post ID.
	 * @param int    $booking_id    Booking post ID.
	 * @param string $reviewer_name Customer display name.
	 * @param int    $rating        1-5.
	 * @param string $comment       Review text.
	 * @return int|WP_Error
	 */
	public static function save( $provider_id, $job_id, $booking_id, $reviewer_name, $rating, $comment ) {
		$job = get_post( (int) $job_id );
		if ( ! $job || $job->post_type !== Nekoko_CPT::POST_TYPE ) {
			return new WP_Error( 'invalid_job', 'Job not found.' );
		}

		$post_id = wp_insert_post(
			[
				'post_type'    => self::POST_TYPE,
				'post_title'   => 'Review — ' . $job->post_title,
				'post_content' => $comment,
				'post_status'  => 'publish',
				'post_date'    => current_time( 'mysql' ),
			],
			true
		);

		if ( is_wp_error( $post_id ) ) {
			return $post_id;
		}

		update_post_meta( $post_id, '_review_provider_id',   (int) $provider_id );
		update_post_meta( $post_id, '_review_job_id',        (int) $job_id );
		update_post_meta( $post_id, '_review_booking_id',    (int) $booking_id );
		update_post_meta( $post_id, '_review_reviewer_name', sanitize_text_field( $reviewer_name ) );
		update_post_meta( $post_id, '_review_rating',        min( 5, max( 1, (int) $rating ) ) );

		$cats = wp_get_post_terms( $job_id, Nekoko_Taxonomies::CATEGORY );
		if ( $cats && ! is_wp_error( $cats ) ) {
			wp_set_post_terms( $post_id, [ $cats[0]->term_id ], Nekoko_Taxonomies::CATEGORY );
		}

		self::recalculate_job_cache( $job_id );

		return $post_id;
	}

	/**
	 * Get reviews for a specific job listing.
	 *
	 * @param int $job_id
	 * @return WP_Post[]
	 */
	public static function get_for_job( $job_id ) {
		return get_posts(
			[
				'post_type'   => self::POST_TYPE,
				'post_status' => 'publish',
				'numberposts' => -1,
				'orderby'     => 'date',
				'order'       => 'DESC',
				'meta_query'  => [
					[
						'key'     => '_review_job_id',
						'value'   => (int) $job_id,
						'type'    => 'NUMERIC',
						'compare' => '=',
					],
				],
			]
		);
	}

	/**
	 * Get reviews for a provider, optionally filtered by job_category term_id.
	 *
	 * @param int $provider_id WP user ID.
	 * @param int $category_id job_category term ID; 0 = all categories.
	 * @return WP_Post[]
	 */
	public static function get_for_provider( $provider_id, $category_id = 0 ) {
		$args = [
			'post_type'   => self::POST_TYPE,
			'post_status' => 'publish',
			'numberposts' => -1,
			'orderby'     => 'date',
			'order'       => 'DESC',
			'meta_query'  => [
				[
					'key'     => '_review_provider_id',
					'value'   => (int) $provider_id,
					'type'    => 'NUMERIC',
					'compare' => '=',
				],
			],
		];

		if ( $category_id ) {
			$args['tax_query'] = [
				[
					'taxonomy' => Nekoko_Taxonomies::CATEGORY,
					'field'    => 'term_id',
					'terms'    => [ (int) $category_id ],
				],
			];
		}

		return get_posts( $args );
	}

	/**
	 * Get the distinct job_category terms that a provider has reviews in.
	 *
	 * @param int $provider_id WP user ID.
	 * @return WP_Term[]
	 */
	public static function get_categories_for_provider( $provider_id ) {
		$review_ids = get_posts(
			[
				'post_type'   => self::POST_TYPE,
				'post_status' => 'publish',
				'numberposts' => -1,
				'fields'      => 'ids',
				'meta_query'  => [
					[
						'key'     => '_review_provider_id',
						'value'   => (int) $provider_id,
						'type'    => 'NUMERIC',
						'compare' => '=',
					],
				],
			]
		);

		if ( empty( $review_ids ) ) {
			return [];
		}

		$term_ids = [];
		foreach ( $review_ids as $review_id ) {
			$terms = wp_get_post_terms( $review_id, Nekoko_Taxonomies::CATEGORY, [ 'fields' => 'ids' ] );
			if ( ! is_wp_error( $terms ) ) {
				$term_ids = array_merge( $term_ids, $terms );
			}
		}

		if ( empty( $term_ids ) ) {
			return [];
		}

		return get_terms(
			[
				'taxonomy'   => Nekoko_Taxonomies::CATEGORY,
				'include'    => array_unique( $term_ids ),
				'hide_empty' => false,
			]
		);
	}

	/**
	 * Recalculate and cache _nekoko_avg_rating and _nekoko_review_count on a job post.
	 * Called after every review save.
	 */
	private static function recalculate_job_cache( $job_id ) {
		$review_ids = get_posts(
			[
				'post_type'   => self::POST_TYPE,
				'post_status' => 'publish',
				'numberposts' => -1,
				'fields'      => 'ids',
				'meta_query'  => [
					[
						'key'     => '_review_job_id',
						'value'   => (int) $job_id,
						'type'    => 'NUMERIC',
						'compare' => '=',
					],
				],
			]
		);

		if ( empty( $review_ids ) ) {
			delete_post_meta( $job_id, '_nekoko_avg_rating' );
			delete_post_meta( $job_id, '_nekoko_review_count' );
			return;
		}

		$ratings = array_map(
			fn( $id ) => (int) get_post_meta( $id, '_review_rating', true ),
			$review_ids
		);

		$avg   = round( array_sum( $ratings ) / count( $ratings ), 1 );
		$count = count( $ratings );

		update_post_meta( $job_id, '_nekoko_avg_rating',   $avg );
		update_post_meta( $job_id, '_nekoko_review_count', $count );
	}
}
