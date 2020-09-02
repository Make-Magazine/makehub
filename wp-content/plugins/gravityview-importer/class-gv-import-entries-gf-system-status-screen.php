<?php
/**
 * The purpose of this file is to ensure that Gravity Forms checks for the "Feeds" table being properly added to the DB
 *
 * @link https://github.com/gravityview/Import-Entries/issues/323
 * @link https://github.com/gravityview/Import-Entries/issues/324
 */

namespace GV\Import_Entries;

use GFForms;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Make sure Gravity Forms is active and already loaded.
if ( ! class_exists( 'GFForms' ) ) {
	return;
}

GFForms::include_feed_addon_framework();

/**
 * Register a Feed Add-On to trigger Gravity Forms showing feed table on the System Status screen
 *
 * @see GF_System_Report::get_database
 *
 * @since 2.1.1
 */
class GF_System_Status_Screen extends \GFFeedAddOn {

	public static function get_instance() {
		return new GF_System_Status_Screen();
	}

	// Prevent a menu item from appearing in Form Settings
	public function form_settings_init() {}

	// Prevent logging for this status screen hack
	public function set_logging_supported( $plugins ) {
		return $plugins;
	}
}

GF_System_Status_Screen::register( '\GV\Import_Entries\GF_System_Status_Screen' );
