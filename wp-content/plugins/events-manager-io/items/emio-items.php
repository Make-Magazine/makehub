<?php

/**
 * Class used to search and retrieve imports and exports
 * @author marcus
 *
 */
class EMIO_Items extends EM_Object implements Iterator, Countable {
	
	/**
	 * Type of list we're creating, either import or export
	 * @var string
	 */
	public static $type = 'item';
	/**
	 * Number of found records
	 * @var int
	 */
	public $total_count = 0;
	/**
	 * Items loaded in search
	 * @var array
	 */
	public $items = array();
	
	/**
	 * Array of registered format for this collection (imports or exports).
	 * @var array
	 */
	public static $formats = array();
	
	/**
	 * EMIO_Items constructor.
	 * @param array $args
	 */
	public function __construct($args = array() ){
		global $wpdb;

		$args = self::get_default_search($args);
		$limit = ( $args['limit'] && is_numeric($args['limit'])) ? "LIMIT {$args['limit']}" : '';
		$offset = ( $limit != "" && is_numeric($args['offset']) ) ? "OFFSET {$args['offset']}" : '';

		//Get the default conditions
		$conditions = self::build_sql_conditions($args);
		//Put it all together
		$where = ( count($conditions) > 0 ) ? " WHERE " . implode ( " AND ", $conditions ):'';

		$orderby = self::build_sql_orderby($args);
		$orderby_sql = ( count($orderby) > 0 ) ? 'ORDER BY '. implode(', ', $orderby) : '';

		$sql = apply_filters('emio_objects_sql',"
				SELECT SQL_CALC_FOUND_ROWS * FROM ". EMIO_TABLE ."
				$where
				$orderby_sql
				$limit $offset
				", $args);
		$results = $wpdb->get_results($sql, ARRAY_A);
		$item_generic_class = substr(get_called_class(), 0, -1);
		foreach( $results as $result ){
			$EMIO_Item = static::get_format($result['format']);
			if( empty($EMIO_Item) ){
				$EMIO_Item = $item_generic_class;
			}
			$this->items[] = new $EMIO_Item($result);
		}
		$this->total_count = $wpdb->get_var('SELECT FOUND_ROWS()');
		return $this->items;
	}
	
	/**
	 * Returns the class name for this specific format, for import or export.
	 * @param string $format
	 * @return string
	 */
	public static function get_format( $format ){
		if( !empty(static::$formats[$format]) ){
			return static::$formats[$format];
		}
		return false;
	}
	
	/**
	 * Register an export format so it can be used in the exporter interface.
	 * @param EMIO_Item|string $EMIO_Item The class name of the desired format to register.
	 */
	public static function register_format($EMIO_Item ){
		static::$formats[$EMIO_Item::$format] = $EMIO_Item;
	}
	
	/**
	 * Returns the EMIO_Export or EMIO_Import format class based on the $item_id, returns false if no item exists.
	 * @param int $item_id
	 * @return EMIO_Import|EMIO_Export|false
	 */
	public static final function load( $item_id ){
		global $wpdb;
		if( wp_is_uuid( $item_id ) ){
			$sql = $wpdb->prepare("SELECT * FROM ".EMIO_TABLE." WHERE uuid=%s", $item_id);
		}else{
			$sql = $wpdb->prepare("SELECT * FROM ".EMIO_TABLE." WHERE ID=%d", $item_id);
		}
		$result = $wpdb->get_row($sql, ARRAY_A);
		if( is_array($result) ){
			$EMIO_Item = static::get_format($result['format']);
			if( !empty($EMIO_Item) ){
				$EMIO_Item = new $EMIO_Item($result);
				return $EMIO_Item;
			}
		}
		return false;
	}
	
	/**
	 * @param array $args
	 * @param array $accepted_fields
	 * @param string $default_order
	 * @return array
	 */
	public static function build_sql_orderby($args, $accepted_fields = array(), $default_order = 'ASC' ){
		if( empty($accepted_fields) ) $accepted_fields = array('name','date_created','status','scope','frequency_start','frequency_end', 'last_import','format');
		return parent::build_sql_orderby($args, $accepted_fields, $default_order);
	}
	
	/**
	 * Builds array of SQL search conditions. We don't need EM_Object conditions so we override that function entirely
	 * @param array $args
	 * @return array
	 */
	public static function build_sql_conditions($args = array() ){
		global $wpdb;
		$conditions = array();
		//frequency types
		if( $args['frequency'] === false ){
			$conditions['frequency'] = 'frequency IS NULL';
		}elseif( $args['frequency'] === true ){
			$conditions['frequency'] = 'frequency IS NOT NULL';
		}elseif( $args['frequency'] ){
			$conditions['frequency'] = $wpdb->prepare('frequency = %s', $args['frequency']);
		}
		//search for frequencies within a certain date, filtered by frequency type or not
		if( !empty($args['frequency_scope']) ){
			$frequency_when = preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $args['frequency_scope']) ? $args['frequency_scope'] : current_time('mysql');
			$frequency_conditions = array();
			$frequency_conditions[] = '('. $wpdb->prepare('%s BETWEEN frequency_start AND frequency_end', $frequency_when) .')';
			$frequency_conditions[] = '('. $wpdb->prepare('frequency_start IS NULL AND frequency_end > %s', $frequency_when) .')';
			$frequency_conditions[] = '('. $wpdb->prepare('frequency_end IS NULL AND frequency_start < %s', $frequency_when) .')';
			$frequency_conditions[] = '(frequency_end IS NULL AND frequency_start IS NULL)';
			$conditions['frequency_scope'] = '('. implode(' OR ', $frequency_conditions) .')';
		}
		//other simple search flags
		if( $args['status'] !== null ){ //overrides frequency_active
			$conditions['status'] = $args['status'] ? 'status = 1' : 'status = 0';
		}
		if( !empty($args['type']) ){
			$conditions['type'] = $wpdb->prepare('type = %s', $args['type']);
		}
		if( !empty($args['format']) ){
			$conditions['format'] = $wpdb->prepare('format = %s', $args['format']);
		}
		return $conditions;
	}
	
