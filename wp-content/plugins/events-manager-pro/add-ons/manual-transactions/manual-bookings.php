<?php
namespace EM\Manual_Transactions;
use EM_Bookings, EM_Person, EM\Payments\Offline\Gateway;

class Bookings {
	
	public static function init(){
		// buttons and links to add manual booking
		add_action('em_admin_event_booking_options_buttons', array(get_called_class(), 'event_booking_options_buttons'),10);
		add_action('em_admin_event_booking_options', array(get_called_class(), 'event_booking_options'),10);
		
		// add a manual booking admin page support
		add_action('em_bookings_manual_booking', array(get_called_class(), 'add_booking_form'),1,1);
		
		// check request and add actions to circumvent a regular booking
		add_action('em_before_booking_action_booking_add', array(get_called_class(), 'em_before_booking_action_booking_add'), 10, 1);
		
		if( !empty($_REQUEST['action']) && !empty($_REQUEST['event_id']) && $_REQUEST['action'] == 'manual_booking' ){
			add_action('admin_enqueue_scripts', array('EM_Scripts_and_Styles','enqueue_public_styles'));
			add_action('wp_enqueue_scripts', array('EM_Scripts_and_Styles', 'enqueue_public_styles'));
		}
	}
	
	/**
	 * Adds an add manual booking button to admin pages
	 */
	public static function event_booking_options_buttons(){
		global $EM_Event;
		?><a href="<?php echo em_add_get_params($EM_Event->get_bookings_url(), array('action'=>'manual_booking','event_id'=>$EM_Event->event_id)); ?>" class="button button-secondary"><?php _e('Add Booking','em-pro') ?></a><?php
	}
	
