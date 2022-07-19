<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if(!class_exists('Wpvc_Vote_Setup_Controller')){
    class Wpvc_Vote_Setup_Controller{
		
		public function __construct() {

			// if we are here, we assume we don't need to run the wizard again
			// and the user doesn't need to be redirected here
			if(!function_exists('wp_get_current_user')) {
				include(ABSPATH . "wp-includes/pluggable.php"); 
			}
			add_action( 'admin_init', array( $this, 'wp_voting_software_setup_page' ) );
	
		}
						
		public static function wp_voting_software_setup_page(){
			
			if ( empty( $_GET['page'] ) || 'votes-setup' !== $_GET['page'] ) {
				return;
			}

			$data = '';
			if($_POST){
				$data = SELF::wpvc_save_settings_setup();
			}

			require_once(WPVC_VIEW_PATH.'wpvc_setup_view.php');			
			$option = get_option(WPVC_VOTES_SETTINGS);			
			wpvc_setup_view($option, $data);			
			exit;
		}
		
		public static function wpvc_save_settings_setup(){
					
			if(!empty($_POST['contest_name'])){
				$error = '';
				$cat_id = term_exists( $_POST['contest_name'], WPVC_VOTES_TAXONOMY );
				if($cat_id == null){
					$cat_id = wp_insert_term( $_POST['contest_name'], WPVC_VOTES_TAXONOMY, array() );
					$term_id = $cat_id['term_id'];
					$args = array(
						'imgcontest' => isset($_POST['imgcontest'])?$_POST['imgcontest']:NULL,					
						'musicfileenable' => isset($_POST['musicfileenable']) ? $_POST['musicfileenable'] : NULL,
						'vote_count_per_cat' => isset($_POST['vote_count_per_cat']) ? $_POST['vote_count_per_cat'] : NULL,
						'total_vote_count' => isset($_POST['total_vote_count']) ? $_POST['total_vote_count'] : NULL,
						'top_ten_count' => isset($_POST['top_ten_count']) ? $_POST['top_ten_count'] : NULL,
						'authordisplay' => isset($_POST['authordisplay']) ? $_POST['authordisplay'] : NULL,
						'authornamedisplay' => isset($_POST['authornamedisplay']) ? $_POST['authornamedisplay'] : NULL,
					);

                    foreach($args as $key => $value){
                        update_term_meta($term_id, $key, $value);
                    }

					$opt_values = get_option(WPVC_VOTES_SETTINGS);

					$setargs['contest'] = array(
						'vote_onlyloggedcansubmit' => isset($_POST['vote_onlyloggedcansubmit'])?$_POST['vote_onlyloggedcansubmit']:$opt_values['contest']['vote_onlyloggedcansubmit'],
						'onlyloggedinuser' => isset($_POST['vote_onlyloggedinuser']) ? $_POST['vote_onlyloggedinuser'] : $opt_values['contest']['onlyloggedinuser'],
						'vote_tracking_method' => isset($_POST['vote_tracking_method'])?$_POST['vote_tracking_method']:$opt_values['contest']['vote_tracking_method'],
						'vote_frequency_count' => isset($_POST['vote_frequency_count']) ? $_POST['vote_frequency_count'] : $opt_values['contest']['vote_frequency_count'],
						'vote_frequency_hours' => isset($_POST['vote_frequency_hours']) ? $_POST['vote_frequency_hours'] : $opt_values['contest']['vote_frequency_hours'],
						'frequency' => isset($_POST['vote_frequency']) ? $_POST['vote_frequency'] : $opt_values['contest']['frequency'],
						'vote_votingtype' => isset($_POST['vote_votingtype']) ? $_POST['vote_votingtype'] :$opt_values['contest']['vote_votingtype']
					);

                    $settings = array_merge($opt_values,$setargs);
					if($error=='') {
						update_option(WPVC_VOTES_SETTINGS, $settings);
						$shortcode = "[showcontestants id=".$term_id."]";
						return $shortcode;
					}
				}else{
					$term_id = $cat_id['term_id'];						
					$shortcode = "[showcontestants id=".$term_id."]";
					return $shortcode;
				}
			}
		}
    }
    
}else
die("<h2>".__('Failed to load Voting Setup Controller','votes-setup')."</h2>");


return new Wpvc_Vote_Setup_Controller();
