<?php
function add_universal_body_classes($classes) {
	// let's get the subdomain in to the body class
	global $current_blog;
	$domain = strtok($current_blog->domain, ".");
	if($domain == "www" || $domain == "make" || $domain == "devmakehub" || $domain == "stagemakehub") {
		$domain = "makeco";
	}
	$classes[] = $domain;
	
	// add class for premium member vs free member
	if(class_exists('MeprUtils')) { 
		$user = wp_get_current_user();      
		$member = new MeprUser(); // initiate the class
		$member->ID = $user->ID; // if using this in admin area, you'll need this to make user id the member id
		$memLevel = $member->get_active_subscription_titles(); //MeprUser function that gets subscription title    
		
		if($memLevel==''){
			$membershipType = 'none';
		}elseif(stripos($memLevel, 'premium') !== false ||
			stripos($memLevel, 'multi-seat') !== false || 
			stripos($memLevel, 'school maker faire') !== false ) {
			//Premium Membership
			$classes[] = 'premium-member';
		}else{
			//free membership
			$classes[] = 'free-member';
		}
	}
	return $classes;
}
add_filter('body_class', 'add_universal_body_classes', 999, 1);
