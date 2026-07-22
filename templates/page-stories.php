<?php
/**
 * Stories Page & Archive Template for Journalist Portfolio Hub.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require JP_HUB_PLUGIN_DIR . 'templates/header-nav.php';

// Handle pagination.
$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : ( ( get_query_var( 'page' ) ) ? get_query_var( 'page' ) : 1 );

// Handle category filter.
$selected_cat = isset( $_GET['story_cat'] ) ? sanitize_text_field( wp_unslash( $_GET['story_cat'] ) ) : '';

$stories_args = array(
	'post_type'      => 'story',
	'post_status'    => 'publish',
	'posts_per_page' => 6,
	'paged'          => $paged,
	'orderby'        => 'date',
	'order'          => 'DESC',
);

if ( ! empty( $selected_cat ) ) {
	$stories_args['tax_query'] = array(
		array(
			'taxonomy' => 'story_category',
			'field'    => 'slug',
			'terms'    => $selected_cat,
		),
	);
}

$stories_query = new \WP_Query( $stories_args );

// Fetch Taxonomy categories for dropdown filter.
$categories = get_terms(
	array(
		'taxonomy'   => 'story_category',
		'hide_empty' => false,
	)
);
?>

<main class="jp-section">
	<div class="jp-container">
		<div style="margin-bottom: 24px;">
			<h1 class="jp-section-title" style="font-size: 2.4rem;"><?php esc_html_e( 'Stories & Investigations', 'journalist-portfolio-hub' ); ?></h1>
			<p class="jp-section-subtitle"><?php esc_html_e( 'Explore published investigative stories, articles, and field reports.', 'journalist-portfolio-hub' ); ?></p>
		</div>

		<!-- Category Filter Bar -->
		<div class="jp-filter-bar">
			<form method="get" action="<?php echo esc_url( home_url( '/stories' ) ); ?>" class="jp-filter-form">
				<label for="story_cat_select" style="font-weight: 600; color: #0f172a;"><?php esc_html_e( 'Filter by Category:', 'journalist-portfolio-hub' ); ?></label>
				<select id="story_cat_select" name="story_cat" class="jp-filter-select" onchange="this.form.submit()">
					<option value=""><?php esc_html_e( 'All Categories', 'journalist-portfolio-hub' ); ?></option>
					<?php if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) : ?>
						<?php foreach ( $categories as $cat ) : ?>
							<option value="<?php echo esc_attr( $cat->slug ); ?>" <?php selected( $selected_cat, $cat->slug ); ?>>
								<?php echo esc_html( $cat->name ); ?> (<?php echo esc_html( $cat->count ); ?>)
							</option>
						<?php endforeach; ?>
					<?php endif; ?>
				</select>

				<?php if ( ! empty( $selected_cat ) ) : ?>
					<a href="<?php echo esc_url( home_url( '/stories' ) ); ?>" style="color: #ef4444; text-decoration: none; font-size: 0.875rem; font-weight: 600; margin-left: 10px;"><?php esc_html_e( 'Reset Filter', 'journalist-portfolio-hub' ); ?></a>
				<?php endif; ?>
			</form>

			<div style="font-size: 0.9rem; color: #64748b; font-weight: 500;">
				<?php printf( esc_html__( 'Showing %d Stories', 'journalist-portfolio-hub' ), (int) $stories_query->found_posts ); ?>
			</div>
		</div>

		<!-- Stories Grid -->
		<?php if ( $stories_query->have_posts() ) : ?>
			<div class="jp-stories-grid">
				<?php
				while ( $stories_query->have_posts() ) :
					$stories_query->the_post();

					$kicker      = get_post_meta( get_the_ID(), '_story_heading', true );
					$custom_desc = get_post_meta( get_the_ID(), '_story_description', true );
					$is_feat     = get_post_meta( get_the_ID(), '_story_is_featured', true );
					$story_cats  = get_the_terms( get_the_ID(), 'story_category' );
					$primary_cat = ( ! empty( $story_cats ) && ! is_wp_error( $story_cats ) ) ? $story_cats[0]->name : 'General';
					$cat_link    = ( ! empty( $story_cats ) && ! is_wp_error( $story_cats ) ) ? get_term_link( $story_cats[0] ) : '#';
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

							<h2 class="jp-story-title">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h2>

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
				<?php endwhile; ?>
			</div>

			<!-- Pagination -->
			<div class="jp-pagination-wrapper">
				<div class="jp-pagination">
					<?php
					echo paginate_links(
						array(
							'total'        => $stories_query->max_num_pages,
							'current'      => $paged,
							'format'       => '?paged=%#%',
							'show_all'     => false,
							'type'         => 'plain',
							'prev_next'    => true,
							'prev_text'    => sprintf( '&laquo; %s', __( 'Previous', 'journalist-portfolio-hub' ) ),
							'next_text'    => sprintf( '%s &raquo;', __( 'Next', 'journalist-portfolio-hub' ) ),
							'add_args'     => ! empty( $selected_cat ) ? array( 'story_cat' => $selected_cat ) : array(),
						)
					);
					?>
				</div>
			</div>
			<?php wp_reset_postdata(); ?>
		<?php else : ?>
			<div style="background: #ffffff; padding: 50px; text-align: center; border-radius: 12px; border: 1px solid #e2e8f0;">
				<h3 style="font-size: 1.4rem; color: #0f172a;"><?php esc_html_e( 'No Stories Found', 'journalist-portfolio-hub' ); ?></h3>
				<p style="color: #64748b;"><?php esc_html_e( 'No published stories match your selected criteria. Try resetting the category filter.', 'journalist-portfolio-hub' ); ?></p>
			</div>
		<?php endif; ?>
	</div>
</main>

<?php require JP_HUB_PLUGIN_DIR . 'templates/footer.php'; ?>
