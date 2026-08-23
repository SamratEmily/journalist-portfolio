<?php
/**
 * Dedicated Photos Page Template with Pagination.
 *
 * @package JournalistPortfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require JP_HUB_PLUGIN_DIR . 'templates/header-nav.php';

$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : ( ( get_query_var( 'page' ) ) ? get_query_var( 'page' ) : 1 );

$photos_args = array(
	'post_type'      => 'jp_photo',
	'post_status'    => 'publish',
	'posts_per_page' => 6,
	'paged'          => $paged,
	'orderby'        => 'date',
	'order'          => 'DESC',
);

$photos_query = new \WP_Query( $photos_args );
?>

<div class="jp-section" style="padding-top: 40px; padding-bottom: 60px; background: #f8fafc;">
	<div class="jp-container">
		
		<!-- Page Header -->
		<div style="margin-bottom: 40px; border-bottom: 1px solid #e2e8f0; padding-bottom: 24px;">
			<span style="font-size: 0.8rem; font-weight: 700; color: #059669; letter-spacing: 0.08em; text-transform: uppercase; display: block; margin-bottom: 6px;"><?php esc_html_e( 'VISUAL JOURNALISM & DISPATCHES', 'journalist-portfolio-hub' ); ?></span>
			<h1 style="font-size: 2.25rem; font-weight: 800; color: #0f172a; margin: 0 0 8px; font-family: var(--jp-font-serif, 'Merriweather', Georgia, serif);"><?php esc_html_e( 'Photos Gallery', 'journalist-portfolio-hub' ); ?></h1>
			<p style="color: #64748b; font-size: 1.05rem; max-width: 650px; margin: 0;"><?php esc_html_e( 'In-depth photojournalism essays, field photography dispatches, and human portraits.', 'journalist-portfolio-hub' ); ?></p>
		</div>

		<!-- Grid Layout -->
		<?php if ( $photos_query->have_posts() ) : ?>
			<div class="jp-photos-grid">
				<?php while ( $photos_query->have_posts() ) : $photos_query->the_post();
					$tags      = get_post_meta( get_the_ID(), '_photo_tags', true );
					$photo_url = get_post_meta( get_the_ID(), '_photo_image', true );
					$desc      = get_post_meta( get_the_ID(), '_photo_description', true );
					$pub_url   = get_post_meta( get_the_ID(), '_photo_published_url', true );

					if ( empty( $photo_url ) && has_post_thumbnail() ) {
						$photo_url = get_the_post_thumbnail_url( get_the_ID(), 'large' );
					}
					if ( empty( $photo_url ) ) {
						$photo_url = 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80';
					}
					?>
					<article class="jp-photo-card" data-photo-url="<?php echo esc_url( $photo_url ); ?>" data-title="<?php echo esc_attr( get_the_title() ); ?>" data-desc="<?php echo esc_attr( $desc ); ?>" data-tags="<?php echo esc_attr( $tags ); ?>" data-published-url="<?php echo esc_url( $pub_url ); ?>">
						<div class="jp-photo-thumb-wrap">
							<img src="<?php echo esc_url( $photo_url ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" class="jp-photo-thumb">
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
								<span class="jp-photo-title-text"><?php the_title(); ?></span>
							</h3>
							<?php if ( ! empty( $desc ) ) : ?>
								<p class="jp-photo-desc"><?php echo esc_html( wp_trim_words( $desc, 18 ) ); ?></p>
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
				<?php endwhile; wp_reset_postdata(); ?>
			</div>

			<!-- Pagination Controls -->
			<div class="jp-pagination-wrapper" style="margin-top: 40px; text-align: center;">
				<div class="jp-pagination">
					<?php
					echo paginate_links(
						array(
							'total'        => $photos_query->max_num_pages,
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
				<span class="dashicons dashicons-format-image" style="font-size: 48px; width: 48px; height: 48px; color: #cbd5e1; margin-bottom: 16px;"></span>
				<h3 style="color: #0f172a; margin-bottom: 8px;"><?php esc_html_e( 'No photos uploaded yet', 'journalist-portfolio-hub' ); ?></h3>
				<p style="color: #64748b; max-width: 400px; margin: 0 auto;"><?php esc_html_e( 'Photos will appear here once uploaded in Portfolio Settings -> Photos.', 'journalist-portfolio-hub' ); ?></p>
			</div>
		<?php endif; ?>

	</div>
</div>

<?php
require JP_HUB_PLUGIN_DIR . 'templates/footer.php';
