<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Manage DirectoryStack analytics through the commands line.
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
 * Handles analytics.
 */
class Analytics extends DirectoryStackCommand {

	/**
	 * Generate random analytics for testing purposes.
	 *
	 * @param array $args arguments.
	 * @param array $assoc_args arguments.
	 * @return void
	 */
	public function generate( $args, $assoc_args ) {

		$args = array(
			'post_type'              => 'listing',
			'posts_per_page'         => -1,
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		);

		$query = new \WP_Query( $args );

		$notify = \WP_CLI\Utils\make_progress_bar( 'Generating random statistics for listings...', count( $query->get_posts() ) );

		$days_list = \DirectoryStack\Addons\Analytics\Helper::get_days_list( 60, 'Y-m-d' );

		foreach ( $query->get_posts() as $listing_id ) {

			foreach ( $days_list as $date ) {

				$views    = wp_rand( 1, 200 );
				$visitors = absint( round( 30 / ( $views / 100 ), 2 ) );
				$desktop  = absint( round( 70 / ( $views / 100 ), 2 ) );
				$mobile   = absint( round( 30 / ( $views / 100 ), 2 ) );

				if ( $visitors <= 200 && $desktop <= 200 && $mobile <= 200 ) {

					$stats = new \DirectoryStack\Addons\Analytics\PageViews();

					$stats->date       = sanitize_text_field( $date );
					$stats->listing_id = absint( $listing_id );
					$stats->visitors   = $visitors;
					$stats->views      = absint( $views );
					$stats->desktop    = $desktop;
					$stats->mobile     = $mobile;

					$stats->create();

				}
			}

			$notify->tick();
		}

		$notify->finish();

		WP_CLI::success( 'Done.' );

	}

}
