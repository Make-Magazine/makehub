<?php
/*
Plugin Name: Events Manager Pro
Plugin URI: https://eventsmanagerpro.com
Description: Supercharge the Events Manager free plugin with extra features to make your events even more successful!
Author: Pixelite
Author URI: https://pixelite.com/
Version: 3.2.8.1
Text Domain: em-pro

Copyright (C) 2023 Pixelite SL
*/

//check pre-requisites
require_once('includes/emp-requirements-check.php');
$requirements = new EMP_Requirements_Check('Events Manager Pro', __FILE__, '5.3');
if( !$requirements->passes(false) ) return;
unset($requirements);

define('EMP_VERSION', '3.2.8.1');
define('EM_MIN_VERSION', '6.1.2.6');
define('EM_MIN_VERSION_CRITICAL', '6.1');
define('EMP_SLUG', plugin_basename( __FILE__ ));
define('EMP_DIR', dirname( __FILE__ )); //an absolute path to this directory

class EM_Pro {

	/**
	 * em_pro_data option
	 * @var array
	 */
	var $data;

	/**
	 * Actions to take upon initial action hook
	 */
	public static function init(){
		global $wpdb;
		//check that an incompatible version of EM is not running
		//check that EM is installed
		if( !defined('EM_VERSION') ){
			add_action('admin_notices','EM_Pro::em_install_warning');
			add_action('network_admin_notices','EM_Pro::em_install_warning');
			return false; //don't load EMP further
		}elseif( version_compare(EM_MIN_VERSION_CRITICAL, get_option('dbem_version'), '>') ){
			//add notice and prevent further loading
			add_action('admin_notices','EM_Pro::em_version_warning_critical');
			add_action('network_admin_notices','EM_Pro::em_version_warning_critical');
			return false;
		}elseif( version_compare(EM_MIN_VERSION, EM_VERSION, '>') && get_option('em_pro_version') != EMP_VERSION ){
			//check that EM is up to date
			$warning = __('Please make sure you have the <a href="http://wordpress.org/extend/plugins/events-manager/">latest version</a> of Events Manager installed, as this may prevent Pro from functioning properly.','em-pro');
			EM_Admin_Notices::add( new EM_Admin_Notice('em-pro-min-version', 'warning', $warning) );
		}
		// Define some tables
		if( EM_MS_GLOBAL ){
			$prefix = $wpdb->base_prefix;
		}else{
			$prefix = $wpdb->prefix;
		}
		define('EM_TRANSACTIONS_TABLE', $prefix.'em_transactions'); //TABLE NAME
		define('EM_EMAIL_QUEUE_TABLE', $prefix.'em_email_queue'); //TABLE NAME
		define('EM_COUPONS_TABLE', $prefix.'em_coupons'); //TABLE NAME
		define('EM_BOOKINGS_RELATIONSHIPS_TABLE', $prefix.'em_bookings_relationships'); //TABLE NAME
		define('EM_TICKETS_BOOKINGS_CHECKINS_TABLE', $prefix.'em_tickets_bookings_checkins'); //TABLE NAME
		define('EM_AUTOMATION_TABLE', $prefix.'em_automation'); //TABLE NAME
		//define('EM_AUTOMATION_LOGS_TABLE', $prefix.'em_automation_logs'); //TABLE NAME
		if( !EM_Pro\License::is_active() ) return false;
		// installation
		if( is_admin() ){ //although activate_plugins would be beter here, superusers don't visit every single site on MS
			add_action('init', 'EM_Pro::install',2);
		}
		// core includes
		if( !class_exists('EM\Feature') ) {
			include('em-feature.php');
		}
		include('emp-formats.php');
		if( is_admin() ){
			include('emp-admin.php');
		}
		//Add extra Styling/JS
		add_action('em_enqueue_styles', 'EM_Pro::em_enqueue_styles');
		add_action('em_public_script_deps', 'EM_Pro::em_public_script_deps');
		add_action('em_enqueue_scripts', 'EM_Pro::em_enqueue_scripts', 1); //added only when EM adds its own scripts
		add_action('em_enqueue_admin_scripts', 'EM_Pro::em_enqueue_scripts', 1); //added only when EM adds its own scripts
		add_action('em_enqueue_admin_styles', 'EM_Pro::em_enqueue_admin_styles', 1); //added only when EM adds its own scripts
		add_action('admin_init', 'EM_Pro::enqueue_admin_script', 1); //specific pages in admin that EMP deals with
	    add_filter('em_wp_localize_script', 'EM_Pro::em_wp_localize_script',10,1);
		//add-ons
		if( get_option('dbem_rsvp_enabled') ){
			//booking-specific features
			include('emp-forms.php'); //form editor
			include('add-ons/bookings-form/bookings-form.php');
			// load gateways
			if( EM_Options::site_get('legacy-gateways', false) || em_constant('EMP_GATEWAY_LEGACY') ) {
				// legacy gateways, containing version freeze from 3.1.5.1 and may disable certain features
				include( 'add-ons/gateways-legacy/gateways.php' );
				include('add-ons/gateways-legacy/emails/emails.php');
				// more add-ons
				if( apply_filters('em_coupons_enabled', true) ) {
					include('add-ons/gateways-legacy/coupons/coupons.php');
				}
			}else{
				include('add-ons/gateways/gateways.php');
				include('add-ons/emails/emails.php');
				// more add-ons
				if( apply_filters('em_coupons_enabled', true) ) {
					include('add-ons/coupons/coupons.php');
				}
			}
			include(EMP_DIR.'/emp-ml.php'); // languages after gateways due to dependent class calls
			// other add-ons
			include('add-ons/manual-transactions/loader.php');
			include('add-ons/dependent-bookings/loader.php');
			include('add-ons/automation/loader.php');
			include('add-ons/user-fields.php');
			include('add-ons/google-maps/google-maps.php');
			include('add-ons/bookings-manager/bookings-manager.php');
			include('add-ons/rsvp/loader.php');
			include('add-ons/attendance/attendance-loader.php');
			include('add-ons/printables/printables.php');
			include('add-ons/waitlists/waitlists.php');
			include('add-ons/toolbox/toolbox.php');
	        if( get_option('dbem_multiple_bookings') ){
			    include('add-ons/multiple-bookings/multiple-bookings.php');
	        }
		}
		//MS Specific stuff
		if( is_multisite() ){
			add_filter('em_ms_globals','EM_Pro::em_ms_globals');
		}
		//done
		do_action('em_pro_loaded');
	}
	
