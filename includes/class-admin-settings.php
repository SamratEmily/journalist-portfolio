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

		// Submenu 3: Footer Settings
		add_submenu_page(
			'journalist-portfolio-settings',
			__( 'Footer Settings', 'journalist-portfolio-hub' ),
			__( 'Footer Settings', 'journalist-portfolio-hub' ),
			'manage_options',
			'jp-footer-settings',
			array( $this, 'render_footer_settings_page' )
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
		if ( false === strpos( $hook, 'journalist-portfolio' ) && false === strpos( $hook, 'jp-social-links' ) && false === strpos( $hook, 'jp-footer-settings' ) ) {
			return;
		}

		wp_enqueue_media();
		wp_enqueue_style( 'jp-admin-style', JP_HUB_PLUGIN_URL . 'assets/css/admin-style.css', array(), JP_HUB_VERSION );
		wp_enqueue_script( 'jp-admin-media', JP_HUB_PLUGIN_URL . 'assets/js/admin-media.js', array( 'jquery' ), JP_HUB_VERSION, true );
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

					<div class="jp-field-group">
						<label for="jp_home_objective"><?php esc_html_e( 'Hero Objective / Mission Statement:', 'journalist-portfolio-hub' ); ?></label>
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
}
