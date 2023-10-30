<?php
/**
 * This represents an import or export job and holds all the similar traits ingoing or outgoing data jobs hold.
 * @author marcus
 *
 */
class EMIO_Item {

	/**
	 * The ID of the import/export
	 * @var int
	 */
	public $ID;
	/**
	 * A unique UUID of the import/export which can be publicly used (e.g. for public export link downloads)
	 * @var int
	 */
	public $uuid;
	/**
	 * The type of item this is, and should be overriden to equal 'import' or 'export'
	 * @var string
	 */
	public $type;
	/**
	 * The user ID of who created/owns this import/export
	 * @var int
	 */
	public $user_id;
	/**
	 * Reference name for this import/export
	 * @var string
	 */
	public $name;
	/**
	 * Are we importing/exporting Events, Locations or all?
	 * @var string
	 */
	public $scope = 'all';
	/**
	 * The status of this import/export, relevant for persistant exports or recurring imports. 1 is active, 0 is inactive 
	 * @var int
	 */
	public $status = 0;
	/**
	 * Source of the data, which would be a URL or 'file' for uploaded files.
	 * @var string
	 */
	public $source;
	/**
	 * Text filter for filtering items in an import/export by the name or description
	 * @var string
	 */
	public $filter;
	/**
	 * Can either be a word such as 'future', 'past', 'all' (as with EM scopes), or date range (where start or end can be empty if 'up till' or 'starting from')
	 * @var string
	 */
	public $filter_scope;
	/**
	 * The number of things to import/export each time.
	 * @var int
	 */
	public $filter_limit;
	/**
	 * Frequency of this import/export, which can be words like 'hourly', 'daily', 'weekly', 'biweekly', 'monthly' relevant only for recurring imports.
	 * 
	 * For imports, this is when the import is run, for exports this may be relevant for caching.
	 * 
	 * @var string
	 */
	public $frequency;
	/**
	 * Start date (YYYY-MM-DD) of this import/export. Can also be a future date if imports/exports shouldn't start immediately.
	 * @var string
	 */
	public $frequency_start;
	/**
	 * End date (YYYY-MM-DD) of this import/export.
	 * @var string
	 */
	public $frequency_end;
	/**
	 * Unix timestamp for date of last import/export.
	 * @var int
	 */
	public $last_update;
	/**
	 * Associative array containing extra data not stored in generic items table, saved/retrieved as a serialized array
	 * @var array
	 */
	public $meta = array();
	/**
	 * Array detailing history of processed objects (e.g. events or locations) for this specific item (e.g. import or export).
	 * @var array
	 */
	public $history = array();
	/**
	 * Array of errors that occurred in previous function run on this object (can also exclude external functions such as EMIO_Item_Admin save attempts)
	 * @var array
	 */
	public $errors = array();
	
	/*
	 * START FORMAT PARAMS
	 */
	/**
	 * The format of the source, which should match up with the format name of a valid parser, for example 'ical' or 'csv'.
	 * @var string
	 */
	public static $format;
	
