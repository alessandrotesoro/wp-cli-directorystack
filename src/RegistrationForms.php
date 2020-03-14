<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Manage DirectoryStack registration forms through the commands line.
 *
 * @package   directorystack
 * @author    Sematico LTD <hello@sematico.com>
 * @copyright 2020 Sematico LTD
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://directorystack.com
 */

namespace DirectoryStackCLI;

use WP_CLI;

/**
 * Manage DirectoryStack registration forms through the commands line.
 */
class RegistrationForms extends DirectoryStackCommand {

	/**
	 * Generate a registration form with all custom user fields for testing purposes.
	 *
	 * @param array $args arguments.
	 * @param array $assoc_args arguments.
	 * @return void
	 */
	public function generate( $args, $assoc_args ) {

		$r = wp_parse_args(
			$assoc_args,
			array(
				'title' => 'Demo form',
			)
		);

		$faker = \Faker\Factory::create();

		$registration_form                = new \DirectoryStack\Models\RegistrationForm();
		$registration_form->name          = $r['title'];
		$registration_form->role          = 'subscriber';
		$registration_form->priority      = $registration_form->findAll()->count() + 1;
		$registration_form->date_created  = $registration_form->getDateTime();
		$registration_form->date_modified = $registration_form->date_created;

		$fields = array(
			'fields_list' => array(),
		);

		$user_fields = ( new \DirectoryStack\Models\UserField() )
			->where( 'default_field', '=', null )
			->findAll()
			->get();

		if ( ! empty( $user_fields ) ) {
			foreach ( $user_fields as $user_field ) {
				$fields['fields_list'][] = array(
					'field_id' => $user_field->getID(),
				);
			}
		}

		$registration_form->settings = $fields;

		$registration_form->create();

		WP_CLI::success( 'Registration form successfully created.' );

	}

}
