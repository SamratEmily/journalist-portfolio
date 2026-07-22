<?php
namespace JournalistPortfolio;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Footer Manager Class.
 * Handles shortcode [portfolio_footer] and rendering helper.
 */
class Footer_Manager {

	/**
	 * Instance.
	 *
	 * @var Footer_Manager|null
	 */
	private static $instance = null;

	/**
	 * Get Instance.
	 *
	 * @return Footer_Manager
	 */
	public static function get_instance(): Footer_Manager {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		add_shortcode( 'portfolio_footer', array( $this, 'render_footer_shortcode' ) );
	}

	/**
	 * Render Footer Shortcode [portfolio_footer]
	 *
	 * @return string HTML output.
	 */
	public function render_footer_shortcode(): string {
		ob_start();
		$template_path = JP_HUB_PLUGIN_DIR . 'templates/footer-portfolio.php';
		if ( file_exists( $template_path ) ) {
			require $template_path;
		}
		return ob_get_clean();
	}
}
