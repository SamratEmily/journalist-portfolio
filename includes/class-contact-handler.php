<?php
namespace JournalistPortfolio;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Contact Form Handler.
 *
 * Registers the [jp_contact_page] shortcode, handles AJAX form submissions
 * with nonce verification, honeypot anti-spam, full input sanitization,
 * and sends the message via wp_mail().
 *
 * @package JournalistPortfolio
 * @since   1.3.0
 */
class Contact_Handler {

	/**
	 * Singleton instance.
	 *
	 * @var Contact_Handler|null
	 */
	private static ?Contact_Handler $instance = null;

	/**
	 * AJAX action name.
	 */
	const AJAX_ACTION = 'jp_submit_contact';

	/**
	 * Nonce action name.
	 */
	const NONCE_ACTION = 'jp_contact_nonce_action';

	/**
	 * Nonce field name.
	 */
	const NONCE_FIELD = 'jp_contact_nonce';

	/**
	 * Get singleton instance.
	 *
	 * @return Contact_Handler
	 */
	public static function get_instance(): Contact_Handler {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor — registers hooks.
	 */
	private function __construct() {
		// Shortcode.
		add_shortcode( 'jp_contact_page', array( $this, 'render_contact_page' ) );

		// AJAX handlers (logged-in and logged-out users).
		add_action( 'wp_ajax_' . self::AJAX_ACTION, array( $this, 'handle_ajax_submission' ) );
		add_action( 'wp_ajax_nopriv_' . self::AJAX_ACTION, array( $this, 'handle_ajax_submission' ) );
	}

	/**
	 * Render the full contact page section (shortcode output).
	 *
	 * @param array|string $atts Shortcode attributes (unused).
	 * @return string HTML output.
	 */
	public function render_contact_page( $atts = array() ): string {
		// Flag consumed by page-contact.php to suppress <html> wrapper when shortcode-embedded.
		$jp_contact_is_shortcode = true; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
		ob_start();
		require JP_HUB_PLUGIN_DIR . 'templates/page-contact.php';
		return ob_get_clean();
	}

	/**
	 * Handle AJAX form submission.
	 *
	 * Performs nonce verification, honeypot check, sanitization,
	 * validation, and sends email via wp_mail().
	 *
	 * @return void Sends JSON response and exits.
	 */
	public function handle_ajax_submission(): void {
		// 1. Nonce verification — abort immediately if it fails.
		if (
			! isset( $_POST[ self::NONCE_FIELD ] ) ||
			! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ self::NONCE_FIELD ] ) ), self::NONCE_ACTION )
		) {
			wp_send_json_error(
				array( 'message' => __( 'Security check failed. Please refresh the page and try again.', 'journalist-portfolio-hub' ) ),
				403
			);
		}

		// 2. Honeypot anti-spam check — bots fill hidden fields, humans don't.
		if ( ! empty( $_POST['jp_website_url'] ) ) {
			// Silently succeed to confuse bots.
			wp_send_json_success( array( 'message' => __( 'Thank you! Your message has been sent.', 'journalist-portfolio-hub' ) ) );
		}

		// 3. Sanitize all inputs.
		$name    = sanitize_text_field( wp_unslash( $_POST['contact_name'] ?? '' ) );
		$email   = sanitize_email( wp_unslash( $_POST['contact_email'] ?? '' ) );
		$subject = sanitize_text_field( wp_unslash( $_POST['contact_subject'] ?? '' ) );
		$message = sanitize_textarea_field( wp_unslash( $_POST['contact_message'] ?? '' ) );

		// 4. Server-side field validation.
		$errors = array();

		if ( empty( $name ) || mb_strlen( $name ) < 2 ) {
			$errors[] = __( 'Please enter your full name (at least 2 characters).', 'journalist-portfolio-hub' );
		}

		if ( empty( $email ) || ! is_email( $email ) ) {
			$errors[] = __( 'Please enter a valid email address.', 'journalist-portfolio-hub' );
		}

		if ( empty( $subject ) || mb_strlen( $subject ) < 3 ) {
			$errors[] = __( 'Please enter a subject (at least 3 characters).', 'journalist-portfolio-hub' );
		}

		if ( empty( $message ) || mb_strlen( $message ) < 10 ) {
			$errors[] = __( 'Your message must be at least 10 characters long.', 'journalist-portfolio-hub' );
		}

		if ( ! empty( $errors ) ) {
			wp_send_json_error( array( 'message' => implode( ' ', $errors ) ), 422 );
		}

		// 5. Determine recipient — prefer the admin's registered email.
		$recipient = get_option( 'jp_footer_contact_email', '' );
		if ( empty( $recipient ) || ! is_email( $recipient ) ) {
			$recipient = get_option( 'jp_contact_email', get_option( 'admin_email' ) );
		}

		// 6. Build email.
		$site_name    = get_bloginfo( 'name' );
		$mail_subject = sprintf(
			/* translators: 1: site name, 2: subject line from user */
			__( '[%1$s] New Contact Message: %2$s', 'journalist-portfolio-hub' ),
			$site_name,
			$subject
		);

		$mail_body = sprintf(
			"You have received a new message from your journalist portfolio contact form.\n\n" .
			"-----------------------------------------------------------\n" .
			"Name:    %s\n" .
			"Email:   %s\n" .
			"Subject: %s\n" .
			"-----------------------------------------------------------\n\n" .
			"%s\n\n" .
			"-----------------------------------------------------------\n" .
			"Sent via: %s\n" .
			"IP Address: %s\n",
			$name,
			$email,
			$subject,
			$message,
			home_url(),
			sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ?? 'unknown' ) )
		);

		$mail_headers = array(
			'Content-Type: text/plain; charset=UTF-8',
			sprintf( 'Reply-To: %s <%s>', $name, $email ),
		);

		// 7. Send email.
		$sent = wp_mail( $recipient, $mail_subject, $mail_body, $mail_headers );

		if ( $sent ) {
			wp_send_json_success(
				array(
					'message' => __( 'Thank you! Your message has been sent successfully. I\'ll get back to you as soon as possible.', 'journalist-portfolio-hub' ),
				)
			);
		} else {
			wp_send_json_error(
				array(
					'message' => __( 'Sorry, something went wrong sending your message. Please try contacting me directly via email.', 'journalist-portfolio-hub' ),
				),
				500
			);
		}
	}
}
