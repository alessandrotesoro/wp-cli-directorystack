<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Manage DirectoryStack listings through the commands line.
 *
 * @package   directorystack
 * @author    Sematico LTD <hello@sematico.com>
 * @copyright 2020 Sematico LTD
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://directorystack.com
 */

namespace DirectoryStackCLI;

use WP_CLI;
use NicoVerbruggen\ImageGenerator\ImageGenerator;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Handles listings.
 */
class Listings extends DirectoryStackCommand {

	/**
	 * Generate random listings for testing purposes.
	 *
	 * ## EXAMPLES
	 *
	 *     $ wp ds listings generate --number=10
	 *
	 * @param array $args command arguments.
	 * @param array $assoc_args command arguments.
	 * @return void
	 */
	public function generate( $args, $assoc_args ) {

		$r = wp_parse_args(
			$assoc_args,
			array(
				'number'  => 10,
				'user'    => 1,
				'images'  => true,
				'form_id' => 1,
			)
		);

		$notify = \WP_CLI\Utils\make_progress_bar( 'Generating random listings.', $r['number'] );
		$faker  = \Faker\Factory::create();

		foreach ( range( 1, $r['number'] ) as $index ) {

			$listing_data = array(
				'post_title'   => $faker->company,
				'post_content' => \Faker\Provider\Lorem::paragraphs( 2, true ),
				'post_status'  => 'publish',
				'post_author'  => $r['user'],
				'post_type'    => 'listing',
			);

			$new_listing_id = wp_insert_post( $listing_data );

			$coordinates = explode( ',', ( new \Intellex\Generator\Plus\GpsLocationGen( 40.730610, -73.935242, 3000 ) )->gen() );
			$lat         = $coordinates[0];
			$lng         = $coordinates[1];

			$location = array(
				'address1' => $faker->streetAddress,
				'lat'      => $lat,
				'lng'      => $lng,
				'zip'      => $faker->postcode,
			);

			update_post_meta( $new_listing_id, 'listing_location', $location );

			update_post_meta( $new_listing_id, 'listing_email_address', $faker->safeEmail );

			update_post_meta( $new_listing_id, 'listing_website', 'https://example.com' );

			update_post_meta( $new_listing_id, 'listing_phone_number', $faker->e164PhoneNumber );

			update_post_meta( $new_listing_id, 'listing_social_profiles', 'a:4:{i:15858399605280;a:2:{s:7:"network";s:8:"facebook";s:3:"url";s:20:"https://facebook.com";}i:15858399679231;a:2:{s:7:"network";s:7:"twitter";s:3:"url";s:19:"https://twitter.com";}i:15858399872973;a:2:{s:7:"network";s:9:"instagram";s:3:"url";s:21:"https://instagram.com";}i:15858399750622;a:2:{s:7:"network";s:7:"youtube";s:3:"url";s:19:"https://youtube.com";}}' );

			update_post_meta( $new_listing_id, 'monday_timeslots', 'hours' );
			update_post_meta( $new_listing_id, 'tuesday_timeslots', 'hours' );
			update_post_meta( $new_listing_id, 'wednesday_timeslots', 'hours' );
			update_post_meta( $new_listing_id, 'thursday_timeslots', 'hours' );
			update_post_meta( $new_listing_id, 'friday_timeslots', 'appointment' );
			update_post_meta( $new_listing_id, 'saturday_timeslots', 'appointment' );
			update_post_meta( $new_listing_id, 'sunday_timeslots', 'closed_all_day' );

			update_post_meta( $new_listing_id, 'monday_opening_time', '08:00' );
			update_post_meta( $new_listing_id, 'monday_closing_time', '19:00' );
			update_post_meta( $new_listing_id, 'tuesday_opening_time', '08:00' );
			update_post_meta( $new_listing_id, 'tuesday_closing_time', '19:00' );
			update_post_meta( $new_listing_id, 'wednesday_opening_time', '08:00' );
			update_post_meta( $new_listing_id, 'wednesday_closing_time', '19:00' );
			update_post_meta( $new_listing_id, 'thursday_opening_time', '08:00' );
			update_post_meta( $new_listing_id, 'thursday_closing_time', '19:00' );

			$taxonomies = \DirectoryStack\Helpers\Admin::get_registered_listings_taxonomies();

			foreach ( $taxonomies as $tax_id => $tax_label ) {
				$terms = get_terms(
					array(
						'taxonomy'   => $tax_id,
						'hide_empty' => false,
						'number'     => 9999,
					)
				);

				$random_terms = \Faker\Provider\Base::randomElements( $terms, 3 );

				$termslist = array();

				if ( ! empty( $random_terms ) ) {
					foreach ( $random_terms as $term ) {
						$termslist[] = $term->term_id;
					}
				}

				wp_set_post_terms( $new_listing_id, $termslist, $tax_id );
			}

			if ( $r['images'] === true ) {
				// Create featured image.
				$generator = new ImageGenerator(
					array(
						'targetSize' => '1024x1024',
						'fontSize'   => 20,
					)
				);

				$generator->fontSize = 90;

				$generator->makePlaceholderImage(
					\Faker\Provider\Base::numerify( 'Demo image ##' ),
					wp_upload_dir()['path'] . '/image_example.png'
				);

				$image = trailingslashit( wp_upload_dir()['url'] ) . 'image_example.png';

				if ( $image ) {
					$upload = ds_rest_upload_image_from_url( $image );
					$id     = ds_rest_set_uploaded_image_as_attachment( $upload, $new_listing_id );
					if ( $id ) {
						set_post_thumbnail( $new_listing_id, $id );
					}
				}

				wp_delete_file( wp_upload_dir()['path'] . '/image_example.png' );

			}

			update_post_meta( $new_listing_id, 'submission_form_id', $r['form_id'] );

			$notify->tick();
		}

		$notify->finish();

		$this->generate_data();

		WP_CLI::success( 'Successfully created random listings.' );

	}

