<?php

/*
 * View and/or edit your membership subscription
 */

function profile_tab_membership_infoname() {
    global $bp;
    $user_id = bp_displayed_user_id();
    $type = bp_get_member_type(bp_displayed_user_id());
    if ($user_id != 0 && wp_get_current_user()->ID == $user_id ) {
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
	//var_dump(Ihc_Db::get_user_levels($user_id, true));
    $user_email = $user_info->user_email;
	if(!class_exists('Stripe\Customer')) {
    	require_once(get_stylesheet_directory() . '/vendor/stripe/stripe-php/init.php');
	}
    \Stripe\Stripe::setApiKey('sk_live_fx1xtpmDg3BUWIxZwKVfZugt');
    $customer = \Stripe\Customer::all(["email" => $user_email]);
    $customerID = (isset($customer->data[0]['id']) ? $customer->data[0]['id'] : NULL); ?>

    <div class="membership-tab-wrapper">
    	<h1>Make: Membership Details</h1>

    	<?php echo do_shortcode("[ihc-list-user-levels exclude_expire=true]"); ?>

		<ul class="nav nav-tabs" id="myTab" role="tablist">
			<li class="nav-item">
				<a class="nav-link active" id="nav-subscriptions-tab" data-toggle="tab" data-target="#nav-subscriptions" role="tab" aria-controls="nav-subscriptions" aria-selected="true">Subscriptions<</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" id="nav-orders-tab" data-toggle="tab" data-target="#nav-orders" role="tab" aria-controls="nav-orders" aria-selected="false">Orders</a>
			</li>
		</ul>
		<div class="tab-content" id="nav-tabContent">
			<div class="tab-pane active" id="nav-subscriptions" role="tabpanel" aria-labelledby="nav-subscriptions-tab">
				<h3>Subscriptions</h3>
				<?php echo do_shortcode("[ihc-account-page-subscriptions-table]"); ?>
				<div class="membership-btns">
					<?php
					if(CAN_UPGRADE == true && IS_MEMBER == true) {
						if(ihcCheckCheckoutSetup()){
							if (isset($attr['checkout_page'])){
								$url = add_query_arg( 'lid', $attr['id'], $attr['checkout_page'] );
							} else {
								$page = get_option('ihc_checkout_page');
								$url = get_permalink($page);
								$url = add_query_arg( 'lid', '20', $url );
							}
							echo '<div onclick="ihcBuyNewLevel(\'' . $url . '\');" class="btn universal-btn">Upgrade</div>';
						} else {
							echo '<div onclick="ihcBuyNewLevelFromAp(\'Membership\', \'24.99\', 20, \'' .CURRENT_URL. '/account/?ihcnewlevel=true&amp;lid=20&amp;urlr=' .urlencode(CURRENT_URL). '%2Faccount%2F%3Fihc_ap_menu%3Dsubscription\');" class="btn universal-btn">Upgrade</div>';
						}
					}
					if (!is_null($customerID) && IS_MEMBER == true) { // if customer exists in stripe
				        $session = \Stripe\BillingPortal\Session::create([
				            'customer' => $customerID,
				            'return_url' => 'https://' . $_SERVER['SERVER_NAME'] . '/members/' . $user_info->user_nicename . "/membership",
				        ]);
				        echo '<a href="'. $session->url .'" class="btn universal-btn" id="manage-membership-btn" target="_blank">Update Payment information</a>';
					} else if(IS_MEMBER == false) {
						echo '<div><h4>Not a Member?</h4><a href="/join" class="btn universal-btn-red">JOIN TODAY</a></div>';
					}
					?>
				</div>
				<?php if(CAN_UPGRADE == true && IS_MEMBER == true) { echo '<p>Upgrade your subscription for digital Make: Magazine access and exclusive videos. Introductory offer $24.99 the first year.</p>'; } ?>
			</div>
			<div class="tab-pane" id="nav-orders" role="tabpanel" aria-labelledby="nav-orders-tab">
				<?php
				if (!is_null($customerID)) { // if customer exists in stripe
					if (!class_exists('ihcAccountPage')) {
						require_once IHC_PATH . 'classes/ihcAccountPage.class.php';
					}
					$obj = new ihcAccountPage();
					echo $obj->print_page("orders");
				} else {
					?> <h3>Orders</h3><div>No orders to display</div> <?php
				}
				?>
			</div>
		</div>
    </div>
<?php
}
