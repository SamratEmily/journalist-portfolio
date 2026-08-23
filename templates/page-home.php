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
$profile_image    = get_option( 'jp_profile_image', '' );
$hero_cover_image = get_option( 'jp_hero_cover_image', '' );
if ( empty( $hero_cover_image ) ) {
	$hero_cover_image = $profile_image;
}
$show_on_home   = get_option( 'jp_show_on_home', '1' );
$home_objective = get_option( 'jp_home_objective', 'Dedicated to uncovering in-depth human stories, global climate challenges, and investigative truth through rigorous reporting.' );

$featured_story_id = 0;
?>

<?php if ( '1' === (string) $show_on_home ) : ?>
<!-- Hero Section (Full Width Background Image with Transparent Content Overlay on Desktop; Mobile-Optimized Stacked Layout) -->
<section class="jp-hero-section" style="<?php echo ! empty( $hero_cover_image ) ? 'background-image: url(' . esc_url( $hero_cover_image ) . ');' : ''; ?>">
	<div class="jp-hero-overlay"></div>
	<?php if ( ! empty( $hero_cover_image ) ) : ?>
		<div class="jp-hero-mobile-image-wrap">
			<img src="<?php echo esc_url( $hero_cover_image ); ?>" alt="<?php echo esc_attr( $full_name ); ?>" class="jp-hero-mobile-image">
		</div>
	<?php endif; ?>
	<div class="jp-container jp-hero-container">
		<div class="jp-hero-content">
			<h1 class="jp-hero-title"><?php echo esc_html( $full_name ); ?></h1>
			<div class="jp-hero-designation"><?php echo esc_html( $designation ); ?></div>

			<?php if ( ! empty( $home_objective ) ) : ?>
				<div class="jp-hero-objective">
					"<?php echo esc_html( $home_objective ); ?>"
				</div>
			<?php endif; ?>

			<div class="jp-hero-actions" style="display: flex; gap: 16px; margin-top: 28px; flex-wrap: wrap;">
				<a href="<?php echo esc_url( home_url( '/stories' ) ); ?>" class="jp-cta-btn" style="padding: 12px 28px; font-size: 1rem;">
					<span><?php esc_html_e( 'Explore Stories', 'journalist-portfolio-hub' ); ?></span>
					<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
				</a>
				<a href="<?php echo esc_url( home_url( '/about' ) ); ?>" class="jp-cta-btn jp-cta-btn-outline" style="background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.35); backdrop-filter: blur(6px); box-shadow: none; padding: 12px 28px; font-size: 1rem;">
					<span><?php esc_html_e( 'Read Full Bio', 'journalist-portfolio-hub' ); ?></span>
				</a>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>

