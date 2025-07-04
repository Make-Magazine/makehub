<?php

/**
 * Class EM_Ticket
 * @param string name
 * @param string description
 * @param string ticket_name
 * @param string ticket_description
 * @param string ticket_start
 * @param string ticket_end
 *
 * @property EM_Event $event
 */
class EM_Ticket extends EM_Object{
	//DB Fields
	var $ticket_id;
	var $event_id;
	protected $ticket_name;
	protected $ticket_description;
	var $ticket_price;
	protected $ticket_start;
	protected $ticket_end;
	var $ticket_min;
	var $ticket_max;
	var $ticket_spaces = 10;
	var $ticket_members = false;
	var $ticket_members_roles = array();
	var $ticket_guests = false;
	var $ticket_required = false;
	var $ticket_parent;
	var $ticket_meta = array();
	var $ticket_order;
	var $fields = array(
		'ticket_id' => array('name'=>'id','type'=>'%d'),
		'event_id' => array('name'=>'event_id','type'=>'%d'),
		'ticket_name' => array('name'=>'name','type'=>'%s'),
		'ticket_description' => array('name'=>'description','type'=>'%s','null'=>1),
		'ticket_price' => array('name'=>'price','type'=>'%f','null'=>1),
		'ticket_start' => array('type'=>'%s','null'=>1),
		'ticket_end' => array('type'=>'%s','null'=>1),
		'ticket_min' => array('name'=>'min','type'=>'%s','null'=>1),
		'ticket_max' => array('name'=>'max','type'=>'%s','null'=>1),
		'ticket_spaces' => array('name'=>'spaces','type'=>'%s','null'=>1),
		'ticket_members' => array('name'=>'members','type'=>'%d','null'=>1),
		'ticket_members_roles' => array('name'=>'ticket_members_roles','type'=>'%s','null'=>1),
		'ticket_guests' => array('name'=>'guests','type'=>'%d','null'=>1),
		'ticket_required' => array('name'=>'required','type'=>'%d','null'=>1),
		'ticket_parent' => array('type'=>'%d','null'=>1),
		'ticket_meta' => array('name'=>'ticket_meta','type'=>'%s','null'=>1),
		'ticket_order' => array('type'=>'%d','null'=>1),
	);
	// array map of $fields mapping name to key
	public static $field_shortcuts = array(
		'id' => 'ticket_id',
		'event_id' => 'event_id',
		'name' => 'ticket_name',
		'description' => 'ticket_description',
		'price' => 'ticket_price',
		'start' => 'ticket_start',
		'end' => 'ticket_end',
		'min' => 'ticket_min',
		'max' => 'ticket_max',
		'spaces' => 'ticket_spaces',
		'members' => 'ticket_members',
		'members_roles' => 'ticket_members_roles',
		'guests' => 'ticket_guests',
		'required' => 'ticket_required',
		'parent' => 'ticket_parent',
		'meta' => 'ticket_meta',
		'order' => 'ticket_order',
	);
	//Other Vars
	/**
	 * Contains only bookings belonging to this ticket.
	 * @var EM_Booking
	 */
	var $bookings = array();
	var $required_fields = array('ticket_name');
	protected $start;
	protected $end;
	/**
	 * is this ticket limited by spaces allotted to this ticket? false if no limit (i.e. the events general limit of seats)
	 * @var bool
	 */
	var $spaces_limit = true;
	
	/**
	 * An associative array containing event IDs as the keys and pending spaces as values.
	 * This is in array form for future-proofing since at one point tickets could be used for multiple events.
	 * @var array
	 */
	protected $pending_spaces = array();
	protected $booked_spaces = array();
	protected $bookings_count = array();
	
	/**
	 * @var EM_Event
	 */
	protected $event;
	/**
	 * @var bool flag whether ticket is available, saved for persistence
	 */
	protected $is_available;
	
