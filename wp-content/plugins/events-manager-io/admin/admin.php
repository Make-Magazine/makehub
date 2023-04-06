<?php
/**
 * Handles the loading of the Admin user interface for Imports and Exports
 */
class EMIO_Admin {
	public static function init(){
		add_action('admin_menu','EMIO_Admin::admin_menu', 100);
		add_action('admin_enqueue_scripts', 'EMIO_Admin::enqueue_scripts' );
		//load things early if nonce is submitted, since we're performing an action
		if( !empty($_REQUEST['emio_import_nonce']) ){
			EMIO_Loader::import_admin();
		}elseif( !empty($_REQUEST['emio_export_nonce']) ){
			EMIO_Loader::export_admin();
		}elseif( !empty($_REQUEST['emio_settings_nonce']) ){
		    EMIO_Loader::admin_settings();
        }
		add_filter('set-screen-option', 'EMIO_Admin::set_screen_option', 10, 3);
	}
	
	public static function admin_menu() {
	    global $menu;
	    //get priority of EM Events page
        $position = 27;
        foreach( $menu as $pos => $menu_item ){
            if( $menu_item[2] == 'edit.php?post_type='.EM_POST_TYPE_EVENT ) $position = $pos;
        }
        $plugin_name = __('Events Manager I/O', 'events-manager-io');
        $page_name = 'events-manager-io-import';
	    add_menu_page($plugin_name, __('Events I/O', 'events-manager-io'), 'manage_options', $page_name, 'EMIO_Admin::import', 'dashicons-networking', $position);
		$import_hook = add_submenu_page($page_name, $plugin_name.' - '. __('Import','events-manager-io'),__('Import','events-manager-io'), 'manage_options', $page_name, 'EMIO_Admin::import');
		$export_hook = add_submenu_page($page_name, $plugin_name.' - '. __('Export','events-manager-io'),__('Export','events-manager-io'), 'manage_options', 'events-manager-io-export', 'EMIO_Admin::export');
        add_submenu_page($page_name, $plugin_name.' - '. __('Settings','events-manager-io'),__('Settings','events-manager-io'), 'manage_options', 'events-manager-io-settings', 'EMIO_Admin::settings');
        //screen options
		if( !empty($_GET['tab']) && $_GET['tab'] == 'history' ){
			add_action( "load-$import_hook", 'EMIO_Admin::set_logs_screen_option' );
			add_action( "load-$export_hook", 'EMIO_Admin::set_logs_screen_option' );
		}
		if( empty($_GET['tab']) && empty($_GET['view']) ){
			add_action( "load-$import_hook", 'EMIO_Admin::set_items_screen_option' );
			add_action( "load-$export_hook", 'EMIO_Admin::set_items_screen_option' );
		}
	}

	/**
	 * Screen options
	 */
	public static function set_logs_screen_option() {
		$args   = array(
			'label'   => __('History Items Per Page','events-manager-io'),
			'default' => 20,
			'option'  => 'emio_logs_per_page'
		);
		add_screen_option( 'per_page', $args );
	}
	
	/**
	 * Screen options
	 */
	public static function set_items_screen_option() {
		$args   = array(
			'label'   => __('Imports/Exports Per Page','events-manager-io'),
			'default' => 20,
			'option'  => 'emio_items_per_page'
		);
		add_screen_option( 'per_page', $args );
	}
	
	public static function set_screen_option($status, $option, $value) {
		if ( 'emio_logs_per_page' == $option ) return $value;
		if ( 'emio_items_per_page' == $option ) return $value;
		return $status;
		
	}
	
	public static function enqueue_scripts( $hook ){
		if( !empty($_REQUEST['page']) && preg_match('/^events\-manager\-io/', $_REQUEST['page']) ){
			$min = defined('WP_DEBUG') && WP_DEBUG ? '':'.min';
            wp_enqueue_style( 'select2', plugin_dir_url( __FILE__ ) . "css/select2{$min}.css", array(), "4.0.3" );
            wp_enqueue_script( 'select2', plugin_dir_url( __FILE__ ) . "js/select2{$min}.js", array('jquery'), "4.0.3" );
			wp_enqueue_style( 'events-manager-io', plugin_dir_url( __FILE__ ) . 'css/events-manager-io.css', array(), EMIO_VERSION );
			wp_enqueue_script( 'events-manager-io', plugin_dir_url( __FILE__ ) . 'js/events-manager-io.js', array('jquery','jquery-ui-core','jquery-ui-widget','jquery-ui-mouse','jquery-ui-sortable'), EMIO_VERSION );
		}
		wp_localize_script('events-manager-io', 'EMIO', array('google_api'=>EMIO_Options::get('google_server_key') !== ''));
	}
	
	public static function import(){
		EMIO_Loader::import_admin();
		EMIO_Admin_Import::init();
	}
	
	public static function export(){
		EMIO_Loader::export_admin();
		EMIO_Admin_Export::init();
	}

    public static function settings(){
        EMIO_Loader::admin_settings();
        EMIO_Admin_Settings::main();
    }
}
EMIO_Admin::init();