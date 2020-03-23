<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Manage DirectoryStack listing fields through the commands line.
 *
 * @package   directorystack
 * @author    Sematico LTD <hello@sematico.com>
 * @copyright 2020 Sematico LTD
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://directorystack.com
 */

namespace DirectoryStackCLI;

use WP_CLI;
use \DirectoryStack\Helpers\Fields as FieldsHelper;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Handles listing custom fields.
 */
class ListingFields extends DirectoryStackCommand {

	/**
	 * Generate random listing custom fields for testing purposes.
	 *
	 * ## EXAMPLES
	 *
	 *     $ wp ds listingfields generate
	 */
	public function generate( $args, $assoc_args ) {

		WP_CLI::success( 'Successfully created random listing fields.' );

	}

	/**
	 * Reset listing custom fields and re-install default fields.
	 *
	 * ## EXAMPLES
	 *
	 *     $ wp ds listingfields generate
	 *
	 * @param array $args command arguments.
	 * @param array $assoc_args command arguments.
	 * @return void
	 */
	public function reset( $args, $assoc_args ) {

		$table = ( new \DirectoryStack\Database\ListingCustomFieldsTable() )->truncate();

		\DirectoryStack\Helpers\Installer::add_listing_fields( true );

		WP_CLI::success( 'Successfully reset listing fields and re-added default fields.' );

	}

}
