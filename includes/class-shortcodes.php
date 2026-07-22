<?php
namespace JournalistPortfolio;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles Shortcodes for Downloadable Portfolio Assets (CV & Media Kit).
 */
class Shortcodes {

	/**
	 * Instance.
	 *
	 * @var Shortcodes|null
	 */
	private static $instance = null;

	/**
	 * Get Instance.
	 *
	 * @return Shortcodes
	 */
	public static function get_instance(): Shortcodes {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		add_shortcode( 'jp_cv_link', array( $this, 'render_cv_link' ) );
		add_shortcode( 'jp_media_kit_link', array( $this, 'render_media_kit_link' ) );
	}

	/**
	 * Render CV Download Shortcode [jp_cv_link class="footer-download-btn"]
	 *
	 * @param array|string $atts Shortcode attributes.
	 * @return string HTML output.
	 */
	public function render_cv_link( $atts ): string {
		$attributes = shortcode_atts(
			array(
				'class'  => 'jp-download-btn',
				'label'  => '',
				'target' => '_blank',
			),
			$atts,
			'jp_cv_link'
		);

		$file_url = get_option( 'jp_cv_file', '' );

		if ( empty( $file_url ) ) {
			return '';
		}

		$label = ! empty( $attributes['label'] ) ? $attributes['label'] : get_option( 'jp_cv_button_label', __( 'Download CV / Resume', 'journalist-portfolio-hub' ) );

		ob_start();
		?>
		<a href="<?php echo esc_url( $file_url ); ?>" class="<?php echo esc_attr( $attributes['class'] ); ?>" target="<?php echo esc_attr( $attributes['target'] ); ?>" rel="noopener noreferrer" download>
			<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="jp-download-icon">
				<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
				<polyline points="14 2 14 8 20 8"></polyline>
				<line x1="12" y1="18" x2="12" y2="12"></line>
				<polyline points="9 15 12 18 15 15"></polyline>
			</svg>
			<span><?php echo esc_html( $label ); ?></span>
		</a>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render Media Kit Download Shortcode [jp_media_kit_link class="footer-download-btn"]
	 *
	 * @param array|string $atts Shortcode attributes.
	 * @return string HTML output.
	 */
	public function render_media_kit_link( $atts ): string {
		$attributes = shortcode_atts(
			array(
				'class'  => 'jp-download-btn',
				'label'  => '',
				'target' => '_blank',
			),
			$atts,
			'jp_media_kit_link'
		);

		$file_url = get_option( 'jp_media_kit_file', '' );

		if ( empty( $file_url ) ) {
			return '';
		}

		$label = ! empty( $attributes['label'] ) ? $attributes['label'] : get_option( 'jp_media_kit_label', __( 'Download Media Kit', 'journalist-portfolio-hub' ) );

		ob_start();
		?>
		<a href="<?php echo esc_url( $file_url ); ?>" class="<?php echo esc_attr( $attributes['class'] ); ?>" target="<?php echo esc_attr( $attributes['target'] ); ?>" rel="noopener noreferrer" download>
			<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="jp-download-icon">
				<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
				<polyline points="7 10 12 15 17 10"></polyline>
				<line x1="12" y1="15" x2="12" y2="3"></line>
			</svg>
			<span><?php echo esc_html( $label ); ?></span>
		</a>
		<?php
		return ob_get_clean();
	}
}