	/**
	 * @param array $array_or_defaults
	 * @param array $array
	 * @return array|mixed|void
	 */
	public static function get_default_search($array_or_defaults = array(), $array = array() ){
		$defaults = array(
			'orderby' => 'date_created',
			'order' => 'DESC',
			'status' => null,
			'offset' => 0,
			'page' => 1,
			'type' => static::$type,
			'limit' => 5,
			'frequency' => null,
			'frequency_scope' => false,
			'scope' => false, //not same as scope for events, it's either 'all', 'events' or 'locations'
			'format' => false,
		);
		//sort out whether defaults were supplied or just the array of search values
		if( empty($array) ){
			$array = $array_or_defaults;
		}else{
			$defaults = array_merge($defaults, $array_or_defaults);
		}
		//let EM_Object clean out these
		$args = apply_filters('emio_objects_get_default_search', parent::get_default_search($defaults,$array), $array, $defaults);
		//we only need args present in our $default, so clean out the rest
		return array_intersect_key($args, $defaults);
	}

	//Countable Implementation
	#[\ReturnTypeWillChange]
	public function count(){
		return count($this->items);
	}

	//Iterator Implementation
	#[\ReturnTypeWillChange]
	public function rewind(){
		reset($this->items);
	}
	#[\ReturnTypeWillChange]
	public function current(){
		return current($this->items);
	}
	#[\ReturnTypeWillChange]
	public function key(){
		return key($this->items);
	}
	#[\ReturnTypeWillChange]
	public function next(){
		return next($this->items);
	}
	#[\ReturnTypeWillChange]
	public function valid(){
		$key = key($this->items);
		$var = ($key !== NULL && $key !== FALSE);
		return $var;
	}
}