	/**
	 * Display name for this parser
	 * @var string
	 */
	public static $format_name;
	/**
	 * Whether this parser supports recurring imports or not.
	 * @var bool
	 */
	public static $recurring;
	/**
	 * The file extension expected. If an array, the first value is used for naming export files, the rest are used to be added for accepted file upload extensions.
	 * @var string|array
	 */
	public static $ext;
	/*
	 * Array of mime types this file can contain.
	 * @var array
	 */
	public static $mime_type = array();
	/*
	 * Defines what objects can be imported/exported individually (e.g. just locations or just events) by key and contains an array of what other objects can be exported alongside a specific object (e.g. events can also export locations).
	 * Allows for expansion of options, including bookings, taxonomies etc. but currently the only option is to import/export events with or without locations, or just locations if the format allows it.
	 * For example, iCal cannot export only locations, since ical only comprises of events with optionally attached locations.
	 * @var array
	 */
	public static $supports = array('events'=> array('locations'), 'locations' => array());
	/**
	 * By default any format can be synced via import, since we generate IDs based on a checksum even if the source doesn't have an ID.
	 * @var array
	 */
	public static $supports_syncing = false;
	/**
	 * Whether or not a format uses oauth, which will therefore require loading of our oauth base classes. Formats that handle oauth all by themselves with own libraries don't need to label this as true.
	 * @var boolean
	 */
	public static $oauth = false;
	/*
	 * Array of mime types this file can contain.
	 * @var array
	 */
	public $batch_uuid;
	/*
	 * MySQL DATETIME stamp when an import job is run, so that all logs for an import job have the same time even if it differs by a few seconds.
	 * @var string
	 */
	public $batch_start;
	/*
	 * END FORMAT PARAMS
	 */
	/**
	 * @var bool Flag to indicate object was initialized once.
	 */
	public static $init;
	/**
	 * @var string The minimum version this item needs I/O to be on in order to work properly.
	 */
	public static $minimum_version;
	
	/**
	 * Build item by retrieving item from DB or using supplied associative array in same format as a DB result.
	 * @param int|array $id_or_array
	 */
	public function __construct( $id_or_array = array() ){
		if( is_numeric($id_or_array) || wp_is_uuid($id_or_array) ){
			global $wpdb;
			if( wp_is_uuid($id_or_array) ){
				$sql = $wpdb->prepare("SELECT * FROM ".EMIO_TABLE." WHERE uuid=%s AND type=%s", $id_or_array, $this->type);
			}else{
				$sql = $wpdb->prepare("SELECT * FROM ".EMIO_TABLE." WHERE ID=%d AND type=%s", $id_or_array, $this->type);
			}
			$result = $wpdb->get_row($sql, ARRAY_A);
			if( is_array($result) ){
				$id_or_array = $result;
			}
		}
		if( is_array($id_or_array) ){
			$this->to_object($id_or_array);
		}
		if( !empty($this->frequency_start)  && $this->frequency_start == '0000-00-00' ) $this->frequency_start = false;
		if( !empty($this->frequency_end)  && $this->frequency_end == '0000-00-00' ) $this->frequency_end = false;
		static::$init = true;
	}
	
	/**
	 * Determines whether this format can import/export items, usually overriden by formats requiring some sort of API key or oAuth token.
	 * @param EMIO_Export
	 * @return bool
	 */
	public function is_ready(){
		return true;
	}

	/**
	 * Save an array into the class properties.
	 * @param array $array
	 * @return null
	 */
	public function to_object( $array = array() ){
		$properties = get_object_vars($this);
		foreach ( $array as $key => $value ) {
			if( !empty($value) && array_key_exists($key, $properties) ){
				if( is_array($properties[$key]) ){
					$array[$key] = maybe_unserialize($array[$key]);
				}
				$this->$key = $array[$key];
			}
		}
	}
	
	/**
	 * Returns the source type for this item.
	 * @return string
	 */
	public function get_source_type(){
		return $this->source;
	}
	
	/**
	 * Return whatever the source is for this import/export, e.g. an ID, a username or anything else relevant to custom format
	 * @return string
	 */
	public function get_source(){
		if( !empty($this->meta['source']) ){
			return $this->meta['source'];
		}
		return '';
	}
	
