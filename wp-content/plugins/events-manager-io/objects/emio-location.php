<?php
/**
 * Class EMIO_Location
 */
class EMIO_Location extends EMIO_CPT {
	/**
	 * Optional. If supplied, all other items in this array are ignored and this is used instead to load an existing location ID.
	 * @var int
	 */
	public $id;
	/**
	 * Optional If fuzzy location is enabled for this import format, this value is assumed as a full address representation and will be used to split address name, street, country etc.
	 * If $name is also set, it's assumed tis does not contain the location name and is just an address.
	 * @var string;
	 */
	public $location;
	/**
	 * Location address.
	 * @var string
	 */
	public $address;
	/**
	 * Location town.
	 * @var string
	 */
	public $town;
	/**
	 * Location state.
	 * @var string
	 */
	public $state;
	/**
	 * Location postcode.
	 * @var string
	 */
	public $postcode;
	/**
	 * Location Country
	 * @var string
	 */
	public $country;
	/**
	 * Optional (longitude must also be supplied). If fuzzy locations are allowed for this format, and geocoding via Google API is enabled, this can be auto-generated by this function.
	 * @var string
	 */
	public $latitude;
	/**
	 * Optional (longitude must also be supplied). If fuzzy locations are allowed for this format, and geocoding via Google API is enabled, this can be auto-generated by this function.
	 * @var string
	 */
	public $longitude;
	/**
	 * Contains the EM_Location to be created or updated.
	 * @var EM_Location
	 */
	public $object;
	/**
	 * Whether or not this has been parsed by EMIO_Import and can be populated reliably into an EM_Location object.
	 * @var boolean
	 */
	public $parsed = false;
	/**
	 * Additional meta that can be merged into the EM_Location->location_attributes array
	 * @var array
	 */
	public $meta = array('location_url' => false);

	public function get_uid_string(){
		$uid = '';
		//since we don't know how complete a location may be, we add a few things. If none of these are present, the location wouldn't validate anyway.
		if( !empty($this->location) ) $uid = $this->location;
		if( !empty($this->name) ) $uid = $this->name;
		if( !empty($this->address) ) $uid .= $this->address;
		if( !empty($this->town) ) $uid .= $this->town;
		if( !empty($this->state) ) $uid .= $this->state;
		if( !empty($this->postcode) ) $uid .= $this->postcode;
		if( !empty($this->country) ) $uid .= $this->country;
		return $uid;
	}

	public function get_checksum_data(){
		$item_checksum = parent::get_checksum_data();
		$keys = array('address', 'town', 'state', 'postcode', 'country', 'latitude', 'longitude');
		foreach( $keys as $key ) if( isset($this->$key) ) $item_checksum[$key] = $this->$key;
		return $item_checksum;
	}

	public function populate_location(){
		if( empty($this->object) ){
			if( !empty($this->post_id) ){
				$this->object = em_get_location( $this->post_id, 'post_id' );
			}elseif( !empty($this->id) ){
				$this->object = em_get_location( $this->id );
			}else{
				$this->object = new EM_Location();
			}
		}
		$EM_Location = $this->object;
		if( !empty($this->name) ) $EM_Location->location_name = $this->name;
		if( !empty($this->address) ) $EM_Location->location_address = $this->address;
		if( !empty($this->town) ) $EM_Location->location_town = $this->town;
		if( !empty($this->state) ) $EM_Location->location_state = $this->state;
		if( !empty($this->postcode) ) $EM_Location->location_postcode = $this->postcode;
		if( !empty($this->country) ) $EM_Location->location_country = $this->country;
		if( !empty($this->latitude) ) $EM_Location->location_latitude = $this->latitude;
		if( !empty($this->longitude) ) $EM_Location->location_longitude = $this->longitude;
		
		if( !empty($this->name) ) $EM_Location->location_name = $this->name;
		if( !empty($this->address) ) $EM_Location->location_address = $this->address;
		if( !empty($this->town) ){
			$EM_Location->location_town = $this->town;
		}else{
			$EM_Location->location_town = ' ';
		}
		if( !empty($this->state) ) $EM_Location->location_state = $this->state;
		if( !empty($this->postcode) ) $EM_Location->location_postcode = $this->postcode;
		if( !empty($this->region) ) $EM_Location->location_region = $this->region;
		if( !empty($this->country) ){
			//we may need to reverse lookup country
			$countries = em_get_countries();
			if( in_array($this->country, $countries) ){
				foreach( $countries as $k => $v ) if( $v == $this->country ) break;
				$EM_Location->location_country = $k;
			}elseif( array_key_exists($this->country, $countries)){
				$EM_Location->location_country = $this->country;
			}elseif( get_option('dbem_location_default_country') ){
				//use default country if it exists
				$EM_Location->location_country = get_option('dbem_location_default_country');
			}
		}elseif( get_option('dbem_location_default_country') ){
			//use default country if it exists
			$EM_Location->location_country = get_option('dbem_location_default_country');
		}
		if( !empty($this->longitude) && !empty($this->latitude) ){
			$EM_Location->location_longitude = $this->longitude;
			$EM_Location->location_latitude = $this->latitude;
		}
		foreach( $this->meta as $meta_key => $meta_value ){
			if( !empty($meta_value) ){
				$EM_Location->location_attributes[$meta_key] = $meta_value;
			}
		}
	}
}