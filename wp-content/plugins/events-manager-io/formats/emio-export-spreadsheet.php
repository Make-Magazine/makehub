<?php

/**
 * This is a intermediate class for those formats that export to spreadsheets. Instead of overriding the export_event and export_location
 * functions, spreadsheet classes can make use of the export_row and export_headers functions which provide pre-parsed set of arrays to export
 * to the destination spreadsheet via the export_headers_row and export_row functions.
 * Class EMIO_Export_Spreadsheet
 */
class EMIO_Export_Spreadsheet extends EMIO_Export {
	
	/**
	 * An associative array in a specific order which corresponds to headers of a spreadsheet.
	 * Exporting rows would copy this array, and fill each array item by key with the relevant info.
	 * @var array
	 */
	public $row_template = array();
	/**
	 * Array of taxonomies that will also be exported, populated when starting an export and referenced within the looping of items to export.
	 * @var array
	 */
	public $taxonomies_array = array();
	/**
	 * If set to true and the $supports_syncing static property for this format is also set to true, spreadsheet formats can choose not to overwrite previous
	 * data and instead append new data to the bottom row, and update the previously exported rows. This is not possible with pull format spreadsheets where
	 * files generated server-side are downloaded, such as CSV and Excel given a file is regenerated dynamically, but could be useful for services like Google Sheets.
	 * @var boolean
	 */
	public $update = false;
	/**
	 * Array of rows to be added at the end of a loop, which should be associative with the object ID as a key, which can later be cross referenced by same
	 * id with the export_loop_end $items parameter. Since many spreadsheets support batch processing, this class will output rows in batches rather than individual rows.
	 *
	 * Each spreadsheet format can then choose to iterate these rows individually adding them, or doing a batch output.
	 *
	 * Each array item is an array containing the 'row' key for the row data, and optional 'cell' data for location of where to update with 'row' data. 'cell' can be updated
	 * during export so that post-export logging will access this information and store it for future exports being able to update this row.
	 *
	 * @var array
	 */
	public $sheet_rows = array();
	public $sheet_rows_cells = array();
	/**
	 * The row number containing the next empty row to insert data into on the spreadsheet.
	 * @var int
	 */
	public $sheet_row_pointer = 1;
	
	/**
	 * Set pagination limits (how many rows to export in bulk, if supported) and hard limit of how many events a format can export.
	 */
	public static function init() {
		if( defined('EMIO_SPREADSHEET_PAGINATION_LIMIT') ){
			static::$export_pagination_limit = EMIO_SPREADSHEET_PAGINATION_LIMIT;
		}
		if( defined('EMIO_SPREADSHEET_HARD_LIMIT') ){ // short circuit how many items can be exported in one export run to Google Sheets
			static::$export_hard_limit = EMIO_SPREADSHEET_HARD_LIMIT;
		}
	}
	
	/**
	 * Decide whether this is an update or a new export for the first time, based on export history and whether this format even allows syncing.
	 * @return true|void|WP_Error
	 */
	public function export_start(){
		//check if this is the first time we're exporting to this destination, if not then no headers should be output
		if( static::$supports_syncing ){
			global $wpdb;
			$count = $wpdb->get_var('SELECT COUNT(*) FROM '. EMIO_TABLE_SYNC .' WHERE io_id='.$this->ID);
			$this->update = $count > 0;
		}else{
			$this->update = false;
		}
		return parent::export_start();
	}
	
