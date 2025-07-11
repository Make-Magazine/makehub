<?php
namespace EM\Waitlist;
use EM_DateTime, EM_Booking, EM_Ticket_Booking, EM_Ticket_Bookings;

class Booking extends EM_Booking {
	
	public $booking_status = 6;
	
	public function __construct($booking_data = false) {
		if( $booking_data instanceof EM_Booking ){
			$booking_data = $booking_data->to_array();
		}
		parent::__construct($booking_data);
	}
	
	public function get_post($override_availability = false) {
		if( empty($this->event_id) && !empty($_REQUEST['event_id']) ) $this->event_id = absint($_REQUEST['event_id']);
		$this->booking_meta['waitlist'] = !empty($_REQUEST['waitlist_spaces']) ? absint($_REQUEST['waitlist_spaces']) : 1;
		$this->booking_spaces = $this->booking_meta['waitlist'];
		$EM_Ticket_Booking = new EM_Ticket_Booking();
		$EM_Ticket_Booking->booking = $this;
		$EM_Ticket_Booking->ticket_id = 0; // we're assigning a waiting list ticket number i.e. 0
		// We add it to the booking directly, tricking EM to think it's OK
		$EM_Tickets_Bookings = $this->get_tickets_bookings();
		$EM_Tickets_Bookings->tickets_bookings[0] = new EM_Ticket_Bookings( array('ticket_id' => 0, 'booking' => $this ) );
		$EM_Tickets_Bookings->tickets_bookings[0][$EM_Ticket_Booking->ticket_uuid] = $EM_Ticket_Booking;
		if( is_user_logged_in() ){
			$this->person_id = get_current_user_id();
			$result = true;
		}else{
			$result = $this->get_person_post();
		}
		return $result;
	}
	
	public function validate($override_availability = false) {
		global $wpdb;
		// check that the spaces requested meet the limits
		$available_spaces = Events::get_available_spaces($this->get_event());
		if( !$available_spaces ){
			// reached limit of event waitlist
			$error = $this->output(get_option('dbem_waitlists_feedback_full'));
		}elseif( $this->get_spaces() >  Events::get_var('limit', $this->get_event()) ){
			// spaces are more than the available spaces
			$error = $this->output(get_option('dbem_waitlists_feedback_limit'));
		}elseif( $this->get_spaces() > $available_spaces ){
			// spaces exceeded for booking limits
			$error = $this->output(get_option('dbem_waitlists_feedback_spaces_limit'));
		}elseif( $this->get_spaces() >  Events::get_var('booking_limit', $this->get_event()) ){
			// spaces exceeded for booking limits
			$error = $this->output(get_option('dbem_waitlists_feedback_booking_limit'));
		}
		if( !empty($error) ){
			$this->add_error( $error );
			$this->feedback_message = $error;
		}
		// check that the user hasn't already reserved
		if( is_user_logged_in() ){
			$sql = 'SELECT * FROM '. EM_BOOKINGS_TABLE . ' WHERE person_id=%d AND event_id=%d AND booking_status IN (6,7)';
			$booking = $wpdb->get_row( $wpdb->prepare($sql, get_current_user_id(), $this->event_id), ARRAY_A );
			if( is_array($booking) ){
				$EM_Booking = new Booking($booking);
				$error = $EM_Booking->output(get_option('dbem_waitlists_feedback_already_waiting'));
				$this->add_error( $error );
				$this->feedback_message = $error;
			}
		}else{
			$email = $this->booking_meta['registration']['user_email'];
			$sql = $wpdb->prepare('SELECT booking_id FROM '.EM_BOOKINGS_META_TABLE.' WHERE meta_key=%s AND meta_value=%s AND booking_id IN (SELECT booking_id FROM '. EM_BOOKINGS_TABLE.' WHERE event_id=%s AND booking_status IN (6,7))', '_registration_user_email', $email, $this->event_id);
			$booking_id = $wpdb->get_var($sql);
			if( $booking_id > 0 ){
				$EM_Booking = new Booking($booking_id);
				$error = $EM_Booking->output(get_option('dbem_waitlists_feedback_already_waiting'));
				$this->add_error( $error );
				$this->feedback_message = $error;
			}elseif( empty($this->booking_meta['registration']['user_name']) ){
				// validate name, email will have been validated in get_post
				$this->add_error( sprintf(esc_html__emp('%s is required.'), esc_html__emp('Your name')) );
			}
		}
		return empty($this->errors);
	}
	
	public function get_status(){
		if( $this->booking_status == 3 ){
			return esc_html__('Waitlist Cancelled');
		}
		return parent::get_status();
	}
	
