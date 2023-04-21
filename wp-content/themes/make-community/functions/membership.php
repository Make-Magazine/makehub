<?php
// This function can be called to give a user a free membership. The membership type is passed in $membership
// This function is currently unused.
function addFreeMembership($email, $userName, $firstName, $lastName, $membership, $sendWelcomeEmail = true, $expiresAt = '0000-00-00 00:00:00', $price = '0.00') {
	$user = get_user_by('email', $email);
	$url = network_home_url() . '/wp-json/mp/v1/transactions';

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
add_action( 'bp_ajax_querystring', 'bp_exclude_users', 20, 2 ); //buddypress

/**
* Filtering only users with an active membership
* @param $sql array
* @param $query BP_User_Query
* @return array
*/
function filtering_memberpress( $sql, $query ) {
    // MemberPress is Active
    if( class_exists( 'MeprDb' ) ) {
        // Get the MemberPress members database object
        $mepr_db = new MeprDb();
        // Add a JOIN to the select
        $sql['select'] .= ' LEFT JOIN ' . $mepr_db->members . ' as members on members.user_id = u.' . $query->uid_name . ' ';
        // Add a WHERE clause
        $sql['where'][] = " members.memberships != ''";
    }
    return $sql;
}
add_filter( 'bp_user_query_uid_clauses', 'filtering_memberpress', 10, 2 );

/* 
 * Set Membership Constants
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
             stripos($membership->post_title, 'school') !== false)  {
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

function make_add_disclaimer($productID){	
	echo '<p class="login-msg">If you are already a member, please <a href="https://make.co/wp-login.php?redirect_to=https://make.co/register/contest">login</a> before continuing.</p>
	<p class="check-fields-msg">Please take a moment to verify the information below is correct and up to date. Thank you!</p>';		
	return;

}
add_action('mepr-above-checkout-form', 'make_add_disclaimer', 10, 1);

function make_enqueue_scripts($is_product_page, $is_group_page, $is_account_page) {
	//The register pages do not pull in our header/footer. Because of that we need to add site styles to the checkout form
  if($is_product_page){  	  	
	  $my_theme = wp_get_theme();
	  $my_version = $my_theme->get('Version');
	  ?>
	  <link rel='stylesheet' id='make-co-style-css' href='<?php echo get_stylesheet_directory_uri();?>/css/style.min.css?ver=<?php echo $my_version;?>' type='text/css' media='all' />
	
		<!-- Is this still needed??
		<script>
			jQuery(document).ready(function(){
				if(jQuery("#profile-view .avatar").attr('src') != 'https://make.co/wp-content/universal-assets/v1/images/default-makey.png' ) {
					jQuery(".login-msg").remove();
				}
			});
		</script>-->
	<?php
  }
}
add_action('mepr_enqueue_scripts', 'make_enqueue_scripts', 10, 3);
