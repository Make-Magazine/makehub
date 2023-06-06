<?php
/*
Plugin Name: Events Manager I/O
Version: 1.1.1
Plugin URI: http://wp-events-plugin.com
Description: Add-On for for importing and exporting events from various soources for the Events Manager WordPress plugin.
Author: Pixelite
Author URI: http://pixelite.com
*/

/*
Copyright (c) 2020, Pixelite SL

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

// Setting constants
define('EMIO_VERSION', '1.1.1'); //self expanatory
define('EMIO_API_VERSION', '1.0'); //the API of our classes may have breaking changes and this is used to check against formats and their declared support for compatibility
define('EMIO_DIR', dirname( __FILE__ )); //an absolute path to this directory
define('EMIO_DIR_URI', trailingslashit(plugins_url('',__FILE__))); //an absolute path to this directory
define('EMIO_PLUGIN_FILE', plugin_basename( __FILE__ )); //for updates

//DB table constants
global $wpdb;
define('EMIO_TABLE', $wpdb->prefix . 'em_io');
define('EMIO_TABLE_SYNC', $wpdb->prefix . 'em_io_sync');
define('EMIO_TABLE_LOG', $wpdb->prefix . 'em_io_log');

//check pre-requisites
require_once('libraries/emio-requirements-check.php');
require_once('libraries/emio-license.php');
$requirements = new EMIO_Requirements_Check('Events Manager I/O', __FILE__, '5.3', EMIO_VERSION, EMIO_API_VERSION);
if( !$requirements->passes() ) return;
unset($requirements);

/**
 * Loads all relevant files required for current operation, or none at all if not using the plugin on this instance
 * @author marcus
 *
 */
class EMIO_Loader {
	
	public static $loaded = array();
	
	public static function init(){
		//load if EM is loaded
		if( defined('EM_VERSION') ){
			if( version_compare(EMIO_VERSION, get_option('emio_version',0)) == 1 ){
				require_once( EM_DIR.'/em-install.php');
				include('emio-install.php');
			}
			if( !EM_IO\License::is_active() ) return false;
			if( is_admin() ){
				self::admin();
			}
			//the only class we need to run in all cases is the cron
			include('emio-cron.php');
			include('emio-formatting.php');
			include('emio-exception.php');
			include('emio-export-feed.php');
			do_action('em_io_loaded');
		}
	}
	
	public static function libraries(){
		require_once(EMIO_DIR.'/libraries/vendor/autoload.php');
	}
	
	/**
	 * Loads all files required for the base admin area, usually called by import_admin() and export_admin() 
	 */
	public static function admin(){
		if( !empty(self::$loaded['admin']) ) return;
		do_action('emio_load_admin');
		include('admin/functions.php');
		include('admin/admin-actions.php');
		include('admin/admin.php');
		self::$loaded['admin'] = true;
		do_action('emio_loaded_admin');
	}

	public static function admin_settings(){
        if( !empty(self::$loaded['admin-settings']) ) return;
		include('oauth/emio-oauth-admin-settings.php');
		do_action('emio_load_admin_settings');
        include('admin/admin-settings.php');
        self::$loaded['admin-settings'] = true;
		do_action('emio_loaded_admin_settings');
	}
	
	public static function formats(){
		if( !empty(self::$loaded['formats']) ) return;
		include('objects/emio-object.php');
		include('objects/emio-cpt.php');
		include('objects/emio-location.php');
		include('objects/emio-event.php');
		self::$loaded['formats'] = true;
		do_action('emio_load_formats');
	}
	
	public static function import_formats(){
		if( !empty(self::$loaded['import_formats']) ) return;
		self::formats();
		include('formats/emio-import-spreadsheet.php');
		//load default formats
		include('formats/ical/emio-import-ical.php');
		include('formats/csv/emio-import-csv.php');
		include('formats/excel/emio-import-excel.php');
		self::$loaded['import_formats'] = true;
		do_action('emio_load_import_formats');
	}
	
	public static function export_formats(){
		if( !empty(self::$loaded['export_formats']) ) return;
		self::formats();
		include('formats/emio-export-spreadsheet.php');
		//load default formats
		include('formats/ical/emio-export-ical.php');
		include('formats/csv/emio-export-csv.php');
		include('formats/excel/emio-export-excel.php');
		self::$loaded['export_formats'] = true;
		do_action('emio_load_export_formats');
	}

	public static function oauth(){
		if( !empty(self::$loaded['oauth']) ) return;
		include('oauth/emio-oauth-api.php');
		include('oauth/emio-oauth-api-client.php');
		include('oauth/emio-oauth-api-token.php');
		self::$loaded['oauth'] = true;
		do_action('emio_load_oauth');
	}
	
	/**
	 * Load all files required for export operations
	 */
	public static function export(){
		if( !empty(self::$loaded['export']) ) return;
		do_action('emio_load_export');
		include('items/emio-item.php');
		include_once('items/emio-items.php');
		include('items/emio-exports.php');
		include('items/emio-export.php');
		self::export_formats();
		self::$loaded['export'] = true;
		do_action('emio_loaded_export');
	}
	
	public static function export_admin(){
		self::export();
		if( !empty(self::$loaded['export-admin']) ) return;
		include_once('admin/admin-item.php');
		include('admin/admin-export.php');
		include('admin/admin-export-actions.php');
		include_once('items/emio-item-admin.php');
		include('items/emio-export-admin.php');
		self::$loaded['export-admin'] = true;
		do_action('emio_loaded_export_admin');
	}
	
	/**
	 * Load all files required for import operations 
	 */
	public static function import(){
		if( !empty(self::$loaded['import']) ) return;
		include('items/emio-item.php');
		include_once('items/emio-items.php');
		include('items/emio-imports.php');
		include('items/emio-import.php');
		self::$loaded['import'] = true;
		self::import_formats();
		do_action('emio_load_import');
	}
	
	public static function import_admin() {
		self::import();
		if( !empty(self::$loaded['import-admin']) ) return;
		include_once('admin/admin-item.php');
		include('admin/admin-import.php');
		include('admin/admin-import-actions.php');
		include_once('items/emio-item-admin.php');
		include('items/emio-import-admin.php');
		do_action('emio_load_import_admin');
		self::$loaded['import-admin'] = true;
	}
}
add_action('plugins_loaded','EMIO_Loader::init', 100);

/**
 * Loads the settings of EMIO which are stored as one serialized array in wp_options
 */
class EMIO_Options{

	/**
	 * @param string $option_name
	 * @return mixed
	 */
	public static function get( $option_name, $default = null ){
		$options = get_option('emio_settings', array());
		if( isset($options[$option_name]) ){
			return $options[$option_name];
		}
		return $default;
	}

	/**
	 * @param array|string $data_or_name
	 * @param mixed $data
	 */
	public static function set( $data_or_name, $data = null ){
		$options = get_option('emio_settings', array());
		if( is_array($data_or_name) ){
			foreach( $data_or_name as $k => $v ){
				$options[$k] = $v;
			}
		}else{
			$options[$data_or_name] = $data;
		}
		update_option('emio_settings', $options);
	}
}
