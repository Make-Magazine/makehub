<?php

/**
* Add custom fields to the checkout page
*/
add_action('woocommerce_before_order_notes', 'custom_checkout_fields');
function custom_checkout_fields($checkout){
	create_custom_order_field($checkout, 'additional-info', 'Is there anything you would like us to know?', 'Special needs, requirements, etc' );
	create_custom_order_field($checkout, 'how-did-you-hear', 'How did you hear about this program?', '' );
	create_custom_order_field($checkout, 'other-classes', 'What other classes would you be interested in?', '' );
}
function create_custom_order_field($checkout, $key, $label, $placeholder) {
	$field = woocommerce_form_field($key, array(
				'type' => 'text',
				'class' => array( 'custom-checkout-field form-row-wide' ),
				'label' => __($label),
				'placeholder' => __($placeholder),
			),
			$checkout->get_value($key));
	return $field;
}

/**
* Update the value given in custom fields on the order
*/
add_action('woocommerce_checkout_update_order_meta', 'custom_checkout_field_update_order_meta');
function custom_checkout_field_update_order_meta($order_id){
	if (!empty($_POST['additional-info'])) {
		update_post_meta($order_id, 'additional-info-field', sanitize_text_field($_POST['additional-info']));
	}
	if (!empty($_POST['how-did-you-hear'])) {
		update_post_meta($order_id, 'how-did-you-hear-field', sanitize_text_field($_POST['how-did-you-hear']));
	}
	if (!empty($_POST['other-classes'])) {
		update_post_meta($order_id, 'other-classes-field', sanitize_text_field($_POST['other-classes']));
	}
}

/**
 * Display field values on the order edit page
 */
add_action( 'woocommerce_admin_order_data_after_billing_address', 'custom_checkout_field_display_admin_order_meta', 10, 1 );
function custom_checkout_field_display_admin_order_meta( $order ){
    $order_id = method_exists( $order, 'get_id' ) ? $order->get_id() : $order->id;
    echo '<p><strong>'.__('Is there anything you would like us to know?').':</strong> ' . get_post_meta( $order_id, 'additional-info-field', true ) . '</p>';
	echo '<p><strong>'.__('How did you hear about this program?').':</strong> ' . get_post_meta( $order_id, 'how-did-you-hear-field', true ) . '</p>';
	echo '<p><strong>'.__('What other classes would you be interested in?').':</strong> ' . get_post_meta( $order_id, 'other-classes-field', true ) . '</p>';
}

/**
 * Add custom fields (in an order) to the emails
 */
add_filter( 'woocommerce_email_customer_details_fields', 'custom_woocommerce_email_order_meta_fields', 10, 3 );
function custom_woocommerce_email_order_meta_fields( $fields, $sent_to_admin, $order ) {
    $fields['additional-info-field'] = array(
        'label' => __( 'Is there anything you would like us to know?' ),
        'value' => get_post_meta( $order->id, 'additional-info-field', true ),
    );
	$fields['how-did-you-hear-field'] = array(
        'label' => __( 'How did you hear about this program?' ),
        'value' => get_post_meta( $order->id, 'how-did-you-hear-field', true ),
    );
	$fields['other-classes-field'] = array(
        'label' => __( 'What other classes would you be interested in?' ),
        'value' => get_post_meta( $order->id, 'other-classes-field', true ),
    );
    return $fields;
}
