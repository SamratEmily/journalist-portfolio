<?php
namespace JournalistPortfolio;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles Custom Post Type 'jp_multimedia' and custom meta fields.
 */
class CPT_Multimedia {

	/**
	 * Instance.
	 *
	 * @var CPT_Multimedia|null
	 */
	private static ?CPT_Multimedia $instance = null;

	/**
	 * Get Instance.
	 *
	 * @return CPT_Multimedia
	 */
	public static function get_instance(): CPT_Multimedia {
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
		add_action( 'add_meta_boxes', array( $this, 'add_multimedia_meta_box' ) );
		add_action( 'save_post_jp_multimedia', array( $this, 'save_multimedia_meta' ), 10, 2 );
	}

	/**
	 * Register Custom Post Type 'jp_multimedia'.
	 */
	public static function register_post_type(): void {
		$labels = array(
			'name'               => _x( 'Videos', 'post type general name', 'journalist-portfolio-hub' ),
			'singular_name'      => _x( 'Video Item', 'post type singular name', 'journalist-portfolio-hub' ),
			'menu_name'          => __( 'Videos', 'journalist-portfolio-hub' ),
			'add_new'            => __( 'Add New Video', 'journalist-portfolio-hub' ),
			'add_new_item'       => __( 'Add New Video Item', 'journalist-portfolio-hub' ),
			'edit_item'          => __( 'Edit Video Item', 'journalist-portfolio-hub' ),
			'new_item'           => __( 'New Video Item', 'journalist-portfolio-hub' ),
			'all_items'          => __( 'All Videos', 'journalist-portfolio-hub' ),
			'view_item'          => __( 'View Video Item', 'journalist-portfolio-hub' ),
			'search_items'       => __( 'Search Videos', 'journalist-portfolio-hub' ),
			'not_found'          => __( 'No video items found', 'journalist-portfolio-hub' ),
			'not_found_in_trash' => __( 'No video items found in Trash', 'journalist-portfolio-hub' ),
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

		register_post_type( 'jp_multimedia', $args );
	}

	/**
	 * Add Meta Box for Multimedia Details.
	 */
	public function add_multimedia_meta_box(): void {
		add_meta_box(
			'jp_multimedia_details',
			__( 'Multimedia Details & Links', 'journalist-portfolio-hub' ),
			array( $this, 'render_multimedia_meta_box' ),
			'jp_multimedia',
			'normal',
			'high'
		);
	}

	/**
	 * Render Meta Box HTML.
	 *
	 * @param \WP_Post $post Current post object.
	 */
	public function render_multimedia_meta_box( $post ): void {
		wp_enqueue_media();
		wp_nonce_field( 'jp_save_multimedia_meta', 'jp_multimedia_meta_nonce' );

		$type        = get_post_meta( $post->ID, '_media_type', true );
		$description = get_post_meta( $post->ID, '_media_description', true );
		$tags        = get_post_meta( $post->ID, '_media_tags', true );
		$youtube_url = get_post_meta( $post->ID, '_media_youtube_url', true );
		$thumbnail   = get_post_meta( $post->ID, '_media_thumbnail', true );

		if ( empty( $type ) ) {
			$type = 'Video';
		}
		?>
		<div class="jp-meta-box-wrapper" style="font-family: inherit; margin-top: 10px;">
			<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 16px;">
				<p>
					<label for="jp_media_type"><strong><?php esc_html_e( 'Media Type:', 'journalist-portfolio-hub' ); ?></strong></label><br>
					<select id="jp_media_type" name="media_type" class="widefat">
						<option value="Video" <?php selected( $type, 'Video' ); ?>><?php esc_html_e( 'Video', 'journalist-portfolio-hub' ); ?></option>
						<option value="Podcast" <?php selected( $type, 'Podcast' ); ?>><?php esc_html_e( 'Podcast', 'journalist-portfolio-hub' ); ?></option>
						<option value="Photo Essay" <?php selected( $type, 'Photo Essay' ); ?>><?php esc_html_e( 'Photo Essay', 'journalist-portfolio-hub' ); ?></option>
						<option value="Data Visualization" <?php selected( $type, 'Data Visualization' ); ?>><?php esc_html_e( 'Data Visualization', 'journalist-portfolio-hub' ); ?></option>
					</select>
				</p>

				<p>
					<label for="jp_media_tags"><strong><?php esc_html_e( 'Tags (comma-separated):', 'journalist-portfolio-hub' ); ?></strong></label><br>
					<input type="text" id="jp_media_tags" name="media_tags" value="<?php echo esc_attr( $tags ); ?>" class="widefat" placeholder="e.g., Climate, River Erosion, Investigation">
				</p>
			</div>

			<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 16px;">
				<p>
					<label for="jp_media_youtube_url"><strong><?php esc_html_e( 'YouTube / Facebook / Video URL:', 'journalist-portfolio-hub' ); ?> <span style="color: #e11d48;">*</span></strong></label><br>
					<input type="url" id="jp_media_youtube_url" name="media_youtube_url" required value="<?php echo esc_attr( $youtube_url ); ?>" class="widefat" placeholder="e.g., https://www.youtube.com/watch?v=... or https://www.facebook.com/watch/?v=...">
				</p>

				<p>
					<label for="jp_media_thumbnail"><strong><?php esc_html_e( 'Custom Thumbnail Image URL:', 'journalist-portfolio-hub' ); ?></strong></label><br>
					<div style="display: flex; gap: 8px; margin-top: 4px;">
						<input type="text" id="jp_media_thumbnail" name="media_thumbnail" value="<?php echo esc_attr( $thumbnail ); ?>" class="widefat jp-media-url" placeholder="https://...">
						<button type="button" class="button jp-upload-btn"><?php esc_html_e( 'Upload', 'journalist-portfolio-hub' ); ?></button>
					</div>
					<div class="jp-thumb-preview-box" style="margin-top: 8px;">
						<img class="jp-thumb-preview" src="<?php echo esc_url( $thumbnail ); ?>" style="<?php echo empty( $thumbnail ) ? 'display:none;' : ''; ?> max-width: 150px; max-height: 90px; border-radius: 4px; border: 1px solid #e2e8f0; object-fit: cover;" alt="Preview">
					</div>
				</p>
			</div>

			<p>
				<label for="jp_media_description"><strong><?php esc_html_e( 'Media Description:', 'journalist-portfolio-hub' ); ?></strong></label><br>
				<textarea id="jp_media_description" name="media_description" rows="4" class="widefat" placeholder="<?php esc_attr_e( 'Brief description or summary of this multimedia production...', 'journalist-portfolio-hub' ); ?>"><?php echo esc_textarea( $description ); ?></textarea>
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
	public function save_multimedia_meta( int $post_id, $post ): void {
		if ( ! isset( $_POST['jp_multimedia_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['jp_multimedia_meta_nonce'] ) ), 'jp_save_multimedia_meta' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( isset( $_POST['media_type'] ) ) {
			update_post_meta( $post_id, '_media_type', sanitize_text_field( wp_unslash( $_POST['media_type'] ) ) );
		}

		if ( isset( $_POST['media_tags'] ) ) {
			update_post_meta( $post_id, '_media_tags', sanitize_text_field( wp_unslash( $_POST['media_tags'] ) ) );
		}

		if ( isset( $_POST['media_youtube_url'] ) ) {
			update_post_meta( $post_id, '_media_youtube_url', esc_url_raw( wp_unslash( $_POST['media_youtube_url'] ) ) );
		}

		if ( isset( $_POST['media_thumbnail'] ) ) {
			update_post_meta( $post_id, '_media_thumbnail', esc_url_raw( wp_unslash( $_POST['media_thumbnail'] ) ) );
		}

		if ( isset( $_POST['media_description'] ) ) {
			update_post_meta( $post_id, '_media_description', sanitize_textarea_field( wp_unslash( $_POST['media_description'] ) ) );
		}
	}
}
