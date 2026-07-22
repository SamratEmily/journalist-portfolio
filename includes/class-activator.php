<?php
namespace JournalistPortfolio;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles plugin activation, page creation, and rewrite rule flushing.
 */
class Activator {

	/**
	 * Activate plugin action.
	 */
	public static function activate(): void {
		// Ensure CPTs and Taxonomies are registered before flushing rewrite rules.
		CPT_Stories::register_post_type_and_taxonomy();
		CPT_Awards::register_post_type();

		// Auto-generate required pages.
		self::create_required_pages();

		// Seed Demo Stories, Categories, Publications, and Featured Images.
		Demo_Seeder::seed();

		// Flush rewrite rules to prevent 404 on custom post types.
		flush_rewrite_rules();
	}

	/**
	 * Deactivate plugin action.
	 */
	public static function deactivate(): void {
		flush_rewrite_rules();
	}

	/**
	 * Programmatically create default portfolio pages if missing.
	 */
	private static function create_required_pages(): void {
		$pages = array(
			'home'       => array(
				'title'   => 'Home',
				'content' => '<!-- Journalist Portfolio Home -->',
			),
			'about'      => array(
				'title'   => 'About',
				'content' => '<!-- Journalist Portfolio About -->',
			),
			'stories'    => array(
				'title'   => 'Stories',
				'content' => '<!-- Journalist Portfolio Stories -->',
			),
			'multimedia' => array(
				'title'   => 'Multimedia',
				'content' => '<!-- Journalist Portfolio Multimedia -->',
			),
			'awards'     => array(
				'title'   => 'Awards',
				'content' => '<!-- Journalist Portfolio Awards -->',
			),
			'contact'    => array(
				'title'   => 'Contact',
				'content' => '<!-- Journalist Portfolio Contact -->',
			),
		);

		$home_page_id = 0;

		foreach ( $pages as $slug => $data ) {
			// Check if page exists by slug or post object.
			$existing_page = get_page_by_path( $slug, OBJECT, 'page' );

			if ( ! $existing_page ) {
				$page_id = wp_insert_post(
					array(
						'post_title'     => $data['title'],
						'post_name'      => $slug,
						'post_content'   => $data['content'],
						'post_status'    => 'publish',
						'post_type'      => 'page',
						'comment_status' => 'closed',
					)
				);

				if ( 'home' === $slug && $page_id && ! is_wp_error( $page_id ) ) {
					$home_page_id = $page_id;
				}
			} else {
				if ( 'home' === $slug ) {
					$home_page_id = $existing_page->ID;
				}
			}
		}

		// Configure static front page settings.
		if ( $home_page_id > 0 ) {
			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', $home_page_id );
		}
	}
}
