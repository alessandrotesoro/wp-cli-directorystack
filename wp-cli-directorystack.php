<?php
/**
 * Plugin Name:     DirectoryStack WP CLI
 * Plugin URI:      https://directorystack.com
 * Description:     DirectoryStack WP CLI Tools.
 * Author:          Sematico LTD
 * Author URI:      https://sematico.com
 * Text Domain:     wp-cli-directorystack
 * Domain Path:     /languages
 * Version:         1.0.0
 *
 * DirectoryStack WP CLI is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * DirectoryStack WP CLI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with DirectoryStack WP CLI. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package   wp-cli-directorystack
 * @author    Sematico LTD <hello@sematico.com>
 * @copyright 2020 Sematico LTD
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://directorystack.com
 */

namespace DirectoryStackCLI;

use WP_CLI;

// Bail if WP-CLI is not present.
if ( ! defined( '\WP_CLI' ) ) {
	return;
}

if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
	require dirname( __FILE__ ) . '/vendor/autoload.php';
}

WP_CLI::add_hook(
	'before_wp_load',
	function() {

		WP_CLI::add_command(
			'ds',
			DirectoryStackCommand::class,
			array(
				'before_invoke' => function() {
					if ( ! class_exists( '\DirectoryStack\Plugin' ) ) {
						WP_CLI::error( 'The DirectoryStack plugin is not active.' );
					}
				},
			)
		);

		WP_CLI::add_command(
			'ds userfields',
			UserFields::class,
			array(
				'before_invoke' => function() {
					if ( ! class_exists( '\DirectoryStack\Plugin' ) ) {
						WP_CLI::error( 'The DirectoryStack plugin is not active.' );
					}
				},
			)
		);

		WP_CLI::add_command(
			'ds registrationforms',
			RegistrationForms::class,
			array(
				'before_invoke' => function() {
					if ( ! class_exists( '\DirectoryStack\Plugin' ) ) {
						WP_CLI::error( 'The DirectoryStack plugin is not active.' );
					}
				},
			)
		);

		WP_CLI::add_command(
			'ds listingfields',
			ListingFields::class,
			array(
				'before_invoke' => function() {
					if ( ! class_exists( '\DirectoryStack\Plugin' ) ) {
						WP_CLI::error( 'The DirectoryStack plugin is not active.' );
					}
				},
			)
		);

		WP_CLI::add_command(
			'ds submissionforms',
			SubmissionForms::class,
			array(
				'before_invoke' => function() {
					if ( ! class_exists( '\DirectoryStack\Plugin' ) ) {
						WP_CLI::error( 'The DirectoryStack plugin is not active.' );
					}
				},
			)
		);

		WP_CLI::add_command(
			'ds taxonomies',
			Taxonomies::class,
			array(
				'before_invoke' => function() {
					if ( ! class_exists( '\DirectoryStack\Plugin' ) ) {
						WP_CLI::error( 'The DirectoryStack plugin is not active.' );
					}
				},
			)
		);

	}
);
