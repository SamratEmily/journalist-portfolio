<?php
namespace JournalistPortfolio;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles Custom Post Type 'jp_award' and custom meta fields.
 */
class CPT_Awards {

	/**
	 * Instance.
	 *
	 * @var CPT_Awards|null
	 */
	private static $instance = null;

	/**
	 * Get Instance.
	 *
	 * @return CPT_Awards
	 */
	public static function get_instance(): CPT_Awards {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		add_action( 'init', array( __CLASS__, 'register_post_type' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_award_meta_box' ) );
		add_action( 'save_post_jp_award', array( $this, 'save_award_meta' ), 10, 2 );
	}

	/**
	 * Register Custom Post Type 'jp_award'.
	 */
	public static function register_post_type(): void {
		$labels = array(
			'name'               => _x( 'Awards & Fellowships', 'post type general name', 'journalist-portfolio-hub' ),
			'singular_name'      => _x( 'Award / Fellowship', 'post type singular name', 'journalist-portfolio-hub' ),
			'menu_name'          => __( 'Awards & Fellowships', 'journalist-portfolio-hub' ),
			'add_new'            => __( 'Add New Award', 'journalist-portfolio-hub' ),
			'add_new_item'       => __( 'Add New Award / Fellowship', 'journalist-portfolio-hub' ),
			'edit_item'          => __( 'Edit Award', 'journalist-portfolio-hub' ),
			'new_item'           => __( 'New Award', 'journalist-portfolio-hub' ),
			'all_items'          => __( 'All Awards', 'journalist-portfolio-hub' ),
			'view_item'          => __( 'View Award', 'journalist-portfolio-hub' ),
			'search_items'       => __( 'Search Awards', 'journalist-portfolio-hub' ),
			'not_found'          => __( 'No awards found', 'journalist-portfolio-hub' ),
			'not_found_in_trash' => __( 'No awards found in Trash', 'journalist-portfolio-hub' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => false, // Managed via custom Portfolio Settings submenu
			'query_var'          => false,
			'rewrite'            => false,
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'supports'           => array( 'title' ),
		);

		register_post_type( 'jp_award', $args );
	}

	/**
	 * Add Meta Box for Award Details.
	 */
	public function add_award_meta_box(): void {
		add_meta_box(
			'jp_award_details',
			__( 'Award & Fellowship Details', 'journalist-portfolio-hub' ),
			array( $this, 'render_award_meta_box' ),
			'jp_award',
			'normal',
			'high'
		);
	}

	/**
	 * Render Meta Box HTML.
	 *
	 * @param \WP_Post $post Current post object.
	 */
	public function render_award_meta_box( $post ): void {
		wp_nonce_field( 'jp_save_award_meta', 'jp_award_meta_nonce' );

		$award_for   = get_post_meta( $post->ID, '_award_for', true );
		$year        = get_post_meta( $post->ID, '_award_year', true );
		$location    = get_post_meta( $post->ID, '_award_location', true );
		$icon        = get_post_meta( $post->ID, '_award_icon', true );
		$description = get_post_meta( $post->ID, '_award_description', true );

		if ( empty( $icon ) ) {
			$icon = 'dashicons-awards';
		}
		?>
		<div class="jp-meta-box-wrapper" style="font-family: inherit; margin-top: 10px;">
			<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 16px;">
				<p>
					<label for="jp_award_for"><strong><?php esc_html_e( 'Awarded For / Organization Name:', 'journalist-portfolio-hub' ); ?></strong></label><br>
					<input type="text" id="jp_award_for" name="award_for" value="<?php echo esc_attr( $award_for ); ?>" class="widefat" placeholder="e.g., Climate Storytelling / Global Investigative Journalism Network">
				</p>

				<p>
					<label for="jp_award_year"><strong><?php esc_html_e( 'Award Year:', 'journalist-portfolio-hub' ); ?></strong></label><br>
					<input type="text" id="jp_award_year" name="award_year" value="<?php echo esc_attr( $year ); ?>" class="widefat" placeholder="e.g., 2025">
				</p>
			</div>

			<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 16px;">
				<p>
					<label for="jp_award_location"><strong><?php esc_html_e( 'Location / Region:', 'journalist-portfolio-hub' ); ?></strong></label><br>
					<input type="text" id="jp_award_location" name="award_location" value="<?php echo esc_attr( $location ); ?>" class="widefat" placeholder="e.g., International, Asia, Geneva">
				</p>

				<p>
					<label for="jp_award_icon"><strong><?php esc_html_e( 'Icon (Dashicon class or Image URL):', 'journalist-portfolio-hub' ); ?></strong></label><br>
					<input type="text" id="jp_award_icon" name="award_icon" value="<?php echo esc_attr( $icon ); ?>" class="widefat" placeholder="e.g., dashicons-awards, dashicons-star-filled, or https://...">
					<span class="description" style="font-size: 0.85em; color: #64748b;"><?php esc_html_e( 'Default: dashicons-awards. Can use dashicons-welcome-learn-more, dashicons-star-filled, etc.', 'journalist-portfolio-hub' ); ?></span>
				</p>
			</div>

			<p>
				<label for="jp_award_description"><strong><?php esc_html_e( 'Award Description / Impact Summary:', 'journalist-portfolio-hub' ); ?></strong></label><br>
				<textarea id="jp_award_description" name="award_description" rows="4" class="widefat" placeholder="<?php esc_attr_e( 'Brief description of the recognition, panel evaluation, or story impact...', 'journalist-portfolio-hub' ); ?>"><?php echo esc_textarea( $description ); ?></textarea>
			</p>
		</div>
		<?php
	}

	/**
	 * Save Meta Box Data.
	 *
	 * @param int      $post_id Post ID.
	 * @param \WP_Post $post    Post Object.
	 */
	public function save_award_meta( int $post_id, $post ): void {
		if ( ! isset( $_POST['jp_award_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['jp_award_meta_nonce'] ) ), 'jp_save_award_meta' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( isset( $_POST['award_for'] ) ) {
			update_post_meta( $post_id, '_award_for', sanitize_text_field( wp_unslash( $_POST['award_for'] ) ) );
		}

		if ( isset( $_POST['award_year'] ) ) {
			update_post_meta( $post_id, '_award_year', sanitize_text_field( wp_unslash( $_POST['award_year'] ) ) );
		}

		if ( isset( $_POST['award_location'] ) ) {
			update_post_meta( $post_id, '_award_location', sanitize_text_field( wp_unslash( $_POST['award_location'] ) ) );
		}

		if ( isset( $_POST['award_icon'] ) ) {
			update_post_meta( $post_id, '_award_icon', sanitize_text_field( wp_unslash( $_POST['award_icon'] ) ) );
		}

		if ( isset( $_POST['award_description'] ) ) {
			update_post_meta( $post_id, '_award_description', sanitize_textarea_field( wp_unslash( $_POST['award_description'] ) ) );
		}
	}
}
