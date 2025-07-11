<?php
class EM_Custom_Emails{
	
	/**
	 * Initializes custom emails by hooking into booking email filters and modifying the recpipients and message content accordingly 
	 */
	public static function init(){
		//hooks for gateway-specific emails
		if( get_option('dbem_custom_emails_gateways') ){
		    add_filter('em_booking_email_messages', 'EM_Custom_Emails::gateway_email_messages',100,2);
			if( get_option('dbem_custom_emails_gateways_admins') ){
			    //hook into admin emails for gateway-specific extra admin addresses
				add_filter('em_booking_admin_emails','EM_Custom_Emails::gateway_admin_emails', 100, 2);
			}
			//multiple bookings
			if( get_option('dbem_multiple_bookings') ){
			    add_filter('em_custom_emails_gateway_groups', 'EM_Custom_Emails::em_custom_emails_gateway_groups',10,3);
			    add_filter('em_multiple_booking_email_messages', 'EM_Custom_Emails::gateway_email_messages',100,2);
			}
		}
		//hooks for event-specific emails, which override gateway-spefic ones
		if( get_option('dbem_custom_emails_events') ){
		    add_filter('em_booking_email_messages', 'EM_Custom_Emails::event_email_messages',110,2); //110 so it overrides gateway-specific
    		if( get_option('dbem_custom_emails_events_admins')){
    		    //hook into admin emails for custom per-event extra admin email addresses
    			add_filter('em_booking_admin_emails','EM_Custom_Emails::event_admin_emails', 100, 2);
    		}
		}
		//admin area
		if( is_admin() ){
		    include('custom-emails-admin.php');
		}
		//multilingual hook - this SHOULD fire after the EM_ML init hook as it's added after EM_ML is loaded
		add_action('em_ml_init', 'EM_Custom_Emails::em_ml_init');
		//data privacy
		add_filter('em_data_privacy_export_events_item', 'EM_Custom_Emails::em_data_privacy_export_events_item', 10, 2);
	}
	
	public static function em_ml_init(){ include('custom-emails-ml.php'); }
	
	/**
	 * Merges two email arrays recursively, only overwriting to the second level arrays in the first array.
	 * @param array $custom_emails
	 * @param array $overriding_emails
	 * @return array
	 */
	public static function merge_emails_array( $custom_emails, $overriding_emails ){
	    //example array of $custom_emails : array('offline-admin'=>array(0=>array(...),5=>array(...)), 'offline-user'=>array(2=>array(....));
	    foreach($overriding_emails as $group => $statuses){
	        //go through the overriding emails and overwrite statuses in $custom_emails
	        if( !empty($custom_emails[$group]) ){
	            //group already exists, so overwrite any conflicting keys
	            foreach( $statuses as $status_key => $status_data ){
	                $custom_emails[$group][$status_key] = $status_data;
	            }
	        }else{
	            //this group isn't in $custom_emails so just add it entirely
	            $custom_emails[$group] = $statuses;
	        }
	    }
	    return $custom_emails;
	}
	
    /*
     * --------------------------------------------
     * Custom Event Booking Emails
     * --------------------------------------------
     */
	
	/**
	 * Returns an array of email templates specific to the supplied event
	 * @param EM_Event $EM_Event the event object which may contain custom emails
	 * @return array
	 */
	public static function get_event_emails( $EM_Event ){
		global $wpdb;
		$custom_email_values = array();
		if( !empty($EM_Event->event_id) ){
			$sql = $wpdb->prepare('SELECT meta_value FROM '.EM_META_TABLE." WHERE object_id = %d AND meta_key = %s LIMIT 1", $EM_Event->event_id, 'event-emails');
			$possible_email_values = maybe_unserialize($wpdb->get_var($sql));
			if( is_array($possible_email_values) ){
				$custom_email_values = $possible_email_values;
			}
		}
		return $custom_email_values;
	}
	
