<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Manage DirectoryStack users through the commands line.
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
 * Handles users.
 */
class Users extends DirectoryStackCommand {

	/**
	 * Generate random users for testing purposes.
	 *
	 * ## OPTIONS
	 *
	 * [--number=<number>]
	 * : Number of users to generate.
	 *
	 * @param array $args arguments.
	 * @param array $assoc_args arguments.
	 * @return void
	 */
	public function generate( $args, $assoc_args ) {

		$r = wp_parse_args(
			$assoc_args,
			array(
				'number' => 1,
			)
		);

		$number = $r['number'];

		if ( is_multisite() ) {
			WP_CLI::error( 'Multisite is not supported!' );
		}

		if ( $number > 1 ) {

			$notify = \WP_CLI\Utils\make_progress_bar( "Generating $number users(s)", $number );

			foreach ( range( 0, $number ) as $i ) {
				$this->register_user();
				$notify->tick();
			}

			$notify->finish();

		} else {

			$this->register_user();

		}

		WP_CLI::success( 'Done.' );

	}

	/**
	 * Create a random user.
	 *
	 * @return void
	 */
	private function register_user() {

		$faker = \Faker\Factory::create();

		$password = wp_generate_password( 12, false );
		$username = $faker->userName;
		$email    = $faker->safeEmail;

		$create_user = wp_create_user( $username, $password, $email );

		if ( ! is_wp_error( $create_user ) ) {
			wp_update_user(
				array(
					'ID'         => $create_user,
					'first_name' => $faker->firstName,
					'last_name'  => $faker->lastName,
				)
			);
		}

	}

}