	/**
	 * Creates ticket object and retreives ticket data (default is a blank ticket object). Accepts either array of ticket data (from db) or a ticket id.
	 * @param mixed $ticket_data
	 * @return null
	 */
	function __construct( $ticket_data = false ){
		$this->ticket_name = __('Standard Ticket','events-manager');
		$ticket = array();
		if( $ticket_data !== false ){
			//Load ticket data
			if( is_array($ticket_data) ){
				$ticket = $ticket_data;
			}elseif( is_numeric($ticket_data) ){
				//Retreiving from the database		
				global $wpdb;
				$sql = "SELECT * FROM ". EM_TICKETS_TABLE ." WHERE ticket_id ='$ticket_data'";   
			  	$ticket = $wpdb->get_row($sql, ARRAY_A);
			}
			//Save into the object
			$this->to_object($ticket);
			//serialized arrays
			$this->ticket_meta = (!empty($ticket['ticket_meta'])) ? maybe_unserialize($ticket['ticket_meta']):array();
			$this->ticket_members_roles = maybe_unserialize($this->ticket_members_roles);
			if( !is_array($this->ticket_members_roles) ) $this->ticket_members_roles = array();
			//sort out recurrence meta to save extra empty() checks, the 'true' cut-off info is here for the ticket if part of a recurring event
			if( !empty($this->ticket_meta['recurrences']) ){
				if( !array_key_exists('start_days', $this->ticket_meta['recurrences']) ) $this->ticket_meta['recurrences']['start_days'] = false;
				if( !array_key_exists('end_days', $this->ticket_meta['recurrences']) ) $this->ticket_meta['recurrences']['end_days'] = false;
				if( !array_key_exists('start_time', $this->ticket_meta['recurrences']) ) $this->ticket_meta['recurrences']['start_time'] = false;
				if( !array_key_exists('end_time', $this->ticket_meta['recurrences']) ) $this->ticket_meta['recurrences']['end_time'] = false;
				//if we have start and end times, we'll set the ticket start/end properties
				if( !empty($this->ticket_meta['recurrences']['start_time']) ){
					$this->ticket_start = date('Y-m-d ') . $this->ticket_meta['recurrences']['start_time'];
				}
				if( !empty($this->ticket_meta['recurrences']['end_time']) ){
					$this->ticket_end = date('Y-m-d ') . $this->ticket_meta['recurrences']['end_time'];
				}
			}
		}
		$this->compat_keys();
		if( empty($this->ticket_price) ) $this->ticket_price = 0;
		do_action('em_ticket',$this, $ticket_data, $ticket);
	}

	
	function __get( $var ){
		if( $var == 'name' || $var == 'ticket_name' || $var == 'description' || $var == 'ticket_description' ){
			$prop = $var == 'name' || $var == 'description' ? 'ticket_'.$var : $var;
			if( EM_ML::$is_ml && !$this->ticket_parent && !empty($this->ticket_meta['langs'][EM_ML::$current_language][$prop]) ){
				return $this->ticket_meta['langs'][EM_ML::$current_language][$prop];
			}else{
				return $this->{$prop};
			}
		}elseif( $var == 'event' ){
			return $this->get_event();
		}elseif( $var == 'ticket_start' || $var == 'ticket_end' ){
	    	return $this->$var;
	    }
	   //these are deprecated properties, use the start() and end() functions directly instead
	    elseif( $var == 'start_timestamp' || $var == 'start' ){
	    	if( !$this->start()->valid ) return 0;
	    	return $this->start()->getTimestampWithOffset();
	    }elseif( $var == 'end_timestamp' || $var == 'end' ){
	    	if( !$this->end()->valid ) return 0;
	    	return $this->end()->getTimestampWithOffset();
	    }
	    return parent::__get( $var );
	}
	
	public function __set( $prop, $val ){
		if( $prop == 'name' || $prop == 'description' ){
			$prop = 'ticket_'.$prop;
		}elseif( $prop == 'event' && $val instanceof EM_Event ){
			$this->event = $val;
			$this->event_id = $this->event->event_id;
		}
		elseif( $prop == 'ticket_start' ){
			$this->{$prop} = $val;
			$this->start = false;
		}elseif( $prop == 'ticket_end' ){
			$this->{$prop} = $val;
			$this->end = false;
		}
		//These are deprecated and should not be used. Either use the class start() or end() equivalent methods
		elseif( $prop == 'start_timestamp' ){
	    	if( $this->start() !== false ) $this->start()->setTimestamp($val);
		}elseif( $prop == 'end_timestamp' ){
	    	if( $this->end() !== false ) $this->end()->setTimestamp($val);
		}elseif( $prop == 'start' || $prop == 'end' ){
			//start and end properties are inefficient to set, and deprecated. Set ticket_start and ticket_end with a valid MySQL DATETIME value instead.
			$EM_DateTime = new EM_DateTime( $val, $this->get_event()->get_timezone() );
			if( !$EM_DateTime->valid ) return false;
			$when_prop = 'ticket_'.$prop;
			$this->{$when_prop} = $EM_DateTime->getDateTime();
		}elseif( $prop == 'is_available' && $val === null ) {
			// reset available value so it forces a refresh
			$this->is_available = null;
		}
		parent::__set( $prop, $val );
	}
	
	public function __isset( $prop ){
		//start_timestamp and end_timestamp are deprecated, don't use them anymore
		if( $prop == 'ticket_start' || $prop == 'start_timestamp' ){
			return !empty($this->ticket_start);
		}elseif( $prop == 'ticket_end' || $prop == 'end_timestamp' ){
			return !empty($this->ticket_end);
		}
		if( $prop == 'name' || $prop == 'ticket_name' || $prop == 'description' || $prop == 'ticket_description'  || $prop == 'event' ){
			$prop = $prop == 'name' || $prop == 'description' ? 'ticket_'.$prop : $prop;
			return !empty($this->{$prop});
		}
		return parent::__isset( $prop );
	}
	
	function get_notes(){
		global $wpdb;
		if( !is_array($this->notes) && !empty($this->ticket_id) ){
		  	$notes = $wpdb->get_results("SELECT * FROM ". EM_META_TABLE ." WHERE meta_key='ticket-note' AND object_id ='{$this->ticket_id}'", ARRAY_A);
		  	foreach($notes as $note){
		  		$this->ticket_id[] = unserialize($note['meta_value']);
		  	}
		}elseif( empty($this->booking_id) ){
			$this->notes = array();
		}
		return $this->notes;
	}
	
