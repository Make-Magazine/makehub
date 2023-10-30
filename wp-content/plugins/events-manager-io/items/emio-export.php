<?php
/**
 * Represents a single import job for a specific format. Contains all the information needed to initiate the import with the correct format.
 * @author marcus
 *
 */
class EMIO_Export extends EMIO_Item {
	/**
	 * Declares that this EMIO_Item object is an import, therefore an EMIO_Export object
	 * @var string
	 */
	public $type = 'export';
	/**
	 * Defines how this export format is exported. Can have a value of 'push' or 'pull', meaning exports are 'pushed' to destination or data is 'pulled' via a download or feed.
	 * @var string|false
	 */
	public static $method = 'pull';
	/**
	 * Limit to how many items can be exported, regardless of export settings, in order to prevent memory errors.
	 * @var int
	 */
	public static $export_hard_limit = 0;
	/**
	 * Limit to how many items (events, locations, etc.) can be exported per loop and passed onto the export_loop_start and export_loop_end functions. Default is 300 per loop.
	 * This affects performance as loading too many items per loop may exceed memory limits, yet too few will increase database lookups and therefore this should be tweaked according to server specs.
	 * This is also relevant for formats where exporting is done in batches, e.g. to a third party service, so that batches can be handled in the export_loop_start/end functions.
	 * @var int
	 */
	public static $export_pagination_limit = 300;
	/**
	 * Whilst exporting, this keeps a tally of how many items have been output or processed, depending on the format.
	 * @var int
	 */
	public $export_output_count = 0;
	/**
	 * Array of search arguments used in EM_Objects::get() functions, loaded initially when performing an export function for reuse.
	 * @var array
	 */
	public $export_args = array();
	/**
	 * Flag for whether this static object has been initialized at least once and that static variables have been set.
	 * @var array
	 */
	public static $init = false;
	/**
	 * Flag for whether this format supports syncing, e.g. syncing to CSV or iCal is not really possible, whereas to Google Calendar or a service liek Meetup.com is possible.
	 * @var array
	 */
	public static $supports_syncing = false;
	
	/**
	 * Runs the export and outputs desired export format. Either true is returned, or WP_Error
	 * @param int|array $id_or_array
	 */
	public function __construct( $id_or_array = array()) {
		parent::__construct( $id_or_array );
		if( !static::$init ) static::init();
	}
	
	public static function init(){
		//set hard limit for this format type
		if( defined('EMIO_EXPORT_HARD_LIMIT') ){
			//generic hard limit for all formats, overridden by individual formats
			static::$export_hard_limit = EMIO_EXPORT_HARD_LIMIT;
		}
		static::$export_hard_limit = apply_filters('emio_export_hard_limit', static::$export_hard_limit , get_called_class()); //override hard limit via filter for more control over each format type
		//set pagination limit for this format type
		if( defined('EMIO_EXPORT_PAGINATION_LIMIT') ){
			//generic pagination limit for all formats, overridden by individual formats
			static::$export_pagination_limit = EMIO_EXPORT_PAGINATION_LIMIT;
		}
		static::$export_pagination_limit = apply_filters('emio_export_hard_limit', static::$export_pagination_limit , get_called_class()); //override pagination limit via filter for more control over each format type
	}
	
