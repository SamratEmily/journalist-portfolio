<?php
namespace JournalistPortfolio;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles Custom Post Type 'story', taxonomy 'story_category', and custom Meta Box.
 */
class CPT_Stories {

	/**
	 * Class Instance.
	 *
	 * @var CPT_Stories|null
	 */
	private static $instance = null;

	/**
	 * Get Class Instance.
	 *
	 * @return CPT_Stories
	 */
	public static function get_instance(): CPT_Stories {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		add_action( 'init', array( __CLASS__, 'register_post_type_and_taxonomy' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_story_meta_box' ) );
		add_action( 'save_post_story', array( $this, 'save_story_meta' ), 10, 2 );
	}

	/**
	 * Static method to register Post Type and Taxonomy (used also during activation).
	 */
	public static function register_post_type_and_taxonomy(): void {
		// Register Taxonomy 'story_category'.
		$tax_labels = array(
			'name'              => _x( 'Story Categories', 'taxonomy general name', 'journalist-portfolio-hub' ),
			'singular_name'     => _x( 'Story Category', 'taxonomy singular name', 'journalist-portfolio-hub' ),
			'search_items'      => __( 'Search Story Categories', 'journalist-portfolio-hub' ),
			'all_items'         => __( 'All Story Categories', 'journalist-portfolio-hub' ),
			'parent_item'       => __( 'Parent Category', 'journalist-portfolio-hub' ),
			'parent_item_colon' => __( 'Parent Category:', 'journalist-portfolio-hub' ),
			'edit_item'         => __( 'Edit Category', 'journalist-portfolio-hub' ),
			'update_item'       => __( 'Update Category', 'journalist-portfolio-hub' ),
			'add_new_item'      => __( 'Add New Category', 'journalist-portfolio-hub' ),
			'new_item_name'     => __( 'New Category Name', 'journalist-portfolio-hub' ),
			'menu_name'         => __( 'Story Categories', 'journalist-portfolio-hub' ),
		);

		$tax_args = array(
			'hierarchical'      => true,
			'labels'            => $tax_labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'story-category' ),
		);

		register_taxonomy( 'story_category', array( 'story' ), $tax_args );

		// Register Post Type 'story'.
		$cpt_labels = array(
			'name'                  => _x( 'Stories', 'Post type general name', 'journalist-portfolio-hub' ),
			'singular_name'         => _x( 'Story', 'Post type singular name', 'journalist-portfolio-hub' ),
			'menu_name'             => _x( 'Stories', 'Admin Menu text', 'journalist-portfolio-hub' ),
			'name_admin_bar'        => _x( 'Story', 'Add New on Toolbar', 'journalist-portfolio-hub' ),
			'add_new'               => __( 'Add New Story', 'journalist-portfolio-hub' ),
			'add_new_item'          => __( 'Add New Story', 'journalist-portfolio-hub' ),
			'new_item'              => __( 'New Story', 'journalist-portfolio-hub' ),
			'edit_item'             => __( 'Edit Story', 'journalist-portfolio-hub' ),
			'view_item'             => __( 'View Story', 'journalist-portfolio-hub' ),
			'all_items'             => __( 'All Stories', 'journalist-portfolio-hub' ),
			'search_items'          => __( 'Search Stories', 'journalist-portfolio-hub' ),
			'parent_item_colon'     => __( 'Parent Stories:', 'journalist-portfolio-hub' ),
			'not_found'             => __( 'No stories found.', 'journalist-portfolio-hub' ),
			'not_found_in_trash'    => __( 'No stories found in Trash.', 'journalist-portfolio-hub' ),
		);

		$cpt_args = array(
			'labels'             => $cpt_labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'stories-item' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => 5,
			'menu_icon'          => 'dashicons-book-alt',
			'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
			'show_in_rest'       => true,
		);

		register_post_type( 'story', $cpt_args );
	}

	/**
	 * Register Custom Meta Box for Stories.
	 */
	public function add_story_meta_box(): void {
		add_meta_box(
			'jp_story_portfolio_details',
			__( 'Story Portfolio Details', 'journalist-portfolio-hub' ),
			array( $this, 'render_story_meta_box' ),
			'story',
			'normal',
			'high'
		);
	}

	/**
	 * Render Custom Meta Box HTML.
	 *
	 * @param \WP_Post $post Current post object.
	 */
	public function render_story_meta_box( $post ): void {
		wp_nonce_field( 'jp_save_story_meta', 'jp_story_meta_nonce' );

		$heading          = get_post_meta( $post->ID, '_story_heading', true );
		$description      = get_post_meta( $post->ID, '_story_description', true );
		$is_featured      = get_post_meta( $post->ID, '_story_is_featured', true );
		$feature_priority = get_post_meta( $post->ID, '_story_feature_priority', true );

		if ( '' === $feature_priority || false === $feature_priority ) {
			$feature_priority = 0;
		}
		?>
		<div class="jp-meta-box-wrapper" style="font-family: inherit; margin-top: 10px;">
			<p>
				<label for="jp_story_heading"><strong><?php esc_html_e( 'Story Kicker / Custom Sub-Heading:', 'journalist-portfolio-hub' ); ?></strong></label><br>
				<input type="text" id="jp_story_heading" name="story_heading" value="<?php echo esc_attr( $heading ); ?>" class="widefat" placeholder="<?php esc_attr_e( 'e.g., Exclusive Investigation | Environment & Climate', 'journalist-portfolio-hub' ); ?>">
			</p>

			<p>
				<label for="jp_story_description"><strong><?php esc_html_e( 'Custom Story Summary / Description:', 'journalist-portfolio-hub' ); ?></strong></label><br>
				<?php
				$editor_settings = array(
					'textarea_name' => 'story_description',
					'textarea_rows' => 5,
					'media_buttons' => false,
					'teeny'         => true,
				);
				wp_editor( $description, 'jp_story_description_editor', $editor_settings );
				?>
			</p>

			<div style="display: flex; gap: 30px; align-items: center; background: #f8fafc; padding: 12px 16px; border-radius: 6px; border: 1px solid #e2e8f0; margin-top: 15px;">
				<div>
					<label for="jp_story_is_featured" style="font-weight: 600; cursor: pointer;">
						<input type="checkbox" id="jp_story_is_featured" name="story_is_featured" value="1" <?php checked( $is_featured, '1' ); ?>>
						<?php esc_html_e( 'Feature this Story on Home Page?', 'journalist-portfolio-hub' ); ?>
					</label>
				</div>
				<div>
					<label for="jp_story_feature_priority"><strong><?php esc_html_e( 'Feature Priority Ranking:', 'journalist-portfolio-hub' ); ?></strong></label>
					<input type="number" id="jp_story_feature_priority" name="story_feature_priority" value="<?php echo esc_attr( $feature_priority ); ?>" min="0" style="width: 80px; margin-left: 8px;">
					<span class="description"><?php esc_html_e( '(Higher numbers appear first)', 'journalist-portfolio-hub' ); ?></span>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Save Meta Box Data.
	 *
	 * @param int      $post_id Post ID.
	 * @param \WP_Post $post    Post Object.
	 */
	public function save_story_meta( int $post_id, $post ): void {
		// Nonce Check.
		if ( ! isset( $_POST['jp_story_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['jp_story_meta_nonce'] ) ), 'jp_save_story_meta' ) ) {
			return;
		}

		// Auto save check.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Capability check.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Save Heading.
		if ( isset( $_POST['story_heading'] ) ) {
			update_post_meta( $post_id, '_story_heading', sanitize_text_field( wp_unslash( $_POST['story_heading'] ) ) );
		}

		// Save Description.
		if ( isset( $_POST['story_description'] ) ) {
			update_post_meta( $post_id, '_story_description', wp_kses_post( wp_unslash( $_POST['story_description'] ) ) );
		}

		// Save Featured Checkbox.
		$is_featured = isset( $_POST['story_is_featured'] ) ? '1' : '0';
		update_post_meta( $post_id, '_story_is_featured', $is_featured );

		// Save Priority.
		if ( isset( $_POST['story_feature_priority'] ) ) {
			update_post_meta( $post_id, '_story_feature_priority', absint( $_POST['story_feature_priority'] ) );
		}
	}
}
