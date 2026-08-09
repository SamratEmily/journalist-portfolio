<?php
/**
 * Modern Story Search Results Template for Journalist Portfolio Hub.
 *
 * @package JournalistPortfolio
 * @since   1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require JP_HUB_PLUGIN_DIR . 'templates/header-nav.php';

global $wp_query;

$search_query = get_search_query();
$paged        = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : ( ( get_query_var( 'page' ) ) ? get_query_var( 'page' ) : 1 );

$selected_cat = isset( $_GET['story_cat'] ) ? sanitize_text_field( wp_unslash( $_GET['story_cat'] ) ) : '';
$selected_ord = isset( $_GET['orderby'] ) ? sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) : 'date_desc';

// Get categories for filter dropdown.
$categories = get_terms(
	array(
		'taxonomy'   => 'story_category',
		'hide_empty' => false,
	)
);

$found_count = (int) $wp_query->found_posts;
?>

<main class="jp-section jp-search-results-page">
	<div class="jp-container">
		
		<!-- Search Page Banner -->
		<div class="jp-search-header-card">
			<div class="jp-search-header-content">
				<span class="jp-search-kicker-badge"><?php esc_html_e( 'Story Search', 'journalist-portfolio-hub' ); ?></span>
				<h1 class="jp-search-title">
					<?php if ( ! empty( $search_query ) ) : ?>
						<?php printf( esc_html__( 'Search Results for: "%s"', 'journalist-portfolio-hub' ), esc_html( $search_query ) ); ?>
					<?php else : ?>
						<?php esc_html_e( 'Explore Stories & Investigations', 'journalist-portfolio-hub' ); ?>
					<?php endif; ?>
				</h1>
				<p class="jp-search-subtitle">
					<?php
					if ( ! empty( $search_query ) ) {
						printf(
							/* translators: %d: number of results, %s: query string */
							esc_html__( 'Found %1$d published %2$s matching your query.', 'journalist-portfolio-hub' ),
							$found_count,
							_n( 'story', 'stories', $found_count, 'journalist-portfolio-hub' )
						);
					} else {
						esc_html_e( 'Use the keyword search and category filters below to find specific investigations, field reports, and articles.', 'journalist-portfolio-hub' );
					}
					?>
				</p>
			</div>
		</div>

		<!-- Interactive Filter & Search Controls Bar -->
		<div class="jp-search-filter-wrapper">
			<form method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="jp-search-page-form">
				<input type="hidden" name="post_type" value="story">

				<!-- Keyword Input -->
				<div class="jp-search-input-wrapper jp-search-page-input-wrap">
					<input type="search" name="s" class="jp-search-input jp-search-page-input" placeholder="<?php esc_attr_e( 'Search keywords, topics, outlets...', 'journalist-portfolio-hub' ); ?>" value="<?php echo esc_attr( $search_query ); ?>" autocomplete="off" />
					<button type="button" class="jp-search-clear-btn" aria-label="<?php esc_attr_e( 'Clear search', 'journalist-portfolio-hub' ); ?>" style="<?php echo ! empty( $search_query ) ? 'display: flex;' : 'display: none;'; ?>">&times;</button>
					<button type="submit" class="jp-search-submit-icon-btn" aria-label="<?php esc_attr_e( 'Search', 'journalist-portfolio-hub' ); ?>">
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<circle cx="11" cy="11" r="8"></circle>
							<line x1="21" y1="21" x2="16.65" y2="16.65"></line>
						</svg>
					</button>
				</div>

				<!-- Category Select -->
				<div class="jp-filter-select-wrap">
					<select name="story_cat" class="jp-filter-select" onchange="this.form.submit()">
						<option value=""><?php esc_html_e( 'All Categories', 'journalist-portfolio-hub' ); ?></option>
						<?php if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) : ?>
							<?php foreach ( $categories as $cat ) : ?>
								<option value="<?php echo esc_attr( $cat->slug ); ?>" <?php selected( $selected_cat, $cat->slug ); ?>>
									<?php echo esc_html( $cat->name ); ?> (<?php echo esc_html( $cat->count ); ?>)
								</option>
							<?php endforeach; ?>
						<?php endif; ?>
					</select>
				</div>

				<!-- Sorting Select -->
				<div class="jp-filter-select-wrap">
					<select name="orderby" class="jp-filter-select" onchange="this.form.submit()">
						<option value="date_desc" <?php selected( $selected_ord, 'date_desc' ); ?>><?php esc_html_e( 'Newest First', 'journalist-portfolio-hub' ); ?></option>
						<option value="date_asc" <?php selected( $selected_ord, 'date_asc' ); ?>><?php esc_html_e( 'Oldest First', 'journalist-portfolio-hub' ); ?></option>
						<option value="title" <?php selected( $selected_ord, 'title' ); ?>><?php esc_html_e( 'Title (A-Z)', 'journalist-portfolio-hub' ); ?></option>
					</select>
				</div>

				<?php if ( ! empty( $search_query ) || ! empty( $selected_cat ) || 'date_desc' !== $selected_ord ) : ?>
					<a href="<?php echo esc_url( home_url( '/stories' ) ); ?>" class="jp-search-reset-btn">
						<?php esc_html_e( 'Reset Filters', 'journalist-portfolio-hub' ); ?>
					</a>
				<?php endif; ?>
			</form>
		</div>

		<!-- Results Count Meta -->
		<div class="jp-results-summary-bar">
			<div class="jp-results-count-tag">
				<?php printf( esc_html__( 'Showing %d %s', 'journalist-portfolio-hub' ), $found_count, _n( 'story', 'stories', $found_count, 'journalist-portfolio-hub' ) ); ?>
			</div>
		</div>

		<!-- Stories Grid -->
		<?php if ( have_posts() ) : ?>
			<div class="jp-stories-grid">
				<?php
				while ( have_posts() ) :
					the_post();

					$kicker        = get_post_meta( get_the_ID(), '_story_heading', true );
					$publisher     = get_post_meta( get_the_ID(), '_story_publisher', true );
					$publisher_url = get_post_meta( get_the_ID(), '_story_publisher_url', true );
					$custom_desc   = get_post_meta( get_the_ID(), '_story_description', true );
					$is_feat       = get_post_meta( get_the_ID(), '_story_is_featured', true );
					$read_time     = \JournalistPortfolio\CPT_Stories::get_reading_time( get_the_ID() );

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
					$add_args = array( 'post_type' => 'story' );
					if ( ! empty( $search_query ) ) {
						$add_args['s'] = $search_query;
					}
					if ( ! empty( $selected_cat ) ) {
						$add_args['story_cat'] = $selected_cat;
					}
					if ( 'date_desc' !== $selected_ord ) {
						$add_args['orderby'] = $selected_ord;
					}

					echo paginate_links(
						array(
							'total'     => $wp_query->max_num_pages,
							'current'   => $paged,
							'format'    => '?paged=%#%',
							'show_all'  => false,
							'type'      => 'plain',
							'prev_next' => true,
							'prev_text' => sprintf( '&laquo; %s', __( 'Previous', 'journalist-portfolio-hub' ) ),
							'next_text' => sprintf( '%s &raquo;', __( 'Next', 'journalist-portfolio-hub' ) ),
							'add_args'  => $add_args,
						)
					);
					?>
				</div>
			</div>
			<?php wp_reset_postdata(); ?>

		<?php else : ?>
			<!-- Modern Empty State -->
			<div class="jp-search-empty-card">
				<div class="jp-empty-icon-wrap">
					<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
						<circle cx="11" cy="11" r="8"></circle>
						<line x1="21" y1="21" x2="16.65" y2="16.65"></line>
						<line x1="8" y1="11" x2="14" y2="11"></line>
					</svg>
				</div>

				<h3 class="jp-empty-title">
					<?php
					if ( ! empty( $search_query ) ) {
						printf( esc_html__( 'No stories found for "%s"', 'journalist-portfolio-hub' ), esc_html( $search_query ) );
					} else {
						esc_html_e( 'No stories match your criteria', 'journalist-portfolio-hub' );
					}
					?>
				</h3>

				<p class="jp-empty-desc">
					<?php esc_html_e( 'Try checking for typos, searching for broader terms (e.g. "Climate", "Investigation", "Reuters"), or resetting category filters.', 'journalist-portfolio-hub' ); ?>
				</p>

				<?php if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) : ?>
					<div class="jp-empty-pills-wrap">
						<span class="jp-empty-pills-title"><?php esc_html_e( 'Or browse popular story categories:', 'journalist-portfolio-hub' ); ?></span>
						<div class="jp-empty-pills-list">
							<?php foreach ( array_slice( $categories, 0, 6 ) as $cat_pill ) : ?>
								<a href="<?php echo esc_url( home_url( '/stories?story_cat=' . $cat_pill->slug ) ); ?>" class="jp-cat-pill-link">
									<?php echo esc_html( $cat_pill->name ); ?>
								</a>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>

				<div style="margin-top: 24px;">
					<a href="<?php echo esc_url( home_url( '/stories' ) ); ?>" class="jp-btn-primary-gradient">
						<?php esc_html_e( 'View All Stories', 'journalist-portfolio-hub' ); ?>
					</a>
				</div>
			</div>
		<?php endif; ?>

	</div>
</main>

<?php require JP_HUB_PLUGIN_DIR . 'templates/footer.php'; ?>
