<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if(!class_exists('Wpvc_Common_Settings_Controller')){
    class Wpvc_Common_Settings_Controller{
      
      //Overview page      
      public function wpvc_voting_overview(){
        require_once(WPVC_VIEW_PATH.'wpvc_settings_view.php');
        wpvc_overview_view();
      }	
    
      //Settings page
      public static function wpvc_voting_setting_common(){
        require_once(WPVC_VIEW_PATH.'wpvc_settings_view.php');
        wpvc_settings_view();
      }	 

      //Category page
      public static function wpvc_voting_category_list(){
        require_once(WPVC_VIEW_PATH.'wpvc_settings_view.php');
        wpvc_category_view();
      }

      //Clear Voting Entries
      public static function wpvc_voting_clear_voting_entries(){
        require_once(WPVC_VIEW_PATH.'wpvc_settings_view.php');
        wpvc_voting_clear_voting_entry();
      }

      //License
      public static function wpvc_voting_software_license_page(){
        require_once(WPVC_VIEW_PATH.'wpvc_settings_view.php');
        $license = get_option('wp_voting_software_license_key');
			  $status = get_option('wp_voting_software_license_status');
        wpvc_voting_license($license,$status);
      }
      
      //Custom Fields
      public static function wpvc_voting_custom_fields(){
        require_once(WPVC_VIEW_PATH.'wpvc_settings_view.php');
        wpvc_custom_fields_view();
      }

      public static function wpvc_voting_registration_custom_fields(){
        require_once(WPVC_VIEW_PATH.'wpvc_settings_view.php');
        wpvc_reg_custom_fields_view();
      }
      
      //Contestants other links
      public static function wpvc_voting_move_contestants(){
        global $wpdb;
        require_once(WPVC_VIEW_PATH.'wpvc_settings_view.php');
        
        if ( current_user_can('edit_posts') ) {
          if (isset($_REQUEST['move_contest_submit'])) {
            $posts = $_POST['selected_post'];
            $all_post = $_POST['select_all_post'];
            $old_cat = absint($_POST['selected_category']);
				    $new_cat = ($_POST['movecategory'] == -1) ? -1 : absint($_POST['movecategory']);
            
            $cls = '';
            $msg = '';
            if($new_cat != 0){

              if($all_post=='all'){
                $args = array(
                  'fields' => 'ids',
                  'posts_per_page' => -1,
                  'post_type' => WPVC_VOTES_TYPE,
                  'tax_query' => array(
                    array(
                      'taxonomy' => WPVC_VOTES_TAXONOMY,
                      'field' => 'id',
                      'terms' => $old_cat
                    )
                  ),
                  'post_status' => 'publish'
                );
                $posts = get_posts($args);
              }

              if(count($posts)){
                foreach ($posts as $post) {
                  $current_cats = wp_get_object_terms($post, WPVC_VOTES_TAXONOMY,array('fields' => 'ids'));
                  $current_cats = array_diff($current_cats, array($old_cat));
                  if ($new_cat != -1) {
                    $current_cats[] = $new_cat;
                  }
          
                  if (count($current_cats) <= 0) {
                    $cls = 'error';
                    $msg = 'Invalid Category';
                  }
                  else {
                    $current_cats = array_values($current_cats);
                    $term = get_term($new_cat, WPVC_VOTES_TAXONOMY);
                    wp_set_post_terms( $post, $current_cats, WPVC_VOTES_TAXONOMY);
                    $cls = 'updated';
                    $msg = count($posts).' '.__('Contestants Successfully Moved','voting-contest');
          
                    $wpdb->update(
                      WPVC_VOTES_TBL,
                      array(
                        'termid' => $current_cats[0]
                      ),
                      array( 'post_id' => $post ),
                      array(
                        '%d'
                      ),
                      array( '%d' )
                    );
                  }
                }
              }
            }else{
              $cls = 'error';
              $msg = 'Invalid Category';
            }
          }
        }
        wpvc_voting_move_contestants_view($cls,$msg);
      }

      public static function wpvc_voting_export_contestants(){
        require_once(WPVC_VIEW_PATH.'wpvc_settings_view.php');
        wpvc_voting_export_contestants_view();
      }

      public static function wpvc_voting_import_contestants(){
        global $wpdb;
        require_once(WPVC_VIEW_PATH.'wpvc_settings_view.php');
       
        if (isset($_POST['import_contest_submit'])){
          set_time_limit(0);
          require(WPVC_CONTROLLER_XL_PATH.'wpvc_spreadsheetreader.php');
          $inserted = array();
          $term_id = $_POST['contest_csv_term'];

          if (isset($term_id) && $term_id > 0) {

            $tempFile = $_FILES['contest_csv_file']['tmp_name'];
            $targetPath = WPVC_ASSETS_UPLOAD_PATH;
            $sourceCSV = $_FILES['contest_csv_file']['name'];
            $get_source_file_ext = $ext = pathinfo($sourceCSV, PATHINFO_EXTENSION);
            $accepted_formats = array('ods','ODS','xlsx','XLSX','csv','CSV','xls','XLS');
            $headers_for_import = '';
            
            if (in_array($get_source_file_ext, $accepted_formats)) {
              $targetFile = str_replace('//', '/', $targetPath) . $_FILES['contest_csv_file']['name'];
						  if ($_FILES['contest_csv_file']['error'] == 0 && move_uploaded_file($tempFile, $targetFile)) {
                if($get_source_file_ext=='xls' || $get_source_file_ext=='XLS')
                  $keyd = 1;
                else
                  $keyd = 0;

                $Reader = new Wpvc_SpreadsheetReader($targetFile);
                foreach ($Reader as $key => $Row){
                  $cur_id = '';
                  if($key == 1){
                    $system_names = $Row;
                    $headers_for_import = $Row;
									  $contest_title_key = array_search ('contest_title', $system_names);
                  }
                  if($key > 1){
                    if (trim($Row[$contest_title_key]) != '') {
                      $cur_id = Wpvc_Export_Model::wpvc_import_contestants($term_id,$Row,$headers_for_import);
                    }
                  }
                  if($cur_id != '')
                    $inserted[] = $cur_id;
                }
                $cls = "updated";
							  $msg = count($inserted) . " Contestants Uploaded";

              }else{
                $cls = "error";
                $msg = __('Error in Uploading','voting-contest');
              }
            }else{
              $cls = "error";
              $msg = __('Invalid File format','voting-contest');
            }
            
          }else{
            $cls = "error";
            $msg = __('Invalid category.','voting-contest');
          }
        }
        wpvc_voting_import_contestants_view($cls,$msg);
      }
      
      public static function wpvc_voting_vote_logs(){
        require_once(WPVC_VIEW_PATH.'wpvc_settings_view.php');
        wpvc_voting_vote_logs_view();
      }
      
      public static function wpvc_voting_migration(){
        require_once(WPVC_VIEW_PATH.'wpvc_settings_view.php');
        wpvc_migration_view();
      }

      //Translations
      public static function wpvc_voting_software_translations_page(){
        require_once(WPVC_VIEW_PATH.'wpvc_settings_view.php');
        wpvc_voting_translations();
      }
    }
}else
die("<h2>".__('Failed to load Voting Common Settings Controller','voting-contest')."</h2>");


return new Wpvc_Common_Settings_Controller();