	public static function install(){
	    if( current_user_can('list_users') ){
		    //Upgrade/Install Routine
	    	$old_version = get_option('em_pro_version');
	    	if( version_compare(EMP_VERSION, $old_version, '>') || $old_version == '' || (is_multisite() && !EM_MS_GLOBAL && get_option('emp_ms_global_install')) ){
	    		require_once('emp-install.php');
	    		emp_install();
	    	}
	    }
	}

	public static function em_ms_globals($globals){
		$globals[] = 'dbem_pro_api_key';
		$globals[] = 'dbem_enable_logging';
		return $globals;
	}
	
	/**
	 * Add script dependencies to filter that should be enqueued into EM script loader.
	 * @param array $scripts
	 * @return array
	 */
	public static function em_public_script_deps( $scripts ){
	    global $wp_query;
	    if( ( !empty($wp_query->get_queried_object()->post_type) && $wp_query->get_queried_object()->post_type == EM_POST_TYPE_EVENT) || (!empty($_REQUEST['event_id']) && !empty($_REQUEST['action']) && $_REQUEST['action'] == 'manual_booking') ){
	        $scripts['jquery-ui-datepicker'] = 'jquery-ui-datepicker'; //for the booking form
	    }
	    return $scripts;
	}

	/**
	 * Enqueue scripts when fired by em_enqueue_scripts action.
	 */
	public static function em_enqueue_scripts(){
		wp_enqueue_script('events-manager-pro', plugins_url('includes/js/events-manager-pro.js',__FILE__), array('jquery'), EMP_VERSION); //jQuery will load as dependency
	}

