<?php
/*
Plugin Name: Gravity Press Pro
Plugin URI: https://ristrettoapps.com/downloads/gravity-press/
Description: Integrates Gravity Forms with MemberPress. <a href='https://docs.ristrettoapps.com/article-categories/version-3-x/' target='blank'>Instructions</a>
Version: 3.4.6
Author: Ristretto Apps
Author URI: http://ristrettoapps.com
------------------------------------------------------------------------
Copyright 2016 Ristretto Apps
*/

/******** Define Constants ********/

// plugin folder path
if(!defined('GRAVITYPRESS_PLUGIN_DIR')) {
    define('GRAVITYPRESS_PLUGIN_DIR', dirname(__FILE__));
}

//Display request to view get started screen

add_action('admin_notices', 'gravitypress_admin_notice_getstarted');


//Check Multisite / Multisite Activation
register_activation_hook(__FILE__, 'gravitypress_activate');

function gravitypress_activate($networkwide)
{
    global $wpdb;
    if (function_exists('is_multisite') && is_multisite()) {

        // check if it is a network activation - if so, run the activation function for each blog id  ~Vel

        if ($networkwide) {
            $old_blog = $wpdb->blogid;

            // Get all blog ids

            $blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
            foreach ($blogids as $blog_id) {
                switch_to_blog($blog_id);
                _gravitypress_activate();
            }
            switch_to_blog($old_blog);
            return;
        }
    }
    _gravitypress_activate();
}

function _gravitypress_activate()
{

    //Add initial plugin options here ~Vel
    $current_theme = wp_get_theme(); // Changed the depreciated function call 'get_current_theme()' to 'wp_get_theme()'
    add_option('gravity_form_to_MemberPress_bg_theme', $current_theme);

}

//Initialize Plugin
require_once( plugin_dir_path(__FILE__) . 'initialize.php' );

//Check if MemberPress Plugin Activated
function gravitypress_MP_active()
{
    global $gravitypress_MP_active;

    if (is_plugin_active('memberpress/memberpress.php')) {
        $gravitypress_MP_active == true;
    }
}

//Login box shortcode function to display a login box on any page
function gravitypress_login_form_shortcode($atts, $content = null)
{
    extract(shortcode_atts(array(
        'redirect' => ''
    ), $atts));
    if (!is_user_logged_in()) {
        if ($redirect) {
            $redirect_url = $redirect;
        } else {
            $redirect_url = $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
        }
        $form = wp_login_form(array('echo' => false, 'redirect' => $redirect_url, 'form_id' => 'gmloginform',));
        return "<div id='gravitypress_login_form_title'><i>If You Already Have an Account, Please Login First</i></div><br />" . $form;
    }
    //echo "<i>You Are Logged In</i>";
    return;
}

add_shortcode('gravitypress_loginform', 'gravitypress_login_form_shortcode');

//Plugin Version
define( 'GRAVITYPRESS_VERSION', '3.4.5' );

?>
