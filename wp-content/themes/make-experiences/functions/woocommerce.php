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

add_filter( 'woocommerce_billing_fields', 'wps_remove_filter_phone', 10, 1 );
function wps_remove_filter_phone( $address_fields ) {
  $address_fields['billing_phone']['required'] = false;
  return $address_fields;
}

/*
// Add some extra checkout fields
add_filter( 'woocommerce_checkout_fields' , 'add_custom_checkout_fields' );
// Our hooked in function – $fields is passed via the filter!
function add_custom_checkout_fields( $fields ) {
	foreach( WC()->cart->get_cart() as $cart_item ){
	    $product_id = $cart_item['product_id'];
		// only add these extra fields if there's school maker faire in the cart
		if($product_id == "8624") {
		    $fields['billing']['member_email'] = array(
		    	'label'     	=> __('Member Email', 'woocommerce'),
			    'placeholder'   => _x('Email', 'placeholder', 'woocommerce'),
			    'required'  	=> true,
			    'class'     	=> array('form-row-wide'),
			    'clear'     	=> true
		    );
			$fields['billing']['member_first_name'] = array(
		    	'label'     	=> __('Member First Name', 'woocommerce'),
			    'placeholder'   => _x('First Name', 'placeholder', 'woocommerce'),
			    'required'  	=> true,
			    'class'     	=> array('form-row-wide'),
			    'clear'     	=> true
		    );
			$fields['billing']['member_last_name'] = array(
		    	'label'     	=> __('Member Last Name', 'woocommerce'),
			    'placeholder'   => _x('Last Name', 'placeholder', 'woocommerce'),
			    'required'  	=> true,
			    'class'     	=> array('form-row-wide'),
			    'clear'     	=> true
		    );

		    return $fields;
		 }
	 }
}

add_action( 'woocommerce_form_field_text','checkout_custom_headings', 10, 2 );
function checkout_custom_headings( $field, $key ){
    // will only execute if the field is member_email and we are on the checkout page...
    if ( is_checkout() && ( $key == 'member_email') ) {
		foreach( WC()->cart->get_cart() as $cart_item ){
		    $product_id = $cart_item['product_id'];
			// only add these extra text if there's school maker faire in the cart
			if($product_id =="8624") {
				$field = '<p class="form-row form-row-wide">Enter the Email, First Name and Last Name you\'d like associated with the School Makerfaire registration below. If you do not have a membership already, one will be created for you associated with this email as well.</p>' . $field;
			}
		}
    }
    return $field;
}

//Display order custom fields in the admin as well
add_action( 'woocommerce_admin_order_data_after_shipping_address', 'custom_checkout_field_display_admin_order_meta', 10, 1 );
function custom_checkout_field_display_admin_order_meta($order){
    echo '<p><strong>'.__('Member Email').':</strong> ' . get_post_meta( $order->get_id(), 'member_email', true ) . '</p><span>Changing this won\'t change the member\'s email. Their Membership has been created already.';
}

add_action('woocommerce_checkout_update_order_meta', 'custom_checkout_field_update_order_meta', 10, 2 );
function custom_checkout_field_update_order_meta( $order_id ) {
	$order = wc_get_order( $order_id );
	$items = $order->get_items();
	foreach ( $items as $item ) {
	    $product_id = $item->get_product_id();
		// only do this if we're purchasing a school maker faire registration
		if($product_id == "8624") {
			update_post_meta( $order_id, 'member_first_name', esc_attr($_POST['member_first_name']));
			update_post_meta( $order_id, 'member_last_name', esc_attr($_POST['member_last_name']));
			update_post_meta( $order_id, 'member_email', esc_attr($_POST['member_email']));
		}
	}
}*/

// add membership when order is completed
add_action( 'woocommerce_payment_complete', 'woocommerce_add_membership', 10, 2 );
function woocommerce_add_membership( $order_id ){
    $order = wc_get_order( $order_id );
	$items = $order->get_items();
	foreach ( $items as $item ) {
	    $product_id = $item->get_product_id();
		// only do this if we're purchasing a school maker faire registration
		if($product_id == "8624") {
			if( !$order->get_user() ){
				// Although we are taking new fields, these fields are not going into the order's post meta
				$user_id = create_new_user('Welcome to Make: Community', $order->get_billing_first_name(), $order->get_billing_last_name(), 'Thank you for registering for our School Maker Faire Program.  Included with your purchase is a free membership to Make: Community. This is where you will find the event information, resources and community.  Please login to access Make: Community and your School Maker Faire', $order->get_billing_email());
				//$user_id = create_new_user('Welcome to Make: Community', get_post_meta($order_id, 'member_first_name', true ), get_post_meta( $order_id, 'member_last_name', true ), 'Thank you for registering for our School Maker Faire Program.  Included with your purchase is a free membership to Make: Community. This is where you will find the event information, resources and community.  Please login to access Make: Community and your School Maker Faire', get_post_meta( $order_id, 'member_email', true ));
				bp_set_member_type( $user_id, 'member742' );
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
