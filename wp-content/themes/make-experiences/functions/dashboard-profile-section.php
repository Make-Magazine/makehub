<?php

/*
 * See a variety of widgets connected to your profile
 */

function profile_tab_dashboard_info_name() {
    global $bp;
    $user_id = bp_displayed_user_id();
    $type = bp_get_member_type(bp_displayed_user_id());
    if ($user_id != 0 && wp_get_current_user()->ID == $user_id ) {
        bp_core_new_nav_item(array(
            'name' => 'Dashboard',
            'slug' => 'dashboard',
            'screen_function' => 'dashboard_info_screen',
            'position' => 40,
            'parent_url' => bp_loggedin_user_domain() . '/dashboard/',
            'parent_slug' => $bp->profile->slug,
            'default_subnav_slug' => 'dashboard'
        ));
    }
}

add_action('bp_setup_nav', 'profile_tab_dashboard_info_name');

function dashboard_info_screen() {
    // Add title and content here - last is to call the members plugin.php template.
    //add_action('bp_template_title', 'membership_info_title');
    add_action('bp_template_content', 'dashboard_info_content');
    bp_core_load_template('buddypress/members/single/plugins');
}

function dashboard_info_title() {
    //echo 'Maker Faire Information';
}

function dashboard_info_content() {
    global $wpdb;

    $user_id = bp_displayed_user_id();
    //get the users email
    $user_info = get_userdata($user_id);
    $user_email = $user_info->user_email;
	
	$return = '<div class="dashboard-wrapper">';
	
	// Maker Shed Orders widget
	$api_url = 'https://4e27971e92304f98d3e97056a02045f1:32e156e38d7df1cd6d73298fb647be72@makershed.myshopify.com';
	$customer_api =  $api_url . '/admin/customers/search.json?query=email:"' . /*$user_email*/ 'ken@nmhu.edu' .'"&fields=id';
	$customer_content = basicCurl($customer_api);
	if( isset($customer_content) && !empty($customer_content) ) {
		// Decode the JSON in the file
		$customer = json_decode($customer_content, true);
		$customerID = $customer['customers'][0]['id'];
		$orders_api = $api_url . '/admin/orders.json?customer_id=' . $customerID;
		$orders_content = basicCurl($orders_api);
		$orderJson = json_decode($orders_content, true);
		$return .= '<div class="dashboard-box expando-box">
					  <h4>Makershed Orders</h4>
					  <ul>';
		foreach($orderJson['orders'] as $order) {
			$return .= '<li><p><b><a href="' . $order['order_status_url'] . '">Order #' . $order['id'] . '</a></b></p>';
			foreach($order['line_items'] as $line_item) {
				$return .= '<p>' . $line_item['name'] . ' - $' . $line_item['price'] . '</p>';
			}
			$return .= '</li>';
		}
		$return .=   '</ul>
				   </div>';
	}
	
	$return .= "</div>"; // End .dashboard-wrapper
	echo $return;
}

