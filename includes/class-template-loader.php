<?php
namespace JournalistPortfolio;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles template routing via template_include filter and enqueues frontend styles.
 */
class Template_Loader {

	/**
	 * Instance.
	 *
	 * @var Template_Loader|null
	 */
	private static $instance = null;

	/**
	 * Get Instance.
	 *
	 * @return Template_Loader
	 */
	public static function get_instance(): Template_Loader {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		add_filter( 'template_include', array( $this, 'route_templates' ), 99 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );
	}

	/**
	 * Intercept and route template files.
	 *
	 * @param string $template Current template file path.
	 * @return string Modified template file path.
	 */
	public function route_templates( string $template ): string {
		if ( is_admin() ) {
			return $template;
		}

		// Home Page Routing.
		if ( is_front_page() || is_page( 'home' ) ) {
			$home_template = JP_HUB_PLUGIN_DIR . 'templates/page-home.php';
			if ( file_exists( $home_template ) ) {
				return $home_template;
			}
		}

		// About Page Routing.
		if ( is_page( 'about' ) ) {
			$about_template = JP_HUB_PLUGIN_DIR . 'templates/page-about.php';
			if ( file_exists( $about_template ) ) {
				return $about_template;
			}
		}

		// Awards Page Routing.
		if ( is_page( 'awards' ) ) {
			$awards_template = JP_HUB_PLUGIN_DIR . 'templates/page-awards.php';
			if ( file_exists( $awards_template ) ) {
				return $awards_template;
			}
		}

		// Stories Archive & Search Routing.
		if ( is_search() || isset( $_GET['s'] ) || is_page( 'stories' ) || is_post_type_archive( 'story' ) || is_tax( 'story_category' ) ) {
			$stories_template = JP_HUB_PLUGIN_DIR . 'templates/page-stories.php';
			if ( file_exists( $stories_template ) ) {
				return $stories_template;
			}
		}

		// Single Story Routing.
		if ( is_singular( 'story' ) ) {
			$single_template = JP_HUB_PLUGIN_DIR . 'templates/single-story.php';
			if ( file_exists( $single_template ) ) {
				return $single_template;
			}
		}

		// Contact Page Routing.
		if ( is_page( 'contact' ) ) {
			$contact_template = JP_HUB_PLUGIN_DIR . 'templates/page-contact.php';
			if ( file_exists( $contact_template ) ) {
				return $contact_template;
			}
		}

		// Photos Page Routing.
		if ( is_page( 'photos' ) ) {
			$photos_template = JP_HUB_PLUGIN_DIR . 'templates/page-photos.php';
			if ( file_exists( $photos_template ) ) {
				return $photos_template;
			}
		}

		// Videos & Multimedia Page Routing.
		if ( is_page( 'videos' ) || is_page( 'multimedia' ) ) {
			$videos_template = JP_HUB_PLUGIN_DIR . 'templates/page-videos.php';
			if ( file_exists( $videos_template ) ) {
				return $videos_template;
			}
			$multimedia_template = JP_HUB_PLUGIN_DIR . 'templates/page-multimedia.php';
			if ( file_exists( $multimedia_template ) ) {
				return $multimedia_template;
			}
		}

		return $template;
	}

	/**
	 * Enqueue Frontend Styles and Scripts.
	 */
	public function enqueue_frontend_assets(): void {
		wp_enqueue_style( 'google-fonts-inter', 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Merriweather:ital,wght@0,400;0,700;1,300&display=swap', array(), null );
		wp_enqueue_style( 'dashicons' );
		wp_enqueue_style( 'jp-portfolio-style', JP_HUB_PLUGIN_URL . 'assets/css/portfolio-style.css', array(), JP_HUB_VERSION );
		wp_enqueue_style( 'jp-portfolio-footer-style', JP_HUB_PLUGIN_URL . 'assets/css/portfolio-footer.css', array(), JP_HUB_VERSION );
		wp_enqueue_style( 'jp-portfolio-awards-style', JP_HUB_PLUGIN_URL . 'assets/css/portfolio-awards.css', array(), JP_HUB_VERSION );
		wp_enqueue_style( 'jp-portfolio-contact-style', JP_HUB_PLUGIN_URL . 'assets/css/portfolio-contact.css', array( 'jp-portfolio-style' ), JP_HUB_VERSION );
		wp_enqueue_style( 'jp-portfolio-extra-style', JP_HUB_PLUGIN_URL . 'assets/css/portfolio-homepage-extra.css', array( 'jp-portfolio-style' ), JP_HUB_VERSION );
		wp_enqueue_style( 'jp-portfolio-stats-style', JP_HUB_PLUGIN_URL . 'assets/css/portfolio-stats.css', array( 'jp-portfolio-style' ), JP_HUB_VERSION );
		wp_enqueue_style( 'jp-search-ajax-style', JP_HUB_PLUGIN_URL . 'assets/css/search-ajax.css', array( 'jp-portfolio-style' ), JP_HUB_VERSION );

		wp_enqueue_script( 'jp-multimedia-modal', JP_HUB_PLUGIN_URL . 'assets/js/multimedia-modal.js', array(), JP_HUB_VERSION, true );
		wp_enqueue_script( 'jp-photo-modal', JP_HUB_PLUGIN_URL . 'assets/js/photo-modal.js', array(), JP_HUB_VERSION, true );

		// Enqueue Live AJAX Search Script.
		wp_enqueue_script( 'jp-search-ajax', JP_HUB_PLUGIN_URL . 'assets/js/search-ajax.js', array(), JP_HUB_VERSION, true );
		wp_localize_script(
			'jp-search-ajax',
			'jpSearchVars',
			array(
				'ajax_url'   => admin_url( 'admin-ajax.php' ),
				'nonce'      => wp_create_nonce( 'jp_search_nonce' ),
				'search_url' => home_url( '/' ),
				'i18n'       => array(
					'searching'       => __( 'Searching stories...', 'journalist-portfolio-hub' ),
					'no_results'      => __( 'No stories found matching your query.', 'journalist-portfolio-hub' ),
					'view_all'        => __( 'View all %d story results', 'journalist-portfolio-hub' ),
					'press_esc'       => __( 'Press Esc to close', 'journalist-portfolio-hub' ),
				),
			)
		);
	}
}
