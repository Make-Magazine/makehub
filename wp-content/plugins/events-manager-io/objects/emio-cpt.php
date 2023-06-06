<?php
/**
 * Bace Class for Custom Post Types (mainly Event and Location), containing similar/shared values
 * Class EMIO_Object
 */
class EMIO_CPT extends EMIO_Object {
	/**
	 * Optional. Post ID of item if previously imported, if unchanged parsing can be skipped.
	 * @var int
	 */
	public $post_id;
	/**
	 * Name of event or location.
	 * @var string
	 */
	public $name;
	/**
	 * Optional. Description of event or location.
	 * @var string
	 */
	public $content;
	/**
	 * Featured Image to be imported as a URL that can be fetched.
	 * @var string
	 */
	public $image;
	/**
	 * Optional. Categories to be imported/created.
	 * @var string
	 */
	public $categories;
	/**
	 * Optional. Tags to be imported/created.
	 * @var string
	 */
	public $tags;

	/**
	 * Gets the data belonging to this object that'll be used to generate a checksum. Should be overriden by child objects.
	 * @return array
	 */
	public function get_checksum_data(){
		$item_checksum = array();
		$keys = array_merge( parent::get_checksum_data(), array('name', 'content', 'image', 'categories', 'tags', 'meta'));
		foreach( $keys as $key ) if( isset($this->$key) ) $item_checksum[$key] = $this->$key;
		return $item_checksum;
	}
}