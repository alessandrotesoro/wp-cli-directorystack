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

		$available_field_types = FieldsHelper::get_registered_listing_field_types( array( 'password', 'heading', 'social-profiles', 'map', 'listing-opening-hours' ) );

		$faker = \Faker\Factory::create();

		foreach ( $available_field_types as $field_type => $type_label ) {

			$field                = new \DirectoryStack\Models\ListingField();
			$field->date_created  = $field->getDateTime();
			$field->date_modified = $field->date_created;
			$field->type          = $field_type;
			$field->name          = "Demo {$type_label}";
			$field->metakey       = "field_demo_{$field_type}";
			$field->priority      = $field->findAll()->count() + 1;

			$settings = array();

			if ( in_array( $field_type, array( 'select', 'radio', 'multiselect', 'multicheckbox' ), true ) ) {
				$settings['selectable_options'] = array(
					"demo_{$faker->randomNumber}" => array(
						'option_name' => $faker->name,
					),
					"demo_{$faker->randomNumber}" => array(
						'option_name' => $faker->name,
					),
					"demo_{$faker->randomNumber}" => array(
						'option_name' => $faker->name,
					),
					"demo_{$faker->randomNumber}" => array(
						'option_name' => $faker->name,
					),
					"demo_{$faker->randomNumber}" => array(
						'option_name' => $faker->name,
					),
				);
			}

			switch ( $field_type ) {
				case 'tax-radio':
					$settings['taxonomy'] = 'listing_type';
					break;
				case 'tax-checkboxes':
					$settings['taxonomy'] = 'listing_tag';
					break;
				case 'tax-cascade-select':
					$settings['taxonomy'] = 'listing_location';
					break;
				case 'tax-cascade-multiselect':
					$settings['taxonomy'] = 'listing_category';
					break;
				case 'tax-select':
					$settings['taxonomy'] = 'listing_taxonomy_demo1';
					break;
				case 'tax-multiselect':
					$settings['taxonomy'] = 'listing_taxonomy_demo2';
					break;
			}

			if ( ! empty( $settings ) ) {
				$field->settings = $settings;
			}

			$field->save();

		}

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
