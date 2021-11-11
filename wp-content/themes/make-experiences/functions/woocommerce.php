<?php

// Remove Empty Tabs
add_filter( 'woocommerce_product_tabs', 'woo_remove_empty_tabs', 20, 1 );
function woo_remove_empty_tabs( $tabs ) {
	if ( ! empty( $tabs ) ) {
		foreach ( $tabs as $title => $tab ) {
			if ( empty( $tab['content'] ) ) {
				unset( $tabs[ $title ] );
			}
		}
	}
	return $tabs;
}

// add membership when order is completed
add_action( 'woocommerce_payment_complete', 'woocommerce_add_membership' );
function woocommerce_add_membership( $order_id ){
    $order = wc_get_order( $order_id );
	$items = $order->get_items();
	foreach ( $items as $item ) {
	    $product_name = $item->get_name();
		// only do this if we're purchasing a school maker faire registration
		if($product_name == "School Maker Faire Registration") {
			if( !$order->get_user() ){
				$customer_email = $order->get_billing_email();
				//create a user
				$username = strstr($customer_email, '@', true); //first try username being the first part of the email
				if(username_exists( $username )){  //username exists try something else
					$count=1;
					$exists = true;
					while($exists){
						$username = $username.$count;
						if(!username_exists($username)){
							$exists = false;
						}
						$count++;
					}
				}
				//generate random password, create user, send email
				$random_password = wp_generate_password( 12, false );
				$user_id = wp_create_user( $username, $random_password, $customer_email, );
				update_user_meta( $user_id, 'first_name', $order->get_billing_first_name() );
				update_user_meta( $user_id, 'last_name', $order->get_billing_last_name() );

				$subject = 'Welcome to Make: Community.';
				$my_groups = CURRENT_URL . '/wp-login.php?redirect_to=' . CURRENT_URL . '/members/me/groups/';
				$message = 'Hello ' . $order->get_billing_first_name() .', <br /><br />Thank you for registering for our School Maker Faire Program.  Included with your purchase is a free membership to Make: Community. This is where you will find the event information, resources and community.  Please login to access Make: Community and your School Maker Faire <a href="'. $my_groups .'">event group</a>. <br /><br />
		<b>Email:</b> ' . $customer_email . '<br />
		<b>Temporary Password:</b> ' . $random_password;
				$headers = 'Content-Type: text/html; charset=ISO-8859-1' . '\r\n';
				$headers .= 'From: Make: Community <make@make.co>' . '\r\n';
				wp_mail($customer_email, $subject, $message, $headers );
				assign_schoolmakerfaire_level($user_id);
			} else {
				$user = $order->get_user();
				assign_schoolmakerfaire_level($user->ID);
			}
		}
	}

}

function assign_schoolmakerfaire_level($user_id) {
	// give them a school maker faire membership
	//$assignLevel = basicCurl(IHC_URL .'apigate.php?ihch=z4BVmZKKzCCDoSFmgil5XUoDHU&action=user_add_level&uid='.$user_id.'&lid=18');
	//$activateLevel = basicCurl(IHC_URL .'apigate.php?ihch=z4BVmZKKzCCDoSFmgil5XUoDHU&action=user_activate_level&uid='.$user_id.'&lid=18');
	$assignLevel = \Indeed\Ihc\UserSubscriptions::assign( $user_id, 18 );
	$activateLevel = \Indeed\Ihc\UserSubscriptions::makeComplete( $user_id, 18 );
	// add them to the school maker faire group
	groups_join_group( 152, $user_id);
}
