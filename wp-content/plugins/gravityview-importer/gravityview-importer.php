<?php
/**
 * Plugin Name: GravityView - Gravity Forms Import Entries
 * Plugin URI:  https://gravityview.co/extensions/gravity-forms-entry-importer/
 * Description: The best way to import entries into Gravity Forms.
 * Version:     2.0.1
 * Author:      GravityView
 * Author URI:  https://gravityview.co
 * Text Domain: gravityview-importer
 * License:     GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define( 'GV_IMPORT_ENTRIES_VERSION', '2.0.1' );

define( 'GV_IMPORT_ENTRIES_FILE', __FILE__ );

define( 'GV_IMPORT_ENTRIES_MIN_GF', '2.2' );

define( 'GV_IMPORT_ENTRIES_MIN_PHP', '5.3.3' );

add_action( 'plugins_loaded', 'gv_import_entries_load' );

/**
 * Main plugin loading function.
 *
 * @codeCoverageIgnore Tested during load
 *
 * @return void
 */
function gv_import_entries_load() {

	// Require PHP 5.3.3+
	if ( version_compare( phpversion(), GV_IMPORT_ENTRIES_MIN_PHP, '<' ) ) {
		add_action( 'admin_notices', 'gv_import_entries_noload_php' );
		return;
	}

	// Require Gravity Forms 2.2+
	if ( ! class_exists( 'GFForms' ) || ! property_exists( 'GFForms', 'version' ) || version_compare( GFForms::$version, GV_IMPORT_ENTRIES_MIN_GF, '<' ) ) {
		add_action( 'admin_notices', 'gv_import_entries_noload_gravityforms' );
		return;
	}

	// Boot it up.
	require_once dirname( __FILE__ ) . '/class-gv-import-entries-core.php';
	call_user_func( array( '\GV\Import_Entries\Core', 'bootstrap' ) );
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
