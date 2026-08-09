<?php
namespace JournalistPortfolio;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles Story Search logic, query parameters, custom meta/taxonomy search matching,
 * and AJAX live search endpoints.
 *
 * @package JournalistPortfolio
 * @since   1.4.0
 */
class Search_Handler {

	/**
	 * Instance of this class.
	 *
	 * @var Search_Handler|null
	 */
	private static ?Search_Handler $instance = null;

	/**
	 * Get singleton instance.
	 *
	 * @return Search_Handler
	 */
	public static function get_instance(): Search_Handler {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		// Restrict main search queries to CPT 'story' and modify query params.
		add_action( 'pre_get_posts', array( $this, 'restrict_search_to_stories' ) );

		// Extend WordPress search query SQL to include story meta fields & categories.
		add_filter( 'posts_join', array( $this, 'custom_search_join' ), 10, 2 );
		add_filter( 'posts_where', array( $this, 'custom_search_where' ), 10, 2 );
		add_filter( 'posts_distinct', array( $this, 'custom_search_distinct' ), 10, 2 );

		// AJAX Live Search Handlers.
		add_action( 'wp_ajax_jp_live_search', array( $this, 'handle_live_search' ) );
		add_action( 'wp_ajax_nopriv_jp_live_search', array( $this, 'handle_live_search' ) );
	}

	/**
	 * Enforce post_type => 'story' on all frontend search queries.
	 *
	 * @param \WP_Query $query Main query instance.
	 */
	public function restrict_search_to_stories( $query ): void {
		if ( ! is_admin() && $query->is_main_query() && ( $query->is_search() || isset( $_GET['s'] ) ) ) {
			$query->is_search   = true;
			$query->is_404      = false;
			$query->is_page     = false;
			$query->is_single   = false;
			$query->is_singular = false;
			$query->is_archive  = false;

			$query->set( 'post_type', 'story' );
			$query->set( 'post_status', 'publish' );

			if ( isset( $_GET['s'] ) ) {
				$query->set( 's', sanitize_text_field( wp_unslash( $_GET['s'] ) ) );
			}

			// Check for category filter passed via URL.
			if ( ! empty( $_GET['story_cat'] ) ) {
				$cat_slug = sanitize_text_field( wp_unslash( $_GET['story_cat'] ) );
				$query->set(
					'tax_query',
					array(
						array(
							'taxonomy' => 'story_category',
							'field'    => 'slug',
							'terms'    => $cat_slug,
						),
					)
				);
			}

			// Check for ordering passed via URL.
			if ( ! empty( $_GET['orderby'] ) ) {
				$orderby = sanitize_text_field( wp_unslash( $_GET['orderby'] ) );
				if ( 'title' === $orderby ) {
					$query->set( 'orderby', 'title' );
					$query->set( 'order', 'ASC' );
				} elseif ( 'date_asc' === $orderby ) {
					$query->set( 'orderby', 'date' );
					$query->set( 'order', 'ASC' );
				} else {
					$query->set( 'orderby', 'date' );
					$query->set( 'order', 'DESC' );
				}
			}
		}
	}

	/**
	 * Join postmeta and taxonomy tables when searching stories.
	 *
	 * @param string    $join  SQL JOIN clause.
	 * @param \WP_Query $query Query object.
	 * @return string Modified JOIN clause.
	 */
	public function custom_search_join( string $join, \WP_Query $query ): string {
		global $wpdb;

		if ( $this->is_story_search( $query ) ) {
			$join .= " LEFT JOIN {$wpdb->postmeta} AS jp_sm ON ({$wpdb->posts}.ID = jp_sm.post_id AND jp_sm.meta_key IN ('_story_heading', '_story_publisher', '_story_description')) ";
			$join .= " LEFT JOIN {$wpdb->term_relationships} AS jp_tr ON ({$wpdb->posts}.ID = jp_tr.object_id) ";
			$join .= " LEFT JOIN {$wpdb->term_taxonomy} AS jp_tt ON (jp_tr.term_taxonomy_id = jp_tt.term_taxonomy_id AND jp_tt.taxonomy = 'story_category') ";
			$join .= " LEFT JOIN {$wpdb->terms} AS jp_t ON (jp_tt.term_id = jp_t.term_id) ";
		}

		return $join;
	}

