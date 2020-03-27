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
				'number' => 10,
				'user'   => 1,
				'images' => true,
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

			$notify->tick();
		}

		$notify->finish();

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

}
