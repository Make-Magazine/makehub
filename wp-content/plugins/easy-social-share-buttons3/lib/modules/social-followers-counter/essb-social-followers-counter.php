<?php

class ESSBSocialFollowersCounter {
	
	private $version = '4.0';
	private $essb3_cache_option_name = 'essbfcounter_cached';
	private $essb3_expire_name = 'essbfcounter_expire';
	private $updater_instance;
	
	private $should_update = false;
	private $force_update_method = false;
	
	function __construct() {

		// include updater class
		include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/social-followers-counter/essb-social-followers-counter-updater.php');
		
		// include visual draw class
		if (!class_exists('ESSBSocialFollowersCounterDraw')) {
            include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/social-followers-counter/essb-social-followers-counter-draw.php');
		}
		
		add_shortcode( 'essb-fans' , array ( $this , 'register_plugin_shortcodes' ) );
		add_shortcode( 'easy-fans' , array ( $this , 'register_plugin_shortcodes' ) );
		add_shortcode( 'easy-followers' , array ( $this , 'register_plugin_shortcodes' ) );
		add_shortcode( 'easy-total-fans' , array ( $this , 'register_plugin_shortcode_totalfans' ) );
		add_shortcode( 'easy-total-followers' , array ( $this , 'register_plugin_shortcode_totalfans' ) );
		add_shortcode( 'easy-followers-layout' , array ( $this , 'register_plugin_shortcodes_layout' ) );
		add_shortcode( 'followme-bar', array($this, 'register_shortcode_followme_bar'));				
		
		if (essb_option_bool_value('fanscounter_sidebar')) {
			add_action( 'wp_footer', array ($this, 'draw_followers_sidebar'), 99);
		}
		
		if (essb_option_bool_value('fanscounter_postbar')) {
			add_filter( 'the_content', array ($this, 'draw_followers_postbar'), 99);
		}
				
		/**
		 * @var ESSBSocialFollowersCounter $should_update
		 */
		$this->should_update = $this->should_update_followers();
		
		/**
		 * Loading Module Assets
		 */
		if (!class_exists('ESSBSocialFollowersCounterAssets')) {
		    // include visual draw class
		    include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/social-followers-counter/essb-social-followers-counter-assets.php');
		}
		ESSBSocialFollowersCounterAssets::init();
	}	
	
	/**
	 * Automatically assign follow me bar below post content
	 * 
	 * @param unknown_type $content
	 */
	public function draw_followers_postbar($content = '') {
		if (essb_is_plugin_deactivated_on() || essb_is_module_deactivated_on('fanscounter')) {
			return $content;
		}
		
		if (!is_singular()) {
			return $content;
		}
		
		$profile_bar = ESSBSocialFollowersCounterDraw::draw_followers_bar();
		
		return $content.$profile_bar;
	}
	
	/**
	 * Generate and draw the shortcode [followme-bar]
	 * 
	 * @param unknown_type $atts
	 */
	public function register_shortcode_followme_bar($atts = array()) {
		return ESSBSocialFollowersCounterDraw::draw_followers_bar();
	}
	
	public function register_plugin_shortcodes($attrs) {
		$default_options = ESSBSocialFollowersCounterHelper::default_instance_settings();	
		
		$attrs = shortcode_atts( $default_options , $attrs );
		
		ob_start();
		ESSBSocialFollowersCounterDraw::draw_followers($attrs, true);
		$html = ob_get_contents();
		ob_end_clean();
		
		return $html;
	}
	
	public function register_plugin_shortcodes_layout($attrs) {
		$default_options = ESSBSocialFollowersCounterHelper::default_instance_settings();
		
		
		$attrs = shortcode_atts( $default_options , $attrs );
		
		ob_start();
		ESSBSocialFollowersCounterDraw::draw_followers($attrs, false, true);
		$html = ob_get_contents();
		ob_end_clean();
		
		return $html;
		
	}
	