	/**
	 * Wipe all listings in the database.
	 *
	 * ## EXAMPLES
	 *
	 *     $ wp ds listings wipe
	 *
	 * @param array $args command arguments.
	 * @param array $assoc_args command arguments.
	 * @return void
	 */
	public function wipe( $args, $assoc_args ) {

		$listings = ( new \DirectoryStack\Models\Listing() )
			->findAll()
			->get();

		if ( ! empty( $listings ) ) {
			foreach ( $listings as $listing ) {
				$listing->deleteForever();
			}
		}

		WP_CLI::success( 'Successfully deleted all listings.' );

	}

	/**
	 * Generate data for listings.
	 *
	 * @return void
	 */
	private function generate_data() {

		$excluded = array(
			'tax-radio',
			'tax-checkboxes',
			'tax-select',
			'tax-multiselect',
			'tax-cascade-select',
			'tax-cascade-multiselect',
		);

		$fields = ( new \DirectoryStack\Models\ListingField() )
			->where( 'default_field', '=', null )
			->where( 'type', 'NOT IN', $excluded )
			->findAll()
			->get();

		$listings = ( new \DirectoryStack\Models\Listing() )
			->findAll()
			->get();

		$faker = \Faker\Factory::create();

		if ( ! empty( $fields ) ) {

			$notify = \WP_CLI\Utils\make_progress_bar( 'Generating data for the listing fields.', count( $fields ) );

			foreach ( $fields as $field ) {

				if ( $field->type === 'file' ) {
					continue;
				}

				switch ( $field->type ) {
					case 'url':
						foreach ( $listings as $listing ) {
							$text = 'https://example.com';
							update_post_meta( $listing->getID(), $field->metakey, $text );
						}
						break;
					case 'email':
						foreach ( $listings as $listing ) {
							$text = $faker->safeEmail;
							update_post_meta( $listing->getID(), $field->metakey, $text );
						}
						break;
					case 'password':
					case 'text':
						foreach ( $listings as $listing ) {
							$text = \Faker\Provider\Lorem::sentence( 10, true );
							update_post_meta( $listing->getID(), $field->metakey, $text );
						}
						break;
					case 'editor':
					case 'textarea':
						foreach ( $listings as $listing ) {
							$text = \Faker\Provider\Lorem::paragraphs( 2, true );
							update_post_meta( $listing->getID(), $field->metakey, $text );
						}
						break;
					case 'multiselect':
					case 'multicheckbox':
						$options = $field->get_setting( 'selectable_options', array() );
						$options = array_rand( $options, 2 );
						foreach ( $listings as $listing ) {
							update_post_meta( $listing->getID(), $field->metakey, $options );
						}
						break;
					case 'radio':
					case 'select':
						$options = $field->get_setting( 'selectable_options', array() );
						foreach ( $listings as $listing ) {
							update_post_meta( $listing->getID(), $field->metakey, key( array_slice( $options, 1, 1, true ) ) );
						}
						break;
					case 'checkbox':
						foreach ( $listings as $listing ) {
							update_post_meta( $listing->getID(), $field->metakey, true );
						}
						break;
					case 'phone':
						foreach ( $listings as $listing ) {
							update_post_meta( $listing->getID(), $field->metakey, $faker->e164PhoneNumber );
						}
						break;
					case 'number':
						foreach ( $listings as $listing ) {
							update_post_meta( $listing->getID(), $field->metakey, \Faker\Provider\Base::randomNumber() );
						}
						break;
				}

				$notify->tick();
			}

			$notify->finish();

			// Setup listing types.
			$notify = \WP_CLI\Utils\make_progress_bar( 'Generating listing type for listings.', count( $listings ) );

			foreach ( $listings as $listing ) {
				$terms = get_terms(
					array(
						'taxonomy'   => 'listing_type',
						'hide_empty' => false,
						'number'     => 9999,
					)
				);

				$random_terms = \Faker\Provider\Base::randomElements( $terms, 1 );

				$termslist = array();
				foreach ( $random_terms as $term ) {
					$termslist[] = $term->term_id;
				}
				wp_set_post_terms( $listing->getID(), $termslist, 'listing_type' );

				$notify->tick();
			}

			$notify->finish();

		}

		WP_CLI::success( 'Done.' );

	}

