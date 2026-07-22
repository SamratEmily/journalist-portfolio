<?php
/**
 * Single Story Template for Journalist Portfolio Hub.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require JP_HUB_PLUGIN_DIR . 'templates/header-nav.php';

while ( have_posts() ) :
	the_post();

	$kicker        = get_post_meta( get_the_ID(), '_story_heading', true );
	$publisher     = get_post_meta( get_the_ID(), '_story_publisher', true );
	$publisher_url = get_post_meta( get_the_ID(), '_story_publisher_url', true );
	$custom_desc   = get_post_meta( get_the_ID(), '_story_description', true );
	$read_time     = \JournalistPortfolio\CPT_Stories::get_reading_time( get_the_ID() );

	$story_cats  = get_the_terms( get_the_ID(), 'story_category' );
	$primary_cat = ( ! empty( $story_cats ) && ! is_wp_error( $story_cats ) ) ? $story_cats[0]->name : 'General';
	$cat_link    = ( ! empty( $story_cats ) && ! is_wp_error( $story_cats ) ) ? get_term_link( $story_cats[0] ) : '#';
	$author_name = get_option( 'jp_full_name', get_the_author() );
	?>

	<article class="jp-section" style="padding-top: 40px;">
		<div class="jp-container" style="max-width: 860px;">
			
			<div style="margin-bottom: 24px;">
				<a href="<?php echo esc_url( home_url( '/stories' ) ); ?>" class="jp-read-more" style="margin-bottom: 16px; display: inline-flex;">
					&larr; <?php esc_html_e( 'Back to All Stories', 'journalist-portfolio-hub' ); ?>
				</a>

				<div class="jp-story-meta" style="margin-top: 12px; font-size: 0.9rem; display: flex; flex-wrap: wrap; gap: 8px; align-items: center;">
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
					<time><?php echo get_the_date( 'F j, Y' ); ?></time>
					<span>&bull;</span>
					<span><?php printf( esc_html__( 'By %s', 'journalist-portfolio-hub' ), esc_html( $author_name ) ); ?></span>
					<span>&bull;</span>
					<span>⏱️ <?php echo esc_html( $read_time ); ?></span>
				</div>

				<?php if ( ! empty( $kicker ) ) : ?>
					<div class="jp-story-kicker" style="font-size: 1.1rem; margin-top: 10px;"><?php echo esc_html( $kicker ); ?></div>
				<?php endif; ?>

				<h1 style="font-size: 2.6rem; font-weight: 800; color: #0f172a; line-height: 1.2; margin: 10px 0 20px 0;">
					<?php the_title(); ?>
				</h1>
			</div>

			<?php if ( has_post_thumbnail() ) : ?>
				<div style="margin-bottom: 36px; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
					<?php the_post_thumbnail( 'full', array( 'style' => 'width: 100%; height: auto; display: block;' ) ); ?>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $custom_desc ) ) : ?>
				<div style="font-family: var(--jp-font-serif); font-size: 1.2rem; color: #334155; line-height: 1.7; font-style: italic; background: #ffffff; padding: 24px 30px; border-left: 4px solid var(--jp-accent); border-radius: 6px; margin-bottom: 36px; border: 1px solid #e2e8f0; border-left-width: 4px;">
					<?php echo wp_kses_post( $custom_desc ); ?>
				</div>
			<?php endif; ?>

			<div class="jp-story-full-content" style="font-family: var(--jp-font-serif); font-size: 1.1rem; line-height: 1.8; color: #1e293b; background: #ffffff; padding: 40px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: var(--jp-shadow-sm);">
				<?php the_content(); ?>
			</div>

			<div style="margin-top: 40px; padding-top: 24px; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
				<a href="<?php echo esc_url( home_url( '/stories' ) ); ?>" class="jp-cta-btn" style="background: #f1f5f9; color: #0f172a !important; box-shadow: none; border: 1px solid #cbd5e1;">
					&larr; <?php esc_html_e( 'Return to Portfolio Stories', 'journalist-portfolio-hub' ); ?>
				</a>
			</div>
		</div>
	</article>

<?php endwhile; ?>

<?php require JP_HUB_PLUGIN_DIR . 'templates/footer.php'; ?>
