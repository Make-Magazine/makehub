<?php

if (!defined('ABSPATH')) {
    die('You are not allowed to call this page directly.');
}

/**
 *  Display subscription support Message on Gravity Press settings page
 *
 */
function gpPro_license_page_subscription_message()
{
  $html = '<h2><i>'.esc_html__("Gravity Press Subscription Support Enabled", "gravitypress").'</i></h2>';

  $html .= '<div>The following features are enabled with Gravity Press Pro:';
  $html .= "<ul style='margin-left: 2.5em;'>
            <li style='list-style-type: disc;'>Subscriptions payments are now initialized by Gravity Forms and finalized by MemberPress, which means that they become native MemberPress subscriptions, <a href='/wp-admin/admin.php?page=memberpress-subscriptions'>managed by MemberPress</a>, not
              Gravity Forms going forward.</li>
                <ul>
                  <li style='list-style-type: square;margin-left: 2.5em;'>To ensure everything works, you must setup your payment gateways in <b>both</b> MemberPress and Gravity Forms</li>
                </ul>
            <li style='list-style-type: disc;'>Members can see their Gravity Forms subscriptions on their MemberPress Account page</li>
            <li style='list-style-type: disc;'>Members can cancel their subscriptions from their MemberPress Account page</li>
            <li style='list-style-type: disc;'>Members can upgrade their subscriptions from their MemberPress Account page using the MemberPress Groups feature</li>
          </ul>";
  $html .= '</div>';
    
    return $html;
}

/**
 *  Display Various Admin Messages if Stripe is present
 *
 */

function gpPro_subs_admin_notice_stripe_webhooks() {
  global $current_user ;
  $user_id = $current_user->ID;
  $user_dismissed_notice = get_user_meta( $user_id, 'gp_subs_stripe_webhooks_ignore_notice' );
  $is_stripe_addon_active = is_plugin_active( 'gravityformsstripe/stripe.php' );

  /* Check that the user hasn't already clicked to ignore the message */
  if ( !$user_dismissed_notice && $is_stripe_addon_active ) {
  $get_gf_stripe_options = get_option('gravityformsaddon_gravityformsstripe_settings');
  $webhooks_enabled = $get_gf_stripe_options['webhooks_enabled'];

    //Check to see if Stripe Payment Form settings is enabled. If so, provide warning
    if ( class_exists( 'GFStripe' ) && gf_stripe()->get_plugin_setting( 'checkout_method' ) == 'stripe_checkout' ) {
      echo '<div class="notice notice-warning is-dismissible"><p>';
      printf(__('Gravity Press does not support Stripe Payment Form checkout method at this time. Please change your <a href="admin.php?page=gf_settings&subview=gravityformsstripe">settings</a> to Stripe Credit Card Field.<span style="float: right;"><i><a href="%1$s" class="dashicons dashicons-no-alt"></a></i></span>'), '?gp_subs_stripe_webhooks_ignore_notice=0');
      echo "</p></div>";
    }
  }
}
add_action('admin_notices', 'gpPro_subs_admin_notice_stripe_webhooks');

function gpPro_subs_stripe_webhooks_ignore_notice() {
   global $current_user;
    $user_id = $current_user->ID;
       /* If user clicks to ignore the notice, add that to their user meta */
       if ( isset($_GET['gp_subs_stripe_webhooks_ignore_notice']) && '0' == $_GET['gp_subs_stripe_webhooks_ignore_notice'] ) {
            add_user_meta($user_id, 'gp_subs_stripe_webhooks_ignore_notice', 'true', true);
   }
}
add_action('admin_init', 'gpPro_subs_stripe_webhooks_ignore_notice');
