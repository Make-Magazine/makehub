<?php

// Remove Member Press->Info SubMenu and make the subscriptions subnav item the default
function change_memberpress_subnav(){
	global $bp;
	$mp_buddyboss = new MpBuddyPress;
	if ( $bp->current_component == 'mp-membership' ) {
		bp_core_remove_subnav_item( 'mp-membership', 'mp-info' );
		bp_core_new_nav_default( 'mp-membership' );
		bp_core_new_nav_default (
	        array(
	            'parent_slug'       => 'mp-membership',
	            'subnav_slug'       => 'mp-subscriptions',
	            'screen_function'   => $mp_buddyboss->membership_subscriptions()
	        )
	    );
	}
}
add_action( 'wp', 'change_memberpress_subnav', 5 );
