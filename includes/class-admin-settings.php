<?php
namespace JournalistPortfolio;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles Admin Menu & Submenus for Portfolio Settings, Social Links, and Footer Settings.
 */
class Admin_Settings {

	/**
	 * Class Instance.
	 *
	 * @var Admin_Settings|null
	 */
	private static $instance = null;

	/**
	 * Option Group Names for Isolated Page Saving.
	 */
	const OPTION_GROUP_GENERAL = 'jp_general_settings_group';
	const OPTION_GROUP_SOCIAL  = 'jp_social_settings_group';
	const OPTION_GROUP_FOOTER  = 'jp_footer_settings_group';

	/**
	 * Get Instance.
	 *
	 * @return Admin_Settings
	 */
	public static function get_instance(): Admin_Settings {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
	}

	/**
	 * Register Top-Level Admin Menu & Submenus.
	 */
	public function register_admin_menu(): void {
		// Top Level Menu
		add_menu_page(
			__( 'Portfolio Settings', 'journalist-portfolio-hub' ),
			__( 'Portfolio Settings', 'journalist-portfolio-hub' ),
			'manage_options',
			'journalist-portfolio-settings',
			array( $this, 'render_general_settings_page' ),
			'dashicons-id-alt',
			25
		);

		// Submenu 1: General & Profile
		add_submenu_page(
			'journalist-portfolio-settings',
			__( 'General & Profile', 'journalist-portfolio-hub' ),
			__( 'General & Profile', 'journalist-portfolio-hub' ),
			'manage_options',
			'journalist-portfolio-settings',
			array( $this, 'render_general_settings_page' )
		);

		// Submenu 2: Follow Me (Social Links)
		add_submenu_page(
			'journalist-portfolio-settings',
			__( 'Follow Me (Social Links)', 'journalist-portfolio-hub' ),
			__( 'Follow Me (Social Links)', 'journalist-portfolio-hub' ),
			'manage_options',
			'jp-social-links',
			array( $this, 'render_social_links_page' )
		);


		// Submenu 3: Awards & Fellowships
		add_submenu_page(
			'journalist-portfolio-settings',
			__( 'Awards & Fellowships', 'journalist-portfolio-hub' ),
			__( 'Awards & Fellowships', 'journalist-portfolio-hub' ),
			'manage_options',
			'jp-awards-settings',
			array( $this, 'render_awards_settings_page' )
		);

		// Submenu 4: Multimedia
		add_submenu_page(
			'journalist-portfolio-settings',
			__( 'Multimedia', 'journalist-portfolio-hub' ),
			__( 'Multimedia', 'journalist-portfolio-hub' ),
			'manage_options',
			'jp-multimedia-settings',
			array( $this, 'render_multimedia_settings_page' )
		);

		// Submenu 5: Impact Stats
		add_submenu_page(
			'journalist-portfolio-settings',
			__( 'Impact Stats', 'journalist-portfolio-hub' ),
			__( 'Impact Stats', 'journalist-portfolio-hub' ),
			'manage_options',
			'jp-impact-stats-settings',
			array( $this, 'render_impact_stats_page' )
		);

		// Submenu 6: Footer Settings
		add_submenu_page(
			'journalist-portfolio-settings',
			__( 'Footer Settings', 'journalist-portfolio-hub' ),
			__( 'Footer Settings', 'journalist-portfolio-hub' ),
			'manage_options',
			'jp-footer-settings',
			array( $this, 'render_footer_settings_page' )
		);

		// Submenu 7: Re-seed Demo Data
		add_submenu_page(
			'journalist-portfolio-settings',
			__( 'Re-seed Demo Data', 'journalist-portfolio-hub' ),
			__( 'Re-seed Demo Data', 'journalist-portfolio-hub' ),
			'manage_options',
			'jp-reseed-demo-settings',
			array( \JournalistPortfolio\CPT_Stories::get_instance(), 'render_reseed_demo_page' )
		);
	}

	/**
	 * Register Settings in WP Options API with Isolated Groups.
	 */
	public function register_settings(): void {
		// 1. General & Profile Group
		$general_fields = array(
			'jp_full_name'        => 'sanitize_text_field',
			'jp_designation'      => 'sanitize_text_field',
			'jp_contact_phone'    => 'sanitize_text_field',
			'jp_contact_email'    => 'sanitize_email',
			'jp_contact_location' => 'sanitize_text_field',
			'jp_bio_text'         => 'wp_kses_post',
			'jp_profile_image'    => 'esc_url_raw',
			'jp_hero_cover_image' => 'esc_url_raw',
			'jp_show_on_home'     => array( $this, 'sanitize_checkbox' ),
			'jp_home_objective'   => 'wp_kses_post',
		);

		foreach ( $general_fields as $field_name => $sanitize_callback ) {
			register_setting(
				self::OPTION_GROUP_GENERAL,
				$field_name,
				array(
					'type'              => 'string',
					'sanitize_callback' => $sanitize_callback,
					'default'           => '',
				)
			);
		}

		// 2. Social Links Group
		$social_fields = array(
			'jp_social_links_data' => array( $this, 'sanitize_social_links_json' ),
			'jp_social_twitter'    => 'esc_url_raw',
			'jp_social_facebook'   => 'esc_url_raw',
			'jp_social_linkedin'   => 'esc_url_raw',
			'jp_social_youtube'    => 'esc_url_raw',
			'jp_social_instagram'  => 'esc_url_raw',
			'jp_social_medium'     => 'esc_url_raw',
		);

		foreach ( $social_fields as $field_name => $sanitize_callback ) {
			register_setting(
				self::OPTION_GROUP_SOCIAL,
				$field_name,
				array(
					'type'              => 'string',
					'sanitize_callback' => $sanitize_callback,
					'default'           => '',
				)
			);
		}

		// 3. Footer Options Group
		$footer_fields = array(
			'jp_footer_col1_title'       => 'sanitize_text_field',
			'jp_footer_col1_desc'        => 'sanitize_textarea_field',
			'jp_footer_contact_email'    => 'sanitize_email',
			'jp_footer_contact_phone'    => 'sanitize_text_field',
			'jp_footer_contact_location' => 'sanitize_text_field',
			'jp_footer_col2_title'       => 'sanitize_text_field',
			'jp_footer_col3_title'       => 'sanitize_text_field',
			'jp_footer_menu_id'          => 'sanitize_text_field',
			'jp_footer_col4_title'       => 'sanitize_text_field',
			'jp_footer_cv_file'          => 'esc_url_raw',
			'jp_cv_file'                 => 'esc_url_raw',
			'jp_cv_button_label'         => 'sanitize_text_field',
			'jp_footer_mediakit_file'    => 'esc_url_raw',
			'jp_media_kit_file'          => 'esc_url_raw',
			'jp_media_kit_label'        => 'sanitize_text_field',
			'jp_footer_copyright_text'   => 'sanitize_text_field',
			'jp_footer_tagline'          => 'sanitize_text_field',
		);

		foreach ( $footer_fields as $field_name => $sanitize_callback ) {
			register_setting(
				self::OPTION_GROUP_FOOTER,
				$field_name,
				array(
					'type'              => 'string',
					'sanitize_callback' => $sanitize_callback,
					'default'           => '',
				)
			);
		}
	}

	/**
	 * Sanitize Social Links JSON string.
	 *
	 * @param mixed $value JSON string or raw input.
	 * @return string Clean JSON string.
	 */
	public function sanitize_social_links_json( $value ): string {
		if ( empty( $value ) ) {
			return '[]';
		}

		$raw_data = is_string( $value ) ? wp_unslash( $value ) : $value;
		$decoded  = json_decode( $raw_data, true );

		if ( ! is_array( $decoded ) ) {
			return '[]';
		}

		$clean = array();
		foreach ( $decoded as $item ) {
			if ( ! empty( $item['name'] ) && ! empty( $item['url'] ) ) {
				$clean[] = array(
					'name' => sanitize_text_field( $item['name'] ),
					'icon' => sanitize_text_field( isset( $item['icon'] ) ? $item['icon'] : 'dashicons-share' ),
					'url'  => esc_url_raw( $item['url'] ),
				);
			}
		}

		return wp_json_encode( $clean );
	}

