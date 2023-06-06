<?php
class EMIO_Import_Spreadsheet extends EMIO_Import {
	
	public static $options = array(
		'fuzzy_location' => array(
			'default' => true,
			'delimiter' => true,
			'placeholder_format' => true,
			'format' => true,
			'google_api' => true,
			'country' => true,
		),
		'ignore_uid' => true
	);
	
	public static $option_defaults = array(
		'fuzzy_location' => array(
			'default' => 0,
			'delimiter' => ',',
			'placeholder_format' => 'NAME, ADDRESS',
			'format' => array('address','town','state','postcode','country'),
			'google_api' => 1,
			'country' => false,
		)
	);
	
	public static $field_mapping = true;
	
	/**
	 * Parses the passed on import object and returns array of items for processing.
	 * Each parser should return a standardized format of arrays, so that it once parsed the data is consistent with any other format.
	 * @return array
	 */
	public function import(){
		$data = $this->import_data();
		//map the fields to the right item field names
		$items = $this->get_mapped_fields( $data['data']);
		return $items;
	}
	
	/**
	 * Returns array data from import source or a WP_Error object upon failure.
	 * @param int $limit
	 * @return array|WP_Error
	 */
	public function import_data( $limit = 0 ){
		return array();
	}
}