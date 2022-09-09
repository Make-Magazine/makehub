<?php
/**
 * Plugin Name: LearnDash HUB
 * Plugin URI:
 * Description: LearnDash Hub allows you to connect your license and handles all updates related to the LearnDash family of products.
 * Author:      LearnDash
 * Author URI: https://learndash.com
 * Version:     1.0.0
 * Text Domain: learndash_hub
 * Domain Path: includes/languages/
 * Network:     true
 */

const HUB_VERSION    = '1.0';
const HUB_DB_VERSION = '1.0';
const HUB_SLUG       = 'learndash-hub/learndash-hub.php';
define( 'HUB_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
if ( ! defined( 'LEARNDASH_UPDATES_ENABLED' ) ) {
	$enable = false;
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		$action = $_GET['action'] ?? '';
		if ( 'learndash_setup_wizard_verify_license' === $action ) {
			$enable = true;
		}
	}

	define( 'LEARNDASH_UPDATES_ENABLED', $enable );
}
// autoload.
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/configs.php';
require_once __DIR__ . '/src/functions.php';

$boot = new \LearnDash\Hub\Boot();
add_action( 'init', array( $boot, 'start' ) );
add_action( 'plugins_loaded', array( $boot, 'load_plugin_textdomain' ) );
/**
 * LearnDash Hub Activate/Install function.
 */
function learndash_hub_install( $network_wide = null ) {
	// Nothing to see here.
}

register_activation_hook( __FILE__, 'learndash_hub_install' );

register_deactivation_hook( __DIR__, array( $boot, 'deactivate' ) );
