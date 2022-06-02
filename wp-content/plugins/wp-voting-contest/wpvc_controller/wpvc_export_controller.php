<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if(!class_exists('Wpvc_Export_Controller')){
	class Wpvc_Export_Controller{

		public function __construct(){
      add_action('plugins_loaded',array($this,'wpvc_vote_export_contestants'));
      add_action('plugins_loaded',array($this,'wpvc_vote_import_sample'));
      add_action('plugins_loaded',array($this,'wpvc_voting_log_contestants'));
		}

    public function wpvc_vote_import_sample(){
      global $pagenow;
			if ($pagenow=='admin.php' && isset($_GET['votes_import_sample'])) {
          $term_id = $_GET['votes_import_sample'];
          $imgcontest = get_term_meta($term_id,'imgcontest',true);
          require(WPVC_CONTROLLER_XL_PATH.'PHPExcel.php');
          $objPHPExcel = new PHPExcel();
          $objPHPExcel->setActiveSheetIndex(0);
          //User Defined Field Name
          $rowCount = 1;
          $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount,'Contestant Title');
          $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount,'Status');
          $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount,'Votes');
          
          if($imgcontest=='photo'){
            $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount,'Featured Image');
          }

          $rowCount = $system_count = 2;
          $objPHPExcel->getActiveSheet()->SetCellValue('A'.$system_count,'contest_title');
          $objPHPExcel->getActiveSheet()->SetCellValue('B'.$system_count,'contest_status');
          $objPHPExcel->getActiveSheet()->SetCellValue('C'.$system_count,'contest_votes');
          if($imgcontest=='photo'){
            $objPHPExcel->getActiveSheet()->SetCellValue('D'.$system_count,'featured_image_url');
            $string_coulmn='E';
          }else{
            $string_coulmn='D';
          }
          $rowCount = 1;
          $category_options = get_term_meta($term_id,'contest_category_assign_custom',true);
          $custom_fields = maybe_unserialize($category_options);
          if(empty($custom_fields)){
            $imgcontest = get_term_meta($term_id,'imgcontest',true);
            $musicfileenable = get_term_meta($term_id,'musicfileenable',true);
            $getcustom = Wpvc_Shortcode_Model::wpvc_custom_fields_by_contest($imgcontest,$musicfileenable);
            $custom_fields = maybe_unserialize($getcustom);
          }
          //For Exporting contestants
          if(!empty($custom_fields)){
            foreach($custom_fields as $custom_field){
              //Restrict contestant-title && contestant-image already having in the loop
              if($custom_field['system_name'] != 'contestant-title' && $custom_field['system_name'] != 'contestant-image'){
                //Check the contestant-ow_music_url for the Music
                if($custom_field['system_name'] == 'contestant-ow_music_url' || $custom_field['system_name'] == 'contestant-ow_music_url_link'){
                  if($imgcontest=='music' || $term_id == 0){
                    //User Defined Field Name
                    $objPHPExcel->getActiveSheet()->SetCellValue($string_coulmn.$rowCount,$custom_field['system_name']);
                    //System Defined Field Name
                    $objPHPExcel->getActiveSheet()->SetCellValue($string_coulmn.$system_count,$custom_field['system_name']);
                    $string_coulmn++;
                  }
                }//Check the contestant-ow_video_url for the Video
                else if($custom_field['system_name'] == 'contestant-ow_video_url'){
                  if($imgcontest=='video' || $term_id == 0){
                    //User Defined Field Name
                    $objPHPExcel->getActiveSheet()->SetCellValue($string_coulmn.$rowCount,$custom_field['system_name']);
                    //System Defined Field Name
                    $objPHPExcel->getActiveSheet()->SetCellValue($string_coulmn.$system_count,$custom_field['system_name']);
                    $string_coulmn++;
                  }
                }
                else{
                  //User Defined Field Name
                  $objPHPExcel->getActiveSheet()->SetCellValue($string_coulmn.$rowCount,$custom_field['system_name']);
                  //System Defined Field Name
                  $objPHPExcel->getActiveSheet()->SetCellValue($string_coulmn.$system_count,$custom_field['system_name']);
                  $string_coulmn++;
                }
              }
            }
          }

          $filename = "contest_".date('d-m-Y-H-i-s').'.csv';
          header('Content-Description: File Transfer');
          header('Content-Type: application/force-download');
          header('Content-Disposition: attachment; filename="'.$filename.'"');
          header('Cache-Control: max-age=0');
          // If you're serving to IE 9, then the following may be needed
          header('Cache-Control: max-age=1');

          // If you're serving to IE over SSL, then the following may be needed
          header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
          header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
          header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
          header ('Pragma: public'); // HTTP/1.0

          $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
          $objWriter->save('php://output');
          exit;
      }
    }

    public function wpvc_vote_export_contestants()
		{
        global $pagenow,$wpdb;
        if (isset($_REQUEST['export_contest_submit'])) {
              $term_id = $_POST['vote_contest_term'];
              $get_type = $_POST['export'];

              require(WPVC_CONTROLLER_XL_PATH.'PHPExcel.php');
              $objPHPExcel = new PHPExcel();
              $objPHPExcel->setActiveSheetIndex(0);
              $terms ='';
              if($term_id == 0){
                $terms = $_POST['ow_all_terms'];
                $terms = explode(',',$terms);
              }
  
              $imgcontest = get_term_meta($term_id,'imgcontest',true);
  
              //User Defined Field Name
              $rowCount = 1;
              $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount,'Contestant Title');
              $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount,'Status');
              $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount,'Contest Category');
              $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount,'Votes');
              $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount,'Created Date');
              $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount,'Featured Image');
              //User Defined Field Name
              $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount,'Author Email');
              $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount,'Author Name');
                              
  
  
              //System Defined Field Name
              $rowCount = $system_count = 2;
              $objPHPExcel->getActiveSheet()->SetCellValue('A'.$system_count,'contest_title');
              $objPHPExcel->getActiveSheet()->SetCellValue('B'.$system_count,'contest_status');
              $objPHPExcel->getActiveSheet()->SetCellValue('C'.$system_count,'contest_category');
              $objPHPExcel->getActiveSheet()->SetCellValue('D'.$system_count,'contest_votes');
              $objPHPExcel->getActiveSheet()->SetCellValue('E'.$system_count,'contest_date');
              $objPHPExcel->getActiveSheet()->SetCellValue('F'.$system_count,'featured_image_url');
              //User Defined Field Name
              $objPHPExcel->getActiveSheet()->SetCellValue('G'.$system_count,'author_email');
              $objPHPExcel->getActiveSheet()->SetCellValue('H'.$system_count,'author_name');
  
  
              $rowCount = 1;
              //Update Column If COntest is PHOTO
              $string_coulmn= 'I';

              if($term_id != 0){
                  $category_options = get_term_meta($term_id,'contest_category_assign_custom',true);
                  $custom_fields = maybe_unserialize($category_options);
                  if(empty($custom_fields)){
                    $imgcontest = get_term_meta($term_id,'imgcontest',true);
                    $musicfileenable = get_term_meta($term_id,'musicfileenable',true);
                    $getcustom = Wpvc_Shortcode_Model::wpvc_custom_fields_by_contest($imgcontest,$musicfileenable);
                    $custom_fields = maybe_unserialize($getcustom);
                  }
              }else{
                  $custom_fields = Wpvc_Export_Model::wpvc_get_all_term_custom_fields($terms);
              }
              //For Exporting contestants
              if(!empty($custom_fields)){
                foreach($custom_fields as $custom_field){
                  //Restrict contestant-title && contestant-image already having in the loop
                  if($custom_field['system_name'] != 'contestant-title' && $custom_field['system_name'] != 'contestant-image'){
                    //Check the contestant-ow_music_url for the Music
                    if($custom_field['system_name'] == 'contestant-ow_music_url' || $custom_field['system_name'] == 'contestant-ow_music_url_link'){
                      if($imgcontest=='music' || $term_id == 0){
                        //User Defined Field Name
                        $objPHPExcel->getActiveSheet()->SetCellValue($string_coulmn.$rowCount,$custom_field['system_name']);
                        //System Defined Field Name
                        $objPHPExcel->getActiveSheet()->SetCellValue($string_coulmn.$system_count,$custom_field['system_name']);
                        $string_coulmn++;
                      }
                    }//Check the contestant-ow_video_url for the Video
                    else if($custom_field['system_name'] == 'contestant-ow_video_url'){
                      if($imgcontest=='video' || $term_id == 0){
                        //User Defined Field Name
                        $objPHPExcel->getActiveSheet()->SetCellValue($string_coulmn.$rowCount,$custom_field['system_name']);
                        //System Defined Field Name
                        $objPHPExcel->getActiveSheet()->SetCellValue($string_coulmn.$system_count,$custom_field['system_name']);
                        $string_coulmn++;
                      }
                    }
                    else{
                      //User Defined Field Name
                      $objPHPExcel->getActiveSheet()->SetCellValue($string_coulmn.$rowCount,$custom_field['system_name']);
                      //System Defined Field Name
                      $objPHPExcel->getActiveSheet()->SetCellValue($string_coulmn.$system_count,$custom_field['system_name']);
                      $string_coulmn++;
                    }
                  }
                }
              }

              //System Defined Field Name
              //Decrementing the Column
              $string_coulmn = chr(ord($string_coulmn) - 1);
              //Call The Function for looping
              $objPHPExcel = Wpvc_Export_Model::wpvc_render_export($objPHPExcel,$custom_fields,$terms,$term_id);
              switch ($get_type) {
  
                case 'excel_xlsx':
                  $filename = "contest_".date('d-m-Y-H-i-s').'.xlsx';
                  // Redirect output to a client’s web browser (Excel2007)
                  header('Content-Type: application/vnd.oasis.opendocument.spreadsheet');
                  header('Content-Disposition: attachment;filename="'.$filename.'"');
                  header('Cache-Control: max-age=0');
                  // If you're serving to IE 9, then the following may be needed
                  header('Cache-Control: max-age=1');
      
                  // If you're serving to IE over SSL, then the following may be needed
                  header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
                  header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
                  header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
                  header ('Pragma: public'); // HTTP/1.0
      
                  $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'OpenDocument');
                  $objWriter->save('php://output');
                break;
      
                case 'excel_xls':
                  $filename = "contest_".date('d-m-Y-H-i-s').'.xls';
                  // Redirect output to a client’s web browser (Excel5)
                  header('Content-Type: application/vnd.oasis.opendocument.spreadsheet');
                  header('Content-Disposition: attachment;filename="'.$filename.'"');
                  header('Cache-Control: max-age=0');
                  // If you're serving to IE 9, then the following may be needed
                  header('Cache-Control: max-age=1');
      
                  // If you're serving to IE over SSL, then the following may be needed
                  header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
                  header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
                  header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
                  header ('Pragma: public'); // HTTP/1.0
      
                  $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'OpenDocument');
                  $objWriter->save('php://output');
                break;
      
                case 'excel_ods':
                  $filename = "contest_".date('d-m-Y-H-i-s').'.ods';
                  // Redirect output to a client’s web browser (OpenDocument)
                  header('Content-Type: application/vnd.oasis.opendocument.spreadsheet');
                  header('Content-Disposition: attachment;filename="'.$filename.'"');
                  header('Cache-Control: max-age=0');
                  // If you're serving to IE 9, then the following may be needed
                  header('Cache-Control: max-age=1');
      
                  // If you're serving to IE over SSL, then the following may be needed
                  header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
                  header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
                  header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
                  header ('Pragma: public'); // HTTP/1.0
      
                  $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'OpenDocument');
                  $objWriter->save('php://output');
                break;
      
                case 'CSV':
                  $filename = "contest_".date('d-m-Y-H-i-s').'.csv';
                  header('Content-Description: File Transfer');
                  header('Content-Type: application/force-download');
                  header('Content-Disposition: attachment; filename="'.$filename.'"');
                  header('Cache-Control: max-age=0');
                  // If you're serving to IE 9, then the following may be needed
                  header('Cache-Control: max-age=1');
      
                  // If you're serving to IE over SSL, then the following may be needed
                  header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
                  header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
                  header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
                  header ('Pragma: public'); // HTTP/1.0
      
                  $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
                  $objWriter->save('php://output');
                break;
      
                case 'html':
                  $filename = "contest_".date('d-m-Y-H-i-s').'.html';
                  header('Content-Description: File Transfer');
                  header('Content-Type: application/force-download');
                  header('Content-Disposition: attachment; filename="'.$filename.'"');
                  header('Cache-Control: max-age=0');
                  // If you're serving to IE 9, then the following may be needed
                  header('Cache-Control: max-age=1');
      
                  // If you're serving to IE over SSL, then the following may be needed
                  header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
                  header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
                  header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
                  header ('Pragma: public'); // HTTP/1.0
                  $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'HTML');
                  $objWriter->save('php://output');
                break;
              }
              exit;
          }
      }

      public function wpvc_voting_log_contestants(){
        global $pagenow,$wpdb;
        if (isset($_REQUEST['export_vote_submit'])) {
          $get_type = $_POST['export'];
          if($get_type!=''){
            require(WPVC_CONTROLLER_XL_PATH.'PHPExcel.php');
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->setActiveSheetIndex(0);
  
            $rowCount = 1;
            $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount,'Contestant Title');
            $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount,'Author');
            $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount,'Voter Name');
            $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount,'Voter');
            $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount,'Voter IP');
            $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount,'Voter Email');
            $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount,'Vote Date');
            $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount,'No. of Votes');

            $voting = Wpvc_Settings_Model::wpvc_votings_get();
            if(!empty($voting)){
                $string_colmn='A';

                foreach($voting as $logs){
                    if($logs['email_always'] !=''){
                        $user = get_user_by('email', $logs['email_always']);
                        $logs['username'] = $user->display_name;
                    }else{
                        $logs['username'] = '';
                    }
                    $rowCount++;
                    $voted_date    = $logs["date"];
                    $objPHPExcel->getActiveSheet()->SetCellValue($string_colmn.$rowCount, $logs["post_title"]);
                    $objPHPExcel->getActiveSheet()->SetCellValue(++$string_colmn.$rowCount, $logs["display_name"]);
                    $objPHPExcel->getActiveSheet()->SetCellValue(++$string_colmn.$rowCount, $logs["username"]);
                    $objPHPExcel->getActiveSheet()->SetCellValue(++$string_colmn.$rowCount, $logs["ip"]);
                    $objPHPExcel->getActiveSheet()->SetCellValue(++$string_colmn.$rowCount, $logs["ip_always"]);
                    $objPHPExcel->getActiveSheet()->SetCellValue(++$string_colmn.$rowCount, $logs["email_always"]);
                    $objPHPExcel->getActiveSheet()->SetCellValue(++$string_colmn.$rowCount, $voted_date);
                    $objPHPExcel->getActiveSheet()->SetCellValue(++$string_colmn.$rowCount, $logs["votes"]);

                    $string_colmn='A';
                    set_time_limit(100);
                }
            }

            switch ($get_type) {

              case 'excel_xlsx':
                $filename = "Voting_logs_".date('d-m-Y-H-i-s').'.xls';
                // Redirect output to a client’s web browser (Excel2007)
                header('Content-Type: application/vnd.oasis.opendocument.spreadsheet');
                header('Content-Disposition: attachment;filename="'.$filename.'"');
                header('Cache-Control: max-age=0');
                // If you're serving to IE 9, then the following may be needed
                header('Cache-Control: max-age=1');
  
  
                set_time_limit ( 3000 );
                $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
                $cacheSettings = array( 'memoryCacheSize' => '512MB');
                PHPExcel_Settings::setCacheStorageMethod($cacheMethod,$cacheSettings);
  
                // If you're serving to IE over SSL, then the following may be needed
                header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
                header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
                header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
                header ('Pragma: public'); // HTTP/1.0
  
                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'OpenDocument');
                $objWriter->save('php://output');
              break;
  
              case 'excel_xls':
                $filename = "Voting_logs_".date('d-m-Y-H-i-s').'.xls';
                // Redirect output to a client’s web browser (Excel5)
                header('Content-Type: application/vnd.oasis.opendocument.spreadsheet');
                header('Content-Disposition: attachment;filename="'.$filename.'"');
                header('Cache-Control: max-age=0');
                // If you're serving to IE 9, then the following may be needed
                header('Cache-Control: max-age=1');
  
                set_time_limit ( 3000 );
                $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
                $cacheSettings = array( 'memoryCacheSize' => '512MB');
                PHPExcel_Settings::setCacheStorageMethod($cacheMethod,$cacheSettings);
  
  
                // If you're serving to IE over SSL, then the following may be needed
                header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
                header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
                header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
                header ('Pragma: public'); // HTTP/1.0
  
                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'OpenDocument');
                $objWriter->save('php://output');
              break;
  
              case 'excel_ods':
                $filename = "Voting_logs_".date('d-m-Y-H-i-s').'.ods';
                // Redirect output to a client’s web browser (OpenDocument)
                header('Content-Type: application/vnd.oasis.opendocument.spreadsheet');
                header('Content-Disposition: attachment;filename="'.$filename.'"');
                header('Cache-Control: max-age=0');
                // If you're serving to IE 9, then the following may be needed
                header('Cache-Control: max-age=1');
  
                set_time_limit ( 3000 );
                $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
                $cacheSettings = array( 'memoryCacheSize' => '512MB');
                PHPExcel_Settings::setCacheStorageMethod($cacheMethod,$cacheSettings);
  
                // If you're serving to IE over SSL, then the following may be needed
                header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
                header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
                header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
                header ('Pragma: public'); // HTTP/1.0
  
                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'OpenDocument');
                $objWriter->save('php://output');
              break;
  
              case 'CSV':
                $filename = "Voting_logs_".date('d-m-Y-H-i-s').'.csv';
                header('Content-Description: File Transfer');
                header('Content-Type: application/force-download');
                header('Content-Disposition: attachment; filename="'.$filename.'"');
                header('Cache-Control: max-age=0');
                // If you're serving to IE 9, then the following may be needed
                header('Cache-Control: max-age=1');
  
                set_time_limit ( 3000 );
                $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
                $cacheSettings = array( 'memoryCacheSize' => '512MB');
                PHPExcel_Settings::setCacheStorageMethod($cacheMethod,$cacheSettings);
  
                // If you're serving to IE over SSL, then the following may be needed
                header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
                header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
                header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
                header ('Pragma: public'); // HTTP/1.0
  
                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
                $objWriter->save('php://output');
              break;
  
              case 'html':
                $filename = "Voting_logs_".date('d-m-Y-H-i-s').'.html';
                header('Content-Description: File Transfer');
                header('Content-Type: application/force-download');
                header('Content-Disposition: attachment; filename="'.$filename.'"');
                header('Cache-Control: max-age=0');
                // If you're serving to IE 9, then the following may be needed
                header('Cache-Control: max-age=1');
  
                set_time_limit ( 3000 );
                $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
                $cacheSettings = array( 'memoryCacheSize' => '512MB');
                PHPExcel_Settings::setCacheStorageMethod($cacheMethod,$cacheSettings);
  
                // If you're serving to IE over SSL, then the following may be needed
                header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
                header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
                header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
                header ('Pragma: public'); // HTTP/1.0
                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'HTML');
                $objWriter->save('php://output');
              break;
            }
            exit;

          }
        }
      }


    }//Class 
}
else
die("<h2>".__('Failed to load the Voting Export Controller','voting-contest')."</h2>");

return new Wpvc_Export_Controller();
