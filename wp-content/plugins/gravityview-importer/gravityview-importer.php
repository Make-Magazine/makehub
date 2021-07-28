<?php
/**
 * Plugin Name: GravityView - Gravity Forms Import Entries
 * Plugin URI:  https://gravityview.co/extensions/gravity-forms-entry-importer/
 * Description: The best way to import entries into Gravity Forms. Proud to be a Gravity Forms Certified Add-On.
 * Version:     2.2.3
 * Author:      GravityView
 * Author URI:  https://gravityview.co
 * Text Domain: gravityview-importer
 * License:     GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define( 'GV_IMPORT_ENTRIES_VERSION', '2.2.3' );

define( 'GV_IMPORT_ENTRIES_FILE', __FILE__ );

define( 'GV_IMPORT_ENTRIES_MIN_GF', '2.2' );

define( 'GV_IMPORT_ENTRIES_MIN_PHP', '5.6' );

define( 'GV_IMPORT_ENTRIES_MIN_WP', '5.0' );

add_action( 'plugins_loaded', 'gv_import_entries_load', 1 );

/**
 * Main plugin loading function.
 *
 * @codeCoverageIgnore Tested during load
 *
 * @return void
 */
function gv_import_entries_load() {
	global $wp_version;

	// Require PHP min version
	if ( version_compare( phpversion(), GV_IMPORT_ENTRIES_MIN_PHP, '<' ) ) {
		add_action( 'admin_notices', 'gv_import_entries_noload_php' );
		return;
	}

	// Require WordPress min version
	if ( version_compare( $wp_version, GV_IMPORT_ENTRIES_MIN_WP, '<' ) ) {
		add_action( 'admin_notices', 'gv_import_entries_noload_wp' );
		return;
	}

	// Require Gravity Forms min version
	if ( ! class_exists( 'GFForms') || version_compare( GFForms::$version, GV_IMPORT_ENTRIES_MIN_GF, '<' ) ) {
		add_action( 'admin_notices', 'gv_import_entries_noload_gravityforms' );
		return;
	}

	// Make sure the plugin is built properly
	if( ! file_exists( plugin_dir_path( __FILE__ ) . 'vendor/autoload.php' ) ) {
		add_action( 'admin_notices', 'gv_import_entries_noload_composer' );
		return;
	}

	// Boot it up.
	require_once dirname( __FILE__ ) . '/class-gv-import-entries-core.php';
	call_user_func( array( '\GV\Import_Entries\Core', 'bootstrap' ) );
}

/**
 * Notice output in dashboard if Composer hasn't been built.
 *
 * @codeCoverageIgnore Just some output.
 *
 * @return void
 */
function gv_import_entries_noload_composer() {
	// Note: Not going to translate this at the moment, since the repo is private.
	$message = wpautop( 'You are using a developer build of Gravity Forms Import Entries, but it has not been properly built. Using the terminal, change directories into the plugin, then run <code>composer install</code>.' );
	echo "<div class='error'>$message</div>";
}

/**
 * Notice output in dashboard if PHP is incompatible.
 *
 * @codeCoverageIgnore Just some output.
 *
 * @return void
 */
function gv_import_entries_noload_php() {
	$message = wpautop( sprintf( esc_html__( 'The %s Extension requires PHP Version %s or newer. Please ask your host to upgrade your server\'s PHP.', 'gravityview-importer' ), 'Gravity Forms Import Entries', GV_IMPORT_ENTRIES_MIN_PHP ) );
	echo "<div class='error'>$message</div>";
}

/**
 * Notice output in dashboard if WordPress is incompatible.
 *
 * @since 2.0.2
 *
 * @codeCoverageIgnore Just some output.
 *
 * @return void
 */
function gv_import_entries_noload_wp() {
	$message = wpautop( sprintf( esc_html__( 'The %s Extension requires WordPress Version %s or newer.', 'gravityview-importer' ), 'Gravity Forms Import Entries', GV_IMPORT_ENTRIES_MIN_WP ) );
	echo "<div class='error'>$message</div>";
}

/**
 * Notice output in dashboard if Gravity Forms is incompatible.
 *
 * @codeCoverageIgnore Just some output.
 *
 * @return void
 */
function gv_import_entries_noload_gravityforms() {
	$message = wpautop( sprintf( esc_html__( '%s requires Gravity Forms Version %s or higher.', 'gravityview-importer' ), 'Gravity Forms Import Entries', GV_IMPORT_ENTRIES_MIN_GF ) );
	echo "<div class='error'>$message</div>";
}
