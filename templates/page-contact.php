<?php
/**
 * Contact Page Template — Journalist Portfolio Hub.
 *
 * Rendered at /contact via Template_Loader or via [jp_contact_page] shortcode.
 * Uses Contact_Handler::AJAX_ACTION / NONCE_ACTION constants for secure submission.
 *
 * @package JournalistPortfolio
 * @since   1.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Only output the full <html> shell when rendered as a standalone page template,
// not when embedded via the shortcode (which runs inside a page that already has a shell).
// The Contact_Handler sets $jp_contact_is_shortcode = true before requiring this file.
$is_standalone = empty( $jp_contact_is_shortcode );

if ( $is_standalone ) {
	require JP_HUB_PLUGIN_DIR . 'templates/header-nav.php';
}

// ── Pull settings from the plugin options ────────────────────────────────────
$full_name   = get_option( 'jp_full_name', get_bloginfo( 'name' ) );
$designation = get_option( 'jp_designation', '' );

// Contact details — try footer keys first (they are newer), fall back to general keys.
$contact_email = get_option( 'jp_footer_contact_email', '' );
if ( empty( $contact_email ) ) {
	$contact_email = get_option( 'jp_contact_email', '' );
}

$contact_phone = get_option( 'jp_footer_contact_phone', '' );
if ( empty( $contact_phone ) ) {
	$contact_phone = get_option( 'jp_contact_phone', '' );
}

$contact_location = get_option( 'jp_footer_contact_location', '' );
if ( empty( $contact_location ) ) {
	$contact_location = get_option( 'jp_contact_location', '' );
}

// Social links.
$social_twitter   = get_option( 'jp_social_twitter', '' );
$social_linkedin  = get_option( 'jp_social_linkedin', '' );
$social_youtube   = get_option( 'jp_social_youtube', '' );
$social_facebook  = get_option( 'jp_social_facebook', '' );
$social_medium    = get_option( 'jp_social_medium', '' );
$social_instagram = get_option( 'jp_social_instagram', '' );

// Download files.
$cv_file      = get_option( 'jp_footer_cv_file', get_option( 'jp_cv_file', '' ) );
$cv_label     = get_option( 'jp_cv_button_label', __( 'Download CV', 'journalist-portfolio-hub' ) );
$mk_file      = get_option( 'jp_footer_mediakit_file', get_option( 'jp_media_kit_file', '' ) );
$mk_label     = get_option( 'jp_media_kit_label', __( 'Download Media Kit', 'journalist-portfolio-hub' ) );
$profile_img  = get_option( 'jp_profile_image', '' );

// AJAX URL and nonce for the form.
$ajax_url  = admin_url( 'admin-ajax.php' );
$nonce_val = wp_create_nonce( \JournalistPortfolio\Contact_Handler::NONCE_ACTION );
$nonce_fld = \JournalistPortfolio\Contact_Handler::NONCE_FIELD;
$ajax_act  = \JournalistPortfolio\Contact_Handler::AJAX_ACTION;
?>

<main class="jp-contact-main">
	<div class="jp-container">

		<!-- Page Header -->
		<div class="jp-contact-page-header">
			<span class="jp-contact-page-badge"><?php esc_html_e( 'Get in Touch', 'journalist-portfolio-hub' ); ?></span>
			<h1 class="jp-contact-page-title"><?php esc_html_e( "Let's Connect", 'journalist-portfolio-hub' ); ?></h1>
			<p class="jp-contact-page-subtitle"><?php esc_html_e( 'For story ideas, collaborations, interviews, speaking engagements, and media inquiries.', 'journalist-portfolio-hub' ); ?></p>
		</div>

		<!-- 2-Column Grid: Info Card + Contact Form -->
		<div class="jp-contact-grid">

			<!-- ══ LEFT COLUMN — Author Info Card ═══════════════════════════ -->
			<aside class="jp-contact-info-card">

				<!-- Author Intro -->
				<div class="jp-contact-author-intro">
					<?php if ( ! empty( $profile_img ) ) : ?>
						<img
							src="<?php echo esc_url( $profile_img ); ?>"
							alt="<?php echo esc_attr( $full_name ); ?>"
							class="jp-contact-avatar"
						>
					<?php else : ?>
						<div class="jp-contact-avatar jp-contact-avatar-placeholder">
							<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
								<circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="1.5"/>
								<path d="M4 20c0-4 3.582-7 8-7s8 3 8 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
							</svg>
						</div>
					<?php endif; ?>

					<div class="jp-contact-author-text">
						<h2 class="jp-contact-author-name"><?php echo esc_html( $full_name ); ?></h2>
						<?php if ( ! empty( $designation ) ) : ?>
							<span class="jp-contact-author-role"><?php echo esc_html( $designation ); ?></span>
						<?php endif; ?>
					</div>
				</div>

				<!-- Divider -->
				<hr class="jp-contact-divider">

				<!-- Contact Details -->
				<ul class="jp-contact-details-list">

					<?php if ( ! empty( $contact_email ) ) : ?>
					<li class="jp-contact-details-item">
						<span class="jp-contact-icon-wrap">
							<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
								<rect x="2" y="4" width="20" height="16" rx="2" stroke="currentColor" stroke-width="1.5"/>
								<path d="M2 7l10 7 10-7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
							</svg>
						</span>
						<div class="jp-contact-details-text">
							<span class="jp-contact-details-label"><?php esc_html_e( 'Email', 'journalist-portfolio-hub' ); ?></span>
							<a href="mailto:<?php echo esc_attr( $contact_email ); ?>" class="jp-contact-details-value">
								<?php echo esc_html( $contact_email ); ?>
							</a>
						</div>
					</li>
					<?php endif; ?>

					<?php if ( ! empty( $contact_phone ) ) : ?>
					<li class="jp-contact-details-item">
						<span class="jp-contact-icon-wrap">
							<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
								<path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z" fill="currentColor"/>
							</svg>
						</span>
						<div class="jp-contact-details-text">
							<span class="jp-contact-details-label"><?php esc_html_e( 'Phone', 'journalist-portfolio-hub' ); ?></span>
							<a href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $contact_phone ) ); ?>" class="jp-contact-details-value">
								<?php echo esc_html( $contact_phone ); ?>
							</a>
						</div>
					</li>
					<?php endif; ?>

					<?php if ( ! empty( $contact_location ) ) : ?>
					<li class="jp-contact-details-item">
						<span class="jp-contact-icon-wrap">
							<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
								<path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" stroke="currentColor" stroke-width="1.5"/>
								<circle cx="12" cy="9" r="2.5" stroke="currentColor" stroke-width="1.5"/>
							</svg>
						</span>
						<div class="jp-contact-details-text">
							<span class="jp-contact-details-label"><?php esc_html_e( 'Location', 'journalist-portfolio-hub' ); ?></span>
							<span class="jp-contact-details-value"><?php echo esc_html( $contact_location ); ?></span>
						</div>
					</li>
					<?php endif; ?>

				</ul>

				<!-- Social Profiles -->
				<?php
				$socials = array(
					'twitter'   => array( 'label' => 'Twitter / X', 'url' => $social_twitter, 'icon' => '<svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.741l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>' ),
					'linkedin'  => array( 'label' => 'LinkedIn', 'url' => $social_linkedin, 'icon' => '<svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>' ),
					'youtube'   => array( 'label' => 'YouTube', 'url' => $social_youtube, 'icon' => '<svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M23.495 6.205a3.007 3.007 0 00-2.088-2.088c-1.87-.501-9.396-.501-9.396-.501s-7.507-.01-9.396.501A3.007 3.007 0 00.527 6.205a31.247 31.247 0 00-.522 5.805 31.247 31.247 0 00.522 5.783 3.007 3.007 0 002.088 2.088c1.868.502 9.396.502 9.396.502s7.506 0 9.396-.502a3.007 3.007 0 002.088-2.088 31.247 31.247 0 00.5-5.783 31.247 31.247 0 00-.5-5.805zM9.609 15.601V8.408l6.264 3.602z"/></svg>' ),
					'facebook'  => array( 'label' => 'Facebook', 'url' => $social_facebook, 'icon' => '<svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>' ),
					'medium'    => array( 'label' => 'Medium', 'url' => $social_medium, 'icon' => '<svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M13.54 12a6.8 6.8 0 01-6.77 6.82A6.8 6.8 0 010 12a6.8 6.8 0 016.77-6.82A6.8 6.8 0 0113.54 12zM20.96 12c0 3.54-1.51 6.42-3.38 6.42-1.87 0-3.39-2.88-3.39-6.42s1.52-6.42 3.39-6.42 3.38 2.88 3.38 6.42M24 12c0 3.17-.53 5.75-1.19 5.75-.66 0-1.19-2.58-1.19-5.75s.53-5.75 1.19-5.75C23.47 6.25 24 8.83 24 12z"/></svg>' ),
					'instagram' => array( 'label' => 'Instagram', 'url' => $social_instagram, 'icon' => '<svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>' ),
				);

				$has_socials = array_filter( $socials, fn( $s ) => ! empty( $s['url'] ) && '#' !== $s['url'] );
				?>
				<?php if ( ! empty( $has_socials ) ) : ?>
				<div class="jp-contact-social-section">
					<span class="jp-contact-social-label"><?php esc_html_e( 'Follow Me', 'journalist-portfolio-hub' ); ?></span>
					<div class="jp-contact-social-row">
						<?php foreach ( $socials as $key => $social ) : ?>
							<?php if ( ! empty( $social['url'] ) && '#' !== $social['url'] ) : ?>
								<a
									href="<?php echo esc_url( $social['url'] ); ?>"
									class="jp-contact-social-btn jp-contact-social-<?php echo esc_attr( $key ); ?>"
									target="_blank"
									rel="noopener noreferrer"
									aria-label="<?php echo esc_attr( $social['label'] ); ?>"
								>
									<?php echo $social['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG is hardcoded above. ?>
								</a>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
				</div>
				<?php endif; ?>

				<!-- Download Buttons -->
				<?php if ( ! empty( $cv_file ) || ! empty( $mk_file ) ) : ?>
				<div class="jp-contact-downloads">
					<span class="jp-contact-downloads-label"><?php esc_html_e( 'Quick Download', 'journalist-portfolio-hub' ); ?></span>
					<div class="jp-contact-download-row">
						<?php if ( ! empty( $cv_file ) ) : ?>
							<a
								href="<?php echo esc_url( $cv_file ); ?>"
								class="jp-contact-download-btn"
								download
								target="_blank"
								rel="noopener noreferrer"
							>
								<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M12 16l-4-4h2.5V4h3v8H16l-4 4z" fill="currentColor"/><path d="M4 18h16v2H4v-2z" fill="currentColor"/></svg>
								<?php echo esc_html( $cv_label ?: __( 'Download CV', 'journalist-portfolio-hub' ) ); ?>
							</a>
						<?php endif; ?>
						<?php if ( ! empty( $mk_file ) ) : ?>
							<a
								href="<?php echo esc_url( $mk_file ); ?>"
								class="jp-contact-download-btn jp-contact-download-btn--outline"
								download
								target="_blank"
								rel="noopener noreferrer"
							>
								<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M12 16l-4-4h2.5V4h3v8H16l-4 4z" fill="currentColor"/><path d="M4 18h16v2H4v-2z" fill="currentColor"/></svg>
								<?php echo esc_html( $mk_label ?: __( 'Download Media Kit', 'journalist-portfolio-hub' ) ); ?>
							</a>
						<?php endif; ?>
					</div>
				</div>
				<?php endif; ?>

			</aside><!-- /left column -->

			<!-- ══ RIGHT COLUMN — Contact Form ══════════════════════════════ -->
			<div class="jp-contact-form-wrap">

				<div class="jp-contact-form-header">
					<h2 class="jp-contact-form-title"><?php esc_html_e( 'Send a Message', 'journalist-portfolio-hub' ); ?></h2>
					<p class="jp-contact-form-desc"><?php esc_html_e( "Fill in the form below and I'll respond within 24–48 hours.", 'journalist-portfolio-hub' ); ?></p>
				</div>

				<!-- Alert notice area -->
				<div id="jp-contact-notice" class="jp-contact-notice" role="alert" aria-live="polite" style="display:none;"></div>

				<form
					id="jp-contact-form"
					class="jp-contact-form"
					method="post"
					novalidate
				>
					<!-- Security nonce -->
					<input type="hidden" name="action" value="<?php echo esc_attr( $ajax_act ); ?>">
					<input type="hidden" name="<?php echo esc_attr( $nonce_fld ); ?>" value="<?php echo esc_attr( $nonce_val ); ?>">

					<!-- Honeypot anti-spam field — hidden from humans via CSS -->
					<div class="jp-contact-honeypot" aria-hidden="true" tabindex="-1">
						<label for="jp_website_url"><?php esc_html_e( 'Leave this field blank', 'journalist-portfolio-hub' ); ?></label>
						<input
							type="text"
							id="jp_website_url"
							name="jp_website_url"
							autocomplete="off"
							tabindex="-1"
						>
					</div>

					<!-- Row 1: Name + Email -->
					<div class="jp-contact-form-row jp-contact-form-row--2col">

						<div class="jp-contact-field-wrap">
							<label class="jp-contact-label" for="jp-contact-name">
								<?php esc_html_e( 'Full Name', 'journalist-portfolio-hub' ); ?>
								<span class="jp-required" aria-label="required">*</span>
							</label>
							<div class="jp-contact-input-wrap">
								<!-- <span class="jp-contact-field-icon">
									<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="1.5"/><path d="M4 20c0-4 3.582-7 8-7s8 3 8 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
								</span> -->
								<input
									type="text"
									id="jp-contact-name"
									name="contact_name"
									class="jp-contact-input"
									placeholder="<?php esc_attr_e( 'Your full name', 'journalist-portfolio-hub' ); ?>"
									required
									minlength="2"
									maxlength="100"
									autocomplete="name"
								>
							</div>
						</div>

						<div class="jp-contact-field-wrap">
							<label class="jp-contact-label" for="jp-contact-email">
								<?php esc_html_e( 'Email Address', 'journalist-portfolio-hub' ); ?>
								<span class="jp-required" aria-label="required">*</span>
							</label>
							<div class="jp-contact-input-wrap">
								<!-- <span class="jp-contact-field-icon">
									<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><rect x="2" y="4" width="20" height="16" rx="2" stroke="currentColor" stroke-width="1.5"/><path d="M2 7l10 7 10-7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
								</span> -->
								<input
									type="email"
									id="jp-contact-email"
									name="contact_email"
									class="jp-contact-input"
									placeholder="<?php esc_attr_e( 'you@example.com', 'journalist-portfolio-hub' ); ?>"
									required
									maxlength="200"
									autocomplete="email"
								>
							</div>
						</div>

					</div><!-- /row 1 -->

					<!-- Row 2: Subject -->
					<div class="jp-contact-form-row">
						<div class="jp-contact-field-wrap">
							<label class="jp-contact-label" for="jp-contact-subject">
								<?php esc_html_e( 'Subject', 'journalist-portfolio-hub' ); ?>
								<span class="jp-required" aria-label="required">*</span>
							</label>
							<div class="jp-contact-input-wrap">
								<!-- <span class="jp-contact-field-icon">
									<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M4 6h16M4 10h10M4 14h7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
								</span> -->
								<input
									type="text"
									id="jp-contact-subject"
									name="contact_subject"
									class="jp-contact-input"
									placeholder="<?php esc_attr_e( 'Story pitch, collaboration, interview request…', 'journalist-portfolio-hub' ); ?>"
									required
									minlength="3"
									maxlength="200"
								>
							</div>
						</div>
					</div><!-- /row 2 -->

					<!-- Row 3: Message -->
					<div class="jp-contact-form-row">
						<div class="jp-contact-field-wrap">
							<label class="jp-contact-label" for="jp-contact-message">
								<?php esc_html_e( 'Message', 'journalist-portfolio-hub' ); ?>
								<span class="jp-required" aria-label="required">*</span>
							</label>
							<textarea
								id="jp-contact-message"
								name="contact_message"
								class="jp-contact-textarea"
								rows="6"
								placeholder="<?php esc_attr_e( 'Please describe your inquiry in detail…', 'journalist-portfolio-hub' ); ?>"
								required
								minlength="10"
								maxlength="5000"
							></textarea>
							<div class="jp-contact-char-count">
								<span id="jp-char-count">0</span> / 5000
							</div>
						</div>
					</div><!-- /row 3 -->

					<!-- Submit -->
					<div class="jp-contact-form-footer">
						<p class="jp-contact-privacy-note">
							<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" width="14" height="14"><path d="M12 2L4 5v6c0 5.25 3.4 10.17 8 11.38C16.6 21.17 20 16.25 20 11V5l-8-3z" stroke="currentColor" stroke-width="1.5"/></svg>
							<?php esc_html_e( 'Your information is never shared with third parties.', 'journalist-portfolio-hub' ); ?>
						</p>

						<button
							type="submit"
							id="jp-contact-submit"
							class="jp-contact-submit-btn"
						>
							<span class="jp-contact-btn-text"><?php esc_html_e( 'Send Message', 'journalist-portfolio-hub' ); ?></span>
							<span class="jp-contact-btn-spinner" aria-hidden="true"></span>
							<svg class="jp-contact-btn-arrow" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
								<path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
						</button>
					</div>

				</form><!-- /jp-contact-form -->

			</div><!-- /right column -->

		</div><!-- /jp-contact-grid -->

	</div><!-- /jp-container -->
</main><!-- /jp-contact-main -->

<script>
(function () {
	'use strict';

	var form     = document.getElementById( 'jp-contact-form' );
	var notice   = document.getElementById( 'jp-contact-notice' );
	var submit   = document.getElementById( 'jp-contact-submit' );
	var textarea = document.getElementById( 'jp-contact-message' );
	var charCnt  = document.getElementById( 'jp-char-count' );

	if ( ! form ) return;

	// Character counter for the textarea.
	if ( textarea && charCnt ) {
		textarea.addEventListener( 'input', function () {
			charCnt.textContent = this.value.length;
		} );
	}

	// Show an alert notice.
	function showNotice( message, type ) {
		notice.innerHTML   = '<span class="jp-notice-icon">' + ( type === 'success' ? '✓' : '✕' ) + '</span>' + message;
		notice.className   = 'jp-contact-notice jp-contact-notice--' + type;
		notice.style.display = 'flex';
		notice.scrollIntoView( { behavior: 'smooth', block: 'nearest' } );
	}

	// Toggle loading state.
	function setLoading( loading ) {
		submit.disabled = loading;
		submit.classList.toggle( 'is-loading', loading );
	}

	// Handle form submission via Fetch API.
	form.addEventListener( 'submit', function ( e ) {
		e.preventDefault();
		notice.style.display = 'none';
		setLoading( true );

		var data = new FormData( this );

		fetch( '<?php echo esc_url( $ajax_url ); ?>', {
			method  : 'POST',
			body    : data,
			credentials: 'same-origin',
		} )
		.then( function ( response ) { return response.json(); } )
		.then( function ( json ) {
			setLoading( false );
			if ( json.success ) {
				showNotice( json.data.message, 'success' );
				form.reset();
				if ( charCnt ) charCnt.textContent = '0';
			} else {
				var msg = ( json.data && json.data.message ) ? json.data.message : '<?php echo esc_js( __( 'Something went wrong. Please try again.', 'journalist-portfolio-hub' ) ); ?>';
				showNotice( msg, 'error' );
			}
		} )
		.catch( function () {
			setLoading( false );
			showNotice( '<?php echo esc_js( __( 'A network error occurred. Please check your connection and try again.', 'journalist-portfolio-hub' ) ); ?>', 'error' );
		} );
	} );
}());
</script>

<?php if ( $is_standalone ) : ?>
	<?php require JP_HUB_PLUGIN_DIR . 'templates/footer.php'; ?>
<?php endif; ?>
