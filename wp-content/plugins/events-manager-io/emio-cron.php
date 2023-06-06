<?php
/**
 * EMIO_Cron manages cron jobs within WP, calling all available cron frequencies and executing import/export jobs.
 */
class EMIO_Cron {
	
	/**
	 * @var array Contains an associative array of frequencies, where the key represents the cront schedule key in WP.
	 * 		interval	-	represents the numeric interval between crons or a callable to manually set the next upcoming scheduled cron.
	 * 		display		-	the displayed name for this frequency, used in dropdown menus and import/export update frequency descriptions.
	 */
	public static $frequencies = array();
	
	/**
	 * Defines the default frequencies, merges in custom frequencies, adds filter for adding them into wp cron schedules and sets the scheduled crons.
	 */
	public static function init(){
		self::$frequencies['hourly'] = array();
		self::$frequencies['twicedaily'] = array();
		self::$frequencies['daily'] = array();
		self::$frequencies['twiceweekly'] = array('interval' => 302400, 'display' => __('Twice Weekly', 'events-manager-io'));
		self::$frequencies['weekly'] = array('interval' => 604800, 'display' => __('Weekly', 'events-manager-io'));
		self::$frequencies['twoweeks'] = array('interval' => 1209600, 'display' => __('Every Two Weeks', 'events-manager-io'));
		self::$frequencies['monthly'] = array('interval' => 'EMIO_Cron::schedule_monthly', 'display' => __('Monthly', 'events-manager-io'));
		$schedules = wp_get_schedules();
		//merge default frequencies with custom frequencies
		self::$frequencies = apply_filters('emio_cron_schedules', self::$frequencies);
		//add custom schedules for custom cron keys
		add_filter('cron_schedules', 'EMIO_Cron::schedules');
		//manage schedules for these and register actions
		foreach( self::$frequencies as $key => $freq ){
			//pre-fill default WP schedules
			if( empty($freq['display']) ) self::$frequencies[$key]['display'] = !empty($schedules[$key]['display']) ? $schedules[$key]['display'] : $key;
			//schedule if not already
			if( !wp_next_scheduled('emio_cron_'.$key) ){
				if( empty($freq['interval']) || is_numeric($freq['interval']) ){
					wp_schedule_event(time(), $key, 'emio_cron_'.$key);
				}elseif( is_callable($freq['interval']) ){
					call_user_func($freq['interval']);
				}
			}
			add_action('emio_cron_'.$key, 'EMIO_Cron::cron_'.$key );
		}
		do_action('emio_cron');
	}
	
	public static function unschedule(){
		foreach( self::$frequencies as $key => $freq ){
			wp_clear_scheduled_hook('emio_cron_'.$key);
		}
	}
	
	/**
	 * Catchall for cron callbacks, function names would be in the format of cron_[schedule] and all Imports/Exports of [schedule] active now will run.
	 * @param string $name
	 */
	public static function __callStatic( $name, $args ){
		global $current_user;
		//copy $current_user for later on
		$old_user = $current_user;
		//get frequency name from called function
		$frequency = str_replace('cron_', '', $name);
		//search all imports with this frequency and run them now
		EMIO_Loader::import();
		$EMIO_Imports = new EMIO_Imports( array('frequency' => $frequency, 'frequency_scope' => 'now', 'status'=>true) );
		foreach( $EMIO_Imports as $EMIO_Import ){ /* @var $EMIO_Import EMIO_Import */
			//change current user to be the user who owns the import, to circumvent permission issues
			$current_user = new WP_User($EMIO_Import->user_id);
			//run the import
			$EMIO_Import->run();
		}
		//reset the current user
		$current_user = $old_user;
		/* @todo add export crons */
	}
	
	/**
	 * Callback for cron_schedules filter, adding our own schedules to the WP Cron system.
	 * @param array $schedules
	 * @return array
	 */
	public static function schedules( $schedules ){
		foreach( self::$frequencies as $k => $freq ){
			if( !empty($freq['interval']) && is_numeric($freq['interval']) ){
				$schedules[$k] = $freq;
			}
		}
		return $schedules;
	}
	
	/**
	 * Callback to schedule a monthly cron. 
	 */
	public static function schedule_monthly(){
		//monthly is special, we do one-offs and schedule next month here automatically
		$EM_DateTime = new EM_DateTime();
		wp_schedule_single_event($EM_DateTime->modify('first day of next month')->getTimestamp(), 'emio_cron_monthly');
	}
	
	public static function get_name( $frequency ){
		if( !empty( static::$frequencies[$frequency]['display']) ){
			return static::$frequencies[$frequency]['display'];
		}else{
			return __('Unregistered Frequency', 'events-manager-io');
		}
	}
}
//initiate our cron
EMIO_Cron::init();
//$EMIO_Cron->unschedule(); //for testing/debugging
//add_action('init', function(){ do_action('emio_cron_hourly'); });