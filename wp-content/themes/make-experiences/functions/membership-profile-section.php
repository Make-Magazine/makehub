<?php

/*
 * View and/or edit your membership subscription
 */

function profile_tab_membership_infoname() {
    global $bp;
    $user_id = bp_displayed_user_id();
    $type = bp_get_member_type(bp_displayed_user_id());
    if ($user_id != 0 && wp_get_current_user()->ID == $user_id) {
        bp_core_new_nav_item(array(
            'name' => 'Membership',
            'slug' => 'membership',
            'screen_function' => 'membership_info_screen',
            'position' => 40,
            'parent_url' => bp_loggedin_user_domain() . '/membership/',
            'parent_slug' => $bp->profile->slug,
            'default_subnav_slug' => 'membership'
        ));
    }
}

add_action('bp_setup_nav', 'profile_tab_membership_infoname');

function membership_info_screen() {
    // Add title and content here - last is to call the members plugin.php template.
    //add_action('bp_template_title', 'membership_info_title');
    add_action('bp_template_content', 'membership_info_content');
    bp_core_load_template('buddypress/members/single/plugins');
}

function membership_info_title() {
    //echo 'Maker Faire Information';
}

function membership_info_content() {
    global $wpdb;

    $user_id = bp_displayed_user_id();
    //get the users email
    $user_info = get_userdata($user_id);
    $user_email = $user_info->user_email;
    require_once(get_stylesheet_directory() . '/vendor/stripe/stripe-php/init.php');
    \Stripe\Stripe::setApiKey('sk_live_fx1xtpmDg3BUWIxZwKVfZugt');
    $customer = \Stripe\Customer::all(["email" => $user_email]);
    $customerID = (isset($customer->data[0]['id']) ? $customer->data[0]['id'] : NULL);

    echo '<div class="membership-tab-wrapper">';
    echo '<h1>Make: Membership Details</h1>';
    
    $hasDigitalAccess = false;
    $levels = Ihc_Db::get_user_levels($user_id, true);
    foreach($levels as $level) {
        if($level['level_slug'] != "community") {
            $hasDigitalAccess = true;
        }
    }
    echo do_shortcode("[ihc-list-user-levels exclude_expire=true]");
    if (!is_null($customerID)) { // if customer exists in stripe
        $session = \Stripe\BillingPortal\Session::create([
            'customer' => $customerID,
            'return_url' => 'https://' . $_SERVER['SERVER_NAME'] . '/members/' . $user_info->display_name . "/membership",
        ]);
        echo '<a href="' . $session->url . '" class="btn universal-btn" id="manage-membership-btn" target="_blank">Update Payment information</a>';
        if (!class_exists('ihcAccountPage')) {
            require_once IHC_PATH . 'classes/ihcAccountPage.class.php';
        }
        $obj = new ihcAccountPage($attr);
        echo $obj->print_page("orders");
    }
    if($hasDigitalAccess == false) {
        echo '<p>Upgrade your subscription for digital Make: Magazine access and exclusive videos.</p>';
        echo '<a href="/join" class="btn universal-btn">Upgrade your Subscription</a>';
    }

    echo '</div>';
}