	/**
	 * register_plugin_shortcode_totalfans
	 * 
	 * handle [easy-total-fans] shortcode
	 * @since 3.4
	 * 
	 */
	public function register_plugin_shortcode_totalfans($attrs) {

		$counters = $this->get_followers();
		
		$total = 0;
		foreach ($counters as $network => $follow_count) {
			if (intval($follow_count) > 0) {
				$total += $follow_count;
			}
		}
		
		return ESSBSocialFollowersCounterDraw::followers_number($total);
	}
	
	/**
	 * require_counter_update
	 * 
	 * check and make update of social counters uppon cache expiration
	 * 
	 * @return boolean
	 * @since 3.4
	 */
	public function require_counter_update() {
		$expire_time = get_option ( $this->essb3_expire_name );
		$now = time ();
		
		$is_alive = ($expire_time > $now);
		
		/**
		 * @since 7.7 / Filter integrated
		 */
		if (has_filter("essb_followers_counter_update_is_live")) {
		    $is_alive = apply_filters("essb_followers_counter_update_is_live", $is_alive);
		}
				
		if (true == $is_alive) {
			return false;
		}
		
		return true;
	}
	
	public function should_update_followers() {
	    $request_update = $this->require_counter_update();
	    
	    $counters = array();
	    
	    // if it is not required we load the counters from cache
	    if (!$request_update) {
	        $counters = get_option ( $this->essb3_cache_option_name );
	        
	        /**
	         * @since 7.7 / Filter integrated
	         */
	        if (has_filter("essb_followers_counter_values")) {
	            $counters = apply_filters("essb_followers_counter_values", $counters);
	        }
	        
	        // does not exist cached counters - initiate full counter update
	        if (!isset($counters)) {
	            $request_update = true;
	        }
	        else {
	            if (!is_array($counters)) {
	                $request_update = true;
	            }
	        }
	    }
	    
	    /**
	     * Manually cal update of social followers via the query option
	     */
	    if (!$request_update && isset($_GET['update_followers'])) {
	        $request_update = true;
	    }
	    
	    return $request_update;
	}
	
	/**
	 * get_followers
	 * 
	 * get value of followers as object
	 * 
	 * @since 3.4
	 * @return array
	 */
	public function get_followers() {
		// check previously stored time for expiration based on user settings
		$request_update = $this->require_counter_update();
		
		$counters = array();
		
		// if it is not required we load the counters from cache
		if (!$request_update) {
			$counters = get_option ( $this->essb3_cache_option_name );
			
			// does not exist cached counters - initiate full counter update
			if (!isset($counters)) {
				$request_update = true;
			}
			else {
				if (!is_array($counters)) {
					$request_update = true;
				}
			}
		}
		
		/**
		 * Manually cal update of social followers via the query option
		 */
		if (!$request_update && isset($_GET['update_followers'])) {
			$request_update = true;
		}
		
		if ($request_update) {
			$counters = $this->update_all_followers();
			
			ESSB_Runtime_Cache::set('followers_counter_update', true);
		}
		
		/**
		 * @since 7.7 Counter value filter
		 */
		if (has_filter("essb_followers_counter_values")) {
		    $counters = apply_filters("essb_followers_counter_values", $counters);
		}
		
		return $counters;
	}
	
	public function settle_immediate_update() {
		delete_option($this->essb3_expire_name);
	}
	
	public function clear_stored_values() {
		delete_option($this->essb3_cache_option_name);
	}
	
	public function updater() {
		if (!$this->updater_instance) {
			$this->updater_instance = new ESSBSocialFollowersCounterUpdater;
		}
		
		return $this->updater_instance;
	}
	
	public function update_manual_value($social) {
		return ESSBSocialFollowersCounterHelper::get_option($social.'_uservalue');
	}
	
