<?php
/**
 * Plugin Name: Visual Customizer for LearnDash
 * Plugin URI: http://www.snaporbital.com/downloads/
 * Description: Enhance and customize the LearnDash design
 * Version: 2.3.16
 * Author: SnapOrbital
 * Author URI: http://www.snaporbital.com
 * Text Domain: lds_skins
 * License: GPL2
 */

/*
 * Required Files and Constants
 *
 */

if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
	include_once('ldvc-license.php');
}

$definitions = array(
	'LDS_STORE_URL'			=>	'https://www.snaporbital.com',
	'EDD_LEARNDASH_SKINS'		=>	'LearnDash Visual Customizer',
	'LDS_VER'					=>	'2.3.16',
	'LDS_PATH'				=>	plugin_dir_path(__FILE__),
	'LDS_URL'					=>	plugins_url( '', __FILE__ )
);

foreach( $definitions as $definition => $value ) {
	if( !defined( $definition ) ) define( $definition, $value );
}

add_action( 'plugins_loaded', 'ldvc_init', 9000 );
function ldvc_init() {

	// Make sure LearnDash is running
	if( !defined('LEARNDASH_VERSION') ) {
		return;
	}

	$active_theme = false;

	if( class_exists('LearnDash_Theme_Register') ) {
		$active_theme = LearnDash_Theme_Register::get_active_theme_key();
	}

	if( defined('LEARNDASH_VERSION') && version_compare( LEARNDASH_VERSION, '2.6.5', '>=' ) && $active_theme == 'legacy' ) {
		define( 'LDVC_MODE', 'legacy' );
	} else {
		define( 'LDVC_MODE', 'modern' );
	}

	do_action( 'ldvc_before_init' );

	if( LDVC_MODE == 'legacy' ) {

		$deps = array(
			'legacy/lds-stylesheet-management',
			'legacy/lds-shortcodes',
			'legacy/lds-assets',
			'legacy/lds-settings',
			'legacy/lds-views',
			'legacy/lds-widgets',
			'legacy/models/lds-lesson-topics',
			'legacy/controllers/lds-templates',
			'legacy/lds-wp-customizer-api'
		);

	} else {

		$deps = array(
			'inc/ldvc-views',
			'inc/ldvc-assets',
			'inc/ldvc-widgets',
			'inc/ldvc-shortcodes',
			'inc/ldvc-models',
			'inc/ldvc-helpers',
			'inc/ldvc-settings',
			'inc/ldvc-customizer',
			'inc/ldvc-themes'
		);

	}

	foreach( $deps as $dep ) {
		include_once( $dep . '.php' );
	}

	do_action( 'ldvc_after_init' );

}

/*
 * Initialize the plugin
 *
 */
// register_activation_hook( __FILE__ , 'lds_build_stylesheet' );

/**
 * Settings Page
 * @return [type] [description]
 */
function lds_settings_page() {

   add_submenu_page( 'edit.php?post_type=sfwd-courses',__('LearnDash Appearance','lds_skins'), __('LearnDash Appearance','lds_skins'), 'manage_options', 'admin.php?page=learndash-appearance', 'lds_appearance_settings' );
   $hook = add_submenu_page( 'learndash-lms-non-existant',__('LearnDash Appearance','lds_skins'), __('LearnDash Appearance','lds_skins'), 'manage_options', 'learndash-appearance', 'lds_appearance_settings' );

   add_action( 'load-' . $hook , 'lds_options_saved' );

}
add_action( 'admin_menu', 'lds_settings_page', 2500 );

/*
 * Add translation support
 *
 */
function ldvc_i18ize() {
	load_plugin_textdomain( 'lds_skins', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', "ldvc_i18ize" );

add_action( 'admin_init', 'ldvc_plugin_updater' );
function ldvc_plugin_updater() {

	if( !class_exists('EDD_SL_Plugin_Updater') ) {
		return false;
	}

	// retrieve our license key from the DB
	$license_key = trim( get_option( 'lds_skins_license_key' ) );

	// setup the updater
	$edd_updater = new EDD_SL_Plugin_Updater( LDS_STORE_URL, __FILE__, array(
			'version' 	=> LDS_VER, 				// current version number
			'license' 	=> $license_key, 		// license key (used get_option above to retrieve from DB)
			'item_name' 	=> EDD_LEARNDASH_SKINS, 	// name of this plugin
			'author' 		=> 'SnapOrbital',  // author of this plugin
			'url'           => home_url()
		)
	);

}
