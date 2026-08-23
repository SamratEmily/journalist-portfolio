<?php
namespace JournalistPortfolio;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles Custom Post Type 'jp_photo' and custom meta fields.
 */
class CPT_Photos {

	/**
	 * Instance.
	 *
	 * @var CPT_Photos|null
	 */
	private static ?CPT_Photos $instance = null;

	/**
	 * Get Instance.
	 *
	 * @return CPT_Photos
	 */
	public static function get_instance(): CPT_Photos {
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
		add_action( 'add_meta_boxes', array( $this, 'add_photo_meta_box' ) );
		add_action( 'save_post_jp_photo', array( $this, 'save_photo_meta' ), 10, 2 );
	}

	/**
	 * Register Custom Post Type 'jp_photo'.
	 */
	public static function register_post_type(): void {
		$labels = array(
			'name'               => _x( 'Photos', 'post type general name', 'journalist-portfolio-hub' ),
			'singular_name'      => _x( 'Photo Item', 'post type singular name', 'journalist-portfolio-hub' ),
			'menu_name'          => __( 'Photos', 'journalist-portfolio-hub' ),
			'add_new'            => __( 'Add New Photo', 'journalist-portfolio-hub' ),
			'add_new_item'       => __( 'Add New Photo Item', 'journalist-portfolio-hub' ),
			'edit_item'          => __( 'Edit Photo Item', 'journalist-portfolio-hub' ),
			'new_item'           => __( 'New Photo Item', 'journalist-portfolio-hub' ),
			'all_items'          => __( 'All Photos', 'journalist-portfolio-hub' ),
			'view_item'          => __( 'View Photo', 'journalist-portfolio-hub' ),
			'search_items'       => __( 'Search Photos', 'journalist-portfolio-hub' ),
			'not_found'          => __( 'No photos found', 'journalist-portfolio-hub' ),
			'not_found_in_trash' => __( 'No photos found in Trash', 'journalist-portfolio-hub' ),
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
			'supports'           => array( 'title', 'thumbnail' ),
		);

		register_post_type( 'jp_photo', $args );
	}

	/**
	 * Add Meta Box for Photo Details.
	 */
	public function add_photo_meta_box(): void {
		add_meta_box(
			'jp_photo_details',
			__( 'Photo Details & Image', 'journalist-portfolio-hub' ),
			array( $this, 'render_photo_meta_box' ),
			'jp_photo',
			'normal',
			'high'
		);
	}

	/**
	 * Render Meta Box HTML.
	 *
	 * @param \WP_Post $post Current post object.
	 */
	public function render_photo_meta_box( $post ): void {
		wp_enqueue_media();
		wp_nonce_field( 'jp_save_photo_meta', 'jp_photo_meta_nonce' );

		$description   = get_post_meta( $post->ID, '_photo_description', true );
		$tags          = get_post_meta( $post->ID, '_photo_tags', true );
		$photo_url     = get_post_meta( $post->ID, '_photo_image', true );
		$published_url = get_post_meta( $post->ID, '_photo_published_url', true );
		?>
		<div class="jp-meta-box-wrapper" style="font-family: inherit; margin-top: 10px;">
			<div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 16px;">
				<p>
					<label for="jp_photo_tags"><strong><?php esc_html_e( 'Tags (comma-separated):', 'journalist-portfolio-hub' ); ?></strong></label><br>
					<input type="text" id="jp_photo_tags" name="photo_tags" value="<?php echo esc_attr( $tags ); ?>" class="widefat" placeholder="e.g., Landscape, Climate, Wildlife">
				</p>

				<p>
					<label for="jp_photo_published_url"><strong><?php esc_html_e( 'Published Link URL:', 'journalist-portfolio-hub' ); ?></strong></label><br>
					<input type="url" id="jp_photo_published_url" name="photo_published_url" value="<?php echo esc_attr( $published_url ); ?>" class="widefat" placeholder="e.g., https://example.com/story-or-publication">
				</p>

				<p>
					<label for="jp_photo_image"><strong><?php esc_html_e( 'Photo Image URL / Upload:', 'journalist-portfolio-hub' ); ?> <span style="color: #e11d48;">*</span></strong></label><br>
					<div style="display: flex; gap: 8px; margin-top: 4px;">
						<input type="text" id="jp_photo_image" name="photo_image" value="<?php echo esc_attr( $photo_url ); ?>" class="widefat jp-media-url" placeholder="https://...">
						<button type="button" class="button jp-upload-btn"><?php esc_html_e( 'Upload Photo', 'journalist-portfolio-hub' ); ?></button>
					</div>
					<div class="jp-thumb-preview-box" style="margin-top: 8px;">
						<img class="jp-thumb-preview" src="<?php echo esc_url( $photo_url ); ?>" style="<?php echo empty( $photo_url ) ? 'display:none;' : ''; ?> max-width: 200px; max-height: 120px; border-radius: 4px; border: 1px solid #e2e8f0; object-fit: cover;" alt="Preview">
					</div>
				</p>
			</div>

			<p>
				<label for="jp_photo_description"><strong><?php esc_html_e( 'Simple Description:', 'journalist-portfolio-hub' ); ?></strong></label><br>
				<textarea id="jp_photo_description" name="photo_description" rows="4" class="widefat" placeholder="<?php esc_attr_e( 'Simple description of this photo or photograph shot...', 'journalist-portfolio-hub' ); ?>"><?php echo esc_textarea( $description ); ?></textarea>
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
	public function save_photo_meta( int $post_id, $post ): void {
		if ( ! isset( $_POST['jp_photo_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['jp_photo_meta_nonce'] ) ), 'jp_save_photo_meta' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( isset( $_POST['photo_tags'] ) ) {
			update_post_meta( $post_id, '_photo_tags', sanitize_text_field( wp_unslash( $_POST['photo_tags'] ) ) );
		}

		if ( isset( $_POST['photo_published_url'] ) ) {
			update_post_meta( $post_id, '_photo_published_url', esc_url_raw( wp_unslash( $_POST['photo_published_url'] ) ) );
		}

		if ( isset( $_POST['photo_image'] ) ) {
			update_post_meta( $post_id, '_photo_image', esc_url_raw( wp_unslash( $_POST['photo_image'] ) ) );
		}

		if ( isset( $_POST['photo_description'] ) ) {
			update_post_meta( $post_id, '_photo_description', sanitize_textarea_field( wp_unslash( $_POST['photo_description'] ) ) );
		}
	}
}