	/**
	 * Returns an array of additional admin emails specific to the supplied event
	 * @param EM_Event $EM_Event the event object which may contain custom emails
	 * @return array
	 */
	public static function get_event_admin_emails( $EM_Event ){
		global $wpdb;
		$custom_admin_emails = array();
		if( !empty($EM_Event->event_id) ){
		    //get stored event emails from em_meta table
			$sql = $wpdb->prepare('SELECT meta_value FROM '.EM_META_TABLE." WHERE object_id = %d AND meta_key = %s LIMIT 1", $EM_Event->event_id, 'event-admin-emails');
			$possible_email_values = maybe_unserialize($wpdb->get_var($sql));
			if( is_array($possible_email_values) ){
				$custom_admin_emails = $possible_email_values;
			}
			//convert comma-seperated values into arrays
			foreach($custom_admin_emails as $k => $v) $custom_admin_emails[$k] = !empty($v) ? explode(',', $v):array();
		}
		return $custom_admin_emails;
	}	
	
	/**
	 * Filter out the right email to be sent. Takes an array of custom email possibilities, types of email groups to check for and a booking object to checks against.
	 * @param array $custom_emails array of custom email groups to override
	 * @param array $groups_to_check array of custom email group keys corresponding to user types e.g. array('offline-admin'=>'admin','user'=>'user')
	 * @param EM_Booking $EM_Booking contains event information used to retrieve custom email templates
	 * @return array
	 */
	public static function get_booking_messages( $custom_emails, $groups_to_check, $EM_Booking ){
	    $msg = array(); //emails that could be used to override
		//set both admin and user email messages according to settings in custom emails defined above
		foreach( $groups_to_check as $user => $email_type ){
			$booking_status = $EM_Booking->booking_status;
		    if( !empty($custom_emails[$user][$booking_status]) ){
    			if( $custom_emails[$user][$booking_status]['status'] == 1 ){
    				//override default email with custom email
    		    	$msg[$email_type]['subject'] = $custom_emails[$user][$booking_status]['subject'];
    		    	$msg[$email_type]['body'] = $custom_emails[$user][$booking_status]['message'];
    			}elseif( !empty($custom_emails[$user][$booking_status]) && $custom_emails[$user][$booking_status]['status'] == 2 ){
    				//disable the email entirely
    				$msg[$email_type]['subject'] = $msg[$email_type]['body'] = '';		
    			}
		    }
		}
		return $msg;
	}
	
	/**
	 * Hooks into the em_booking_email_messages filter to modify email templates with event-specific ones if defined.
	 * @param array $msg array of admin and users messages
	 * @param EM_Booking $EM_Booking contains event information used to retrieve custom email templates
	 * @return array modified $msg array
	 * @uses EM_Custom_Emails::get_event_emails()
	 * @uses EM_Custom_Emails::get_booking_messages()
	 */
	public static function event_email_messages( $msg, $EM_Booking ){
	    if( get_class($EM_Booking) == 'EM_Multiple_Booking') return; //ignore MB bookings
		//create set of users to check against $custom_emails
		$groups_to_check = array('admin'=>'admin','user'=>'user'); //by default, we check admin and user email keys in $custom_emails
		//add gateway checks if booking used one, event gateway emails will override the default ones since added to end of array
		if( !empty($EM_Booking->booking_meta['gateway']) ){
		    $gateway_groups_to_check = array($EM_Booking->booking_meta['gateway'].'-admin' => 'admin', $EM_Booking->booking_meta['gateway'].'-user' => 'user');
		    $groups_to_check = array_merge($groups_to_check, $gateway_groups_to_check);
		}
		//get custom event emails and determine which should be used
		$custom_emails = apply_filters('em_custom_emails_event_messages', self::get_event_emails($EM_Booking->get_event()), $EM_Booking);
		$event_emails = self::get_booking_messages($custom_emails, $groups_to_check, $EM_Booking);
		//merge in custom event emails into default $msg
		return array_merge($msg, $event_emails);
	}
	

