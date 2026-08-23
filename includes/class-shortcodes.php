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
		add_shortcode( 'jp_photos_section', array( $this, 'render_photos_section' ) );
		add_shortcode( 'jp_videos_section', array( $this, 'render_multimedia_section' ) );
		add_shortcode( 'jp_multimedia_section', array( $this, 'render_multimedia_section' ) );
		add_shortcode( 'jp_about_brief_section', array( $this, 'render_about_brief_section' ) );
		add_shortcode( 'jp_impact_stats', array( $this, 'render_impact_stats' ) );
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

	/**
	 * Render Photos Homepage Section Shortcode [jp_photos_section]
	 *
	 * @param array|string $atts Shortcode attributes.
	 * @return string HTML output.
	 */
	public function render_photos_section( $atts = array() ): string {
		$items = get_posts(
			array(
				'post_type'      => 'jp_photo',
				'post_status'    => 'publish',
				'posts_per_page' => 3,
				'orderby'        => 'date',
				'order'          => 'DESC',
			)
		);

		if ( empty( $items ) ) {
			return '';
		}

		ob_start();
		?>
		<section class="jp-section jp-photos-section" style="padding-top: 50px; padding-bottom: 50px; background: #f8fafc;">
			<div class="jp-container">
				<div class="jp-photos-header-row">
					<div class="jp-photos-title-wrap">
						<span class="jp-section-kicker" style="font-size: 0.8rem; font-weight: 700; color: #059669; letter-spacing: 0.08em; text-transform: uppercase; display: block; margin-bottom: 4px;"><?php esc_html_e( 'PHOTOJOURNALISM & VISUAL DISPATCHES', 'journalist-portfolio-hub' ); ?></span>
						<h2 style="font-size: 1.75rem; font-weight: 800; color: #0f172a; margin: 0; letter-spacing: -0.02em;"><?php esc_html_e( 'PHOTOS', 'journalist-portfolio-hub' ); ?></h2>
					</div>
					<a href="<?php echo esc_url( home_url( '/photos' ) ); ?>" class="jp-read-more" style="font-size: 0.95rem; font-weight: 700;">
						<?php esc_html_e( 'Explore All Photos', 'journalist-portfolio-hub' ); ?> &rarr;
					</a>
				</div>

				<div class="jp-photos-grid">
					<?php foreach ( $items as $item ) :
						$tags      = get_post_meta( $item->ID, '_photo_tags', true );
						$photo_url = get_post_meta( $item->ID, '_photo_image', true );
						$desc      = get_post_meta( $item->ID, '_photo_description', true );
						$pub_url   = get_post_meta( $item->ID, '_photo_published_url', true );

						if ( empty( $photo_url ) && has_post_thumbnail( $item->ID ) ) {
							$photo_url = get_the_post_thumbnail_url( $item->ID, 'large' );
						}
						if ( empty( $photo_url ) ) {
							$photo_url = 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80';
						}
						?>
						<article class="jp-photo-card" data-photo-url="<?php echo esc_url( $photo_url ); ?>" data-title="<?php echo esc_attr( $item->post_title ); ?>" data-desc="<?php echo esc_attr( $desc ); ?>" data-tags="<?php echo esc_attr( $tags ); ?>" data-published-url="<?php echo esc_url( $pub_url ); ?>">
							<div class="jp-photo-thumb-wrap">
								<img src="<?php echo esc_url( $photo_url ); ?>" alt="<?php echo esc_attr( $item->post_title ); ?>" class="jp-photo-thumb">
								<div class="jp-photo-overlay">
									<span class="jp-photo-view-btn" aria-label="<?php esc_attr_e( 'View Photo', 'journalist-portfolio-hub' ); ?>">
										<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
									</span>
								</div>
							</div>
							<div class="jp-photo-content">
								<?php if ( ! empty( $tags ) ) : ?>
									<div class="jp-photo-tags"><?php echo esc_html( $tags ); ?></div>
								<?php endif; ?>
								<h3 class="jp-photo-title">
									<a href="<?php echo esc_url( home_url( '/photos' ) ); ?>" class="jp-photo-link">
										<?php echo esc_html( $item->post_title ); ?>
									</a>
								</h3>
								<?php if ( ! empty( $desc ) ) : ?>
									<p class="jp-photo-desc"><?php echo esc_html( wp_trim_words( $desc, 14 ) ); ?></p>
								<?php endif; ?>

								<?php if ( ! empty( $pub_url ) ) : ?>
									<div style="margin-top: 14px;">
										<a href="<?php echo esc_url( $pub_url ); ?>" target="_blank" rel="noopener noreferrer" class="jp-photo-pub-btn" onclick="event.stopPropagation();">
											<span><?php esc_html_e( 'View Publication', 'journalist-portfolio-hub' ); ?></span>
											<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
										</a>
									</div>
								<?php endif; ?>
							</div>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render Multimedia Homepage Section Shortcode [jp_multimedia_section]
	 *
	 * @param array|string $atts Shortcode attributes.
	 * @return string HTML output.
	 */
	public function render_multimedia_section( $atts = array() ): string {
		$items = get_posts(
			array(
				'post_type'      => 'jp_multimedia',
				'post_status'    => 'publish',
				'posts_per_page' => 4,
				'orderby'        => 'date',
				'order'          => 'DESC',
			)
		);

		if ( empty( $items ) ) {
			return '';
		}

		ob_start();
		?>
		<section class="jp-section jp-multimedia-section" style="padding-top: 50px; padding-bottom: 50px; background: #ffffff;">
			<div class="jp-container">
				<div class="jp-multimedia-header-row">
					<div class="jp-multimedia-title-wrap">
						<span class="jp-section-kicker" style="font-size: 0.8rem; font-weight: 700; color: #059669; letter-spacing: 0.08em; text-transform: uppercase; display: block; margin-bottom: 4px;"><?php esc_html_e( 'DOCUMENTARY & AUDIO REPORTING', 'journalist-portfolio-hub' ); ?></span>
						<h2 style="font-size: 1.75rem; font-weight: 800; color: #0f172a; margin: 0; letter-spacing: -0.02em;"><?php esc_html_e( 'VIDEOS', 'journalist-portfolio-hub' ); ?></h2>
					</div>
					<a href="<?php echo esc_url( home_url( '/videos' ) ); ?>" class="jp-read-more" style="font-size: 0.95rem; font-weight: 700;">
						<?php esc_html_e( 'Explore All Videos', 'journalist-portfolio-hub' ); ?> &rarr;
					</a>
				</div>

				<div class="jp-multimedia-grid">
					<?php foreach ( $items as $item ) :
						$type        = get_post_meta( $item->ID, '_media_type', true );
						$tags        = get_post_meta( $item->ID, '_media_tags', true );
						$youtube_url = get_post_meta( $item->ID, '_media_youtube_url', true );
						$thumbnail   = get_post_meta( $item->ID, '_media_thumbnail', true );
						$desc        = get_post_meta( $item->ID, '_media_description', true );

						if ( empty( $thumbnail ) && has_post_thumbnail( $item->ID ) ) {
							$thumbnail = get_the_post_thumbnail_url( $item->ID, 'large' );
						}
						if ( empty( $thumbnail ) ) {
							$thumbnail = 'https://images.unsplash.com/photo-1594909122845-11baa439b7bf?auto=format&fit=crop&w=800&q=80';
						}
						if ( empty( $type ) ) {
							$type = 'Video';
						}
						?>
						<article class="jp-multimedia-card" data-video-url="<?php echo esc_url( $youtube_url ); ?>" data-title="<?php echo esc_attr( $item->post_title ); ?>">
							<div class="jp-multimedia-thumb-wrap">
								<img src="<?php echo esc_url( $thumbnail ); ?>" alt="<?php echo esc_attr( $item->post_title ); ?>" class="jp-multimedia-thumb">
								<div class="jp-multimedia-overlay">
									<span class="jp-multimedia-play-btn" aria-label="<?php esc_attr_e( 'Play Media', 'journalist-portfolio-hub' ); ?>">
										<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
									</span>
								</div>
								<span class="jp-multimedia-badge"><?php echo esc_html( $type ); ?></span>
							</div>
							<div class="jp-multimedia-content">
								<?php if ( ! empty( $tags ) ) : ?>
									<div class="jp-multimedia-tags"><?php echo esc_html( $tags ); ?></div>
								<?php endif; ?>
								<h3 class="jp-multimedia-title">
									<a href="<?php echo ! empty( $youtube_url ) ? esc_url( $youtube_url ) : '#'; ?>" class="jp-multimedia-link" <?php echo ! empty( $youtube_url ) ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>>
										<?php echo esc_html( $item->post_title ); ?>
									</a>
								</h3>
								<?php if ( ! empty( $desc ) ) : ?>
									<p class="jp-multimedia-desc"><?php echo esc_html( wp_trim_words( $desc, 14 ) ); ?></p>
								<?php endif; ?>
							</div>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render About Me Brief Section Shortcode [jp_about_brief_section]
	 *
	 * @param array|string $atts Shortcode attributes.
	 * @return string HTML output.
	 */
	public function render_about_brief_section( $atts = array() ): string {
		$full_name     = get_option( 'jp_full_name', get_bloginfo( 'name' ) );
		$designation   = get_option( 'jp_designation', 'Investigative & Environmental Journalist' );
		$bio_text      = get_option( 'jp_bio_text', 'Dedicated to uncovering in-depth human stories, global climate challenges, and investigative truth through rigorous reporting and human-centered storytelling.' );
		$profile_image = get_option( 'jp_profile_image', '' );
		$about_page    = get_page_by_path( 'about' );
		$about_url     = $about_page ? get_permalink( $about_page->ID ) : home_url( '/about' );

		$excerpt_text = wp_trim_words( $bio_text, 48, '...' );

		ob_start();
		?>
		<section class="jp-section jp-about-brief-section" style="padding-top: 60px; padding-bottom: 70px; background: #F9F9FB;">
			<div class="jp-container">
				<div class="jp-about-brief-grid">
					<!-- Left Column: Profile Frame -->
					<div class="jp-about-brief-card-frame">
						<?php if ( ! empty( $profile_image ) ) : ?>
							<img src="<?php echo esc_url( $profile_image ); ?>" alt="<?php echo esc_attr( $full_name ); ?>" class="jp-about-brief-img">
						<?php else : ?>
							<div class="jp-about-brief-avatar-placeholder">
								<span class="dashicons dashicons-admin-users" style="font-size: 72px; width: 72px; height: 72px; color: #94a3b8;"></span>
							</div>
						<?php endif; ?>
					</div>

					<!-- Right Column: Details & CTA -->
					<div class="jp-about-brief-content">
						<span class="jp-about-brief-kicker"><?php esc_html_e( 'ABOUT ME', 'journalist-portfolio-hub' ); ?></span>
						<h2 class="jp-about-brief-name"><?php echo esc_html( $full_name ); ?></h2>
						<div class="jp-about-brief-designation"><?php echo esc_html( $designation ); ?></div>

						<p class="jp-about-brief-excerpt">
							<?php echo esc_html( $excerpt_text ); ?>
						</p>

						<a href="<?php echo esc_url( $about_url ); ?>" class="jp-about-brief-btn">
							<span><?php esc_html_e( 'Read Full Biography', 'journalist-portfolio-hub' ); ?> &rarr;</span>
						</a>
					</div>
				</div>
			</div>
		</section>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render Impact Stats Counter Bar [jp_impact_stats]
	 *
	 * @param array|string $atts Shortcode attributes.
	 * @return string HTML output.
	 */
	public function render_impact_stats( $atts = array() ): string {
		$stats_json = get_option( 'jp_impact_stats_data', '' );
		if ( empty( $stats_json ) ) {
			$stats = Admin_Settings::get_default_impact_stats();
		} else {
			$stats = json_decode( $stats_json, true );
			if ( ! is_array( $stats ) || empty( $stats ) ) {
				$stats = Admin_Settings::get_default_impact_stats();
			}
		}

		// Sort by stat_order
		usort( $stats, function( $a, $b ) {
			return ( $a['stat_order'] ?? 1 ) <=> ( $b['stat_order'] ?? 1 );
		});

		$stats = array_slice( $stats, 0, 5 );

		ob_start();
		?>
		<section class="jp-section jp-impact-stats-section" style="padding-top: 15px; padding-bottom: 45px;">
			<div class="jp-container">
				<div class="jp-impact-stats-card">
					<div class="jp-impact-stats-grid">
						<?php foreach ( $stats as $index => $stat ) : ?>
							<?php
							$number = $stat['stat_number'] ?? '';
							$label  = $stat['stat_label'] ?? '';
							$icon   = $stat['stat_icon'] ?? 'dashicons-chart-bar';

							if ( empty( $number ) && empty( $label ) ) {
								continue;
							}
							?>
							<div class="jp-impact-stat-item">
								<div class="jp-impact-stat-icon-wrap">
									<?php if ( str_starts_with( $icon, 'http' ) ) : ?>
										<img src="<?php echo esc_url( $icon ); ?>" alt="<?php echo esc_attr( $number ); ?>" class="jp-impact-stat-custom-img">
									<?php elseif ( str_starts_with( $icon, 'dashicons-' ) ) : ?>
										<span class="dashicons <?php echo esc_attr( $icon ); ?>"></span>
									<?php else : ?>
										<span class="dashicons dashicons-chart-bar"></span>
									<?php endif; ?>
								</div>

								<div class="jp-impact-stat-content">
									<div class="jp-impact-stat-number"><?php echo esc_html( $number ); ?></div>
									<div class="jp-impact-stat-label"><?php echo esc_html( $label ); ?></div>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</section>
		<?php
		return ob_get_clean();
	}
}