	public function get_spaces( $force_refresh = false ){
		if( !empty($this->booking_meta['waitlist']) ) return $this->booking_meta['waitlist'];
		return 1;
	}
	
	public function get_price($format = false, $format_depricated = null) {
		if( $format || $format_depricated ){
			return '-';
		}
		return parent::get_price($format, $format_depricated); // TODO: Change the autogenerated stub
	}
	
	public function is_expired(){
		if( $this->booking_status == 8 ) return true;
		if( !empty($this->booking_meta['waitlist_expired']) ) return true;
		$expiry = $this->get_expiry_timestamp();
		if( $expiry > 0 ){
			return $expiry < time();
		}elseif( $expiry === 0 ){
			// 0 = never expires
			return false;
		}
		return true;
	}
	
	public function get_expiry_timestamp(){
		if( isset($this->booking_meta['waitlist_expiry']) ){
			return absint($this->booking_meta['waitlist_expiry']);
		}
		// if we get here, we can only assume there's no expiry
		return 0;
	}
	
	public function get_expiry_time_left( $expired_time = false ){
		if( $expired_time ){
			$expiry = 0;
			if( !empty( $this->booking_meta['waitlist_expired']) ){
				$expiry = absint($this->booking_meta['waitlist_expired']);
			}elseif( $this->is_expired() ){
				// default to date of booking
				$expiry = $this->date()->getTimestamp();
			}
		}else{
			$expiry = $this->get_expiry_timestamp();
		}
		if( $expiry == 0 ){
			$time_left = '['.__('unlimited', 'em-pro'). ']';
		}else{
			$expriy_datetime = new EM_DateTime( $expiry );
			$now_datetime = new EM_DateTime('now', 'UTC');
			if( !$expired_time && $expriy_datetime < $now_datetime ){
				$time_left = '['.__('expired', 'em-pro'). ']';
			}else{
				$interval = $expriy_datetime->diff($now_datetime);
				$days = $interval->d > 1 ? translate('days') : translate('day');
				$hours = $interval->h > 1 ? translate('hours') : translate('hour');
				$minutes = $interval->i > 1 ? translate('minutes') : translate('minute');
				$seconds = $interval->s > 1 ? translate('seconds') : translate('second');
				if( $interval->d != 0 ){
					$format = '%d '. $days .' %h '. $hours .' %i '. $minutes;
				}elseif( $interval->h != 0 ){
					$format = '%h '. $hours .' %i '. $minutes;
				}elseif( $interval->i != 0 ){
					$format = '%i '. $minutes;
				}else{
					$format = '%s '. $seconds;
				}
				$time_left = $interval->format($format);
			}
		}
		return $time_left;
	}
	
	public function get_booking_url(){
		$url = em_get_my_bookings_url();
		$query_args = array('pno' => null, 'uuid' => $this->booking_uuid, 'waitlist_booking' => 1);
		if( $this->person_id == 0 ){
			$query_args['email'] = urlencode($this->booking_meta['registration']['user_email']);
		}
		return add_query_arg( $query_args, $url );
	}
	
	/**
	 * @param string $result
	 * @param array $placeholder_atts
	 * @param string $format
	 * @param string $target
	 * @return string
	 */
	public function output_placeholder( $result, $placeholder_atts, $format, $target ){
		global $wpdb;
		$replace = $result;
		switch( $result ){
			case '#_WAITLIST_BOOKING_EXPIRY':
				$replace = $this->get_expiry_time_left();
				break;
			case '#_WAITLIST_BOOKING_EXPIRED':
				$replace = $this->get_expiry_time_left(true);
				break;
			case '#_WAITLIST_BOOKING_AHEAD': // people ahead of booking
				$sql = $wpdb->prepare('SELECT COUNT(*) FROM '. EM_BOOKINGS_TABLE .' WHERE event_id=%d AND booking_status=6 AND booking_id < %d', $this->event_id, $this->booking_id);
				$replace = absint($wpdb->get_var($sql));
				break;
			case '#_WAITLIST_BOOKING_BEHIND': // reservations behind booking
				$sql = $wpdb->prepare('SELECT COUNT(*) FROM '. EM_BOOKINGS_TABLE .' WHERE event_id=%d AND booking_status=6 AND booking_id > %d', $this->event_id, $this->booking_id);
				$replace = absint($wpdb->get_var($sql));
				break;
			case '#_WAITLIST_BOOKING_POSITION': // place in line
				$sql = $wpdb->prepare('SELECT COUNT(*) FROM '. EM_BOOKINGS_TABLE .' WHERE event_id=%d AND booking_status=6 AND booking_id <= %d', $this->event_id, $this->booking_id);
				$replace = absint($wpdb->get_var($sql));
				break;
			case '#_WAITLIST_BOOKING_SPACES_AHEAD': // spaces reserved ahead of yours
				$sql = $wpdb->prepare('SELECT SUM(booking_spaces) FROM '. EM_BOOKINGS_TABLE .' WHERE event_id=%d AND booking_status=6 AND booking_id <= %d', $this->event_id, $this->booking_id);
				$replace = absint($wpdb->get_var($sql));
				break;
			case '#_WAITLIST_BOOKING_URL':
				$replace = $this->get_booking_url();
				break;
		}
		return $replace;
	}
	
