<?php

/**
 * Template Name: Initializer
 *
 * This file initializes the required functions of the plugin and gets called from the main plugin file after it's verified
 *
 * @package     gravitypress
 * @author      Kevin Marshall
 *
 * @since       1.4
 *
 */
/*******************************************
 * INCLUDES
 *******************************************/

require_once('includes/gp_authorization.php');
require_once('includes/gp_core_functions.php');
require_once('includes/legacy.php');
require_once('includes/gp_admin_alerts.php');

//Extend GFFeedAddOn if function exists
add_action( 'gform_loaded', array( 'GravityPress_AddOn', 'load' ), 5 );

class GravityPress_AddOn {
    public static function load() {
        if ( ! method_exists( 'GFForms', 'include_feed_addon_framework' ) ) {
            return;
        }
        require_once( 'includes/gp_admin.php' );
        GFAddOn::register( 'GravityPressFeedAddOn' );

        // check if Memberpress plugin is active
        if(is_plugin_active('memberpress/memberpress.php') || function_exists('mepr_plugin_info')){
            require_once( 'includes/gateways/gp_gateways_common.php' );
            require_once( 'includes/gateways/gp_stripe.php' );
            require_once( 'includes/gateways/gp_paypal.php' );
            require_once( 'includes/gateways/gp_offline.php' );

            /**
             * Added by @kumaranup594
             * @since 3.3.1
             * @description Added PayPal checkout gateway support
             */
            require_once( 'includes/gateways/gp_paypal_checkout.php' );
            
            // enable authorize gateway if Memberpress Plus plugins is active
            if(class_exists('MeprAuthorizeGateway')){
                require_once( 'includes/gateways/gp_authorize.php' );
            }
        }
    }
}

function gp_addon() {
    if ( class_exists( 'GFForms' ) ) {
        return GravityPressFeedAddOn::get_instance();
	}
}