	/**
	 * When exporting a spreadsheet, on the first loop headers will be generated and output, as well as generating a corresponding array
	 * template to fill with event/location item data for each row.
	 * @param array $items
	 * @return boolean
	 */
	public function export_loop_start( $items ){
		//check if this is the first loop (i.e. nothing output yet)
		if( $this->export_output_count == 0 ){
			//determine whether to build location/event headers
			if( $this->scope == 'locations' ){
				//build and output headers headers
				$EM_Location = current($items); /* @var EM_Location $EM_Location */
				$location_headers = $EM_Location->to_array();
				//add taxonomies
				$this->taxonomies_array = EM_Object::get_taxonomies();
				foreach( $this->taxonomies_array as $taxonomy ){
					if( in_array(EM_POST_TYPE_LOCATION, $taxonomy['context']) ){
						$location_headers[$taxonomy['name']] = '';
						$location_taxonomy_headers[] = $taxonomy['name'];
					}
				}
				//add custom meta fields, we need to get all possible fields for Event Post Types
				if( get_option('dbem_attributes_enabled') ){
					$location_atts_headers_array = em_get_attributes( true );
					foreach( $location_atts_headers_array['names'] as $location_att ){
						$location_headers[$location_att] = '';
					}
				}
				//pass on the export header columns for output
				$this->export_header_row( array_keys($location_headers) );
				//create a data template for subsequent rows so everything lines up
				$this->row_template = array();  //empty the headers array for use as a data template, in the event we one day add 'nice names' to column headers
				foreach( $location_headers as $k => $v ) $this->row_template[$k] = '';
			}else{
				//output headers
				$EM_Event = current($items); /* @var EM_Event $EM_Event */
				$event_headers = $EM_Event->to_array();
				//add taxonomies
				$this->taxonomies_array = EM_Object::get_taxonomies();
				foreach( $this->taxonomies_array as $taxonomy ){
					if( in_array(EM_POST_TYPE_EVENT, $taxonomy['context']) ){
						$event_headers[$taxonomy['name']] = '';
						$event_taxonomy_headers[] = $taxonomy['name'];
					}
				}
				//add custom meta fields, we need to get all possible fields for Event Post Types
				if( get_option('dbem_attributes_enabled') ){
					$event_atts_headers_array = em_get_attributes();
					foreach( $event_atts_headers_array['names'] as $event_att ){
						$event_headers[$event_att] = '';
					}
				}
				if( $this->scope == 'events+locations' || $this->scope == 'all' ){
					$EM_Location = new EM_Location();
					foreach( $EM_Location->to_array() as $k => $v ){
						if( in_array($k, array('post_id', 'blog_id', 'post_content')) ){
							$k = 'location_'.$k;
						}
						$event_headers[$k] = '';
					}
				}
				//pass on the export header columns for output
				$this->export_header_row( array_keys($event_headers) );
				//create a data template for subsequent rows so everything lines up
				$this->row_template = array();  //empty the headers array for use as a data template, in the event we one day add 'nice names' to column headers
				foreach( $event_headers as $k => $v ) $this->row_template[$k] = '';
			}
		}
		return true;
	}
	
	/**
	 * Takes an event object and translates it into an exportable row of data to the spreadsheet, and passes it onto export_row() for export. If $update_id is provided this is also passed on and treated as the location of the starting Cell/Row where this event was previously written to.
	 * @param EM_Event $EM_Event
	 * @param string $update_id
	 * @return bool
	 */
	public function export_event( $EM_Event, $update_id = null ){
		$event_row = $this->row_template;
		//add regular array rows to template row
		$event_array = $EM_Event->to_array();
		foreach( $event_array as $k => $v ){
			if( isset($event_row[$k]) ) $event_row[$k] = $v;
		}
		//add taxonomies
		foreach( $this->taxonomies_array as $taxonomy ){
			$event_taxonomy_terms = array();
			$WP_Terms = get_the_terms( $EM_Event->post_id, $taxonomy['name'] );
			if( is_array($WP_Terms) ){
				foreach( $WP_Terms as $WP_Term ){
					$event_taxonomy_terms[] = $WP_Term->name;
				}
			}
			$event_row[$taxonomy['name']] = implode(',', $event_taxonomy_terms);
		}
		//add custom meta fields
		if( get_option('dbem_attributes_enabled') ){
			foreach( $EM_Event->event_attributes as $event_att ){
				if( isset($event_row[$event_att]) ){
					$event_row[$event_att] = $EM_Event->event_attributes[$event_att];
				}
			}
		}
		//add location fields if needed
		if( $this->scope == 'events+locations' || $this->scope == 'all' ){
			if( $EM_Event->location_id ){
				$location_array = $EM_Event->get_location()->to_array();
				foreach( $location_array as $k => $v ){
					if( isset($event_row[$k]) ){
						if( in_array($k, array('post_id', 'blog_id', 'post_content')) ){
							$k = 'location_'.$k;
						}
						$event_row[$k] = $v;
					}
				}
			}
		}
		//now output event row
		$this->sheet_rows[$EM_Event->event_id] = $event_row;
		if( $update_id !== null ){
			$this->sheet_rows_cells[$EM_Event->event_id] = $update_id;
		}
		return true;
	}
	
