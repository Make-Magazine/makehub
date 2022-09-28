<?php
function addFreeMembership($email, $userName, $firstName, $lastName, $membership, $sendWelcomeEmail = true, $expiresAt = '0000-00-00 00:00:00', $price = '0.00') {
	$user = get_user_by('email', $email);
	$url = NETWORK_HOME_URL . '/wp-json/mp/v1/transactions';

	$datastring = json_encode(
	  [
		  'member'		=> $user->ID,
	      'membership'  => $membership, // ID of the Membership
	      'amount'      => $price,
	      'total'       => $price,
	      'tax_amount'  => '0.00',
	      'tax_rate'    => '0.000',
	      'trans_num'   => 'mp-txn-'.uniqid(),
	      'status'      => 'complete',
	      'gateway'     => 'free',
	      'created_at'  => date("Y-m-d H:i:s"),
	      'expires_at'  => $expiresAt
	  ]
	);

	$headers = setMemPressHeaders($datastring);

	postCurl($url, $headers, $datastring);
}

// add the users membership levels to the body class so specific pages can be styled differently based on membership
function add_membership_class_profile($classes) {
	foreach (CURRENT_MEMBERSHIPS as $membership) {
	    $classes[] = "member-level-" . str_replace(' ', '-',strtolower($membership));
	}
    return $classes;
}
add_filter('body_class', 'add_membership_class_profile', 12);

// Rename the Info subnav tab for
function change_profile_submenu_tabs(){
	global $bp;
	$bp->bp_options_nav['mp-membership']['mp-info']['name'] = 'Membership Details';
}
add_action('bp_setup_nav', 'change_profile_submenu_tabs', 999);

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
	xprofile_set_field_data("I wish to remain anonymous and opt out of the Member Directory", $userInfo->ID, get_user_meta( $userInfo->ID, 'mepr_i_wish_to_remain_anonymous_and_opt_out_of_the_member_directory', true));
}
add_action('mepr-event-member-signup-completed', 'mepr_capture_new_member_added', 12);

/*
 * Only list active MemberPress members in the members directory.
 * sources:
 * https://buddydev.com/hiding-users-on-buddypress-based-site
 * https://buddypress.org/support/topic/member-loop-only-memberpress-members/
 * @param array $args args.
 *
 * @return array

function tmp_only_show_members_in_directory( $args ) {
    // do not exclude in admin.
    if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
        return $args;
    }
    global $wpdb;
    $member_ids = $wpdb->get_col("SELECT DISTINCT user_id FROM ".$wpdb->prefix."mepr_transactions WHERE status IN('confirmed','complete') AND (expires_at >= NOW() OR expires_at = '0000-00-00 00:00:00')");
    $args['include'] = $member_ids;

    return $args;
}
add_filter( 'bp_after_has_members_parse_args', 'tmp_only_show_members_in_directory' );
*/

/* Exclude opted out Members from BuddyPress Members List. */
function bp_exclude_users( $qs = '', $object = '' ) {
	global $wpdb;
    // list of users to exclude.
	$optOutFieldID = xprofile_get_field_id_from_name( 'I wish to remain anonymous and opt out of the Member Directory' );
	$excluded_users = $wpdb->get_col("SELECT user_id FROM wp_bp_xprofile_data WHERE field_id = $optOutFieldID AND value LIKE '%on%'");
    if ( $object != 'members' ) {
        return $qs;
    }
    $args = wp_parse_args( $qs );
    // check if we are listing friends?, do not exclude in this case.
    if ( ! empty( $args['user_id'] ) ) {
        return $qs;
    }
    if ( ! empty( $args['exclude'] ) ) {
        $args['exclude'] = $args['exclude'] . ',' . $excluded_users;
    } else {
        $args['exclude'] = $excluded_users;
    }
    $qs = build_query( $args );
    return $qs;
}

add_action( 'bp_ajax_querystring', 'bp_exclude_users', 20, 2 );

/* TBD: The init action is triggering this function multiple times, even when there is no interaction on a page
 * We need to find a better way to trigger this
 * setting of CURRENT_URL and CURRENT_POSTID should be moved to wp-config
 */
 function set_membership_constants() {
 	// Decide if user can upgrade
  $memLevels = checkMembershipLevels();
  $hasMembership = !empty($memLevels['levels'])? TRUE:'';
  $currentMemberships = $memLevels['levels'];
 	$canUpgrade = $memLevels['type']=='upgrade'?TRUE:FALSE;

 	define('CURRENT_MEMBERSHIPS', $currentMemberships);
 	define('IS_MEMBER', $hasMembership);
 	define('CAN_UPGRADE', $canUpgrade);
 }

add_action( 'init', 'set_membership_constants' );

/* Check Membership Levels for current blog
*  for current user return list of membership levels (levels) and membership type(type)
*  Note: This CANNOT call the memberpress API as it will get stuck in an init loop
*/
function checkMembershipLevels(){
  $currentMemberships=array();
  $type = 'upgrade';

  if( class_exists('MeprUtils') ) {
    $mepr_current_user = MeprUtils::get_currentuserinfo();

    if($mepr_current_user) {
      //Returns an array of Membership ID's that the current user is active on
      $active_products = $mepr_current_user->active_product_subscriptions('ids');

      if(!empty($active_products)) {
        foreach($active_products as $id) {
          $membership = new MeprProduct($id);

          $currentMemberships[] = $membership->post_title;

          //look for part of the membership title, case-insensitive. This saves us if membership titles change in the future
          if(stripos($membership->post_title, 'premium') !== false ||
             stripos($membership->post_title, 'multi-seat')  !== false ||
             stripos($membership->post_title, 'global') !== false ||
             stripos($membership->post_title, 'school') !== false ||
					 	 stripos($membership->post_title, 'magazine') !== false)  {
            $type = 'premium';
          }
        }
      }else{
        //no active membership
        $type = 'none';
      }
    } else {
      //no memberships
      $type = 'none';
    }
  }
  return array('levels'=>$currentMemberships,'type'=>$type);
}
