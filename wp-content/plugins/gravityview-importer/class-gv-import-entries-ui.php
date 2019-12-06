<?php

namespace GV\Import_Entries;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class UI {

	/**
	 * @var string AJAX action to handle csv upload
	 */
	const AJAX_ACTION_CSV_UPLOAD = 'gv_import_entries_csv_upload';

	/**
	 * @var string AJAX action to get form input fields
	 */
	const AJAX_ACTION_FORM_DATA = 'gv_import_entries_form_data';

	/**
	 * @var string AJAX action to add new GF form
	 */
	const AJAX_ACTION_ADD_FORM_FIELD = 'gv_import_entries_add_form_field';

	/**
	 * @var string Unique nonce reference
	 */
	const NONCE_HANDLE = 'gv_import_entries_nonce';

	/**
	 * @var string Unique reference name for UI script
	 */
	const ASSETS_HANDLE = 'gv_import_entries';

	/**
	 * @var string Slug used for the page URL
	 */
	const PAGE_SLUG = 'gv-admin-import-entries';

	/**
	 * @var string HelpScout Beacon identifier key
	 */
	const HS_BEACON_KEY = '0025b2ba-29b0-4ec3-8192-230533ea29b9';

	/**
	 * @var string Plugin version
	 */
	private $plugin_version;

	/**
	 * @var string Minimum capability required to display the UI
	 */
	private $_capabilities = array( 'manage_options', 'gravityforms_import_entries', 'gravityforms_edit_entries' );

	/**
	 * @var array Array of translation strings
	 */
	public $localization = array();