	/**
	 * Expand search WHERE clause to match story kicker, publisher, custom summary & category names.
	 *
	 * @param string    $where SQL WHERE clause.
	 * @param \WP_Query $query Query object.
	 * @return string Modified WHERE clause.
	 */
	public function custom_search_where( string $where, \WP_Query $query ): string {
		global $wpdb;

		if ( $this->is_story_search( $query ) ) {
			$s = $query->get( 's' );
			if ( ! empty( $s ) ) {
				$escaped_s = $wpdb->esc_like( $s );
				// Append meta and category matching OR clauses.
				$search_addition = $wpdb->prepare(
					" OR (jp_sm.meta_value LIKE %s) OR (jp_t.name LIKE %s)",
					'%' . $escaped_s . '%',
					'%' . $escaped_s . '%'
				);

				// Insert our addition before the closing parenthesis of WP search WHERE clause.
				$where = preg_replace(
					'/\(\s*' . preg_quote( $wpdb->posts, '/' ) . '\.post_title\s+LIKE\s+([^\)]+)\)/i',
					'(' . $wpdb->posts . '.post_title LIKE $1' . $search_addition . ')',
					$where,
					1
				);
			}
		}

		return $where;
	}

	/**
	 * Prevent duplicate rows caused by JOINs.
	 *
	 * @param string    $distinct SQL DISTINCT clause.
	 * @param \WP_Query $query    Query object.
	 * @return string Modified DISTINCT clause.
	 */
	public function custom_search_distinct( string $distinct, \WP_Query $query ): string {
		if ( $this->is_story_search( $query ) ) {
			return 'DISTINCT';
		}
		return $distinct;
	}

	/**
	 * Helper to check if current query is searching stories.
	 *
	 * @param \WP_Query $query Query object.
	 * @return bool True if searching stories.
	 */
	private function is_story_search( \WP_Query $query ): bool {
		if ( $query->is_search() ) {
			$post_type = $query->get( 'post_type' );
			if ( 'story' === $post_type || ( is_array( $post_type ) && in_array( 'story', $post_type, true ) ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Handle Live AJAX Search requests.
	 *
	 * @return void Outputs JSON response and exits.
	 */
	public function handle_live_search(): void {
		// Nonce Check.
		$nonce = isset( $_REQUEST['nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'jp_search_nonce' ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid security token.', 'journalist-portfolio-hub' ) ), 403 );
		}

		$search_term = isset( $_REQUEST['s'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['s'] ) ) : '';
		$cat_slug    = isset( $_REQUEST['category'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['category'] ) ) : '';

		if ( empty( trim( $search_term ) ) && empty( $cat_slug ) ) {
			wp_send_json_success(
				array(
					'results' => array(),
					'total'   => 0,
				)
			);
		}

		$args = array(
			'post_type'      => 'story',
			'post_status'    => 'publish',
			'posts_per_page' => 5,
			's'              => $search_term,
			'orderby'        => 'date',
			'order'          => 'DESC',
		);

		if ( ! empty( $cat_slug ) ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'story_category',
					'field'    => 'slug',
					'terms'    => $cat_slug,
				),
			);
		}

		$search_query = new \WP_Query( $args );
		$results      = array();

		if ( $search_query->have_posts() ) {
			while ( $search_query->have_posts() ) {
				$search_query->the_post();
				$post_id       = get_the_ID();
				$kicker        = get_post_meta( $post_id, '_story_heading', true );
				$publisher     = get_post_meta( $post_id, '_story_publisher', true );
				$custom_desc   = get_post_meta( $post_id, '_story_description', true );
				$read_time     = CPT_Stories::get_reading_time( $post_id );
				$thumb_url     = get_the_post_thumbnail_url( $post_id, 'thumbnail' );

				$story_cats  = get_the_terms( $post_id, 'story_category' );
				$primary_cat = ( ! empty( $story_cats ) && ! is_wp_error( $story_cats ) ) ? $story_cats[0]->name : '';

				$excerpt = ! empty( $custom_desc ) ? wp_strip_all_tags( $custom_desc ) : wp_strip_all_tags( get_the_excerpt() );

				$results[] = array(
					'id'           => $post_id,
					'title'        => get_the_title(),
					'permalink'    => get_permalink(),
					'thumb_url'    => $thumb_url ? $thumb_url : '',
					'kicker'       => $kicker ? $kicker : '',
					'publisher'    => $publisher ? $publisher : '',
					'category'     => $primary_cat,
					'reading_time' => $read_time,
					'date'         => get_the_date( 'M j, Y' ),
					'excerpt'      => wp_trim_words( $excerpt, 12 ),
				);
			}
			wp_reset_postdata();
		}

		wp_send_json_success(
			array(
				'results'   => $results,
				'total'     => (int) $search_query->found_posts,
				'search_url' => esc_url( add_query_arg( array( 's' => $search_term, 'post_type' => 'story' ), home_url( '/' ) ) ),
			)
		);
	}
}