	/**
	 * Sanitize Checkbox.
	 */
	public function sanitize_checkbox( $value ): string {
		return ( ! empty( $value ) && ( '1' === (string) $value || 'on' === (string) $value || true === $value ) ) ? '1' : '0';
	}

	/**
	 * Enqueue Admin Assets.
	 */
	public function enqueue_admin_assets( string $hook ): void {
		$page_param = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
		$post_type  = isset( $_GET['post_type'] ) ? sanitize_text_field( wp_unslash( $_GET['post_type'] ) ) : '';

		$is_portfolio_page = (
			! empty( $page_param ) && (
				false !== strpos( $page_param, 'jp' ) ||
				false !== strpos( $page_param, 'portfolio' )
			)
		) || (
			false !== strpos( $hook, 'journalist-portfolio' ) ||
			false !== strpos( $hook, 'jp-social-links' ) ||
			false !== strpos( $hook, 'jp-footer-settings' ) ||
			false !== strpos( $hook, 'jp-awards-settings' ) ||
			false !== strpos( $hook, 'jp-multimedia-settings' ) ||
			false !== strpos( $hook, 'jp-impact-stats-settings' )
		);

		$screen        = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		$is_cpt_screen = (
			! empty( $post_type ) && in_array( $post_type, array( 'jp_multimedia', 'story', 'jp_award' ), true )
		) || (
			$screen && ! empty( $screen->post_type ) && in_array( $screen->post_type, array( 'jp_multimedia', 'story', 'jp_award' ), true )
		);

		if ( ! $is_portfolio_page && ! $is_cpt_screen ) {
			return;
		}

		wp_enqueue_media();
		wp_enqueue_style( 'jp-admin-style', JP_HUB_PLUGIN_URL . 'assets/css/admin-style.css', array(), JP_HUB_VERSION );
		wp_enqueue_script( 'jp-admin-media', JP_HUB_PLUGIN_URL . 'assets/js/admin-media.js', array( 'jquery', 'media-upload', 'media-views' ), JP_HUB_VERSION, true );
	}

	/**
	 * Render Submenu 1: General & Profile Page.
	 */
	public function render_general_settings_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$full_name        = get_option( 'jp_full_name', '' );
		$designation      = get_option( 'jp_designation', '' );
		$contact_phone    = get_option( 'jp_contact_phone', '' );
		$contact_email    = get_option( 'jp_contact_email', '' );
		$contact_location = get_option( 'jp_contact_location', '' );
		$bio_text         = get_option( 'jp_bio_text', '' );
		$profile_image    = get_option( 'jp_profile_image', '' );
		$show_on_home     = get_option( 'jp_show_on_home', '1' );
		$home_objective   = get_option( 'jp_home_objective', '' );
		?>
		<div class="wrap jp-admin-wrap">
			<div class="jp-admin-header">
				<h1><?php esc_html_e( 'General & Profile Settings', 'journalist-portfolio-hub' ); ?></h1>
				<p><?php esc_html_e( 'Manage your personal profile, designation, contact info (Mobile Number, Email, Address), bio, and hero statement.', 'journalist-portfolio-hub' ); ?></p>
			</div>

			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php settings_fields( self::OPTION_GROUP_GENERAL ); ?>

				<div class="jp-admin-card">
					<h2><?php esc_html_e( 'Personal & Professional Details', 'journalist-portfolio-hub' ); ?></h2>

					<div class="jp-field-group">
						<label for="jp_full_name"><?php esc_html_e( 'Full Name:', 'journalist-portfolio-hub' ); ?></label>
						<input type="text" id="jp_full_name" name="jp_full_name" value="<?php echo esc_attr( $full_name ); ?>" placeholder="e.g. Jane Doe">
					</div>

					<div class="jp-field-group">
						<label for="jp_designation"><?php esc_html_e( 'Designation / Title:', 'journalist-portfolio-hub' ); ?></label>
						<input type="text" id="jp_designation" name="jp_designation" value="<?php echo esc_attr( $designation ); ?>" placeholder="e.g. Senior Investigative & Environmental Journalist">
					</div>

					<div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 20px;">
						<div class="jp-field-group">
							<label for="jp_contact_phone"><strong><?php esc_html_e( 'Mobile / Phone Number:', 'journalist-portfolio-hub' ); ?></strong></label>
							<input type="text" id="jp_contact_phone" name="jp_contact_phone" value="<?php echo esc_attr( $contact_phone ); ?>" placeholder="+880 1700 000000" style="width: 100%;">
						</div>

						<div class="jp-field-group">
							<label for="jp_contact_email"><strong><?php esc_html_e( 'Contact Email Address:', 'journalist-portfolio-hub' ); ?></strong></label>
							<input type="text" id="jp_contact_email" name="jp_contact_email" value="<?php echo esc_attr( $contact_email ); ?>" placeholder="reporter@journalist.com" style="width: 100%;">
						</div>

						<div class="jp-field-group">
							<label for="jp_contact_location"><strong><?php esc_html_e( 'Address / Location:', 'journalist-portfolio-hub' ); ?></strong></label>
							<input type="text" id="jp_contact_location" name="jp_contact_location" value="<?php echo esc_attr( $contact_location ); ?>" placeholder="Dhaka, Bangladesh" style="width: 100%;">
						</div>
					</div>

					<div class="jp-field-group">
						<label for="jp_profile_image"><?php esc_html_e( 'Profile Image:', 'journalist-portfolio-hub' ); ?></label>
						<input type="hidden" id="jp_profile_image" name="jp_profile_image" value="<?php echo esc_url( $profile_image ); ?>">
						<button type="button" class="button button-secondary" id="jp_upload_image_btn"><?php esc_html_e( 'Select Image from Media Library', 'journalist-portfolio-hub' ); ?></button>
						<button type="button" class="button button-link-delete" id="jp_remove_image_btn" style="<?php echo empty( $profile_image ) ? 'display:none;' : ''; ?>"><?php esc_html_e( 'Remove Image', 'journalist-portfolio-hub' ); ?></button>
						
						<div class="jp-profile-preview-box">
							<img id="jp_profile_image_preview" src="<?php echo esc_url( $profile_image ); ?>" style="<?php echo empty( $profile_image ) ? 'display:none;' : ''; ?>" alt="Profile Preview">
						</div>
					</div>

					<div class="jp-field-group">
						<label for="jp_bio_text"><?php esc_html_e( 'Full Bio & Background:', 'journalist-portfolio-hub' ); ?></label>
						<?php
						$editor_settings = array(
							'textarea_name' => 'jp_bio_text',
							'textarea_rows' => 7,
							'media_buttons' => true,
						);
						wp_editor( $bio_text, 'jp_bio_text_editor', $editor_settings );
						?>
					</div>
				</div>

				<div class="jp-admin-card">
					<h2><?php esc_html_e( 'Homepage Display & Mission Statement', 'journalist-portfolio-hub' ); ?></h2>

					<div class="jp-field-group">
						<label for="jp_show_on_home">
							<input type="checkbox" id="jp_show_on_home" name="jp_show_on_home" value="1" <?php checked( $show_on_home, '1' ); ?>>
							<?php esc_html_e( 'Show Profile & Hero Section on Home Page', 'journalist-portfolio-hub' ); ?>
						</label>
					</div>

					<?php $hero_cover_image = get_option( 'jp_hero_cover_image', '' ); ?>
					<div class="jp-field-group" style="margin-top: 16px;">
						<label for="jp_hero_cover_image"><strong><?php esc_html_e( 'Hero Cover / Background Image:', 'journalist-portfolio-hub' ); ?></strong></label>
						<div style="display: flex; gap: 10px; align-items: center; margin-top: 6px;">
							<input type="text" id="jp_hero_cover_image" name="jp_hero_cover_image" value="<?php echo esc_url( $hero_cover_image ); ?>" class="widefat jp-media-url" placeholder="https://...">
							<button type="button" class="button jp-upload-btn"><?php esc_html_e( 'Select Hero Cover Image', 'journalist-portfolio-hub' ); ?></button>
						</div>
						<span class="description" style="font-size: 0.85em; color: #64748b; margin-top: 4px; display: block;">
							<?php esc_html_e( 'Recommended size: 1920x800px or high-resolution wide image. Will fit full-width as Hero background without repeating.', 'journalist-portfolio-hub' ); ?>
						</span>
						<div class="jp-hero-cover-preview-box" style="margin-top: 10px;">
							<img class="jp-thumb-preview" src="<?php echo esc_url( $hero_cover_image ); ?>" style="<?php echo empty( $hero_cover_image ) ? 'display:none;' : ''; ?> max-width: 360px; max-height: 160px; border-radius: 8px; border: 1px solid #cbd5e1; object-fit: cover;" alt="Hero Cover Preview">
						</div>
					</div>

