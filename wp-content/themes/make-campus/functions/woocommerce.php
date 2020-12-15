<?php

/**

* Add custom field to the checkout page

*/

add_action('woocommerce_before_order_notes', 'custom_checkout_fields');

function custom_checkout_fields($checkout){

	woocommerce_form_field('additional-info', array(
		'type' => 'text',
		'class' => array(
			'custom-checkout-field form-row-wide'
		),
		'label' => __('Is there anything you would like us to know?'),
	),
	$checkout->get_value('additional-info'));
	woocommerce_form_field('how-did-you-hear', array(
		'type' => 'text',
		'class' => array(
			'custom-checkout-field form-row-wide'
		),
		'label' => __('How did you hear about this program?'),
	),
	$checkout->get_value('how-did-you-hear'));
	woocommerce_form_field('other-classes', array(
		'type' => 'text',
		'class' => array(
			'custom-checkout-field form-row-wide'
		),
		'label' => __('What other classes would you be interested in?'),
	),
	$checkout->get_value('other-classes'));

}