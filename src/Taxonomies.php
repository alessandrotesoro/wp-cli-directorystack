<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Manage DirectoryStack taxonomy terms through the commands line.
 *
 * @package   directorystack
 * @author    Sematico LTD <hello@sematico.com>
 * @copyright 2020 Sematico LTD
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://directorystack.com
 */

namespace DirectoryStackCLI;

use WP_CLI;
use \DirectoryStack\Helpers\Admin;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Handles taxonomy terms.
 */
class Taxonomies extends DirectoryStackCommand {

	/**
	 * Reset all terms of a specific listing taxonomy.
	 *
	 * ## EXAMPLES
	 *
	 *     $ wp ds taxonomies reset --taxonomy=listing_category
	 *
	 * @param array $args command arguments.
	 * @param array $assoc_args command arguments.
	 * @return void
	 */
	public function reset( $args, $assoc_args ) {

		$r = wp_parse_args(
			$assoc_args,
			array(
				'taxonomy' => false,
			)
		);

		$taxonomies = Admin::get_registered_listings_taxonomies();

		if ( ! array_key_exists( $r['taxonomy'], $taxonomies ) ) {
			WP_CLI::error( 'Taxonomy does not belong to the listing post type.' );
		}

		$terms = get_terms(
			array(
				'taxonomy'   => $r['taxonomy'],
				'hide_empty' => false,
				'number'     => 99999,
			)
		);

		if ( ! empty( $terms ) ) {
			foreach ( $terms as $term_to_delete ) {
				wp_delete_term( $term_to_delete->term_id, $r['taxonomy'] );
			}
		}

		WP_CLI::success( 'Taxonomy terms successfully deleted.' );

	}

}