	/**
	 * Saves the ticket into the database, whether a new or existing ticket
	 * @return boolean
	 */
	function save(){
		global $wpdb;
		$table = EM_TICKETS_TABLE;
		do_action('em_ticket_save_pre',$this);
		//First the person
		if($this->validate() && $this->can_manage() ){			
			//Now we save the ticket
			$data = $this->to_array(true); //add the true to remove the nulls
			if( !empty($data['ticket_meta']) ) $data['ticket_meta'] = serialize($data['ticket_meta']);
			if( !empty($data['ticket_members_roles']) ) $data['ticket_members_roles'] = serialize($data['ticket_members_roles']);
			if( !empty($this->ticket_meta['recurrences']) ){
				$data['ticket_start'] = $data['ticket_end'] = null;
			}
			if($this->ticket_id != ''){
				//since currently wpdb calls don't accept null, let's build the sql ourselves.
				$set_array = array();
				foreach( $this->fields as $field_name => $field ){
					if( empty( $data[$field_name]) && $field['null'] ){
						$set_array[] = "{$field_name}=NULL";
					}else{
						$set_array[] = "{$field_name}='".esc_sql($data[$field_name])."'";						
					}
				}
				$sql = "UPDATE $table SET ".implode(', ', $set_array)." WHERE ticket_id={$this->ticket_id}";
				$result = $wpdb->query($sql);
				$this->feedback_message = __('Changes saved','events-manager');
			}else{
				if( isset($data['ticket_id']) && empty($data['ticket_id']) ) unset($data['ticket_id']);
				$result = $wpdb->insert($table, $data, $this->get_types($data));
			    $this->ticket_id = $wpdb->insert_id;
				$this->feedback_message = __('Ticket created','events-manager'); 
			}
			if( $result === false ){
				$this->feedback_message = __('There was a problem saving the ticket.', 'events-manager');
				$this->errors[] = __('There was a problem saving the ticket.', 'events-manager');
			}
			$this->compat_keys();
			return apply_filters('em_ticket_save', ( count($this->errors) == 0 ), $this);
		}else{
			$this->feedback_message = __('There was a problem saving the ticket.', 'events-manager');
			$this->errors[] = __('There was a problem saving the ticket.', 'events-manager');
			return apply_filters('em_ticket_save', false, $this);
		}
		return true;
	}
	
	/**
	 * Get posted data and save it into the object (not db)
	 * @return boolean
	 */
	function get_post($post = array()){
		//We are getting the values via POST or GET
		global $allowedposttags;
		if( empty($post) ){
		    $post = $_REQUEST;
		}
		do_action('em_ticket_get_post_pre', $this, $post);
		$this->ticket_id = ( !empty($post['ticket_id']) && is_numeric($post['ticket_id']) ) ? absint($post['ticket_id']):'';
		$this->event_id = ( !empty($post['event_id']) && is_numeric($post['event_id']) ) ? absint($post['event_id']):'';
		$this->ticket_name = ( !empty($post['ticket_name']) ) ? wp_kses_data(wp_unslash($post['ticket_name'])):'';
		$this->ticket_description = ( !empty($post['ticket_description']) ) ? wp_kses(wp_unslash($post['ticket_description']), $allowedposttags):'';
		//spaces and limits
		$this->ticket_min = ( !empty($post['ticket_min']) && is_numeric($post['ticket_min']) ) ? absint($post['ticket_min']):'';
		$this->ticket_max = ( !empty($post['ticket_max']) && is_numeric($post['ticket_max']) ) ? absint($post['ticket_max']):'';
		$this->ticket_spaces = ( !empty($post['ticket_spaces']) && is_numeric($post['ticket_spaces']) ) ? absint($post['ticket_spaces']):10;
		//sort out price and un-format in the event of special decimal/thousand seperators
		$price = ( !empty($post['ticket_price']) ) ? wp_kses_data($post['ticket_price']):'';
		if( preg_match('/^[0-9]*\.[0-9]+$/', $price) || preg_match('/^[0-9]+$/', $price) ){
			$this->ticket_price = $price;
		}else{
			$this->ticket_price = str_replace( array( get_option('dbem_bookings_currency_thousands_sep'), get_option('dbem_bookings_currency_decimal_point') ), array('','.'), $price );
		}
		//Sort out date/time limits
		$this->ticket_start = ( !empty($post['ticket_start']) ) ? wp_kses_data($post['ticket_start']):'';
		$this->ticket_end = ( !empty($post['ticket_end']) ) ? wp_kses_data($post['ticket_end']):'';
		$start_time = !empty($post['ticket_start_time']) ? $post['ticket_start_time'] : $this->get_event()->start()->format('H:i');
		if( !empty($this->ticket_start) ) $this->ticket_start .= ' '. $this->sanitize_time($start_time);
		$end_time = !empty($post['ticket_end_time']) ? $post['ticket_end_time'] : $this->get_event()->start()->format('H:i');
		if( !empty($this->ticket_end) ) $this->ticket_end .= ' '. $this->sanitize_time($end_time);
		$this->start = $this->end = false; // reset start/end objects
		//sort out user availability restrictions
		$this->ticket_members = ( !empty($post['ticket_type']) && $post['ticket_type'] == 'members' ) ? 1:0;
		$this->ticket_guests = ( !empty($post['ticket_type']) && $post['ticket_type'] == 'guests' ) ? 1:0;
		$this->ticket_members_roles = array();
		if( $this->ticket_members && !empty($post['ticket_members_roles']) && is_array($post['ticket_members_roles']) ){
			$WP_Roles = new WP_Roles();
			foreach($WP_Roles->roles as $role => $role_data ){
				if( in_array($role, $post['ticket_members_roles']) ){
					$this->ticket_members_roles[] = $role;
				}
			}
		}
		$this->ticket_required = ( !empty($post['ticket_required']) ) ? 1:0;
		//if event is recurring, store start/end restrictions of this ticket, which are determined by number of days before (negative number) or after (positive number) the event start date
		if($this->get_event()->is_recurring()){
			if( empty($this->ticket_meta['recurrences']) ){
				$this->ticket_meta['recurrences'] = array('start_days'=>false, 'start_time'=>false, 'end_days'=>false, 'end_time'=>false);
			}
			foreach( array('start', 'end') as $start_or_end ){
				//start/end of ticket cut-off
				if( array_key_exists('ticket_'.$start_or_end.'_recurring_days', $post) && is_numeric($post['ticket_'.$start_or_end.'_recurring_days']) ){
					if( !empty($post['ticket_'.$start_or_end.'_recurring_when']) && $post['ticket_'.$start_or_end.'_recurring_when'] == 'after' ){
						$this->ticket_meta['recurrences'][$start_or_end.'_days'] = absint($post['ticket_'.$start_or_end.'_recurring_days']);
					}else{ //by default the start/end date is the point of reference
						$this->ticket_meta['recurrences'][$start_or_end.'_days'] = absint($post['ticket_'.$start_or_end.'_recurring_days']) * -1;
					}
					$this->ticket_meta['recurrences'][$start_or_end.'_time'] = ( !empty($post['ticket_'.$start_or_end.'_time']) ) ? $this->sanitize_time($post['ticket_'.$start_or_end.'_time']) : $this->get_event()->$start_or_end()->format('H:i');
				}else{
					unset($this->ticket_meta['recurrences'][$start_or_end.'_days']);
					unset($this->ticket_meta['recurrences'][$start_or_end.'_time']);
				}
			}
			$this->ticket_start = $this->ticket_end = null;
		}
		$this->compat_keys();
		do_action('em_ticket_get_post', $this, $post);
	}
	

