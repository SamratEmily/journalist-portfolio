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
		add_action( 'admin_menu', array( $this, 'register_publications_submenu' ) );
		add_action( 'admin_init', array( $this, 'maybe_seed_demo_data' ) );
	}

	/**
	 * Automatically seed demo stories if no story posts exist.
	 */
	public function maybe_seed_demo_data(): void {
		if ( ! get_option( 'jp_demo_stories_seeded' ) ) {
			Demo_Seeder::seed();
			update_option( 'jp_demo_stories_seeded', '1' );
		}
	}

	/**
	 * Static method to register Post Type and Taxonomy (used also during activation).
	 */
	public static function register_post_type_and_taxonomy(): void {
		// Register 400x300 image size for featured stories.
		add_image_size( 'jp_featured_400_300', 400, 300, true );

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
	/**
	 * Register Publications Submenu under Stories.
	 */
	public function register_publications_submenu(): void {
		add_submenu_page(
			'edit.php?post_type=story',
			__( 'Publications', 'journalist-portfolio-hub' ),
			__( 'Publications', 'journalist-portfolio-hub' ),
			'manage_options',
			'jp-publications',
			array( $this, 'render_publications_page' )
		);

		add_submenu_page(
			'edit.php?post_type=story',
			__( 'Re-seed Demo Data', 'journalist-portfolio-hub' ),
			__( 'Re-seed Demo Data', 'journalist-portfolio-hub' ),
			'manage_options',
			'jp-reseed-demo',
			array( $this, 'render_reseed_demo_page' )
		);
	}

	/**
	 * Render Publications Submenu Page.
	 */
	public function render_publications_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Handle Form Submissions (Add / Delete).
		if ( isset( $_POST['jp_pub_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['jp_pub_nonce'] ) ), 'jp_save_publications' ) ) {
			$pubs_json = get_option( 'jp_publications_list', '[]' );
			$pubs_list = json_decode( $pubs_json, true );
			if ( ! is_array( $pubs_list ) ) {
				$pubs_list = array();
			}

			// Delete Action
			if ( isset( $_POST['action'] ) && 'delete' === $_POST['action'] && isset( $_POST['pub_index'] ) ) {
				$idx = absint( $_POST['pub_index'] );
				if ( isset( $pubs_list[ $idx ] ) ) {
					array_splice( $pubs_list, $idx, 1 );
					update_option( 'jp_publications_list', wp_json_encode( $pubs_list ) );
					echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Publication deleted successfully.', 'journalist-portfolio-hub' ) . '</p></div>';
				}
			}
			// Add Action
			elseif ( isset( $_POST['pub_name'] ) && ! empty( trim( $_POST['pub_name'] ) ) ) {
				$new_name = sanitize_text_field( wp_unslash( $_POST['pub_name'] ) );
				$new_url  = esc_url_raw( wp_unslash( $_POST['pub_url'] ?? '' ) );
				
				$pubs_list[] = array(
					'name' => $new_name,
					'url'  => $new_url,
				);
				update_option( 'jp_publications_list', wp_json_encode( $pubs_list ) );
				echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Publication added successfully.', 'journalist-portfolio-hub' ) . '</p></div>';
			}
		}

		$pubs_json = get_option( 'jp_publications_list', '[]' );
		$pubs_list = json_decode( $pubs_json, true );
		if ( ! is_array( $pubs_list ) ) {
			$pubs_list = array();
		}
		?>
		<div class="wrap jp-admin-wrap">
			<div class="jp-admin-header" style="margin-bottom: 24px;">
				<h1><?php esc_html_e( 'Publications & Media Outlets', 'journalist-portfolio-hub' ); ?></h1>
				<p><?php esc_html_e( 'Add and manage news outlets, publishers, and media platforms where your stories are published.', 'journalist-portfolio-hub' ); ?></p>
			</div>

			<div style="display: grid; grid-template-columns: 360px 1fr; gap: 30px;">
				<!-- Add New Publication Form -->
				<div class="jp-admin-card" style="background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #ccd0d4;">
					<h2><?php esc_html_e( 'Add New Publication', 'journalist-portfolio-hub' ); ?></h2>
					<form method="post">
						<?php wp_nonce_field( 'jp_save_publications', 'jp_pub_nonce' ); ?>
						<p>
							<label for="pub_name"><strong><?php esc_html_e( 'Publication Name:', 'journalist-portfolio-hub' ); ?></strong></label><br>
							<input type="text" id="pub_name" name="pub_name" required class="widefat" placeholder="e.g., Kaler Kantho, Reuters, The Daily Star">
						</p>
						<p>
							<label for="pub_url"><strong><?php esc_html_e( 'Publication Website Link (URL):', 'journalist-portfolio-hub' ); ?></strong></label><br>
							<input type="url" id="pub_url" name="pub_url" class="widefat" placeholder="https://www.kalerkantho.com">
						</p>
						<p style="margin-top: 20px;">
							<button type="submit" class="button button-primary" style="background: #059669; border-color: #059669; color: #fff;"><?php esc_html_e( 'Add Publication', 'journalist-portfolio-hub' ); ?></button>
						</p>
					</form>
				</div>

				<!-- Registered Publications List -->
				<div class="jp-admin-card" style="background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #ccd0d4;">
					<h2><?php esc_html_e( 'Registered Publications', 'journalist-portfolio-hub' ); ?></h2>
					<?php if ( ! empty( $pubs_list ) ) : ?>
						<table class="widefat striped">
							<thead>
								<tr>
									<th>#</th>
									<th><?php esc_html_e( 'Publication Name', 'journalist-portfolio-hub' ); ?></th>
									<th><?php esc_html_e( 'Website Link', 'journalist-portfolio-hub' ); ?></th>
									<th style="width: 80px; text-align: center;"><?php esc_html_e( 'Action', 'journalist-portfolio-hub' ); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $pubs_list as $index => $item ) : ?>
									<tr>
										<td><?php echo esc_html( $index + 1 ); ?></td>
										<td><strong><?php echo esc_html( $item['name'] ); ?></strong></td>
										<td>
											<?php if ( ! empty( $item['url'] ) ) : ?>
												<a href="<?php echo esc_url( $item['url'] ); ?>" target="_blank"><?php echo esc_html( $item['url'] ); ?></a>
											<?php else : ?>
												<span style="color: #94a3b8;"><?php esc_html_e( 'No Link', 'journalist-portfolio-hub' ); ?></span>
											<?php endif; ?>
										</td>
										<td style="text-align: center;">
											<form method="post" style="display: inline-block;" onsubmit="return confirm('Delete this publication?');">
												<?php wp_nonce_field( 'jp_save_publications', 'jp_pub_nonce' ); ?>
												<input type="hidden" name="action" value="delete">
												<input type="hidden" name="pub_index" value="<?php echo esc_attr( $index ); ?>">
												<button type="submit" class="button button-small button-link-delete"><?php esc_html_e( 'Delete', 'journalist-portfolio-hub' ); ?></button>
											</form>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					<?php else : ?>
						<p style="color: #64748b;"><?php esc_html_e( 'No publications added yet. Add one using the form on the left.', 'journalist-portfolio-hub' ); ?></p>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render Custom Meta Box HTML.
	 *
	 * @param \WP_Post $post Current post object.
	 */
	public function render_story_meta_box( $post ): void {
		wp_nonce_field( 'jp_save_story_meta', 'jp_story_meta_nonce' );

		$heading          = get_post_meta( $post->ID, '_story_heading', true );
		$publisher        = get_post_meta( $post->ID, '_story_publisher', true );
		$publisher_url    = get_post_meta( $post->ID, '_story_publisher_url', true );
		$description      = get_post_meta( $post->ID, '_story_description', true );
		$is_featured      = get_post_meta( $post->ID, '_story_is_featured', true );
		$feature_priority = get_post_meta( $post->ID, '_story_feature_priority', true );

		if ( '' === $feature_priority || false === $feature_priority ) {
			$feature_priority = 0;
		}

		$pubs_json = get_option( 'jp_publications_list', '[]' );
		$pubs_list = json_decode( $pubs_json, true );
		if ( ! is_array( $pubs_list ) ) {
			$pubs_list = array();
		}
		?>
		<div class="jp-meta-box-wrapper" style="font-family: inherit; margin-top: 10px;">
			<p>
				<label for="jp_story_heading"><strong><?php esc_html_e( 'Story Kicker / Custom Sub-Heading:', 'journalist-portfolio-hub' ); ?></strong></label><br>
				<input type="text" id="jp_story_heading" name="story_heading" value="<?php echo esc_attr( $heading ); ?>" class="widefat" placeholder="<?php esc_attr_e( 'e.g., Exclusive Investigation | Environment & Climate', 'journalist-portfolio-hub' ); ?>">
			</p>

			<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; background: #f8fafc; padding: 14px; border-radius: 6px; border: 1px solid #e2e8f0; margin-bottom: 16px;">
				<div>
					<label for="jp_story_publisher"><strong><?php esc_html_e( 'Publisher / Media Outlet Name:', 'journalist-portfolio-hub' ); ?></strong></label><br>
					<input type="text" id="jp_story_publisher" name="story_publisher" value="<?php echo esc_attr( $publisher ); ?>" class="widefat" placeholder="<?php esc_attr_e( 'e.g., Kaler Kantho, The Daily Star, Reuters', 'journalist-portfolio-hub' ); ?>">
					
					<?php if ( ! empty( $pubs_list ) ) : ?>
						<p style="margin: 6px 0 0 0; font-size: 0.85em; color: #64748b;">
							<?php esc_html_e( 'Select from Publications List:', 'journalist-portfolio-hub' ); ?>
							<select onchange="if(this.value){ document.getElementById('jp_story_publisher').value = this.options[this.selectedIndex].text; document.getElementById('jp_story_publisher_url').value = this.value; }">
								<option value=""><?php esc_html_e( '-- Quick Select --', 'journalist-portfolio-hub' ); ?></option>
								<?php foreach ( $pubs_list as $pub ) : ?>
									<option value="<?php echo esc_attr( $pub['url'] ); ?>"><?php echo esc_html( $pub['name'] ); ?></option>
								<?php endforeach; ?>
							</select>
						</p>
					<?php endif; ?>
				</div>

				<div>
					<label for="jp_story_publisher_url"><strong><?php esc_html_e( 'Publication Website Link (URL):', 'journalist-portfolio-hub' ); ?></strong></label><br>
					<input type="url" id="jp_story_publisher_url" name="story_publisher_url" value="<?php echo esc_url( $publisher_url ); ?>" class="widefat" placeholder="<?php esc_attr_e( 'https://www.kalerkantho.com', 'journalist-portfolio-hub' ); ?>">
				</div>
			</div>

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

		// Save Publisher.
		if ( isset( $_POST['story_publisher'] ) ) {
			update_post_meta( $post_id, '_story_publisher', sanitize_text_field( wp_unslash( $_POST['story_publisher'] ) ) );
		}

		// Save Publisher URL.
		if ( isset( $_POST['story_publisher_url'] ) ) {
			update_post_meta( $post_id, '_story_publisher_url', esc_url_raw( wp_unslash( $_POST['story_publisher_url'] ) ) );
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

	/**
	 * Calculate Estimated Reading Time for a Story.
	 *
	 * @param int $post_id Post ID.
	 * @return string Formatted reading time string (e.g., "8 min read").
	 */
	public static function get_reading_time( int $post_id ): string {
		$post = get_post( $post_id );
		if ( ! $post ) {
			return '1 min read';
		}

		$content     = $post->post_content;
		$custom_desc = get_post_meta( $post_id, '_story_description', true );
		
		$combined_text = wp_strip_all_tags( $content . ' ' . $custom_desc );
		$word_count    = str_word_count( $combined_text );

		// Average reading speed: 200 words per minute.
		$minutes = (int) ceil( $word_count / 200 );
		if ( $minutes < 1 ) {
			$minutes = 1;
		}

		return sprintf( __( '%d min read', 'journalist-portfolio-hub' ), $minutes );
	}

	/**
	 * Render Re-seed Demo Data Submenu Page.
	 */
	public function render_reseed_demo_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$seeded_message = '';
		if ( isset( $_POST['jp_reseed_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['jp_reseed_nonce'] ) ), 'jp_reseed_demo_action' ) ) {
			if ( isset( $_POST['action'] ) && 'seed_demo' === $_POST['action'] ) {
				Demo_Seeder::seed();
				$seeded_message = __( 'Demo stories, publications, categories, awards, and multimedia items have been successfully seeded!', 'journalist-portfolio-hub' );
			}
		}
		?>
		<div class="wrap jp-admin-wrap" style="max-width: 960px; margin-top: 20px;">
			
			<?php if ( ! empty( $seeded_message ) ) : ?>
				<div class="notice notice-success is-dismissible" style="border-left-color: #059669; padding: 12px 16px;">
					<p style="font-size: 1rem; font-weight: 600; color: #065f46; margin: 0;">✨ <?php echo esc_html( $seeded_message ); ?></p>
				</div>
			<?php endif; ?>

			<!-- Header Banner -->
			<div class="jp-admin-header" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #ffffff; padding: 32px; border-radius: 12px; margin-bottom: 28px; box-shadow: 0 4px 20px rgba(15,23,42,0.15);">
				<span style="background: #059669; color: #ffffff; font-size: 0.75rem; font-weight: 800; padding: 4px 12px; border-radius: 20px; text-transform: uppercase; letter-spacing: 0.08em; display: inline-block; margin-bottom: 12px;"><?php esc_html_e( 'Demo Content Generator', 'journalist-portfolio-hub' ); ?></span>
				<h1 style="color: #ffffff; font-size: 2rem; font-weight: 800; margin: 0 0 8px; font-family: var(--jp-font-serif, Georgia, serif);"><?php esc_html_e( 'Re-seed Demo Portfolio Content', 'journalist-portfolio-hub' ); ?></h1>
				<p style="color: #94a3b8; font-size: 1rem; margin: 0; max-width: 650px; line-height: 1.6;"><?php esc_html_e( 'Quickly populate or reset your portfolio with high-quality demo stories, climate/investigative reporting categories, media outlets, awards, and multimedia productions.', 'journalist-portfolio-hub' ); ?></p>
			</div>

			<!-- Action Card -->
			<div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 36px; box-shadow: 0 4px 14px rgba(15,23,42,0.06);">
				<h2 style="font-size: 1.25rem; font-weight: 700; color: #0f172a; margin-top: 0; margin-bottom: 20px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px;"><?php esc_html_e( 'Items Included in Demo Seed:', 'journalist-portfolio-hub' ); ?></h2>
				
				<div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 32px;">
					<div style="background: #f8fafc; padding: 18px; border-radius: 10px; border: 1px solid #e2e8f0; display: flex; gap: 14px; align-items: flex-start;">
						<span class="dashicons dashicons-format-aside" style="font-size: 28px; width: 28px; height: 28px; color: #059669; flex-shrink: 0; margin-top: 2px;"></span>
						<div>
							<strong style="color: #0f172a; font-size: 0.98rem; display: block; margin-bottom: 4px;"><?php esc_html_e( '4 Full Demo Stories', 'journalist-portfolio-hub' ); ?></strong>
							<span style="color: #64748b; font-size: 0.88rem; line-height: 1.45; display: block;"><?php esc_html_e( 'Includes kicker headings, publisher meta, estimated reading time, and editorial photos.', 'journalist-portfolio-hub' ); ?></span>
						</div>
					</div>

					<div style="background: #f8fafc; padding: 18px; border-radius: 10px; border: 1px solid #e2e8f0; display: flex; gap: 14px; align-items: flex-start;">
						<span class="dashicons dashicons-networking" style="font-size: 28px; width: 28px; height: 28px; color: #059669; flex-shrink: 0; margin-top: 2px;"></span>
						<div>
							<strong style="color: #0f172a; font-size: 0.98rem; display: block; margin-bottom: 4px;"><?php esc_html_e( '4 Publications & Media Outlets', 'journalist-portfolio-hub' ); ?></strong>
							<span style="color: #64748b; font-size: 0.88rem; line-height: 1.45; display: block;"><?php esc_html_e( 'Kaler Kantho, The Daily Star, Reuters, and Prothom Alo with direct website links.', 'journalist-portfolio-hub' ); ?></span>
						</div>
					</div>

					<div style="background: #f8fafc; padding: 18px; border-radius: 10px; border: 1px solid #e2e8f0; display: flex; gap: 14px; align-items: flex-start;">
						<span class="dashicons dashicons-awards" style="font-size: 28px; width: 28px; height: 28px; color: #059669; flex-shrink: 0; margin-top: 2px;"></span>
						<div>
							<strong style="color: #0f172a; font-size: 0.98rem; display: block; margin-bottom: 4px;"><?php esc_html_e( '6 Awards & Fellowships', 'journalist-portfolio-hub' ); ?></strong>
							<span style="color: #64748b; font-size: 0.88rem; line-height: 1.45; display: block;"><?php esc_html_e( 'GCCA+ Youth Awards, South Asian Fellowship, IFCN Poynter Grant, and more.', 'journalist-portfolio-hub' ); ?></span>
						</div>
					</div>

					<div style="background: #f8fafc; padding: 18px; border-radius: 10px; border: 1px solid #e2e8f0; display: flex; gap: 14px; align-items: flex-start;">
						<span class="dashicons dashicons-video-alt3" style="font-size: 28px; width: 28px; height: 28px; color: #059669; flex-shrink: 0; margin-top: 2px;"></span>
						<div>
							<strong style="color: #0f172a; font-size: 0.98rem; display: block; margin-bottom: 4px;"><?php esc_html_e( '4 Multimedia Productions', 'journalist-portfolio-hub' ); ?></strong>
							<span style="color: #64748b; font-size: 0.88rem; line-height: 1.45; display: block;"><?php esc_html_e( 'Video documentaries, podcasts, photo essays, and data visualization projects.', 'journalist-portfolio-hub' ); ?></span>
						</div>
					</div>
				</div>

				<form method="post" onsubmit="return confirm('<?php esc_js( esc_html_e( 'Are you sure you want to re-seed demo portfolio content?', 'journalist-portfolio-hub' ) ); ?>');">
					<?php wp_nonce_field( 'jp_reseed_demo_action', 'jp_reseed_nonce' ); ?>
					<input type="hidden" name="action" value="seed_demo">
					<div style="display: flex; align-items: center; justify-content: space-between; background: #ecfdf5; border: 1px solid #a7f3d0; padding: 20px 24px; border-radius: 10px;">
						<div style="color: #065f46; font-size: 0.95rem;">
							<strong><?php esc_html_e( 'Ready to generate demo data?', 'journalist-portfolio-hub' ); ?></strong> <?php esc_html_e( 'Click the button on the right to populate your portfolio.', 'journalist-portfolio-hub' ); ?>
						</div>
						<button type="submit" class="button button-primary" style="background: #059669; border-color: #059669; color: #ffffff; font-size: 0.95rem; font-weight: 700; padding: 8px 24px; height: auto; border-radius: 8px; box-shadow: 0 4px 12px rgba(5,150,105,0.3); cursor: pointer;">
							✨ <?php esc_html_e( 'Re-seed Demo Stories & Images', 'journalist-portfolio-hub' ); ?>
						</button>
					</div>
				</form>
			</div>
		</div>
		<?php
	}
}

