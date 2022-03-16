<?php

/////////////////////////////////////////
//       Membership Card Widget       //
////////////////////////////////////////
function return_membership_widget($user){
	global $user_slug;
	$return = '';
	$user_id   = $user->ID;
	$user_meta = get_user_meta($user_id);

	if ( isset($user_meta['ihc_user_levels'][0]) && class_exists( '\Indeed\Ihc\UserSubscriptions' ) ) {
		$return  = '<div class="dashboard-box expando-box">';
		$return .= '  <h4 class="open"><img style="max-width:100px;" src="'. get_stylesheet_directory_uri().'/images/make-community-logo.png" /> Membership Details</h4>';
		$return .= '  <ul class="open">';
		$return .= '    <li>'. do_shortcode('[ihc-membership-card]').'</li>';
		$return .= '    <li><a href="/members/'. $user_slug . '/membership/" class="btn universal-btn">See More Details</a></li>';
		$return .= '  </ul>';
		$return .= '</div>';
	} else if( class_exists('MeprUtils') ) {
		$mepr_current_user = MeprUtils::get_currentuserinfo();

	    $sub_cols = array('id','user_id','product_id','product_name','subscr_id','status','created_at','expires_at','active');
		$user = bp_get_displayed_user();

	    $table = MeprSubscription::account_subscr_table(
	      'created_at', 'DESC',
	      1, '', 'any', 0, false,
	      array(
	        'member' => $mepr_current_user->user_login,
	      ),
	      $sub_cols
	    );
	    $subscriptions = $table['results'];
		foreach($subscriptions as $subscription) {
			if($subscription->status == "active" || $subscription->status == "None") {
				$subscribe_date = date("Y/m/d H:i:s", strtotime($subscription->created_at));
				$expire_date = isset($subscription->expires_at) ? date("Y/m/d H:i:s", strtotime($subscription->expires_at)) : 'Never';
				if($expire_date == "-0001/11/30 00:00:00") { $expire_date = "Never"; }
				$return  = '<div class="dashboard-box"">';
				$return .= '  <h4><img style="max-width:100px;" src="'. get_stylesheet_directory_uri().'/images/make-community-logo.png" /> Membership Details</h4>';
				$return .= '  <div class="membership-card">';
				$return .= '        <img src="https://make.co/wp-content/uploads/2021/06/make_logo-2.svg" alt="'.$user->fullname.'\'s Membership Card">';
				$return .= '        <h3 class="mebr-name">' . $user->fullname . '</h3>';
				$return .= '        <div class="mebr-membership"><label>Level:</label> ' . $subscription->product_name .'</div>';
				$return .= '        <div class="mebr-startdate"><label>Member Since:</label> ' . $subscribe_date . '</div>';
				$return .= '        <div class="mebr-expiredate"><label>Expiration Date:</label> ' . $expire_date . '</div>';
				$return .= '  </div>';
				$return .= '</div>';
			}
		}
	}
	return $return;
}