	/**
	 * Gets a format option name which may be a single value or array, and if enabled for this format returns the value defined by this item class or otherwise the default provided by  the format 
	 * @param string $option_name
	 * @return array|string
	 */
	public function get_format_option( $option_name ){
		$option = false;
		if( !empty(static::$options[$option_name]) ){
			if( is_array(static::$options[$option_name]) ){
				$option = array();
				foreach(static::$options[$option_name] as $k => $v){
					$option[$k] = false;
					if( $v !== false ){
						$option[$k] = true;
						if( !empty($this->meta[$option_name]) && isset($this->meta[$option_name][$k]) ){
							$option[$k] = $this->meta[$option_name][$k];
						}elseif( array_key_exists($k, static::$option_defaults[$option_name]) ){
							$option[$k] = static::$option_defaults[$option_name][$k];
						}
					}
				}
			}else{
				$option = true;
				if( !empty($this->meta[$option_name]) && isset($$this->meta[$option_name]) ){
					$option = $this->meta[$option_name];
				}elseif( !empty(static::$option_defaults[$option_name]) ){
					$option = static::$option_defaults[$option_name];
				}
			}
		}
		return $option;
	}
	
	/**
	 * Converts the filte scope into a date range in an array. For example, if the scope is '+7 days' then it'd be an array of dates containing today's date and a date 7 days later relative to today.
	 * returned array will contain two items, where one or both may contain a date value or no value for no range.
	 * @return array
	 */
	public function get_filter_scope(){
		$scope = $this->filter_scope;
		if( empty($scope) ) return array('','');
		if ( preg_match ( "/^([0-9]{4}-[0-9]{2}-[0-9]{2})?,([0-9]{4}-[0-9]{2}-[0-9]{2})?$/", $scope ) ) {
			//This is to become an array, so let's split it up and that's it.
			return explode(',', $scope);
		}else{
			//This is a relative search.
			$EM_DateTime = new EM_DateTime();
			if( $scope == 'future' ){
				return array( $EM_DateTime->getDate(), '');
			}elseif( $scope == 'past' ){
				return array('', $EM_DateTime->sub('P1D')->getDate());
			}else{
				//strtotime value, in form of +/-n x, e.g. +1 days
				if( substr($scope, 0, 1) == '+' ){
					return array($EM_DateTime->getDate(), $EM_DateTime->modify($scope)->sub('P1D')->getDate());
				}else{
					return array($EM_DateTime->modify($scope)->add('P1D')->getDate(), $EM_DateTime->setTimestamp(time())->getDate());				
				}
			}
		}
	}
	
	/**
	 * Get a list of valid filter scopes that can be applied to an import or export.
	 * @return array()
	 */
	public function get_filter_scopes(){
		$scopes = array(
				'future' => __('Future Events','events-manager-io'),
				'past' => __('Past Events','events-manager-io'),
				'+7 days' => sprintf(__('Next %d Days', 'events-manager-io'), 7),
				'+14 days' => sprintf(__('Next %d Days', 'events-manager-io'), 14),
				'-7 days' => sprintf(__('Past %d Days', 'events-manager-io'), 7),
				'-14 days' => sprintf(__('Past %d Days', 'events-manager-io'), 14),
				'custom' => sprintf(__('Specfic date range', 'events-manager-io'), 14),
		);
		//you can add array items with keys containing valid strtotime values 
		return array_merge($scopes, apply_filters('emio_item_get_filter_scopes', array()));
	}
	
	/**
	 * Depending on filter scope, compare the start and end date of an event to determine if the chosen scope is met
	 * @param string $start Start timestamp, if false only end time is checked.
	 * @param string $end End timestamp, which may be the same as the start timestamp.
	 * @return boolean
	 */
	public function filter_scope( $start, $end ){
		$scope = $this->get_filter_scope();
		if( !empty($scope[0]) ) $scope[0] = strtotime($scope[0], current_time('timestamp'));
		if( !empty($scope[1]) ) $scope[1] = strtotime($scope[1], current_time('timestamp'));
		if( empty($scope[0]) || $start >= $scope[0] ){
			if( empty($scope[1]) || $start <= $scope[1] ){
				return true;
			}
		}
		if( get_option('dbem_events_current_are_past') ){
			if( empty($scope[0]) || $end >= $scope[0] ){
				if( empty($scope[1]) || $end <= $scope[1] ){
					return true;
				}
			}
		}
		return false;
	}
	
