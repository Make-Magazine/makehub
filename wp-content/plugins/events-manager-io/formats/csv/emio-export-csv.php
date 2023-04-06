<?php
/**
 * CSV Export Format class, which basically opens/closes an output handle when exporting starts/stops, and writes to it via fputcsv when each
 * row array is to be exported. Also allows for custom delimeters to be set via the EM_CSV_DELIMITER constant and em_csv_delimiter filter.
 *
 * Class EMIO_Export_CSV
 */
class EMIO_Export_CSV extends EMIO_Export_Spreadsheet {
	
	/**
	 * Short reference for this parser, such as 'ical' or 'csv'
	 * @var string
	 */
	public static $format = 'csv';
	/**
	 * Display name for this parser
	 * @var string
	 */
	public static $format_name = 'CSV';
	/**
	 * The extension of csv files
	 * @var string
	 */
	public static $ext = 'csv';
	/**
	 * MIME type used to detect and output this sort of format.
	 * @var bool
	 */
	public static $mime_type = array('text/csv');
	/**
	 * iCal can only export events with or without locations, it cannot export locations without events, as locations must belong to an event.
	 * @var array
	 */
	public static $supports = array('events' => array('locations'), 'locations' => array());
	/**
	 * Delimeter used for CSV output.
	 * @var string
	 */
	public static $delimeter = ';';
	/**
	 * File handle for outputting CSV file via fputcsv();
	 * @var resource
	 */
	public $handle = false;
	
	/**
	 * Intialize the parent class and also establish the delimeter to be used, which can be overriden by the em_csv_delimeter or EM_CSV_DELIMITER
	 */
	public static function init() {
		//delimiter
		parent::init();
		$delimiter = !defined('EM_CSV_DELIMITER') ? static::$delimeter : EM_CSV_DELIMITER;
		static::$delimeter = apply_filters('em_csv_delimiter', $delimiter);
	}
	
	/**
	 * Outputs supplied array to CSV output handle.
	 * @param array $row_array
	 * @param string $update_id
	 * @return bool|WP_Error
	 */
	public function export_row($row_array, $update_id = null ){
		$result = fputcsv($this->handle, $row_array, static::$delimeter);
		if( $result === false ){
			return new WP_Error('export-csv', 'We had a CSV error...');
		}
		$this->sheet_row_pointer++;
		return $this->sheet_row_pointer;
	}
	
	//We hook into start/done to open up output handle for subsequent fput functions
	
	/**
	 * Creates an output handle before exporting headers and rows.
	 * @return true|WP_Error
	 */
	public function export_start(){
		$this->handle = fopen("php://output", "w");
		return parent::export_start();
	}
	
	/**
	 * Closes output handle we opened in export_start()
	 * @return true|WP_Error
	 */
	public function export_done(){
		fclose($this->handle);
		return parent::export_done();
	}
}
//register this format
EMIO_Exports::register_format('EMIO_Export_CSV');