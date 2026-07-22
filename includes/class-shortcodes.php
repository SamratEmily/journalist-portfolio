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
		add_shortcode( 'jp_awards_carousel', array( $this, 'render_awards_carousel' ) );
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

	/**
	 * Render Awards & Fellowships Carousel Shortcode [jp_awards_carousel]
	 *
	 * @param array|string $atts Shortcode attributes.
	 * @return string HTML output.
	 */
	public function render_awards_carousel( $atts = array() ): string {
		$awards = get_posts(
			array(
				'post_type'      => 'jp_award',
				'post_status'    => 'publish',
				'posts_per_page' => 10,
				'orderby'        => 'date',
				'order'          => 'DESC',
			)
		);

		if ( empty( $awards ) ) {
			return '';
		}

		$slider_id = 'jp_awards_slider_' . wp_rand( 100, 999 );

		ob_start();
		?>
		<section class="jp-section jp-awards-section" style="padding-top: 40px; padding-bottom: 40px;">
			<div class="jp-container">
				<div class="jp-awards-header-row">
					<div class="jp-awards-title-wrap">
						<h2><?php esc_html_e( 'AWARDS & FELLOWSHIPS', 'journalist-portfolio-hub' ); ?></h2>
						<p><?php esc_html_e( 'Recognition, reporting grants, and international journalistic honors.', 'journalist-portfolio-hub' ); ?></p>
					</div>

					<div style="display: flex; align-items: center; gap: 20px;">
						<a href="<?php echo esc_url( home_url( '/awards' ) ); ?>" class="jp-read-more" style="font-size: 0.95rem; font-weight: 700;">
							<?php esc_html_e( 'View All', 'journalist-portfolio-hub' ); ?> &rarr;
						</a>

						<div class="jp-awards-nav-controls">
							<button type="button" class="jp-slider-nav-btn" aria-label="<?php esc_attr_e( 'Previous Award', 'journalist-portfolio-hub' ); ?>" onclick="document.getElementById('<?php echo esc_js( $slider_id ); ?>').scrollBy({ left: -340, behavior: 'smooth' });">
								&larr;
							</button>
							<button type="button" class="jp-slider-nav-btn" aria-label="<?php esc_attr_e( 'Next Award', 'journalist-portfolio-hub' ); ?>" onclick="document.getElementById('<?php echo esc_js( $slider_id ); ?>').scrollBy({ left: 340, behavior: 'smooth' });">
								&rarr;
							</button>
						</div>
					</div>
				</div>

				<div class="jp-awards-slider-container">
					<div id="<?php echo esc_attr( $slider_id ); ?>" class="jp-awards-slider-track">
						<?php foreach ( $awards as $award ) :
							$for      = get_post_meta( $award->ID, '_award_for', true );
							$year     = get_post_meta( $award->ID, '_award_year', true );
							$location = get_post_meta( $award->ID, '_award_location', true );
							$icon     = get_post_meta( $award->ID, '_award_icon', true );
							$desc     = get_post_meta( $award->ID, '_award_description', true );
							if ( empty( $icon ) ) {
								$icon = 'dashicons-awards';
							}
							?>
							<article class="jp-awards-slider-card">
								<div>
									<div class="jp-award-card-header">
										<div class="jp-award-icon-circle">
											<?php if ( str_starts_with( $icon, 'http' ) ) : ?>
												<img src="<?php echo esc_url( $icon ); ?>" alt="<?php echo esc_attr( $award->post_title ); ?>">
											<?php else : ?>
												<span class="dashicons <?php echo esc_attr( $icon ); ?>"></span>
											<?php endif; ?>
										</div>

										<?php if ( ! empty( $year ) ) : ?>
											<span class="jp-award-year-badge"><?php echo esc_html( $year ); ?></span>
										<?php endif; ?>
									</div>

									<h3 class="jp-award-title"><?php echo esc_html( $award->post_title ); ?></h3>

									<?php if ( ! empty( $for ) ) : ?>
										<span class="jp-award-subtitle"><?php echo esc_html( $for ); ?></span>
									<?php endif; ?>

									<?php if ( ! empty( $desc ) ) : ?>
										<p class="jp-award-desc"><?php echo esc_html( wp_trim_words( $desc, 18 ) ); ?></p>
									<?php endif; ?>
								</div>

								<?php if ( ! empty( $location ) ) : ?>
									<div class="jp-award-footer-meta">
										<span class="jp-award-location-tag">
											<span class="dashicons dashicons-location" style="font-size: 14px; width: 14px; height: 14px;"></span>
											<?php echo esc_html( $location ); ?>
										</span>
									</div>
								<?php endif; ?>
							</article>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</section>
		<?php
		return ob_get_clean();
	}
}
