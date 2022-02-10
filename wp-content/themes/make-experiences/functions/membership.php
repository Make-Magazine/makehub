<?php

function addFreeMembership($email, $userName, $firstName, $lastName, $membership, $sendWelcomeEmail = true, $expiresAt = '0000-00-00 00:00:00') {
	$url = CURRENT_URL . '/wp-json/mp/v1/members';

	$datastring = json_encode(
	  [
		'first_name'          => $firstName,
  	    'last_name'           => $lastName,
	    'email'               => $email,
	    'username'            => $userName,
	    'send_welcome_email'  => $sendWelcomeEmail,
	    'transaction'         => [
	      'membership'  => $membership, // ID of the Membership
	      'amount'      => '0.00',
	      'total'       => '0.00',
	      'tax_amount'  => '0.00',
	      'tax_rate'    => '0.000',
	      'trans_num'   => 'mp-txn-'.uniqid(),
	      'status'      => 'complete',
	      'gateway'     => 'free',
	      'created_at'  => date("Y-m-d H:i:s"),
	      'expires_at'  => $expiresAt
	    ]
	  ]
	);

	$headers = setMemPressHeaders($datastring);

	postCurl($url, $headers, $datastring);
}
function setMemPressHeaders($datastring = null) {
	$headers = array();
	$headers[] = 'MEMBERPRESS-API-KEY: FGLzhqujP4'; // Your API KEY from MemberPress Developer Tools Here -- 0n8p2YkomO for local FGLzhqujP4 for stage
	$headers[] = 'Content-Type: application/json';
	if($datastring){
		$headers[] = 'Content-Length: ' . strlen($datastring);
	}
	return $headers;
}

// Remove Member Press->Info SubMenu and make the subscriptions subnav item the default
function change_memberpress_subnav(){
	global $bp;
	if(class_exists('MpBuddyPress')){
		$mp_buddyboss = new MpBuddyPress;
		if ( $bp->current_component == 'mp-membership' ) {
			bp_core_remove_subnav_item( 'mp-membership', 'mp-info' );
			bp_core_new_nav_default (
						array(
								'parent_slug'       => 'mp-membership',
								'subnav_slug'       => 'mp-subscriptions',
								'screen_function'   => $mp_buddyboss->membership_subscriptions()
						)
				);
		}
	}
}
add_action( 'wp', 'change_memberpress_subnav', 5 );

// add the users membership levels to the body class so specific pages can be styled differently based on membership
function add_membership_class_profile($classes) {
	foreach (CURRENT_MEMBERSHIPS as $membership) {
	    $classes[] = "member-level-" . strtolower($membership);
	}
    return $classes;
}
add_filter('body_class', 'add_membership_class_profile', 12);

// Take all the membership fields for a new member and add them to the xprofile buddyboss fields
function mepr_capture_new_member_added($event) {
	$user = $event->get_data();
	$userInfo = $user->rec;
	xprofile_set_field_data("Address", $userInfo->ID, get_user_meta( $userInfo->ID, 'mepr-address-one', true));
	xprofile_set_field_data("Address 2", $userInfo->ID, get_user_meta( $userInfo->ID, 'mepr-address-two', true));
	xprofile_set_field_data("City", $userInfo->ID, get_user_meta( $userInfo->ID, 'mepr-address-city', true));
	xprofile_set_field_data("State / Province", $userInfo->ID, get_user_meta( $userInfo->ID, 'mepr-address-state', true));
	xprofile_set_field_data("Country", $userInfo->ID, get_user_meta( $userInfo->ID, 'mepr-address-country', true));
	xprofile_set_field_data("Zip", $userInfo->ID, get_user_meta( $userInfo->ID, 'mepr-address-zip', true));
	xprofile_set_field_data("Testimonial", $userInfo->ID, get_user_meta( $userInfo->ID, 'mepr_testimonial', true));
	xprofile_set_field_data("I wish to remain anonymous and opt out of the Member Directory ", $userInfo->ID, get_user_meta( $userInfo->ID, 'mepr_i_wish_to_remain_anonymous_and_opt_out_of_the_member_directory', true));
}
add_action('mepr-event-member-signup-completed', 'mepr_capture_new_member_added', 12);
