<?php
namespace JournalistPortfolio;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles seeding demo publications, categories, and featured stories with images.
 */
class Demo_Seeder {

	/**
	 * Run the demo seeder.
	 */
	public static function seed(): void {
		// 1. Seed Publications.
		$publications = array(
			array(
				'name' => 'Kaler Kantho',
				'url'  => 'https://www.kalerkantho.com',
			),
			array(
				'name' => 'The Daily Star',
				'url'  => 'https://www.thedailystar.net',
			),
			array(
				'name' => 'Reuters',
				'url'  => 'https://www.reuters.com',
			),
			array(
				'name' => 'Prothom Alo',
				'url'  => 'https://www.prothomalo.com',
			),
		);
		update_option( 'jp_publications_list', wp_json_encode( $publications ) );

		// 2. Seed Categories.
		$categories = array(
			'Environment & Climate',
			'Banking & Financial Corruption',
			'Migration & Labor Rights',
			'Tech & Digital Surveillance',
		);

		$cat_ids = array();
		foreach ( $categories as $cat_name ) {
			$term = get_term_by( 'name', $cat_name, 'story_category' );
			if ( ! $term ) {
				$term = wp_insert_term( $cat_name, 'story_category' );
				if ( ! is_wp_error( $term ) ) {
					$cat_ids[ $cat_name ] = $term['term_id'];
				}
			} else {
				$cat_ids[ $cat_name ] = $term->term_id;
			}
		}

		// 3. Seed Demo Awards & Fellowships.
		$demo_awards = array(
			array(
				'title'       => 'GCCA+ Youth Awards',
				'for'         => 'for Climate Storytelling',
				'year'        => '2025',
				'location'    => 'International',
				'icon'        => 'dashicons-awards',
				'description' => 'Awarded for outstanding investigative field reporting on riverbank erosion, sea-level rise, and coastal climate displacement in South Asia.',
			),
			array(
				'title'       => 'South Asian Environmental Journalism Fellowship',
				'for'         => 'Global Reporting Network',
				'year'        => '2024',
				'location'    => 'South Asia',
				'icon'        => 'dashicons-welcome-learn-more',
				'description' => 'Selected as one of ten regional investigative fellows analyzing transboundary river water allocation agreements.',
			),
			array(
				'title'       => 'International Fact-Checking Innovation Grant',
				'for'         => 'IFCN / Poynter Institute',
				'year'        => '2024',
				'location'    => 'Geneva, Switzerland',
				'icon'        => 'dashicons-star-filled',
				'description' => 'Innovation grant supporting open-source intelligence (OSINT) research into algorithmic disinformation networks.',
			),
			array(
				'title'       => 'National Investigative Journalism Award',
				'for'         => 'Press Club Media Excellence',
				'year'        => '2023',
				'location'    => 'Dhaka, Bangladesh',
				'icon'        => 'dashicons-shield',
				'description' => 'First place national award for exposing multi-million dollar banking loan fraud and shell company syndicates.',
			),
			array(
				'title'       => 'Cross-Border Labor Rights Reporting Grant',
				'for'         => 'International Labor Organization',
				'year'        => '2023',
				'location'    => 'Gulf Region',
				'icon'        => 'dashicons-groups',
				'description' => 'Field reporting grant investigating predatory recruitment agency fees and overseas migrant worker contracts.',
			),
			array(
				'title'       => 'Digital Rights & Cyber Surveillance Fellowship',
				'for'         => 'Center for International Media Assistance',
				'year'        => '2022',
				'location'    => 'Washington DC, USA',
				'icon'        => 'dashicons-admin-network',
				'description' => 'Fellowship focusing on mobile interception technologies, spyware deployment, and digital safety for press freedom.',
			),
		);

		foreach ( $demo_awards as $award ) {
			$existing_award = get_page_by_title( $award['title'], OBJECT, 'jp_award' );
			if ( ! $existing_award ) {
				$award_id = wp_insert_post(
					array(
						'post_title'  => $award['title'],
						'post_status' => 'publish',
						'post_type'   => 'jp_award',
					)
				);

				if ( $award_id && ! is_wp_error( $award_id ) ) {
					update_post_meta( $award_id, '_award_for', $award['for'] );
					update_post_meta( $award_id, '_award_year', $award['year'] );
					update_post_meta( $award_id, '_award_location', $award['location'] );
					update_post_meta( $award_id, '_award_icon', $award['icon'] );
					update_post_meta( $award_id, '_award_description', $award['description'] );
				}
			}
		}

		// 4. Seed Demo Multimedia Items.
		$demo_multimedia = array(
			array(
				'title'       => 'Voices of the Erosion: Coastal Communities Fight the Rising Tide',
				'type'        => 'Video',
				'tags'        => 'Climate, Coastal Erosion, Field Documentary',
				'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
				'thumbnail'   => 'https://images.unsplash.com/photo-1594909122845-11baa439b7bf?auto=format&fit=crop&w=800&q=80',
				'description' => 'A short documentary following families along the Meghna riverbank fighting displacement.',
			),
			array(
				'title'       => 'Undercover: Inside the High-Tech Banking Fraud Syndicates',
				'type'        => 'Podcast',
				'tags'        => 'Banking, Fraud, Investigation',
				'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
				'thumbnail'   => 'https://images.unsplash.com/photo-1590602847861-f357a9332bbc?auto=format&fit=crop&w=800&q=80',
				'description' => 'Episode #1 of our investigative audio series analyzing money laundering networks.',
			),
			array(
				'title'       => 'Shadow Workers: The Human Cost of Overseas Migrant Contracts',
				'type'        => 'Photo Essay',
				'tags'        => 'Labor Rights, Migration, Photojournalism',
				'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
				'thumbnail'   => 'https://images.unsplash.com/photo-1541872703-74c5e44368f9?auto=format&fit=crop&w=800&q=80',
				'description' => 'A visual chronicle documenting migrant workers in departure lounges and transit camps.',
			),
			array(
				'title'       => 'Mapping the Network: Data Analysis of Covert Surveillance Assets',
				'type'        => 'Data Visualization',
				'tags'        => 'Surveillance, Digital Rights, OSINT',
				'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
				'thumbnail'   => 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?auto=format&fit=crop&w=800&q=80',
				'description' => 'Interactive map and dataset uncovering cross-border spyware infrastructure.',
			),
		);

		foreach ( $demo_multimedia as $media ) {
			$existing_media = get_page_by_title( $media['title'], OBJECT, 'jp_multimedia' );
			if ( ! $existing_media ) {
				$media_id = wp_insert_post(
					array(
						'post_title'  => $media['title'],
						'post_status' => 'publish',
						'post_type'   => 'jp_multimedia',
					)
				);

				if ( $media_id && ! is_wp_error( $media_id ) ) {
					update_post_meta( $media_id, '_media_type', $media['type'] );
					update_post_meta( $media_id, '_media_tags', $media['tags'] );
					update_post_meta( $media_id, '_media_youtube_url', $media['youtube_url'] );
					update_post_meta( $media_id, '_media_thumbnail', $media['thumbnail'] );
					update_post_meta( $media_id, '_media_description', $media['description'] );
				}
			}
		}

		// 5. Seed Demo Photos.
		$demo_photos = array(
			array(
				'title'         => 'Guardians of the Sundarbans: Honey Collectors in the Mangrove Wilds',
				'tags'          => 'Wildlife, Sundarbans, Environment',
				'image'         => 'https://images.unsplash.com/photo-1544717305-2782549b5136?auto=format&fit=crop&w=800&q=80',
				'published_url' => 'https://example.com/sundarbans-honey-collectors',
				'description'   => 'A photo essay capturing traditional Mowali honey harvesters navigating tiger territory in the coastal mangroves.',
			),
			array(
				'title'         => 'Surviving the Surging Tide: Life on the Erosion Frontline',
				'tags'          => 'Climate, River Erosion, Coastal Life',
				'image'         => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80',
				'published_url' => 'https://example.com/river-erosion-frontline',
				'description'   => 'Documenting families rebuilding homes along the shifting banks of the Padma and Meghna rivers.',
			),
			array(
				'title'         => 'Shadows of the Brick Kilns: Air Quality and Seasonal Labor',
				'tags'          => 'Pollution, Labor Rights, Industry',
				'image'         => 'https://images.unsplash.com/photo-1541872703-74c5e44368f9?auto=format&fit=crop&w=800&q=80',
				'published_url' => 'https://example.com/brick-kilns-labor',
				'description'   => 'A stark visual series uncovering smoke stack emissions and migrant workers in suburban brick fields.',
			),
			array(
				'title'         => 'Echoes of the Monsoon: Urban Inundation and Drainage Struggles',
				'tags'          => 'Urbanization, Floods, Infrastructure',
				'image'         => 'https://images.unsplash.com/photo-1519692933481-e162a57d6721?auto=format&fit=crop&w=800&q=80',
				'published_url' => 'https://example.com/monsoon-urban-floods',
				'description'   => 'Field portrait of city dwellers navigating flooded streets during unprecedented heavy rainfall.',
			),
			array(
				'title'         => 'Twilight at the Fish Harbor: Maritime Economy at Dawn',
				'tags'          => 'Maritime, Economy, Trade',
				'image'         => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=800&q=80',
				'published_url' => 'https://example.com/fish-harbor-dawn',
				'description'   => 'Visual coverage of fishermen unloading silver hilsa at regional fish landing centers.',
			),
			array(
				'title'         => 'Rays of Hope: Solar Microgrids in Remote Off-Grid Char Islands',
				'tags'          => 'Renewable Energy, Climate Innovation',
				'image'         => 'https://images.unsplash.com/photo-1509391365360-2e959784a276?auto=format&fit=crop&w=800&q=80',
				'published_url' => 'https://example.com/char-islands-solar',
				'description'   => 'Photographic story on clean solar power transforming night life for isolated island villagers.',
			),
			array(
				'title'         => 'Voices of the Hill Tracts: Indigenous Farming and Soil Conservation',
				'tags'          => 'Indigenous Rights, Agriculture, Soil',
				'image'         => 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=800&q=80',
				'published_url' => 'https://example.com/indigenous-farming-hill-tracts',
				'description'   => 'Capturing sustainable slope farming and indigenous community knowledge in the Chittagong Hill Tracts.',
			),
			array(
				'title'         => 'The Silent Forest: Biodiversity Monitoring in National Parks',
				'tags'          => 'Conservation, Forest, Biodiversity',
				'image'         => 'https://images.unsplash.com/photo-1448375240586-882707db888b?auto=format&fit=crop&w=800&q=80',
				'published_url' => 'https://example.com/silent-forest-biodiversity',
				'description'   => 'High-resolution field photography from camera trap surveys and forestry conservationists.',
			),
		);

		foreach ( $demo_photos as $photo ) {
			$existing_photo = get_page_by_title( $photo['title'], OBJECT, 'jp_photo' );
			if ( ! $existing_photo ) {
				$photo_id = wp_insert_post(
					array(
						'post_title'  => $photo['title'],
						'post_status' => 'publish',
						'post_type'   => 'jp_photo',
					)
				);

				if ( $photo_id && ! is_wp_error( $photo_id ) ) {
					update_post_meta( $photo_id, '_photo_tags', $photo['tags'] );
					update_post_meta( $photo_id, '_photo_image', $photo['image'] );
					update_post_meta( $photo_id, '_photo_description', $photo['description'] );
					update_post_meta( $photo_id, '_photo_published_url', $photo['published_url'] );
				}
			} else {
				update_post_meta( $existing_photo->ID, '_photo_published_url', $photo['published_url'] );
			}
		}

		// Image paths generated by AI
		$artifacts_dir = '/Users/samrathossen/.gemini/antigravity-ide/brain/99e170b3-8772-4d53-989f-2d0ab9dbf7aa/';
		
		$demo_stories = array(
			array(
				'title'          => 'Displaced by the Surge: How Riverbank Erosion is Swallowing Coastal Communities',
				'subheading'     => 'Exclusive Field Report | Environment & Climate Crisis',
				'publisher'      => 'Kaler Kantho',
				'publisher_url'  => 'https://www.kalerkantho.com',
				'category'       => 'Environment & Climate',
				'is_featured'    => '1',
				'priority'       => 10,
				'image_file'     => $artifacts_dir . 'investigation_climate_cover_1784721968336.png',
				'description'    => 'An in-depth 6-month investigation into rising sea levels, riverbank collapse, and climate refugees along the coastal belt of Southern Asia.',
				'content'        => '<p>Along the vast, shifting riverbanks of the coastal delta, thousands of families wake up every morning to a coastline that is shrinking beneath their feet. Over the past decade, severe monsoon surges and accelerated riverbank erosion have erased entire villages from the map, displacing over 150,000 residents into informal urban slums.</p>
<p>Our six-month investigation across seven coastal districts reveals a systemic failure in embankment maintenance, budget embezzlement by local contractors, and a severe lack of long-term resettlement policies. Engineers from the Water Development Board acknowledge that over 60% of existing flood control dikes are structurally compromised.</p>
<blockquote style="font-style: italic; border-left: 4px solid #059669; padding-left: 16px; margin: 20px 0; color: #334155;">"We lost three acres of paddy land in a single night. Now we live on embankment slopes with nowhere else to go," says Rahima Begum, a 52-year-old mother of four.</blockquote>
<p>Satellite imagery analyzed for this report confirms that more than 4,200 hectares of arable land have been lost to the river over the past five years alone. With sea levels projected to rise further, experts warn that without immediate policy intervention and transparent embankment engineering, millions more risk becoming permanent climate displaced citizens.</p>',
			),
			array(
				'title'          => 'The Ghost Accounts: Uncovering Multi-Million Dollar Loan Fraud in State Banks',
				'subheading'     => 'Financial Audit | Banking Corruption',
				'publisher'      => 'The Daily Star',
				'publisher_url'  => 'https://www.thedailystar.net',
				'category'       => 'Banking & Financial Corruption',
				'is_featured'    => '0',
				'priority'       => 0,
				'image_file'     => $artifacts_dir . 'investigation_banking_cover_1784721983128.png',
				'description'    => 'Leaked audit documents reveal how shell companies siphoned billions from public financial institutions under the guise of export credit.',
				'content'        => '<p>A confidential internal audit obtained during this investigation uncovers how a syndicate of paper companies secured over $120 million in non-performing loans from three state-owned commercial banks using forged trade bills and fictitious collateral.</p>
<p>The scheme operated seamlessly for nearly four years through complicit branch managers who bypassed mandatory credit verification checks. More than 45 dummy accounts were registered at virtual office addresses, receiving funds that were instantly wired to offshore tax havens.</p>
<p>Financial regulators have initiated formal proceedings, but forensic auditors emphasize that recovery of the stolen capital remains highly unlikely without international asset tracing protocols.</p>',
			),
			array(
				'title'          => 'Trapped in the Gulf: The Secret Exploitation of Overseas Laborers',
				'subheading'     => 'Human Rights | International Migration',
				'publisher'      => 'Reuters',
				'publisher_url'  => 'https://www.reuters.com',
				'category'       => 'Migration & Labor Rights',
				'is_featured'    => '0',
				'priority'       => 0,
				'image_file'     => $artifacts_dir . 'investigation_migration_cover_1784721998604.png',
				'description'    => 'Investigating predatory recruitment agencies and unkept contracts facing thousands of migrant construction workers abroad.',
				'content'        => '<p>Promises of high salaries and dignified working conditions quickly vanished for hundreds of migrant workers upon arrival at overseas construction sites. Confiscated passports, unpaid wages for up to eight months, and overcrowded labor camps remain harsh realities.</p>
<p>This cross-border investigation traces the supply chain of unlicensed middlemen who charge exorbitant recruitment fees, plunging low-income families into crippling debt cycles before they even step on the airplane.</p>
<p>Advocacy groups and diplomatic missions are pushing for stricter enforcement of bilateral labor agreements to hold predatory recruitment agencies accountable.</p>',
			),
			array(
				'title'          => 'Eyes in the Wire: Unmasking the Covert Digital Surveillance Network',
				'subheading'     => 'Tech Report | Digital Rights & Privacy',
				'publisher'      => 'Prothom Alo',
				'publisher_url'  => 'https://www.prothomalo.com',
				'category'       => 'Tech & Digital Surveillance',
				'is_featured'    => '0',
				'priority'       => 0,
				'image_file'     => $artifacts_dir . 'investigation_tech_cover_1784722012301.png',
				'description'    => 'Forensic analysis of mobile interception devices and spyware deployed to monitor independent journalists and civil society activists.',
				'content'        => '<p>Digital forensics experts have uncovered evidence of military-grade spyware and IMSI-catchers being utilized to intercept encrypted phone communications and track location data of investigative reporters and human rights defenders.</p>
<p>Technical logs obtained by cybersecurity researchers indicate that target devices were infected through zero-click exploits delivered via compromised network infrastructure.</p>
<p>Civil liberty lawyers are requesting transparent legal oversight and clear legislative boundaries regarding commercial surveillance software purchases by governmental bodies.</p>',
			),
		);

		require_once ABSPATH . 'wp-admin/includes/image.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';

		foreach ( $demo_stories as $story ) {
			// Check if story already exists to prevent duplicate insertion
			$existing = get_page_by_title( $story['title'], OBJECT, 'story' );
			if ( $existing ) {
				$post_id = $existing->ID;
			} else {
				$post_id = wp_insert_post(
					array(
						'post_title'   => $story['title'],
						'post_content' => $story['content'],
						'post_status'  => 'publish',
						'post_type'    => 'story',
					)
				);
			}

			if ( $post_id && ! is_wp_error( $post_id ) ) {
				// Set Meta Values
				update_post_meta( $post_id, '_story_heading', $story['subheading'] );
				update_post_meta( $post_id, '_story_publisher', $story['publisher'] );
				update_post_meta( $post_id, '_story_publisher_url', $story['publisher_url'] );
				update_post_meta( $post_id, '_story_description', $story['description'] );
				update_post_meta( $post_id, '_story_is_featured', $story['is_featured'] );
				update_post_meta( $post_id, '_story_feature_priority', $story['priority'] );

				// Set Taxonomy Category
				if ( isset( $cat_ids[ $story['category'] ] ) ) {
					wp_set_object_terms( $post_id, (int) $cat_ids[ $story['category'] ], 'story_category' );
				}

				// Attach Featured Image if available and not set
				if ( ! has_post_thumbnail( $post_id ) && file_exists( $story['image_file'] ) ) {
					self::attach_featured_image( $post_id, $story['image_file'], $story['title'] );
				}
			}
		}
	}

	/**
	 * Helper function to upload and attach image to post as featured image.
	 */
	private static function attach_featured_image( int $post_id, string $file_path, string $title ): void {
		$upload_dir = wp_upload_dir();
		$filename   = basename( $file_path );

		if ( wp_mkdir_p( $upload_dir['path'] ) ) {
			$file = $upload_dir['path'] . '/' . $filename;
		} else {
			$file = $upload_dir['basedir'] . '/' . $filename;
		}

		copy( $file_path, $file );

		$filetype = wp_check_filetype( $filename, null );

		$attachment = array(
			'post_mime_type' => $filetype['type'],
			'post_title'     => sanitize_file_name( $title ),
			'post_content'   => '',
			'post_status'    => 'inherit',
		);

		$attach_id = wp_insert_attachment( $attachment, $file, $post_id );

		if ( ! is_wp_error( $attach_id ) ) {
			$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
			wp_update_attachment_metadata( $attach_id, $attach_data );
			set_post_thumbnail( $post_id, $attach_id );
		}
	}
}
