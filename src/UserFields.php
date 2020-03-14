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
use \DirectoryStack\Helpers\Fields as FieldsHelper;

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

		$available_field_types = FieldsHelper::get_registered_user_field_types();

		$faker = \Faker\Factory::create();

		foreach ( $available_field_types as $field_type => $type_label ) {

			if ( in_array( $field_type, array( 'password', 'heading' ), true ) ) {
				continue;
			}

			$field                = new \DirectoryStack\Models\UserField();
			$field->date_created  = $field->getDateTime();
			$field->date_modified = $field->date_created;
			$field->type          = $field_type;
			$field->name          = "Demo {$type_label}";
			$field->metakey       = "field_demo_{$field_type}";

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

			if ( ! empty( $settings ) ) {
				$field->settings = $settings;
			}

			$field->save();

		}

		WP_CLI::success( 'Successfully created random user fields.' );

	}

	/**
	 * Reset user custom fields and re-install default fields.
	 *
	 * ## EXAMPLES
	 *
	 *     $ wp ds userfields generate
	 *
	 * @param array $args command arguments.
	 * @param array $assoc_args command arguments.
	 * @return void
	 */
	public function reset( $args, $assoc_args ) {

		$user_table = ( new \DirectoryStack\Database\UserCustomFieldsTable() )->truncate();

		\DirectoryStack\Helpers\Installer::add_user_fields( true );

		WP_CLI::success( 'Successfully reset user fields and re-added default fields.' );

	}

}
