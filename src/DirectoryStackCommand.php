<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Manage DirectoryStack through the commands line.
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
 * Manage DirectoryStack through the command-line.
 */
class DirectoryStackCommand extends BaseCommand {

	/**
	 * Adds description and subcomands to the DOC.
	 *
	 * @param  string $command Command.
	 * @return string
	 */
	private function command_to_array( $command ) {
		$dump = array(
			'name'        => $command->get_name(),
			'description' => $command->get_shortdesc(),
			'longdesc'    => $command->get_longdesc(),
		);

		foreach ( $command->get_subcommands() as $subcommand ) {
			$dump['subcommands'][] = $this->command_to_array( $subcommand );
		}

		if ( empty( $dump['subcommands'] ) ) {
			$dump['synopsis'] = (string) $command->get_synopsis();
		}

		return $dump;
	}

}
