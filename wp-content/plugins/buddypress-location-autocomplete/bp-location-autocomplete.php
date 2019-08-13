<?php
/**
 * Plugin Name: Location Autocomplete for BuddyPress
 * Plugin URI:  http://buddyboss.com/product/locations-for-buddypress/
 * Description: Adds structured Google API location data to BuddyPress Profile fields and Groups.
 * Author:      BuddyBoss
 * Author URI:  http://buddyboss.com
 * Version:     1.2.2
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * ========================================================================
 * CONSTANTS
 * ========================================================================
 */
// Codebase version
if ( ! defined( 'BUDDYBOSS_BPLA_PLUGIN_VERSION' ) ) {
	define( 'BUDDYBOSS_BPLA_PLUGIN_VERSION', '1.2.2' );
}

// Database version
if ( ! defined( 'BUDDYBOSS_BPLA_PLUGIN_DB_VERSION' ) ) {
	define( 'BUDDYBOSS_BPLA_PLUGIN_DB_VERSION', 1 );
}

// Directory
if ( ! defined( 'BUDDYBOSS_BPLA_PLUGIN_DIR' ) ) {
	define( 'BUDDYBOSS_BPLA_PLUGIN_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
}

// Url
if ( ! defined( 'BUDDYBOSS_BPLA_PLUGIN_URL' ) ) {
	$plugin_url = plugin_dir_url( __FILE__ );

	// If we're using https, update the protocol. Workaround for WP13941, WP15928, WP19037.
	if ( is_ssl() )
		$plugin_url = str_replace( 'http://', 'https://', $plugin_url );

	define( 'BUDDYBOSS_BPLA_PLUGIN_URL', $plugin_url );
}

// File
if ( ! defined( 'BUDDYBOSS_BPLA_PLUGIN_FILE' ) ) {
	define( 'BUDDYBOSS_BPLA_PLUGIN_FILE', __FILE__ );
}

/**
 * ========================================================================
 * MAIN FUNCTIONS
 * ========================================================================
 */

/**
 * Main
 *
 * @return void
 */
add_action( 'plugins_loaded', 'BUDDYBOSS_BPLA_init' );

function BUDDYBOSS_BPLA_init() {
	if ( ! function_exists( 'bp_is_active' ) ) {
		//Check BuddyPress is install and active
		add_action( 'admin_notices', 'buddyboss_la_install_buddypress_notice' );
		return;
	}

	global $BUDDYBOSS_BPLA;

	$main_include = BUDDYBOSS_BPLA_PLUGIN_DIR . 'includes/main-class.php';

	try {
		if ( file_exists( $main_include ) ) {
			require( $main_include );
		} else {
			$msg = sprintf( __( "Couldn't load main class at:<br/>%s", 'bp-location-autocomplete' ), $main_include );
			throw new Exception( $msg, 404 );
		}
	} catch ( Exception $e ) {
		$msg = sprintf( __( "<h1>Fatal error:</h1><hr/><pre>%s</pre>", 'bp-location-autocomplete' ), $e->getMessage() );
		echo $msg;
	}

	$BUDDYBOSS_BPLA = BuddyBoss_BPLA_Plugin::instance();
}

/**
 * Check whether
 * it meets all requirements
 * @return void
 */
function bpla_requirements()
{

    global $Plugin_Requirements_Check;

    $requirements_Check_include  = BUDDYBOSS_BPLA_PLUGIN_DIR  . 'includes/requirements-class.php';

    try
    {
        if ( file_exists( $requirements_Check_include ) )
        {
            require( $requirements_Check_include );
        }
        else{
            $msg = sprintf( __( "Couldn't load BPLA_Plugin_Requirements_Check class at:<br/>%s", 'bp-location-autocomplete' ), $requirements_Check_include );
            throw new Exception( $msg, 404 );
        }
    }
    catch( Exception $e )
    {
        $msg = sprintf( __( "<h1>Fatal error:</h1><hr/><pre>%s</pre>", 'bp-location-autocomplete' ), $e->getMessage() );
        echo $msg;
    }

    $Plugin_Requirements_Check = new BPLA_Plugin_Requirements_Check();
    $Plugin_Requirements_Check->activation_check();

    $site_options = get_site_option('bp_bpla_plugin_options');
    $site_options['enable-for-profiles'] = 'yes';
    $site_options['enable-for-groups'] = 'yes';

    update_site_option('bp_bpla_plugin_options', $site_options);

    if ( is_multisite() ) {
        update_blog_option(get_current_blog_id(), 'bp_bpla_plugin_options', $site_options);
    }

}
register_activation_hook( __FILE__, 'bpla_requirements' );

/**
 * Must be called after hook 'plugins_loaded'
 * @return BuddyBoss BLF Plugin main controller object
 */
function bp_bpla() {
	global $BUDDYBOSS_BPLA;
	return $BUDDYBOSS_BPLA;
}

/**
 * Register BuddyBoss Menu Page
 */
if ( !function_exists( 'register_buddyboss_menu_page' ) ) {

	function register_buddyboss_menu_page() {

		if ( ! empty( $GLOBALS['admin_page_hooks']['buddyboss-settings'] ) ) return;

		// Set position with odd number to avoid confict with other plugin/theme.
		add_menu_page( 'BuddyBoss', 'BuddyBoss', 'manage_options', 'buddyboss-settings', '', bp_bpla()->assets_url . '/images/logo.svg', 3 );

		// To remove empty parent menu item.
		add_submenu_page( 'buddyboss-settings', 'BuddyBoss', 'BuddyBoss', 'manage_options', 'buddyboss-settings' );
		remove_submenu_page( 'buddyboss-settings', 'buddyboss-settings' );
	}

	add_action( 'admin_menu', 'register_buddyboss_menu_page' );
}

/**
 * Show the admin notice to install/activate BuddyPress first
 */
function buddyboss_la_install_buddypress_notice() {
	echo '<div id="message" class="error fade"><p style="line-height: 150%">';
	_e('<strong>BuddyPress Location Autocomplete</strong></a> requires the BuddyPress plugin to work. Please <a href="http://buddypress.org">install BuddyPress</a> first.', 'bp-location-autocomplete');
	echo '</p></div>';
}

/**
 * Allow automatic updates via the WordPress dashboard
 */
require_once('includes/buddyboss-plugin-updater.php');
//new buddyboss_updater_plugin( 'http://update.buddyboss.com/plugin', plugin_basename(__FILE__), 466);