	/**
	 * Adds a link to add a new manual booking in admin pages
	 */
	public static function event_booking_options(){
		global $EM_Event;
		?><a href="<?php echo em_add_get_params($EM_Event->get_bookings_url(), array('action'=>'manual_booking','event_id'=>$EM_Event->event_id)); ?>"><?php _e('add booking','em-pro') ?></a><?php
	}
	
	
	/**
	 * Generates a booking form where an event admin can add a booking for another user. $EM_Event is assumed to be global at this point.
	 */
	public static function add_booking_form() {
		/* @var $EM_Event \EM_Event */
		global $EM_Event;
		if( !is_object($EM_Event) ) { return; }
		//force all user fields to be loaded
		EM_Bookings::$force_registration = EM_Bookings::$disable_restrictions = true;
		//make all tickets available
		foreach( $EM_Event->get_bookings()->get_tickets() as $EM_Ticket ) $EM_Ticket->is_available = true; //make all tickets available
		//remove unecessary footer payment stuff and add our own
		remove_action('em_booking_form_footer', array('EM_Gateways','booking_form_footer'),10);
		remove_action('em_booking_form_footer', array('EM_Gateways','event_booking_form_footer'),10);
		remove_action('em_booking_form_footer_before_buttons', array('EM_Gateways','event_booking_form_footer'),10);
		// add manual booking sections
		add_action('em_booking_form_footer', array( get_called_class(), 'em_booking_form_footer'),10,2);
		add_action('em_booking_form_custom', array( get_called_class(), 'em_booking_form_custom'), 1);
		$header_button_classes = is_admin() ? 'page-title-action':'button add-new-h2';
		add_action('pre_option_dbem_bookings_double','__return_true'); //so we don't get a you're already booked here message
		do_action('em_before_manual_booking_form');
		//Data privacy consent - not added in admin by default, so we add it here
		if( get_option('dbem_data_privacy_consent_bookings') > 0 ){
			add_filter('pre_option_dbem_data_privacy_consent_remember', '__return_zero');
			add_action('em_booking_form_footer', 'em_data_privacy_consent_checkbox', 9, 0); //supply 0 args since arg is $EM_Event and callback will think it's an event submission form
			add_action('em_booking_form_after_user_details', 'em_data_privacy_consent_checkbox', 9, 0); //supply 0 args since arg is $EM_Event and callback will think it's an event submission form
		}
		?>
		<div class='wrap em-manual-booking'>
			<?php if( is_admin() ): ?>
				<h1 class="wp-heading-inline"><?php echo sprintf(__('Add Booking For &quot;%s&quot;','em-pro'), $EM_Event->name); ?></h1>
				<a href="<?php echo esc_url($EM_Event->get_bookings_url()); ?>" class="<?php echo $header_button_classes; ?>"><?php echo esc_html(sprintf(__('Go back to &quot;%s&quot; bookings','em-pro'), $EM_Event->name)) ?></a>
				<hr class="wp-header-end" />
			<?php else: ?>
				<h2>
					<?php echo sprintf(__('Add Booking For &quot;%s&quot;','em-pro'), $EM_Event->name); ?>
					<a href="<?php echo esc_url($EM_Event->get_bookings_url()); ?>" class="<?php echo $header_button_classes; ?>"><?php echo esc_html(sprintf(__('Go back to &quot;%s&quot; bookings','em-pro'), $EM_Event->name)) ?></a>
				</h2>
			<?php endif; ?>
			<?php echo $EM_Event->output('#_BOOKINGFORM'); ?>
			<script type="text/javascript">
				jQuery(document).ready(function($){
					var user_fields = $('.em-booking-form p.input-user-field');
					$('select#person_id').on('change', function(e){
						var person_id = $('select#person_id option:selected').val();
						person_id > 0 ? user_fields.addClass('hidden') : user_fields.removeClass('hidden');
						<?php if( get_option('dbem_data_privacy_consent_bookings') > 0 ): remove_filter('pre_option_dbem_data_privacy_consent_remember', '__return_zero'); ?>
						var consent_enabled = <?php echo esc_js( get_option('dbem_data_privacy_consent_bookings') ); ?>;
						var consent_remember = <?php echo esc_js( get_option('dbem_data_privacy_consent_remember') ); ?>;
						var consent_field = $('.em-booking-form p.input-field-data_privacy_consent');
						var consent_checkbox = consent_field.find('input[type="checkbox"]').prop('checked', false);
						if( person_id > 0 ){
							$('.em-booking-form p.input-user-field').addClass('hidden');
							if( consent_enabled === 1 ){
								var consented = Number($(this).find(':selected').data('consented')) === 1;
								if( consent_remember > 0 ){
									consent_checkbox.prop('checked', consented);
									if( consent_remember === 1 ) consented ? consent_field.addClass('hidden') : consent_field.removeClass('hidden');
								}
							}else if( consent_enabled === 2 ){
								consent_field.addClass('hidden');
							}
						}else{
							$('.em-booking-form p.input-user-field').removeClass('hidden');
							consent_field.removeClass('hidden');
						}
						<?php endif; ?>
					});
				});
			</script>
		</div>
		<?php
		do_action('em_after_manual_booking_form');
		//add js that calculates final price, and also user auto-completer
		//if user is chosen, we use normal registration and change person_id after the fact
		//make sure payment amounts are resporcted
	}
	
	/**
	 * Modifies the booking status if the event isn't free and also adds a filter to modify user feedback returned.
	 * Triggered by the em_booking_add_yourgateway action.
	 * @param \EM_Event $EM_Event
	 */
	public static function em_before_booking_action_booking_add( $EM_Event ){
		//manual bookings
		if( !empty($_REQUEST['manual_booking']) && wp_verify_nonce($_REQUEST['manual_booking'], 'em_manual_booking_'.$EM_Event->event_id) ){
			if( !empty($_REQUEST['manual_booking_override']) ) {
				add_action( 'pre_option_dbem_bookings_double', '__return_true' ); //so we don't get a you're already booked here message
				EM_Bookings::$disable_restrictions = true; // disable other restrictions
			}
			//add filters to add extra manual booking stuff
			add_filter('em_booking_get_post', array( get_called_class(), 'em_booking_get_post' ), 1, 2 );
			add_filter('em_booking_validate', array( get_called_class(), 'em_booking_validate' ), 9, 2 ); //before EM_Bookings_Form hooks in
			add_filter('em_booking_save', array( get_called_class(), 'em_booking_save' ), 10, 2 );
			//set flag that we're manually booking here, and set gateway to offline
			if( empty($_REQUEST['person_id']) || $_REQUEST['person_id'] < 0 ){
				EM_Bookings::$force_registration = true;
			}
		}
	}
	
