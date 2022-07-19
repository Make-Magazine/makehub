<?php
/*
Plugin Name: WP Voting Contest
Plugin URI: https://plugins.ohiowebtech.com/?download=wordpress-voting-photo-contest-plugin
Version: 5.2
Description: Quickly and seamlessly integrate an online contest with voting into your Wordpress 5.0+ website. You can start many types of online contests such as photo, video, audio, essay/writing with very little effort.
Author: Ohio Web Technologies
Author URI: http://www.ohiowebtech.com
Copyright (c) 2008-2016 Ohio Web Technologies All Rights Reserved.

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/

//ini_set('display_errors', 1);
//error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
error_reporting(0);
if ( ! defined( 'ABSPATH' ) ) exit;
global $wpdb;

define('WPVC_VOTE_VERSION','5.2');
/*********** File path constants **********/
define('WPVC_VOTES_ABSPATH', dirname(__FILE__) . '/');
define('WPVC_VOTES_PATH', plugin_dir_url(__FILE__));
define('WPVC_VOTES_SL_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WPVC_VOTES_SL_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WPVC_VOTES_SL_PLUGIN_FILE', __FILE__);
define('WPVC_WP_VOTING_SL_STORE_API_URL', 'http://plugins.ohiowebtech.com');
define('WPVC_WP_VOTING_SL_PRODUCT_NAME', 'WordPress Voting Photo Contest Plugin');
define('WPVC_WP_VOTING_SL_PRODUCT_ID', 924);


require_once('configuration/config.php');
register_activation_hook(__FILE__,'wpvc_activation_init');
add_image_size( 'voting-image', 300, 0, true );
register_deactivation_hook(__FILE__,'wpvc_votes_deactivation_init');

if(!function_exists('wpvc_votes_version_updater_admin')){
    function wpvc_votes_version_updater_admin()
    {
        $wp_voting_sl_license_key = trim(get_option('wp_voting_software_license_key'));
        $wp_voting = new Wpvc_Vote_Updater(WPVC_WP_VOTING_SL_STORE_API_URL, __FILE__, array(
                            'version' => '5.2',
                            'license' => $wp_voting_sl_license_key,
                            'item_id' => WPVC_WP_VOTING_SL_PRODUCT_ID,
                            'author' => 'Ohio Web Technologies'
                        ));
    }
}
add_action( 'admin_init', 'wpvc_votes_version_updater_admin' );