<!-- 1. FEATURED INVESTIGATION SECTION (Single Card Matching Screenshot) -->
<section class="jp-section" style="padding-bottom: 30px;">
	<div class="jp-container">
		<?php
		// Query single highest priority featured story.
		$featured_args = array(
			'post_type'      => 'story',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
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

		// Fallback to latest post if no story explicitly marked as featured
		if ( ! $featured_query->have_posts() ) {
			$featured_args = array(
				'post_type'      => 'story',
				'post_status'    => 'publish',
				'posts_per_page' => 1,
				'orderby'        => 'date',
				'order'          => 'DESC',
			);
			$featured_query = new \WP_Query( $featured_args );
		}

		if ( $featured_query->have_posts() ) :
			while ( $featured_query->have_posts() ) :
				$featured_query->the_post();
				$featured_story_id = get_the_ID();

				$kicker        = get_post_meta( get_the_ID(), '_story_heading', true );
				$publisher     = get_post_meta( get_the_ID(), '_story_publisher', true );
				$publisher_url = get_post_meta( get_the_ID(), '_story_publisher_url', true );
				$custom_desc   = get_post_meta( get_the_ID(), '_story_description', true );
				$read_time     = \JournalistPortfolio\CPT_Stories::get_reading_time( get_the_ID() );

				$terms       = get_the_terms( get_the_ID(), 'story_category' );
				$cat_names   = array();
				if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
					foreach ( $terms as $term ) {
						$cat_names[] = $term->name;
					}
				}
				$cat_str = ! empty( $cat_names ) ? implode( ', ', $cat_names ) : 'General';
				?>
				<article class="jp-featured-card-horizontal">
					<a href="<?php the_permalink(); ?>" class="jp-featured-thumb-col">
						<?php if ( has_post_thumbnail() ) : ?>
							<?php the_post_thumbnail( 'jp_featured_400_300', array( 'class' => 'jp-featured-thumb-img', 'width' => 400, 'height' => 300 ) ); ?>
						<?php else : ?>
							<div class="jp-featured-thumb-img" style="background: linear-gradient(135deg, #0f172a, #1e293b); display: flex; align-items: center; justify-content: center; color: #64748b;">
								<span class="dashicons dashicons-format-aside" style="font-size: 56px; width: 56px; height: 56px;"></span>
							</div>
						<?php endif; ?>
					</a>

					<div class="jp-featured-content-col">
						<div>
							<div class="jp-featured-kicker"><?php esc_html_e( 'FEATURED INVESTIGATION', 'journalist-portfolio-hub' ); ?></div>

							<h2 class="jp-featured-title">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h2>

							<div class="jp-featured-excerpt">
								<?php
								if ( ! empty( $custom_desc ) ) {
									echo wp_kses_post( wp_trim_words( $custom_desc, 30 ) );
								} else {
									echo esc_html( wp_trim_words( get_the_excerpt(), 30 ) );
								}
								?>
							</div>
						</div>

						<div class="jp-featured-footer-row">
							<div class="jp-featured-meta-info">
								<?php if ( ! empty( $publisher ) ) : ?>
									<span><strong><?php esc_html_e( 'Published in:', 'journalist-portfolio-hub' ); ?></strong> 
										<?php if ( ! empty( $publisher_url ) ) : ?>
											<a href="<?php echo esc_url( $publisher_url ); ?>" target="_blank" rel="noopener noreferrer" style="color: inherit; text-decoration: underline; font-weight: 600;"><?php echo esc_html( $publisher ); ?></a>
										<?php else : ?>
											<?php echo esc_html( $publisher ); ?>
										<?php endif; ?>
									</span>
									<span style="margin: 0 8px; color: #cbd5e1;">|</span>
								<?php endif; ?>
								<span><strong><?php esc_html_e( 'Category:', 'journalist-portfolio-hub' ); ?></strong> <?php echo esc_html( $cat_str ); ?></span>
								<span style="margin: 0 8px; color: #cbd5e1;">|</span>
								<span>⏱️ <?php echo esc_html( $read_time ); ?></span>
							</div>

							<a href="<?php the_permalink(); ?>" class="jp-featured-cta-btn">
								<span><?php esc_html_e( 'Read Full Story', 'journalist-portfolio-hub' ); ?></span>
								<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
							</a>
						</div>
					</div>
				</article>
				<?php
			endwhile;
			wp_reset_postdata();
		endif;
		?>
	</div>
</section>

<!-- IMPACT STATS COUNTER BAR SECTION -->
<?php jp_render_impact_stats(); ?>

