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
			)
		);

		$notify = \WP_CLI\Utils\make_progress_bar( 'Generating random listings.', $r['number'] );
		$faker = \Faker\Factory::create();

		foreach ( range( 1, $r['number'] ) as $index ) {

			$listing_data = [
				'post_title'   => $faker->company,
				'post_content' => \Faker\Provider\Lorem::paragraphs( 2, true ),
				'post_status'  => 'publish',
				'post_author'  => $user_id,
				'post_type'    => 'listings',
			];

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

	}

}