					<div class="jp-field-group" style="margin-top: 16px;">
						<label for="jp_home_objective"><strong><?php esc_html_e( 'Hero Objective / Mission Statement:', 'journalist-portfolio-hub' ); ?></strong></label>
						<textarea id="jp_home_objective" name="jp_home_objective" rows="4" placeholder="<?php esc_attr_e( 'Uncovering human stories, climate crisis impacts, and investigative truth across global borders.', 'journalist-portfolio-hub' ); ?>"><?php echo esc_textarea( $home_objective ); ?></textarea>
					</div>
				</div>

				<?php submit_button( __( 'Save General Settings', 'journalist-portfolio-hub' ) ); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Render Submenu 2: Follow Me (Social Links) Page.
	 */
	public function render_social_links_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$social_json = get_option( 'jp_social_links_data', '[]' );
		$social_list = json_decode( $social_json, true );

		if ( ! is_array( $social_list ) || empty( $social_list ) ) {
			// Populate defaults from legacy social fields if available
			$social_list = array();
			$legacy_map  = array(
				'Twitter / X' => array( 'icon' => 'dashicons-twitter', 'url' => get_option( 'jp_social_twitter', '' ) ),
				'LinkedIn'    => array( 'icon' => 'dashicons-linkedin', 'url' => get_option( 'jp_social_linkedin', '' ) ),
				'Facebook'    => array( 'icon' => 'dashicons-facebook', 'url' => get_option( 'jp_social_facebook', '' ) ),
				'YouTube'     => array( 'icon' => 'dashicons-video-alt3', 'url' => get_option( 'jp_social_youtube', '' ) ),
				'Instagram'   => array( 'icon' => 'dashicons-instagram', 'url' => get_option( 'jp_social_instagram', '' ) ),
				'Medium'      => array( 'icon' => 'dashicons-format-aside', 'url' => get_option( 'jp_social_medium', '' ) ),
			);
			foreach ( $legacy_map as $name => $data ) {
				if ( ! empty( $data['url'] ) ) {
					$social_list[] = array( 'name' => $name, 'icon' => $data['icon'], 'url' => $data['url'] );
				}
			}
		}

		$icon_options = array(
			'dashicons-twitter'      => 'Twitter / X',
			'dashicons-linkedin'     => 'LinkedIn',
			'dashicons-facebook'     => 'Facebook',
			'dashicons-video-alt3'   => 'YouTube / Video',
			'dashicons-instagram'    => 'Instagram',
			'dashicons-format-aside' => 'Medium / Article',
			'dashicons-share'        => 'Share / Social',
			'dashicons-email'        => 'Email / Mail',
			'dashicons-networking'   => 'Website / Globe',
			'dashicons-rss'          => 'RSS Feed',
		);
		?>
		<div class="wrap jp-admin-wrap">
			<div class="jp-admin-header">
				<h1><?php esc_html_e( 'Follow Me - Social Links Manager', 'journalist-portfolio-hub' ); ?></h1>
				<p><?php esc_html_e( 'Add, edit, or remove your social media profiles. Each item includes Social Name, Icon, and Profile Link.', 'journalist-portfolio-hub' ); ?></p>
			</div>

			<?php settings_errors(); ?>

			<form method="post" action="options.php" id="jp_social_links_form">
				<?php settings_fields( self::OPTION_GROUP_SOCIAL ); ?>

				<input type="hidden" id="jp_social_links_data" name="jp_social_links_data" value="<?php echo esc_attr( wp_json_encode( $social_list ) ); ?>">

				<div class="jp-admin-card">
					<h2><?php esc_html_e( 'Social Profiles List ("Follow Me")', 'journalist-portfolio-hub' ); ?></h2>

					<table class="widefat fixed striped" id="jp_social_links_table" style="margin-bottom: 20px;">
						<thead>
							<tr>
								<th style="width: 25%;"><?php esc_html_e( 'Social Name', 'journalist-portfolio-hub' ); ?></th>
								<th style="width: 25%;"><?php esc_html_e( 'Icon', 'journalist-portfolio-hub' ); ?></th>
								<th style="width: 40%;"><?php esc_html_e( 'Profile Link (URL)', 'journalist-portfolio-hub' ); ?></th>
								<th style="width: 10%; text-align: center;"><?php esc_html_e( 'Action', 'journalist-portfolio-hub' ); ?></th>
							</tr>
						</thead>
						<tbody id="jp_social_rows_body">
							<?php if ( ! empty( $social_list ) ) : ?>
								<?php foreach ( $social_list as $index => $item ) : ?>
									<tr class="jp-social-row">
										<td>
											<input type="text" class="jp-social-name widefat" value="<?php echo esc_attr( $item['name'] ); ?>" placeholder="e.g. Twitter / X">
										</td>
										<td>
											<select class="jp-social-icon widefat">
												<?php foreach ( $icon_options as $icon_class => $icon_label ) : ?>
													<option value="<?php echo esc_attr( $icon_class ); ?>" <?php selected( isset( $item['icon'] ) ? $item['icon'] : '', $icon_class ); ?>>
														<?php echo esc_html( $icon_label ); ?>
													</option>
												<?php endforeach; ?>
											</select>
										</td>
										<td>
											<input type="url" class="jp-social-url widefat" value="<?php echo esc_url( $item['url'] ); ?>" placeholder="https://x.com/username">
										</td>
										<td style="text-align: center;">
											<button type="button" class="button button-link-delete jp-remove-social-row">&times; <?php esc_html_e( 'Remove', 'journalist-portfolio-hub' ); ?></button>
										</td>
									</tr>
								<?php endforeach; ?>
							<?php endif; ?>
						</tbody>
					</table>

					<button type="button" class="button button-secondary" id="jp_add_social_row">+ <?php esc_html_e( 'Add New Social Link', 'journalist-portfolio-hub' ); ?></button>
				</div>

				<?php submit_button( __( 'Save Social Links', 'journalist-portfolio-hub' ) ); ?>
			</form>
		</div>