	/**
	 * Validates the ticket for saving. Should be run during any form submission or saving operation.
	 * @return boolean
	 */
	function validate(){
		$missing_fields = Array ();
		$this->errors = array();
		foreach ( $this->required_fields as $field ) {
			if ( $this->$field == "") {
				$missing_fields[] = $field;
			}
		}
		if( !empty($this->ticket_price) && !is_numeric($this->ticket_price) ){
			$this->add_error(esc_html__('Please enter a valid ticket price e.g. 10.50 (no currency signs)','events-manager'));
		}
		if( !empty($this->ticket_min) && !empty($this->ticket_max) && $this->ticket_max < $this->ticket_min ) {
			$error = esc_html__('Ticket %s has a higher minimum spaces requirement than the maximum spaces allowed.','events-manager');
			$this->add_error( sprintf($error, '<em>'. esc_html($this->ticket_name) .'</em>'));
		}
		if ( count($missing_fields) > 0){
			// TODO Create friendly equivelant names for missing fields notice in validation 
			$this->errors[] = __ ( 'Missing fields: ' ) . implode ( ", ", $missing_fields ) . ". ";
		}
		return apply_filters('em_ticket_validate', count($this->errors) == 0, $this );
	}
	
	/**
	 * @param bool $ignore_member_restrictions  Makes a member-restricted ticket available to any user or guest
	 * @param bool $ignore_guest_restrictions   Makes a guest-restricted ticket available to any user or guest
	 * @param bool $ignore_spaces               Ignores space availability
	 * @param false|WP_User $user               The user to check ticket availability against. Accepts a user object or false for a guest. By default current logged in user (or guest) is used.
	 * @return mixed|null
	 */
	function is_available( $ignore_member_restrictions = false, $ignore_guest_restrictions = false, $ignore_spaces = false, $user = null ){
		if( EM_Bookings::$disable_restrictions ) return apply_filters('em_ticket_is_available', true, $this, true, true, true, null); // complete short-circuit, but overriding functions should beware of the $disable_restrictions flag!
		if( isset($this->is_available) && !$ignore_member_restrictions && !$ignore_guest_restrictions && !$ignore_spaces && $user === null ) return apply_filters('em_ticket_is_available',  $this->is_available, $this, false, false, false, null); //save extra queries if doing a standard check
		$is_available = false;
		$EM_Event = $this->get_event();
		if( $EM_Event->get_active_status() !== 0 ){
			if( $user === null ){
				$user = is_user_logged_in() ? wp_get_current_user() : false;
			}
			$available_spaces = $this->get_available_spaces();
			$condition_1 = empty($this->ticket_start) || $this->start()->getTimestamp() <= time();
			$condition_2 = empty($this->ticket_end) || $this->end()->getTimestamp() >= time();
			$condition_3 = $EM_Event->rsvp_end()->getTimestamp() > time(); //either defined ending rsvp time, or start datetime is used here
			$condition_4 = !$this->ticket_members || $user !== false || $ignore_member_restrictions;
			$condition_5 = true;
			if( !$ignore_member_restrictions && $this->ticket_members && !empty($this->ticket_members_roles) ){
				//check if user has the right role to use this ticket
				$condition_5 = false;
				if( $user !== false ){
					if( count(array_intersect($user->roles, $this->ticket_members_roles)) > 0 ){
						$condition_5 = true;
					}
				}
			}
			$condition_6 = !$this->ticket_guests || $user === false || $ignore_guest_restrictions;
			if( $condition_1 && $condition_2 && $condition_3 && $condition_4 && $condition_5 && $condition_6  ){
				//Time Constraints met, now quantities
				if( $available_spaces > 0 && ($available_spaces >= $this->ticket_min || empty($this->ticket_min)) ){
					$is_available = true;
				}elseif( $ignore_spaces === true ) {
					$is_available = true;
				}
			}
		}
		if( !$ignore_member_restrictions && !$ignore_guest_restrictions && !$ignore_spaces ){ //$this->is_available is only stored for the viewing user
			$this->is_available = $is_available;
		}
		return apply_filters('em_ticket_is_available', $is_available, $this, $ignore_guest_restrictions, $ignore_member_restrictions, $ignore_spaces, $user);
	}
	
