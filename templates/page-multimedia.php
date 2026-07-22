<?php
/**
 * Dedicated Multimedia Productions Page Template.
 *
 * @package JournalistPortfolio
 * @since   1.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require JP_HUB_PLUGIN_DIR . 'templates/header-nav.php';

$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : ( ( get_query_var( 'page' ) ) ? get_query_var( 'page' ) : 1 );

$media_args = array(
	'post_type'      => 'jp_multimedia',
	'post_status'    => 'publish',
	'posts_per_page' => 8,
	'paged'          => $paged,
	'orderby'        => 'date',
	'order'          => 'DESC',
);

$media_query = new \WP_Query( $media_args );
?>

<div class="jp-section" style="padding-top: 40px; padding-bottom: 60px;">
	<div class="jp-container">
		
		<!-- Page Header -->
		<div style="margin-bottom: 40px; border-bottom: 1px solid #e2e8f0; padding-bottom: 24px;">
			<span style="font-size: 0.8rem; font-weight: 700; color: #059669; letter-spacing: 0.08em; text-transform: uppercase; display: block; margin-bottom: 6px;"><?php esc_html_e( 'AUDIO & VISUAL JOURNALISM', 'journalist-portfolio-hub' ); ?></span>
			<h1 style="font-size: 2.25rem; font-weight: 800; color: #0f172a; margin: 0 0 8px; font-family: var(--jp-font-serif, 'Merriweather', Georgia, serif);"><?php esc_html_e( 'Multimedia Gallery', 'journalist-portfolio-hub' ); ?></h1>
			<p style="color: #64748b; font-size: 1.05rem; max-width: 650px; margin: 0;"><?php esc_html_e( 'Documentary videos, investigative podcasts, photo essays, and data visualization projects.', 'journalist-portfolio-hub' ); ?></p>
		</div>

		<!-- Grid Layout -->
		<?php if ( $media_query->have_posts() ) : ?>
			<div class="jp-multimedia-grid">
				<?php while ( $media_query->have_posts() ) : $media_query->the_post();
					$type        = get_post_meta( get_the_ID(), '_media_type', true );
					$tags        = get_post_meta( get_the_ID(), '_media_tags', true );
					$youtube_url = get_post_meta( get_the_ID(), '_media_youtube_url', true );
					$thumbnail   = get_post_meta( get_the_ID(), '_media_thumbnail', true );
					$desc        = get_post_meta( get_the_ID(), '_media_description', true );

					if ( empty( $thumbnail ) && has_post_thumbnail() ) {
						$thumbnail = get_the_post_thumbnail_url( get_the_ID(), 'large' );
					}
					if ( empty( $thumbnail ) ) {
						$thumbnail = 'https://images.unsplash.com/photo-1594909122845-11baa439b7bf?auto=format&fit=crop&w=800&q=80';
					}
					if ( empty( $type ) ) {
						$type = 'Video';
					}
					?>
					<article class="jp-multimedia-card" data-video-url="<?php echo esc_url( $youtube_url ); ?>" data-title="<?php echo esc_attr( get_the_title() ); ?>">
						<div class="jp-multimedia-thumb-wrap">
							<img src="<?php echo esc_url( $thumbnail ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" class="jp-multimedia-thumb">
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
									<?php the_title(); ?>
								</a>
							</h3>
							<?php if ( ! empty( $desc ) ) : ?>
								<p class="jp-multimedia-desc"><?php echo esc_html( wp_trim_words( $desc, 18 ) ); ?></p>
							<?php endif; ?>
						</div>
					</article>
				<?php endwhile; wp_reset_postdata(); ?>
			</div>

			<!-- Pagination -->
			<div class="jp-pagination-wrapper" style="margin-top: 40px; text-align: center;">
				<div class="jp-pagination">
					<?php
					echo paginate_links(
						array(
							'total'        => $media_query->max_num_pages,
							'current'      => $paged,
							'format'       => '?paged=%#%',
							'show_all'     => false,
							'type'         => 'plain',
							'prev_next'    => true,
							'prev_text'    => sprintf( '&laquo; %s', __( 'Previous', 'journalist-portfolio-hub' ) ),
							'next_text'    => sprintf( '%s &raquo;', __( 'Next', 'journalist-portfolio-hub' ) ),
						)
					);
					?>
				</div>
			</div>
		<?php else : ?>
			<div style="background: #ffffff; padding: 60px; text-align: center; border-radius: 12px; border: 1px solid #e2e8f0;">
				<span class="dashicons dashicons-video-alt3" style="font-size: 48px; width: 48px; height: 48px; color: #cbd5e1; margin-bottom: 16px;"></span>
				<h3 style="color: #0f172a; margin-bottom: 8px;"><?php esc_html_e( 'No multimedia items found', 'journalist-portfolio-hub' ); ?></h3>
				<p style="color: #64748b; max-width: 400px; margin: 0 auto;"><?php esc_html_e( 'Multimedia productions will appear here once added in Portfolio Settings -> Multimedia.', 'journalist-portfolio-hub' ); ?></p>
			</div>
		<?php endif; ?>

	</div>
</div>

<?php
require JP_HUB_PLUGIN_DIR . 'templates/footer.php';
