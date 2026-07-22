<?php
/**
 * Navigation Header Template Component for Journalist Portfolio Hub.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$full_name     = get_option( 'jp_full_name', get_bloginfo( 'name' ) );
$designation   = get_option( 'jp_designation', 'Journalist & Writer' );
$profile_image = get_option( 'jp_profile_image', '' );
$contact_page  = get_page_by_path( 'contact' );
$contact_url   = $contact_page ? get_permalink( $contact_page->ID ) : home_url( '/contact' );
$home_url      = home_url( '/' );

$current_page_slug = get_post_field( 'post_name', get_post() );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php wp_title( '|', true, 'right' ); ?></title>
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'jp-portfolio-active' ); ?>>

<header class="jp-header">
	<div class="jp-container">
		<nav class="jp-navbar">
			<!-- Journalist Brand Logo / Name -->
			<a href="<?php echo esc_url( $home_url ); ?>" class="jp-brand">
				<?php if ( ! empty( $profile_image ) ) : ?>
					<img src="<?php echo esc_url( $profile_image ); ?>" alt="<?php echo esc_attr( $full_name ); ?>" class="jp-brand-avatar">
				<?php endif; ?>
				<div>
					<span class="jp-brand-title"><?php echo esc_html( $full_name ? $full_name : get_bloginfo( 'name' ) ); ?></span>
					<span class="jp-brand-subtitle"><?php echo esc_html( $designation ); ?></span>
				</div>
			</a>

			<!-- Navigation Links -->
			<ul class="jp-nav-menu">
				<li class="jp-nav-item <?php echo ( is_front_page() || is_page( 'home' ) ) ? 'active' : ''; ?>">
					<a href="<?php echo esc_url( home_url( '/home' ) ); ?>"><?php esc_html_e( 'Home', 'journalist-portfolio-hub' ); ?></a>
				</li>
				<li class="jp-nav-item <?php echo is_page( 'about' ) ? 'active' : ''; ?>">
					<a href="<?php echo esc_url( home_url( '/about' ) ); ?>"><?php esc_html_e( 'About', 'journalist-portfolio-hub' ); ?></a>
				</li>
				<li class="jp-nav-item <?php echo ( is_page( 'stories' ) || is_post_type_archive( 'story' ) || is_tax( 'story_category' ) || is_singular( 'story' ) ) ? 'active' : ''; ?>">
					<a href="<?php echo esc_url( home_url( '/stories' ) ); ?>"><?php esc_html_e( 'Stories', 'journalist-portfolio-hub' ); ?></a>
				</li>
				<li class="jp-nav-item <?php echo is_page( 'multimedia' ) ? 'active' : ''; ?>">
					<a href="<?php echo esc_url( home_url( '/multimedia' ) ); ?>"><?php esc_html_e( 'Multimedia', 'journalist-portfolio-hub' ); ?></a>
				</li>
				<li class="jp-nav-item <?php echo is_page( 'awards' ) ? 'active' : ''; ?>">
					<a href="<?php echo esc_url( home_url( '/awards' ) ); ?>"><?php esc_html_e( 'Awards', 'journalist-portfolio-hub' ); ?></a>
				</li>
			</ul>

			<!-- Right Controls: Search Box & Contact CTA -->
			<div class="jp-header-right">
				<form role="search" method="get" class="jp-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
					<span class="dashicons dashicons-search jp-search-icon"></span>
					<input type="search" class="jp-search-input" placeholder="<?php esc_attr_e( 'Search stories...', 'journalist-portfolio-hub' ); ?>" value="<?php echo get_search_query(); ?>" name="s" />
					<input type="hidden" name="post_type" value="story" />
				</form>

				<a href="<?php echo esc_url( $contact_url ); ?>" class="jp-cta-btn">
					<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
						<polyline points="22,6 12,13 2,6"></polyline>
					</svg>
					<span><?php esc_html_e( 'Contact Me', 'journalist-portfolio-hub' ); ?></span>
				</a>
			</div>
		</nav>
	</div>
</header>