	/**
	 * Checks if ticket is abailable to a specific user type based on user restrictions, other restrictions like dates, availability etc. is ignored. This can generically be guest (0 or false), registered user (true) or more specifically role (string) or a specific user ID (int).
	 * If this check is for a generic registered user, a check will not be made for role restrictions, only if the ticket is restricted to logged in users.
	 * @param int|WP_User|bool|string $user_type
	 * @return bool
	 * @since 6.1.1.1
	 */
	public function is_available_to( $user_type = 0 ){
		if( $user_type === 0 || $user_type === false ){
			// guest
			return !$this->ticket_members;
		}elseif( $user_type === true ){
			// registered user, all but guest tickets theoretically available
			return !$this->ticket_guests;
		}elseif( is_numeric($user_type) || $user_type instanceof WP_User ){
			// real user id or
			if( $user_type instanceof WP_User ){
				$user = $user_type;
			}else{
				$user = new WP_User($user_type);
			} /*  @var WP_User $user */
			if( $this->ticket_members && !empty($this->ticket_members_roles) ) {
				// return whether roles coincide with current user roles
				return count(array_intersect($user->roles, $this->ticket_members_roles)) > 0;
			}
			// otherwise check if limited to guests, if not it doesn't matter whether it's general user logged-in limit or no limit
			return !$this->ticket_guests;
		}elseif( is_string($user_type) ){
			// user role
			if( $this->ticket_members && !empty($this->ticket_members_roles) ) {
				return in_array($user_type, $this->ticket_members_roles);
			}
			// otherwise check if limited to guests
			return !$this->ticket_guests;
		}
		return false;
	}
	
	/**
	 * Returns whether this ticket should be displayed based on availability and other ticket properties and general settings
	 * @param bool $ignore_member_restrictions
	 * @param bool $ignore_guest_restrictions
	 * @return boolean
	 */
	function is_displayable( $ignore_member_restrictions = false, $ignore_guest_restrictions = false ){
		$return = false;
		if( $this->is_available($ignore_member_restrictions, $ignore_guest_restrictions) ){
			$return = true;
		}else{
			if( get_option('dbem_bookings_tickets_show_unavailable') ){
				$return =  true;
				if( $this->ticket_members && !get_option('dbem_bookings_tickets_show_member_tickets') ){
					$return = false;
				}
			}
		}
		return apply_filters('em_ticket_is_displayable', $return, $this, $ignore_guest_restrictions, $ignore_member_restrictions);
	}
	
	/**
	 * Gets the total price for this ticket, includes tax if settings dictates that tax is added to ticket price. 
	 * Use $this->ticket_price or $this->get_price_without_tax() if you definitely don't want tax included. 
	 * @param boolean $format
	 * @return float
	 */
	function get_price($format = false){
		$price = $this->ticket_price;
		if( get_option('dbem_bookings_tax_auto_add') ){
			$price = $this->get_price_with_tax();
		}
		$price = apply_filters('em_ticket_get_price',$price,$this);
		if($format){
			return $this->format_price($price);
		}
		return $price;
	}
	
	/**
	 * Calculates how much the individual ticket costs with applicable event/site taxes included.
	 * @param boolean $format
	 * @return float|int|string
	 */
	function get_price_with_tax( $format = false ){
	    $price = $this->get_price_without_tax() * (1 + $this->get_event()->get_tax_rate( true ));
	    if( $format ) return $this->format_price($price);
	    return $price; 
	}
	
	/**
	 * Calculates how much the individual ticket costs with taxes excluded.
	 * @param boolean $format
	 * @return float|int|string
	 */
	function get_price_without_tax( $format = false ){
	    if( $format ) return $this->format_price($this->ticket_price);
	    return apply_filters('em_ticket_get_price_without_tax', $this->ticket_price, $this);
	}
	