	/**
	 * update_all_followers
	 * 
	 * make full counter update of all active social networks from the list
	 * 
	 * @since 3.4
	 */
	public function update_all_followers() {
		$counters = array();		
		
		if (class_exists('ESSB_Logger_Followers_Update')) {
		    ESSB_Logger_Followers_Update::clear();
		}		
		
		$require_check_in_cache = false;
				
		/**
		 * @since 7.9 Instagram number of followers goes manually
		 */			
		foreach ( $this->active_social_networks() as $social ) {
			switch ($social) {
				case 'twitter' :
					$count = $this->updater()->update_twitter ();
					break;
				case 'facebook' :
					$count = $this->updater()->update_facebook ();
					break;
				case 'google' :
					$count = $this->updater()->update_googleplus ();
					break;
				case 'pinterest' :
					$count = $this->updater()->update_pinterest ();
					break;
				case 'linkedin' :
					/**
					 * @since 8.4 - moved to update manually only
					 */
				    //$count = $this->updater()->update_linkedin_token ();
				    $count = $this->update_manual_value($social);
					break;
				case 'vimeo' :
					$count = $this->updater()->update_vimeo ();
					break;
				case 'github' :
					$count = $this->updater()->update_github ();
					break;
				case 'dribbble' :
					$count = $this->updater()->update_dribbble ();
					break;
				case 'envato' :
					$count = $this->updater()->update_envato ();
					break;
				case 'soundcloud' :
					$count = $this->updater()->update_soundcloud ();
					break;
				case 'behance' :
					$count = $this->updater()->update_behance ();
					break;
				case 'foursquare' :
					$count = $this->updater()->update_foursquare ();
					break;
				case 'forrst' :
					$count = $this->updater()->update_forrst ();
					break;
				case 'mailchimp' :
					$count = $this->updater()->update_mailchimp ();
					break;
				case 'delicious' :
					$count = $this->updater()->update_delicious ();
					break;
				/*
				 * @since 7.9 deprecated Instagram does not allow access to value of followers anymore
				 * case 'instgram':
				 * case 'instagram' :
				 *	$count = $this->updater()->update_instagram ();
				 *	break;
				 */
				case 'youtube' :
					$count = $this->updater()->update_youtube ();
					break;
				case 'vk' :
					$count = $this->updater()->update_vk ();
					break;
				case 'rss' :
					$count = $this->updater()->update_rss ();
					break;
				case 'vine' :
					$count = $this->updater()->update_vine ();
					break;
				case 'tumblr' :
					$count = $this->updater()->update_tumblr ();
					break;
				case 'slideshare' :
					$count = $this->updater()->update_slideshare ();
					break;
				case '500px' :
					$count = $this->updater()->update_c500Px ();
					break;
				case 'flickr' :
					$count = $this->updater()->update_flickr ();
					break;
				case 'wp_posts' :
					$count = $this->updater()->update_wpposts ();
					break;
				case 'wp_comments' :
					$count = $this->updater()->update_wpcomments ();
					break;
				case 'wp_users' :
					$count = $this->updater()->update_wpusers ();
					break;
				case 'audioboo' :
					$count = $this->updater()->update_audioboo ();
					break;
				case 'steamcommunity' :
					$count = $this->updater()->update_steamcommunity ();
					break;
				case 'weheartit' :
					$count = $this->updater()->update_weheartit ();
					break;
				case 'feedly' :
					$count = $this->updater()->update_feedly ();
					break;
				case 'love' :
					$count = $this->updater()->update_love ();
					break;
				case 'spotify':
					$count = $this->updater()->update_spotify();
					break;
				case 'twitch':
					$count = $this->updater()->update_twitch();
					break;
				case 'mymail':
					$count = $this->updater()->update_mymail();
					break;
				case 'mailpoet':
					$count = $this->updater()->update_mailpoet();
					break;
				case 'mailerlite':
				case 'itunes':
				case 'deviantart':
				case 'paypal':
				case 'whatsapp':
				case 'tripadvisor':
				case 'snapchat':
				case 'telegram':
				case 'subscribe':
				case 'xing':
				case 'medium':
				case 'tiktok':
				case 'mixer':
				case 'patreon':
				case 'ok':
				case 'subscribe_form':
				case 'periscope':
				case 'instgram':
				case 'instagram' :
					$count = $this->update_manual_value($social);
					break;
				default :
					$count = 0;
					break;
			}
			
			if (has_filter("essb4_followers_{$social}_counter")) {
				$count = apply_filters("essb4_followers_{$social}_counter", $social);
			}
			
			if (has_filter("essb_get_followers_{$social}_counter")) {
			    $count = apply_filters("essb_get_followers_{$social}_counter", $social);
			}
			
			$counters[$social] = $count;
			
			if (empty($count)) {
				$require_check_in_cache = true;
			}
		}
		
		/**
		 * @since 7.6 User values are always ON
		 */
		$is_active_selfcounts = true;
		
		if ($is_active_selfcounts) {
			foreach ( $this->active_social_networks() as $social ) {
				$user_value = ESSBSocialFollowersCounterHelper::get_option($social.'_uservalue');
				$count = isset($counters[$social]) ? $counters[$social] : 0;
				
				if (intval($user_value) > intval($count)) {
					$count = $user_value;
					$counters[$social] = $count;
				}
			}
		}
		
		if ($require_check_in_cache) {
			// apply additional check for previously cached counters for blanked values
			$cached_counters = get_option ( $this->essb3_cache_option_name );
			
			foreach ( $this->active_social_networks() as $social ) {
				$prev_value = isset($cached_counters[$social]) ? $cached_counters[$social] : 0;
				$new_value = isset($counters[$social]) ? $counters[$social] : 0;
				
				if (intval($new_value) < intval($prev_value)) {
					$counters[$social] = $prev_value;
				}
			}
		}
		
		$expire_time = ESSBSocialFollowersCounterHelper::get_option ( 'update' );
		
		if ($expire_time == '' || intval($expire_time) == 0) {
			$expire_time = 1440;
		}
		
		update_option ( $this->essb3_cache_option_name, $counters );
		update_option ( $this->essb3_expire_name, (time () + ($expire_time * 60)) );
		
		return $counters;
	}
	