<!-- 2. STORIES SECTION (Grid of remaining stories) -->
<section class="jp-section" style="padding-top: 10px;">
	<div class="jp-container">
		<div class="jp-section-header-wrap">
			<div>
				<h2 class="jp-section-title"><?php esc_html_e( 'Latest Stories', 'journalist-portfolio-hub' ); ?></h2>
				<p class="jp-section-subtitle"><?php esc_html_e( 'Explore recent investigative reporting, features, and field dispatches.', 'journalist-portfolio-hub' ); ?></p>
			</div>
			<a href="<?php echo esc_url( home_url( '/stories' ) ); ?>" class="jp-read-more jp-section-header-link">
				<?php esc_html_e( 'View All Stories', 'journalist-portfolio-hub' ); ?> &rarr;
			</a>
		</div>

		<?php
		// Query remaining stories (exclude featured story ID)
		$stories_args = array(
			'post_type'      => 'story',
			'post_status'    => 'publish',
			'posts_per_page' => 3,
			'post__not_in'   => $featured_story_id > 0 ? array( $featured_story_id ) : array(),
			'orderby'        => 'date',
			'order'          => 'DESC',
		);

		$stories_query = new \WP_Query( $stories_args );

		if ( $stories_query->have_posts() ) :
			?>
			<div class="jp-stories-grid">
				<?php
				while ( $stories_query->have_posts() ) :
					$stories_query->the_post();

					$kicker        = get_post_meta( get_the_ID(), '_story_heading', true );
					$publisher     = get_post_meta( get_the_ID(), '_story_publisher', true );
					$publisher_url = get_post_meta( get_the_ID(), '_story_publisher_url', true );
					$custom_desc   = get_post_meta( get_the_ID(), '_story_description', true );
					$read_time     = \JournalistPortfolio\CPT_Stories::get_reading_time( get_the_ID() );

					$categories  = get_the_terms( get_the_ID(), 'story_category' );
					$primary_cat = ( ! empty( $categories ) && ! is_wp_error( $categories ) ) ? $categories[0]->name : 'General';
					$cat_link    = ( ! empty( $categories ) && ! is_wp_error( $categories ) ) ? get_term_link( $categories[0] ) : '#';
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
						</a>

						<div class="jp-story-content">
							<div class="jp-story-meta" style="display: flex; flex-wrap: wrap; gap: 6px; align-items: center; font-size: 0.825rem; color: var(--jp-text-muted);">
								<a href="<?php echo esc_url( $cat_link ); ?>" class="jp-story-category-tag"><?php echo esc_html( $primary_cat ); ?></a>
								
								<?php if ( ! empty( $publisher ) ) : ?>
									<span>&bull;</span>
									<?php if ( ! empty( $publisher_url ) ) : ?>
										<a href="<?php echo esc_url( $publisher_url ); ?>" target="_blank" rel="noopener noreferrer" style="color: inherit; text-decoration: underline; font-weight: 600;"><?php echo esc_html( $publisher ); ?></a>
									<?php else : ?>
										<span><?php echo esc_html( $publisher ); ?></span>
									<?php endif; ?>
								<?php endif; ?>

								<span>&bull;</span>
								<span><?php echo esc_html( $read_time ); ?></span>
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
								<span><?php esc_html_e( 'Read Full Story', 'journalist-portfolio-hub' ); ?></span>
								<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
							</a>
						</div>
					</article>
				<?php endwhile; wp_reset_postdata(); ?>
			</div>
		<?php else : ?>
			<div style="background: #ffffff; padding: 40px; text-align: center; border-radius: 12px; border: 1px solid #e2e8f0;">
				<h3><?php esc_html_e( 'No Stories Published Yet', 'journalist-portfolio-hub' ); ?></h3>
				<p><?php esc_html_e( 'Create new stories from the WordPress Admin Dashboard under Stories -> Add New.', 'journalist-portfolio-hub' ); ?></p>
			</div>
		<?php endif; ?>
	</div>
</section>

<!-- 3. AWARDS & FELLOWSHIPS CAROUSEL SECTION -->
<?php echo do_shortcode( '[jp_awards_carousel]' ); ?>

<!-- 4. PHOTOS SECTION (Rendered directly below Awards) -->
<?php jp_render_photos(); ?>

<!-- 5. VIDEOS SECTION (Rendered directly below Photos) -->
<?php jp_render_videos(); ?>

<!-- 6. BRIEF ABOUT ME SECTION (Rendered directly below Videos) -->
<?php jp_render_about_brief(); ?>

<?php require JP_HUB_PLUGIN_DIR . 'templates/footer.php'; ?>