	/**
	 * Add admin scripts for specific pages handled by EM Pro. Fired by admin_init
	 */
	public static function enqueue_admin_script(){
	    global $pagenow;
	    if( !empty($_REQUEST['page']) && ($_REQUEST['page'] == 'events-manager-forms-editor' || ($_REQUEST['page'] == 'events-manager-bookings' && !empty($_REQUEST['action']) && $_REQUEST['action'] == 'manual_booking')) ){
			wp_enqueue_script('events-manager-pro', plugins_url('includes/js/events-manager-pro.js',__FILE__), array('jquery', 'jquery-ui-core','jquery-ui-widget','jquery-ui-position')); //jQuery will load as dependency
			do_action('em_enqueue_admin_scripts');
	    }
	    if( $pagenow == 'user-edit.php' ){
	        //need to include the em script for dates
	        EM_Scripts_and_Styles::admin_enqueue(true);
	    }
	}
	
	/**
	 * Enqueue Pro CSS file when action em_enqueue_admin_styles is fired.
	 */
	public static function em_enqueue_admin_styles(){
	    wp_enqueue_style('events-manager-pro-admin', plugins_url('includes/css/events-manager-pro-admin.css',__FILE__), array(), EMP_VERSION);
	}
	
	/**
	 * Add extra localized JS options to the em_wp_localize_script filter.
	 * @param array $vars
	 * @return array
	 */
	public static function em_wp_localize_script( $vars ){
	    $vars['cache'] = defined('WP_CACHE') && WP_CACHE;
		$vars['api_nonce'] = wp_create_nonce('wp_rest');
	    return $vars;
	}

	/**
	 * Enqueues the CSS required by Pro features. Fired by action em_enqueue_styles which is when EM enqueues it's stylesheet, if it doesn't then this shouldn't either 
	 */
	public static function em_enqueue_styles(){
	    wp_enqueue_style('events-manager-pro', plugins_url('includes/css/events-manager-pro.css',__FILE__), array(), EMP_VERSION);
	}

	public static function em_install_warning(){
		?>
		<div class="error"><p><?php _e('Please make sure you install Events Manager as well. You can search and install this plugin from your plugin installer or download it <a href="http://wordpress.org/extend/plugins/events-manager/">here</a>.','em-pro'); ?> <em><?php _e('Only admins see this message.','em-pro'); ?></em></p></div>
		<?php
	}
	
	public static function em_version_warning_critical(){
		?>
		<div class="error">
			<p><strong><?php echo sprintf(esc_html__('Please make sure you have AT LEAST version %s of Events Manager installed, as this may prevent Pro from functioning properly.','em-pro'), '<code>'.EM_MIN_VERSION_CRITICAL.'</code>'); ?> <em><?php _e('Only admins see this message.','em-pro'); ?></em></strong></p>
			<p><?php _e('Until it is updated, Events Manager Pro will remain inactive to prevent further errors.', 'em-pro'); ?>
		</div>
		<?php
	}
	
	/**
	 * Creates a log entry in plugins/events-manager-logs/$log_name-yyyy-mm-dd-logs.txt
	 * @param string|array|object $log_text
	 * @param string $log_name
	 */
	public static function log($log_text, $log_name = 'general', $force_logging = false){
		if( get_site_option('dbem_enable_logging') || $force_logging ){
			if( !class_exists('EMP_Logs') ){
				include_once('emp-logs.php');
			}
			return EMP_Logs::log($log_text, $log_name);
		}
		return false;
	}

}
add_action( 'plugins_loaded', 'EM_Pro::init' );
include_once('includes/emp-license.php'); //update manager

//Add translation
function emp_load_plugin_textdomain() {
    load_plugin_textdomain('em-pro', false, dirname( plugin_basename( __FILE__ ) ).'/langs');
}
add_action('plugins_loaded', 'emp_load_plugin_textdomain');

