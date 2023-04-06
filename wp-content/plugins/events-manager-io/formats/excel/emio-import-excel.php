<?php
class EMIO_Import_Excel extends EMIO_Import_Spreadsheet {
	
	public static $format = 'excel';
	public static $format_name = 'Excel';
	public static $ext = 'xlsx';
	public static $mime_type = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
	public static $recurring = false;
	
	/**
	 * Returns CSV data from import source or a WP_Error object upon failure.
	 * @param int $limit
	 * @return array|WP_Error
	 */
	public function import_data( $limit = 0 ){
		$filepath = $this->get_source_filepath();
		$data = array('header'=>array(), 'data'=>array());
		if( !file_exists($filepath) ){
			return new WP_Error('excel-source-missing', __('We could not find your Excel file. Please try uploading it again.', 'events-manager-io'));
		}
		//get the file from url or file and let PHPExcel do the parsing of data
		try{
			EMIO_Loader::libraries();
			$spreadsheet = PhpOffice\PhpSpreadsheet\IOFactory::load($filepath);
			$csv = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
		} catch ( PhpOffice\PhpSpreadsheet\Reader\Exception $e ){
			return new WP_Error('excel-reader-exception', $e->getMessage());
		} catch ( PhpOffice\PhpSpreadsheet\Exception $e ){
			return new WP_Error('excel-reader-exception', $e->getMessage());
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
EMIO_Imports::register_format('EMIO_Import_Excel');