	/**
	 * active_social_networks
	 * 
	 * Generate list of available social networks 
	 * @return array
	 * @since 3.4
	 */
	public function active_social_networks() {
		$networks_order = ESSBSocialFollowersCounterHelper::get_active_networks_order();
		$networks = ESSBSocialFollowersCounterHelper::get_active_networks();
		
		$result = array ();
		
		foreach ( $networks_order as $social ) {
			if (in_array($social, $networks)) {
				if ($this->is_properly_configured ( $social )) {
						
					$result [] = $social;
				}
			}
		}		
		
		/**
		 * @since 7.7 / Filter integrated
		 */
		if (has_filter("essb_followers_active_networks")) {
		    $result = apply_filters("essb_followers_active_networks", $result);
		}

		return $result;
	}
	
	/**
	 * is_properly_configured
	 * 
	 * Check active social networks to ensure is the activated networks properly set
	 * 
	 * @param string $social
	 * @return boolean
	 */
	private function is_properly_configured($social) {
	
		switch ($social) {
			case 'instagram':
			case 'instgram':
				return ESSBSocialFollowersCounterHelper::get_option ( $social . '_username' );
				break;
			case 'mailchimp' :
				return ESSBSocialFollowersCounterHelper::get_option ( $social . '_list_id' );
				break;
			case 'rss' :
				return ESSBSocialFollowersCounterHelper::get_option ( $social . '_link' );
				break;
			case 'feedly' :
				return ESSBSocialFollowersCounterHelper::get_option ( $social . '_url' );
				break;
			case 'vine' :
			case 'slideshare' :
			case '500px' :
				return ESSBSocialFollowersCounterHelper::get_option ( $social . '_username' );
				break;
			case 'tumblr' :
				return ESSBSocialFollowersCounterHelper::get_option ( $social . '_basename' );
				break;
			case 'wp_posts' :
			case 'wp_comments' :
			case 'wp_users' :
			case 'love':
			case 'subscribe_form':
				return true;
				break;
				
			case 'itunes':
			case 'deviantart':
			case 'paypal':
			case 'whatsapp':
			case 'tripadvisor':
			case 'snapchat':
			case 'telegram':
			case 'subscribe':
			case 'xing':
			case 'medium':
			case 'tiktok':
			case 'mixer':
			case 'patreon':
			case 'ok':
			case 'periscope': 
				return ESSBSocialFollowersCounterHelper::get_option ( $social . '_url' );
				break;
			default :
			    /**
			     * @since 8.8.2 Compatibility with custom networks (they support URL)
			     */			    
			    $network_value = ESSBSocialFollowersCounterHelper::get_option ( $social . '_id' );
			    if (empty($network_value)) {
			        $network_value = ESSBSocialFollowersCounterHelper::get_option ( $social . '_url' );
			    }
			    
			    return $network_value;
				break;
		}
	}
	
