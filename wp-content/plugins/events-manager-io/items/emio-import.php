<?php
/**
 * Represents a single import job for a specific format. Contains all the information needed to initiate the import with the correct format.
 * @author marcus
 *
 */
class EMIO_Import extends EMIO_Item {
	/**
	 * Declares that this EMIO_Item object is an import, therefore an EMIO_Import object
	 * @var string
	 */
	public $type = 'import';
	/**
	 * Meta values specific for imports
	 *
	 * - action 		What to do with item when it's new, either 'publish' or 'draft'
	 * - attachments 	What to do with attachment, either 'ignore', 'download' or 'remote'
	 *
	 * @var array
	 */
	public $meta = array(
		'post_status' => 'draft',
		'post_update_status' => 'same',
		'attachments' => 'remote',
		'taxonomies' => array(),
		'taxonoimes_new' => array(),
		'fuzzy_location' => array(),
		'ignore_uid' => false
	);
	/**
	 * Indicates whether format grabs infromation in batches or pages. For example APIs such as FB and Meetup will grab 200 events at a time,
	 * so if you're importing more than that in one go, you'll need to parse results multiple times and this flag lets EMIO know to check things
	 * such as total amount of items parsed from previous pages so that it doesn't go over import limits.
	 * @var boolean
	 */
	public static $pagination = false;
	/**
	 * Whether or not a format provides abstract fields that may need manual mapping to our own fields, e.g. a csv file with different headers.
	 * @var boolean
	 */
	public static $field_mapping = false;
	
	public $parsed_locations = array();
	
	public $guessed_timezones = array();
	
	public $preview_mode = false;
	
	/* START Formatting Params */
	public static $options = array();
	
	/**
	 * Options that can be made available to the user when creating imports of this format.
	 * @var array
	 */
	private static $options_default = array(
		'fuzzy_location' => array( //setting this to true will show all options that help handle fuzzy locations, such as in ical where a single address is given
			//setting any of these to true will make them options you can set when adding an import
			//giving them a value other than true or false will use this function for location parsing, but won't be offered as an option to the user when creating an import
			'default' => true, //we leave this to true as an example, you could in most cases only import events and link it to a specific location for some reason
			'placeholder_format' => false, //a placeholder format way of allowing us to extract a location name from a single line address, example 'NAME (ADDRESS)' will allow you to more predictably parse 'Some Cool Place (123 Street, Sometown, USA)'
			'delimiter' => false, //if set to true, user will have option to define a delimiter. If set to false, an array must be provided to parse_location method. If set to a string e.g. ', ', this will be used as the delimiter without asking the user.
			'format' => false, //if set to true, user will have option to define an order for how the location address is formatted and what items it contains e.g. street, country, postcode etc.
			'google_api' => false, //whether or not the Google API should be used which can be values of 0 (no), 1 (yes, use Google to guess the address completely), 2 (parse the location first according to options here, then use Google API to guess address)
			'country' => false, //default country code to use if no country name provided in location info
		),
		'ignore_uid' => false, //if a format may not provide reliable IDs (basically any format without a reliable standard like csv or ical), we can try to generate unique ids to help with updating previously imported items
		'attachments' => true,
	);
	/**
	 * Default option values, if they're enabled in the $options property.
	 * @var array
	 */
	public static $option_defaults = array(
		'fuzzy_location' => array(
			'default' => 0,
			'placeholder_format' => 'NAME, ADDRESS',
			'delimiter' => ',',
			'format' => array('address','town','state','postcode','country'),
			'google_api' => 1,
			'country' => false,
		)
	);
	/**
	 * Default field mapping values for a format that has field mapping enabled. Allows for spreadsheets exported by EM to be automatically mapped.
	 * @var array
	 */
	public static $field_mapping_default = array(
		//event-specific field mapping
		'event_id' => 'event/event_id',
		'event_uid' => 'event/uid', // external uid
		'event_slug' => 'event/slug',
		'event_name' => 'event/name',
		'event_start' => 'event/start',
		'event_end' => 'event/end',
		'event_all_day' => 'event/all_day',
		'post_content' => 'event/content',
		'event_start_time' => 'event/start_time',
		'event_end_time' => 'event/end_time',
		'event_start_date' => 'event/start_date',
		'event_end_date' => 'event/end_date',
		'event_timezone' => 'event/timezone',
		'event_image' => 'event/image',
		'event_language' => 'event/language',
		'event_categories' => 'event/categories',
		'event_tags' => 'event/tags',
		//event with possibly multiple fields to one end point as an array
		'event_attributes' => 'event/meta',
		'event_meta' => 'event/meta',
		'event_url' => 'event/meta/event_url',
		'bookings_url' => 'event/meta/bookings_url',
		'bookings_price' => 'event/meta/bookings_price',
		'bookings_currency' => 'event/meta/bookings_currency',
		'bookings_spaces' => 'event/meta/bookings_spaces',
		'bookings_available' => 'event/meta/bookings_available',
		'bookings_confirmed' => 'event/meta/bookings_confirmed',
		'event_location_type' => 'event/event_location_type',
		'event_location' => 'event/event_location',
		//location-specific field mapping
		'location' => 'location/location',
		'location_id' => 'location/location_id',
		'location_uid' => 'location/uid', // external uid
		'location_slug' => 'location/slug',
		'location_name' => 'location/name',
		'location_address' => 'location/address',
		'location_town' => 'location/town',
		'location_state' => 'location/state',
		'location_postcode' => 'location/postcode',
		'location_region' => 'location/region',
		'location_country' => 'location/country',
		'location_latitude' => 'location/latitude',
		'location_longitude' => 'location/longitude',
		'location_content' => 'location/content',
		'location_image' => 'location/image',
		'location_language' => 'location/language',
		'location_categories' => 'location/categories',
		'location_tags' => 'location/tags',
		//location fields that are possibly repeated and mapped to one destination a an array
		'location_attributes' => 'location/meta',
		'location_url' => 'location/meta/location_url',
		'location_meta' => 'location/meta',
	);
	/**
	 * By default any format can be synced via import, since we generate IDs based on a checksum even if the source doesn't have an ID.
	 * @var array
	 */
	public static $supports_syncing = true;
	/* END Formatting Params */
	
	public function __construct( $id_or_array = array() ){
		if( !static::$init ){
			// get all default options in there
			static::$options = array_replace_recursive( self::$options_default, static::$options);
		}
		parent::__construct( $id_or_array );
		if( !empty($this->meta['url']) && empty($this->meta['source']) ) $this->meta['source'] = $this->meta['url'];
		if( !empty($this->meta['temp_file']) && empty($this->meta['source']) ) $this->meta['source'] = $this->meta['temp_file'];
		if( !empty($this->meta['import_taxonomies']) ) {
			// backwards compatability
			$this->meta['taxonomies'] = $this->meta['import_taxonomies'];
			unset($this->meta['import_taxonomies']);
		}
		// regularize taxonomy names so they match the slug
		if( EM_TAXONOMY_CATEGORY !== 'event-categories' && !empty(static::$field_mapping['event-categories']) ){
			static::$field_mapping[EM_TAXONOMY_CATEGORY] = static::$field_mapping['event-categories'];
			unset(static::$field_mapping['event-categories']);
		}
		if( EM_TAXONOMY_TAG !== 'event-tags' && !empty(static::$field_mapping['event-tags']) ){
			static::$field_mapping[EM_TAXONOMY_TAG] = static::$field_mapping['event-tags'];
			unset(static::$field_mapping['event-tags']);
		}
	}
	