	/**
	 * Shows the ticket price which can contain long decimals but will show up to 2 decimal places and remove trailing 0s
	 * For example: 10.010230 => 10.01023 and 10 => 10.00
	 * @param bool $format If true, the number is provided with localized digit separator and padded with 0, 2 or 4 digits
	 * @return float|int|string
	 */
	function get_price_precise( $format = false ){
		$price = $this->ticket_price * 1;
		if( floor($price) == (float) $price ) $price = number_format($price, 2, '.', '');
		if( $format ){
			$digits = strlen(substr(strrchr($price, "."), 1));
			$precision = ( $digits > 2 ) ? 4 : 2;
			$price = number_format( $price, $precision, get_option('dbem_bookings_currency_decimal_point','.'), '');
		}
		return $price;
	}
		
	/**
	 * Get the total number of tickets (spaces) available, bearing in mind event-wide maxiumums and ticket priority settings.
	 * @return int
	 */
	function get_spaces(){
		return apply_filters('em_ticket_get_spaces',$this->ticket_spaces,$this);
	}

	/**
	 * Returns the number of available spaces left in this ticket, bearing in mind event-wide restrictions, previous bookings, approvals and other tickets.
	 * @return int
	 */
	function get_available_spaces(){
		$event_available_spaces = $this->get_event()->get_bookings()->get_available_spaces();
		$ticket_available_spaces = $this->get_spaces() - $this->get_booked_spaces();
		if( get_option('dbem_bookings_approval_reserved')){
		    $ticket_available_spaces = $ticket_available_spaces - $this->get_pending_spaces();
		}
		$return = ($ticket_available_spaces <= $event_available_spaces) ? $ticket_available_spaces:$event_available_spaces;
		return apply_filters( 'em_ticket_get_available_spaces', $return, $this, array('event_spaces' => $event_available_spaces, 'ticket_spaces' => $ticket_available_spaces) );
	}
	
	/**
	 * Get total number of pending spaces for this ticket.
	 * @param boolean $force_refresh
	 * @return int
	 */
	function get_pending_spaces( $force_refresh = false ){
		global $wpdb;
		if( !array_key_exists($this->event_id, $this->pending_spaces) || $force_refresh ){
			$sub_sql = 'SELECT booking_id FROM '.EM_BOOKINGS_TABLE." WHERE event_id=%d AND booking_status=0";
			$sql = 'SELECT SUM(ticket_booking_spaces) FROM '.EM_TICKETS_BOOKINGS_TABLE. " WHERE booking_id IN ($sub_sql) AND ticket_id=%d";
			$pending_spaces = $wpdb->get_var($wpdb->prepare($sql, $this->event_id, $this->ticket_id));
			$this->pending_spaces[$this->event_id] = $pending_spaces > 0 ? $pending_spaces : 0;
			$this->pending_spaces[$this->event_id] = apply_filters('em_ticket_get_pending_spaces', $this->pending_spaces[$this->event_id], $this, $force_refresh);  
		}
		return $this->pending_spaces[$this->event_id];
	}

	/**
	 * Returns the number of booked spaces in this ticket.
	 * @param boolean $force_refresh
	 * @return int
	 */
	function get_booked_spaces( $force_refresh = false ){
		global $wpdb;
		if( !array_key_exists($this->event_id, $this->pending_spaces) || $force_refresh ){
			$status_cond = !get_option('dbem_bookings_approval') ? 'booking_status IN (0,1)' : 'booking_status = 1';
			$sub_sql = 'SELECT booking_id FROM '.EM_BOOKINGS_TABLE." WHERE event_id=%d AND $status_cond";
			$sql = 'SELECT SUM(ticket_booking_spaces) FROM '.EM_TICKETS_BOOKINGS_TABLE. " WHERE booking_id IN ($sub_sql) AND ticket_id=%d";
			$booked_spaces = $wpdb->get_var($wpdb->prepare($sql, $this->event_id, $this->ticket_id));
			$this->booked_spaces[$this->event_id] = $booked_spaces > 0 ? $booked_spaces : 0;
			$this->booked_spaces[$this->event_id] = apply_filters('em_ticket_get_booked_spaces', $this->booked_spaces[$this->event_id], $this, $force_refresh);
		}
		return $this->booked_spaces[$this->event_id];
	}

	/**
	 * Returns the total number of bookings of all statuses for this ticket
	 * @param int $status
	 * @param boolean $force_refresh
	 * @return int
	 */
	function get_bookings_count( $status = false, $force_refresh = false ){
		global $wpdb;
		if( !array_key_exists($this->event_id, $this->bookings_count) || $force_refresh ){
			$sql = 'SELECT COUNT(*) FROM '.EM_TICKETS_BOOKINGS_TABLE. ' WHERE booking_id IN (SELECT booking_id FROM '.EM_BOOKINGS_TABLE.' WHERE event_id=%d) AND ticket_id=%d';
			$bookings_count = $wpdb->get_var($wpdb->prepare($sql, $this->event_id, $this->ticket_id));
			$this->bookings_count[$this->event_id] = $bookings_count > 0 ? $bookings_count : 0;
			$this->bookings_count[$this->event_id] = apply_filters('em_ticket_get_bookings_count', $this->bookings_count[$this->event_id], $this, $force_refresh);
		}
		return $this->bookings_count[$this->event_id];
	}
	
