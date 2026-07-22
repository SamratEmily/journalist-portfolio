<?php
/**
 * About Page Template for Journalist Portfolio Hub.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require JP_HUB_PLUGIN_DIR . 'templates/header-nav.php';

$full_name        = get_option( 'jp_full_name', 'About Journalist' );
$designation      = get_option( 'jp_designation', 'Investigative & Environmental Journalist' );
$profile_image    = get_option( 'jp_profile_image', '' );
$bio_text         = get_option( 'jp_bio_text', 'Welcome to my journalist portfolio. I specialize in deep investigative journalism, human rights reporting, and environmental features.' );

$contact_phone    = get_option( 'jp_contact_phone', get_option( 'jp_footer_contact_phone', '' ) );
$contact_email    = get_option( 'jp_contact_email', get_option( 'jp_footer_contact_email', '' ) );
$contact_location = get_option( 'jp_contact_location', get_option( 'jp_footer_contact_location', '' ) );
?>

<main class="jp-section">
	<div class="jp-container">
		<div style="margin-bottom: 20px;">
			<h1 class="jp-section-title" style="font-size: 2.4rem;"><?php esc_html_e( 'About & Background', 'journalist-portfolio-hub' ); ?></h1>
			<p class="jp-section-subtitle"><?php esc_html_e( 'Professional background, reporting focus, awards, and journalistic mission.', 'journalist-portfolio-hub' ); ?></p>
		</div>

		<div class="jp-about-layout">
			<!-- Profile Sidebar Image & Info -->
			<div class="jp-about-sidebar">
				<?php if ( ! empty( $profile_image ) ) : ?>
					<img src="<?php echo esc_url( $profile_image ); ?>" alt="<?php echo esc_attr( $full_name ); ?>" class="jp-about-image">
				<?php else : ?>
					<div class="jp-about-image" style="background: #334155; display: flex; align-items: center; justify-content: center; color: #94a3b8;">
						<span class="dashicons dashicons-admin-users" style="font-size: 80px; width: 80px; height: 80px;"></span>
					</div>
				<?php endif; ?>

				<h2 class="jp-about-name"><?php echo esc_html( $full_name ); ?></h2>
				<div class="jp-about-designation"><?php echo esc_html( $designation ); ?></div>

				<?php if ( ! empty( $contact_phone ) || ! empty( $contact_email ) || ! empty( $contact_location ) ) : ?>
					<div style="margin-top: 24px; padding-top: 18px; border-top: 1px solid #e2e8f0; text-align: left; font-size: 0.9rem; color: #475569;">
						<?php if ( ! empty( $contact_phone ) ) : ?>
							<p style="margin: 0 0 8px 0; display: flex; align-items: center; gap: 8px;">
								<span class="dashicons dashicons-phone" style="font-size: 16px; color: #059669;"></span>
								<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $contact_phone ) ); ?>" style="color: inherit; text-decoration: none;"><?php echo esc_html( $contact_phone ); ?></a>
							</p>
						<?php endif; ?>

						<?php if ( ! empty( $contact_email ) ) : ?>
							<p style="margin: 0 0 8px 0; display: flex; align-items: center; gap: 8px;">
								<span class="dashicons dashicons-email" style="font-size: 16px; color: #059669;"></span>
								<a href="mailto:<?php echo esc_attr( $contact_email ); ?>" style="color: inherit; text-decoration: none;"><?php echo esc_html( $contact_email ); ?></a>
							</p>
						<?php endif; ?>

						<?php if ( ! empty( $contact_location ) ) : ?>
							<p style="margin: 0; display: flex; align-items: center; gap: 8px;">
								<span class="dashicons dashicons-location" style="font-size: 16px; color: #059669;"></span>
								<span><?php echo esc_html( $contact_location ); ?></span>
							</p>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>

			<!-- Full Bio Content -->
			<div class="jp-about-bio-content">
				<h3 style="font-size: 1.5rem; color: #0f172a; margin-top: 0; margin-bottom: 16px; font-weight: 700;"><?php esc_html_e( 'Biography', 'journalist-portfolio-hub' ); ?></h3>
				<?php echo wp_kses_post( wpautop( $bio_text ) ); ?>

				<div style="margin-top: 40px; padding-top: 24px; border-top: 1px solid #e2e8f0;">
					<a href="<?php echo esc_url( home_url( '/contact' ) ); ?>" class="jp-cta-btn">
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
						<span><?php esc_html_e( 'Get In Touch / Contact', 'journalist-portfolio-hub' ); ?></span>
					</a>
				</div>
			</div>
		</div>
	</div>
</main>

<?php require JP_HUB_PLUGIN_DIR . 'templates/footer.php'; ?>