	/**
	 * Runs the export and outputs desired export format. Either true is returned, or WP_Error
	 */
	public function run(){
		global $wpdb;
		$this->batch_uuid = wp_generate_uuid4();
		$this->batch_start = current_time('mysql');
		//reset counters, load format, search arguments
		$this->load_args();
		$this->export_output_count = 0;
		//determine the hard (overall) limit of items to export which can be determined by user but overriden by format object hard limits
		$hard_limit = $this->filter_limit;
		if( static::$export_hard_limit && static::$export_hard_limit < $this->filter_limit ){
			$hard_limit = static::$export_hard_limit;
		}
		//begin the export loop process
		$export_start = $this->export_start();
		if( is_wp_error( $export_start ) ){
			return $export_start;
		}
		$this->export_http_headers();
		//get the events or locations we'll be outputing
		if( $this->scope == 'locations' ){
			$EM_Objects = EM_Locations::get( $this->export_args );
		}else{
			$EM_Objects = EM_Events::get( $this->export_args );
		}
		$WP_Error = new WP_Error();
		if( !empty($EM_Objects) ){
			//now loop through and ouptut
			$export_count = 0;
			while( !empty($EM_Objects) && (empty($hard_limit) || $export_count < $hard_limit) ){
				//get export history for syncing
				if( static::$supports_syncing ){
					$history = $post_ids = array();
					//get post ids to search sync table for
					foreach( $EM_Objects as $EM_Object ){
						$post_ids[] = $EM_Object->post_id;
					}
					$uids = $wpdb->get_results('SELECT uid, post_id FROM '.EMIO_TABLE_SYNC.' WHERE post_id IN ('. implode(',', $post_ids) .') AND io_id='.$this->ID, ARRAY_A);
					//get results and build assoc array mappy post ids to uids
					foreach( $uids as $uid_array ){
						$history[$uid_array['post_id']] = $uid_array['uid'];
					}
				}
				//execute start of loop hook
				$export_loop_start = $this->export_loop_start( $EM_Objects );
				if( is_wp_error($export_loop_start) ){ /* @var WP_Error $export_loop_start */
					$WP_Error->errors = array_merge_recursive( $WP_Error->errors, $export_loop_start->errors );
					$WP_Error->error_data = array_merge_recursive( $WP_Error->error_data, $export_loop_start->error_data );
				}
				//run over the items through a loop within this loop
				$looped_objects = array(); //we can index $EM_Objects items by key so that we supply this to our loop_end function, which may be useful for referencing after export_event() for some formats
				foreach( $EM_Objects as $EM_Object ) {
					//get update value if syncing is possible
					$update_value = !empty($history[$EM_Object->post_id]) ? $history[$EM_Object->post_id] : null;
					//export event or location
					if( $this->scope == 'locations' ){ /* @var EM_Location $EM_Object */
						$export_result = $this->export_location( $EM_Object, $update_value );
						$looped_objects[$EM_Object->location_id] = $EM_Object;
					}else{ /* @var EM_Event $EM_Object */
						$export_result = $this->export_event( $EM_Object, $update_value );
						$looped_objects[$EM_Object->event_id] = $EM_Object;
					}
					//decide whether an update was effected or just a regular created export
					if( is_array($export_result) && !isset($export_result['action']) ){
						//ideally the export will decide whether this was truly updated or not
						$export_result['action'] = !empty($history[$EM_Object->post_id]) ? 'update':'create';
					}
					//handle error or save history
					if( is_wp_error($export_result) ){ /* @var WP_Error $export_result */
						$WP_Error->errors = array_merge_recursive( $WP_Error->errors, $export_result->errors );
						$WP_Error->error_data = array_merge_recursive( $WP_Error->error_data, $export_result->error_data );
					}else{
						if( empty($export_result['action']) || $export_result['action'] !== 'error' ){
							$this->export_output_count++;
						}
						$this->save_history($export_result, $EM_Object);
					}
					//rinse and repeat loop
					$export_count++;
					if( $export_count == $hard_limit ) break;
				}
				//execute end of loop
				$export_loop_result = $this->export_loop_end( $looped_objects );
				if( is_wp_error($export_loop_result) ){ /* @var WP_Error $export_loop_result */
					$WP_Error->errors = array_merge_recursive( $WP_Error->errors, $export_loop_result->errors );
					$WP_Error->error_data = array_merge_recursive( $WP_Error->error_data, $export_loop_result->error_data );
				}elseif( !empty($export_loop_result)  && is_array($export_loop_result) ){
					//for exports like spreadsheets which are done in bulk, an array of exported items can be passed here and added to history in one go
					foreach( $export_loop_result as $object_id => $export_result ){
						$this->save_history( $export_result, $looped_objects[$object_id] );
					}
				}
				//reiterate loop
				$EM_Objects = array();
				if( empty($hard_limit) || $export_count < $hard_limit ){
					$this->export_args['page']++;
					//get the events or locations we'll be outputing in next loop
					if( $this->scope == 'locations' ){
						$EM_Objects = EM_Locations::get( $this->export_args );
					}else{
						$EM_Objects = EM_Events::get( $this->export_args );
					}
				}
			}
		}
		$export_done_result = $this->export_done();
		if( is_wp_error($export_done_result) ){ /* @var WP_Error $export_done_result */
			$WP_Error->errors = array_merge_recursive( $WP_Error->errors, $export_done_result->errors );
			$WP_Error->error_data = array_merge_recursive( $WP_Error->error_data, $export_done_result->error_data );
		}
		if( !empty($WP_Error->errors) ){
			return $WP_Error;
		}
		return true;
	}
	