	/**
	 * Returnds the event associated with this set of tickets, if there is one.
	 * @return EM_Event
	 */
	function get_event(){
		if( $this->event && $this->event->event_id == $this->event_id ){
			return $this->event;
		}
		global $EM_Event;
		if( is_object($EM_Event) && $EM_Event->event_id == $this->event_id ){
			$this->event = $EM_Event;
			return $EM_Event;
		}else{
			$this->event = em_get_event($this->event_id);
			return $this->event;
		}
	}
	/**
	 * returns array of EM_Booking objects that have this ticket
	 * @return EM_Bookings
	 */
	function get_bookings(){
		$bookings = array();
		foreach( $this->get_event()->get_bookings()->bookings as $EM_Booking ){
			foreach($EM_Booking->get_tickets_bookings()->tickets_bookings as $EM_Ticket_Booking){
				if( $EM_Ticket_Booking->ticket_id == $this->ticket_id ){
					$bookings[$EM_Booking->booking_id] = $EM_Booking;
				}
			}
		}
		$this->bookings = new EM_Bookings($bookings);
		return $this->bookings;
	}
	
	/**
	 *
	 * @return mixed|void
	 */
	public function get_recurrence_ticket_ids(){
		global $wpdb;
		$ticket_ids = array();
		if( $this->get_event()->is_recurring() ){
			//try the new way, just search tickets with the recurring ticket id stored as parent
			$sql = $wpdb->prepare('SELECT ticket_id FROM '.EM_TICKETS_TABLE." WHERE ticket_parent=%d", $this->ticket_id);
			$ticket_ids = $wpdb->get_col($sql);
			if( empty($ticket_ids) ){
				$recurrence_event_ids = $wpdb->get_col('SELECT event_id FROM '.EM_EVENTS_TABLE.' WHERE recurrence_id='.absint($this->event_id));
				if( !empty($recurrence_event_ids) ){
					$recurrence_event_ids_sql = implode(', ', $recurrence_event_ids);
					//we don't have the exact ID reference for each ticket, and we can't assume changes to EM save_events will reschedule previously created events in earlier versions, we need to do it this way
					$sql_vars = array($this->ticket_name, $this->ticket_spaces);
					$sql_prepare = 'SELECT ticket_id FROM '.EM_TICKETS_TABLE." WHERE ticket_name=%s AND ticket_spaces=%s AND event_id IN ($recurrence_event_ids_sql)";
					if( $this->ticket_description ){
						$sql_prepare .= ' AND ticket_description=%s';
						$sql_vars[] = $this->ticket_description;
					}else{
						$sql_prepare .= ' AND ticket_description IS NULL';
					}
					if( $this->ticket_price ){
						$sql_prepare .= ' AND ticket_price=%s';
						$sql_vars[] = $this->ticket_price;
					}else{
						$sql_prepare .= ' AND ticket_price IS NULL';
					}
					$sql = $wpdb->prepare($sql_prepare, $sql_vars);
					$ticket_ids = $wpdb->get_col($sql);
				}
			}
		}
		$ticket_ids = is_array($ticket_ids) ? $ticket_ids : array();
		foreach( $ticket_ids as $k => $v ) $ticket_ids[$k] = absint($v); //clean for SQL usage
		return apply_filters('em_ticket_get_recurrence_ticket_ids', $ticket_ids, $this);
	}
	
	/**
	 * I wonder what this does....
	 * @return boolean
	 */
	function delete(){
		global $wpdb;
		$result = false;
		if( $this->can_manage() ){
			if( count($this->get_bookings()->bookings) == 0 ){
				$sql = $wpdb->prepare("DELETE FROM ". EM_TICKETS_TABLE . " WHERE ticket_id=%d", $this->ticket_id);
				$result = $wpdb->query( $sql );
			}else{
				$this->feedback_message = __('You cannot delete a ticket that has a booking on it.','events-manager');
				$this->add_error($this->feedback_message);
				return false;
			}
		}
		return apply_filters('em_ticket_delete', $result !== false, $this);
	}

	/**
	 * Based on ticket minimums, whether required and if the event has more than one ticket this function will return the absolute minimum required spaces for a booking 
	 */
	function get_spaces_minimum(){
	    $ticket_count = count($this->get_event()->get_bookings()->get_tickets()->tickets);
	    //count available tickets to make sure
	    $available_tickets = 0;
	    foreach($this->get_event()->get_bookings()->get_tickets()->tickets as $EM_Ticket){
	    	if($EM_Ticket->is_available()){
	    		$available_tickets++;
	    	}
	    }
	    $min_spaces = 0;
	    if( $ticket_count > 1 ){
	        if( $this->is_required() && $this->is_available() ){
	            $min_spaces = ($this->ticket_min > 0) ? $this->ticket_min:1;
	        }elseif( $this->is_available() && $this->ticket_min > 0 ){
	            $min_spaces = $this->ticket_min;	            
	        }elseif( $this->is_available() && $available_tickets == 1 ){
	            $min_spaces = 1;
	        }
	    }else{
	    	$min_spaces = $this->ticket_min > 0 ? $this->ticket_min : 1;
	    }
	    return $min_spaces;
	}
	
	function is_required(){
	    if( $this->ticket_required || count($this->get_event()->get_tickets()->tickets) == 1 ){
	        return true;
	    }
	    return false;
	}

