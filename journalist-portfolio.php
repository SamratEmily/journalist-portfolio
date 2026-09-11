<?php
/**
 * Plugin Name:       Journalist Portfolio Hub
 * Plugin URI:        https://example.com/journalist-portfolio-hub
 * Description:       A complete, standalone journalist portfolio plugin with CPT Stories, downloadable portfolio assets (CV & Media Kit), admin-controlled 4-column footer, customizable profile settings, shortcodes, and frontend templates.
 * Version:           1.3.0
 * Author:            MD. Samrat Hossen
 * Author URI:        https://samrat-personal-portfolio.netlify.app/
 * Text Domain:       journalist-portfolio-hub
 * Domain Path:       /languages
 * Requires at least: 6.0
 * Requires PHP:      8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Define Plugin Constants.
define( 'JP_HUB_VERSION', '1.3.0' );
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

if ( ! function_exists( 'jp_get_browser_tab_title' ) ) {
	/**
	 * Get dynamic browser tab / document title.
	 * Rules:
	 * - Home page: "[Full Name]"
	 * - Every other page: "[Full Name] | [Page Title]"
	 *
	 * @param string $default_title Optional incoming title from filters.
	 * @return string Formatted page title.
	 */
	function jp_get_browser_tab_title( string $default_title = '' ): string {
		$full_name = trim( (string) get_option( 'jp_full_name', '' ) );
		if ( empty( $full_name ) ) {
			$full_name = get_bloginfo( 'name' );
		}

		// 1. Home Page: return only Full Name.
		if ( is_front_page() || is_page( 'home' ) || ( is_home() && ! is_paged() ) ) {
			return $full_name;
		}

		// 2. Determine Page Title for all other pages.
		$page_title = '';

		if ( is_search() ) {
			$search_query = get_search_query();
			/* translators: %s: Search keyword */
			$page_title = ! empty( $search_query )
				? sprintf( __( 'Search Results for "%s"', 'journalist-portfolio-hub' ), $search_query )
				: __( 'Search Results', 'journalist-portfolio-hub' );
		} elseif ( is_404() ) {
			$page_title = __( 'Page Not Found', 'journalist-portfolio-hub' );
		} elseif ( is_singular( 'story' ) ) {
			$page_title = single_post_title( '', false );
		} elseif ( is_singular() ) {
			$page_title = single_post_title( '', false );
		} elseif ( is_page() ) {
			$page_title = single_post_title( '', false );
		} elseif ( is_post_type_archive( 'story' ) ) {
			$page_title = __( 'Stories', 'journalist-portfolio-hub' );
		} elseif ( is_post_type_archive() ) {
			$page_title = post_type_archive_title( '', false );
		} elseif ( is_tax() || is_category() || is_tag() ) {
			$page_title = single_term_title( '', false );
		} elseif ( is_author() ) {
			$author     = get_queried_object();
			$page_title = ( $author && isset( $author->display_name ) ) ? $author->display_name : __( 'Author', 'journalist-portfolio-hub' );
		} elseif ( is_archive() ) {
			$page_title = __( 'Archive', 'journalist-portfolio-hub' );
		}

		// Fallback if empty: inspect queried object.
		if ( empty( $page_title ) ) {
			$queried = get_queried_object();
			if ( $queried instanceof \WP_Post ) {
				$page_title = $queried->post_title;
			} elseif ( $queried instanceof \WP_Term ) {
				$page_title = $queried->name;
			} elseif ( $queried instanceof \WP_Post_Type ) {
				$page_title = $queried->labels->name;
			}
		}

		// Fallback to incoming default title if available.
		if ( empty( $page_title ) && ! empty( $default_title ) ) {
			$page_title = trim( str_replace( array( '|', '-', '–', '—' ), '', $default_title ) );
		}

		$page_title = trim( wp_strip_all_tags( $page_title ) );

		if ( ! empty( $page_title ) ) {
			return $full_name . ' | ' . $page_title;
		}

		return $full_name;
	}
}