	/* FORMAT FUNCTIONS */
	
	/**
	 * Returns the source types which are allowed by this format, whether pushing or pulling information from.
	 * @return array
	 */
	public function get_source_types(){
		return array(
			'fields' => array(
				'file' => array(
					'name' => __('File', 'events-manager-io'),
					'description' => __('Upload the file containing your import data.', 'events-manager-io')
				),
				'url' => array(
					'name' => __('URL', 'events-manager-io'),
					'placeholder' => 'http://your-source-site.com/path/to/source/',
					'description' => __('Enter a valid URL.', 'events-manager-io')
				)
			),
			'groups' => array()
		);
	}
	
	/**
	 * Logs a pre-prepared array of information about an imported object. If true is supplied as $history or no uid key exists, then no action is taken.
	 * If $EM_Object also doesn't have an ID, then it will also be ignored.
	 * @param array $history
	 * @param EM_Event|EM_Location $EM_Object
	 * @return bool
	 */
	public function save_history($history, $EM_Object ){
		global $wpdb;
		//check we can even save history here
		$is_error = !empty($history['action']) && $history['action'] === 'error' && !empty($history['error']);
		if( !is_array($history) || empty($history['uid']) || (empty($EM_Object->post_id) && !$is_error) ) return false;
		//prepare data for saving
		$history['type'] = get_class($EM_Object) == 'EM_Event' ? 'event':'location';
		$format = $history['type'] == 'event' ? '#_EVENTNAME (#_EVENTDATES)' : '#_LOCATIONNAME, #_LOCATIONTOWN';
		if( empty($history['uid_md5']) ){
			$history['uid_md5'] = md5($history['uid']);
		}
		//check if syncing is enabled and if so then save sync data
		if( (empty($history['action']) || $history['action'] !== 'error') && ($this->type == 'import' || static::$supports_syncing) ){
			if( empty($history['checksum']) ){
				$history['checksum'] = md5(json_encode($EM_Object));
			}
			$sync_history = array(
				'uid_md5' => pack('H*', $history['uid_md5']),
				'uid' => $history['uid'],
				'checksum' => pack('H*', $history['checksum']),
				'io_id' => $this->ID,
				'post_id' => $EM_Object->post_id,
				'type' => $history['type'],
			);
			if( !empty($history['action']) && $history['action'] == 'update' ){
				$sync_history['date_modified'] = $this->batch_start;
				unset($sync_history['io_id']); unset($sync_history['post_id']);
				$sync_result = $wpdb->update(EMIO_TABLE_SYNC, $sync_history, array('io_id' => $this->ID, 'post_id' => $EM_Object->post_id)) !== false;
			}else{
				$sync_history['date_created'] = $sync_history['date_modified'] = $this->batch_start;
				$sync_result = $wpdb->insert(EMIO_TABLE_SYNC, $sync_history);
			}
		}else{
			$sync_result = true;
		}
		//save log history
		$io_log = array(
			'io_id' => $this->ID,
			'post_id' => absint($EM_Object->post_id),
			'uuid' => pack('H*',str_replace('-', '', $this->batch_uuid)), //we hash this table for space saving and marginal performance gains
			'uid_md5' => pack('H*',$history['uid_md5']),
			'uid' => $history['uid'],
			'type' => $history['type'],
			'log_date' => $this->batch_start,
			'log_desc' => $EM_Object->output($format)
		);
		if( $is_error ){
			$io_log['action'] = 'error';
			$io_log['log_desc'] = $io_log['log_desc'] .' && '. $history['error'];
		}else{
			$io_log['action'] = empty($history['action']) || $history['action'] == 'create' ? 'create':'update';
		}
		if( !empty($history['url']) ) $io_log['url'] = $history['url'];
		return $sync_result && $wpdb->insert(EMIO_TABLE_LOG, $io_log);
	}
}