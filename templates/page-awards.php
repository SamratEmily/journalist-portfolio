<?php
/**
 * Dedicated Awards & Fellowships Page Template.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require JP_HUB_PLUGIN_DIR . 'templates/header-nav.php';

$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : ( ( get_query_var( 'page' ) ) ? get_query_var( 'page' ) : 1 );

$awards_args = array(
	'post_type'      => 'jp_award',
	'post_status'    => 'publish',
	'posts_per_page' => 6, // 3 cards per row x 2 rows max per page
	'paged'          => $paged,
	'orderby'        => 'date',
	'order'          => 'DESC',
);

$awards_query = new \WP_Query( $awards_args );
?>

<div class="jp-section" style="padding-top: 40px; padding-bottom: 60px;">
	<div class="jp-container">
		
		<!-- Page Header -->
		<div style="margin-bottom: 40px; border-bottom: 1px solid #e2e8f0; padding-bottom: 24px;">
			<h1 style="font-size: 2.5rem; font-weight: 800; color: #0f172a; margin: 0 0 10px 0;">
				<?php esc_html_e( 'Awards & Fellowships', 'journalist-portfolio-hub' ); ?>
			</h1>
			<p style="font-size: 1.1rem; color: #64748b; margin: 0; max-width: 680px;">
				<?php esc_html_e( 'Recognitions, investigative reporting grants, international fellowships, and media honors.', 'journalist-portfolio-hub' ); ?>
			</p>
		</div>

		<!-- 3 Cards Per Row Grid Layout -->
		<?php if ( $awards_query->have_posts() ) : ?>
			<div class="jp-awards-grid">
				<?php
				while ( $awards_query->have_posts() ) :
					$awards_query->the_post();

					$for      = get_post_meta( get_the_ID(), '_award_for', true );
					$year     = get_post_meta( get_the_ID(), '_award_year', true );
					$location = get_post_meta( get_the_ID(), '_award_location', true );
					$icon     = get_post_meta( get_the_ID(), '_award_icon', true );
					$desc     = get_post_meta( get_the_ID(), '_award_description', true );
					if ( empty( $icon ) ) {
						$icon = 'dashicons-awards';
					}
					?>
					<article class="jp-award-card">
						<div>
							<div class="jp-award-card-header">
								<div class="jp-award-icon-circle">
									<?php if ( str_starts_with( $icon, 'http' ) ) : ?>
										<img src="<?php echo esc_url( $icon ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>">
									<?php else : ?>
										<span class="dashicons <?php echo esc_attr( $icon ); ?>"></span>
									<?php endif; ?>
								</div>

								<?php if ( ! empty( $year ) ) : ?>
									<span class="jp-award-year-badge"><?php echo esc_html( $year ); ?></span>
								<?php endif; ?>
							</div>

							<h2 class="jp-award-title"><?php the_title(); ?></h2>

							<?php if ( ! empty( $for ) ) : ?>
								<span class="jp-award-subtitle"><?php echo esc_html( $for ); ?></span>
							<?php endif; ?>

							<?php if ( ! empty( $desc ) ) : ?>
								<p class="jp-award-desc"><?php echo esc_html( $desc ); ?></p>
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
				<?php endwhile; wp_reset_postdata(); ?>
			</div>

			<!-- Numeric Pagination -->
			<div class="jp-pagination-wrapper">
				<div class="jp-pagination">
					<?php
					echo paginate_links(
						array(
							'total'        => $awards_query->max_num_pages,
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
				<span class="dashicons dashicons-awards" style="font-size: 48px; width: 48px; height: 48px; color: #cbd5e1; margin-bottom: 16px;"></span>
				<h3 style="color: #0f172a; margin-bottom: 8px;"><?php esc_html_e( 'No awards found', 'journalist-portfolio-hub' ); ?></h3>
				<p style="color: #64748b; max-width: 400px; margin: 0 auto;"><?php esc_html_e( 'Awards and fellowships will appear here once added in the plugin settings.', 'journalist-portfolio-hub' ); ?></p>
			</div>
		<?php endif; ?>

	</div>
</div>

<?php
require JP_HUB_PLUGIN_DIR . 'templates/footer.php';