	/**
	 * Processes a location into a row of data to send to spreadsheet. See export_event() as the functionality and arguments are the same.
	 * @param EM_Location $EM_Location
	 * @param string $update_id
	 * @return bool
	 * @see EMIO_Export_Spreadsheet::export_event()
	 */
	public function export_location( $EM_Location, $update_id = null ){
		$location_row = $this->row_template;
		//add regular array rows to template row
		$location_array = $EM_Location->to_array();
		foreach( $location_array as $k => $v ){
			if( isset($location_row[$k]) ) $location_row[$k] = $v;
		}
		//add taxonomies
		foreach( $this->taxonomies_array as $taxonomy ){
			$location_taxonomy_terms = array();
			$WP_Terms = get_the_terms( $EM_Location->post_id, $taxonomy['name'] );
			if( is_array($WP_Terms) ){
				foreach( $WP_Terms as $WP_Term ){
					$location_taxonomy_terms[] = $WP_Term->name;
				}
			}
			$location_row[$taxonomy['name']] = implode(',', $location_taxonomy_terms);
		}
		//add custom meta fields
		if( get_option('dbem_attributes_enabled') ){
			foreach( $EM_Location->location_attributes as $location_att ){
				if( isset($location_row[$location_att]) ){
					$location_row[$location_att] = $EM_Location->location_attributes[$location_att];
				}
			}
		}
		//now save row to object for output at end of current loop
		$this->sheet_rows[$EM_Location->location_id] = $location_row;
		if( $update_id !== null ){
			$this->sheet_rows_cells[$EM_Location->location_id] = $update_id;
		}
		return true;
	}
	
	public function export_loop_end( $EM_Objects ){
		//export the rows and get the ultimate result
		$results = $this->export_rows();
		//go through the result and prepare it up for logging
		if( is_array($results) ){
			//remove headers for logging
			if( !empty($results[0]) ) unset($results[0]);
		}elseif( $results === true ){
			//create a 'simple' set of results to log
			$results = array();
			foreach( $this->sheet_rows as $id => $row ){
				if( $id !== 0 ){
					$results[$id] = array(
						'uid' => $this->sheet_row_pointer,
						'action' => 'create'
					);
				}
				//update row pointer
				$this->sheet_row_pointer++;
			}
		}
		//rinse and repeat
		$this->sheet_rows = array(); //reset the rows array for next loop
		return $results;
	}
	
	/**
	 * Exports the headers that would go at the top of the spreadsheet, which constitute an ordered array of column key names, which will correspond to keys of array items supplied to export_row().
	 * By default, if not overriden, the supplied array is output as the first row of the spreadsheet via export_row.
	 * @param array $header_row
	 * @return true|WP_Error
	 */
	public function export_header_row( $header_row ){
		if( $this->update ) return true;
		$this->sheet_rows[0] = $header_row;
		return true;
	}
	
	/**
	 * Exports a single row to the destination spreadsheet by providing the associative array of data to put in the row, and possibly a row or Cell ID $update_id to replace/update.
	 * The provided $row_array keys are already ordered in the same order of the header columns, which are stored in a the $row_template property in order but indexed associatively.
	 * In cases an $update_id is provided, each format can decide how to handle that according to their limitations, CSV/Excel will ignore it as they regenerate files from scratch.
	 * Something like Google Spreadsheets could take $update_id as a row and/or cell location and write from that point onwards to the right of that cell/row.
	 * If successful, the Row/Cell ID for the leftmost column exported can be provided for future updates of this row, otherwise a boolean true for success (CSV/Excel), WP_Error upon error.
	 *
	 * @param array $row_array Associative array of fields to export to row, in order of columns.
	 * @param string $update_id Row/Cell ID where row should be placed/updated
	 * @return string|WP_Error|array = [
	 *     'uid' => string //ID to find exported item via api
	 *     'action' => string //'create' or 'update'
	 *     'url' => string //url of where to find exported item
	 * ]
	 */
	public function export_row( $row_array, $update_id = null ){
		return true;
	}
	
	public function export_rows(){
		//this is somewhat proof-of-concept, but will also recursively run export_row and contactenate results from that as well
		$results = array();
		//in this implementation, we actually use the export_row() function to iterate each row, we could do a batch upload and generate uid/action data for each row we exported
		foreach( $this->sheet_rows as $id => $row ){
			$cell_id = !empty($this->sheet_rows_cells[$id]) ? $this->sheet_rows_cells[$id] : null;
			$result = $this->export_row( $row, $cell_id );
			//handle the result of this export, or generate a set of results based on a batch export
			if( is_array($result) || is_wp_error($result) ){
				//either an error occurred, or we're just not providing results for logging
				$results[$id] = $result;
			}else{
				$action = $cell_id === null ? 'create':'update'; //generally, if a cell id was supplied, the cell was probably updated
				$results[$id] = array(
					'uid' => $result,
					'action' => $action,
				);
				//calculate URL (if you can)
				//$results[$id]['url'] = null;
			}
		}
		return $results;
	}
}