<?php
    /***************** File paths ******************/
    define('WPVC_MODEL_PATH',WPVC_VOTES_ABSPATH.'wpvc_model/');
    define('WPVC_CONTROLLER_PATH',WPVC_VOTES_ABSPATH.'wpvc_controller/');
    define('WPVC_CONTROLLER_XL_PATH',WPVC_VOTES_ABSPATH.'wpvc_controller/xl_classes/');
    define('WPVC_VIEWS',WPVC_VOTES_ABSPATH.'wpvc_views/');
    define('WPVC_VIEW_PATH',WPVC_VOTES_ABSPATH.'wpvc_views/admin/');
    define('WPVC_FR_VIEW_PATH',WPVC_VOTES_ABSPATH.'wpvc_views/frontend/');
    define('WPVC_ASSETS_JS_PATH',WPVC_VOTES_SL_PLUGIN_URL.'assets/js/');
    define('WPVC_ASSETS_CSS_PATH',WPVC_VOTES_SL_PLUGIN_URL.'assets/css/');
    define('WPVC_ASSETS_SETUP_CSS_PATH',WPVC_VOTES_SL_PLUGIN_URL.'assets/css/wpvc_setup.css');
    define('WPVC_ASSETS_UPLOAD_PATH',WPVC_VOTES_ABSPATH.'assets/uploads/');
    define('WPVC_ASSETS_IMAGE_PATH',WPVC_VOTES_SL_PLUGIN_URL.'assets/images/');
    define('WPVC_NO_IMAGE_CONTEST',WPVC_ASSETS_IMAGE_PATH.'/img_not_available.png');
    define('WPVC_PARENT_TEMPLATE_PATH',get_template_directory().'/ow_templates/');
		define('WPVC_CHILD_TEMPLATE_PATH',get_stylesheet_directory().'/ow_templates/');

    /*********** Program constants ***********/
    define('WPVC_VOTES_TYPE', 'contestants');
    define('WPVC_VOTES_TAXONOMY', 'contest_category');
    define('WPVC_VOTES_POST', 'contest_post_customfields');
    define('WPVC_VOTES_TAXONOMY_ASSIGN', 'contest_category_assign_custom');
    define('WPVC_VOTES_REG_ASSIGN', 'contest_reg_assign_custom');
    define('WPVC_VOTES_CUSTOMFIELD', 'votes_count');
    define('WPVC_VOTES_VIEWERS', 'votes_viewers');
    define('WPVC_VOTES_USER_META', 'votes_user_meta');
    define('WPVC_CONTESTANT_LINK','ow_contestant_link');
    
    define('WPVC_VOTES_EXPIRATIONFIELD', 'votes_expiration');
    define('WPVC_OLD_VOTES_SETTINGS', 'votes_settings');
    define('OLD_BUYVOTES_SETTINGS', 'ow_buyvotes_settings');
    define('OLD_VIDEO_EXTENSION_SETTINGS', 'ow_videoextension_settings');
    define('WPVC_VOTES_SETTINGS', 'wpvc_votes_settings');
    define('WPVC_VOTES_COLORSETTINGS', 'votes_color_settings');
    define('WPVC_VOTES_ACTIVE_THEME', 'votes_current_theme');

    define('WPVC_VOTES_REACT', 'votes_react_file');

    define('WPVC_VOTES_SHOW_DESC', 'list');
    define('WPVC_VOTES_ENTRY_LIMIT_FORM', '');

    define('WPVC_VOTES_TAXEXPIRATIONFIELD', 'votes_taxexpiration');
    define('WPVC_VOTES_TAXACTIVATIONLIMIT', 'votes_taxactivationlimit');
    define('WPVC_VOTES_TAXSTARTTIME', 'votes_taxstarttime');
    define('WPVC_VOTES_GENERALEXPIRATIONFIELD', 'votes_generalexpiration');
    define('WPVC_VOTES_GENERALSTARTTIME', 'votes_generalstarttime');
    define('WPVC_VOTES_CONTESTPHOTOGRAPHERNAME', 'contestant_photographer_name');
    define('WPVC_VOTES_CONTENT_LENGTH', get_option('votesadvancedexcerpt_length'));
    define('WPVC_VOTES_CONTENT_ELLIPSES', get_option('votesadvancedexcerpt_ellipsis'));
    define('WPVC_VOTES_VIEWS', 'votes_viewers');
    
		define('WPVC_DEF_THEME_SUMMER', 'Summer');
    define('WPVC_DEF_THEME', 'Summer,Noel,New_Years,Christmas,Cupcake');
    define('WPVC_DEF_PUBLISHING', 'pending');
    define('WPVC_MOBILE_APP_COLOR_DEFAULT', '#7f70e7');
    define('WPVC_MOBILE_APP_MENU_ENABLE',0);



    define('VOTES_TYPE_PAGIN', 'votes_setting_paginate');
    define('WPVC_VOTES_TEXTDOMAIN', 'wp-pagenavi');

    /*************** Table constants **************/
    define('WPVC_VOTES_TBL', $wpdb->prefix . 'votes_tbl');
    define("WPVC_VOTES_ENTRY_CUSTOM_TABLE", $wpdb->prefix . "votes_custom_field_contestant");
    define("WPVC_VOTES_USER_CUSTOM_TABLE", $wpdb->prefix . "votes_custom_registeration_contestant");
    define("WPVC_VOTES_ENTRY_TRACK", $wpdb->prefix . "votes_post_contestant_track");


    /******** Intialize the needed classes **********/
    require_once('installation.php');
    require_once('helper.php');
    require_once(WPVC_VOTES_SL_PLUGIN_DIR.'wp-tus/wp-tus.php');

    //Load appropriate files
    if (is_admin()){
      $auto_ctrl_files = array('Wpvc_Admin_Controller','Wpvc_Vote_Setup_Controller','Wpvc_Common_Settings_Controller','Wpvc_Contestant_Post_Controller','Wpvc_Widget_Leader_Controller','Wpvc_Widget_Recent_Controller','Wpvc_Export_Controller','Wpvc_Vote_Updater','Wpvc_Vote_License_Controller','Wpvc_Email_Controller');
      $auto_model_files = array('Wpvc_Installation_Model','Wpvc_Migration_Model','Wpvc_Export_Model','Wpvc_Settings_Model','Wpvc_Shortcode_Model','Wpvc_Voting_Save_Model','Wpvc_Voting_Model');
    }else{
      $auto_ctrl_files = array('Wpvc_Admin_Controller','Wpvc_Common_Settings_Controller','Wpvc_Contestant_Post_Controller','Wpvc_Shortcode_Controller','Wpvc_Common_Shortcode_Controller','Wpvc_Excerpt_Controller','Wpvc_Widget_Leader_Controller','Wpvc_Widget_Recent_Controller','Wpvc_Email_Controller');
      $auto_model_files = array('Wpvc_Installation_Model','Wpvc_Migration_Model','Wpvc_Shortcode_Model','Wpvc_Settings_Model','Wpvc_Voting_Save_Model','Wpvc_Voting_Model','Wpvc_Single_Contestant_Model');
    }

    controller_autoload($auto_ctrl_files);
    model_autoload($auto_model_files);

    function controller_autoload($class_name)
    {
      if(!empty($class_name)){
        foreach($class_name as $class_nam):
          $filename = strtolower($class_nam).'.php';
          $file = WPVC_CONTROLLER_PATH.$filename;
  
          if (file_exists($file) == false)
          {
            return false;
          }
          include_once($file);
        endforeach;
      }               
    }

    function model_autoload($class_name)
    {
      if(!empty($class_name)){
        foreach($class_name as $class_nam):
          $filename = strtolower($class_nam).'.php';
          $file = WPVC_MODEL_PATH.$filename;
  
          if (file_exists($file) == false)
          {
            return false;
          }
          include_once($file);
        endforeach;
      }
    }

    function setup_wizard(){
      $mycustomposts = get_posts(array('post_type' => WPVC_VOTES_TYPE, 'numberposts' => 1, 'post_status' => 'any'));
      if (count($mycustomposts) == 0) {
        wp_redirect( admin_url( '/index.php?page=votes-setup' ));
        exit;
      }
      return;
    }

    add_action( 'wp_loaded', 'wpvc_load_rest_afterloaded');
    function wpvc_load_rest_afterloaded() { 
      //Rest class must be added at the last  
      $rest_class = array('Wpvc_Rest_Register_Controller','Wpvc_Rest_Actions_Controller','Wpvc_Common_State_Controller','Wpvc_Front_Rest_Register_Controller','Wpvc_Front_Rest_Actions_Controller');   
      controller_autoload($rest_class);
    }

?>