	/**
	 * Hooks into the em_booking_save filter and checks whether a partial or full payment has been submitted
	 * @param boolean $result
	 * @param \EM_Booking $EM_Booking
	 */
	public static function em_booking_save( $result, $EM_Booking ){
		if( $result && wp_verify_nonce($_REQUEST['manual_booking'], 'em_manual_booking_'.$EM_Booking->event_id) ){
			if ( !empty($_REQUEST['payment_full']) ) {
				$price = $EM_Booking->get_price();
				Gateway::record_transaction($EM_Booking, $price, get_option('dbem_bookings_currency'), current_time('mysql'), '', 'Completed', __('Manual booking.', 'em-pro'));
				$EM_Booking->set_status(1, false);
			} elseif (!empty($_REQUEST['payment_amount']) && is_numeric($_REQUEST['payment_amount'])) {
				Gateway::record_transaction($EM_Booking, $_REQUEST['payment_amount'], get_option('dbem_bookings_currency'), current_time('mysql'), '', 'Completed', __('Manual booking.', 'em-pro'));
				if ($_REQUEST['payment_amount'] >= $EM_Booking->get_price()) {
					$EM_Booking->set_status(1, false);
				}
			} else {
				// we still currently process manual bookings via offline gateway (to be changed), so we restore the booking status to what it should be without gateways
				$booking_status = !get_option('dbem_bookings_approval') || !empty($_REQUEST['manual_booking_confirm']) ? 1:0;
				$EM_Booking->set_status($booking_status, false);
			}
			add_filter('em_action_booking_add', array(get_called_class(), 'em_action_booking_add') );
		}
		return $result;
	}
	
	public static function em_action_booking_add( $feedback ){
		$add_txt = '<a href="'.em_wp_get_referer().'"">'.__('Add another booking','em-pro').'</a>';
		$feedback["message"] = esc_html__emp('Booking Successful') .'<br><br>'. $add_txt;
		return $feedback;
	}
	
	public static function em_booking_validate($result, $EM_Booking){
		if( wp_verify_nonce($_REQUEST['manual_booking'], 'em_manual_booking_'.$EM_Booking->event_id) ){
			//validate post
			if( !empty($_REQUEST['payment_amount']) && !is_numeric($_REQUEST['payment_amount'])){
				$result = false;
				$EM_Booking->add_error( 'Invalid payment amount, please provide a number only.', 'em-pro' );
			}
			if( !empty($_REQUEST['person_id']) ){
				//@todo allow users to update user info during manual booking
				add_filter('option_dbem_emp_booking_form_reg_input', '__return_false');
				//impose double bookings here, because earlier we had to disable it due to the fact that the logged in admin is checked for double booking rather than represented user
				remove_all_actions('pre_option_dbem_bookings_double'); //so we don't get a you're already booked here message
				if( !get_option('dbem_bookings_double') && $EM_Booking->get_event()->get_bookings()->has_booking($_REQUEST['person_id']) ){
					$result = false;
					$EM_Booking->add_error( get_option('dbem_booking_feedback_already_booked') );
				}
			}
		}
		return $result;
	}
	
	/**
	 * @param boolean $result
	 * @param \EM_Booking $EM_Booking
	 */
	public static function em_booking_get_post( $result, $EM_Booking ){
		if( $result && wp_verify_nonce($_REQUEST['manual_booking'], 'em_manual_booking_'.$EM_Booking->event_id) ){ // additional check for concurrent booking manipulation - remove in future
			if( !empty($_REQUEST['person_id']) ){
				$person = new EM_Person($_REQUEST['person_id']);
				if( !empty($person->ID) ){
					$EM_Booking->person = $person;
					$EM_Booking->person_id = $person->ID;
				}
			}elseif( get_option('dbem_bookings_registration_disable') ){
				//for no-user bookings mode we circumvent
				$EM_Booking->person = new EM_Person(0);
				$EM_Booking->person_id = 0;
			}
		}
		return $result;
	}
	
