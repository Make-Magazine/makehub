<?php
$auto_ctrl_files = array('Wpvc_JudgeRest_Controller','Wpvc_Voting_Judge_Updater');	
$auto_model_files = array('Wpvc_Judging_Model');

define('OW_JUDGE_ABSPATH', dirname(dirname(__FILE__)) . '/');
define('OW_JUDGE_SETTINGS', 'ow_judge_settings');
define('OW_JUDGECONTROLLER_PATH',OW_JUDGE_ABSPATH.'wpvc_controller/');
define('OW_JUDGEMODEL_PATH',OW_JUDGE_ABSPATH.'wpvc_model/');
define('OW_JUDGE_IMAGE_PATH', plugin_dir_url(__FILE__).'img/');
define('OW_JUDGE_ASSETS_JS_PATH',plugin_dir_url(__FILE__).'assets/js/');
define('OW_JUDGE_ASSETS_CSS_PATH',plugin_dir_url(__FILE__).'assets/css/');
define('OW_JUDGEVIEW_PATH',OW_JUDGE_ABSPATH.'views/');
define('OW_JUDGEVIEW_ADMIN_APP_PATH',plugins_url('/admin/app/',dirname(__FILE__) ));
define('OW_JUDGEVIEW_ADMIN_VIEW_PATH',OW_JUDGE_ABSPATH.'/admin/views/');
define('WPVC_JUDGE_SCORE','wpvc_judge_score');

global $wpdb;
define('WPVC_JUDGES_TBL',$wpdb->prefix . 'judges_tbl');

//Updater Classes & Functions
define('OW_WP_VOTING_JUDGE_STORE_API_URL', 'http://plugins.ohiowebtech.com');
define('OW_WP_VOTING_JUDGE_PRODUCT_NAME', 'Judging Addon for WP Voting Contest');
define('WPVC_JUDGE_PRODUCT_ID',421096);


controller_judge($auto_ctrl_files);
model_judge($auto_model_files);

function controller_judge($class_name)
{
	if(!empty($class_name)){
	foreach($class_name as $class_nam):
		$filename = strtolower($class_nam).'.php';
		$file = OW_JUDGECONTROLLER_PATH.$filename;
		if (file_exists($file) == false)
		{
			return false;
		}
		include_once($file);
	endforeach;
	}               
}
function model_judge($class_name)
{
	if(!empty($class_name)){
	foreach($class_name as $class_nam):
		$filename = strtolower($class_nam).'.php';
		$file = OW_JUDGEMODEL_PATH.$filename;

		if (file_exists($file) == false)
		{
			return false;
		}
		include_once($file);
	endforeach;
	}
}
	




add_action( 'admin_enqueue_scripts', 'add_juding_admin_tools' );	

//Add the Admin CSS and JS 
function add_juding_admin_tools($hook){	

	$current_page = get_current_screen()->post_type; 

	if ( 'owjudge' == get_post_type() )
        wp_dequeue_script( 'autosave' );

	//if($current_page == 'owjudge'){

		wp_enqueue_style( 'admin-judge-css', plugins_url( 'assets/css/admin-judge-css.css' , dirname(__FILE__) ) );

			//Add Builded React JS 
			wp_enqueue_script(
				'wpvc-judging-runtime-admin',
				plugins_url('/wpvc_views/build/runtime.js', dirname(__FILE__)),
				array('wp-element','wp-i18n'),
				time(), // Change this to null for production
				true
			);	
			wp_enqueue_script(
				'wpvc-judging-vendor-admin',
				plugins_url('/wpvc_views/build/vendors.js', dirname(__FILE__)),
				array('wp-element','wp-i18n'),
				time(), // Change this to null for production
				true
			);	
			//Add Builded React JS 
			wp_enqueue_script(
				'wpvc-judging-admin-react',
				plugins_url('/wpvc_views/build/admin.js', dirname(__FILE__)),
				array('wp-element','wp-i18n'),
				time(), // Change this to null for production
				true
			);	
	//}
}

function ow_load_judging_scripts() {
    global $post;

	//Add Script only in the Single Contestant Page
    // if( is_single() )
    // {
    //     if($post->post_type == WPVC_VOTES_TYPE){
			//wp_enqueue_script('react');
			//Add Builded React JS 
			wp_enqueue_script(
				'wpvc-judging-runtime-front',
				plugins_url('/wpvc_views/build/runtime.js', dirname(__FILE__)),
				array('wp-element','wp-i18n'),
				time(), // Change this to null for production
				true
			);	
			wp_enqueue_script(
				'wpvc-judging-vendor-front',
				plugins_url('/wpvc_views/build/vendors.js', dirname(__FILE__)),
				array('wp-element','wp-i18n'),
				time(), // Change this to null for production
				true
			);	
			wp_enqueue_script(
				'judgingapp',
				plugins_url('/wpvc_views/build/front.js', dirname(__FILE__)),
				array('jquery','wp-element','wp-i18n'),
				time(), // Change this to null for production
				true
			);
	// 	}
    // } 
}

add_action('wp_enqueue_scripts', 'ow_load_judging_scripts',999);

function wpvc_display_judging($post_id){

	if(is_user_logged_in()){
		$term_id = get_the_terms($post_id,WPVC_VOTES_TAXONOMY)[0]->term_id;
		$judging_id = get_term_meta($term_id,'judging_post_id',true);

		//Check Judging Group Assigned or not 
		if($judging_id != null){
			?>
				<input type="hidden" id="term_id" value="<?php echo $term_id; ?>" />
				<input type="hidden" id="owdataURL" value="<?php echo site_url('/'); ?>" />
				<input type="hidden" id="judge_id" value="<?php echo get_current_user_id(); ?>" />
				<input type="hidden" id="post_id" value="<?php echo $post_id; ?>" />
				<div id="judging-front-app"></div>
			<?php
		}
	}

}
add_filter('wpvc_single_contestants_html','wpvc_display_judging',10,1);

add_filter('cat_extension_update_values', function ($updated_terms,$term_id = null){
	if($updated_terms['judging_post_id'] != null && $updated_terms['judging_post_id'] != ""){
		$judge_settings = get_post_meta($updated_terms['judging_post_id'],'judging_data',true);	
		$judge_settings = json_decode($judge_settings);
		$judgeValue = $judge_settings->judgeValue;
		$found_key = array_search($GLOBALS['wpvc_user_id'], array_column($judgeValue, 'ID'));
		$updated_terms['is_judge'] = $found_key;    
	}
	else{
		$updated_terms['is_judge'] = false;  
	}
	
	return $updated_terms;
},99,2);


add_filter('cat_extension_rest', function ($settings)
{
	$settings['judging_post_id'] = ''; 
	$settings['enable_judge_top10'] = ''; 
	$settings['enable_judge_button'] = ''; 

	return $settings;
});


require_once('installation.php');
require_once('metabox.php');