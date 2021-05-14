<?php
/*
Plugin Name: Notes for LearnDash
Plugin URI:  http://www.snaporbital.com/learndash-notes
Description: Take notes on any Learndash course, lesson or topic and list a users notes using [learndash_my_notes]
Version:     1.6.7.2
Author:      SnapOrbital
Author URI:  http://www.snaporbital.com
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: sfwd-lms
Domain Path: /languages
*/


$ld_constants = array(
	'LD_NOTES_URL'			=>	plugin_dir_url( __FILE__ ),
	'LD_NOTES_PATH'			=>	plugin_dir_path( __FILE__ ),
	'PSP_NOTES_STORE_URL'	=>	'https://www.snaporbital.com',
	'PSP_NOTES_ITEM_NAME'	=>	'LearnDash Notes',
	'LDNT_VER'				=>	'1.6.7.2',
	'LD_FINAL_NAME'			=>	'LearnDash Notes'
);

foreach( $ld_constants as $constant => $value ) {
	if( !defined($constant) ) define( $constant, $value );
}

if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
	// load our custom updater
	include( dirname( __FILE__ ) . '/EDD_SL_Plugin_Updater.php' );
}

do_action( 'nt_note_before_initialize' );

$inits = array(
	'nt-ajax.php',
	'nt-admin.php',
	'nt-models.php',
	'nt-controller.php',
	'nt-views.php',
	'nt-assets.php',
	'nt-shortcodes.php',
	'nt-settings.php',
	'nt-permissions.php'
);

foreach( $inits as $init ) {
	include_once( $init );
}

do_action( 'nt_note_after_initialize' );

// retrieve our license key from the DB
$license_key = trim( get_option( 'learndash_notes_license_key' ) );

add_action( 'plugins_loaded', 'lds_notes_translation_i18ize' );
function lds_notes_translation_i18ize() {
	load_plugin_textdomain( 'sfwd-lms', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

// setup the updater
$edd_updater = new EDD_SL_Plugin_Updater( PSP_NOTES_STORE_URL, __FILE__, array(
		'version' 		=> LDNT_VER, 		// current version number
		'license' 		=> $license_key, 	// license key (used get_option above to retrieve from DB)
		'item_name'     => PSP_NOTES_ITEM_NAME, 	// name of this plugin
		'author' 		=> 'Snap Orbital',  // author of this plugin
		'url'           => home_url()
	)
);
