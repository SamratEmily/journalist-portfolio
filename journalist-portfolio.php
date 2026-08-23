<?php
/**
 * Plugin Name:       Journalist Portfolio Hub
 * Plugin URI:        https://example.com/journalist-portfolio-hub
 * Description:       A complete, standalone journalist portfolio plugin with CPT Stories, downloadable portfolio assets (CV & Media Kit), admin-controlled 4-column footer, customizable profile settings, shortcodes, and frontend templates.
 * Version:           1.2.1
 * Author:            Senior WordPress Developer
 * Text Domain:       journalist-portfolio-hub
 * Domain Path:       /languages
 * Requires at least: 6.0
 * Requires PHP:      8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Define Plugin Constants.
define( 'JP_HUB_VERSION', '1.2.1' );
define( 'JP_HUB_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'JP_HUB_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'JP_HUB_PLUGIN_FILE', __FILE__ );

// Load Required Classes.
require_once JP_HUB_PLUGIN_DIR . 'includes/class-activator.php';
require_once JP_HUB_PLUGIN_DIR . 'includes/class-cpt-stories.php';
require_once JP_HUB_PLUGIN_DIR . 'includes/class-cpt-awards.php';
require_once JP_HUB_PLUGIN_DIR . 'includes/class-cpt-multimedia.php';
require_once JP_HUB_PLUGIN_DIR . 'includes/class-cpt-photos.php';
require_once JP_HUB_PLUGIN_DIR . 'includes/class-admin-settings.php';
require_once JP_HUB_PLUGIN_DIR . 'includes/class-template-loader.php';
require_once JP_HUB_PLUGIN_DIR . 'includes/class-shortcodes.php';
require_once JP_HUB_PLUGIN_DIR . 'includes/class-footer-manager.php';
require_once JP_HUB_PLUGIN_DIR . 'includes/class-demo-seeder.php';
require_once JP_HUB_PLUGIN_DIR . 'includes/class-contact-handler.php';
require_once JP_HUB_PLUGIN_DIR . 'includes/class-search-handler.php';

// Register Activation & Deactivation Hooks.
register_activation_hook( __FILE__, array( 'JournalistPortfolio\Activator', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'JournalistPortfolio\Activator', 'deactivate' ) );

/**
 * Main Journalist Portfolio Hub Class.
 */
final class Journalist_Portfolio_Hub {

	/**
	 * Instance of this class.
	 *
	 * @var Journalist_Portfolio_Hub|null
	 */
	private static $instance = null;

	/**
	 * Get class instance.
	 *
	 * @return Journalist_Portfolio_Hub
	 */
	public static function get_instance(): Journalist_Portfolio_Hub {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->init_components();
	}

	/**
	 * Initialize plugin components.
	 */
	private function init_components(): void {
		// Initialize Custom Post Types.
		\JournalistPortfolio\CPT_Stories::get_instance();
		\JournalistPortfolio\CPT_Awards::get_instance();
		\JournalistPortfolio\CPT_Multimedia::get_instance();
		\JournalistPortfolio\CPT_Photos::get_instance();

		// Initialize Admin Settings.
		if ( is_admin() ) {
			\JournalistPortfolio\Admin_Settings::get_instance();
		}

		// Initialize Template Loader.
		\JournalistPortfolio\Template_Loader::get_instance();

		// Initialize Shortcodes.
		\JournalistPortfolio\Shortcodes::get_instance();

		// Initialize Footer Manager.
		\JournalistPortfolio\Footer_Manager::get_instance();

		// Initialize Contact Handler (shortcode + AJAX).
		\JournalistPortfolio\Contact_Handler::get_instance();

		// Initialize Search Handler.
		\JournalistPortfolio\Search_Handler::get_instance();
	}
}

/**
 * Initialize Plugin.
 */
function run_journalist_portfolio_hub(): Journalist_Portfolio_Hub {
	return Journalist_Portfolio_Hub::get_instance();
}

add_action( 'plugins_loaded', 'run_journalist_portfolio_hub' );

/* ==========================================================================
   GLOBAL HELPER FUNCTIONS FOR THEMES & TEMPLATES
   ========================================================================== */

if ( ! function_exists( 'jp_get_cv_url' ) ) {
	/**
	 * Get the raw URL string of the uploaded CV / Resume.
	 *
	 * @return string CV file URL.
	 */
	function jp_get_cv_url(): string {
		return esc_url( get_option( 'jp_footer_cv_file', get_option( 'jp_cv_file', '' ) ) );
	}
}

if ( ! function_exists( 'jp_get_media_kit_url' ) ) {
	/**
	 * Get the raw URL string of the uploaded Media Kit.
	 *
	 * @return string Media Kit file URL.
	 */
	function jp_get_media_kit_url(): string {
		return esc_url( get_option( 'jp_footer_mediakit_file', get_option( 'jp_media_kit_file', '' ) ) );
	}
}

if ( ! function_exists( 'jp_render_footer' ) ) {
	/**
	 * Echo the 4-Column Admin Controlled Footer HTML output directly in PHP templates.
	 */
	function jp_render_footer(): void {
		$template_path = JP_HUB_PLUGIN_DIR . 'templates/footer-portfolio.php';
		if ( file_exists( $template_path ) ) {
			require $template_path;
		}
	}
}

if ( ! function_exists( 'jp_render_photos' ) ) {
	/**
	 * Echo the Photos Homepage Section HTML output directly in PHP templates.
	 */
	function jp_render_photos(): void {
		echo do_shortcode( '[jp_photos_section]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

if ( ! function_exists( 'jp_render_videos' ) ) {
	/**
	 * Echo the Videos Homepage Section HTML output directly in PHP templates.
	 */
	function jp_render_videos(): void {
		echo do_shortcode( '[jp_videos_section]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

if ( ! function_exists( 'jp_render_multimedia' ) ) {
	/**
	 * Echo the Multimedia / Videos Homepage Section HTML output directly in PHP templates.
	 */
	function jp_render_multimedia(): void {
		echo do_shortcode( '[jp_videos_section]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

if ( ! function_exists( 'jp_render_about_brief' ) ) {
	/**
	 * Echo the Brief About Me Homepage Section HTML output directly in PHP templates.
	 */
	function jp_render_about_brief(): void {
		echo do_shortcode( '[jp_about_brief_section]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

if ( ! function_exists( 'jp_render_impact_stats' ) ) {
	/**
	 * Echo the Impact Stats Counter Bar HTML output directly in PHP templates.
	 */
	function jp_render_impact_stats(): void {
		echo do_shortcode( '[jp_impact_stats]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