	/**
	 * Takes current emails passed by the em_booking_email_messages filter and adds/replaces with gateway-specific emails.
	 * @param array $custom_emails default emails
	 * @param EM_Booking $EM_Booking booking object generating this email
	 * @return array emails with gateway-specific content merged in
	 */
	public static function gateway_email_messages( $msg, $EM_Booking ){
		//firstly, check if we're using a gateway at all for this booking
		if( empty($EM_Booking->booking_meta['gateway']) || $EM_Booking->get_price() == 0 ) return $msg;
		$Gateway = \EM\Payments\Gateways::get_gateway($EM_Booking->booking_meta['gateway']);
		//create set of groups to check against $custom_emails
		$groups_to_check = apply_filters('em_custom_emails_gateway_groups', array($Gateway::$gateway.'-admin' => 'admin', $Gateway::$gateway.'-user' => 'user'), $EM_Booking, $Gateway);
		//get custom gateway email messages
		$custom_emails = apply_filters('em_custom_emails_gateway_messages', maybe_unserialize($Gateway::get_option('emails')), $EM_Booking, $Gateway);
		//if we have an 'awaiting payment' status and only a 'pending' status template, default to 'pending' so we don't skip to the general default pending template
		foreach( $groups_to_check as $key => $val ){
			if( $EM_Booking->booking_status == 5 && empty($custom_emails[$key][5]) && !empty($custom_emails[$key][0]) ){
				$custom_emails[$key][5] = $custom_emails[$key][0];
			}
		}
		//determine which will be used
		$gateway_emails = self::get_booking_messages($custom_emails, $groups_to_check, $EM_Booking);
		//merge into default $msg and return
		return array_merge($msg, $gateway_emails);
	}
	
	/**
	 * Hooks into em_booking_admin_emails filter and adds additional admin email addresses specific for this event
	 * @param array $emails array of current email addresses that will be sent to
	 * @param EM_Booking $EM_Booking contains event information, used to retrieve the relevant emails
	 * @return array
	 * @uses EM_Custom_Emails::get_event_admin_emails()
	 */
	public static function event_admin_emails( $emails, $EM_Booking ){
		$admin_emails = array();
		if( get_class($EM_Booking) == 'EM_Booking' ){ //prevent MB bookings from possibly sending individual event emails
		    $admin_emails_array = apply_filters('em_custom_emails_event_admin', self::get_event_admin_emails($EM_Booking->get_event()), $EM_Booking);
		    $group = empty($EM_Booking->booking_meta['gateway']) || $EM_Booking->get_price() == 0 ? 'default':$EM_Booking->booking_meta['gateway'];
		    if( !empty($admin_emails_array[$group]) ){
		        $admin_emails = $admin_emails_array[$group];
		    }
		}
		return array_merge($emails, $admin_emails);
	}
	
    /*
     * --------------------------------------------
     * Custom Gateway Booking Emails
     * --------------------------------------------
     */
	
	/**
	 * Gets array of admin email addresses for a specific gateway.
	 *
	 * @param \EM\Payments\Gateway $Gateway
	 * @param string $lang
	 *
	 * @return array Multi-dimensional array containing emails for gateway variations, e.g. array('paypal'=>'email1,email2','paypal-mb'=>'email1,email2');
	 */
	public static function get_gateway_admin_emails($Gateway, $lang = false){
		$possible_email_values = maybe_unserialize($Gateway::get_option('emails_admins'));
		$custom_admin_emails = !is_array($possible_email_values) ? array() : $possible_email_values;
		//convert all comma-delimited values
		foreach( $custom_admin_emails as $k => $v ) $custom_admin_emails[$k] = !empty($v) ? explode(',', $v) : array();
		return $custom_admin_emails;
	}
	
