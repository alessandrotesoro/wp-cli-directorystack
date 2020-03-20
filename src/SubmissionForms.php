<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Manage DirectoryStack submission forms through the commands line.
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
 * Manage DirectoryStack submission forms through the commands line.
 */
class SubmissionForms extends DirectoryStackCommand {

	/**
	 * Generate a submission form with all custom fields for testing purposes.
	 *
	 * ## OPTIONS
	 *
	 * [--title=<title>]
	 * : Title of the form.
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

		$submission_form                = new \DirectoryStack\Models\SubmissionForm();
		$submission_form->name          = $r['title'];
		$submission_form->date_created  = $submission_form->getDateTime();
		$submission_form->date_modified = $submission_form->date_created;

		$fields = array(
			'fields_list' => array(),
		);

		$submission_fields = ( new \DirectoryStack\Models\ListingField() )
			->findAll()
			->get();

		foreach ( $submission_fields as $field ) {
			$fields['fields_list'][] = array(
				'field_id' => $field->getID(),
			);
		}

		$submission_form->settings = $fields;

		$submission_form->create();

		WP_CLI::success( 'Submission form successfully created.' );

	}

	/**
	 * Reset the submission forms database table.
	 *
	 * @param array $args arguments.
	 * @param array $assoc_args arguments.
	 * @return void
	 */
	public function reset( $args, $assoc_args ) {

		$table = ( new \DirectoryStack\Database\SubmissionFormsTable() )->truncate();

		WP_CLI::success( 'Submission forms have been successfully removed.' );

	}

}