	/**
	 * Parses the passed on EMIO_Import object and uses that to obtain source information for import parsing, returning array of items for processing.
	 * Each parser should return a standardized format of arrays that matches the format described in EMIO_Import::parse(), so that once parsed the returned data is consistent with any other format and can be processed by EMIO_Import
	 * @return array
	 */
	public function import( ){
		return array();
	}
	
	/**
	 * Converts raw data into an associative array of event/location data, which can be limited by $limit.
	 * Returns an array of items within the 'data' key containing associative arrays with keys corresponding to the array of 'headers'.
	 * Specifically used for field mapping purposes in formats such as CSV where the data provided is in freeform tabular format.
	 * @param int $limit
	 * @return array
	 */
	public function import_data( $limit = 0 ){
		return array(
			'headers' => array(),
			'data' => array()
		);
	}
	
	/**
	 * Runs the import based on current settings and save the items to the DB.
	 * An array of items can be supplied, which must be an array of either EM_Event objects or EM_Location objects, depending on the scope.
	 * If no array of items is supplied, EMIO_Import::get() is invoked and retrieves items from the format source.
	 *
	 * @param array $EMIO_Objects
	 * @return array
	 */
	public function run( $EMIO_Objects = array() ){
		global $wpdb;
		$this->batch_uuid = wp_generate_uuid4();
		$this->batch_start = current_time('mysql');
		//get items from source if not supplied
		if( empty($EMIO_Objects) ) $EMIO_Objects = $this->get();
		if( is_wp_error($EMIO_Objects) ) return $EMIO_Objects;
		//deal with taxonomies at this point, creating a reusable id map and EM_Categories object
		if( !empty($this->meta['taxonomies']) ){
			//clean up taxonomy values for wp_set_object_terms()
			foreach( $this->meta['taxonomies'] as $taxonomy_name => $taxonomy_terms ){
				$this->meta['taxonomies'][$taxonomy_name] = array_unique( array_map('intval', $this->meta['taxonomies'][$taxonomy_name]) );
			}
			//MS specific, so we can add categories at a global level
			if( EM_MS_GLOBAL && ($this->scope == 'all' || $this->scope == 'events+locations' || $this->scope == 'events') && !empty($this->meta['taxonomies'][EM_TAXONOMY_CATEGORY]) ){
				$EM_Categories = new EM_Categories($this->meta['taxonomies'][EM_TAXONOMY_CATEGORY]);
			}
			$EM_taxonomies = EM_Object::get_taxonomies();
			$taxonomy_map = array();
			//check the context of the taxonomy and add to array we'll use to save taxonomies for each item saved
			foreach( $EM_taxonomies as $taxonomy ){
				if( $this->scope == 'all' || $this->scope == 'events+locations' || $this->scope == 'events' ){
					if( $taxonomy == EM_TAXONOMY_CATEGORY && !empty($EM_Categories) ) continue; //if we're dealing with EM_Categories for events, skip the category taxonomy
					//check event context
					if( in_array(EM_POST_TYPE_EVENT, $taxonomy['context']) && !empty($this->meta['taxonomies'][$taxonomy['name']]) ){
						$taxonomy_map['events'][$taxonomy['name']] = $this->meta['taxonomies'][$taxonomy['name']];
					}
				}
				if( $this->scope == 'all' || $this->scope == 'events+locations' || $this->scope == 'locations' ){
					//check event context
					if( in_array(EM_POST_TYPE_LOCATION, $taxonomy['context']) && !empty($this->meta['taxonomies'][$taxonomy['name']]) ){
						$taxonomy_map['locations'][$taxonomy['name']] = $this->meta['taxonomies'][$taxonomy['name']];
					}
				}
			}
		}
		//add meta attributes hook
		add_filter('em_get_attributes', function($attributes, $matches, $lattributes){
			$EMIO_Object = $lattributes ? new EMIO_Location() : new EMIO_Event();
			$attributes['names'] = array_merge( $attributes['names'], array_keys($EMIO_Object->meta) );
			return $attributes;
		}, 10, 3);
		//remove hooks that might cause problems
		remove_action('em_event_validate', 'em_data_privacy_cpt_validate');
		remove_action('em_location_validate', 'em_data_privacy_cpt_validate');
		//go through items to import and save
		$result = array('failed' => array(), 'publish'=>array(), 'pending'=>array(), 'draft'=>array() );
		$location_history = array();
		foreach( $EMIO_Objects as $EMIO_Object ){ /* @var EMIO_Object $EMIO_Object */
			$item = $EMIO_Object->object; /* @var EM_Event $item */
			if( !empty($EMIO_Object->skip) ) continue;
			//add categories if applicable (further up)
			if( !empty($EM_Categories) ) $item->categories = $EM_Categories;
			//check if we're also importing a location into an event, and if so make sure we're not importing the same location multiple times as new locations
			if( ($this->scope == 'all' || $this->scope == 'events+locations') && !empty($EMIO_Object->location) ){
				if( !empty($location_history[$EMIO_Object->location->uid_md5]) ){
					$item->location = $location_history[$EMIO_Object->location->uid_md5];
					$item->location_id = $item->location->location_id;
					$EMIO_Object->location->location_id = $item->location->location_id;
					$EMIO_Object->location->emio_skip = true;
				} elseif ( !empty($item->location_id) ){
					// locatino already exists, no action required
					$EMIO_Object->location->emio_skip = true;
				}
			}
			//save the item if it validates and proceed with post-save actions
			$res = $item->validate() && $item->save();
			if( $res ){
				//add meta about import source
				update_post_meta( $item->post_id, 'import_source', static::$format);
				if( ($this->scope == 'all' || $this->scope == 'events+locations') && !empty($item->location) ){
					update_post_meta( $item->get_location()->post_id, 'import_source', static::$format);
				}
				//add any additional meta supplied by this event
				$meta = $item->post_type == EM_POST_TYPE_LOCATION ? $item->location_attributes : $item->event_attributes;
				foreach( $meta as $k => $v ){
					update_post_meta( $item->post_id, $k, $v);
				}
				//if scope is all, meaning we've saved locations too, add an import history for that as well
				$history_res = $history_res_loc = $this->save_history($EMIO_Object, $item);
				if( ($this->scope == 'all' || $this->scope == 'events+locations') && !empty($EMIO_Object->location) && empty($EMIO_Object->location->emio_skip) ){
					$history_res_loc = $this->save_history($EMIO_Object->location, $item->get_location());
					//also save location to uid array, so we can avoid saving duplicate locations that match up
					$location_history[$EMIO_Object->location->uid_md5] = $item->get_location();
				}
				if( $history_res === false || $history_res_loc === false ){
					$result['errors'] = __("Could not update import history database table. Please check with an admin to ensure frequently imported items aren't duplicated", 'events-manager-io');
				}
				//add import to results feedback stati
				if( empty($EMIO_Object->updated) ){
					$result[$item->post_status][] = $item;
					//if( ($this->scope == 'all' || $this->scope == 'events+locations') && !empty($item->location_id) ) $result[$item->get_location()->post_status][] = $item->get_location();
				}else{
					$result['updated'][] = $item;
				}
				// add categories and tags to event objects via $taxonomy_map
				if( $EMIO_Object instanceof EMIO_Event ){
					$taxonomies = array('categories' => EM_TAXONOMY_CATEGORY, 'tags' => EM_TAXONOMY_TAG);
					foreach( $taxonomies as $prop => $taxonomy ){
						if( !empty($EMIO_Object->$prop) ){
							$EMIO_Object->$prop = array_unique( $EMIO_Object->$prop );
							// if cannot create new taxonomies, we only accept numbers
							if( empty($this->meta['taxonomies_new'][$taxonomy]) ){
								$EMIO_Object->$prop = array_unique( array_map('intval', $EMIO_Object->$prop) );
								foreach( $EMIO_Object->$prop as $k => $v ) {
									if( $v === 0 ) unset($EMIO_Object->$prop[$k]);
								}
							}
							if( empty($taxonomy_map) ) $taxonomy_map = array();
							if( empty($taxonomy_map['events']) ) $taxonomy_map['events'] = array();
							if( !empty($taxonomy_map['events'][$taxonomy]) ){
								$taxonomy_map['events'][$taxonomy] = array_merge($taxonomy_map['events'][$taxonomy], $EMIO_Object->$prop);
							}else{
								$taxonomy_map['events'][$taxonomy] = $EMIO_Object->$prop;
							}
						}
					}
				}
				//add extra taxonomies to post object
				if( !empty($taxonomy_map) ){
					//firstly save top tier post id of event or location
					$scope = ($this->scope == 'all' || $this->scope == 'events+locations') ? 'events':$this->scope;
					foreach( $taxonomy_map[$scope] as $taxonomy_name => $taxonomy_terms ){
						wp_set_object_terms($item->post_id, $taxonomy_terms, $taxonomy_name);
					}
					//if scope is importing events and locations, check save taxonomies to location within event
					if( ($this->scope == 'all' || $this->scope == 'events+locations') && !empty($item->get_location()->post_id) && !empty($taxonomy_map['locations']) ){
						foreach( $taxonomy_map['locations'] as $taxonomy_name => $taxonomy_terms ){
							wp_set_object_terms($item->get_location()->post_id, $taxonomy_terms, $taxonomy_name);
						}
					}
				}
				//final step, if we're ready to go... depending on setting, we download the featured image and save it to our site
				if( $this->meta['attachments'] == 'download' && !empty($item->featured_image) ){
					//copied from media_sideload_image(), so that we get the thumbnail ID of image
					/* @todo suggest to WP Trac that media_sideload_image accepts return of id as well */
					// Set variables for storage, fix file filename for query strings.
					preg_match( '/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $item->featured_image, $matches );
					if ( ! $matches ) {
						$image_result = new WP_Error( 'image_sideload_failed', __( 'Invalid image URL' ) );
					}else{
						//download file and add to $_FILES so WP can handle the upload
						$file_array = array();
						$file_array['name'] = basename( $matches[0] );
						// Need to require these files
						if ( !function_exists('media_handle_upload') ) {
							require_once(ABSPATH . "wp-admin" . '/includes/image.php');
							require_once(ABSPATH . "wp-admin" . '/includes/file.php');
							require_once(ABSPATH . "wp-admin" . '/includes/media.php');
						}
						// Download file to temp location.
						$file_array['tmp_name'] = download_url( $item->featured_image );
						// If error storing temporarily, return the error.
						if ( is_wp_error( $file_array['tmp_name'] ) ) {
							$image_result = $file_array['tmp_name'];
						}else{
							// Do the validation and storage stuff.
							$image_result = media_handle_sideload( $file_array, $item->post_id, $item->post_title );
							// If error storing permanently, unlink.
							if ( is_wp_error( $image_result ) ) {
								@unlink( $file_array['tmp_name'] );
							}else{
								update_post_meta($item->post_id, '_thumbnail_id', $image_result);
							}
						}
					}
					if( is_wp_error($image_result) ){
						$err = sprintf(__('Could not download image for %s.', 'events-manager-io'), $item->post_name);
						if( is_super_admin() ) $err .= ' '. $image_result->get_error_message();
						$result['errors'][] = $err;
					}
				}
			}else{
				$result['failed'][] = $item;
				$result['errors'][] = sprintf(_x('Could not import %s named %s.', 'Could not import location/event named XYZ', 'events-manager-io'), $item->post_type, $item->name);
				// log error in IO history
				$EMIO_Object->errors = $item->errors;
				$this->save_history($EMIO_Object, $item);
			}
		}
		// clean up the source
		if( !$this->flush_source( false ) ){
			$result['errors'][] = sprintf(__('Could not delete temporary file %s.', 'events-manager-io'), $this->get_source());
		}
		$wpdb->update(EMIO_TABLE, array('last_update' => current_time('mysql'), 'meta' => maybe_serialize($this->meta)), array('ID' => $this->ID), array('%s', '%s'), '%d');
		return $result;
	}
	
	/**
	 * Returns parsed array of EM objects ready for saving to the database.
	 * @param bool $preview
	 * @return array|WP_Error
	 * @see EMIO_Item::get()
	 * @uses EMIO_Import::get_source()
	 * @uses EMIO_Import::parse()
	 * @uses EMIO_Format::import()
	 */
	public function get( $preview = null ){
		//if preview mode explicitly implied, set it here
		if( $preview !== null ){
			$this->preview_mode = !empty($preview);
		}
		//get source, either by downloading file or obtaining supplied file
		$source = $this->get_source();
		if( is_wp_error($source) ){
			return $source;
		}else{
			//get raw items data
			if( static::$pagination ) $this->load_import_history();
			$items = $this->import();
			if( is_wp_error($items) ){
				return $items;
			}
			if( static::$pagination ) $this->flush_import_history();
			//remove uids if this import is set to ignore uids
			if( !empty(static::$options['ignore_uid']) && !empty($this->meta['ignore_uid']) ){
				foreach( $items as $k => $item ){
					if( !empty($items[$k]->uid) ) unset($items[$k]->uid);
					if( !empty($items[$k]->uid_md5) ) unset($items[$k]->uid_md5);
				}
			}
			//parse items into location or event objects
			$return = $this->parse($items);
			if( $this->source == 'url' ) $this->flush_source();
			return $return;
		}
	}
	
	/**
	 * Get array of imports and turn them into objects. The array of items is an array of EMIO_Event or EMIO_Location objects.
	 * If the scope of this import is 'all' or 'event+locations', then the array will consist of EMIO_Events which will also contain EMIO_Location objects
	 * @param array	$items array of EMIO_Events or EMIO_Locations
	 * @return array|WP_Error Array of parsed items, which may be EM_Location or EM_Event objects
	 */
	public function parse($items){
		$parsed = array();
		//go through items for parsing
		$count = 0;
		//if a format has pagination, it will externally load import history (via EMIO_Import::get()) and run EMIO_Import::pre_parse() from within the format import function.
		if( !static::$pagination ) $this->load_import_history();
		foreach( $items as $EMIO_Object ){ /* @var EMIO_Object $EMIO_Object */
			//double-check this is an EMIO_Object and if it's an array, convert it
			if( !is_object($EMIO_Object) ){
				if( $this->scope == 'all' || $this->scope == 'events+locations' || $this->scope == 'events' ) $EMIO_Object = new EMIO_Event($EMIO_Object);
				elseif( $this->scope == 'locations') $EMIO_Object = new EMIO_Location($EMIO_Object);
				else return new WP_Error();
			}
			$EMIO_Object->meta['import_source'] = static::$format;
			
			//only non-paginated formats need to pre-parse, it's expected that formats with pagination (e.g. APIs like FB and Meetup) need to run pre_parse() whilst looping pages
			if( !static::$pagination ){
				if( !$this->pre_parse( $EMIO_Object, $count ) ) continue;
			}
			
			//fist step is to see if we've imported this item before, and whether to update or skip the item entirely
			//now, use the uid to check if the item has been imported before and if so, decide how to proceed with the import.
			if( !empty($EMIO_Object->post_id) ){
				//if we're still here and we need to load the previously item, and check whether it was deleted (therefore ignored) and/or may need updating
				if( $EMIO_Object->deleted ){
					//we can skip this event as it's unchanged or deleted
					$EM_Object = get_class($EMIO_Object) == 'EMIO_Location' ? new EM_Location() : new EM_Event();
				}else{
					$EM_Object = get_class($EMIO_Object) == 'EMIO_Location' ? em_get_location($EMIO_Object->post_id, 'post_id') : em_get_event($EMIO_Object->post_id, 'post_id');
				}
				$EMIO_Object->object = $EM_Object;
			}
			
			//if it's a new item or we're updating, through here. also if deleted but displaying data for preview, parse again
			if( empty($EMIO_Object->post_id) || $EMIO_Object->updated || $EMIO_Object->deleted ){
				//prepare objects for final parsing
				if( $this->scope == 'all' || $this->scope == 'events+locations' || $this->scope == 'events'){
					$EM_Event = empty($EM_Object) ? new EM_Event() : $EM_Object; /* @var EM_Event $EM_Event */
					$EM_Event->event_name = $EMIO_Object->name;
					$EM_Event->event_owner = $this->user_id;
					if( !empty($EMIO_Object->content) ) $EM_Event->post_content = $EMIO_Object->content;
					if( !empty($EM_Event->post_id) ){
						$EM_Event->post_status = $EM_Event->force_status = $this->meta['post_update_status'] == 'same' ? $EM_Event->post_status : $this->meta['post_update_status'];
					}else{
						$EM_Event->post_status = $EM_Event->force_status = $this->meta['post_status'];
					}
					if( empty($EM_Event->event_owner) ) $EM_Event->event_owner = $EM_Event->post_author = $this->user_id;
					$EM_Event->post_excerpt = '';
					//sort out the times
					if( empty($EMIO_Object->timezone) ) $EMIO_Object->timezone = EM_DateTimeZone::create()->getName();
					$EM_DateTime = new EM_DateTime($EMIO_Object->start, $EMIO_Object->timezone);
					$EM_DateTime->setTimezone($EMIO_Object->timezone); //<= 5.9.2 compat
					$EM_Event->event_timezone = $EM_DateTime->getTimezone()->getName();
					$EM_Event->event_start_date = $EM_DateTime->getDate();
					$EM_Event->event_start_time = $EM_DateTime->getTime();
					$EM_Event->event_start = $EM_DateTime->getDateTime();
					$EM_Event->event_end_date = $EM_DateTime->setTimestamp($EMIO_Object->end)->getDate();
					$EM_Event->event_end_time = $EM_DateTime->getTime();
					$EM_Event->event_end = $EM_DateTime->getDateTime();
					$EM_Event->event_all_day = !empty($EMIO_Object->all_day);
					//add meta
					if( !empty($EMIO_Object->meta) ) foreach( $EMIO_Object->meta as $k => $v ) $EM_Event->event_attributes[$k] = $v;
					//pass on image for later use
					if( !empty($EMIO_Object->image) && preg_match('/http(s)?:\/\/.+\.(jpg|jpeg|png|gif|ico)(\?.+)?$/i', $EMIO_Object->image) ) $EM_Event->featured_image = $EMIO_Object->image;
					
					//If scope is all, then we deal with the possibility of a supplied location belonging to this event
					if( $this->scope == 'all' || $this->scope == 'events+locations' ){
						if( empty($EMIO_Object->location) ){
							// load event location if necessary
							if( !empty($EMIO_Object->event_location) && !empty($EMIO_Object->event_location_type) ){
								$EM_Event->location_id = null;
								$EM_Event->event_location_type = $EMIO_Object->event_location_type;
								$EMIO_Object->get_event_location( $EM_Event );
							}else{
								//no location info supplied, so it's a 'no location' event
								$EM_Event->location_id = 0;
							}
						}else{
							$EMIO_Location = $EMIO_Object->location;
							if( !empty($EMIO_Location->id) ){
								//we're adding a default location to this event, nothing else needed
								$EM_Event->location_id = $EMIO_Object->location->id;
							}else{
								if( !empty($EMIO_Location->post_id) ){
									//load a location, check if it exists. If it doesn't, including if deleted, we recreate it, because the event has a location.
									if( $EMIO_Location->deleted ){
										$EMIO_Location->post_id = null;
										if( !$EMIO_Object->skip ){
											if( $EMIO_Object->post_id ){
												$EMIO_Object->updated = true;
											}
											$EMIO_Location->deleted = false;
											$EMIO_Location =  $this->parse_location( $EMIO_Object );
										}else{
											//populate the location as best as possible
											$EMIO_Location->populate_location();
										}
									}else{
										$EMIO_Location->deleted = false;
										$EMIO_Location->object = em_get_location( $EMIO_Location->post_id, 'post_id' );
										$EMIO_Location->id = $EMIO_Location->object->location_id;
										//now if necessary we populate the new data into the location to update/recreate it
										if( $EMIO_Location->updated ){
											//no skipping here, since we either update a location or recreate it since this event needs it
											$EMIO_Location = $this->parse_location( $EMIO_Object );
										}else{
											$EMIO_Object->location->skip = true;
										}
									}
								}else{
									$EMIO_Location = $this->parse_location( $EMIO_Object );
								}
								//add location to event
								if( !empty($EMIO_Location->object) ){
									$EM_Event->location = $EMIO_Location->object;
									$EM_Event->location_id = $EMIO_Location->object->location_id;
								}
							}
						}
					}else{
						$EM_Event->location_id = 0;
					}
					// tags and categories, currently only considered for events, potentially we could map any taxonomy though a specific taxonomies properties array with actual names for keys
					// convert to term ids if they exist
					$taxonmies = array('categories' => EM_TAXONOMY_CATEGORY, 'tags' => EM_TAXONOMY_TAG);
					foreach( $taxonmies as $prop => $taxonomy ){
						if( !empty($EMIO_Object->$prop) ){
							if( !is_array($EMIO_Object->$prop) ){
								$EMIO_Object->$prop = explode(',', $EMIO_Object->$prop);
							}
							foreach( $EMIO_Object->$prop as $k => $taxonomy_term ){
								$taxonomy_term = is_numeric($taxonomy_term) ? absint($taxonomy_term) : trim($taxonomy_term);
								$term = term_exists($taxonomy_term, $taxonomy);
								if( $term ){
									$EMIO_Object->$prop[$k] = absint($term['term_id']);
								}else{
									// check if owner has access to create categories
									if( empty($this->meta['taxonomies_new'][$taxonomy]) ) {
										unset( $EMIO_Object->$prop[ $k ] );
									}
								}
							}
						}
					}
					//save as $EM_Object so it's added to $parsed array
					$EMIO_Object->object = $EM_Event;
				}elseif( $this->scope == 'locations' ){
					$this->parse_location( $EMIO_Object );
					//pass on image for later use
					if( !empty($EMIO_Object->image) && preg_match('/http(s)?:\/\/.+\.(jpg|jpeg|png|gif|ico)(\?.+)?$/i', $EMIO_Object->location->image) ) $EMIO_Object->object->featured_image = $EMIO_Object->location->image;
				}
			}
			//save to array
			$parsed[] = $EMIO_Object;
			//reset things
			unset($EM_Event, $EM_Location, $EM_Object);
			//update count and repeat
			if( empty($EMIO_Object->skip) ) $count++; //don't count skipped item to limit since no action is taken on it
		}
		if( !static::$pagination ) $this->flush_import_history();
		return $parsed;
	}
	
	/**
	 * Does some pre-parsing of an $item array and returns a false as early as possible on whether this $item should be skipped for
	 * import or display during a preview. If true, item will be imported or considered for display.
	 *
	 * This is most useful when run within a loop context of all items being considered for import, and using $count to identify how many items
	 * have already been considered for import.
	 *
	 * @param EMIO_Object $EMIO_Object
	 * @param int $count
	 * @return boolean
	 */
	public function pre_parse( $EMIO_Object, $count = 0 ){
		//do a quick check if we've been here before, if so return based on whether we had previously decided to preview it or not
		if( !empty($EMIO_Object->skip) ) return empty($EMIO_Object->display);
		//limit, which will skip items that are to be ignored
		if( $this->filter_limit > 0 && $count + 1 > $this->filter_limit ){
			$EMIO_Object->skip = true;
			return false;
		}
		//text search
		if( !empty($this->filter) ){
			$found = false;
			if( $this->scope == 'events' || $this->scope == 'all' || $this->scope == 'events+locations' ){
				if( !empty($EMIO_Object->name) && stristr($EMIO_Object->name, $this->filter) ) $found = true;
				if( !empty($EMIO_Object->content) && stristr($EMIO_Object->content, $this->filter) ) $found = true;
				if( ($this->scope == 'all' || $this->scope == 'events+locations') && !empty($EMIO_Object->location) ){
					if( !empty($EMIO_Object->location->location) && stristr($EMIO_Object->location->location, $this->filter) ) $found = true;
					if( !empty($EMIO_Object->location->name) && stristr($EMIO_Object->location->name, $this->filter) ) $found = true;
					if( !empty($EMIO_Object->location->address) && stristr($EMIO_Object->location->address, $this->filter) ) $found = true;
					if( !empty($EMIO_Object->location->content) && stristr($EMIO_Object->location->content, $this->filter) ) $found = true;
				}
			}elseif( $this->scope == 'locations' ){
				if( !empty($EMIO_Object->location) && stristr($EMIO_Object->location, $this->filter) ) $found = true;
				if( !empty($EMIO_Object->name) && stristr($EMIO_Object->name, $this->filter) ) $found = true;
				if( !empty($EMIO_Object->address) && stristr($EMIO_Object->address, $this->filter) ) $found = true;
				if( !empty($EMIO_Object->content) && stristr($EMIO_Object->content, $this->filter) ) $found = true;
			}
			if( empty($found) ){
				$EMIO_Object->skip = true;
				return false; //skip loop if no text matches
			}
		}
		//Dates Filter - we can determine the event time if this is importing events, and use just the time to filter further before doing anything else
		if( $this->scope == 'events' || $this->scope == 'all' || $this->scope == 'events+locations' ){
			if( $this->filter_scope ){
				if( !$this->filter_scope($EMIO_Object->start, $EMIO_Object->end) ){
					$EMIO_Object->skip = true;
					return false; //skip loop if not within date range
				}
			}
		}
		//now we check if this item has been imported before, we can still display it if we're in preview mode.
		$item_type = ( $this->scope == 'all' || $this->scope == 'events+locations' || $this->scope == 'events' ) ? 'event':'location';
		//duplicates check
		if( !empty($this->meta['ignore_duplicates']) ){
			if( $item_type == 'event' ){
				//do a simple look up for a duplicate event containing the same event name, start/end date/time
				$EM_DateTime = new EM_DateTime($EMIO_Object->start, 'UTC'); //we'll search the UTC times so we can have the timezone in UTC
				$start = $EM_DateTime->getDateTime();
				$end = $EM_DateTime->setTimestamp($EMIO_Object->end)->getDateTime();
				global $wpdb;
				$subquery = $wpdb->prepare('SELECT post_id FROM '.EMIO_TABLE_SYNC.' WHERE io_id=%d', $this->ID);
				$sql = 'SELECT post_id FROM '.EM_EVENTS_TABLE.' WHERE event_name=%s AND event_start=%s AND event_end=%s AND post_id NOT IN ('.$subquery.')';
				$duplicate = $wpdb->get_var($wpdb->prepare($sql, $EMIO_Object->name, $start, $end));
				if( $duplicate > 0 ){
					$EMIO_Object->post_id = $duplicate;
					$EMIO_Object->skip = true;
					$EMIO_Object->duplicate = true;
				}
			}
		}
		//make a hash of this item
		$EMIO_Object->generate_checksum();
		//get unique ID which is either provided by source or generated from data combination, hash it for faster lookups
		$EMIO_Object->generate_uid();
		//fist step is to see if we've imported this item before, and whether to update or skip the item entirely
		//now, use the uid to check if the item has been imported before and if so, decide how to proceed with the import.
		if( array_key_exists( $EMIO_Object->uid_md5, $this->history[$item_type] ) ){
			//use post id for later loading
			$EMIO_Object->post_id = $this->history[$item_type][$EMIO_Object->uid_md5]['post_id'];
			//compare this checksum with history checksum to determine if anything has changed since last import
			$EMIO_Object->updated = $EMIO_Object->checksum != $this->history[$item_type][$EMIO_Object->uid_md5]['checksum'];
			//check if item is deleted
			$EMIO_Object->deleted = $this->history[$item_type][$EMIO_Object->uid_md5]['deleted'];
			//if the item has been updated, and this import ignores updates, we skip this item at this point
			if( $EMIO_Object->updated && (!empty($this->meta['post_update_status']) && $this->meta['post_update_status'] == 'ignore') ){
				$EMIO_Object->skip = true;
				return false;
			}
			//if this is not a preview (or preview has a number limit filter), we don't count unaltered items towards the limit of imports, nor do we need to parse further as they won't be used in an import
			if( (!$EMIO_Object->updated || $EMIO_Object->deleted)  ){
				$EMIO_Object->skip = true;
				if( !$this->preview_mode || $this->filter_limit > 0 ) return false;
				if( $this->preview_mode ) $EMIO_Object->display = true;
			}
		}
		//whilst we don't skip based on location, we also parse the location info since we got this far
		if( ($this->scope == 'all' || $this->scope == 'events+locations') && !empty($EMIO_Object->location) ){
			//check if location was previously created, and if so decide whether to update it or recreate if deleted.
			//in cases where a location doesn't come with it's own uid any change in location will change the uid and therefore end up being treated as a new location
			if( array_key_exists( $EMIO_Object->location->uid_md5, $this->history['location']) ){
				//use post id for later loading
				$EMIO_Object->location->post_id = $this->history['location'][$EMIO_Object->location->uid_md5]['post_id'];
				//make a hash of this item for comparison with history
				$EMIO_Object->location->updated = $EMIO_Object->location->checksum != $this->history['location'][$EMIO_Object->location->uid_md5]['checksum'];
				//check if item is deleted
				$EMIO_Object->location->deleted = $this->history['location'][$EMIO_Object->location->uid_md5]['deleted'];
			}
		}
		return true;
	}


	/**
	 * Makes best effort decision to take a EMIO_Location object or an EMIO_Event containing a location and parse it using assumptions provided by the format class $options['fuzzy_location'] property, which then updates the $EM_Location object within it.
	 * If an EMIO_Event object is supplied, the object will be updated with the correct EMIO_Location, whether or not the same object is updated or a cached object replaces it.
	 * If an EMIO_Location is supplied, do not assume provided object will reflect updated changes, since a cached value might be used, use the returned value instead.
	 * @param EMIO_Location|EMIO_Event $EMIO_Object data to import which should have a value in the location parameter or individual location-specific parameters (such as address, country etc.), and may additionally contain latitude and longitude coordinates
	 * @return EMIO_Location
	 */
	public function parse_location( $EMIO_Object ){
		if( get_class($EMIO_Object) == 'EMIO_Event' ){
			$EMIO_Location = $EMIO_Object->location;
		}else{
			$EMIO_Location = $EMIO_Object;
		}
		//firstly, if we have to deal with formats that may provide fuzzy locations (e.g. ical gives a string representation with no standards), and if so use provided values to best guess a location
		if( !empty(static::$options['fuzzy_location']) && !$EMIO_Location->parsed ){
			$opt = $this->get_format_option('fuzzy_location');
			//if no location supplied, check if there's a default location and return that
			if( empty($EMIO_Location) && !empty($opt['default']) ){
				//return a default location
				$EMIO_Location->id = $opt['default'];
			}else{
				//check parsed locations against this data, in case we parsed the same thing before
				$md5_location = md5(serialize($EMIO_Location));
				if( !empty($this->parsed_locations[$md5_location]) ){
					//We don't need to repopulate location in this case, just move on with cached object
					$EMIO_Location = $this->parsed_locations[$md5_location];
					$cached_location = true;
					//in this case we reassign the $EMIO_Location to the EMIO_Event, if supplied so that object reference remains in intact outside this function.
					if( get_class($EMIO_Object) == 'EMIO_Event' ){
						$EMIO_Object->location = $EMIO_Location;
					}
				}else{
					//if a placeholder format is supplied and we're dealing with a general location string, try to use this and split the location name from the address
					if( !empty($EMIO_Location->location) && !empty($opt['placeholder_format']) ){
						//escape string from regex expression characters
						$ph_regex = preg_quote('/'.$opt['placeholder_format'].'/');
						//figure out if NAME comes before or after ADDRESS for regex
						$ph_name_pos = strpos($ph_regex, 'NAME');
						$ph_address_pos = strpos($ph_regex, 'ADDRESS');
						if($ph_name_pos < $ph_address_pos){
							$ph_name_pos = 1;
							$ph_address_pos = 2;
						}else{
							$ph_name_pos = 1;
							$ph_address_pos = 2;
						}
						//replace placeholders with regex expressions
						$ph_regex = str_replace('NAME\+', '(.+)', $ph_regex);
						$ph_regex = str_replace('NAME', '(.+?)', $ph_regex);
						$ph_regex = str_replace('ADDRESS', '(.+)', $ph_regex);
						//run the regex and grab the info
						if( preg_match($ph_regex, $EMIO_Location->location, $ph_location_data) ){
							//we have a location name, so we'll make it the name and the rest the address
							$EMIO_Location->name = $EMIO_Location->name = $ph_location_data[$ph_name_pos];
							$EMIO_Location->location = $ph_location_data[$ph_address_pos];
						}
					}
					//if not letting google do all the guessing, split up the address further as per the filtering settings
					if( (int) $opt['google_api'] !== 1 && $opt['delimiter'] ){
						$allowed_data = array('name', 'address', 'town', 'state', 'region', 'postcode', 'country');
						if( !empty($EMIO_Location->location) ) {
							//location string is supplied, so we split it up into an array
							$location_data_raw = explode($opt['delimiter'], $EMIO_Location->location);
							foreach( $location_data_raw as $k => $v ) $location_data_raw[$k] = trim($v);
							//map array to format
							if( !empty($opt['format']) ){
								reset($location_data_raw);
								foreach( $opt['format'] as $format_item ){
									if( !current($location_data_raw) || !in_array($format_item, $allowed_data) ) break;
									$EMIO_Location->$format_item = current($location_data_raw);
									next($location_data_raw);
								}
							}
						}
					}
					//get allowed keys from $location_data and put into $EMIO_Location for further processing
					//foreach($EMIO_Location as $k => $v) if( in_array($k, $allowed_data) ) $EMIO_Location->$k = $v;
					//check if a country was provided and is valid
					if( !empty($EMIO_Location->country) ){
						$countries = em_get_countries(false);
						if( !array_key_exists($EMIO_Location->country, $countries) ){
							//try to correct country, in case it's supplied as text representation
							if( in_array($EMIO_Location->country, $countries) ){
								$countries = array_flip($countries);
								$EMIO_Location->country = $countries[$EMIO_Location->country];
							}else{
								//remove country as it's not valid
								unset($EMIO_Location->country);
							}
						}
					}
					//revert to default country if allowed
					if( empty($EMIO_Location->country) && !empty($opt['country']) ){
						$EMIO_Location->country = $opt['country'];
					}
					//finally, take all this and use Google API to make it better if possible
					if( $opt['google_api'] ){
						//if coordinates supplied, use that to reverse geocode and get valid coordinates
						$api_key = EMIO_Options::get('google_server_key');
						if( empty($EMIO_Location->location) && !empty($EMIO_Location->latitude) && !empty($EMIO_Location->longitude) ){
							$api_address = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=%s&key=%s&result_type=street_address';
							$latlng = $EMIO_Location->latitude.','.$EMIO_Location->longitude;
							$api_address = sprintf($api_address, $latlng, $api_key);
							$response = wp_remote_get($api_address);
						}else{
							//get the address search string to search for
							if( empty($EMIO_Location->location) || (int) $opt['google_api'] === 2 ){
								//either location string (fuzzy representation) is not available, or we're just improving the address already provided, build a street address from current data set
								$search_address = array();
								//create location address variable based on current location info, omit duplicate values to increase Google's accuracy.
								//Example... "Placa Catalunya, Placa Catalunya, Barcelona, Spain" is inaccurate due to dupe value.
								$allowed_data = array('address', 'town', 'state', 'region', 'postcode', 'country');
								foreach( $allowed_data as $k ) if( !empty($EMIO_Location->$k) && !in_array($EMIO_Location->$k, $search_address) ) $search_address[] = $EMIO_Location->$k;
								$search_address = implode(', ', $search_address);
							}
							if( empty($search_address) ){
								//we're using the generic location string provided for searching
								$search_address = $EMIO_Location->location;
							}
							//We've decided to use only the Google Places API Web Service because it allows 150,000
							$api_address = 'https://maps.googleapis.com/maps/api/place/textsearch/json?query=%s&key=%s';
							$api_address = sprintf($api_address, urlencode($search_address), $api_key);
							$response = wp_remote_get($api_address);
							if( !is_wp_error($response) ){
								$places_response = json_decode($response['body']);
								if( $places_response->status == 'OK' ){
									$result = current($places_response->results);
									$place_id = $result->place_id;
									$api_address = 'https://maps.googleapis.com/maps/api/place/details/json?placeid=%s&key=%s';
									$api_address = sprintf($api_address, urlencode($place_id), $api_key);
									$response = wp_remote_get($api_address);
								}elseif( $places_response->status == 'OVER_QUERY_LIMIT' ){
									//fallback if over the limit, we can try geocoding because it allows 1500 more requests per day - https://developers.google.com/maps/pricing-and-plans/#details
									//no coordinates, so we use geocoding to obtain location info and prefill address more fully
									$api_address = 'https://maps.googleapis.com/maps/api/geocode/json?address=%s&key=%s&result_type=street_address';
									$api_address = sprintf($api_address, urlencode($search_address), $api_key);
									$response = wp_remote_get($api_address);
								}
							}
						}
						if( empty($response) || !is_wp_error($response) ){
							$response = json_decode($response['body']);
							if( $response->status == 'OK' ){
								$result = !empty($response->result) ? $response->result : current($response->results);
								//add geometry values
								$EMIO_Location->latitude = $result->geometry->location->lat;
								$EMIO_Location->longitude = $result->geometry->location->lng;
								//if there's a name, and we don't have one already, use it
								if( empty($EMIO_Location->name) && !empty($result->name) ){
									$EMIO_Location->name = $result->name;
								}
								//correct the address
								if( empty($EMIO_Location->address) ){
									$location_address = array();
									foreach( $result->address_components as $add ){
										if( in_array('street_number', $add->types) ){
											//for street numbers, we want a street number in originally supplied address or just ignore it, otherwise we're getting likely an inaccurrate or irrelevant number
											if( (int) $opt['google_api'] === 2 ){
												if( !empty($EMIO_Location->address) && stristr($EMIO_Location->address, $add->long_name) || !empty($EMIO_Location->name) && stristr($EMIO_Location->name, $add->long_name) ){
													$location_address[1] = $add->long_name;
												}
											}else{
												$location_address[1] = $add->long_name;
											}
										}elseif( in_array('route', $add->types) ){
											$location_address[0] = $add->long_name;
										}
									}
									ksort($location_address);
									$location_address = implode(' ', $location_address);
									if( !empty($location_address) ) $EMIO_Location->address = $location_address;
								}
								//correct the town
								if( empty($EMIO_Location->town) ){
									foreach( $result->address_components as $add ){
										if( in_array('postal_town', $add->types) ){
											$EMIO_Location->town = $add->long_name;
											break;
										}elseif( in_array('locality', $add->types) ){
											$EMIO_Location->town = $add->long_name;
											break;
										}
									}
								}
								//correct the state
								if( empty($EMIO_Location->state) ){
									foreach( $result->address_components as $add ){
										if( in_array('administrative_area_level_1', $add->types) ) $EMIO_Location->state = $add->long_name;
									}
								}
								//correct the postcode
								if( empty($EMIO_Location->postcode) ){
									foreach( $result->address_components as $add ){
										if( in_array('postal_code', $add->types) ) $EMIO_Location->postcode = $add->long_name;
									}
								}
								//correct the country
								if( empty($EMIO_Location->country) ){
									foreach( $result->address_components as $add ){
										if( in_array('country', $add->types) ) $EMIO_Location->country = $add->short_name;
									}
								}
							}
						}
					}
					//finally, a little nuance trick... check the address line and if it matches other parts of the location, make the name the location address or vice versa
					if( empty($EMIO_Location->name) && !empty($EMIO_Location->address) ){
						$EMIO_Location->name = $EMIO_Location->address;
					}elseif( empty($EMIO_Location->address) && !empty($EMIO_Location->name) ){
						$EMIO_Location->address = $EMIO_Location->name;
					}
					$this->parsed_locations[$md5_location] = $EMIO_Location;
					$EMIO_Location->parsed = true;
				}
			}
		}
		// repopulate EM_Location object if not a cached object
		if( empty($cached_location) ){
			$EMIO_Location->populate_location();
		}
		//assign some user-defined values relative to these import options
		$EM_Location = $EMIO_Location->object;
		if( !empty($EM_Location->post_id) ){
			$EM_Location->post_status = $EM_Location->force_status = $this->meta['post_update_status'] == 'same' ? $EM_Location->post_status : $this->meta['post_update_status'];
		}else{
			$EM_Location->post_status = $EM_Location->force_status = $this->meta['post_status'];
		}
		if( empty($EM_Location->location_owner) ) $EM_Location->location_owner = $EM_Location->post_author = $this->user_id;
		return $EMIO_Location;
	}
	
	public function guess_timezone($lat, $lng, $timestamp, $city = '', $country = ''){
		$format_options = $this->get_format_option('fuzzy_location');
		$api_key = EMIO_Options::get('google_server_key');
		$timezone = false;
		if( $city & $country ){
			if( !empty($this->guessed_timezones[$city.'/'.$country]) ) return $this->guessed_timezones[$city.'/'.$country];
		}
		if( isset($this->guessed_timezones[$lat.','.$lng]) ) return $this->guessed_timezones[$lat.','.$lng];
		if( !empty($format_options['google_api']) && $api_key ){
			//try getting timezone from Google API
			$api_address = 'https://maps.googleapis.com/maps/api/timezone/json?location=%s,%s&timestamp=%d&key=%s';
			$api_address = sprintf($api_address, $lat, $lng, $timestamp, $api_key);
			$response = wp_remote_get($api_address);
			if( empty($response) || !is_wp_error($response) ){
				$response = json_decode($response['body']);
				if( $response->status == 'OK' && !empty($response->timeZoneId) ){
					//we have a valid response, so try and create timezone from this response and make sure it matches with local time of event
					$EM_DateTimeZone = EM_DateTimeZone::create($response->timeZoneId);
					if( $EM_DateTimeZone ){
						$timezone = $EM_DateTimeZone->getName();
						if( $city && $country ){
							$this->guessed_timezones[$city.'/'.$country] = $timezone;
						}else{
							$this->guessed_timezones[$lat.','.$lng] = $timezone;
						}
					}else{
						$this->guessed_timezones[$lat.','.$lng] = false;
					}
				}
			}
		}
		return $timezone;
	}
	
	/**
	 * Returns an array of EMIO_Object child classes based on the import and mapping settings.
	 * @param array $unmapped_items
	 * @return array
	 */
	public function get_mapped_fields( $unmapped_items ){
		$fields_map = !empty($this->meta['field_mapping']) ? $this->meta['field_mapping'] : static::$field_mapping_default;
		$items = array();
		foreach( $unmapped_items as $unmapped_item ){
			$mapped_item = $item = array();
			foreach( $fields_map as $k => $v ){
				if( !empty($unmapped_item[$k]) ){
					//break up the mapped key into an array that'll map to the right associative array we'll expect in EMIO_Import::parse()
					$item_keys = explode('/', $v);
					if( empty($mapped_item[$item_keys[0]]) ) $mapped_item[$item_keys[0]] = array();
					switch( count($item_keys) ){
						case 1:
							$mapped_item[$item_keys[0]] = $unmapped_item[$k];
							break;
						case 2:
							if( $item_keys[1] == 'meta'){
								//convert meta to associative array, it could be passed as an array, object, serialized or JSON
								$meta = maybe_unserialize( $unmapped_item[$k] );
								if( !is_array($meta)  ){
									if( is_object($meta) ) $meta = (array) $meta;
									if( is_string($meta) ) $meta = json_decode($meta, true);
								}
								if( is_array($meta) && !empty($meta) ){
									if( !empty($mapped_item[$item_keys[0]]['meta']) ){
										//merge in meta array, overwriting anything else
										$mapped_item[$item_keys[0]]['meta'] = array_merge($mapped_item[$item_keys[0]]['meta'], $meta);
									}else{
										//create a new array of meta since none exists
										$mapped_item[$item_keys[0]]['meta'] = $meta;
									}
								}
							}elseif( $item_keys[1] == 'event_location' && is_string($unmapped_item[$k]) ){
								// event_location must be a json-encoded item
								$mapped_item[$item_keys[0]][$item_keys[1]] = json_decode($unmapped_item[$k], true);
							}else{
								$mapped_item[$item_keys[0]][$item_keys[1]] = $unmapped_item[$k];
							}
							break;
						case 3:
							if( $item_keys[1] == 'meta' && empty($mapped_item[$item_keys[0]]['meta']) ) $mapped_item[$item_keys[0]]['meta'] = array(); //if this is meta and meta array not defined
							if( $item_keys[1] == 'meta' && $item_keys[2] == 'custom_field' ){
								//convert meta to associative array, it could be passed as an array, object, serialized or JSON
								$mapped_item[$item_keys[0]]['meta'][$k] = $unmapped_item[$k];
							}else{
								$mapped_item[$item_keys[0]][$item_keys[1]][$item_keys[2]]= $unmapped_item[$k];
							}
					}
				}
			}
			if( $this->scope == 'events' || $this->scope == 'all' || $this->scope == 'events+locations' ){
				$item = $mapped_item['event'];
				if( $this->scope == 'all' || $this->scope == 'events+locations' ) $item['location'] = $mapped_item['location'];
				//if times aren't set, check the extras section
				foreach( array('start', 'end') as $w ){
					if( empty($item[$w]) && !empty($item[$w.'_date']) ){
						$event_date_time = $item[$w.'_date'];
						unset($item[$w.'_date']);
						if( !empty($item[$w.'_time']) ){
							$event_date_time .= ' '.$item[$w.'_time'];
							unset($item[$w.'_time']);
						}
						$timezone = empty($item['timezone']) ? null : $item['timezone'];
						$datetime = new EM_DateTime($event_date_time, $timezone);
						$item[$w] = $datetime->getTimestamp();
					}elseif( isset($item[$w]) && !is_numeric($item[$w]) ){
						//if supplied a full datetime we assume it UTC time
						$datetime = new EM_DateTime($item[$w], 'UTC');
						$item[$w] = $datetime->getTimestamp();
					}
				}
				$EMIO_Object = new EMIO_Event($item);
			}elseif( $this->scope == 'locations' ){
				$EMIO_Object = new EMIO_Location($mapped_item['location']);
			}else{
				$EMIO_Object = new EMIO_Object();
			}
			$items[] = $EMIO_Object;
		}
		return $items;
	}
	
	public function load_import_history(){
		global $wpdb;
		if( !empty($this->history) ) return; //if previously loaded return early
		//get array of items to check previous imports
		$results = $wpdb->get_results( $wpdb->prepare('SELECT post_id, LOWER(HEX(uid_md5)) as uid_md5, LOWER(HEX(checksum)) as checksum, type, post_status, ID FROM '.EMIO_TABLE_SYNC.' LEFT JOIN '.$wpdb->posts.' ON post_id=ID WHERE io_id=%d', $this->ID) );
		$this->history = array('event' => array(), 'location' => array());
		foreach( $results as $res ){
			if( empty($this->history[$res->type]) ) $this->history[$res->type] = array();
			$this->history[$res->type][$res->uid_md5] = array('post_id'=>$res->post_id, 'checksum'=>$res->checksum);
			//an or location is deleted if it does not exist anymore, or may be in the trash
			$this->history[$res->type][$res->uid_md5]['deleted'] = empty($res->ID) || $res->post_status == 'trash';
		} unset($results);
	}
	
	public function flush_import_history(){
		unset($this->history);
	}
	
	/**
	 * Get the source filepath if it exists, returns false if not. If this is a URL it'll download the file, save it like a normal file and return the newly saved path.
	 * @return string|boolean
	 */
	public function get_source_filepath(){
		if( !empty($this->meta['source']) && file_exists($this->meta['source']) ){
			return $this->meta['source'];
		}elseif( !empty($this->meta['temp_file']) && file_exists($this->meta['temp_file']) ){
			return $this->meta['temp_file'];
		}
		return false;
	}
	
	/**
	 * Returns a file path for this source. For both file and URL source types, a temporary file is kept until the item is imported.
	 * If no temporary file exists, this function will return false, or a WP Error if there is a problem retrieving URL.
	 *
	 * @param bool $fetch If true and source is a file or URL then the destination data will be fetched, otherwise just a filepath or URL will be returned.
	 * @return WP_Error|string
	 */
	public function get_source( $fetch = true ){
		if( $this->source == 'file' ){
			$filename = $this->get_source_filepath();
			if( !$filename ){
				return new WP_Error('invalid-file', __('Temporary source file does not exist.', 'events-manager-io'));
			}
			if( !$fetch ) return $filename;
			//read file into string and return that
			return file_get_contents($filename);
		}elseif( $this->source == 'url' ){
			if( empty($this->meta['source']) || !wp_http_validate_url($this->meta['source']) ){
				return new WP_Error('invalid-url', __('Invalid source url provided.', 'events-manager-io'));
			}
			if( !$fetch ) return $this->meta['source'];
			//now return previously cached file or otherwise get data from url directory and save cache file
			$filename = $this->get_source_filepath();
			if( !empty($filename) && file_exists($filename) ){
				//we already have a saved cache file, so let's use that one
				return file_get_contents($filename);
			}else{
				//generate a temporary file by getting URL contents and provide the data saved to the temporary file
				$args = apply_filters('emio_import_wp_remote_get_args', array('timeout'=>10));
				$response = wp_remote_get( esc_url_raw($this->meta['source']), $args);
				//return response or WP_Error
				if( is_wp_error($response) ){
					return $response;
				}else{
					$filename = 'emio-tmp-'.sha1(wp_salt().microtime()) .'.'. static::$ext;
					if( file_put_contents($filename, $response['body']) ){
						global $wpdb;
						$this->meta['temp_file'] = $filename;
						$wpdb->update(EMIO_TABLE, array('meta' => maybe_serialize($this->meta)), array('ID' => $this->ID), '%s', '%d');
					}
					return $response['body'];
				}
			}
		}elseif( !empty($this->meta['source']) ){
			//return whatever the source is, e.g. an ID, a username or anything else relevant to custom format
			return $this->meta['source'];
		}
		return '';
	}
	
	/**
	 * Deletes temporary files associated with the source of this item and updates the database record to reflect this.
	 * @param bool $update_db
	 * @return boolean
	 */
	public function flush_source( $update_db = true ){
		global $wpdb;
		$filename = $this->get_source_filepath();
		if( $filename && file_exists($filename) ){
			if( !empty($this->meta['temp_file']) ){
				unset($this->meta['temp_file']);
				if( $update_db ){
					$wpdb->update(EMIO_TABLE, array('meta' => maybe_serialize($this->meta)), array('ID' => $this->ID), '%s', '%d');
				}
			}
			return unlink($filename);
		}
		//at this point, no file exists so just delete
		return true;
	}
	
	/**
	 * @param EMIO_Object $EMIO_Object
	 * @param EM_Event|EM_Location $EM_Object
	 * @return bool
	 */
	public function save_history($EMIO_Object, $EM_Object) {
		//save import to history table
		$io_history = array(
			'uid_md5' => $EMIO_Object->uid_md5,
			'uid' => $EMIO_Object->uid,
			'checksum' => $EMIO_Object->checksum,
			'io_id' => $this->ID,
			'post_id' => absint($EM_Object->post_id),
		);
		if( empty($EMIO_Object->errors) ){
			$io_history['action'] = $EMIO_Object->updated ? 'update' : 'create';
		}else{
			$io_history['action'] = 'error';
			$io_history['error'] = implode(', '."\r\n", $EMIO_Object->errors);
		}
		$io_history['url'] = $EMIO_Object->external_url;
		return parent::save_history($io_history, $EM_Object);
	}
}