	/**
	 * Returns parsed array of EM objects ready for exporting, which is based on the filters used on what to export.
	 * Note that if exporting events and locations EM_Event objects will be provided and during output/encoding to export format it's expected to output location information that way
	 * @return array
	 */
	public function get(){
		$args = $this->load_args();
		if( $this->scope == 'locations' ){
			//only export locations
			return EM_Locations::get( $args );
		}else{
			//export events and possibly locations/bookings, but we start by looking for the events that match
			return EM_Events::get( $args );
		}
	}
	
	/**
	 * Generate a set or search arguments compatible with EM_Object::get(), based on this export
	 * @return array
	 */
	public function load_args(){
		$args = array();
		if( !empty($this->filter) ) $args['search'] = $this->filter;
		if( $this->scope != 'locations' ){
			$args['scope'] = $this->get_filter_scope();
		}
		//add pagination limits so we don't load unlimited sets of data into memory
		$args['pagination'] = 1;
		$args['page'] = 1;
		//determine PAGINATION limit, which is either the export limit defined by user or the pagination/hard limits imposed by format object, whichever is smallest
		$args['limit'] = $this->filter_limit;
		if( static::$export_pagination_limit && (!$args['limit'] || static::$export_pagination_limit < $args['limit']) ){
			$args['limit'] = static::$export_pagination_limit;
		}
		if( static::$export_hard_limit && (!$args['limit'] || static::$export_hard_limit < $args['limit']) ){
			$args['limit'] = static::$export_hard_limit;
		}
		if( !$args['limit'] ){
			$args['limit'] = get_option('dbem_events_default_limit');
		}
		// taxonomies
		foreach( EM_Object::get_taxonomies() as $tax => $taxonomy ){
			//check event context
			if( $this->scope == 'all' || $this->scope == 'events+locations' || $this->scope == 'events' ){
				if( in_array(EM_POST_TYPE_EVENT, $taxonomy['context']) && !empty($this->meta['taxonomies'][$taxonomy['name']]) ){
					$args[$tax] = $this->meta['taxonomies'][$taxonomy['name']];
				}
			}elseif( $this->scope == 'all' || $this->scope == 'events+locations' || $this->scope == 'locations' ){
				//check location context
				if( in_array(EM_POST_TYPE_LOCATION, $taxonomy['context']) && !empty($this->meta['taxonomies'][$taxonomy['name']]) ){
					$args[$tax] = $this->meta['taxonomies'][$taxonomy['name']];
				}
			}
		}
		// save and return args
		$this->export_args = $args;
		return $args;
	}
	
	/**
	 * Overrides parent class function to return false if $scope returns an array of empty scope search values, which can then be directly passed into EM_Object::get() as a search argument.
	 * @return array|bool
	 */
	public function get_filter_scope(){
		$scope = parent::get_filter_scope();
		if( empty($scope[0]) && empty($scope[1]) ){
			return false;
		}else{
			return $scope;
		}
	}
	
