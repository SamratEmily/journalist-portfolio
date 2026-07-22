<?php
/**
 * Home Page Template for Journalist Portfolio Hub.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require JP_HUB_PLUGIN_DIR . 'templates/header-nav.php';

$full_name      = get_option( 'jp_full_name', 'Investigative Journalist' );
$designation    = get_option( 'jp_designation', 'Senior Reporter & Documentarian' );
$profile_image  = get_option( 'jp_profile_image', '' );
$show_on_home   = get_option( 'jp_show_on_home', '1' );
$home_objective = get_option( 'jp_home_objective', 'Dedicated to uncovering in-depth human stories, global climate challenges, and investigative truth through rigorous reporting.' );

?>

<?php if ( '1' === (string) $show_on_home ) : ?>
<!-- Hero Section -->
<section class="jp-hero-section">
	<div class="jp-container">
		<div class="jp-hero-grid">
			<div class="jp-hero-content">
				<span class="jp-hero-badge"><?php esc_html_e( 'Journalist Portfolio', 'journalist-portfolio-hub' ); ?></span>
				<h1 class="jp-hero-title"><?php echo esc_html( $full_name ); ?></h1>
				<div class="jp-hero-designation"><?php echo esc_html( $designation ); ?></div>

				<?php if ( ! empty( $home_objective ) ) : ?>
					<div class="jp-hero-objective">
						"<?php echo esc_html( $home_objective ); ?>"
					</div>
				<?php endif; ?>

				<div style="display: flex; gap: 16px; margin-top: 24px;">
					<a href="<?php echo esc_url( home_url( '/stories' ) ); ?>" class="jp-cta-btn" style="padding: 12px 24px; font-size: 1rem;">
						<span><?php esc_html_e( 'Explore Stories', 'journalist-portfolio-hub' ); ?></span>
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
					</a>
					<a href="<?php echo esc_url( home_url( '/about' ) ); ?>" class="jp-cta-btn" style="background: transparent; border: 1px solid rgba(255,255,255,0.3); box-shadow: none; padding: 12px 24px; font-size: 1rem;">
						<span><?php esc_html_e( 'Read Full Bio', 'journalist-portfolio-hub' ); ?></span>
					</a>
				</div>
			</div>

			<div class="jp-hero-avatar-wrapper">
				<?php if ( ! empty( $profile_image ) ) : ?>
					<img src="<?php echo esc_url( $profile_image ); ?>" alt="<?php echo esc_attr( $full_name ); ?>" class="jp-hero-avatar">
				<?php else : ?>
					<div class="jp-hero-avatar" style="background: #334155; display: flex; align-items: center; justify-content: center; color: #94a3b8; font-size: 48px;">
						<span class="dashicons dashicons-admin-users" style="font-size: 64px; width: 64px; height: 64px;"></span>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>

<!-- Featured Stories Section -->
<section class="jp-section">
	<div class="jp-container">
		<div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 30px;">
			<div>
				<h2 class="jp-section-title"><?php esc_html_e( 'Featured Investigations & Stories', 'journalist-portfolio-hub' ); ?></h2>
				<p class="jp-section-subtitle"><?php esc_html_e( 'Handpicked impactful reporting, investigative series, and deep-dive features.', 'journalist-portfolio-hub' ); ?></p>
			</div>
			<a href="<?php echo esc_url( home_url( '/stories' ) ); ?>" class="jp-read-more" style="font-size: 1rem; font-weight: 700;">
				<?php esc_html_e( 'View All Stories', 'journalist-portfolio-hub' ); ?> &rarr;
			</a>
		</div>

		<?php
		// Query Featured Stories ordered by priority (DESC).
		$featured_args = array(
			'post_type'      => 'story',
			'post_status'    => 'publish',
			'posts_per_page' => 6,
			'meta_query'     => array(
				array(
					'key'     => '_story_is_featured',
					'value'   => '1',
					'compare' => '=',
				),
			),
			'meta_key'       => '_story_feature_priority',
			'orderby'        => 'meta_value_num',
			'order'          => 'DESC',
		);

		$featured_query = new \WP_Query( $featured_args );

		// Fallback query if no story marked as featured yet.
		if ( ! $featured_query->have_posts() ) {
			$featured_args = array(
				'post_type'      => 'story',
				'post_status'    => 'publish',
				'posts_per_page' => 6,
				'orderby'        => 'date',
				'order'          => 'DESC',
			);
			$featured_query = new \WP_Query( $featured_args );
		}

		if ( $featured_query->have_posts() ) :
			?>
			<div class="jp-stories-grid">
				<?php
				while ( $featured_query->have_posts() ) :
					$featured_query->the_post();

					$kicker       = get_post_meta( get_the_ID(), '_story_heading', true );
					$custom_desc  = get_post_meta( get_the_ID(), '_story_description', true );
					$is_feat      = get_post_meta( get_the_ID(), '_story_is_featured', true );
					$categories   = get_the_terms( get_the_ID(), 'story_category' );
					$primary_cat  = ( ! empty( $categories ) && ! is_wp_error( $categories ) ) ? $categories[0]->name : 'General';
					$cat_link     = ( ! empty( $categories ) && ! is_wp_error( $categories ) ) ? get_term_link( $categories[0] ) : '#';
					?>
					<article class="jp-story-card">
						<a href="<?php the_permalink(); ?>" class="jp-story-thumb-link">
							<?php if ( has_post_thumbnail() ) : ?>
								<?php the_post_thumbnail( 'medium_large', array( 'class' => 'jp-story-thumb' ) ); ?>
							<?php else : ?>
								<div class="jp-story-thumb" style="background: linear-gradient(135deg, #1e293b, #0f172a); display: flex; align-items: center; justify-content: center; color: #64748b;">
									<span class="dashicons dashicons-format-aside" style="font-size: 40px; width: 40px; height: 40px;"></span>
								</div>
							<?php endif; ?>

							<?php if ( '1' === (string) $is_feat ) : ?>
								<span class="jp-badge-featured"><?php esc_html_e( 'Featured', 'journalist-portfolio-hub' ); ?></span>
							<?php endif; ?>
						</a>

						<div class="jp-story-content">
							<div class="jp-story-meta">
								<a href="<?php echo esc_url( $cat_link ); ?>" class="jp-story-category-tag"><?php echo esc_html( $primary_cat ); ?></a>
								<span>&bull;</span>
								<time><?php echo get_the_date(); ?></time>
							</div>

							<?php if ( ! empty( $kicker ) ) : ?>
								<div class="jp-story-kicker"><?php echo esc_html( $kicker ); ?></div>
							<?php endif; ?>

							<h3 class="jp-story-title">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h3>

							<div class="jp-story-excerpt">
								<?php
								if ( ! empty( $custom_desc ) ) {
									echo wp_kses_post( wp_trim_words( $custom_desc, 22 ) );
								} else {
									echo esc_html( wp_trim_words( get_the_excerpt(), 22 ) );
								}
								?>
							</div>

							<a href="<?php the_permalink(); ?>" class="jp-read-more">
								<span><?php esc_html_e( 'Read Full Investigation', 'journalist-portfolio-hub' ); ?></span>
								<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
							</a>
						</div>
					</article>
				<?php endwhile; wp_reset_postdata(); ?>
			</div>
		<?php else : ?>
			<div style="background: #ffffff; padding: 40px; text-align: center; border-radius: 12px; border: 1px solid #e2e8f0;">
				<h3><?php esc_html_e( 'No Stories Published Yet', 'journalist-portfolio-hub' ); ?></h3>
				<p><?php esc_html_e( 'Check back soon or create new stories from the WordPress Admin Dashboard under Stories -> Add New.', 'journalist-portfolio-hub' ); ?></p>
			</div>
		<?php endif; ?>
	</div>
</section>

<?php require JP_HUB_PLUGIN_DIR . 'templates/footer.php'; ?>
