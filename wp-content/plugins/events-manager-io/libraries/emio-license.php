<?php
namespace EM_IO;

require('em-license.php');

class License extends EM_License {
	
	static $plugin_name = 'Events Manager IO';
	static $plugin = 'events-manager-io/events-manager-io.php'; //set this on init in case someone changed the folder name
	static $slug = 'events-manager-io';
	static $license_key_option_name = 'dbem_io_license_key';
	static $lang = 'events-manager-io';
	static $constant_prefix = 'EMIO_LICENSE';
	
	public static function init(){
		static::$plugin = EMIO_PLUGIN_FILE;
		static::$version = EMIO_VERSION;
		static::$current_versions['events-manager-io'] = EMIO_VERSION;
		parent::init();
	}
	
	public static function load_admin(){
		include('emio-license-admin.php');
		License_Admin::init();
	}
}
add_action('plugins_loaded', 'EM_IO\License::init', 1);