	public function get_feed_url(){
		global $wp_rewrite; /* @var WP_Rewrite $wp_rewrite */
		if( $this->has_public_feed() ){
			if( !$wp_rewrite->using_permalinks() ){
				return add_query_arg('events-manager-io', $this->uuid, get_home_url());
			}else{
				return get_home_url(null, 'events-manager-io/'.$this->uuid);
			}
		}
		return false;
	}
	
	public function has_public_feed(){
		return !empty($this->uuid) && preg_match('/^public\-(feed|dl)$/', $this->source);
	}
	
	public function get_source_types(){
		return array(
			'fields' => array(
				'example' => array(
					'name' => 'Example Destination',
					'group' => 'example-destination',
					'no_input' => true, //for exports we wouldn't need inputs
					'description' => 'This is an example destination and must be overriden',
				),
			),
			'groups' => array(
				'example-destination' => 'Example Destination Group',
			)
		);
	}
	
	/**
	 * Send http headers in case this format can provide output in downloadable file format or feed.
	 * @param EMIO_Export
	 */
	public function export_http_headers( ){
		if( $this->get_source_type() == 'public-dl' ){
			header("Content-Type: ". current(static::$mime_type));
			$url = explode('/', preg_replace('/https?:\/\//', '', get_home_url()));
			$file_name = $url[0].'-emio-'.static::$ext.'-export';
			header("Content-Disposition: Attachment; filename=".sanitize_file_name($file_name).".".static::$ext);
		}
	}
	
	/**
	 * @param array $EM_Objects Array of EM_Object children, such as EM_Event
	 * @param EMIO_Export
	 * @return true|WP_Error
	 */
	public function export_loop_start( $EM_Objects ){
		return true;
	}
	
	/**
	 * This function can be used to bulk-export items, preferably by pre-processing them into an array via the individual export_event/export_location functions and then bulk-uploading them in this function.
	 * What should be returned is an associative array, indexed by object ID, containing further information about the exported item for logging purposes or a WP_Error if there was a failure.
	 * If format doesn't bulk-export, a true value can be returned to skip any extra logging, or a general WP_Error if there's a total failure for batch processing.
	 *
	 * @param array $EM_Objects Array of EM_Object children, such as EM_Event, indexed by object ID
	 * @return WP_Error|true|array
	 * @see EMIO_Export::export_event() for returned array format
	 */
	public function export_loop_end( $EM_Objects ){
		return array();
	}
	
	/**
	 * @param EM_Event $EM_Event
	 * @param string $update_id
	 * @return true|WP_Error|array = [
	 *    'uid' => string //the ID for this specific result
	 *    'action' => string //what was done 'update' or 'create'
	 *    'url' => string //URL where exported item could be located (optionaol, if possible)
	 * ]
	 */
	public function export_event( $EM_Event, $update_id = null ){
		return array();
	}
	
	/**
	 * @param EM_Location $EM_Location
	 * @param string $update_id
	 * @return true|array|WP_Error
	 */
	public function export_location( $EM_Location, $update_id = null ){
		return array();
	}
	
	/**
	 * @param EMIO_Export
	 * @return WP_Error|true
	 */
	public function export_start( ){
		return true;
	}
	
	/**
	 * @param EMIO_Export
	 * @return WP_Error|true
	 */
	public function export_done( ){
		return true;
	}
	
	/**
	 * Overrides history saving so that any formats that output a feed or file get ignored, since we don't update or add anything.
	 * @param array $history
	 * @param EM_Event|EM_Location $EM_Object
	 * @return bool
	 */
	public function save_history($history, $EM_Object) {
		if( static::$method === 'pull' ){
			return false;
		}
		return parent::save_history($history, $EM_Object);
	}
}