	public function __construct( $plugin_version ) {

		$this->plugin_version = $plugin_version;

		/**
		 * @filter `gravityview/import/capabilities` Filter to control plugin's minimum access rights
		 *
		 * @param  [in,out] array Array of WP caps required to view the Import Entries screen (default: `[ "manage_options", "gravityforms_import_entries" ]`)
		 */
		$this->_capabilities = apply_filters( 'gravityview/import/capabilities', $this->_capabilities );

		add_filter( 'gform_export_menu', array( $this, 'add_gf_import_export_menu' ) );
		add_filter( 'gravityview-import-entries-form-feeds', array( $this, 'handle_custom_form_feeds' ), 10, 2 );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ), 200 );
		add_action( 'admin_init', array( $this, 'redirect_gf_import_export' ) );

		add_action( 'wp_ajax_' . self::AJAX_ACTION_CSV_UPLOAD, array( $this, 'AJAX_csv_upload' ) );
		add_action( 'wp_ajax_' . self::AJAX_ACTION_FORM_DATA, array( $this, 'AJAX_get_form_data' ) );
		add_action( 'wp_ajax_' . self::AJAX_ACTION_ADD_FORM_FIELD, array( $this, 'AJAX_add_form_field' ) );

	}

	/**
	 * Get last batch
	 *
	 * @return array|null Array with batch information (localized "last updated" date and ID)
	 */
	protected function get_last_batch() {

		$batch = Batch::all(
			array( 'limit' => 1, 'order' => 'ID', 'sort' => 'DESC' )
		);

		if ( empty( $batch[0] ) ) {
			return null;
		}

		$last_batch = $batch[0];

		return array(
			'id'        => $last_batch['id'],
			'date'      => date_i18n( get_option( 'date_format' ), $last_batch['updated'] ),
			'timestamp' => $last_batch['updated'],
		);
	}

	/**
	 * Returns translation strings to be used in UI
	 *
	 * @return array Array of strings, all sanitized by esc_html()
	 */
	protected static function strings() {

		return array(
			'app'                => array(
				'previous_import_detected' => esc_html__( 'It appears that you never finished importing %s that you started on %s. Do you want to resume import or start a new import?', 'gravityview-importer' ),
				'resume'                   => esc_html__( 'Resume', 'gravityview-importer', 'gravityview-importer' ),
				'start_new'                => esc_html__( 'Start New Import', 'gravityview-importer' ),
			),
			'modal'              => array(
				'continue' => esc_html_x( 'Continue', 'Dialog box - confirm action', 'gravityview-importer' ),
				'confirm'  => esc_html_x( 'Confirm', 'Dialog box - confirm action', 'gravityview-importer' ),
				'cancel'   => esc_html_x( 'Cancel', 'Dialog box - cancel action', 'gravityview-importer' ),
				'close'    => esc_html_x( 'Close', 'Dialog box - close action', 'gravityview-importer' ),
			),
			'progress'           => array(
				'select_source' => esc_html_x( 'Select Source', 'Import navigation step', 'gravityview-importer' ),
				'select_form'   => esc_html_x( 'Select Form', 'Import navigation step', 'gravityview-importer' ),
				'map_fields'    => esc_html_x( 'Map Fields', 'Import navigation step', 'gravityview-importer' ),
				'configure'     => esc_html_x( 'Configure', 'Import navigation step', 'gravityview-importer' ),
				'import'        => esc_html_x( 'Import', 'Import navigation step', 'gravityview-importer' ),
				'change_form'   => esc_html_x( 'Change Form', 'Change selected GF form', 'gravityview-importer' ),
			),
			'select_source'      => array(
				'import_source'       => esc_html__( 'How would you like to import data?', 'gravityview-importer' ),
				'upload_csv'          => esc_html_x( 'Upload CSV', 'Import action', 'gravityview-importer' ),
				'import_from_gsheets' => esc_html_x( 'Import from Google Sheets', 'Import action', 'gravityview-importer' ),
				'connect_to_ftp'      => esc_html_x( 'Connect to FTP', 'Import action', 'gravityview-importer' ),
				'coming_soon'         => esc_html__( 'Coming Soon', 'gravityview-importer' ),
			),
			'upload_csv'         => array(
				'drop_file_or_click_to_upload' => esc_html__( 'Drop file or click anywhere to upload', 'gravityview-importer' ),
				'csv_data_being_added'         => esc_html__( 'Your CSV file data is being added to your site. Please wait while this completes.', 'gravityview-importer' ),
				'do_not_close_page'            => esc_html__( 'Do not close this page.', 'gravityview-importer' ),
				'max_upload_size'              => sprintf(
					esc_html_x( 'Maximum upload file size: %s.', '%s is replaced with file size in megabytes', 'gravityview-importer' ),
					size_format( wp_max_upload_size() )
				),
				'upload_error'                 => esc_html__( 'CSV file failed to upload.', 'gravityview-importer' ),
			),
			'select_form'        => array(
				'import_source'            => esc_html_x( 'Import source', 'Where import data is coming from', 'gravityview-importer' ),
				'existing_form'            => esc_html__( 'An Existing Form', 'gravityview-importer' ),
				'new_form'                 => esc_html__( 'Create a New Form', 'gravityview-importer' ),
				'where_to_import'          => esc_html__( 'Where would you like to import the entries?', 'gravityview-importer' ),
				'change_form'              => esc_html_x( 'Change', 'Action to change form type (existing or new)', 'gravityview-importer' ),
				'choose_form'              => esc_html__( 'Choose a form for this import', 'gravityview-importer' ),
				'form_not_found'           => esc_html__( 'No forms were found matching the search criterion.', 'gravityview-importer' ),
				'form_does_not_exist'      => esc_html__( 'Gravity Forms form does not exist.', 'gravityview-importer' ),
				'search_forms_placeholder' => esc_html__( 'Type a form name to search', 'gravityview-importer' ),
				'add_new_form'             => esc_html__( 'Add a new form', 'gravityview-importer' ),
				'add_new_form_placeholder' => esc_html__( 'New form name', 'gravityview-importer' ),
				'title_not_unique'         => esc_html__( 'A form with the same title already exists. Please select a unique title.', 'gravityview-importer' ),
			),
			'map_fields'         => array(
				'processing_import_data'                       => esc_html__( 'Please wait while your import data is being processed.', 'gravityview-importer' ),
				'getting_form_fields'                          => esc_html_x( 'Getting form fields', 'Import data processing status', 'gravityview-importer' ),
				'creating_import_task'                         => esc_html_x( 'Creating import task', 'Import data processing status', 'gravityview-importer' ),
				'getting_data'                                 => esc_html_x( 'Getting data preview', 'Import data processing status', 'gravityview-importer' ),
				'failed_to_process_import_data'                => esc_html__( 'We were unable to process your import data.', 'gravityview-importer' ),
				'map_columns_to_fields'                        => esc_html_x( 'Map CSV columns to %s form fields', '%s is replace with form name', 'gravityview-importer' ),
				'map_columns_to_fields_desc'                   => esc_html__( 'You can skip columns by selecting "Do Not Import". To create a new form field, select "Add Form Field".', 'gravityview-importer' ),
				'create_fields_and_map_columns_to_fields'      => esc_html_x( 'Create a form named %s.', '%s is replace with form name', 'gravityview-importer' ),
				'create_fields_and_map_columns_to_fields_desc' => esc_html__( 'Choose which CSV columns will be turned into form fields. Set the field type to match the data.', 'gravityview-importer' ),
				'select_form_field'                            => esc_html__( 'Select Form Field', 'gravityview-importer' ),
				'import_to'                                    => esc_html__( 'Import to&hellip;', 'gravityview-importer' ),
				'duplicate_column_field'                       => esc_html__( 'This field is already assigned to another column. Do you want to re-assign it?', 'gravityview-importer' ),
				'reassign'                                     => esc_html_x( 'Re-assign', 'Action to re-assign previously selected column field to a new column', 'gravityview-importer' ),
				'add_form_field'                               => esc_html__( 'Add Form Field', 'gravityview-importer' ),
				'do_not_import'                                => esc_html__( 'Do Not Import', 'gravityview-importer' ),
				'field_type'                                   => esc_html__( 'Field Type', 'gravityview-importer' ),
				'field_label'                                  => esc_html_x( 'Field Label', 'gravityview-importer' ),
				'field_inputs'                                 => esc_html_x( 'Field Inputs', 'gravityview-importer' ),
				'data_header'                                  => esc_html_x( 'CSV Header', 'gravityview-importer' ),
				'add_new_field_label'                          => esc_html__( 'New field label', 'gravityview-importer' ),
				'invalid_form_or_field_type'                   => esc_html__( 'Form or field type does not exist.', 'gravityview-importer' ),
				'failed_to_add_field'                          => esc_html__( 'We were unable to add this field.', 'gravityview-importer' ),
				'no_import_data_found'                         => esc_html__( 'We could not find any data to import.', 'gravityview-importer' ),
				'no_import_data_found_explanation'             => esc_html__( 'This can be because your CSV file is malformed, empty or due to a server error.', 'gravityview-importer' ),
				'only_header_is_found'                         => esc_html__( 'Only a header row was detected.', 'gravityview-importer' ),
				'only_header_is_found_explanation'             => esc_html__( 'GravityView requires a header row and at least one data row. Please [link]refer to our guide[/link] for CSV file formatting tips.', 'gravityview-importer' ),
				'try_again_or_switch_import_source'            => esc_html__( 'Please try again or select a different import source.', 'gravityview-importer' ),
				'field_contains_multiple_inputs'               => esc_html_x( 'The %s field type contains multiple inputs that can be mapped to your import data.', '%s is replaced by GF field type', 'gravityview-importer' ),
				'select_import_column'                         => esc_html__( 'Select Import Column', 'gravityview-importer' ),
				'warning'                                      => esc_html_x( 'Warning', 'Used to indicate danger of performing an action', 'gravityview-importer' ),
				'entry_overwrite_warning'                      => esc_html__( 'This will update values for existing entries that share the same Entry ID. Click Cancel to create new entries during import.', 'gravityview-importer' ),
				'entry_id_detected'                            => esc_html__( 'One of the columns in your CSV file maps to an Entry ID field.', 'gravityview-importer' ),
				'mapped'                                       => esc_html_x( 'Mapped', 'Indicated that a CSV column is mapped to GF field', 'gravityview-importer' ),
				'clear_field'                                  => esc_html__( 'Clear field selection', 'gravityview-importer' ),
				'field_properties'                             => esc_html__( 'Open field properties', 'gravityview-importer' ),
				'column_label'                                 => esc_html_x( 'Column', 'Multi-column list column label', 'gravityview-importer' ),
			),
			'multi_input_fields' => array(
				'field_contains_multiple_inputs' => esc_html_x( 'The %s field type contains multiple inputs that can be mapped to your import data.', '%s is replaced by GF field type', 'gravityview-importer' ),
				'input'                          => esc_html_x( 'Input', 'Field input', 'gravityview-importer' ),
				'input_label'                    => esc_html__( 'Field Input Label', 'gravityview-importer' ),
				'csv_column'                     => esc_html__( 'CSV Column', 'gravityview-importer' ),
				'add_new_input'                  => esc_html__( 'Add New Field Input', 'gravityview-importer' ),
			),
			'multi_column_lists' => array(
				'maybe_cancel'            => esc_html__( 'If this List field will not have Mutliple Columns, click the Cancel button.', 'gravityview-importer' ),
				'list_field_description'  => esc_html__( 'A list field can have multiple rows of data that are exported by Gravity Forms as separate columns in a CSV file.', 'gravityview-importer' ),
				'select_rows'             => esc_html__( 'Select one or multiple CSV columns to associate as rows to this field:', 'gravityview-importer' ),
				'list_rows'               => esc_html__( 'List Rows', 'gravityview-importer' ),
				'list_columns'            => esc_html__( 'List Columns', 'gravityview-importer' ),
				'columns_detected'        => esc_html_x( 'Based on the associated rows, we detected that your list has %s columns. Please provide a label for each column:', '%s is replaced with number of columns in a list', 'gravityview-importer' ),
				'row'                     => esc_html_x( 'List Row %s', '%s is replaced with list row number', 'gravityview-importer' ),
				'column'                  => esc_html_x( 'List Column %s', '%s is replaced with list column number', 'gravityview-importer' ),
				'column_name'             => esc_html__( 'Column Name', 'gravityview-importer' ),
				'column_already_assigned' => esc_html__( 'This column will be re-assigned', 'gravityview-importer' ),
				'select_csv_column'       => esc_html__( 'Select a CSV Column', 'gravityview-importer' ),
			),
			'date_format_filter' => array(
				'column_contains_date'           => esc_html__( 'This column contains a date.', 'gravityview-importer' ),
				'date_recognized'                => esc_html_x( 'We recognized %s as %s. If this is incorrect, please select one of the available formats or specify your own:', '%s are replaced with column value and default date format, respectively', 'gravityview-importer' ),
				'date_unrecognized'              => esc_html_x( "We couldn't recognize %s using the default %s format. Please select one of the other possible formats or specify your own:", '%s values are replaced with shortcodes used for date formatting', 'gravityview-importer' ),
				'custom_format_hint'             => esc_html_x( 'Use %s for day, %s for month, %s for year, %s to skip a single character and %s to skip multiple characters. Day, month, and year are all required by Gravity Forms.', '%s are replaced with day (DD), month (MM) and year (YYYY) abbreviations', 'gravityview-importer' ),
				'custom_date_format_placeholder' => esc_html__( 'Custom Date Format', 'gravityview-importer' ),
				'select_date_format'             => esc_html__( 'Select Date Format:', 'gravityview-importer' ),
				'custom_format'                  => esc_html__( 'Custom Format', 'gravityview-importer' ),
				'invalid_date'                   => esc_html__( 'Date Not Recognized', 'gravityview-importer' ),
				'incomplete_date'                => esc_html__( 'Date field must contain day, month and year values. Consider using a Number field type instead.', 'gravityview-importer' ),
				'date_live_preview'              => esc_html__( 'Live Preview:', 'gravityview-importer' ),
				'date_live_preview_hint'         => esc_html_x( '%s is recognized as %s', 'Helper text showing how the value will be interpreted', 'gravityview-importer' ),
				'mm'                             => esc_html_x( 'MM', 'Date format - month', 'gravityview-importer' ),
				'dd'                             => esc_html_x( 'DD', 'Date format - date', 'gravityview-importer' ),
				'yyyy'                           => esc_html_x( 'YYYY', 'Date format - year', 'gravityview-importer' ),
			),
			'time_format_filter' => array(
				'column_contains_time'           => esc_html__( 'This column contains a time value.', 'gravityview-importer' ),
				'time_recognized'                => esc_html_x( 'We recognized %s as %s. If this is incorrect, please select one of the available formats or specify your own:', '%s are replaced with column value and default time format, respectively', 'gravityview-importer' ),
				'time_unrecognized'              => esc_html_x( "We couldn't recognize %s using the default %s format. Please select one of the other possible formats or specify your own:", '%s are replaced with column value and default time format, respectively', 'gravityview-importer' ),
				'custom_format_hint'             => esc_html_x( 'Use %s for hour, %s for minute, %s for time period, %s to skip a single character and %s to skip multiple characters', '%s values are replaced with shortcodes used for time formatting', 'gravityview-importer' ),
				'custom_time_format_placeholder' => esc_html__( 'Custom Time Format', 'gravityview-importer' ),
				'select_time_format'             => esc_html__( 'Select Time Format:', 'gravityview-importer' ),
				'custom_format'                  => esc_html__( 'Custom Format', 'gravityview-importer' ),
				'invalid_time'                   => esc_html__( 'Time Not Recognized', 'gravityview-importer' ),
				'incomplete_time'                => esc_html__( 'Time field must contain hour and minute values. Consider using a Number field type instead.', 'gravityview-importer' ),
				'time_live_preview'              => esc_html__( 'Live Preview:', 'gravityview-importer' ),
				'time_live_preview_hint'         => esc_html_x( '%s is recognized as %s', 'Helper text showing how the value will be interpreted', 'gravityview-importer' ),
				'mm'                             => esc_html_x( 'mm', 'Time format - minute', 'gravityview-importer' ),
				'hh'                             => esc_html_x( 'hh', 'Time format - hour', 'gravityview-importer' ),
				'ss'                             => esc_html_x( 'ss', 'Time format - second', 'gravityview-importer' ),
			),
			'entry_notes_filter' => array(
				'invalid_notes' => esc_html__( 'Entry notes not recognized', 'gravityview-importer' ),
				'found_x_notes' => esc_html_x( '%s entry note(s)', '%s is replaced with the number of recognized entry notes', 'gravityview-importer' ),
			),
			'list_json_filter'   => array(
				'invalid_list'         => esc_html__( 'List data not recognized', 'gravityview-importer' ),
				'found_x_columns_rows' => esc_html_x( '%s column(s); %s row(s)', '%s are replaced with the number of detected list columns and rows, respectively', 'gravityview-importer' ),
				'found_x_rows'         => esc_html_x( '%s row(s)', '%s is replaced with the number of detected list rows', 'gravityview-importer' ),
			),
			'configure'          => array(
				'configure_options'   => esc_html__( 'Configure Import Options', 'gravityview-importer' ),
				'create_and_continue' => esc_html__( 'Create Form And Continue With Import', 'gravityview-importer' ),
				'process_feeds'       => array(
					'title'              => esc_html__( 'Process Feeds', 'gravityview-importer' ),
					'description'        => esc_html__( 'available form feeds will be executed for each entry', 'gravityview-importer' ),
					'run_for_each_entry' => esc_html__( 'Run these actions for each entry:', 'gravityview-importer' ),
				),
				'upload_files'        => array(
					'title'       => esc_html__( 'Upload Files', 'gravityview-importer' ),
					'description' => esc_html__( 'upload files mapped to "File Upload" fields', 'gravityview-importer' ),
				),
				'ignore_required'     => array(
					'title'       => esc_html__( 'Ignore Required Form Fields', 'gravityview-importer' ),
					'description' => esc_html__( 'entry will be imported even if it is missing required form fields', 'gravityview-importer' ),
				),
				'ignore_errors'       => array(
					'title'       => esc_html__( 'Continue Processing If Errors Occur', 'gravityview-importer' ),
					'description' => esc_html__( 'import will not interrupt when errors are encountered', 'gravityview-importer' ),
				),
				'overwrite_post_data' => array(
					'title'       => esc_html__( 'Overwrite Post Data', 'gravityview-importer' ),
					'description' => esc_html__( 'existing post content will be overwritten by the imported data', 'gravityview-importer' ),
				),
				'use_default_values'  => array(
					'title'       => esc_html__( 'Use Default Field Values', 'gravityview-importer' ),
					'description' => esc_html__( 'empty fields will be populated with default values', 'gravityview-importer' ),
				),
				'email_notifications' => array(
					'title'       => esc_html__( 'Email Notifications', 'gravityview-importer' ),
					'description' => esc_html__( 'receive email notification for each imported record', 'gravityview-importer' ),
				),
				'skip_validation'     => array(
					'title'       => esc_html__( 'Skip Field Validation', 'gravityview-importer' ),
					'description' => esc_html__( "do not validate imported data", 'gravityview-importer' ),
				),
				'conditional_import'  => array(
					'title'            => esc_html__( 'Conditional Import', 'gravityview-importer' ),
					'description'      => esc_html__( 'only import rows if they match certain conditions', 'gravityview-importer' ),
					'import_row_if'    => esc_html_x( 'Import the row if', 'Part of complete sentence: "Import row if [all|any] of the following match:"', 'gravityview-importer' ),
					'there_is_match'   => esc_html_x( 'of the following match:', 'Part of complete sentence: "Import row if [all|any] of the following match:"', 'gravityview-importer' ),
					'all'              => esc_html_x( 'all', 'Part of complete sentence: "Import row if [all|any] of the following match:"', 'gravityview-importer' ),
					'any'              => esc_html_x( 'any', 'Part of complete sentence: "Import row if [all|any] of the following match:"', 'gravityview-importer' ),
					'is'               => esc_html_x( 'is', 'Comparison operator: "field X [is] Y"', 'gravityview-importer' ),
					'is_not'           => esc_html_x( 'is not', 'Comparison operator: "field X [is not] Y"', 'gravityview-importer' ),
					'greater_than'     => esc_html_x( 'greater than', 'Comparison operator: "field X [greater than] Y"', 'gravityview-importer' ),
					'less_than'        => esc_html_x( 'less than', 'Comparison operator: "field X [less than] Y"', 'gravityview-importer' ),
					'contains'         => esc_html_x( 'contains', 'Comparison operator: "field X [contains] Y"', 'gravityview-importer' ),
					'enter_value'      => esc_html__( 'Enter a value', 'gravityview-importer' ),
					'add_condition'    => esc_html__( 'Add another condition', 'gravityview-importer' ),
					'remove_condition' => esc_html__( 'Remove this condition', 'gravityview-importer' ),
				),
			),
			'import_data'        => array(
				'preparing_to_import'              => esc_html__( 'Preparing to import your data.', 'gravityview-importer' ),
				'importing_data'                   => esc_html__( 'Please wait while we import your data.', 'gravityview-importer' ),
				'do_not_navigate'                  => esc_html__( 'Do not navigate away from this page.', 'gravityview-importer' ),
				'failed_to_import_data'            => esc_html__( 'We were unable to import your data.', 'gravityview-importer' ),
				'processed_x_of_y_records'         => esc_html_x( 'Processed %s of %s records', '%s are replaced with current and total record count, respectively', 'gravityview-importer' ),
				'import_finished'                  => esc_html__( 'Import has finished.', 'gravityview-importer' ),
				'import_finished_with_errors'      => esc_html__( 'Import has finished with errors.', 'gravityview-importer' ),
				'processed_x_records'              => esc_html_x( 'We have processed %s records:', '%s is replaced with record count', 'gravityview-importer' ),
				'processed_and_imported_x_records' => esc_html_x( 'We have processed and %s all %s records.', '%s are replaced with action ("imported" or "updated") and record count, respectively', 'gravityview-importer' ),
				'processed_x_before_failed'        => esc_html_x( '%s records: %s were %s before the import encountered an error', '%s are replaced with total number of records, action (imported or updated) and number of processed records, respectively ', 'gravityview-importer' ),
				'no_records_processed_error'       => esc_html__( 'Processing could not finish due to a server error. Please try modifying field mapping or get in touch with our support.', 'gravityview-importer' ),
				'view_imported_records'            => esc_html__( 'View Imported Records', 'gravityview-importer' ),
				'start_new_import'                 => esc_html__( 'Start New Import', 'gravityview-importer' ),
				'modify_import_configuration'      => esc_html__( 'Modify Import Configuration', 'gravityview-importer' ),
				'download_failed_records'          => esc_html__( 'Download Failed Records', 'gravityview-importer' ),
				'error_report_filename'            => esc_html_x( 'import_error_report-%s', 'Error report filename; %s is replaced with import batch ID', 'gravityview-importer' ),
				'row'                              => esc_html_x( 'Row #%s', '', 'gravityview-importer' ),
				'and'                              => esc_html__( 'and', 'gravityview-importer' ),
				'imported'                         => esc_html_x( 'imported', 'Row status', 'gravityview-importer' ),
				'skipped'                          => esc_html_x( 'skipped', 'Row status', 'gravityview-importer' ),
				'rejected'                         => esc_html_x( 'rejected due to an error ([link]view log[/link])', 'Row status', 'gravityview-importer' ),
				'updated'                          => esc_html_x( 'updated', 'Row status', 'gravityview-importer' ),
			),
			'network_errors'     => array(
				'failed_network_request' => esc_html__( 'Server could not be reached or connection was aborted/interrupted.', 'gravityview-importer' ),
				'empty_response'         => esc_html__( 'Server returned an empty response.', 'gravityview-importer' ),
				'invalid_response'       => esc_html__( 'Server returned an invalid response.', 'gravityview-importer' ),
				'unknown_error'          => esc_html__( 'Network request could not be completed due to an unknown reason.', 'gravityview-importer' ),
				'wp_error'               => esc_html__( 'WordPress has returned an unknown error.', 'gravityview-importer' ),
			),
			'application_error'  => array(
				'error_occured'     => esc_html__( 'An Error Has Occured', 'gravityview-importer' ),
				'cannot_continue'   => esc_html__( 'The importer encountered an error that prevents it from continuing:', 'gravityview-importer' ),
				'help_troubleshoot' => sprintf( esc_html__( 'We&rsquo;re here to help! Share this error with support:', 'gravityview-importer' ), esc_html__( 'Contact Support', 'gravityview-importer' ) ),
				'contact_support'   => esc_html__( 'Contact Support', 'gravityview-importer' ),
				'processing_log'    => esc_html__( 'Processing error log&hellip;', 'gravityview-importer' )
			),
			'shared'             => array(
				'save'                         => esc_html__( 'Save', 'gravityview-importer' ),
				'update'                       => esc_html__( 'Update', 'gravityview-importer' ),
				'continue_with_import'         => esc_html__( 'Continue With Import', 'gravityview-importer' ),
				'try_again'                    => esc_html__( 'Try Again', 'gravityview-importer' ),
				'try_again_or_contact_support' => esc_html__( 'Please try again or contact support.', 'gravityview-importer' ),
				'change_source'                => esc_html_x( 'Change Source', 'Change selected import source (CSV, FTP, Google Sheets, etc.)', 'gravityview-importer' ),
				'change_field_mapping'         => esc_html_x( 'Change Field Mapping', 'Change form field mapping', 'gravityview-importer' ),
				'field_label_not_available'    => esc_html__( 'Field Label Not Available', 'gravityview-importer' ),
			)
		);
	}

	/**
	 * Register and enqueue assets; localize script
	 *
	 * @return void
	 */
	public function enqueue_scripts() {

		if ( ! isset( $_GET['page'] ) || self::PAGE_SLUG !== $_GET['page'] ) {
			return;
		}

		$options = array(
			'ajax_nonce'            => wp_create_nonce( self::NONCE_HANDLE ),
			'api_nonce'             => wp_create_nonce( 'wp_rest' ),
			'action_csv_upload'     => self::AJAX_ACTION_CSV_UPLOAD,
			'action_form_data'      => self::AJAX_ACTION_FORM_DATA,
			'action_add_form_field' => self::AJAX_ACTION_ADD_FORM_FIELD,
			'localization'          => self::strings(),
			'locale'                => str_replace( '_', '-', get_locale() ), // JS uses dash instead of underscore for locales
			'forms'                 => $this->get_forms(),
			'field_types'           => $this->get_available_field_types(),
			'api_url'               => get_rest_url( null, Core::rest_namespace ),
			'gf_entries_url'        => menu_page_url( 'gf_entries', false ),
			'last_batch'            => $this->get_last_batch(),
			'beacon'                => array(
				'suggestions'  => $this->get_beacon_suggestions(),
				'session_data' => $this->get_beacon_configuration( 'session-data' ),
				'prefill'      => $this->get_beacon_configuration( 'prefill' ),
			),
		);

		wp_enqueue_script( self::ASSETS_HANDLE, plugins_url( 'assets/js/gravityview-import-entries.js', __FILE__ ), array( 'wp-element' ), $this->plugin_version, true );
		wp_add_inline_script( self::ASSETS_HANDLE, $this->get_beacon_code(), 'before' );
		wp_enqueue_style( self::ASSETS_HANDLE, plugins_url( 'assets/css/gravityview-import-entries.css', __FILE__ ), array(), $this->plugin_version );
		wp_localize_script( self::ASSETS_HANDLE, 'GV_IMPORT_ENTRIES', $options );
	}

	/**
	 * Return an array of known multi-input fields
	 *
	 * @return array
	 */
	public function get_multi_input_fields() {

		return array(
			'address' => array(
				1 => array(
					'id'        => 1,
					'parent_id' => 'address',
					'label'     => esc_html__( 'Street Address', 'gravityview-importer' )
				),
				2 => array(
					'id'        => 2,
					'parent_id' => 'address',
					'label'     => esc_html__( 'Address Line 2', 'gravityview-importer' )
				),
				3 => array(
					'id'        => 3,
					'parent_id' => 'address',
					'label'     => esc_html__( 'City', 'gravityview-importer' )
				),
				4 => array(
					'id'        => 4,
					'parent_id' => 'address',
					'label'     => esc_html__( 'State / Province / Region', 'gravityview-importer' )
				),
				5 => array(
					'id'        => 5,
					'parent_id' => 'address',
					'label'     => esc_html__( 'ZIP / Postal Code', 'gravityview-importer' )
				),
				6 => array(
					'id'        => 6,
					'parent_id' => 'address',
					'label'     => esc_html__( 'Country', 'gravityview-importer' )
				),
			),
			'name'    => array(
				2 => array(
					'id'        => 2,
					'parent_id' => 'name',
					'label'     => esc_html_x( 'Prefix', 'Relates to name prefix (e.g., Mr., Ms.)', 'gravityview-importer' )
				),
				3 => array(
					'id'        => 3,
					'parent_id' => 'name',
					'label'     => esc_html_x( 'First', 'Relates to name ', 'gravityview-importer' )
				),
				4 => array(
					'id'        => 4,
					'parent_id' => 'name',
					'label'     => esc_html_x( 'Middle', 'Relates to name', 'gravityview-importer' )
				),
				6 => array(
					'id'        => 6,
					'parent_id' => 'name',
					'label'     => esc_html_x( 'Last', 'Relates to name', 'gravityview-importer' )
				),
				8 => array(
					'id'        => 8,
					'parent_id' => 'name',
					'label'     => esc_html_x( 'Suffix', 'Relates to name', 'gravityview-importer' )
				)
			)
		);
	}

	/**
	 * Get a list of suggested articles for each UI navigation step
	 *
	 * @return array HS Beacon suggested articles
	 */
	public function get_beacon_suggestions() {

		return array(
			'upload_csv'        => array(
				'5c3e73c304286304a71e4118',
				'55382ee0e4b0a2d7e23f733a',
				'55383f03e4b0a2d7e23f735e',
				'5b7172822c7d3a03f89d9f33',
				array(
					'text' => esc_html__( 'See all Entry Importer help articles', 'gravityview-importer' ),
					'url'  => 'https://docs.gravityview.co/category/255-gravity-forms-importer'
				),
			),
			'select_form'       => array(
				'5c3e73c304286304a71e4118',
				'5d36314804286347867546b9',
				array(
					'text' => esc_html__( 'See all Entry Importer help articles', 'gravityview-importer' ),
					'url'  => 'https://docs.gravityview.co/category/255-gravity-forms-importer'
				),
			),
			'map_fields'        => array(
				'5c3e73c304286304a71e4118',
				'5d36218e2c7d3a2ec4bf428a',
				array(
					'text' => esc_html__( 'See all Entry Importer help articles', 'gravityview-importer' ),
					'url'  => 'https://docs.gravityview.co/category/255-gravity-forms-importer'
				),
			),
			'configure_options' => array(
				'5d36928304286347867548e0',
				'5c3e73c304286304a71e4118',
				array(
					'text' => esc_html__( 'See all Entry Importer help articles', 'gravityview-importer' ),
					'url'  => 'https://docs.gravityview.co/category/255-gravity-forms-importer'
				),
			),
			'import_data'       => array(
				'5c3e73c304286304a71e4118',
				array(
					'text' => esc_html__( 'See all Entry Importer help articles', 'gravityview-importer' ),
					'url'  => 'https://docs.gravityview.co/category/255-gravity-forms-importer'
				),
			)
		);
	}

	/**
	 * Get HS Beacon configuration options
	 *
	 * @param string|null $property Configuration property. Use "/" to access nested value.
	 *
	 * @return array|string If $property is passed, returns value of property. Otherwise, returns full configuration array.
	 */
	public function get_beacon_configuration( $property = null ) {

		$current_user = wp_get_current_user();

		$beacon_configuration = array(
			'init'         => self::HS_BEACON_KEY,
			'config'       => array(
				'color'       => '#4d9bbe',
				'poweredBy'   => false,
				'docsEnabled' => true,
				'topArticles' => true,
				'iconImage'   => 'question',
				'zIndex'      => 9991,
			),
			// Help Scout length limit is 200 characters
			'session-data' => array(
				'Plugin Version'    => mb_substr( $this->plugin_version, 0, 200 ),
				'WordPress Version' => mb_substr( get_bloginfo( 'version', 'display' ), 0, 200 ),
				'PHP Version'       => mb_substr( phpversion() . ' on ' . esc_html( rgar( $_SERVER, 'SERVER_SOFTWARE' ) ), 200 ),
			),
			'prefill'      => array(
				'name'  => mb_substr( $current_user->display_name, 0, 80 ),
				'email' => mb_substr( $current_user->user_email, 0, 80 ),
			),
		);

		if ( ! $property ) {
			return $beacon_configuration;
		}

		return rgars( $beacon_configuration, $property, $beacon_configuration );
	}

	/**
	 * Output inline JS code that initialized HS Beacon
	 *
	 * @return string Inline JS
	 */
	public function get_beacon_code() {

		$beacon_configuration = json_encode( $this->get_beacon_configuration() );

		return <<<JS
!function(e,t,n){function a(){var e=t.getElementsByTagName("script")[0],n=t.createElement("script");n.type="text/javascript",n.async=!0,n.src="https://beacon-v2.helpscout.net",e.parentNode.insertBefore(n,e)}if(e.Beacon=n=function(t,n,a){e.Beacon.readyQueue.push({method:t,options:n,data:a})},n.readyQueue=[],"complete"===t.readyState)return a();e.attachEvent?e.attachEvent("onload",a):e.addEventListener("load",a,!1)}(window,document,window.Beacon||function(){});
var beaconConfig = {$beacon_configuration}; for (var param in beaconConfig) { window.Beacon( param, beaconConfig[param]); }
JS;
	}

	/**
	 * Return a list of GF field types and labels
	 *
	 * @return array
	 */
	public function get_available_field_types() {

		$GF_multi_input_fields = $this->get_multi_input_fields();

		$field_types    = array();
		$post_fields    = array();
		$product_fields = array();
		foreach ( \GF_Fields::get_all() as $field ) {
			if ( in_array( $field->type, $this->get_excluded_fields( 'new' ) ) ) {
				continue;
			}

			if ( preg_match( '/^post_/', $field->type ) ) {
				$post_fields[ $field->type ] = array(
					'id'        => $field->type,
					'parent_id' => 'post_fields',
					'label'     => esc_html( sprintf( _x( 'Post %s', 'Generates a label for Post fields based on the type of field', 'gravityview-importer' ), ucwords( $field->get_form_editor_field_title() ) ) )
				);

				continue;
			}

			if ( in_array( $field->type, array( 'option', 'total', 'shipping', 'quantity', 'product', 'price' ) ) ) {
				$product_fields[ $field->type ] = array(
					'id'        => $field->type,
					'parent_id' => 'product_fields',
					'label'     => $field->type === 'product' ? ucwords( $field->get_form_editor_field_title() ) : esc_html( sprintf( __( 'Product %s', 'gravityview-importer' ), ucwords( $field->get_form_editor_field_title() ) ) )
				);

				continue;
			}

			$field_types[ $field->type ] = array(
				'id'    => $field->type,
				'label' => ucwords( $field->get_form_editor_field_title() )
			);

			if ( 'checkbox' === $field->type ) {
				$field_types[ $field->type ]['multi']          = true;
				$field_types[ $field->type ]['dynamic_inputs'] = true;

				continue;
			}

			if ( 'list' === $field->type ) {
				$field_types[ $field->type ]['with_properties'] = true;
				$field_types[ $field->type ]['dynamic_choices'] = true;

				continue;
			}

			if ( in_array( $field->type, array_keys( $this->get_form_fields_with_filter() ) ) ) {
				$field_types[ $field->type ]['with_properties'] = true;
				$field_types[ $field->type ]['filter']          = $this->get_form_fields_with_filter()[ $field->type ];
			}

			if ( ! empty( $GF_multi_input_fields[ $field->type ] ) ) {
				$field_types[ $field->type ]['multi']  = true;
				$field_types[ $field->type ]['inputs'] = $GF_multi_input_fields[ $field->type ];
			}
		}

		if ( ! empty( $post_fields ) ) {
			$field_types['post_fields'] = array(
				'id'           => 'post_fields',
				'label'        => esc_html__( 'Post Fields', 'gravityview-importer' ),
				'multi'        => true,
				'virtualGroup' => true,
				'inputs'       => $post_fields,
			);
		}

		if ( ! empty( $product_fields ) ) {
			$field_types['product_fields'] = array(
				'id'           => 'product_fields',
				'label'        => esc_html__( 'Product Fields', 'gravityview-importer' ),
				'multi'        => true,
				'virtualGroup' => true,
				'inputs'       => $product_fields,
			);
		}

		$field_types['entry_properties'] = array(
			'label'        => esc_html__( 'Entry Properties', 'gravityview-importer' ),
			'id'           => 'entry_properties',
			'multi'        => true,
			'virtualGroup' => true,
			'inputs'       => array(),
			'order'        => 1000,
		);

		foreach ( $this->get_entry_fields() as $entry_field ) {
			// Exclude Entry ID as it does not apply to new forms
			if ( $entry_field['id'] === 'id' ) {
				continue;
			}

			$field_types['entry_properties']['inputs'][ $entry_field['id'] ] = array_merge( $entry_field, array( 'parent_id' => $field_types['entry_properties']['id'] ) );
		}

		$field_types['non_field_data'] = array(
			'label'        => esc_html_x( 'Non-Field Data', 'Entry "meta", which is additional data often used by plugins.', 'gravityview-importer' ),
			'id'           => 'non_field_data',
			'multi'        => true,
			'virtualGroup' => true,
			'inputs'       => array(),
			'order'        => 1001
		);

		$field_types = array_merge( $field_types, $this->get_virtual_fields() );

		$form_entry_meta = $this->get_entry_meta( null );

		foreach ( $form_entry_meta as $entry_meta ) {
			if ( in_array( $entry_meta['id'], $this->get_excluded_fields( 'new' ) ) ) {
				continue;
			}

			$field_types['non_field_data']['inputs'][ $entry_meta['id'] ] = array_merge( $entry_meta, array( 'parent_id' => $field_types['non_field_data']['id'] ) );
		}

		if ( empty( $field_types['non_field_data'] ) ) {
			unset( $field_types['non_field_data'] );
		}

		return $this->sort_array_by_label_and_order( $field_types );
	}

	/**
	 * Return GF form input fields, including entry fields and meta
	 *
	 * @param array $form
	 *
	 * @return array Array of form fields
	 */
	public function get_form_fields( $form ) {

		$form_fields = array(
			'entry_properties' => array(
				'label'        => esc_html__( 'Entry Properties', 'gravityview-importer' ),
				'id'           => 'entry_properties',
				'virtualGroup' => true,
				'inputs'       => array(),
				'order'        => 1000
			),
			'non_field_data'   => array(
				'label'        => esc_html__( 'Non-Field Data', 'gravityview-importer' ),
				'id'           => 'non_field_data',
				'inputs'       => array(),
				'virtualGroup' => true,
				'order'        => 1001
			)
		);

		$form_fields = array_merge( $form_fields, $this->get_virtual_fields() );

		foreach ( $this->get_entry_fields() as $entry_field ) {
			$form_fields['entry_properties']['inputs'][ $entry_field['id'] ] = array_merge( $entry_field, array( 'parent_id' => $form_fields['entry_properties']['id'] ) );
		}

		$form_entry_meta = $this->get_entry_meta( $form['id'] );

		foreach ( $form_entry_meta as $entry_meta ) {
			if ( in_array( $entry_meta['id'], $this->get_excluded_fields( 'existing' ) ) ) {
				continue;
			}

			$form_fields['non_field_data']['inputs'][ $entry_meta['id'] ] = array_merge( $entry_meta, array( 'parent_id' => $form_fields['non_field_data']['id'] ) );
		}

		if ( empty( $form_entry_meta ) ) {
			unset( $form_fields['non_field_data'] );
		}

		foreach ( $form['fields'] as $order => $field ) {
			if ( in_array( $field->type, $this->get_excluded_fields( 'existing' ) ) ) {
				continue;
			}

			$field_label               = $field->get_field_label( false, null );
			$form_fields[ $field->id ] = array(
				'label'    => $field_label,
				'type'     => $field->type,
				'id'       => $field->id,
				'order'    => $order,
				'required' => $field->isRequired,
				'default'  => $field->defaultValue !== ''
			);

			if ( 'list' === $field->type && ! empty( $field->choices ) ) {
				$form_fields[ $field->id ]['with_properties'] = true;
				$form_fields[ $field->id ]['list_choices']    = $field->choices;
			}

			if ( in_array( $field->type, array_keys( $this->get_form_fields_with_filter() ) ) ) {
				$filter = $this->get_form_fields_with_filter()[ $field->type ];

				$form_fields[ $field->id ]['with_properties'] = true;
				$form_fields[ $field->id ]['filter']          = $filter;

				if ( 'timeFormat' === $filter ) {
					$form_fields[ $field->id ]['filterData'] = ( '12' === $field->timeFormat ) ? 'g:i a' : 'H:i';
				} elseif ( 'dateFormat' === $filter ) {
					$form_fields[ $field->id ]['filterData'] = $this->convertGFDateShortcodeToDateTimeFormat( $field->dateFormat );
				}
			}

			// Time field has multiple inputs but is not a true multi-input field, so treat it as a single input field
			if ( empty( $field->inputs ) || 'time' === $field->type ) {
				continue;
			}

			$form_fields[ $field->id ]['multi'] = true;

			$inputs = array();

			foreach ( $field->inputs as $order => $input ) {
				$inputs[ (string) $input['id'] ] = array(
					'label'     => $input['label'],
					'id'        => (string) $input['id'],
					'parent_id' => $field->id,
					'order'     => $order,
				);
			}

			$form_fields[ $field->id ] ['inputs'] = $inputs;
		}

		return $this->sort_array_by_label_and_order( $form_fields );
	}

	/**
	 * Return a list of GF fields that can have custom UI filters applied to them
	 *
	 * @return array
	 */
	public function get_form_fields_with_filter() {

		return array(
			'date' => 'dateFormat',
			'time' => 'timeFormat'
		);
	}

	/**
	 * Return a list of virtual fields such as entry notes
	 *
	 * @return array
	 */
	public function get_virtual_fields() {

		return array(
			'notes' => array(
				'id'      => 'notes',
				'label'   => esc_html__( 'Entry Notes', 'gravityview-importer' ),
				'filter'  => 'entryNotes',
				'is_meta' => true
			)
		);
	}

	/**
	 * Return a list of fields that should be excluded for mapping
	 *
	 * @param string|null $form_type Form type: "new" or "existing"
	 *
	 * @return array
	 */
	public function get_excluded_fields( $form_type = null ) {

		if ( $form_type && ! in_array( $form_type, array( 'new', 'existing' ) ) ) {
			return array();
		}

		$excluded_fields = array(
			'all'      => array(
				'html',
				'page',
				'section',
				'singleproduct',
				'singleshipping',
				'gv_revision_parent_id',
				'gv_revision_date',
				'gv_revision_date_gmt',
				'gv_revision_user_id',
				'gv_revision_changed',
				'gquiz_score',
				'gquiz_percent',
				'gquiz_grade',
				'gquiz_is_pass',
				'gsurvey_score',
				'creditcard',
			),
			'new'      => array(
				'poll',
				'quiz'
			),
			'existing' => array()
		);

		return ( $form_type ) ?
			array_merge( $excluded_fields['all'], $excluded_fields[ $form_type ] ) :
			array_merge( $excluded_fields['all'], $excluded_fields['new'], $excluded_fields['existing'] );
	}

	/**
	 * Get available GF forms
	 *
	 * @return array
	 */
	public function get_forms() {

		return array_map( function ( $form ) {

			return array( 'id' => $form['id'], 'title' => $form['title'] );
		}, \GFAPI::get_forms() );
	}

	/**
	 * When clicking Import Entries from Gravity Forms' Import/Export page, safe redirect to Importer page
	 *
	 * @return void
	 */
	public function redirect_gf_import_export() {

		if ( 'gf_export' !== rgget( 'page' ) ) {
			return;
		}

		if ( 'import_entries' !== rgget( 'view' ) ) {
			return;
		}

		$url = admin_url( 'admin.php?page=' . self::PAGE_SLUG );

		if ( $form_id = rgget( 'formId' ) ) {
			$url .= '#targetForm=' . intval( $_GET['formId'] );
		}

		wp_safe_redirect( $url );

		exit();
	}

	/**
	 * Add "Import Entries" menu item to the Gravity Forms Import/Export page
	 *
	 * @param array $setting_tabs
	 *
	 * @return array
	 */
	public function add_gf_import_export_menu( $setting_tabs ) {

		if ( ! \GFCommon::current_user_can_any( $this->_capabilities ) ) {
			return $setting_tabs;
		}

		// Find an open slot
		$key = isset( $setting_tabs[11] ) ? ( isset( $setting_tabs[12] ) ? 13 : 12 ) : 11;

		$setting_tabs[ $key ] = array(
			'name'  => 'import_entries',
			'label' => __( 'Import Entries', 'gravityview-importer' )
		);

		return $setting_tabs;
	}

	/**
	 * Add new admin menu
	 *
	 * @return void
	 */
	public function add_admin_menu() {

		if ( self::PAGE_SLUG !== rgget( 'page' ) ) {
			return;
		}

		if ( ! \GFCommon::current_user_can_any( $this->_capabilities ) ) {
			return;
		}

		$menu_text = esc_html__( 'Import Entries', 'gravityview-importer' );

		$menu_text = sprintf( '<span title="%s" style="margin: 0;">%s</span>', esc_html__( 'Import entries into Gravity Forms.', 'gravityview-importer' ), $menu_text );

		$which_cap = \GFCommon::current_user_can_which( $this->_capabilities );

		if ( empty( $which_cap ) ) {
			$which_cap = 'gform_full_access';
		}

		add_submenu_page(
			'gf_edit_forms',
			esc_html__( 'Import Entries', 'gravityview-importer' ),
			$menu_text,
			$which_cap,
			self::PAGE_SLUG,
			array( $this, 'render_screen' )
		);

	}

	/**
	 * Render user interface
	 *
	 * @return string HTML output
	 *
	 * @see assets/js/src/app.jsx
	 */
	public function render_screen() {

		/**
		 * @deprecated Renamed to `gravityview/import/ui/before`
		 */
		do_action( 'gravityview-import/before-import' );

		do_action( 'gravityview/import/ui/before' );

		?>
        <div class="wrap">
            <a href="https://gravityview.co/" id="gv-logo">GravityView</a>
            <div id="gv-import-entries">
                <div class="error inline"><p><?php
						printf( esc_html__( 'Required scripts aren\'t loading properly. %s', '%s is replaced with "Please contact support" link.', 'gravityview-importer' ),
							sprintf( '<a href="mailto:support@gravityview.co">%s</a>', esc_html__( 'Please contact support.', 'gravityview-importer' ) )
						);
						?></p></div>
                <!-- (Dynamic content populated by JS)
                 ┏━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┓
                 ┃    ____                 _ _       __     ___                 ┃▒
                 ┃   / ___|_ __ __ ___   _(_) |_ _   \ \   / (_) _____      __  ┃▒
                 ┃  | |  _| '__/ _` \ \ / / | __| | | \ \ / /| |/ _ \ \ /\ / /  ┃▒
                 ┃  | |_| | | | (_| |\ V /| | |_| |_| |\ V / | |  __/\ V  V /   ┃▒
                 ┃   \____|_|  \__,_| \_/ |_|\__|\__, | \_/  |_|\___| \_/\_/    ┃▒
                 ┃                               |___/                          ┃▒
                 ┗━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┛▒
                  ▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒
                -->
            </div>
        </div>
		<?php

		/**
		 * @deprecated Renamed to `gravityview/import/ui/after`
		 */
		do_action( 'gravityview-import/after-import' );

		do_action( 'gravityview/import/ui/after' );
	}

	/**
	 * AJAX action to handle CSV upload
	 *
	 * @return void Exit with JSON response (uploaded CSV filename) or terminate request with error code
	 */
	public function AJAX_csv_upload() {

		if ( ! \GFCommon::current_user_can_any( 'upload_files' ) ) {
			return;
		}

		// Validate AJAX request and upload file data
		$is_valid_nonce  = wp_verify_nonce( rgar( $_POST, 'nonce' ), self::NONCE_HANDLE );
		$is_valid_action = self::AJAX_ACTION_CSV_UPLOAD === rgar( $_POST, 'action' );
		$uploaded_file   = rgar( $_FILES, 'upload' );

		if ( ! $is_valid_nonce || ! $is_valid_action || ! $uploaded_file ) {
			// Return 'forbidden' response if nonce is invalid, otherwise it's a 'bad request'
			wp_die( false, false, array( 'response' => ( ! $is_valid_nonce ) ? 403 : 400 ) );
		}

		if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		}

		$upload_overrides = array(
			'test_form'                => false,
			'test_type'                => true,
			'mimes'                    => array(
				'csv' => 'text/csv',
				'txt' => 'text/plain',
				'tsv' => 'text/tab-separated-values'
			),
			'unique_filename_callback' => function ( $path, $filename, $ext = '.csv' ) {

				return md5( uniqid( time() . $filename ) ) . $ext;
			}
		);

		/**
		 * Fixes issue with multisite uploads not validation {@see https://github.com/gravityview/Import-Entries/issues/187}
		 */
		$allow_mimes_callback = function ( $mimes = array() ) use ( $upload_overrides ) {

			return array_merge( $mimes, $upload_overrides['mimes'] );
		};

		add_filter( 'upload_mimes', $allow_mimes_callback );
		add_filter( 'sanitize_file_name', array( $this, 'sanitize_file_name' ) );

		$handle_upload = wp_handle_upload( $uploaded_file, $upload_overrides );

		remove_filter( 'upload_mimes', $allow_mimes_callback );
		remove_filter( 'sanitize_file_name', array( $this, 'sanitize_file_name' ) );

		if ( ! empty( $handle_upload['error'] ) ) {
			wp_send_json_error(
				new \WP_Error( 'upload_move_error', $handle_upload['error'] )
			);
		} else {
			wp_send_json_success(
				array(
					'file' => wp_normalize_path( $handle_upload['file'] )
				)
			);
		}
	}

	/**
	 * AJAX action to get GF form input fields and feeds
	 *
	 * @return void Exit with JSON response (array of form fields) or terminate request with error
	 */
	public function AJAX_get_form_data() {

		if ( ! \GFCommon::current_user_can_any( 'gravityforms_edit_forms' ) ) {
			return;
		}

		// Validate AJAX request and form ID
		$is_valid_nonce  = wp_verify_nonce( rgar( $_POST, 'nonce' ), self::NONCE_HANDLE );
		$is_valid_action = self::AJAX_ACTION_FORM_DATA === sanitize_text_field( rgar( $_POST, 'action' ) );
		$form_id         = intval( sanitize_text_field( rgar( $_POST, 'formId' ) ) );

		if ( ! $is_valid_nonce || ! $is_valid_action || ! $form_id ) {
			// Return 'forbidden' response if nonce is invalid, otherwise it's a 'bad request'
			wp_die( false, false, array( 'response' => ( ! $is_valid_nonce ) ? 403 : 400 ) );
		}

		$form    = \GFAPI::get_form( $form_id );
		$strings = self::strings();

		if ( ! $form ) {
			$code = 'form_does_not_exist';

			wp_send_json_error(
				new \WP_Error( $code, $strings['select_form'][ $code ] )
			);
		} else {
			wp_send_json_success(
				array(
					'form_fields' => $this->get_form_fields( $form ),
					'form_feeds'  => $this->get_form_feeds( $form_id ),
				)
			);
		}
	}

	/**
	 * AJAX action to add new field to an existing GF form
	 *
	 * @return void Exit with JSON response (array of available forms) or terminate request with error
	 */
	public function AJAX_add_form_field() {

		if ( ! \GFCommon::current_user_can_any( 'gravityforms_edit_forms' ) ) {
			return;
		}

		// Validate AJAX request and form title
		$is_valid_nonce  = wp_verify_nonce( rgar( $_POST, 'nonce' ), self::NONCE_HANDLE );
		$is_valid_action = self::AJAX_ACTION_ADD_FORM_FIELD === rgar( $_POST, 'action' );
		$form_id         = intval( sanitize_text_field( rgar( $_POST, 'formId' ) ) );
		$field_label     = trim( sanitize_text_field( stripslashes( rgar( $_POST, 'fieldLabel' ) ) ) );
		$field_type      = trim( sanitize_text_field( rgar( $_POST, 'fieldType' ) ) );

		if ( ! $is_valid_nonce || ! $is_valid_action || ! $form_id || ! $field_label || ! $field_type ) {
			// Return 'forbidden' response if nonce is invalid, otherwise it's a 'bad request'
			wp_die( false, false, array( 'response' => ( ! $is_valid_nonce ) ? 403 : 400 ) );
		}

		$form  = \GFAPI::get_form( $form_id );
		$field = \GF_Fields::get( $field_type );

		$strings               = self::strings();
		$GF_multi_input_fields = $this->get_multi_input_fields();

		if ( ! $form || ! $field ) {
			$code = 'invalid_form_or_field_type';

			wp_send_json_error(
				new \WP_Error( $code, $strings['map_fields'][ $code ] )
			);
		}

		$new_field_id = 0;
		foreach ( $form['fields'] as $form_field ) {
			if ( $form_field->id > $new_field_id ) {
				$new_field_id = $form_field->id;
			}
		}
		$new_field_id ++;

		$field->id    = $new_field_id;
		$field->label = $field_label;

		if ( ! empty( $GF_multi_input_fields[ $field_type ] ) ) {
			$field->inputs = array_map( function ( $input ) use ( $field ) {

				return array(
					'id'    => "{$field->id}.{$input['id']}",
					'label' => $input['label'],
					'name'  => ''
				);
			}, $GF_multi_input_fields[ $field_type ] );
		}

		$form['fields'][] = $field;

		$result = \GFAPI::update_form( $form );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( $result );
		} else {
			wp_send_json_success(
				array(
					'field_id'    => $new_field_id,
					'form_fields' => $this->get_form_fields( $form )
				)
			);
		}
	}

	/**
	 * Return a DateTime format representation of GF's date shortcode (e..g, mdy => m/d/Y)
	 *
	 * @param string $shortcode
	 *
	 * @return array|string
	 */
	public function convertGFDateShortcodeToDateTimeFormat( $shortcode ) {

		$shortcode_map = array(
			'mdy'       => 'm/d/Y',
			'dmy'       => 'd/m/Y',
			'dmy_dash'  => 'd-m-Y',
			'dmy_dot'   => 'd.m.Y',
			'ymd_slash' => 'Y/m/d',
			'ymd_dash'  => 'Y-m-d',
			'ymd_dot'   => 'Y.m.d',
		);

		return ( ! empty( $shortcode_map[ $shortcode ] ) ) ? $shortcode_map[ $shortcode ] : $shortcode;
	}

	/**
	 * Sort form fields/field types array by label and order
	 *
	 * @param array $array Array to be sorted
	 *
	 * @return array Sorted array
	 */
	private function sort_array_by_label_and_order( $array ) {

		// Sort array alphabetically by label
		uasort( $array, function ( $a, $b ) {

			return strnatcasecmp( $a['label'], $b['label'] );
		} );

		// Add order if it doesn't exist
		$ordered_and_sorted_array = array();
		$order                    = 1;
		foreach ( $array as $type => $field ) {
			if ( empty( $field['order'] ) ) {
				$ordered_and_sorted_array[ $type ] = array_merge( $field, array( 'order' => $order ) );
				$order ++;
			} else {
				$ordered_and_sorted_array[ $type ] = $field;
			}
		}

		// Sort array according to order
		uasort( $ordered_and_sorted_array, function ( $a, $b ) {

			return strnatcasecmp( $a['order'], $b['order'] );
		} );

		return $ordered_and_sorted_array;
	}

	/**
	 * Return GF form meta
	 *
	 * @param int|null $form_id
	 *
	 * @return array Array with form meta data
	 */
	public function get_entry_meta( $form_id ) {

		$entry_meta = array();

		$form_entry_meta = ! is_null( $form_id ) ? \GFFormsModel::get_entry_meta( $form_id ) : apply_filters( 'gform_entry_meta', array(), - 1 );

		foreach ( $form_entry_meta as $id => $data ) {
			$entry_meta[ $id ] = array(
				'id'      => $id,
				'label'   => $data['label'],
				'is_meta' => true,
			);
		}

		return $entry_meta;
	}

	/**
	 * Return default GF entry fields
	 *
	 * @return array Array with form entry fields
	 */
	public function get_entry_fields() {

		return array(
			array(
				'id'    => 'id',
				'label' => esc_html__( 'Entry ID', 'gravityview-importer' )
			),
			array(
				'id'    => 'status',
				'label' => esc_html__( 'Entry Status', 'gravityview-importer' )
			),
			array(
				'id'              => 'date_created',
				'label'           => esc_html__( 'Entry Date', 'gravityview-importer' ),
				'with_properties' => true,
				'filter'          => 'dateFormat',
				'filterData'      => 'Y-m-d G:i'
			),
			array(
				'id'              => 'date_updated',
				'label'           => esc_html__( 'Entry Updated Date', 'gravityview-importer' ),
				'with_properties' => true,
				'filter'          => 'dateFormat',
				'filterData'      => 'Y-m-d G:i'
			),
			array(
				'id'    => 'is_starred',
				'label' => esc_html__( 'Entry Is Starred', 'gravityview-importer' )
			),
			array(
				'id'    => 'is_read',
				'label' => esc_html__( 'Entry Is Read', 'gravityview-importer' )
			),
			array(
				'id'    => 'source_url',
				'label' => esc_html__( 'Source URL', 'gravityview-importer' )
			),
			array(
				'id'    => 'created_by',
				'label' => esc_html__( 'User ID', 'gravityview-importer' )
			),
			array(
				'id'    => 'ip',
				'label' => esc_html__( 'User IP', 'gravityview-importer' )
			),
			array(
				'id'    => 'user_agent',
				'label' => esc_html__( 'User Agent', 'gravityview-importer' )
			),
			array(
				'id'    => 'payment_amount',
				'label' => esc_html__( 'Payment Amount', 'gravityview-importer' )
			),
			array(
				'id'    => 'payment_method',
				'label' => esc_html__( 'Payment Method', 'gravityview-importer' )
			),
			array(
				'id'     => 'payment_date',
				'label'  => esc_html__( 'Payment Date', 'gravityview-importer' ),
				'with_properties' => true,
				'filter'          => 'dateFormat',
				'filterData'      => 'Y-m-d G:i',
			),
			array(
				'id'              => 'last_payment_date',
				'label'           => esc_html__( 'Last Payment Date', 'gravityview-importer' ),
				'with_properties' => true,
				'filter'          => 'dateFormat',
				'filterData'      => 'Y-m-d G:i',
			),
			array(
				'id'    => 'payment_status',
				'label' => esc_html__( 'Payment Status', 'gravityview-importer' )
			),
			array(
				'id'    => 'currency',
				'label' => esc_html__( 'Currency', 'gravityview-importer' )
			),
			array(
				'id'    => 'is_fulfilled',
				'label' => esc_html__( 'Is Transaction Fulfilled', 'gravityview-importer' )
			),
			array(
				'id'    => 'transaction_id',
				'label' => esc_html__( 'Transaction ID', 'gravityview-importer' )
			),
			array(
				'id'    => 'transaction_type',
				'label' => esc_html__( 'Transaction Type', 'gravityview-importer' )
			),
		);
	}

	/**
	 * Get a list of active feeds for the current form
	 *
	 * @param int $form_id
	 *
	 * @return null|array
	 */
	public function get_form_feeds( $form_id ) {

		$feeds = \GFAPI::get_feeds( null, intval( $form_id ) );
		if ( is_wp_error( $feeds ) ) {
			$feeds = array();
		}

		$zapier_feeds = $this->get_zapier_form_feeds( $form_id );
		if ( $zapier_feeds ) {
			$feeds = array_merge( $feeds, $zapier_feeds );
		}

		if ( empty( $feeds ) ) {
			return null;
		}

		// Get names of all registered addons
		$registered_addons = \GFAddOn::get_registered_addons();
		$addon_names       = array();
		foreach ( $registered_addons as $addon ) {
			/** @var GFAddOn $addon */
			$addon                = is_a( $addon, 'GFAddOn' ) ? $addon : new $addon;
			$slug                 = $addon->get_slug();
			$title                = $addon->get_short_title();
			$addon_names[ $slug ] = $title;
		}

		// Compile feeds data object and group feeds by addon name
		$feeds_data = array();
		foreach ( $feeds as $feed ) {
			$feed_name   = rgars( $feed, 'meta/feed_name', rgar( $feed['meta'], 'feedName' ) );
			$feed_name   = trim( $feed_name );
			$addon_name  = rgar( $addon_names, $feed['addon_slug'], $feed['addon_slug'] );
			$description = rgars( $feed, 'meta/description' );

			switch ( $addon_name ) {
				case 'gravityformszapier':
					$addon_name = 'Zapier';
					$feed_name  = rgar( $feed, 'name' );
					break;
				case 'gravityflow':
				case 'Workflow':
					$addon_name .= ' (Gravity Flow)';
					$feed_name  = rgars( $feed, 'meta/step_name', $feed_name );
					break;
			}

			$feed_count = ( ! empty( $feeds_data[ $addon_name ] ) ) ? count( $feeds_data[ $addon_name ] ) + 1 : 1;

			$feeds_data[ $addon_name ][] = array(
				'id'          => intval( rgar( $feed, 'id' ) ),
				'name'        => ( ! empty( $feed_name ) ) ? $feed_name : sprintf( esc_html__( 'Feed %d', 'gravityview-importer' ), $feed_count ),
				'description' => $description,
			);
		}

		return $feeds_data;
	}

	/**
	 * Return IDs of feeds to be executed that are otherwise not returned by the \GFAPI::get_feeds() method
	 *
	 * @param array $feed_ids
	 * @param int   $form_id
	 *
	 * @return array Array with feeds IDs
	 */
	public function handle_custom_form_feeds( $feed_ids, $form_id ) {

		// Handle Zapier feeds
		$zapier_feeds = $this->get_zapier_form_feeds( $form_id );

		return ( $zapier_feeds ) ? array_merge( $feed_ids, wp_list_pluck( $zapier_feeds, 'id' ) ) : $feed_ids;
	}

	/* Get a list of Zapier Addon form feeds
	 *
	 * @param int $form_id
	 *
	 * @return null|array
	 */
	private function get_zapier_form_feeds( $form_id ) {

		// GF Zapier Feed Addon stores feed data in a different table
		if ( ! method_exists( 'GFZapierData', 'get_feed_by_form' ) ) {
			return null;
		}

		$zapier_feeds = \GFZapierData::get_feed_by_form( $form_id, true );
		if ( ! $zapier_feeds ) {
			return null;
		}

		$zapier_feeds = array_map( function ( $zapier_feed ) {

			$zapier_feed['addon_slug'] = 'gravityformszapier';
			$zapier_feed['is_active']  = 1;

			return $zapier_feed;
		}, $zapier_feeds );

		return $zapier_feeds;
	}

	/**
	 * Sanitize filename by removing accents and applying other rules
	 *
	 * @param string $filename
	 *
	 * @return string
	 */
	public function sanitize_file_name( $filename ) {

		// No accents
		$filename = remove_accents( $filename );
		// Only alphanum and dots allowed
		$filename = preg_replace( '#[^[:alnum:]\.]#', '-', $filename );
		// Remove repeating dashes
		$filename = preg_replace( '#-+#', '-', $filename );

		return $filename;
	}
}