	/**
	 * create_follow_address
	 * 
	 * Generate social follow address based on user settings
	 * 
	 * @param string $social
	 * @return string
	 * @since 3.4
	 */
	public static function create_follow_address($social) {
	
		switch ($social) {
			case 'facebook' :
				return 'https://www.facebook.com/' . ESSBSocialFollowersCounterHelper::get_option ( $social . '_id' );
				break;
			case 'twitter' :
				return 'https://www.twitter.com/' . ESSBSocialFollowersCounterHelper::get_option ( $social . '_id' );
				break;
			case 'google' :
				return 'https://plus.google.com/' . ESSBSocialFollowersCounterHelper::get_option ( $social . '_id' );
				break;
			case 'pinterest' :
				return 'https://www.pinterest.com/' . ESSBSocialFollowersCounterHelper::get_option ( $social . '_id' );
				break;
			case 'linkedin' :
				
				$type =  ESSBSocialFollowersCounterHelper::get_option ( $social . '_type' );
				if ($type == 'profile') {				
					return ESSBSocialFollowersCounterHelper::get_option ( $social . '_id' );
				}
				else {
					return 'https://www.linkedin.com/company/'.ESSBSocialFollowersCounterHelper::get_option ( $social . '_id' );
				}
				break;
			case 'github' :
				return 'https://github.com/' . ESSBSocialFollowersCounterHelper::get_option ( $social . '_id' );
				break;
			case 'vimeo' :
				if (ESSBSocialFollowersCounterHelper::get_option ( $social . '_account_type', 'channel' ) == 'user') {
					{
						$vimeo_id = trim ( ESSBSocialFollowersCounterHelper::get_option ( $social . '_id' ) );
	
						if (preg_match ( '/^[0-9]+$/', $vimeo_id )) {
							return 'http://vimeo.com/user' . $vimeo_id;
						} else {
							return 'http://vimeo.com/' . $vimeo_id;
						}
					}
				} else {
					return 'http://vimeo.com/channels/' . ESSBSocialFollowersCounterHelper::get_option ( $social . '_id' );
				}
				break;
			case 'dribbble' :
				return 'https://dribbble.com/' . ESSBSocialFollowersCounterHelper::get_option ( $social . '_id' );
				break;
			case 'soundcloud' :
				return 'https://soundcloud.com/' . ESSBSocialFollowersCounterHelper::get_option ( $social . '_id' );
				break;
			case 'behance' :
				return 'https://www.behance.net/' . ESSBSocialFollowersCounterHelper::get_option ( $social . '_id' );
				break;
			case 'foursquare' :
				if (intval ( ESSBSocialFollowersCounterHelper::get_option ( $social . '_id' ) ) && intval ( ESSBSocialFollowersCounterHelper::get_option ( $social . '_id' ) ) == ESSBSocialFollowersCounterHelper::get_option ( $social . '_id' )) {
					return 'https://foursquare.com/user/' . ESSBSocialFollowersCounterHelper::get_option ( $social . '_id' );
				} else {
					return 'https://foursquare.com/' . ESSBSocialFollowersCounterHelper::get_option ( $social . '_id' );
				}
				break;
			case 'forrst' :
				return 'http://forrst.com/people/' . ESSBSocialFollowersCounterHelper::get_option ( $social . '_id' );
				break;
			case 'mailchimp' :
				return ESSBSocialFollowersCounterHelper::get_option ( $social . '_list_url' );
				break;
			case 'delicious' :
				return 'https://delicious.com/' . ESSBSocialFollowersCounterHelper::get_option ( $social . '_id' );
				break;
			case 'instgram' :
			case 'instagram' :
				return 'https://instagram.com/' . ESSBSocialFollowersCounterHelper::get_option ( $social . '_username' );
				break;
			case 'youtube' :
				$account_type = ESSBSocialFollowersCounterHelper::get_option ( $social . '_account_type' );
				$channel_url_type = ESSBSocialFollowersCounterHelper::get_option ( $social . '_url_type' );
				
				if ($channel_url_type != '' && $account_type == 'channel') { $account_type = $channel_url_type; }
				
				$url = 'https://www.youtube.com/' . $account_type . '/' . ESSBSocialFollowersCounterHelper::get_option ( $social . '_id' );
				
				// added support for custom URLs
				$custom_url = ESSBSocialFollowersCounterHelper::get_option ( $social . '_url' );
				if (!empty($custom_url)) {
					$url = $custom_url;
				}
				
				return $url;
				break;
			case 'envato' :
				$ref = '';
				if (ESSBSocialFollowersCounterHelper::get_option ( $social . '_ref' )) {
					$ref = '?ref=' . ESSBSocialFollowersCounterHelper::get_option ( $social . '_ref' );
				}
				return 'https://www.' . ESSBSocialFollowersCounterHelper::get_option ( $social . '_site' ) . '.net/user/' . ESSBSocialFollowersCounterHelper::get_option ( $social . '_id' ) . $ref;
				break;
			case 'vk' :
				$account_type = ESSBSocialFollowersCounterHelper::get_option ( $social . '_account_type' );
				if ($account_type == 'community') {
					return 'https://www.vk.com/' . ESSBSocialFollowersCounterHelper::get_option ( $social . '_id' );
				}
				else {
					return 'https://www.vk.com/id' . ESSBSocialFollowersCounterHelper::get_option ( $social . '_id' );
				}
				break;
			case 'rss' :
				return ESSBSocialFollowersCounterHelper::get_option ( $social . '_link' );
				break;
			case 'vine' :
				return 'https://vine.co/' . ESSBSocialFollowersCounterHelper::get_option ( $social . '_username' );
				break;
			case 'tumblr' :
				$basename2arr = explode ( '.', ESSBSocialFollowersCounterHelper::get_option ( $social . '_basename' ) );
				if ($basename2arr == 'www')
					return 'http://' . ESSBSocialFollowersCounterHelper::get_option ( $social . '_basename' );
				else
					return 'https://www.tumblr.com/follow/' . @$basename2arr [0];
				break;
			case 'slideshare' :
				return 'https://www.slideshare.net/' . ESSBSocialFollowersCounterHelper::get_option ( $social . '_username' );
				break;
			case '500px' :
				return 'https://500px.com/' . ESSBSocialFollowersCounterHelper::get_option ( $social . '_username' );
				break;
			case 'flickr' :
				return 'https://www.flickr.com/photos/' . ESSBSocialFollowersCounterHelper::get_option ( $social . '_id' );
				break;
			case 'wp_posts' :
			case 'wp_users' :
			case 'wp_comments' :
				return ESSBSocialFollowersCounterHelper::get_option ( $social . '_url' );				
				break;
			case 'audioboo' :
				return 'https://audioboo.fm/users/' . ESSBSocialFollowersCounterHelper::get_option ( $social . '_id' );
				break;
			case 'steamcommunity' :
				return 'https://steamcommunity.com/groups/' . ESSBSocialFollowersCounterHelper::get_option ( $social . '_id' );
				break;
			case 'weheartit' :
				return 'https://weheartit.com/' . ESSBSocialFollowersCounterHelper::get_option ( $social . '_id' );
				break;
			case 'love' :
				return ESSBSocialFollowersCounterHelper::get_option ( $social . '_url' );
				break;
			case 'total' :
				return ESSBSocialFollowersCounterHelper::get_option ( $social . '_url' );
				break;
			case 'feedly' :
				return 'https://feedly.com/i/subscription/feed' . urlencode ( '/' . ESSBSocialFollowersCounterHelper::get_option ( $social . '_url' ) );
				break;
			case 'mymail':
				return ESSBSocialFollowersCounterHelper::get_option ( $social . '_url' );
				break;
			case 'mailpoet':
				return ESSBSocialFollowersCounterHelper::get_option ( $social . '_url' );
				break;
			case 'twitch' :
				return 'https://www.twitch.tv/' . ESSBSocialFollowersCounterHelper::get_option ( $social . '_id' ).'/profile';
				break;
			case 'spotify' :
				return ESSBSocialFollowersCounterHelper::get_option ( $social . '_id' );
				break;
			case 'mailerlite':
				return ESSBSocialFollowersCounterHelper::get_option ( $social . '_id' );
				break;
			case 'subscribe_form':
			    return ESSBSocialFollowersCounterHelper::get_option ( $social . '_design' );
			    break;
			case 'itunes':
			case 'deviantart':
			case 'paypal':
			case 'whatsapp':
			case 'tripadvisor':
			case 'snapchat':
			case 'telegram':
			case 'subscribe':
			case 'xing':
			case 'medium':
			case 'tiktok':
			case 'mixer':
			case 'patreon':
			case 'ok':
			case 'periscope':
				return ESSBSocialFollowersCounterHelper::get_option ( $social . '_url' );
				break;				
		}
		
		if (has_filter("essb4_followers_{$social}_url")) {
			return apply_filters("essb4_followers_{$social}_url", $social);
		}
		
	}
	