	public function email_messages() {
		$msg = parent::email_messages();
		switch ($this->booking_status) {
			case 3:
				$msg['user']['subject'] = get_option('dbem_waitlists_emails_cancelled_subject');
				$msg['user']['body'] = get_option('dbem_waitlists_emails_cancelled_message');
				$msg['admin']['subject'] = $msg['admin']['body'] = ''; // we won't send admins this msg
				break;
			case 6:
				$msg['user']['subject'] = get_option('dbem_waitlists_emails_confirmed_subject');
				$msg['user']['body'] = get_option('dbem_waitlists_emails_confirmed_message');
				$msg['admin']['subject'] = $msg['admin']['body'] = ''; // we won't send admins this msg
				break;
			case 7:
				$msg['user']['subject'] = get_option('dbem_waitlists_emails_approved_subject');
				$msg['user']['body'] = get_option('dbem_waitlists_emails_approved_message');
				$msg['admin']['subject'] = $msg['admin']['body'] = ''; // we won't send admins this msg
				break;
			case 8:
				$msg['user']['subject'] = get_option('dbem_waitlists_emails_expired_subject');
				$msg['user']['body'] = get_option('dbem_waitlists_emails_expired_message');
				$msg['admin']['subject'] = $msg['admin']['body'] = ''; // we won't send admins this msg
				break;
		}
		return $msg;
	}
	
	/**
	 * @param int $status
	 * @param bool $email
	 * @param bool $ignore_spaces
	 * @return bool
	 */
	public function set_status( $status, $email = true, $ignore_spaces = false ){
		$func_args = func_get_args();
		$email_args = !empty($func_args[3]) ? $func_args[3] : array();
		if( $status == 1 ) $status = 7;
		if( $status == 7 ){
			// reset the expiry if re-approved, before setting status so emails get correct placeholders for expiry times
			$expiry = Events::get_var('expiry', $this->get_event());
			$this->update_meta('waitlist_expiry', ($expiry * 3600) + time() );
			$this->update_meta('waitlist_expired', null );
		}elseif( $this->booking_status == 3 ){
			// delete expiry record, not relevant anymore
			$this->update_meta('waitlist_expiry', null);
		}
		return parent::set_status($status, $email, $ignore_spaces, $email_args);
	}
	
	// helper static functions
	
	/**
	 * @param \EM_Event $EM_Event
	 *
	 * @return int|mixed
	 */
	public static function get_max_spaces( $EM_Event ){
		// get limits imposed by waitlists (current WL bookings, space limits)
		$limits['limit'] = Events::get_var('limit', $EM_Event);
		$limits['booking_limit'] = Events::get_var('booking_limit', $EM_Event);
		$limits['spaces'] = Events::get_available_spaces( $EM_Event );
		// event spaces
		$limits['event_spaces'] = $EM_Event->get_spaces();
		// determine max amount possible based on lowest number we've gathered
		foreach( $limits as $k => $limit ){
			if( $limit == 0 || ($k == 'spaces' && $limit === true) ) unset($limits[$k]);
		}
		return empty($limits) || min($limits) == 0 ? 20 : min($limits);
	}
	
	// Static functinos that'll deal with specific situations in the regular EM_Booking objects
	
	/**
	 * Converts waitlist bookings into the EM\Waitlists\Bookings function
	 * @param EM_Booking $EM_Booking
	 * @return EM_Booking
	 */
	public static function em_get_booking( $EM_Booking ){
		if( !empty($EM_Booking->booking_meta['waitlist']) || in_array($EM_Booking->booking_status, array(6,7,8)) ) {
			// convert to a Waitlist Booking object
			$booking_array = $EM_Booking->to_array(true);
			$EM_Booking = new Booking($booking_array);
		}
		return $EM_Booking;
	}
	
	/**
	 * Temporary... in future we could automatically put a ready-to-go booking into a waiting list that automatically gets approved if a real booking with real tickets.
	 * @param EM_Booking $EM_Booking
	 * @return void
	 */
	public static function em_booking( $EM_Booking ){
		if( $EM_Booking->booking_id && !(!empty($EM_Booking->booking_meta['waitlist']) || in_array($EM_Booking->booking_status, array(6,7,8))) ){
			unset( $EM_Booking->status_array[6] );
			unset( $EM_Booking->status_array[7] );
			unset( $EM_Booking->status_array[8] );
		}
	}
	
