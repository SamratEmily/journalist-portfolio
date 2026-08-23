<?php
/**
 * 4-Column Admin Controlled Footer Template Matching Reference Screenshot.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Fetch Admin Settings Options.
$full_name        = get_option( 'jp_full_name', 'Md Jahidul Islam' );
$col1_title       = get_option( 'jp_footer_col1_title', __( "Let's Connect", 'journalist-portfolio-hub' ) );
$col1_desc        = get_option( 'jp_footer_col1_desc', __( "For story ideas, collaborations, speaking engagements and media inquiries.", 'journalist-portfolio-hub' ) );

// Contact Info (fallback defaults matching screenshot)
$contact_email    = get_option( 'jp_footer_contact_email', '' );
if ( empty( $contact_email ) ) {
	$contact_email = get_option( 'jp_contact_email', 'contact@mdjahidulislam.com' );
}

$contact_phone    = get_option( 'jp_footer_contact_phone', '' );
if ( empty( $contact_phone ) ) {
	$contact_phone = get_option( 'jp_contact_phone', '+880 17XXX XXXXX' );
}

$contact_location = get_option( 'jp_footer_contact_location', '' );
if ( empty( $contact_location ) ) {
	$contact_location = get_option( 'jp_contact_location', 'Dhaka, Bangladesh' );
}

// Column 2 Social Links
$col2_title       = get_option( 'jp_footer_col2_title', __( "Follow Me", 'journalist-portfolio-hub' ) );
$social_json      = get_option( 'jp_social_links_data', '[]' );
$social_links     = json_decode( $social_json, true );

if ( ! is_array( $social_links ) || empty( $social_links ) ) {
	$social_links = array(
		array( 'name' => 'Twitter / X', 'icon' => 'jp-icon-twitter', 'url' => get_option( 'jp_social_twitter', '#' ) ),
		array( 'name' => 'YouTube', 'icon' => 'jp-icon-youtube', 'url' => get_option( 'jp_social_youtube', '#' ) ),
		array( 'name' => 'Facebook', 'icon' => 'jp-icon-facebook', 'url' => get_option( 'jp_social_facebook', '#' ) ),
		array( 'name' => 'Instagram', 'icon' => 'jp-icon-instagram', 'url' => get_option( 'jp_social_instagram', '#' ) ),
		array( 'name' => 'LinkedIn', 'icon' => 'jp-icon-linkedin', 'url' => get_option( 'jp_social_linkedin', '#' ) ),
		array( 'name' => 'Medium', 'icon' => 'jp-icon-medium', 'url' => get_option( 'jp_social_medium', '#' ) ),
	);
}

// Helper for Social Brand Icon Class
function jp_get_social_brand_class( $name, $icon_class = '' ) {
	$lower_name = strtolower( $name );
	if ( strpos( $lower_name, 'twitter' ) !== false || strpos( $lower_name, 'x' ) !== false ) {
		return 'jp-icon-twitter';
	} elseif ( strpos( $lower_name, 'facebook' ) !== false ) {
		return 'jp-icon-facebook';
	} elseif ( strpos( $lower_name, 'linkedin' ) !== false ) {
		return 'jp-icon-linkedin';
	} elseif ( strpos( $lower_name, 'youtube' ) !== false ) {
		return 'jp-icon-youtube';
	} elseif ( strpos( $lower_name, 'instagram' ) !== false ) {
		return 'jp-icon-instagram';
	} elseif ( strpos( $lower_name, 'medium' ) !== false ) {
		return 'jp-icon-medium';
	}
	return ! empty( $icon_class ) ? $icon_class : 'jp-icon-default';
}

// Column 3 Quick Links
$col3_title       = get_option( 'jp_footer_col3_title', __( "Quick Links", 'journalist-portfolio-hub' ) );
$menu_id          = get_option( 'jp_footer_menu_id', '' );

// Column 4 Downloads
$col4_title       = get_option( 'jp_footer_col4_title', __( "Download", 'journalist-portfolio-hub' ) );

$cv_file = get_option( 'jp_footer_cv_file', '' );
if ( empty( $cv_file ) ) {
	$cv_file = get_option( 'jp_cv_file', '#' );
}

$cv_label = get_option( 'jp_cv_button_label', '' );
if ( empty( $cv_label ) ) {
	$cv_label = __( 'CV / Resume', 'journalist-portfolio-hub' );
}

$media_kit_file = get_option( 'jp_footer_mediakit_file', '' );
if ( empty( $media_kit_file ) ) {
	$media_kit_file = get_option( 'jp_media_kit_file', '#' );
}

$media_kit_label = get_option( 'jp_media_kit_label', '' );
if ( empty( $media_kit_label ) ) {
	$media_kit_label = __( 'Media Kit', 'journalist-portfolio-hub' );
}

// Bottom Bar
$copyright_text   = get_option( 'jp_footer_copyright_text', sprintf( __( '&copy; %s %s. All Rights Reserved.', 'journalist-portfolio-hub' ), date( 'Y' ), esc_html( $full_name ) ) );
$tagline          = get_option( 'jp_footer_tagline', __( 'Built with passion for truth, people and the planet.', 'journalist-portfolio-hub' ) );
?>

<footer class="jp-portfolio-footer">
	<div class="jp-footer-container">
		<div class="jp-footer-grid">
			
			<!-- Column 1: Contact Details -->
			<div class="jp-footer-col">
				<?php if ( ! empty( $col1_title ) ) : ?>
					<h4 class="jp-footer-col-title"><?php echo esc_html( $col1_title ); ?></h4>
				<?php endif; ?>

				<?php if ( ! empty( $col1_desc ) ) : ?>
					<p class="jp-footer-col1-desc"><?php echo esc_html( $col1_desc ); ?></p>
				<?php endif; ?>

				<ul class="jp-footer-contact-list">
					<?php if ( ! empty( $contact_email ) ) : ?>
						<li class="jp-footer-contact-item">
							<span class="jp-footer-icon-wrap">
								<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
							</span>
							<a href="mailto:<?php echo esc_attr( $contact_email ); ?>"><?php echo esc_html( $contact_email ); ?></a>
						</li>
					<?php endif; ?>

					<?php if ( ! empty( $contact_phone ) ) : ?>
						<li class="jp-footer-contact-item">
							<span class="jp-footer-icon-wrap">
								<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
							</span>
							<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $contact_phone ) ); ?>"><?php echo esc_html( $contact_phone ); ?></a>
						</li>
					<?php endif; ?>

					<?php if ( ! empty( $contact_location ) ) : ?>
						<li class="jp-footer-contact-item">
							<span class="jp-footer-icon-wrap">
								<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
							</span>
							<span><?php echo esc_html( $contact_location ); ?></span>
						</li>
					<?php endif; ?>
				</ul>
			</div>

			<!-- Column 2: Follow Me (2-Column Grid with Brand Icons & Names) -->
			<div class="jp-footer-col">
				<?php if ( ! empty( $col2_title ) ) : ?>
					<h4 class="jp-footer-col-title"><?php echo esc_html( $col2_title ); ?></h4>
				<?php endif; ?>

				<?php if ( ! empty( $social_links ) ) : ?>
					<div class="jp-footer-social-grid-2col">
						<?php foreach ( $social_links as $item ) : ?>
							<?php
							$name        = isset( $item['name'] ) ? $item['name'] : 'Social';
							$url         = isset( $item['url'] ) ? $item['url'] : '#';
							$brand_class = jp_get_social_brand_class( $name, isset( $item['icon'] ) ? $item['icon'] : '' );
							?>
							<a href="<?php echo esc_url( $url ); ?>" class="jp-social-item-link" target="_blank" rel="noopener noreferrer">
								<span class="jp-social-badge-icon <?php echo esc_attr( $brand_class ); ?>">
									<?php if ( strpos( $brand_class, 'twitter' ) !== false ) : ?>
										<svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
									<?php elseif ( strpos( $brand_class, 'facebook' ) !== false ) : ?>
										<svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
									<?php elseif ( strpos( $brand_class, 'linkedin' ) !== false ) : ?>
										<svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.239-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
									<?php elseif ( strpos( $brand_class, 'youtube' ) !== false ) : ?>
										<svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/></svg>
									<?php elseif ( strpos( $brand_class, 'instagram' ) !== false ) : ?>
										<svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
									<?php else : ?>
										<span class="dashicons dashicons-share"></span>
									<?php endif; ?>
								</span>
								<span><?php echo esc_html( $name ); ?></span>
							</a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>

			<!-- Column 3: Quick Links (2-Column Subgrid) -->
			<div class="jp-footer-col">
				<?php if ( ! empty( $col3_title ) ) : ?>
					<h4 class="jp-footer-col-title"><?php echo esc_html( $col3_title ); ?></h4>
				<?php endif; ?>

				<?php if ( ! empty( $menu_id ) && is_numeric( $menu_id ) ) : ?>
					<?php
					wp_nav_menu(
						array(
							'menu'        => (int) $menu_id,
							'container'   => false,
							'menu_class'  => 'jp-footer-nav-2col',
							'depth'       => 1,
							'fallback_cb' => false,
						)
					);
					?>
				<?php else : ?>
					<?php
					$footer_pages = get_pages(
						array(
							'post_status' => 'publish',
							'sort_column' => 'menu_order,post_title',
							'number'      => 8,
						)
					);
					?>
					<ul class="jp-footer-nav-2col">
						<?php if ( ! empty( $footer_pages ) ) : ?>
							<?php foreach ( $footer_pages as $page_obj ) : ?>
								<li>
									<a href="<?php echo esc_url( get_permalink( $page_obj->ID ) ); ?>">
										<?php echo esc_html( $page_obj->post_title ); ?>
									</a>
								</li>
							<?php endforeach; ?>
						<?php else : ?>
							<li><a href="<?php echo esc_url( home_url( '/home' ) ); ?>"><?php esc_html_e( 'Home', 'journalist-portfolio-hub' ); ?></a></li>
							<li><a href="<?php echo esc_url( home_url( '/about' ) ); ?>"><?php esc_html_e( 'About', 'journalist-portfolio-hub' ); ?></a></li>
							<li><a href="<?php echo esc_url( home_url( '/stories' ) ); ?>"><?php esc_html_e( 'Stories', 'journalist-portfolio-hub' ); ?></a></li>
							<li><a href="<?php echo esc_url( home_url( '/photos' ) ); ?>"><?php esc_html_e( 'Photos', 'journalist-portfolio-hub' ); ?></a></li>
							<li><a href="<?php echo esc_url( home_url( '/videos' ) ); ?>"><?php esc_html_e( 'Videos', 'journalist-portfolio-hub' ); ?></a></li>
							<li><a href="<?php echo esc_url( home_url( '/awards' ) ); ?>"><?php esc_html_e( 'Awards', 'journalist-portfolio-hub' ); ?></a></li>
							<li><a href="<?php echo esc_url( home_url( '/contact' ) ); ?>"><?php esc_html_e( 'Contact', 'journalist-portfolio-hub' ); ?></a></li>
						<?php endif; ?>
					</ul>
				<?php endif; ?>
			</div>

			<!-- Column 4: Downloads (Clean Document Icon + Label List) -->
			<div class="jp-footer-col">
				<?php if ( ! empty( $col4_title ) ) : ?>
					<h4 class="jp-footer-col-title"><?php echo esc_html( $col4_title ); ?></h4>
				<?php endif; ?>

				<div class="jp-footer-download-vertical">
					<?php if ( ! empty( $media_kit_file ) ) : ?>
						<a href="<?php echo esc_url( $media_kit_file ); ?>" class="jp-footer-download-item" target="_blank" rel="noopener noreferrer" download>
							<span class="jp-download-item-icon">
								<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
									<path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
									<polyline points="14 2 14 8 20 8"/>
									<circle cx="12" cy="13" r="2"/>
									<path d="M12 15v3"/>
								</svg>
							</span>
							<span><?php echo esc_html( $media_kit_label ); ?></span>
						</a>
					<?php endif; ?>

					<?php if ( ! empty( $cv_file ) ) : ?>
						<a href="<?php echo esc_url( $cv_file ); ?>" class="jp-footer-download-item" target="_blank" rel="noopener noreferrer" download>
							<span class="jp-download-item-icon">
								<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
									<path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
									<polyline points="14 2 14 8 20 8"/>
									<circle cx="12" cy="13" r="2"/>
									<path d="M12 15v3"/>
								</svg>
							</span>
							<span><?php echo esc_html( $cv_label ); ?></span>
						</a>
					<?php endif; ?>
				</div>
			</div>

		</div>

		<!-- Footer Bottom Bar -->
		<div class="jp-footer-bottom">
			<div class="jp-footer-copyright">
				<?php echo esc_html( $copyright_text ); ?>
			</div>

			<?php if ( ! empty( $tagline ) ) : ?>
				<div class="jp-footer-tagline">
					<?php echo esc_html( $tagline ); ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</footer>