/* Creating the wp_events table to store event data*/
function emp_activate() {
	global $wp_rewrite;
   	$wp_rewrite->flush_rules();
}
register_activation_hook( __FILE__,'emp_activate');

/**
 * Handle MS blog deletions
 * @param int $blog_id
 */
function emp_delete_blog( $blog_id ){
	global $wpdb;
	$prefix = $wpdb->get_blog_prefix($blog_id);
	$wpdb->query('DROP TABLE '.$prefix.'em_transactions');
	$wpdb->query('DROP TABLE '.$prefix.'em_coupons');
	$wpdb->query('DROP TABLE '.$prefix.'em_email_queue');
	$wpdb->query('DROP TABLE '.$prefix.'em_bookings_relationships');
}
add_action('delete_blog','emp_delete_blog');

//cron functions - ran here since functions aren't loaded, scheduling done by gateways and other modules
/**
 * Adds a schedule according to EM
 * @param array $shcehules
 * @return array
 */
function emp_cron_schedules($schedules){
	$schedules['em_minute'] = array(
		'interval' => 60,
		'display' => 'Every Minute'
	);
	return $schedules;
}
add_filter('cron_schedules','emp_cron_schedules',10,1);

/**
 * Copied from em_locate_template. Same code, but looks up the folder events-manager-pro in your theme.
 * @param string $template_name
 * @param boolean $load
 * @uses locate_template()
 * @return string
 */
function emp_locate_template( $template_name, $load=false, $args = array() ) {
	//First we check if there are overriding tempates in the child or parent theme
	$located = locate_template(array('plugins/events-manager-pro/'.$template_name));
	// now check the wp-content/plugin-templates/events-manager/ folder
	if( file_exists(WP_CONTENT_DIR.'/plugin-templates/events-manager-pro/'.$template_name) ){
		$located = WP_CONTENT_DIR.'/plugin-templates/events-manager-pro/'.$template_name;
	}else{
		// finally get the plugin from EM if no others exist
		$located = apply_filters('emp_locate_template_default', $located, $template_name, $load, $args);
		$dir_location = plugin_dir_path(__FILE__);
		if ( !$located &&  file_exists( $dir_location.'templates/'.$template_name) ) {
			$located = $dir_location.'templates/'.$template_name;
		}
	}
	$located = apply_filters('emp_locate_template', $located, $template_name, $load, $args);
	if( $located && $load ){
		if( is_array($args) ) extract($args);
		include($located);
	}
	return $located;
}

//Translation shortcut functions for times where WP translation shortcuts for strings in the dbem domain. These are here to prevent the POT file generator adding these translations to the pro translations file
/**
 * Shortcut for the __ function
 * @param string $text
 * @param string $domain
 */
function emp__($text, $domain='events-manager'){
    return translate($text, $domain);
}
/**
 * Shortcut for the _e function
 * @param string $text
 * @param string $domain
 */
function _e_emp($text, $domain='events-manager'){
    echo emp__($text, $domain);
}
/**
 * Shortcut for the esc_html__ function
 * @param string $text
 * @param string $domain
 */
function esc_html__emp($text, $domain='events-manager'){
    return esc_html( translate($text, $domain) );
}
/**
 * Shortcut for the esc_html_e function
 * @param string $text
 * @param string $domain
 */
function esc_html_e_emp($text, $domain='events-manager'){
    echo esc_html__emp($text, $domain);
}
/**
 * Shortcut for the esc_attr__ function
 * @param string $text
 * @param string $domain
 */
function esc_attr__emp($text, $domain='events-manager'){
    return esc_attr( translate( $text, $domain ) );
}
/**
 * Shortcut for the esc_attr_e function
 * @param string $text
 * @param string $domain
 */
function esc_attr_e_emp($text, $domain='events-manager'){
    echo esc_attr__emp($text, $domain);
}
/**
 * Shortcut for the esc_html_x function
 * @param string $text
 * @param string $domain
 */
function esc_html_x_emp($text, $context, $domain='events-manager'){
    return esc_html( translate_with_gettext_context( $text, $context, $domain ) );
}
?>