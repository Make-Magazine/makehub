<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
$acct_ctrl = new MeprAccountCtrl();
echo("<h3>Subscriptions</h3>");
echo $acct_ctrl->subscriptions();
echo("<h3>Payments</h3>");
echo $acct_ctrl->payments();

$user = MeprUtils::get_currentuserinfo();
if($user !== false && isset($user->ID)) {
	$active_products = $user->active_product_subscriptions('ids');
    if(empty($active_products)) { ?>
		<div class="mp-no-subs">
			<?php _ex('You have no active subscriptions to display.', 'ui', 'buddyboss-theme'); ?>
			<br /><br /><a href="/join" class="btn universal-btn">Join Make: Community Now</a>
		</div>
	<?php }
}

if(CAN_UPGRADE == true) { ?>
	<a href="/register/premium-subscriber?upgrade=65WSJ3T3GY" class="btn universal-btn membership-btn upgrade">Upgrade to Premium Subscriber</a>
<?php
}

MeprHooks::do_action('mepr_account_payments', $mepr_current_user);