		<script>
		jQuery(document).ready(function($){
			function syncJson() {
				var items = [];
				$('#jp_social_rows_body tr').each(function(){
					var name = $(this).find('.jp-social-name').val().trim();
					var icon = $(this).find('.jp-social-icon').val();
					var url  = $(this).find('.jp-social-url').val().trim();
					if(name !== '' && url !== '') {
						items.push({ name: name, icon: icon, url: url });
					}
				});
				$('#jp_social_links_data').val(JSON.stringify(items));
			}

			$(document).on('input change', '.jp-social-name, .jp-social-icon, .jp-social-url', function(){
				syncJson();
			});

			$('#jp_add_social_row').click(function(e){
				e.preventDefault();
				var newRow = '<tr class="jp-social-row">' +
					'<td><input type="text" class="jp-social-name widefat" placeholder="e.g. LinkedIn"></td>' +
					'<td><select class="jp-social-icon widefat">' +
						'<option value="dashicons-twitter">Twitter / X</option>' +
						'<option value="dashicons-linkedin">LinkedIn</option>' +
						'<option value="dashicons-facebook">Facebook</option>' +
						'<option value="dashicons-video-alt3">YouTube / Video</option>' +
						'<option value="dashicons-instagram">Instagram</option>' +
						'<option value="dashicons-format-aside">Medium / Article</option>' +
						'<option value="dashicons-share">Share / Social</option>' +
						'<option value="dashicons-email">Email / Mail</option>' +
						'<option value="dashicons-networking">Website / Globe</option>' +
					'</select></td>' +
					'<td><input type="url" class="jp-social-url widefat" placeholder="https://linkedin.com/in/username"></td>' +
					'<td style="text-align: center;"><button type="button" class="button button-link-delete jp-remove-social-row">&times; Remove</button></td>' +
				'</tr>';
				$('#jp_social_rows_body').append(newRow);
				syncJson();
			});

			$(document).on('click', '.jp-remove-social-row', function(e){
				e.preventDefault();
				$(this).closest('tr').remove();
				syncJson();
			});

			$('#jp_social_links_form').on('submit', function(){
				syncJson();
			});
		});
		</script>
		<?php
	}

	/**
	 * Render Submenu 3: Footer Settings Page.
	 */
	public function render_footer_settings_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$col1_title       = get_option( 'jp_footer_col1_title', "Let's Connect" );
		$col1_desc        = get_option( 'jp_footer_col1_desc', 'Investigative reporting focused on environmental truth, human rights, and global climate issues.' );
		$contact_email    = get_option( 'jp_footer_contact_email', get_option( 'jp_contact_email', '' ) );
		$contact_phone    = get_option( 'jp_footer_contact_phone', get_option( 'jp_contact_phone', '' ) );
		$contact_location = get_option( 'jp_footer_contact_location', get_option( 'jp_contact_location', 'Dhaka, Bangladesh' ) );

		$col2_title       = get_option( 'jp_footer_col2_title', 'Follow Me' );
		$col3_title       = get_option( 'jp_footer_col3_title', 'Quick Links' );
		$menu_id          = get_option( 'jp_footer_menu_id', '' );

		$col4_title       = get_option( 'jp_footer_col4_title', 'Download' );
		
		$cv_file = get_option( 'jp_footer_cv_file', '' );
		if ( empty( $cv_file ) ) {
			$cv_file = get_option( 'jp_cv_file', '' );
		}

		$media_kit_file = get_option( 'jp_footer_mediakit_file', '' );
		if ( empty( $media_kit_file ) ) {
			$media_kit_file = get_option( 'jp_media_kit_file', '' );
		}

		$copyright_text   = get_option( 'jp_footer_copyright_text', '© ' . date( 'Y' ) . ' Md Jahidul Islam. All Rights Reserved.' );
		$tagline          = get_option( 'jp_footer_tagline', 'Built with passion for truth, people and the planet.' );

		$nav_menus = wp_get_nav_menus();
		?>
		<div class="wrap jp-admin-wrap">
			<div class="jp-admin-header">
				<h1><?php esc_html_e( 'Footer Settings (4-Column Module)', 'journalist-portfolio-hub' ); ?></h1>
				<p><?php esc_html_e( 'Configure Column titles, contact details, menu selection, download links, and footer copyright bar.', 'journalist-portfolio-hub' ); ?></p>
			</div>

			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php settings_fields( self::OPTION_GROUP_FOOTER ); ?>

				<!-- Column 1: Contact Details -->
				<div class="jp-admin-card">
					<h2><?php esc_html_e( 'Column 1: Contact Details ("Let\'s Connect")', 'journalist-portfolio-hub' ); ?></h2>
					
					<div class="jp-field-group">
						<label for="jp_footer_col1_title"><?php esc_html_e( 'Column 1 Header Title:', 'journalist-portfolio-hub' ); ?></label>
						<input type="text" id="jp_footer_col1_title" name="jp_footer_col1_title" value="<?php echo esc_attr( $col1_title ); ?>">
					</div>

					<div class="jp-field-group">
						<label for="jp_footer_col1_desc"><?php esc_html_e( 'Short Description / Paragraph:', 'journalist-portfolio-hub' ); ?></label>
						<textarea id="jp_footer_col1_desc" name="jp_footer_col1_desc" rows="3"><?php echo esc_textarea( $col1_desc ); ?></textarea>
					</div>

					<div class="jp-field-group">
						<label for="jp_footer_contact_email"><?php esc_html_e( 'Contact Email Address:', 'journalist-portfolio-hub' ); ?></label>
						<input type="text" id="jp_footer_contact_email" name="jp_footer_contact_email" value="<?php echo esc_attr( $contact_email ); ?>" placeholder="reporter@journalist.com">
					</div>

					<div class="jp-field-group">
						<label for="jp_footer_contact_phone"><?php esc_html_e( 'Contact Phone Number:', 'journalist-portfolio-hub' ); ?></label>
						<input type="text" id="jp_footer_contact_phone" name="jp_footer_contact_phone" value="<?php echo esc_attr( $contact_phone ); ?>" placeholder="+880 1700 000000">
					</div>

					<div class="jp-field-group">
						<label for="jp_footer_contact_location"><?php esc_html_e( 'Location / Address:', 'journalist-portfolio-hub' ); ?></label>
						<input type="text" id="jp_footer_contact_location" name="jp_footer_contact_location" value="<?php echo esc_attr( $contact_location ); ?>" placeholder="Dhaka, Bangladesh">
					</div>
				</div>

				<!-- Column 2 & 3 -->
				<div class="jp-admin-card">
					<h2><?php esc_html_e( 'Column 2 & 3 Header Titles & Menu Selection', 'journalist-portfolio-hub' ); ?></h2>
					
					<div class="jp-field-group">
						<label for="jp_footer_col2_title"><?php esc_html_e( 'Column 2 Title ("Follow Me"):', 'journalist-portfolio-hub' ); ?></label>
						<input type="text" id="jp_footer_col2_title" name="jp_footer_col2_title" value="<?php echo esc_attr( $col2_title ); ?>">
						<p class="description"><?php esc_html_e( 'Social links populating this column are managed under Portfolio Settings -> Follow Me (Social Links).', 'journalist-portfolio-hub' ); ?></p>
					</div>

					<div class="jp-field-group">
						<label for="jp_footer_col3_title"><?php esc_html_e( 'Column 3 Title ("Quick Links"):', 'journalist-portfolio-hub' ); ?></label>
						<input type="text" id="jp_footer_col3_title" name="jp_footer_col3_title" value="<?php echo esc_attr( $col3_title ); ?>">
					</div>

					<div class="jp-field-group">
						<label for="jp_footer_menu_id"><?php esc_html_e( 'Select Navigation Menu for Quick Links:', 'journalist-portfolio-hub' ); ?></label>
						<select id="jp_footer_menu_id" name="jp_footer_menu_id" class="widefat" style="max-width: 400px;">
							<option value=""><?php esc_html_e( '-- Default Plugin Page Links --', 'journalist-portfolio-hub' ); ?></option>
							<?php if ( ! empty( $nav_menus ) ) : ?>
								<?php foreach ( $nav_menus as $menu ) : ?>
									<option value="<?php echo esc_attr( $menu->term_id ); ?>" <?php selected( $menu_id, $menu->term_id ); ?>>
										<?php echo esc_html( $menu->name ); ?>
									</option>
								<?php endforeach; ?>
							<?php endif; ?>
						</select>
					</div>
				</div>

				<!-- Column 4 Downloads -->
				<div class="jp-admin-card">
					<h2><?php esc_html_e( 'Column 4: Download Files', 'journalist-portfolio-hub' ); ?></h2>
					
					<div class="jp-field-group">
						<label for="jp_footer_col4_title"><?php esc_html_e( 'Column 4 Header Title:', 'journalist-portfolio-hub' ); ?></label>
						<input type="text" id="jp_footer_col4_title" name="jp_footer_col4_title" value="<?php echo esc_attr( $col4_title ); ?>">
					</div>

					<div class="jp-field-group">
						<label for="jp_footer_cv_file"><strong><?php esc_html_e( 'CV / Resume PDF File:', 'journalist-portfolio-hub' ); ?></strong></label>
						<div style="display: flex; gap: 10px; align-items: center; max-width: 600px;">
							<input type="text" id="jp_footer_cv_file" name="jp_footer_cv_file" value="<?php echo esc_url( $cv_file ); ?>" placeholder="https://example.com/wp-content/uploads/cv.pdf" oninput="document.getElementById('jp_cv_file').value = this.value">
							<input type="hidden" id="jp_cv_file" name="jp_cv_file" value="<?php echo esc_url( $cv_file ); ?>">
							<button type="button" class="button button-secondary" id="jp_upload_cv_btn"><?php esc_html_e( 'Upload / Select', 'journalist-portfolio-hub' ); ?></button>
						</div>
					</div>

					<div class="jp-field-group">
						<label for="jp_footer_mediakit_file"><strong><?php esc_html_e( 'Media Kit PDF File:', 'journalist-portfolio-hub' ); ?></strong></label>
						<div style="display: flex; gap: 10px; align-items: center; max-width: 600px;">
							<input type="text" id="jp_footer_mediakit_file" name="jp_footer_mediakit_file" value="<?php echo esc_url( $media_kit_file ); ?>" placeholder="https://example.com/wp-content/uploads/media-kit.pdf" oninput="document.getElementById('jp_media_kit_file').value = this.value">
							<input type="hidden" id="jp_media_kit_file" name="jp_media_kit_file" value="<?php echo esc_url( $media_kit_file ); ?>">
							<button type="button" class="button button-secondary" id="jp_upload_mediakit_btn"><?php esc_html_e( 'Upload / Select', 'journalist-portfolio-hub' ); ?></button>
						</div>
					</div>
				</div>

				<!-- Bottom Bar -->
				<div class="jp-admin-card">
					<h2><?php esc_html_e( 'Footer Bottom Bar Settings', 'journalist-portfolio-hub' ); ?></h2>

					<div class="jp-field-group">
						<label for="jp_footer_copyright_text"><?php esc_html_e( 'Copyright Notice Text:', 'journalist-portfolio-hub' ); ?></label>
						<input type="text" id="jp_footer_copyright_text" name="jp_footer_copyright_text" value="<?php echo esc_attr( $copyright_text ); ?>">
					</div>

					<div class="jp-field-group">
						<label for="jp_footer_tagline"><?php esc_html_e( 'Footer Right Tagline:', 'journalist-portfolio-hub' ); ?></label>
						<input type="text" id="jp_footer_tagline" name="jp_footer_tagline" value="<?php echo esc_attr( $tagline ); ?>">
					</div>
				</div>

				<?php submit_button( __( 'Save Footer Settings', 'journalist-portfolio-hub' ) ); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Render Awards & Fellowships Settings Admin Page.
	 */
	public function render_awards_settings_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Handle Form Submissions (Add / Delete / Edit).
		if ( isset( $_POST['jp_award_admin_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['jp_award_admin_nonce'] ) ), 'jp_save_award_admin' ) ) {
			// Delete action
			if ( isset( $_POST['action'] ) && 'delete_award' === $_POST['action'] && isset( $_POST['award_id'] ) ) {
				$award_id = absint( $_POST['award_id'] );
				wp_delete_post( $award_id, true );
				echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Award deleted successfully.', 'journalist-portfolio-hub' ) . '</p></div>';
			}
			// Save / Update action
			elseif ( isset( $_POST['award_title'] ) && ! empty( trim( $_POST['award_title'] ) ) ) {
				$title       = sanitize_text_field( wp_unslash( $_POST['award_title'] ) );
				$award_for   = sanitize_text_field( wp_unslash( $_POST['award_for'] ?? '' ) );
				$year        = sanitize_text_field( wp_unslash( $_POST['award_year'] ?? '' ) );
				$location    = sanitize_text_field( wp_unslash( $_POST['award_location'] ?? '' ) );
				$icon        = sanitize_text_field( wp_unslash( $_POST['award_icon'] ?? 'dashicons-awards' ) );
				$description = sanitize_textarea_field( wp_unslash( $_POST['award_description'] ?? '' ) );

				$post_id = isset( $_POST['award_id'] ) ? absint( $_POST['award_id'] ) : 0;

				$post_data = array(
					'post_title'  => $title,
					'post_type'   => 'jp_award',
					'post_status' => 'publish',
				);

				if ( $post_id > 0 ) {
					$post_data['ID'] = $post_id;
					wp_update_post( $post_data );
				} else {
					$post_id = wp_insert_post( $post_data );
				}

				if ( $post_id && ! is_wp_error( $post_id ) ) {
					update_post_meta( $post_id, '_award_for', $award_for );
					update_post_meta( $post_id, '_award_year', $year );
					update_post_meta( $post_id, '_award_location', $location );
					update_post_meta( $post_id, '_award_icon', $icon );
					update_post_meta( $post_id, '_award_description', $description );

					echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Award saved successfully.', 'journalist-portfolio-hub' ) . '</p></div>';
				}
			}
		}

		// Edit Mode Check
		$editing_award = null;
		if ( isset( $_GET['edit_award'] ) ) {
			$edit_id = absint( $_GET['edit_award'] );
			$editing_award = get_post( $edit_id );
		}

		// Fetch All Awards
		$awards = get_posts(
			array(
				'post_type'      => 'jp_award',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => 'date',
				'order'          => 'DESC',
			)
		);
		?>
		<div class="wrap jp-admin-wrap">
			<div class="jp-admin-header" style="margin-bottom: 24px;">
				<h1><?php esc_html_e( 'Awards & Fellowships Management', 'journalist-portfolio-hub' ); ?></h1>
				<p><?php esc_html_e( 'Add and manage journalistic awards, grants, fellowships, and honors.', 'journalist-portfolio-hub' ); ?></p>
			</div>

			<div style="display: grid; grid-template-columns: 380px 1fr; gap: 30px;">
				<!-- Add/Edit Form -->
				<div class="jp-admin-card" style="background: #fff; padding: 24px; border-radius: 8px; border: 1px solid #ccd0d4;">
					<h2><?php echo $editing_award ? esc_html__( 'Edit Award', 'journalist-portfolio-hub' ) : esc_html__( 'Add New Award', 'journalist-portfolio-hub' ); ?></h2>
					<form method="post">
						<?php wp_nonce_field( 'jp_save_award_admin', 'jp_award_admin_nonce' ); ?>
						<?php if ( $editing_award ) : ?>
							<input type="hidden" name="award_id" value="<?php echo esc_attr( $editing_award->ID ); ?>">
						<?php endif; ?>

						<p>
							<label for="award_title"><strong><?php esc_html_e( 'Award / Fellowship Title:', 'journalist-portfolio-hub' ); ?></strong></label><br>
							<input type="text" id="award_title" name="award_title" required class="widefat" value="<?php echo $editing_award ? esc_attr( $editing_award->post_title ) : ''; ?>" placeholder="e.g., GCCA+ Youth Awards">
						</p>

						<p>
							<label for="award_for"><strong><?php esc_html_e( 'Awarded For / Organization:', 'journalist-portfolio-hub' ); ?></strong></label><br>
							<input type="text" id="award_for" name="award_for" class="widefat" value="<?php echo $editing_award ? esc_attr( get_post_meta( $editing_award->ID, '_award_for', true ) ) : ''; ?>" placeholder="e.g., for Climate Storytelling">
						</p>

						<p>
							<label for="award_year"><strong><?php esc_html_e( 'Year:', 'journalist-portfolio-hub' ); ?></strong></label><br>
							<input type="text" id="award_year" name="award_year" class="widefat" value="<?php echo $editing_award ? esc_attr( get_post_meta( $editing_award->ID, '_award_year', true ) ) : ''; ?>" placeholder="e.g., 2025">
						</p>

						<p>
							<label for="award_location"><strong><?php esc_html_e( 'Location / Region:', 'journalist-portfolio-hub' ); ?></strong></label><br>
							<input type="text" id="award_location" name="award_location" class="widefat" value="<?php echo $editing_award ? esc_attr( get_post_meta( $editing_award->ID, '_award_location', true ) ) : ''; ?>" placeholder="e.g., International, Asia, Geneva">
						</p>

						<p>
							<label for="award_icon"><strong><?php esc_html_e( 'Icon (Dashicon / Image URL):', 'journalist-portfolio-hub' ); ?></strong></label><br>
							<input type="text" id="award_icon" name="award_icon" class="widefat" value="<?php echo $editing_award ? esc_attr( get_post_meta( $editing_award->ID, '_award_icon', true ) ) : 'dashicons-awards'; ?>" placeholder="dashicons-awards">
						</p>

						<p>
							<label for="award_description"><strong><?php esc_html_e( 'Description / Impact Summary:', 'journalist-portfolio-hub' ); ?></strong></label><br>
							<textarea id="award_description" name="award_description" rows="4" class="widefat" placeholder="Brief summary of the recognition..."><?php echo $editing_award ? esc_textarea( get_post_meta( $editing_award->ID, '_award_description', true ) ) : ''; ?></textarea>
						</p>

						<p style="margin-top: 20px; display: flex; gap: 10px;">
							<button type="submit" class="button button-primary" style="background: #059669; border-color: #059669; color: #fff;"><?php echo $editing_award ? esc_html__( 'Update Award', 'journalist-portfolio-hub' ) : esc_html__( 'Add Award', 'journalist-portfolio-hub' ); ?></button>
							<?php if ( $editing_award ) : ?>
								<a href="<?php echo esc_url( admin_url( 'admin.php?page=jp-awards-settings' ) ); ?>" class="button button-secondary"><?php esc_html_e( 'Cancel Edit', 'journalist-portfolio-hub' ); ?></a>
							<?php endif; ?>
						</p>
					</form>
				</div>

				<!-- Awards List Table -->
				<div class="jp-admin-card" style="background: #fff; padding: 24px; border-radius: 8px; border: 1px solid #ccd0d4;">
					<h2><?php esc_html_e( 'All Registered Awards', 'journalist-portfolio-hub' ); ?></h2>
					<?php if ( ! empty( $awards ) ) : ?>
						<table class="widefat striped">
							<thead>
								<tr>
									<th style="width: 40px; text-align: center;"><?php esc_html_e( 'Icon', 'journalist-portfolio-hub' ); ?></th>
									<th><?php esc_html_e( 'Title', 'journalist-portfolio-hub' ); ?></th>
									<th><?php esc_html_e( 'Organization / For', 'journalist-portfolio-hub' ); ?></th>
									<th><?php esc_html_e( 'Year', 'journalist-portfolio-hub' ); ?></th>
									<th><?php esc_html_e( 'Location', 'journalist-portfolio-hub' ); ?></th>
									<th style="width: 120px; text-align: center;"><?php esc_html_e( 'Actions', 'journalist-portfolio-hub' ); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $awards as $award ) :
									$for      = get_post_meta( $award->ID, '_award_for', true );
									$yr       = get_post_meta( $award->ID, '_award_year', true );
									$loc      = get_post_meta( $award->ID, '_award_location', true );
									$icon_cls = get_post_meta( $award->ID, '_award_icon', true );
									if ( empty( $icon_cls ) ) {
										$icon_cls = 'dashicons-awards';
									}
									?>
									<tr>
										<td style="text-align: center;">
											<span class="dashicons <?php echo esc_attr( $icon_cls ); ?>" style="color: #059669; font-size: 20px;"></span>
										</td>
										<td><strong><?php echo esc_html( $award->post_title ); ?></strong></td>
										<td><?php echo esc_html( $for ); ?></td>
										<td><span class="badge" style="background: #e2e8f0; padding: 2px 8px; border-radius: 12px; font-weight: 600; font-size: 0.85em;"><?php echo esc_html( $yr ); ?></span></td>
										<td><?php echo esc_html( $loc ); ?></td>
										<td style="text-align: center;">
											<a href="<?php echo esc_url( admin_url( 'admin.php?page=jp-awards-settings&edit_award=' . $award->ID ) ); ?>" class="button button-small"><?php esc_html_e( 'Edit', 'journalist-portfolio-hub' ); ?></a>
											<form method="post" style="display: inline-block;" onsubmit="return confirm('Delete this award?');">
												<?php wp_nonce_field( 'jp_save_award_admin', 'jp_award_admin_nonce' ); ?>
												<input type="hidden" name="action" value="delete_award">
												<input type="hidden" name="award_id" value="<?php echo esc_attr( $award->ID ); ?>">
												<button type="submit" class="button button-small button-link-delete"><?php esc_html_e( 'Delete', 'journalist-portfolio-hub' ); ?></button>
											</form>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					<?php else : ?>
						<p style="color: #64748b;"><?php esc_html_e( 'No awards added yet. Create your first award using the form on the left.', 'journalist-portfolio-hub' ); ?></p>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render Submenu 5: Multimedia Page.
	 */
	public function render_multimedia_settings_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		wp_enqueue_media();

		// Handle Form Submissions (Add / Edit / Delete)
		if ( isset( $_POST['jp_multimedia_admin_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['jp_multimedia_admin_nonce'] ) ), 'jp_save_multimedia_admin' ) ) {
			if ( isset( $_POST['action'] ) && 'delete_media' === $_POST['action'] && isset( $_POST['media_id'] ) ) {
				$del_id = absint( $_POST['media_id'] );
				wp_delete_post( $del_id, true );
				echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Multimedia item deleted successfully.', 'journalist-portfolio-hub' ) . '</p></div>';
			} elseif ( isset( $_POST['media_title'] ) ) {
				$media_id    = isset( $_POST['media_id'] ) ? absint( $_POST['media_id'] ) : 0;
				$title       = sanitize_text_field( wp_unslash( $_POST['media_title'] ) );
				$type        = sanitize_text_field( wp_unslash( $_POST['media_type'] ?? 'Video' ) );
				$description = sanitize_textarea_field( wp_unslash( $_POST['media_description'] ?? '' ) );
				$tags        = sanitize_text_field( wp_unslash( $_POST['media_tags'] ?? '' ) );
				$youtube_url = esc_url_raw( wp_unslash( $_POST['media_youtube_url'] ?? '' ) );
				$thumbnail   = esc_url_raw( wp_unslash( $_POST['media_thumbnail'] ?? '' ) );

				if ( empty( $youtube_url ) ) {
					echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__( 'YouTube / Embed URL is a required field.', 'journalist-portfolio-hub' ) . '</p></div>';
				} else {
					$post_data = array(
						'post_title'  => $title,
						'post_status' => 'publish',
						'post_type'   => 'jp_multimedia',
					);

					if ( $media_id > 0 ) {
						$post_data['ID'] = $media_id;
						$post_id         = wp_update_post( $post_data );
					} else {
						$post_id = wp_insert_post( $post_data );
					}

					if ( $post_id && ! is_wp_error( $post_id ) ) {
						update_post_meta( $post_id, '_media_type', $type );
						update_post_meta( $post_id, '_media_description', $description );
						update_post_meta( $post_id, '_media_tags', $tags );
						update_post_meta( $post_id, '_media_youtube_url', $youtube_url );
						update_post_meta( $post_id, '_media_thumbnail', $thumbnail );

						echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Multimedia item saved successfully.', 'journalist-portfolio-hub' ) . '</p></div>';
					}
				}
			}
		}

		// Edit Mode Check
		$editing_media = null;
		if ( isset( $_GET['edit_media'] ) ) {
			$edit_id       = absint( $_GET['edit_media'] );
			$editing_media = get_post( $edit_id );
		}

		// Fetch All Multimedia Items
		$items = get_posts(
			array(
				'post_type'      => 'jp_multimedia',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => 'date',
				'order'          => 'DESC',
			)
		);
		?>
		<div class="wrap jp-admin-wrap">
			<div class="jp-admin-header" style="margin-bottom: 24px;">
				<h1><?php esc_html_e( 'Multimedia Management', 'journalist-portfolio-hub' ); ?></h1>
				<p><?php esc_html_e( 'Add and manage videos, podcasts, photo essays, and data visualizations.', 'journalist-portfolio-hub' ); ?></p>
			</div>

			<div style="display: grid; grid-template-columns: 380px 1fr; gap: 30px;">
				<!-- Add/Edit Form -->
				<div class="jp-admin-card" style="background: #fff; padding: 24px; border-radius: 8px; border: 1px solid #ccd0d4;">
					<h2><?php echo $editing_media ? esc_html__( 'Edit Multimedia Item', 'journalist-portfolio-hub' ) : esc_html__( 'Add New Multimedia Item', 'journalist-portfolio-hub' ); ?></h2>
					<form method="post">
						<?php wp_nonce_field( 'jp_save_multimedia_admin', 'jp_multimedia_admin_nonce' ); ?>
						<?php if ( $editing_media ) : ?>
							<input type="hidden" name="media_id" value="<?php echo esc_attr( $editing_media->ID ); ?>">
						<?php endif; ?>

						<p>
							<label for="media_title"><strong><?php esc_html_e( 'Media Title:', 'journalist-portfolio-hub' ); ?></strong></label><br>
							<input type="text" id="media_title" name="media_title" required class="widefat" value="<?php echo $editing_media ? esc_attr( $editing_media->post_title ) : ''; ?>" placeholder="e.g., Investigative Podcast Episode #1">
						</p>

						<p>
							<label for="media_type"><strong><?php esc_html_e( 'Media Type:', 'journalist-portfolio-hub' ); ?></strong></label><br>
							<?php $curr_type = $editing_media ? get_post_meta( $editing_media->ID, '_media_type', true ) : 'Video'; ?>
							<select id="media_type" name="media_type" class="widefat">
								<option value="Video" <?php selected( $curr_type, 'Video' ); ?>><?php esc_html_e( 'Video', 'journalist-portfolio-hub' ); ?></option>
								<option value="Podcast" <?php selected( $curr_type, 'Podcast' ); ?>><?php esc_html_e( 'Podcast', 'journalist-portfolio-hub' ); ?></option>
								<option value="Photo Essay" <?php selected( $curr_type, 'Photo Essay' ); ?>><?php esc_html_e( 'Photo Essay', 'journalist-portfolio-hub' ); ?></option>
								<option value="Data Visualization" <?php selected( $curr_type, 'Data Visualization' ); ?>><?php esc_html_e( 'Data Visualization', 'journalist-portfolio-hub' ); ?></option>
							</select>
						</p>

						<p>
							<label for="media_tags"><strong><?php esc_html_e( 'Tags (comma-separated):', 'journalist-portfolio-hub' ); ?></strong></label><br>
							<input type="text" id="media_tags" name="media_tags" class="widefat" value="<?php echo $editing_media ? esc_attr( get_post_meta( $editing_media->ID, '_media_tags', true ) ) : ''; ?>" placeholder="e.g., Climate, River Erosion">
						</p>

						<p>
							<label for="media_youtube_url"><strong><?php esc_html_e( 'YouTube / Embed URL:', 'journalist-portfolio-hub' ); ?> <span style="color: #e11d48;">*</span></strong></label><br>
							<input type="url" id="media_youtube_url" name="media_youtube_url" required class="widefat" value="<?php echo $editing_media ? esc_attr( get_post_meta( $editing_media->ID, '_media_youtube_url', true ) ) : ''; ?>" placeholder="https://www.youtube.com/watch?v=...">
						</p>

						<p>
							<label for="media_thumbnail"><strong><?php esc_html_e( 'Preview Poster Thumbnail:', 'journalist-portfolio-hub' ); ?></strong></label><br>
							<div style="display: flex; gap: 8px; margin-top: 4px;">
								<input type="text" id="media_thumbnail" name="media_thumbnail" class="widefat jp-media-url" value="<?php echo $editing_media ? esc_attr( get_post_meta( $editing_media->ID, '_media_thumbnail', true ) ) : ''; ?>" placeholder="https://...">
								<button type="button" class="button jp-upload-btn"><?php esc_html_e( 'Upload', 'journalist-portfolio-hub' ); ?></button>
							</div>
							<div class="jp-thumb-preview-box" style="margin-top: 8px;">
								<?php $curr_thumb = $editing_media ? get_post_meta( $editing_media->ID, '_media_thumbnail', true ) : ''; ?>
								<img class="jp-thumb-preview" src="<?php echo esc_url( $curr_thumb ); ?>" style="<?php echo empty( $curr_thumb ) ? 'display:none;' : ''; ?> max-width: 150px; max-height: 90px; border-radius: 4px; border: 1px solid #e2e8f0; object-fit: cover;" alt="Preview">
							</div>
						</p>

						<p>
							<label for="media_description"><strong><?php esc_html_e( 'Description:', 'journalist-portfolio-hub' ); ?></strong></label><br>
							<textarea id="media_description" name="media_description" rows="4" class="widefat" placeholder="Brief summary of this multimedia production..."><?php echo $editing_media ? esc_textarea( get_post_meta( $editing_media->ID, '_media_description', true ) ) : ''; ?></textarea>
						</p>

						<p style="margin-top: 20px; display: flex; gap: 10px;">
							<button type="submit" class="button button-primary" style="background: #059669; border-color: #059669; color: #fff;"><?php echo $editing_media ? esc_html__( 'Update Item', 'journalist-portfolio-hub' ) : esc_html__( 'Add Multimedia Item', 'journalist-portfolio-hub' ); ?></button>
							<?php if ( $editing_media ) : ?>
								<a href="<?php echo esc_url( admin_url( 'admin.php?page=jp-multimedia-settings' ) ); ?>" class="button button-secondary"><?php esc_html_e( 'Cancel Edit', 'journalist-portfolio-hub' ); ?></a>
							<?php endif; ?>
						</p>
					</form>
				</div>

				<!-- Items List Table -->
				<div class="jp-admin-card" style="background: #fff; padding: 24px; border-radius: 8px; border: 1px solid #ccd0d4;">
					<h2><?php esc_html_e( 'All Multimedia Productions', 'journalist-portfolio-hub' ); ?></h2>
					<?php if ( ! empty( $items ) ) : ?>
						<table class="widefat striped">
							<thead>
								<tr>
									<th style="width: 70px;"><?php esc_html_e( 'Poster', 'journalist-portfolio-hub' ); ?></th>
									<th><?php esc_html_e( 'Title', 'journalist-portfolio-hub' ); ?></th>
									<th><?php esc_html_e( 'Type', 'journalist-portfolio-hub' ); ?></th>
									<th><?php esc_html_e( 'Tags', 'journalist-portfolio-hub' ); ?></th>
									<th><?php esc_html_e( 'YouTube URL', 'journalist-portfolio-hub' ); ?></th>
									<th style="width: 120px; text-align: center;"><?php esc_html_e( 'Actions', 'journalist-portfolio-hub' ); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $items as $item ) :
									$type_val  = get_post_meta( $item->ID, '_media_type', true );
									$tags_val  = get_post_meta( $item->ID, '_media_tags', true );
									$yt_val    = get_post_meta( $item->ID, '_media_youtube_url', true );
									$thumb_val = get_post_meta( $item->ID, '_media_thumbnail', true );
									if ( empty( $thumb_val ) && has_post_thumbnail( $item->ID ) ) {
										$thumb_val = get_the_post_thumbnail_url( $item->ID, 'thumbnail' );
									}
									?>
									<tr>
										<td>
											<?php if ( ! empty( $thumb_val ) ) : ?>
												<img src="<?php echo esc_url( $thumb_val ); ?>" alt="" style="width: 60px; height: 35px; object-fit: cover; border-radius: 4px;">
											<?php else : ?>
												<div style="width: 60px; height: 35px; background: #e2e8f0; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: #94a3b8; font-size: 12px;">No img</div>
											<?php endif; ?>
										</td>
										<td><strong><?php echo esc_html( $item->post_title ); ?></strong></td>
										<td><span class="badge" style="background: #059669; color: #fff; padding: 2px 8px; border-radius: 12px; font-weight: 600; font-size: 0.8em;"><?php echo esc_html( $type_val ? $type_val : 'Video' ); ?></span></td>
										<td><?php echo esc_html( $tags_val ); ?></td>
										<td>
											<?php if ( ! empty( $yt_val ) ) : ?>
												<a href="<?php echo esc_url( $yt_val ); ?>" target="_blank" style="font-size: 0.85em; text-decoration: underline;"><?php echo esc_html( wp_trim_words( $yt_val, 3, '…' ) ); ?></a>
											<?php else : ?>
												<span style="color: #94a3b8;">—</span>
											<?php endif; ?>
										</td>
										<td style="text-align: center;">
											<a href="<?php echo esc_url( admin_url( 'admin.php?page=jp-multimedia-settings&edit_media=' . $item->ID ) ); ?>" class="button button-small"><?php esc_html_e( 'Edit', 'journalist-portfolio-hub' ); ?></a>
											<form method="post" style="display: inline-block;" onsubmit="return confirm('Delete this multimedia item?');">
												<?php wp_nonce_field( 'jp_save_multimedia_admin', 'jp_multimedia_admin_nonce' ); ?>
												<input type="hidden" name="action" value="delete_media">
												<input type="hidden" name="media_id" value="<?php echo esc_attr( $item->ID ); ?>">
												<button type="submit" class="button button-small button-link-delete"><?php esc_html_e( 'Delete', 'journalist-portfolio-hub' ); ?></button>
											</form>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					<?php else : ?>
						<p style="color: #64748b;"><?php esc_html_e( 'No multimedia items added yet. Create your first production using the form on the left.', 'journalist-portfolio-hub' ); ?></p>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Get Default Pre-populated Impact Stats.
	 *
	 * @return array Default 5 stat boxes.
	 */
	public static function get_default_impact_stats(): array {
		return array(
			array(
				'stat_number' => '7+',
				'stat_label'  => __( 'Years of Reporting Experience', 'journalist-portfolio-hub' ),
				'stat_icon'   => 'dashicons-calendar-alt',
				'stat_order'  => 1,
			),
			array(
				'stat_number' => '500+',
				'stat_label'  => __( 'Published Stories & Reports', 'journalist-portfolio-hub' ),
				'stat_icon'   => 'dashicons-document',
				'stat_order'  => 2,
			),
			array(
				'stat_number' => 'International',
				'stat_label'  => __( 'Publications in global media platforms', 'journalist-portfolio-hub' ),
				'stat_icon'   => 'dashicons-globe',
				'stat_order'  => 3,
			),
			array(
				'stat_number' => 'Multiple',
				'stat_label'  => __( 'Fellowships & Awards', 'journalist-portfolio-hub' ),
				'stat_icon'   => 'dashicons-awards',
				'stat_order'  => 4,
			),
			array(
				'stat_number' => 'Impacting',
				'stat_label'  => __( 'Policy, Awareness & Public Accountability', 'journalist-portfolio-hub' ),
				'stat_icon'   => 'dashicons-megaphone',
				'stat_order'  => 5,
			),
		);
	}

	/**
	 * Render Submenu 7: Impact Stats Page.
	 */
	public function render_impact_stats_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		wp_enqueue_media();

		// Handle Form Submission / Reset
		if ( isset( $_POST['jp_impact_stats_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['jp_impact_stats_nonce'] ) ), 'jp_save_impact_stats' ) ) {
			if ( isset( $_POST['action'] ) && 'reset_defaults' === $_POST['action'] ) {
				$defaults = self::get_default_impact_stats();
				update_option( 'jp_impact_stats_data', wp_json_encode( $defaults ) );
				echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Impact Stats reset to default values.', 'journalist-portfolio-hub' ) . '</p></div>';
			} elseif ( isset( $_POST['stats'] ) && is_array( $_POST['stats'] ) ) {
				$raw_stats   = wp_unslash( $_POST['stats'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				$clean_stats = array();

				foreach ( $raw_stats as $index => $item ) {
					$num   = sanitize_text_field( $item['number'] ?? '' );
					$lbl   = sanitize_text_field( $item['label'] ?? '' );
					$icon  = sanitize_text_field( $item['icon'] ?? '' );
					$order = isset( $item['order'] ) ? absint( $item['order'] ) : ( $index + 1 );

					if ( ! empty( $num ) || ! empty( $lbl ) ) {
						$clean_stats[] = array(
							'stat_number' => $num,
							'stat_label'  => $lbl,
							'stat_icon'   => $icon,
							'stat_order'  => $order,
						);
					}
				}

				// Sort by stat_order
				usort( $clean_stats, function( $a, $b ) {
					return ( $a['stat_order'] ?? 1 ) <=> ( $b['stat_order'] ?? 1 );
				});

				update_option( 'jp_impact_stats_data', wp_json_encode( $clean_stats ) );
				echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Impact Stats updated successfully.', 'journalist-portfolio-hub' ) . '</p></div>';
			}
		}

		// Fetch stats data or fall back to defaults
		$stats_json = get_option( 'jp_impact_stats_data', '' );
		if ( empty( $stats_json ) ) {
			$stats = self::get_default_impact_stats();
		} else {
			$stats = json_decode( $stats_json, true );
			if ( ! is_array( $stats ) || empty( $stats ) ) {
				$stats = self::get_default_impact_stats();
			}
		}

		// Ensure 5 boxes for editing
		while ( count( $stats ) < 5 ) {
			$stats[] = array(
				'stat_number' => '',
				'stat_label'  => '',
				'stat_icon'   => 'dashicons-chart-bar',
				'stat_order'  => count( $stats ) + 1,
			);
		}
		?>
		<div class="wrap jp-admin-wrap">
			<div class="jp-admin-header" style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center;">
				<div>
					<h1><?php esc_html_e( 'Impact Stats Counter Settings', 'journalist-portfolio-hub' ); ?></h1>
					<p><?php esc_html_e( 'Manage the 5 key reporting metric boxes displayed in the elevated impact counter bar on the homepage.', 'journalist-portfolio-hub' ); ?></p>
				</div>
				<form method="post" onsubmit="return confirm('<?php esc_js( esc_html_e( 'Reset all 5 stat boxes to default values?', 'journalist-portfolio-hub' ) ); ?>');">
					<?php wp_nonce_field( 'jp_save_impact_stats', 'jp_impact_stats_nonce' ); ?>
					<input type="hidden" name="action" value="reset_defaults">
					<button type="submit" class="button button-secondary"><?php esc_html_e( 'Reset Defaults', 'journalist-portfolio-hub' ); ?></button>
				</form>
			</div>

			<form method="post">
				<?php wp_nonce_field( 'jp_save_impact_stats', 'jp_impact_stats_nonce' ); ?>

				<div style="display: flex; flex-direction: column; gap: 20px;">
					<?php foreach ( array_slice( $stats, 0, 5 ) as $i => $stat ) : ?>
						<div class="jp-admin-card" style="background: #fff; padding: 20px 24px; border-radius: 10px; border: 1px solid #ccd0d4;">
							<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">
								<h3 style="margin: 0; color: #0f172a; font-size: 1.05rem;">
									<span style="background: #059669; color: #fff; width: 24px; height: 24px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 12px; margin-right: 8px; font-weight: 700;"><?php echo esc_html( $i + 1 ); ?></span>
									<?php printf( esc_html__( 'Stat Box %d', 'journalist-portfolio-hub' ), $i + 1 ); ?>
								</h3>
								<span style="font-size: 0.85em; color: #64748b; font-weight: 600;"><?php esc_html_e( 'Order Position:', 'journalist-portfolio-hub' ); ?> 
									<input type="number" name="stats[<?php echo esc_attr( $i ); ?>][order]" value="<?php echo esc_attr( $stat['stat_order'] ?? ( $i + 1 ) ); ?>" style="width: 60px; text-align: center;" min="1" max="5">
								</span>
							</div>

							<div style="display: grid; grid-template-columns: 180px 1fr 1fr; gap: 20px;">
								<div>
									<label for="stat_num_<?php echo esc_attr( $i ); ?>"><strong><?php esc_html_e( 'Metric Number / Value:', 'journalist-portfolio-hub' ); ?></strong></label>
									<input type="text" id="stat_num_<?php echo esc_attr( $i ); ?>" name="stats[<?php echo esc_attr( $i ); ?>][number]" value="<?php echo esc_attr( $stat['stat_number'] ?? '' ); ?>" class="widefat" placeholder="e.g. 7+, 500+, International">
								</div>

								<div>
									<label for="stat_lbl_<?php echo esc_attr( $i ); ?>"><strong><?php esc_html_e( 'Description Label:', 'journalist-portfolio-hub' ); ?></strong></label>
									<input type="text" id="stat_lbl_<?php echo esc_attr( $i ); ?>" name="stats[<?php echo esc_attr( $i ); ?>][label]" value="<?php echo esc_attr( $stat['stat_label'] ?? '' ); ?>" class="widefat" placeholder="e.g. Years of Reporting Experience">
								</div>

								<div>
									<label for="stat_icon_<?php echo esc_attr( $i ); ?>"><strong><?php esc_html_e( 'Icon (Dashicon / Image URL):', 'journalist-portfolio-hub' ); ?></strong></label>
									<div style="display: flex; gap: 6px; margin-top: 4px;">
										<input type="text" id="stat_icon_<?php echo esc_attr( $i ); ?>" name="stats[<?php echo esc_attr( $i ); ?>][icon]" value="<?php echo esc_attr( $stat['stat_icon'] ?? '' ); ?>" class="widefat jp-media-url" placeholder="dashicons-calendar-alt or https://...">
										<button type="button" class="button jp-upload-btn"><?php esc_html_e( 'Upload', 'journalist-portfolio-hub' ); ?></button>
									</div>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>

				<p style="margin-top: 24px;">
					<button type="submit" class="button button-primary" style="background: #059669; border-color: #059669; color: #fff; font-weight: 700; padding: 6px 24px; font-size: 1rem; height: auto;">
						<?php esc_html_e( 'Save Impact Stats Settings', 'journalist-portfolio-hub' ); ?>
					</button>
				</p>
			</form>
		</div>
		<?php
	}
}


