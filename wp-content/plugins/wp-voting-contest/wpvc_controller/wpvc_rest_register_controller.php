<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if(!class_exists('Wpvc_Rest_Register_Controller')){
    class Wpvc_Rest_Register_Controller{

      public function __construct(){
          add_action( 'rest_api_init', array($this,'wpvc_rest_api_register'));
      }
      
      public function wpvc_rest_api_register(){
        //Get needed things for all settings on setting page
        /****************************** Settings Rest ****************************/
        register_rest_route('wpvc-voting/v1', '/wpvcsettingfetch', [
          'methods'  => 'GET',
          'callback' => array('Wpvc_Rest_Actions_Controller','wpvc_callback_plugin_settings_page_data'),		
          'show_in_index' => FALSE
        ]);
        
        register_rest_route('wpvc-voting/v1', '/wpvcupdatesetting', [
          'methods'  => 'POST',
          'callback' => array('Wpvc_Rest_Actions_Controller','wpvc_callback_save_settings'),		
          'show_in_index' => FALSE
        ]);

        register_rest_route('wpvc-voting/v1', '/wpvcvotinglogsfetch', [
          'methods'  => 'GET',
          'callback' => array('Wpvc_Rest_Actions_Controller','wpvc_callback_plugin_voting_logs'),	
          'show_in_index' => FALSE	
        ]);

        register_rest_route('wpvc-voting/v1', '/wpvcvotingdelete', [
          'methods'  => 'POST',
          'callback' => array('Wpvc_Rest_Actions_Controller','wpvc_callback_delete_voting_logs'),		
          'show_in_index' => FALSE
        ]);

        register_rest_route('wpvc-voting/v1', '/wpvcvotingmultipledelete', [
          'methods'  => 'POST',
          'callback' => array('Wpvc_Rest_Actions_Controller','wpvc_callback_delete_multiple_voting_logs'),		
          'show_in_index' => FALSE
        ]);

        register_rest_route('wpvc-voting/v1', '/wpvcvotealldelete', [
          'methods'  => 'POST',
          'callback' => array('Wpvc_Rest_Actions_Controller','wpvc_callback_delete_all_vote_logs'),	
          'show_in_index' => FALSE	
        ]);

        register_rest_route('wpvc-voting/v1', '/wpvcmigratecontestants', [
          'methods'  => 'POST',
          'callback' => array('Wpvc_Rest_Actions_Controller','wpvc_callback_migrate_all_data'),		
          'show_in_index' => FALSE
        ]);    

        
        /****************************** Category Rest ****************************/
        register_rest_route('wpvc-voting/v1', '/wpvccategoryfetch', [
          'methods'  => 'GET',
          'callback' => array('Wpvc_Rest_Actions_Controller','wpvc_callback_plugin_category_data'),		
          'show_in_index' => FALSE
        ]);

        register_rest_route('wpvc-voting/v1', '/wpvccategoryupdate', [
          'methods'  => 'POST',
          'callback' => array('Wpvc_Rest_Actions_Controller','wpvc_callback_plugin_category_update'),	
          'show_in_index' => FALSE	
        ]);

        register_rest_route('wpvc-voting/v1', '/wpvccategorycolorlayout', [
          'methods'  => 'POST',
          'callback' => array('Wpvc_Rest_Actions_Controller','wpvc_callback_plugin_category_color_layout'),		
          'show_in_index' => FALSE
        ]);

        register_rest_route('wpvc-voting/v1', '/wpvccategorydelete', [
          'methods'  => 'POST',
          'callback' => array('Wpvc_Rest_Actions_Controller','wpvc_callback_plugin_category_delete'),	
          'show_in_index' => FALSE	
        ]);        

        register_rest_route('wpvc-voting/v1', '/wpvccategorydesign', [
          'methods'  => 'POST',
          'callback' => array('Wpvc_Rest_Actions_Controller','wpvc_callback_plugin_category_layout_delete'),	
          'show_in_index' => FALSE	
        ]); 
        
        
        /****************************** Custom Fields ****************************/
        register_rest_route('wpvc-voting/v1', '/wpvccustomfieldsfetch', [
          'methods'  => 'GET',
          'callback' => array('Wpvc_Rest_Actions_Controller','wpvc_callback_plugin_custom_field'),		
          'show_in_index' => FALSE
        ]);     
        
        register_rest_route('wpvc-voting/v1', '/wpvcupdatecustom', [
          'methods'  => 'POST',
          'callback' => array('Wpvc_Rest_Actions_Controller','wpvc_callback_save_customfield'),	
          'show_in_index' => FALSE	
        ]);

        register_rest_route('wpvc-voting/v1', '/wpvcupdatecustomfield', [
          'methods'  => 'POST',
          'callback' => array('Wpvc_Rest_Actions_Controller','wpvc_callback_update_customfield'),		
          'show_in_index' => FALSE
        ]);

        register_rest_route('wpvc-voting/v1', '/wpvcdeletecustom', [
          'methods'  => 'POST',
          'callback' => array('Wpvc_Rest_Actions_Controller','wpvc_callback_delete_customfield'),	
          'show_in_index' => FALSE	
        ]);

        register_rest_route('wpvc-voting/v1', '/wpvcassigncustom', [
          'methods'  => 'POST',
          'callback' => array('Wpvc_Rest_Actions_Controller','wpvc_callback_assign_custom'),
          'show_in_index' => FALSE		
        ]);

        register_rest_route('wpvc-voting/v1', '/wpvcgetassigncustom', [
          'methods'  => 'POST',
          'callback' => array('Wpvc_Rest_Actions_Controller','wpvc_callback_get_assign_custom'),	
          'show_in_index' => FALSE	
        ]);

         /****************************** Custom Reg Fields ****************************/
         register_rest_route('wpvc-voting/v1', '/wpvcregcustomfieldsfetch', [
          'methods'  => 'GET',
          'callback' => array('Wpvc_Rest_Actions_Controller','wpvc_callback_plugin_reg_custom_field'),	
          'show_in_index' => FALSE	
        ]);     
        
        register_rest_route('wpvc-voting/v1', '/wpvcregupdatecustom', [
          'methods'  => 'POST',
          'callback' => array('Wpvc_Rest_Actions_Controller','wpvc_callback_save_reg_customfield'),	
          'show_in_index' => FALSE	
        ]);

        register_rest_route('wpvc-voting/v1', '/wpvcregupdatecustomfield', [
          'methods'  => 'POST',
          'callback' => array('Wpvc_Rest_Actions_Controller','wpvc_callback_update_reg_customfield'),	
          'show_in_index' => FALSE	
        ]);

        register_rest_route('wpvc-voting/v1', '/wpvcregdeletecustom', [
          'methods'  => 'POST',
          'callback' => array('Wpvc_Rest_Actions_Controller','wpvc_callback_delete_reg_customfield'),		
          'show_in_index' => FALSE
        ]);

        register_rest_route('wpvc-voting/v1', '/wpvcregassigncustom', [
          'methods'  => 'POST',
          'callback' => array('Wpvc_Rest_Actions_Controller','wpvc_callback_reg_assign_custom'),	
          'show_in_index' => FALSE	
        ]);

        register_rest_route('wpvc-voting/v1', '/wpvcgetregassigncustom', [
          'methods'  => 'POST',
          'callback' => array('Wpvc_Rest_Actions_Controller','wpvc_callback_get_reg_assign_custom'),	
          'show_in_index' => FALSE	
        ]);

        register_rest_route('wpvc-voting/v1', '/wpvcinsertcustomvalues', [
          'methods'  => 'POST',
          'callback' => array('Wpvc_Rest_Actions_Controller','wpvc_callback_insert_custom_values'),		
          'show_in_index' => FALSE
        ]);
        
        register_rest_route('wpvc-voting/v1', '/wpvcgettranslations', [
          'methods'  => 'GET',
          'callback' => array('Wpvc_Rest_Actions_Controller','wpvc_callback_plugin_translations_data'),	
          'show_in_index' => FALSE	
        ]); 
        
        register_rest_route('wpvc-voting/v1', '/wpvccareatetranslations', [
          'methods'  => 'POST',
          'callback' => array('Wpvc_Rest_Actions_Controller','wpvc_callback_create_translations_file'),	
          'show_in_index' => FALSE	
        ]); 

        register_rest_route('wpvc-voting/v1', '/wpvcsavetranslations', [
          'methods'  => 'POST',
          'callback' => array('Wpvc_Rest_Actions_Controller','wpvc_callback_save_translations_file'),	
          'show_in_index' => FALSE	
        ]); 

        register_rest_route('wpvc-voting/v1', '/wpvcgetsitetranslations', [
          'methods'  => 'GET',
          'callback' => array('Wpvc_Rest_Actions_Controller','wpvc_callback_site_translations_data'),	
          'show_in_index' => FALSE	
        ]); 
        
      }				
    }
}else
die("<h2>".__('Failed to load Voting Rest Actions Controller','voting-contest')."</h2>");


return new Wpvc_Rest_Register_Controller();