	/**
	 * Generate random statuses for listings.
	 *
	 * ## EXAMPLES
	 *
	 *     $ wp ds listings random_status --number=10
	 *
	 * @param array $args command arguments.
	 * @param array $assoc_args command arguments.
	 * @return void
	 */
	public function random_status( $args, $assoc_args ) {

		$r = wp_parse_args(
			$assoc_args,
			array(
				'number' => 10,
			)
		);

		$listings = new \WP_Query(
			array(
				'post_type'      => 'listing',
				'posts_per_page' => -1,
				'fields'         => 'ids',
			)
		);

		$random_ids = \Faker\Provider\Base::randomElements( $listings->get_posts(), $r['number'] );

		$statuses = array_keys( \DirectoryStack\Helpers\Listings::get_statuses() );

		unset( $statuses['publish'] );

		$notify = \WP_CLI\Utils\make_progress_bar( 'Generating random statuses for listings.', $r['number'] );

		foreach ( $random_ids as $id ) {

			$random_status = \Faker\Provider\Base::randomElements( $statuses, 1 );

			$args = array(
				'ID'          => $id,
				'post_status' => $random_status[0],
			);

			wp_update_post( $args );

			$notify->tick();

		}

		$notify->finish();

		WP_CLI::success( 'Done.' );

	}

	/**
	 * Mark random listings as featured.
	 *
	 * ## OPTIONS
	 *
	 * [--number=<number>]
	 * : Number of listings to mark as featured. 10 by default.
	 *
	 * ## EXAMPLES
	 *
	 *     $ wp ds listings featurify --number=10
	 *
	 * @param array $args command arguments.
	 * @param array $assoc_args command arguments.
	 * @return void
	 */
	public function featurify( $args, $assoc_args ) {

		$r = wp_parse_args(
			$assoc_args,
			array(
				'number' => 10,
			)
		);

		$amount = absint( $r['number'] );

		$listings = new \WP_Query(
			array(
				'post_type'      => 'listing',
				'posts_per_page' => -1,
				'fields'         => 'ids',
			)
		);

		$random_ids = \Faker\Provider\Base::randomElements( $listings->get_posts(), $amount );

		$notify = \WP_CLI\Utils\make_progress_bar( 'Setting random listings as featured.', $amount );

		foreach ( $random_ids as $id ) {

			ds_mark_listing_as_featured( $id );

			$notify->tick();

		}

		$notify->finish();

		WP_CLI::success( 'Done.' );

	}

}
