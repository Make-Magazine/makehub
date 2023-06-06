<?php
/**
 * Represents an object to be imported or exported.
 * Class EMIO_Object
 */
class EMIO_Object {
	/**
	 * Optional. If the source has a unique ID for this item, it'll be provided. Otherwise, a unique ID will be created based on the the item data.
	 * @var string
	 */
	public $uid;

	//These following properties can be supplied but are ignored other than when generating checksums and unique identifiers for checking if an item has changed and/or needs importing.
	/**
	 * Optional. MD5 hash of uid if not supplied. This is considered the source unique ID in the em_emio_history table for lookup purposes.
	 * @var string
	 */
	public $uid_md5;
	/**
	 * Optional. MD5 checksum of an item array. Used to compare whether there have been changes to the source item since last import.
	 * @var string
	 */
	public $checksum;
	/**
	 * Whether item has been updated since last import (true) or was unchanged therefore can be skipped (false).
	 * @var boolean
	 */
	public $updated = false;
	/**
	 * Indicates if the item should be skipped for import.
	 * @var boolean
	 */
	public $skip = false;
	/**
	 * Indicates if the item be displayed in import preview tables or not?
	 * @var boolean
	 */
	public $display = true;
	/**
	 * Indicates if item was deleted in Events Manager and should be ignored during import.
	 * @var boolean
	 */
	public $deleted = false;
	/**
	 * Indicates if item is a duplicate of another item already in Events Manager and should be ignored during import.
	 * @var boolean
	 */
	public $duplicate = false;
	/**
	 * Array containing meta data about object. All objects should contain a source type representing the format used to import this object.
	 * @var array
	 */
	public $meta = array();
	/**
	 * An external URL for this CPT, if one exists as the source or desitnation depending whether it's due to an import or export
	 * @var string
	 */
	public $external_url;
	/**
	 * Contains the EM_Object to be created or updated, such as a EM_Event or EM_Location
	 * @var EM_Event|EM_Location
	 */
	public $object;

	/**
	 * EMIO_Object constructor.
	 * @param array $props
	 */
	public function __construct($props = array() ){
		$vars = get_class_vars(get_class($this));
		foreach( $props as $k => $v ){
			if( array_key_exists($k, $vars) ){
				$this->$k = $v;
			}
		}
	}

	/**
	 * @return array
	 */
	public function to_array(){
		$vars = get_object_vars($this);
		unset($vars['object']);
		return $vars;
	}

	/**
	 * Generates a unique ID if none has been assigned already and also generates the MD5 hash of this ID.
	 * Child objects will in most cases want to override the get_uid_string() function to supply a default uid when none is supplied by the import source.
	 * @return string
	 */
	public function generate_uid(){
		if( !isset($this->uid) ){
			//generate a UID if none defined or if we need a forced refresh.
			$this->uid = md5($this->get_uid_string());
		}
		$this->uid_md5 = strlen($this->uid) == 32 ? $this->uid:md5($this->uid);
		return $this->uid;
	}

	/**
	 * @return string
	 */
	public function get_uid_string(){
		return '';
	}

	/**
	 * Generates a checksum according to the $item array passed in and within the context of the scope for this import.
	 * If in the 'events' scope, only event-specific and generic fields are included to create the checksum, same with location, and 'all' or 'events+locations' uses all available fields of both contexts.
	 * @param boolean $refresh If set to true the checksum will be regenerated even if previously generated.
	 * @return string MD5 checskum of object containing only relevant key values to this import/export scope context
	 * @uses EMIO_Object::get_checksum_data()
	 */
	public function generate_checksum( $refresh = false ){
		if( !$refresh && empty($this->checksum) ){
			$item_checksum = $this->get_checksum_data();
			$item_checksum = $this->clean_checksum_array($item_checksum);
			$this->checksum = md5(serialize($item_checksum));
		}
		return $this->checksum;
	}

	/**
	 * Generates an array of data to be used when generating a checksum, which is extended by child objects to add their own specific data.
	 * @return array
	 */
	public function get_checksum_data(){
		return array();
	}

	/**
	 * Takes an array and strips out any keys with values within it, which can then be used for a more concise checksum array generation.
	 * @param array $array
	 * @return array
	 */
	protected function clean_checksum_array(array $array ){
		foreach( $array as $key => $value ){
			if( is_array($value) ){
				$clean_array = $this->clean_checksum_array($value);
				if( empty($clean_array) ){
					unset($array[$key]);
				}else{
					$array[$key] = $clean_array;
				}
			}else{
				if( empty($value) ) unset($array[$key]);
			}
		}
		return $array;
	}
}