	/**
	 * @param bool $result
	 * @param EM_Booking $EM_Booking
	 * @return bool
	 */
	public static function em_booking_get_post( $result, $EM_Booking ){
		// check if there's a waitlist-approved booking id appended, if so double-check and disable space restrictions for this amount of bookings
		if( !empty($_REQUEST['waitlist_booking_uuid']) && Bookings::get_booking( $_REQUEST['waitlist_booking_uuid'] ) !== false ){
			// we have an associated waitlist booking, so we'll tack on the reference here, assuming it's approved
			if( Bookings::$booking->booking_status == 7 ) {
				$EM_Booking->booking_meta['waitlist_booking'] = Bookings::$booking->booking_uuid; // we'll set this to true before saving to db
			}
		}
		return $result;
	}
	
	/**
	 * @param EM_Booking $EM_Booking
	 * @return void
	 */
	public static function em_booking_validate_pre( $EM_Booking ){
		if( !empty($EM_Booking->booking_meta['waitlist_booking']) && $EM_Booking->booking_meta['waitlist_booking'] !== 1 && $EM_Booking->booking_meta['waitlist_booking'] !== '1' ){
			// in case somewhere down the line the booking object got changed, reload it
			if( Bookings::$booking && Bookings::$booking->booking_uuid !== $EM_Booking->booking_meta['waitlist_booking'] ){
				Bookings::get_booking($EM_Booking->booking_meta['waitlist_booking']);
			}
			// remove the restriction of waitlist bookings reserving spaces
			Bookings::disable_booking_restrictions();
		}
	}
	
	public static function em_booking_validate_after( $EM_Booking ){
		if( !empty($EM_Booking->booking_meta['waitlist_booking']) && $EM_Booking->booking_meta['waitlist_booking'] != 1 && $EM_Booking->booking_meta['waitlist_booking'] !== '1' ){
			Bookings::reenable_booking_restrictions();
		}
	}
	
	/**
	 * Checks if a space has freed up due to booking update, also associates and cleans up waitlists that have ben changed into a real booking.
	 * @param bool $result
	 * @param \EM_Booking $EM_Booking
	 * @return bool
	 */
	public static function em_booking_save( $result, $EM_Booking ){
		// if this is a boking with an associated waitlist, we need to delete it and reset the
		if( $result && !empty($EM_Booking->booking_meta['waitlist_booking']) && $EM_Booking->booking_meta['waitlist_booking'] !== 1  && $EM_Booking->booking_meta['waitlist_booking'] !== '1' ){
			Bookings::get_booking($EM_Booking->booking_meta['waitlist_booking']);
			Bookings::$booking->delete();
			$EM_Booking->update_meta('waitlist_booking', 1);
			$EM_Booking->update_meta('waitlist_expiry', null);
		}
		return $result;
	}
	
	public static function em_booking_output_show_condition( $show_condition, $args, $EM_Booking){
		$condition = $args['condition'];
		if( $condition === 'is_waitlist' ){
			$show_condition = !empty($EM_Booking->booking_meta['waitlist']) || in_array($EM_Booking->booking_status, array(6,7,8));
		}elseif( $condition == 'not_waitlist'){
			$show_condition = empty($EM_Booking->booking_meta['waitlist']) && !in_array($EM_Booking->booking_status, array(6,7,8));
		}
		return $show_condition;
	}
	
}
// convert EM_Booking waitlist bookings to Waitlist\Booking objects
add_filter('em_get_booking', '\EM\Waitlist\Booking::em_get_booking', 10, 1);
// remove waitlist statuses from non-waitlist statuses
add_action('em_booking', '\EM\Waitlist\Booking::em_booking', 10, 1);
// add conditional placeholder for use in general emails
add_action('em_booking_output_show_condition', '\EM\Waitlist\Booking::em_booking_output_show_condition', 10, 4);
// intercept regular bookings that are dreived from a waitlist-approved reservation
add_filter('em_booking_get_post', '\EM\Waitlist\Booking::em_booking_get_post', 10, 2);
add_filter('em_booking_validate_pre', '\EM\Waitlist\Booking::em_booking_validate_pre', 10, 1);
add_filter('em_booking_validate_after', '\EM\Waitlist\Booking::em_booking_validate_after', 10, 1);
add_filter('em_booking_save', '\EM\Waitlist\Booking::em_booking_save', 9, 2); // before ::Manager