	/**
	 * Hooks into em_multiple_booking_email_messages and checks for gateway-specific emails for multiple booking objects.
	 * @param array $groups_to_check groups of email types => user type to check for gateway emails
	 * @param EM_Booking $EM_Booking The booking in question
	 * @param \EM\Payments\Gateway $Gateway The gateway used by this booking
	 * @return array modified $groups_to_check if this is an EM_Multiple_Booking object
	 */
	public static function em_custom_emails_gateway_groups( $groups_to_check, $EM_Booking, $Gateway ){
		//MB Mode bookings has normal gateway checks
		if( get_class($EM_Booking) == 'EM_Multiple_Booking'){
		    $groups_to_check = array($Gateway::$gateway.'-mb-admin' => 'admin', $Gateway::$gateway.'-mb-user' => 'user');
		}
		return $groups_to_check;
	}
	
	/**
	 * Hook for em_booking_admin_emails which adds to a list of admin emails being notified of the passed EM_Booking object.
	 * @param array $emails array of admin emails
	 * @param EM_Booking $EM_Booking
	 * @return array:
	 */
	public static function gateway_admin_emails( $emails, $EM_Booking ){
		if( empty($EM_Booking->booking_meta['gateway']) || $EM_Booking->get_price() == 0 ) return $emails;
		$gateway = $EM_Booking->booking_meta['gateway'];
		$Gateway = \EM\Payments\Gateways::get_gateway($gateway);
		$admin_emails_array = apply_filters('em_custom_emails_gateway_admin', self::get_gateway_admin_emails($Gateway), $EM_Booking, $Gateway);
		$admin_emails = array();
		if( get_class($EM_Booking) == 'EM_Booking' ){
			if( !empty($admin_emails_array[$gateway]) ){
				$admin_emails = $admin_emails_array[$gateway];
			}
		}elseif( get_class($EM_Booking) == 'EM_Multiple_Booking' ){
			//if MB mode is on, we check the mb email templates instead
			if( !empty($admin_emails_array[$gateway.'-mb']) ){
				$admin_emails = $admin_emails_array[$gateway.'-mb'];
			}
		}
		return array_merge($emails, $admin_emails);
	}

	/**
	 * @param array $export_item
	 * @param EM_Event $EM_Event
	 * @return array
	 */
	public static function em_data_privacy_export_events_item($export_item, $EM_Event ){
		//if( empty($EM_Event->event_owner_anonymous) ){ //we may want to limit this to registered users only since anon users can't do this
		$admin_emails = self::get_event_admin_emails($EM_Event);
		$event_emails = self::get_event_emails($EM_Event);
		if( !empty($admin_emails) ){
			$admin_emails_export = array();
			foreach( $admin_emails as $admin_email_type => $admin_email_type_emails ){
				$admin_emails_export[] = $admin_email_type . ': '. implode(', ', $admin_email_type_emails);
			}
			$export_item['data'][] = array( 'name' => __('Admin Emails', 'em-pro'), 'value' => implode('<br>', $admin_emails_export) );
			unset($admin_emails_export, $admin_emails);
		}
		if( !empty($event_emails) ){
			$EM_Booking = new EM_Booking();
			$event_emails_export = array();
			foreach( $event_emails as $event_email_type => $event_email_type_emails ){
				$event_emails_string = $event_email_type . '<br>------';
				foreach( $event_email_type_emails as $status => $email_data ){
					$event_emails_string .= '<br>'. emp__('Status') . ' : ' . $EM_Booking->status_array[$status] ;
					foreach( $email_data as $email_data_key => $email_data_item ){
						$event_emails_string .= '<br>'. $email_data_key . ' : ';
						$event_emails_string .= $email_data_key == 'message' ? '<br>'.$email_data_item : $email_data_item;
					}
				}
				$event_emails_export[] = $event_emails_string;
			}
			$export_item['data'][] = array( 'name' => __('Admin Emails', 'em-pro'), 'value' => implode('<br><br>--------------<br><br>', $event_emails_export) );
		}
		return $export_item;
	}
}
EM_Custom_Emails::init();