<?php

/**
  * Filter for testing https://events.codebasehq.com/projects/event-espresso/tickets/7056
  * This filter tests different date and time formats used for the ticket editor in the event editor.
  * To use:
  * 1. Activate this plugin
  * 2. Comment out the below filter and callback
  * 3. Modify the date and time formats in the callback to be various things for testing
  * 4. Visit the event editor and see that dates are dispayed in the set format, also verify the datepicker uses that format.
  * 5. Verify that functionality with this format for creating, editing, and saving dates and times in this format works as expected.
  */
/*function ee_new_dtt_formats( $formats ) {
	return array(
		'date' => 'd F, Y',
		'time' => 'H:i'
		);
}
add_filter( 'FHEE__espresso_events_Pricing_Hooks___set_hooks_properties__date_format_strings', 'ee_new_dtt_formats' );/**/


/**
 * Filter for testing https://events.codebasehq.com/projects/event-espresso/tickets/7595
 * This is used to add a bogus message type to the default message types array when a messenger is
 * activated.  It helps verify that activating the messages system does not result in errors on activation.
 * To use:
 * 1. Start with an install of WordPress where EE has never been active (or the db is wiped of all traces of EE).
 * 2. Activate this plugin.
 * 3. Activate Event Espresso.
 * 4. Deactivate and reactivate Event Espresso.
 * 5. There should be no errors.
 */
/*add_filter( 'FHEE__EE_messenger__get_default_message_types__default_types', function( $default_types, $messenger ) {
      $default_types[] = 'bogus_message_type';
      return $default_types;
    }, 10, 2);/**/


/**
 * Below actions are for the email messenger wrapper template.  Uncomment to test all the actions when sending an email for the
 * EE_Registration_message_type.  Emails for other message types should NOT show any changes.
 */
/*add_action( 'AHEE__EE_Email_Messenger_main_wrapper_template_head', function( $message_type, $subject, $from, $main_body ) {
	if ( ! $message_type instanceof EE_Registration_message_type ) {
		return;
	} else {
		echo '<meta property="testing-filter" content="passed">';
	}
}, 10, 4 );*/
add_action( 'AHEE__EE_Email_Messenger_main_wrapper_template_header', function( $message_type, $subject, $from, $main_body ) {
	$header = '<div width="100%" bgcolor="#FAFBFD" style="margin:0;background-color:#FAFBFD;padding-bottom: 100px;">
				<table role="presentation" style="max-width:600px;margin:0 auto;" width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
					<tbody><tr>
						<td style="text-align: left; padding: 50px 0 30px 0; font-family: sans-serif; mso-height-rule: exactly; font-weight: bold; color: #122B46; font-size: 20px" class="site_title_text_color site_title_text_size">
							<img src="https://'. $_SERVER['HTTP_HOST'] .'/wp-content/themes/make-experiences/images/makercampus-logo-square.png" alt="Maker Campus" style="margin:0; padding:0; border:none; display:block; max-height:auto; height:auto; width:180px;" border="0">				
						</td>
					</tr></tbody>
				</table>';
	echo $header;
}, 10, 4 );
add_action( 'AHEE__EE_Email_Messenger_main_wrapper_template_before_main_body', function( $message_type, $subject, $from, $main_body ) {
	echo '<table role="presentation" style="margin:0 auto;border-collapse:separate!important;max-width:600px;border-radius:5px;border:1px solid #e7e9ec" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#FFFFFF" align="center"><tbody><tr>';
}, 10, 4 );
add_action( 'AHEE__EE_Email_Messenger_main_wrapper_template_after_main_body', function( $message_type, $subject, $from, $main_body ) {
	echo '</tr></tbody></table>';
}, 10, 4 );
add_action( 'AHEE__EE_Email_Messenger_main_wrapper_template_footer', function( $message_type, $subject, $from, $main_body ) {
	$footer =  '<table role="presentation" style="margin: 0 auto;max-width: 600px; border-radius: 5px;" width="100%" cellspacing="0" cellpadding="0" border="0" align="left">
			<tbody><tr>
				<td style="padding: 20px 40px; width: 100%; font-size: 12px; font-family: sans-serif; mso-height-rule: exactly; line-height: 19px; text-align: center; color: #7F868F;" class="footer_text_color footer_text_size repsonsive-padding">
					<span class="footer_text">© 2021 Make.co</span>
				</td>
			</tr>
			<tr>
				<td style="font-size: 45px; line-height: 45px;" height="45px">&nbsp;</td>
			</tr>
		</tbody></table>
		</div>';
	echo $footer;
}, 10, 4 );

/**
 * Below actions are for the html messenger wrapper template.  Uncomment to test all the actions when sending an email for the
 * EE_Receipt_message_type.  Emails for other message types should NOT show any changes.
 
add_action( 'AHEE__EE_Html_Messenger_main_wrapper_template_head', function( $message_type, $subject, $from, $main_body ) {
	if ( ! $message_type instanceof EE_Receipt_message_type ) {
		return;
	} else {
		echo '<meta property="testing-filter" content="passed">';
	}
}, 10, 4 );
add_action( 'AHEE__EE_Html_Messenger_main_wrapper_template_header', function( $message_type, $subject, $from, $main_body ) {
	if ( ! $message_type instanceof EE_Receipt_message_type ) {
		return;
	} else {
		echo '<header><p>Heya this be the header yo!</p></header>';
	}
}, 10, 4 );
add_action( 'AHEE__EE_Html_Messenger_main_wrapper_template_before_main_body', function( $message_type, $subject, $from, $main_body ) {
	if ( ! $message_type instanceof EE_Receipt_message_type ) {
		return;
	} else {
		echo '<p>HEY YO this be before the main body!</p>';
	}
}, 10, 4 );
add_action( 'AHEE__EE_Html_Messenger_main_wrapper_template_after_main_body', function( $message_type, $subject, $from, $main_body ) {
	if ( ! $message_type instanceof EE_Receipt_message_type ) {
		return;
	} else {
		echo '<p>HEY YO this be after the main body!</p>';
	}
}, 10, 4 );
add_action( 'AHEE__EE_Html_Messenger_main_wrapper_template_footer', function( $message_type, $subject, $from, $main_body ) {
	if ( ! $message_type instanceof EE_Receipt_message_type ) {
		return;
	} else {
		echo '<footer><p>HEY YO this be the footer!</p></footer>';
	}
}, 10, 4 );
*/