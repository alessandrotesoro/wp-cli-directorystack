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

function ds_demo_taxonomies() {

	$labels = array(
		'name'                       => _x( 'Taxonomy Demo 1', 'Taxonomy General Name', 'wp-cli-directorystack' ),
		'singular_name'              => _x( 'Taxonomy Demo 1', 'Taxonomy Singular Name', 'wp-cli-directorystack' ),
		'menu_name'                  => __( 'Taxonomy Demo 1', 'wp-cli-directorystack' ),
		'all_items'                  => __( 'All Items', 'wp-cli-directorystack' ),
		'parent_item'                => __( 'Parent Item', 'wp-cli-directorystack' ),
		'parent_item_colon'          => __( 'Parent Item:', 'wp-cli-directorystack' ),
		'new_item_name'              => __( 'New Item Name', 'wp-cli-directorystack' ),
		'add_new_item'               => __( 'Add New Item', 'wp-cli-directorystack' ),
		'edit_item'                  => __( 'Edit Item', 'wp-cli-directorystack' ),
		'update_item'                => __( 'Update Item', 'wp-cli-directorystack' ),
		'view_item'                  => __( 'View Item', 'wp-cli-directorystack' ),
		'separate_items_with_commas' => __( 'Separate items with commas', 'wp-cli-directorystack' ),
		'add_or_remove_items'        => __( 'Add or remove items', 'wp-cli-directorystack' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'wp-cli-directorystack' ),
		'popular_items'              => __( 'Popular Items', 'wp-cli-directorystack' ),
		'search_items'               => __( 'Search Items', 'wp-cli-directorystack' ),
		'not_found'                  => __( 'Not Found', 'wp-cli-directorystack' ),
		'no_terms'                   => __( 'No items', 'wp-cli-directorystack' ),
		'items_list'                 => __( 'Items list', 'wp-cli-directorystack' ),
		'items_list_navigation'      => __( 'Items list navigation', 'wp-cli-directorystack' ),
	);
	$args   = array(
		'labels'            => $labels,
		'hierarchical'      => false,
		'public'            => true,
		'show_ui'           => true,
		'show_admin_column' => true,
		'show_in_nav_menus' => true,
		'show_tagcloud'     => true,
	);
	register_taxonomy( 'listing_taxonomy_demo1', array( 'listing' ), $args );

	$labels2 = array(
		'name'                       => _x( 'Taxonomy Demo 2', 'Taxonomy General Name', 'wp-cli-directorystack' ),
		'singular_name'              => _x( 'Taxonomy Demo 2', 'Taxonomy Singular Name', 'wp-cli-directorystack' ),
		'menu_name'                  => __( 'Taxonomy Demo 2', 'wp-cli-directorystack' ),
		'all_items'                  => __( 'All Items', 'wp-cli-directorystack' ),
		'parent_item'                => __( 'Parent Item', 'wp-cli-directorystack' ),
		'parent_item_colon'          => __( 'Parent Item:', 'wp-cli-directorystack' ),
		'new_item_name'              => __( 'New Item Name', 'wp-cli-directorystack' ),
		'add_new_item'               => __( 'Add New Item', 'wp-cli-directorystack' ),
		'edit_item'                  => __( 'Edit Item', 'wp-cli-directorystack' ),
		'update_item'                => __( 'Update Item', 'wp-cli-directorystack' ),
		'view_item'                  => __( 'View Item', 'wp-cli-directorystack' ),
		'separate_items_with_commas' => __( 'Separate items with commas', 'wp-cli-directorystack' ),
		'add_or_remove_items'        => __( 'Add or remove items', 'wp-cli-directorystack' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'wp-cli-directorystack' ),
		'popular_items'              => __( 'Popular Items', 'wp-cli-directorystack' ),
		'search_items'               => __( 'Search Items', 'wp-cli-directorystack' ),
		'not_found'                  => __( 'Not Found', 'wp-cli-directorystack' ),
		'no_terms'                   => __( 'No items', 'wp-cli-directorystack' ),
		'items_list'                 => __( 'Items list', 'wp-cli-directorystack' ),
		'items_list_navigation'      => __( 'Items list navigation', 'wp-cli-directorystack' ),
	);
	$args2   = array(
		'labels'            => $labels2,
		'hierarchical'      => false,
		'public'            => true,
		'show_ui'           => true,
		'show_admin_column' => true,
		'show_in_nav_menus' => true,
		'show_tagcloud'     => true,
	);
	register_taxonomy( 'listing_taxonomy_demo2', array( 'listing' ), $args2 );

}
add_action( 'init', __NAMESPACE__ . '\\ds_demo_taxonomies', 100 );

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

		WP_CLI::add_command(
			'ds listings',
			Listings::class,
			array(
				'before_invoke' => function() {
					if ( ! class_exists( '\DirectoryStack\Plugin' ) ) {
						WP_CLI::error( 'The DirectoryStack plugin is not active.' );
					}
				},
			)
		);

		WP_CLI::add_command(
			'ds users',
			Users::class,
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