	/**
	 * Get the html options for quantities to go within a <select> container
	 * @return string
	 */
	function get_spaces_options($zero_value = true, $default_value = 0){
		$available_spaces = $this->get_available_spaces();
		if( EM_Bookings::$disable_restrictions ) $available_spaces = get_option('dbem_bookings_form_max');
		if( $this->is_available() ) {
		    $min_spaces = $this->get_spaces_minimum();
		    if( $default_value > 0 ){
			    $default_value = $min_spaces > $default_value ? $min_spaces:absint($default_value);
		    }else{
		        $default_value = $this->is_required() ? $min_spaces:0;
		    }
			ob_start();
			?>
			<select name="em_tickets[<?php echo $this->ticket_id ?>][spaces]" class="em-ticket-select" id="em-ticket-spaces-<?php echo $this->ticket_id ?>" data-ticket-id="<?php echo esc_attr($this->ticket_id); ?>">
				<?php 
					$min = ($this->ticket_min > 0) ? $this->ticket_min:1;
					$max = ($this->ticket_max > 0) ? $this->ticket_max:get_option('dbem_bookings_form_max');
					if( $this->get_event()->event_rsvp_spaces > 0 && $this->get_event()->event_rsvp_spaces < $max ) $max = $this->get_event()->event_rsvp_spaces;
				?>
				<?php if($zero_value && !$this->is_required()) : ?><option>0</option><?php endif; ?>
				<?php for( $i=$min; $i<=$available_spaces && $i<=$max; $i++ ): ?>
					<option <?php if($i == $default_value){ echo 'selected="selected"'; $shown_default = true; } ?>><?php echo absint($i) ?></option>
				<?php endfor; ?>
				<?php if(empty($shown_default) && $default_value > 0 ): ?><option selected="selected"><?php echo absint($default_value); ?></option><?php endif; ?>
			</select>
			<?php 
			return apply_filters('em_ticket_get_spaces_options', ob_get_clean(), $zero_value, $default_value, $this);
		}else{
			return false;
		}
	}
	
	/**
	 * Returns an EM_DateTime object of the ticket start date/time in local timezone of event.
	 * If no start date defined or if date is invalid, false is returned.
	 * @param bool $utc_timezone Returns EM_DateTime with UTC timezone if set to true, returns local timezone by default.
	 * @return EM_DateTime|false
	 * @see EM_Event::get_datetime()
	 */
	public function start( $utc_timezone = false ){
		return apply_filters('em_ticket_start', $this->get_datetime('start', $utc_timezone), $this);
	}
	
	/**
	 * Returns an EM_DateTime object of the ticket end date/time in local timezone of event.
	 * If no start date defined or if date is invalid, false is returned.
	 * @param bool $utc_timezone Returns EM_DateTime with UTC timezone if set to true, returns local timezone by default.
	 * @return EM_DateTime|false
	 * @see EM_Event::get_datetime()
	 */
	public function end( $utc_timezone = false ){
		return apply_filters('em_ticket_end', $this->get_datetime('end', $utc_timezone), $this);
	}
	
	/**
	 * Generates an EM_DateTime for the the start/end date/times of the ticket in local timezone.
	 * If ticket has no start/end date, or an invalid format, false is returned.
	 * @param string $when 'start' or 'end' date/time
	 * @param bool $utc_timezone Returns EM_DateTime with UTC timezone if set to true, returns local timezone by default. Do not use if EM_DateTime->valid is false. 
	 * @return EM_DateTime|false
	 */
	public function get_datetime( $when = 'start', $utc_timezone = false ){
		if( $when != 'start' && $when != 'end') return new EM_DateTime(); //currently only start/end dates are relevant
		//Initialize EM_DateTime if not already initialized, or if previously initialized object is invalid (e.g. draft event with invalid dates being resubmitted)
		$when_date = 'ticket_'.$when;
		//we take a pass at creating a new datetime object if it's empty, invalid or a different time to the current start date
		if( !empty($this->$when_date) ){
			if( empty($this->$when) || !$this->$when->valid ){
				$this->$when = new EM_DateTime( $this->$when_date, $this->get_event()->get_timezone() );
			}
		}else{
			$this->$when = new EM_DateTime();
			$this->$when->valid = false;
		}
		//Set to UTC timezone if requested, local by default
		$tz = $utc_timezone ? 'UTC' : $this->get_event()->get_timezone();
		$this->$when->setTimezone($tz);
		return $this->$when;
	}
	
	/**
	 * Can the user manage this event? 
	 */
	function can_manage( $owner_capability = false, $admin_capability = false, $user_to_check = false ){
		if( $this->ticket_id == '' && !is_user_logged_in() && get_option('dbem_events_anonymous_submissions') ){
			$user_to_check = get_option('dbem_events_anonymous_user');
		}
		return $this->get_event()->can_manage('manage_bookings','manage_others_bookings', $user_to_check);
	}
	
	/**
	 * Deprecated since 5.8.2, just access properties directly or use relevant functions such as $this->start() for ticket_start time - Outputs properties with formatting
	 * @param string $property
	 * @return string
	 */
	function output_property($property){
		switch($property){
			case 'start':
				$value = ( $this->start()->valid ) ? $this->start()->i18n( em_get_date_format() ) : '';
				break;
			case 'end':
				$value = ( $this->end()->valid ) ? $this->end()->i18n( em_get_date_format() ) : '';
				break;
			default:
				$value = $this->$property;
				break;
		}
		return apply_filters('em_ticket_output_property',$value,$this, $property);
	}
}
?>