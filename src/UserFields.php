<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Manage DirectoryStack user fields through the commands line.
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
 * Handles user custom fields.
 */
class UserFields extends DirectoryStackCommand {

	/**
	 * Generate random user custom fields for testing purposes.
	 *
	 * ## EXAMPLES
	 *
	 *     $ wp ds userfields generate
	 */
	public function generate( $args, $assoc_args ) {
		WP_CLI::success( 'Test' );
	}

}
