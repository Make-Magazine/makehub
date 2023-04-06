<?php
/**
 * Class EMIO_Event
 */
class EMIO_Event extends EMIO_CPT {
	/**
	 * Start date of event as a unix timestamp in UTC timezone.
	 * @var int
	 */
	public $start;
	/**
	 * End date of event in unix timestamp in UTC timezone.
	 * @var int
	 */
	public $end;
	/**
	 * Timezone of the event.
	 * @var string
	 */
	public $timezone;
	/**
	 * Optional, set to true if event is an all-day event.
	 * @var boolean
	 */
	public $all_day;
	/**
	 * Location object, if event has a location to be parsed.
	 * @var EMIO_Location
	 */
	public $location;
	/**
	 * The location type name/string, used if no location is set. Requires $this->event_location also to be set.
	 * @var string
	 */
	public $event_location_type;
	/**
	 * Contains all settings needed to create an event location object.
	 * @var array
	 */
	public $event_location;
	
	public $meta = array(
		'event_url' => false,
		'bookings_url' => false,
		'bookings_price' => false,
		'bookings_currency' => false,
		'bookings_spaces' => false,
		'bookings_available' => false,
		'bookings_confirmed' => false,
	);

	public function __construct( $props = array() ) {
		//remove location and add construct/add it separately as an object
		if( !empty($props['location']) ){
			$this->location = new EMIO_Location($props['location']);
			unset($props['location']);
		}elseif( !empty($props['event_location_type']) ){
			$this->event_location_type = $props['event_location_type'];
			$this->event_location = $props['event_location'];
		}
		parent::__construct($props);
	}
	
	public function get_event_location( $EM_Event ){
		$Event_Location = \EM_Event_Locations\Event_Locations::get( $this->event_location_type, $EM_Event ); /* @var  \EM_Event_Locations\Event_Location $Event_Location */
 		$base_meta_key = '_event_location_'.$Event_Location::$type.'_';
 		$event_location_meta = array();
		foreach( $this->event_location as $k => $v ){
			// prep location data with correct base key
			if( is_array($v) ){ $v = array($v); } // trick
			$event_location_meta[$base_meta_key.$k] = $v;
		}
		$Event_Location->load_postdata( $event_location_meta );
		$EM_Event->event_location = $Event_Location;
	}

	public function generate_uid(){
		parent::generate_uid();
		if( !empty($this->location) ) $this->location->generate_uid();
		return $this->uid;
	}

	public function get_uid_string(){
		return $this->name.'_'.$this->start.'_'.$this->end;
	}

	public function generate_checksum($refresh = false) {
		if( !empty($this->location) ) $this->location->generate_checksum( $refresh );
		return parent::generate_checksum($refresh);
	}

	public function get_checksum_data(){
		$item_checksum = parent::get_checksum_data();
		if( !empty($this->location) ){
			$item_checksum['location'] = $this->location->get_checksum_data();
		}
		$keys = array('start', 'end', 'all_day', 'timezone');
		foreach( $keys as $key ) if( isset($this->$key) ) $item_checksum[$key] = $this->$key;
		return $item_checksum;
	}
	
	public function to_array() {
		$array = parent::to_array();
		if( !empty($array['location']) && is_object($array['location']) ){
			$array['location'] = $this->location->to_array();
		}
		return $array;
	}
}