	public function draw_followers_sidebar() {
	    
	    /**
	     * @since 8.0 - validate the generation of the profiles
	     */
	    if (ESSB_Plugin_Loader::is_module_deactivated('followers_sidebar')) {
	        return;
	    }
		
		$options = array('position' => '', 'template' => '', 'animation' => '', 'nospace' => '', 'width' => '');
		
		$sidebar_template = ESSBSocialFollowersCounterHelper::get_option('sidebar_template');
		$sidebar_animation = ESSBSocialFollowersCounterHelper::get_option('sidebar_animation');
		$sidebar_nospace = ESSBSocialFollowersCounterHelper::get_option('sidebar_nospace');
		$sidebar_position = ESSBSocialFollowersCounterHelper::get_option('sidebar_position');
		$sidebar_width = ESSBSocialFollowersCounterHelper::get_option('sidebar_width');
		$sidebar_orientation = ESSBSocialFollowersCounterHelper::get_option('sidebar_orientation');
		$sidebar_total = ESSBSocialFollowersCounterHelper::get_option('sidebar_total');
		
		if ($sidebar_orientation == '') { $sidebar_orientation = 'h'; }
		
		if ($sidebar_template != '') {
			$options['template'] = $sidebar_template;
		}
		else {
			$options['template'] = 'flat';
		}
		
		$options['animation'] = $sidebar_animation;
		$options['nospace'] = ($sidebar_nospace == 'true') ? 1 : 0;
		$options['position'] = ($sidebar_position != '') ? $sidebar_position: 'left';
		$options['width'] = $sidebar_width;
		$options['button'] = $sidebar_orientation;
		
		$options['total'] = ($sidebar_total == 'true') ? 1 : 0;
		
		ESSBSocialFollowersCounterDraw::draw_followers_sidebar($options);
	}
	
	/**
	 * @deprecated 7.9
	 *
	 * The function is depreacted and pending removale due to Instagram API change
	 */
	public function ajax_load_cache_js() {
	    echo '';
	    die();
	}
	
	/**
	 * @deprecated 7.9
	 * Instagram API does not accept anymore public connection for reading profiles.
	 */
	public function push_instagram_update_action() {
	    
	    __doing_it_wrong( 'push_instagram_update_action', 'This method is deprecated because of Instagram API change' );
	    
	}	
}