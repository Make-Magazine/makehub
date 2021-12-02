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
	    $product_id = $item->get_product_id();
		// only do this if we're purchasing a school maker faire registration
		if($product_id == "8624") {
			if( !$order->get_user() ){
				create_new_user('Welcome to Make: Community', $order->get_billing_first_name(), $order->get_billing_last_name(), 'Thank you for registering for our School Maker Faire Program.  Included with your purchase is a free membership to Make: Community. This is where you will find the event information, resources and community.  Please login to access Make: Community and your School Maker Faire', $order->get_billing_email());
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
	$assignLevel = \Indeed\Ihc\UserSubscriptions::assign( $user_id, 18 );
	$activateLevel = \Indeed\Ihc\UserSubscriptions::makeComplete( $user_id, 18 );
	// add them to the school maker faire group
	groups_join_group( 152, $user_id);
}