	/**
	 * Called before EM_Forms fields are added, when a manual booking is being made
	 */
	public static function em_booking_form_custom(){
		global $wpdb;
		?>
		<p>
			<?php
			$person_id = (!empty($_REQUEST['person_id'])) ? $_REQUEST['person_id'] : false;
			//get consent info for each user, for use later on
			$user_consents_raw = $wpdb->get_results("SELECT user_id, meta_value FROM " . $wpdb->usermeta . " WHERE meta_key='em_data_privacy_consent' GROUP BY user_id");
			$user_consents = array();
			foreach( $user_consents_raw as $user_consent ) $user_consents[$user_consent->user_id] = $user_consent->meta_value;
			//output list of users
			$users = get_users( array( 'orderby' => 'display_name', 'order' => 'ASC', 'fields' => array('ID','display_name','user_login') ) );
			if( !empty( $users ) ){
				$placeholder = esc_html__( "Select a user (type to search), or enter a new one below.", 'em-pro' );
				$selectized = apply_filters('em_gateway_offline_select_user_manual_booking', 'em-selectize');
				echo '<select name="person_id" id="person_id" class="'. $selectized .'" placeholder="'.$placeholder.'">';
				echo "\t<option value=''>" . $placeholder . "</option>\n";
				foreach ( (array) $users as $user ) {
					$display = sprintf( _x( '%1$s (%2$s)', 'user dropdown' ), $user->display_name, $user->user_login );
					$_selected = selected( $user->ID, $person_id, false );
					$consented = !empty($user_consents[$user->ID]) ? 1:0;
					echo "\t<option value='$user->ID' data-consented='$consented'$_selected>" . esc_html( $display ) . "</option>\n";
				}
				echo '</select>';
			}
			//wp_dropdown_users ( array ('name' => 'person_id', 'show_option_none' => __ ( "Select a user, or enter a new one below.", 'em-pro' ), 'selected' => $person_id  ) );
			?>
		</p>
		<?php
	}
	
	/**
	 * Called instead of the filter in EM_Gateways if a manual booking is being made
	 * @param EM_Event $EM_Event
	 */
	public static function em_booking_form_footer($EM_Event){
		if( $EM_Event->can_manage('manage_bookings','manage_others_bookings') ){
			//Admin is adding a booking here, so let's show a different form here.
			?>
			<input type="hidden" name="manual_booking" value="<?php echo wp_create_nonce('em_manual_booking_'.$EM_Event->event_id); ?>" />
			<p class="em-booking-gateway" id="em-booking-gateway">
				<label><?php _e('Amount Paid','em-pro'); ?></label>
				<input type="text" name="payment_amount" id="em-payment-amount" value="<?php if(!empty($_REQUEST['payment_amount'])) echo esc_attr($_REQUEST['payment_amount']); ?>">
			</p>
			<p class="input-group input-checkbox input-manual-fully-paid">
				<label>
					<input type="checkbox" name="payment_full" id="em-payment-full" value="1">
					<?php _e('Fully Paid','em-pro'); ?>
				</label>
				<em><?php _e('If you check this as fully paid, and leave the amount paid blank, it will be assumed the full payment has been made.' ,'em-pro'); ?></em>
			</p>
			<?php if( get_option('dbem_bookings_approval') ): ?>
				<p class="input-group input-checkbox input-manual-payment-status">
					<label style="width:100%;">
						<input type="checkbox" name="manual_booking_confirm" value="1" checked>
						<?php _e('Confirm Booking?','em-pro'); ?>
					</label>
					<em><?php _e('If you check this, the booking will be marked as confirmed automatically.' ,'em-pro'); ?></em>
				</p>
			<?php endif; ?>
			<p class="input-group input-checkbox">
				<label style="width:100%;">
					<input type="checkbox" name="manual_booking_override" value="1">
					<?php _e('Override any restrictions to ticket availability and limits (this may lead to overbooking).','em-pro'); ?>
				</label>
			</p>
			<?php
		}
	}
	
	/**
	 * Verification of whether current page load is for a manual booking or not. If $new_registration is true, it will also check whether a new user registration
	 * is being requested and return true or false depending on both conditions being met.
	 * @param boolean $new_registration
	 * @return boolean
	 */
	public static function is_manual_booking( $new_registration = true ){
		if( !empty($_REQUEST['manual_booking']) && wp_verify_nonce($_REQUEST['manual_booking'], 'em_manual_booking_'.$_REQUEST['event_id']) ){
			if( $new_registration ){
				return empty($_REQUEST['person_id']) || $_REQUEST['person_id'] < 0;
			}
			return true;
		}
		return false;
	}
}
Bookings::init();