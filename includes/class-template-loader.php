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
		add_filter( 'wp_resource_hints', array( $this, 'add_resource_hints' ), 10, 2 );
		add_filter( 'pre_get_document_title', 'jp_get_browser_tab_title', 999 );
		add_filter( 'wp_title', 'jp_get_browser_tab_title', 999 );
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
	 * Add preconnect hints for the Google Fonts stylesheet and font files.
	 *
	 * @param array  $urls          Resource URLs for the given relation type.
	 * @param string $relation_type Relation type being fetched.
	 * @return array Filtered URLs.
	 */
	public function add_resource_hints( array $urls, string $relation_type ): array {
		if ( 'preconnect' === $relation_type && wp_style_is( 'google-fonts-inter', 'enqueued' ) ) {
			$urls[] = array(
				'href'        => 'https://fonts.gstatic.com',
				'crossorigin' => 'anonymous',
			);
		}

		return $urls;
	}

	/**
	 * Check whether the post being rendered uses any of the given shortcodes.
	 *
	 * @param array $shortcodes Shortcode tags to look for.
	 * @return bool True when at least one shortcode is present.
	 */
	private function current_post_has_shortcode( array $shortcodes ): bool {
		$post = get_post();

		if ( ! $post instanceof \WP_Post ) {
			return false;
		}

		foreach ( $shortcodes as $shortcode ) {
			if ( has_shortcode( $post->post_content, $shortcode ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Enqueue Frontend Styles and Scripts.
	 *
	 * Every stylesheet is registered, but only the ones the current view renders
	 * are enqueued — a visitor on the Stories page has no use for the contact
	 * form or impact stats CSS. Section stylesheets are also registered so that
	 * the shortcode callbacks can pull in whatever they need when a theme drops
	 * a section onto an arbitrary page.
	 */
	public function enqueue_frontend_assets(): void {
		$is_home    = is_front_page() || is_page( 'home' );
		$is_stories = is_search() || is_page( 'stories' ) || is_post_type_archive( 'story' ) || is_tax( 'story_category' );
		$is_contact = is_page( 'contact' ) || $this->current_post_has_shortcode( array( 'jp_contact_page' ) );
		$is_awards  = is_page( 'awards' ) || $this->current_post_has_shortcode( array( 'jp_awards_carousel' ) );
		$is_stats   = $this->current_post_has_shortcode( array( 'jp_impact_stats' ) );
		$is_photos  = is_page( 'photos' ) || $this->current_post_has_shortcode( array( 'jp_photos_section' ) );
		$is_videos  = is_page( 'videos' ) || is_page( 'multimedia' ) || $this->current_post_has_shortcode( array( 'jp_videos_section', 'jp_multimedia_section' ) );

		// Only the weights the stylesheets actually declare are requested.
		wp_enqueue_style( 'google-fonts-inter', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Merriweather:ital,wght@0,400;0,700;1,400&display=swap', array(), null );
		wp_enqueue_style( 'dashicons' );

		// Shared chrome: header, hero, cards and footer render on every view.
		wp_enqueue_style( 'jp-portfolio-style', JP_HUB_PLUGIN_URL . 'assets/css/portfolio-style.css', array(), JP_HUB_VERSION );
		wp_enqueue_style( 'jp-portfolio-footer-style', JP_HUB_PLUGIN_URL . 'assets/css/portfolio-footer.css', array(), JP_HUB_VERSION );

		wp_register_style( 'jp-portfolio-extra-style', JP_HUB_PLUGIN_URL . 'assets/css/portfolio-homepage-extra.css', array( 'jp-portfolio-style' ), JP_HUB_VERSION );
		wp_register_style( 'jp-portfolio-awards-style', JP_HUB_PLUGIN_URL . 'assets/css/portfolio-awards.css', array(), JP_HUB_VERSION );
		wp_register_style( 'jp-portfolio-stats-style', JP_HUB_PLUGIN_URL . 'assets/css/portfolio-stats.css', array( 'jp-portfolio-style' ), JP_HUB_VERSION );
		wp_register_style( 'jp-portfolio-contact-style', JP_HUB_PLUGIN_URL . 'assets/css/portfolio-contact.css', array( 'jp-portfolio-style' ), JP_HUB_VERSION );
		wp_register_style( 'jp-search-ajax-style', JP_HUB_PLUGIN_URL . 'assets/css/search-ajax.css', array( 'jp-portfolio-style' ), JP_HUB_VERSION );

		wp_register_script( 'jp-multimedia-modal', JP_HUB_PLUGIN_URL . 'assets/js/multimedia-modal.js', array(), JP_HUB_VERSION, true );
		wp_register_script( 'jp-photo-modal', JP_HUB_PLUGIN_URL . 'assets/js/photo-modal.js', array(), JP_HUB_VERSION, true );
		wp_register_script( 'jp-search-ajax', JP_HUB_PLUGIN_URL . 'assets/js/search-ajax.js', array(), JP_HUB_VERSION, true );

		// Photo/video grids, the about-brief block, both lightboxes and pagination.
		if ( $is_home || $is_photos || $is_videos || $is_stories || $is_awards ) {
			wp_enqueue_style( 'jp-portfolio-extra-style' );
		}

		if ( $is_home || $is_awards ) {
			wp_enqueue_style( 'jp-portfolio-awards-style' );
		}

		if ( $is_home || $is_stats ) {
			wp_enqueue_style( 'jp-portfolio-stats-style' );
		}

		if ( $is_contact ) {
			wp_enqueue_style( 'jp-portfolio-contact-style' );
		}

		if ( $is_home || $is_photos ) {
			wp_enqueue_script( 'jp-photo-modal' );
		}

		if ( $is_home || $is_videos ) {
			wp_enqueue_script( 'jp-multimedia-modal' );
		}

		// Live search only exists on the Stories and Search templates.
		if ( $is_stories ) {
			wp_enqueue_style( 'jp-search-ajax-style' );
			wp_enqueue_script( 'jp-search-ajax' );
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
}
