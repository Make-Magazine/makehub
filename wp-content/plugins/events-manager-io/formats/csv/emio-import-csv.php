<?php
class EMIO_Import_CSV extends EMIO_Import_Spreadsheet {
	
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
	public static $mime_type = 'text/csv';
	/**
	 * Whether this parser supports recurring imports or not.
	 * @var bool
	 */
	public static $recurring = true;
	
	/**
	 * Returns CSV data from import source or a WP_Error object upon failure.
	 * @param int $limit
	 * @return array|WP_Error
	 */
	public function import_data( $limit = 0 ){
		$this->get_source(); // trigger a recheck for file in case it got deleted
		$filepath = $this->get_source_filepath();
		$data = array('header'=>array(), 'data'=>array());
		if( !file_exists($filepath) ){
			return new WP_Error('csv-source-missing', __('We could not find your CSV file. Please try uploading it again.', 'events-manager-io'));
		}
		//get the file from url or file and let PHPExcel do the parsing of data
		try{
			EMIO_Loader::libraries();
			$spreadsheet = PhpOffice\PhpSpreadsheet\IOFactory::load($filepath);
			$csv = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
		} catch ( PhpOffice\PhpSpreadsheet\Reader\Exception $e ){
			return new WP_Error('csv-reader-exception', $e->getMessage());
		} catch ( PhpOffice\PhpSpreadsheet\Exception $e ){
			return new WP_Error('csv-reader-exception', $e->getMessage());
		}
		//add parsed data to the array we'll be passing back
		$data['headers'] = array_shift($csv);
		$csv = !empty($limit) ? array_slice($csv, 0, $limit) : $csv; //reduce number of results by the limit imposed
		//convert array items into associative array based on headers
		foreach($csv as $csv_i){
			$csv_item = array();
			foreach( $csv_i as $k => $v ){
				$csv_item[$data['headers'][$k]] = $v;
			}
			$data['data'][] = $csv_item;
		}
		return $data;
	}
}
//register this format
EMIO_Imports::register_format('EMIO_Import_CSV');

//filter for CSV files where mime type is detected as text/html by mistake
add_filter('wp_check_filetype_and_ext', function($values, $file, $filename, $mimes, $real_mime) {
	if ( $real_mime === 'text/html' && preg_match( '/\.(csv)$/i', $filename ) ) {
		$values['ext']  = 'csv';
		$values['type'] = 'text/csv';
	}
	return $values;
}, PHP_INT_MAX, 5);