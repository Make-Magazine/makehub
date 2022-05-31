<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if(!class_exists('Wpvc_Rest_Actions_Controller')){
    class Wpvc_Rest_Actions_Controller{

        public function __construct(){
            if($_SERVER['HTTP_AUTHORIZE_WPVC_REQUEST']!='xmlhttprequest'){
                $create_random_hash="wpvcvotingcontestadmin".rand();
                $hash = wp_hash($create_random_hash);
                unset($_COOKIE['wpvc_voting_authorize']);
                setcookie('wpvc_voting_authorize', $hash, (time()+86400), "/");
            }
        }

        public static function wpvc_callback_plugin_settings_page_data(){
            if($_SERVER['HTTP_AUTHORIZE_WPVC']!='' && strtolower($_SERVER['HTTP_AUTHORIZE_WPVC']) == $_COOKIE['wpvc_voting_authorize'] && $_SERVER['HTTP_AUTHORIZE_WPVC_REQUEST']=='xmlhttprequest')
            { 
                $response = Wpvc_Settings_Model::wpvc_settings_page_json();
                return new WP_REST_Response($response,200);
            }else{
                die(json_encode(array('no_cheating' => "You have no permission to access Voting contest")));
            } 
        }

        public static function wpvc_callback_save_settings($request_data){
            if($_SERVER['HTTP_AUTHORIZE_WPVC']!='' && strtolower($_SERVER['HTTP_AUTHORIZE_WPVC']) == $_COOKIE['wpvc_voting_authorize'] && $_SERVER['HTTP_AUTHORIZE_WPVC_REQUEST']=='xmlhttprequest')
            { 
                global $wpdb;
                $result = $request_data->get_params();	
                update_option(WPVC_VOTES_SETTINGS,$result);
                return new WP_REST_Response($result,200);
            }else{
                die(json_encode(array('no_cheating' => "You have no permission to access Voting contest")));
            }   
        }

        public static function wpvc_callback_plugin_voting_logs($request_data){  
            if($_SERVER['HTTP_AUTHORIZE_WPVC']!='' && strtolower($_SERVER['HTTP_AUTHORIZE_WPVC']) == $_COOKIE['wpvc_voting_authorize'] && $_SERVER['HTTP_AUTHORIZE_WPVC_REQUEST']=='xmlhttprequest')
            { 
                $param = $request_data->get_params();
                $paged = $param['paged'];
                $total_count = Wpvc_Settings_Model::wpvc_get_total_voting_count();
                $voting = Wpvc_Settings_Model::wpvc_votings_get_paged($paged);    
  
                $vote_return = array(); 
                if(!empty($voting)){
                    foreach($voting as $key=>$vote_log){
                        if($vote_log['email_always'] !=''){
                            $user = get_user_by('email', $vote_log['email_always']);
                            $vote_log['username'] = $user->display_name;
                        }else{
                            $vote_log['username'] = '';
                        }
                        $vote_return[$key] = $vote_log;
                    }
                }       
                $return_val = array('voting_logs' => $vote_return,'total_count'=>$total_count,'paged'=>$paged);               
                return new WP_REST_Response($return_val,200);         
            }else{
                die(json_encode(array('no_cheating' => "You have no permission to access Voting contest")));
            }   
        }

        public static function wpvc_callback_delete_voting_logs($request_data){
            if($_SERVER['HTTP_AUTHORIZE_WPVC']!='' && strtolower($_SERVER['HTTP_AUTHORIZE_WPVC']) == $_COOKIE['wpvc_voting_authorize'] && $_SERVER['HTTP_AUTHORIZE_WPVC_REQUEST']=='xmlhttprequest')
            { 
                $param = $request_data->get_params();	
                $deleted = Wpvc_Settings_Model::wpvc_delete_contestant_voting($param['id']);
                $voting = Wpvc_Settings_Model::wpvc_votings_get();   
                $vote_return = array(); 
                if(!empty($voting)){
                    foreach($voting as $key=>$vote_log){
                        if($vote_log['email_always'] !=''){
                            $user = get_user_by('email', $vote_log['email_always']);
                            $vote_log['username'] = $user->display_name;
                        }else{
                            $vote_log['username'] = '';
                        }
                        $vote_return[$key] = $vote_log;
                    }
                }              
                $return_val = array('voting_logs' => $vote_return);               
                return new WP_REST_Response($return_val,200);  
            }else{
                die(json_encode(array('no_cheating' => "You have no permission to access Voting contest")));
            }
        }

        public static function wpvc_callback_delete_multiple_voting_logs($request_data){
            if($_SERVER['HTTP_AUTHORIZE_WPVC']!='' && strtolower($_SERVER['HTTP_AUTHORIZE_WPVC']) == $_COOKIE['wpvc_voting_authorize'] && $_SERVER['HTTP_AUTHORIZE_WPVC_REQUEST']=='xmlhttprequest')
            { 
                $param = $request_data->get_params();	
                $deleted = Wpvc_Settings_Model::wpvc_multiple_delete_contestant_voting($param['id']);
                $voting = Wpvc_Settings_Model::wpvc_votings_get();             
                $vote_return = array(); 
                if(!empty($voting)){
                    foreach($voting as $key=>$vote_log){
                        if($vote_log['email_always'] !=''){
                            $user = get_user_by('email', $vote_log['email_always']);
                            $vote_log['username'] = $user->display_name;
                        }else{
                            $vote_log['username'] = '';
                        }
                        $vote_return[$key] = $vote_log;
                    }
                }              
                $return_val = array('voting_logs' => $vote_return);                 
                return new WP_REST_Response($return_val,200);  
            }else{
                die(json_encode(array('no_cheating' => "You have no permission to access Voting contest")));
            }
        }
        

        public static function wpvc_callback_delete_all_vote_logs($request_data){
            if($_SERVER['HTTP_AUTHORIZE_WPVC']!='' && strtolower($_SERVER['HTTP_AUTHORIZE_WPVC']) == $_COOKIE['wpvc_voting_authorize'] && $_SERVER['HTTP_AUTHORIZE_WPVC_REQUEST']=='xmlhttprequest')
            { 
                $param = $request_data->get_params();	
                $deleted = Wpvc_Settings_Model::wpvc_vote_clear_category($param['id']);
                $return_val = array('clrvotelogs' => $deleted);          
                return new WP_REST_Response($return_val,200);  
            }else{
                die(json_encode(array('no_cheating' => "You have no permission to access Voting contest")));
            }
        }

        /***************** Category ***************/
        
        public static function wpvc_callback_plugin_category_data($val=null){ 
            if($_SERVER['HTTP_AUTHORIZE_WPVC']!='' && strtolower($_SERVER['HTTP_AUTHORIZE_WPVC']) == $_COOKIE['wpvc_voting_authorize'] && $_SERVER['HTTP_AUTHORIZE_WPVC_REQUEST']=='xmlhttprequest')
            {    
                $new_taxonomy = Wpvc_Common_State_Controller::wpvc_new_taxonomy_state();  
                $terms = Wpvc_Settings_Model::wpvc_category_get_allTerms($new_taxonomy);             
                $return_val = array('taxonomy' => $terms,'new_taxonomy' => $new_taxonomy, 'currentTerm' => -1,'currentLayoutTerm' => -1,'error_slug'=>$val);               
                return new WP_REST_Response($return_val,200);        
            }else{
                die(json_encode(array('no_cheating' => "You have no permission to access Voting contest")));
            }
        }

        public static function wpvc_callback_plugin_category_update($request_data){        
            if($_SERVER['HTTP_AUTHORIZE_WPVC']!='' && strtolower($_SERVER['HTTP_AUTHORIZE_WPVC']) == $_COOKIE['wpvc_voting_authorize'] && $_SERVER['HTTP_AUTHORIZE_WPVC_REQUEST']=='xmlhttprequest')
            {     
                $result = $request_data->get_params();   
                if(isset($result['term_id'])){ //Edit 
                    $check_result = Wpvc_Settings_Model::wpvc_category_edit($result,$result['term_id']);
                }
                else{   //Add 
                    $check_result = Wpvc_Settings_Model::wpvc_category_insert($result);
                }
                if($check_result !== 0){
                    $return_val = Wpvc_Rest_Actions_Controller::wpvc_callback_plugin_category_data($check_result);
                    return $return_val;     
                } 
            }else{
                die(json_encode(array('no_cheating' => "You have no permission to access Voting contest")));
            }   
        }

        public static function wpvc_callback_plugin_category_color_layout($request_data){
            if($_SERVER['HTTP_AUTHORIZE_WPVC']!='' && strtolower($_SERVER['HTTP_AUTHORIZE_WPVC']) == $_COOKIE['wpvc_voting_authorize'] && $_SERVER['HTTP_AUTHORIZE_WPVC_REQUEST']=='xmlhttprequest')
            { 
                $result = $request_data->get_params(); 
                $term_id = $result['term_id'];
                $type = $result['type'];
                update_term_meta($term_id, $type, $result[$type]);
                $return_val = Wpvc_Rest_Actions_Controller::wpvc_callback_plugin_category_data();
                return $return_val;     
            }else{
                die(json_encode(array('no_cheating' => "You have no permission to access Voting contest")));
            }   
        }

        public static function wpvc_callback_plugin_category_delete($request_data){
            if($_SERVER['HTTP_AUTHORIZE_WPVC']!='' && strtolower($_SERVER['HTTP_AUTHORIZE_WPVC']) == $_COOKIE['wpvc_voting_authorize'] && $_SERVER['HTTP_AUTHORIZE_WPVC_REQUEST']=='xmlhttprequest')
            { 
                $result = $request_data->get_params(); 
                $term_ids = $result;
                foreach($term_ids as $term){
                    wp_delete_term( $term, WPVC_VOTES_TAXONOMY );
                }            
                $return_val = Wpvc_Rest_Actions_Controller::wpvc_callback_plugin_category_data();
                return $return_val;     
            }else{
                die(json_encode(array('no_cheating' => "You have no permission to access Voting contest")));
            }   
        }

        public static function wpvc_callback_plugin_category_layout_delete($request_data){
            if($_SERVER['HTTP_AUTHORIZE_WPVC']!='' && strtolower($_SERVER['HTTP_AUTHORIZE_WPVC']) == $_COOKIE['wpvc_voting_authorize'] && $_SERVER['HTTP_AUTHORIZE_WPVC_REQUEST']=='xmlhttprequest')
            { 
                $result = $request_data->get_params(); 
                $term_ids = $result['terms'];
                $design = $result['design'];
                if($term_ids !='' && $design!=''){
                    delete_term_meta($term_ids,$design);          
                }  
                $return_val = Wpvc_Rest_Actions_Controller::wpvc_callback_plugin_category_data();
                return $return_val;     
            }else{
                die(json_encode(array('no_cheating' => "You have no permission to access Voting contest")));
            }   
        }
        
        public static function wpvc_callback_migrate_all_data($request_data){    
            if($_SERVER['HTTP_AUTHORIZE_WPVC']!='' && strtolower($_SERVER['HTTP_AUTHORIZE_WPVC']) == $_COOKIE['wpvc_voting_authorize'] && $_SERVER['HTTP_AUTHORIZE_WPVC_REQUEST']=='xmlhttprequest')
            {     
                $response = Wpvc_Migration_Model::wpvc_migrate_plugin_to_react();
                return $response; 
            }else{
                die(json_encode(array('no_cheating' => "You have no permission to access Voting contest")));
            }           
        }
        
        
        /*************************Custom Field ********************************/

        public static function wpvc_callback_assign_custom($request_data){
            if($_SERVER['HTTP_AUTHORIZE_WPVC']!='' && strtolower($_SERVER['HTTP_AUTHORIZE_WPVC']) == $_COOKIE['wpvc_voting_authorize'] && $_SERVER['HTTP_AUTHORIZE_WPVC_REQUEST']=='xmlhttprequest')
            { 
                global $wpdb;
                $result = $request_data->get_params();	
                $data = $result['insertData'];
                $update_id = $result['id'];
                update_term_meta($update_id,WPVC_VOTES_TAXONOMY_ASSIGN,$data);
                return new WP_REST_Response($result,200);
            }else{
                die(json_encode(array('no_cheating' => "You have no permission to access Voting contest")));
            }   
        }

        public static function wpvc_callback_get_assign_custom($request_data){
            if($_SERVER['HTTP_AUTHORIZE_WPVC']!='' && strtolower($_SERVER['HTTP_AUTHORIZE_WPVC']) == $_COOKIE['wpvc_voting_authorize'] && $_SERVER['HTTP_AUTHORIZE_WPVC_REQUEST']=='xmlhttprequest')
            { 
                global $wpdb;
                $result = $request_data->get_params();	
                $cat_id = $result['id'];
                $imgcontest = get_term_meta($cat_id,'imgcontest',true);
                $options = get_term_meta($cat_id,WPVC_VOTES_TAXONOMY_ASSIGN,true);
                if(is_array($options)){
                    foreach($options as $key => $opt){
                        $get_original = Wpvc_Settings_Model::wpvc_custom_fields_get_original($opt['id']);
                        if(!empty($get_original)){
                            $options[$key]['question'] = $get_original['question'];
                            $options[$key]['original'] = $get_original;
                        }
                    }
                }
                if($imgcontest == 'video'){
                    $video_extension = get_option('_ow_video_extension');
                    // if($video_extension ==1){
                    //     $options= Wpvc_Settings_Model::wpvc_video_extension_field_change($options);
                    // }
                    if($video_extension != 1){
                        foreach($options as $key => $val){
                            if($val['system_name'] == 'contestant-ow_video_upload_url'){
                                array_splice($options, $key, 1);
                            }
                        }
                    }
                }
                $selected_items= array('selected_items'=>$options);
                return new WP_REST_Response($selected_items,200);
            }else{
                die(json_encode(array('no_cheating' => "You have no permission to access Voting contest")));
            }   
        }

       
        public static function wpvc_callback_plugin_custom_field(){
            if($_SERVER['HTTP_AUTHORIZE_WPVC']!='' && strtolower($_SERVER['HTTP_AUTHORIZE_WPVC']) == $_COOKIE['wpvc_voting_authorize'] && $_SERVER['HTTP_AUTHORIZE_WPVC_REQUEST']=='xmlhttprequest')
            { 
                $custom= Wpvc_Settings_Model::wpvc_custom_fields_json();
                $new_field= Wpvc_Common_State_Controller::wpvc_new_custom_fields_state();
                $terms = get_terms( array(
                    'taxonomy' => WPVC_VOTES_TAXONOMY,
                    'hide_empty' => false,
                ));
                if(!empty($terms)){
                    foreach($terms as $key=>$term){
                        $imgcontest = get_term_meta($term->term_id,'imgcontest',true);
                        $term->category_type = $imgcontest;

                        if($imgcontest=='music'){
                            $musicfileenable = get_term_meta($term->term_id,'musicfileenable',true);
                            $term->music_enable = $musicfileenable;
                        }elseif($imgcontest=='video'){
                            $video_extension = get_option('_ow_video_extension');
                            if($video_extension ==1){
                                $term->music_enable = 'on';
                            }else{
                                $term->music_enable = 'off';
                            }
                        }
                        $array = json_decode(json_encode($term), true);
                        array_merge($array, array("category_type"=>$imgcontest));
                    }
                }
                $return_val = array('custom'=>$custom,'new_customfield'=>$new_field,'insert'=>0,'deleted'=>0,'updated'=>0,'taxonomy'=>$terms);
                return new WP_REST_Response($return_val,200);
            }else{
                die(json_encode(array('no_cheating' => "You have no permission to access Voting contest")));
            }   
        }

        public static function wpvc_callback_update_customfield($request_data){
            if($_SERVER['HTTP_AUTHORIZE_WPVC']!='' && strtolower($_SERVER['HTTP_AUTHORIZE_WPVC']) == $_COOKIE['wpvc_voting_authorize'] && $_SERVER['HTTP_AUTHORIZE_WPVC_REQUEST']=='xmlhttprequest')
            { 
                $param = $request_data->get_params();	
                $data = $param['insertData'];
                $update_id = $param['id'];
                $added = Wpvc_Settings_Model::wpvc_update_contestant_custom_field($update_id,$data);
                $terms = get_terms( array(
                    'taxonomy' => WPVC_VOTES_TAXONOMY,
                    'hide_empty' => false,
                ));
                if(!empty($terms)){
                    foreach($terms as $key=>$term){
                        $imgcontest = get_term_meta($term->term_id,'imgcontest',true);
                        $term->category_type = $imgcontest;
                        $musicfileenable = get_term_meta($term->term_id,'musicfileenable',true);
                        $term->music_enable = $musicfileenable;
                        $array = json_decode(json_encode($term), true);
                        array_merge($array, array("category_type"=>$imgcontest));
                    }
                }
                
                //Get values again
                $custom= Wpvc_Settings_Model::wpvc_custom_fields_json();
                $new_field= Wpvc_Common_State_Controller::wpvc_new_custom_fields_state();
                $return_val = array('custom'=>$custom,'new_customfield'=>$new_field,'insert'=>0,'deleted'=>0,'updated'=>$added,'taxonomy'=>$terms);
                return new WP_REST_Response($return_val,200);
            }else{
                die(json_encode(array('no_cheating' => "You have no permission to access Voting contest")));
            }   
        }


        public static function wpvc_callback_save_customfield($request_data){
            if($_SERVER['HTTP_AUTHORIZE_WPVC']!='' && strtolower($_SERVER['HTTP_AUTHORIZE_WPVC']) == $_COOKIE['wpvc_voting_authorize'] && $_SERVER['HTTP_AUTHORIZE_WPVC_REQUEST']=='xmlhttprequest')
            { 
                $param = $request_data->get_params();	
                $added = Wpvc_Settings_Model::wpvc_insert_contestant_custom_field($param);
                $terms = get_terms( array(
                    'taxonomy' => WPVC_VOTES_TAXONOMY,
                    'hide_empty' => false,
                ));
                
                if(!empty($terms)){
                    foreach($terms as $key=>$term){
                        $imgcontest = get_term_meta($term->term_id,'imgcontest',true);
                        $term->category_type = $imgcontest;
                        $musicfileenable = get_term_meta($term->term_id,'musicfileenable',true);
                        $term->music_enable = $musicfileenable;
                        $array = json_decode(json_encode($term), true);
                        array_merge($array, array("category_type"=>$imgcontest));
                    }
                }

                //Get values again
                $custom= Wpvc_Settings_Model::wpvc_custom_fields_json();
                $new_field= Wpvc_Common_State_Controller::wpvc_new_custom_fields_state();
                $return_val = array('custom'=>$custom,'new_customfield'=>$new_field,'insert'=>$added,'deleted'=>0,'updated'=>0,'taxonomy'=>$terms);
                return new WP_REST_Response($return_val,200);
            }else{
                die(json_encode(array('no_cheating' => "You have no permission to access Voting contest")));
            }   
        }

        public static function wpvc_callback_delete_customfield($request_data){
            if($_SERVER['HTTP_AUTHORIZE_WPVC']!='' && strtolower($_SERVER['HTTP_AUTHORIZE_WPVC']) == $_COOKIE['wpvc_voting_authorize'] && $_SERVER['HTTP_AUTHORIZE_WPVC_REQUEST']=='xmlhttprequest')
            { 
                $param = $request_data->get_params();	
                $deleted = Wpvc_Settings_Model::wpvc_delete_contestant_custom_field($param);
                $terms = get_terms( array(
                    'taxonomy' => WPVC_VOTES_TAXONOMY,
                    'hide_empty' => false,
                ));
                if(!empty($terms)){
                    foreach($terms as $key=>$term){
                        $imgcontest = get_term_meta($term->term_id,'imgcontest',true);
                        $term->category_type = $imgcontest;
                        $musicfileenable = get_term_meta($term->term_id,'musicfileenable',true);
                        $term->music_enable = $musicfileenable;
                        $array = json_decode(json_encode($term), true);
                        array_merge($array, array("category_type"=>$imgcontest));

                        $options = get_term_meta($term->term_id,WPVC_VOTES_TAXONOMY_ASSIGN,true);
                        if(is_array($options)){
                            foreach($options as $key => $opt){
                                $get_original = Wpvc_Settings_Model::wpvc_custom_fields_get_original($opt['id']);
                                if(empty($get_original)){
                                    unset($options[$key]);
                                }
                            }
                        }
                        if(is_array($options)){
                            $datas = array_values($options);
                            update_term_meta($term->term_id,WPVC_VOTES_TAXONOMY_ASSIGN,$datas);      
                        }              
                    }
                }
                //Get values again
                $custom= Wpvc_Settings_Model::wpvc_custom_fields_json();
                $new_field= Wpvc_Common_State_Controller::wpvc_new_custom_fields_state();
                $return_val = array('custom'=>$custom,'new_customfield'=>$new_field,'insert'=>0,'deleted'=>$deleted,'updated'=>0,'taxonomy'=>$terms);
                return new WP_REST_Response($return_val,200);
            }else{
                die(json_encode(array('no_cheating' => "You have no permission to access Voting contest")));
            }   
        }

        //Insert Custom Field Values  in add/edit contestants
        public static function wpvc_callback_insert_custom_values($request_data){
            if($_SERVER['HTTP_AUTHORIZE_WPVC']!='' && strtolower($_SERVER['HTTP_AUTHORIZE_WPVC']) == $_COOKIE['wpvc_voting_authorize'] && $_SERVER['HTTP_AUTHORIZE_WPVC_REQUEST']=='xmlhttprequest')
            { 
                global $wpdb;
                $param = $request_data->get_params();	
                $custom_values = $param['insertData'];
                $postID = $param['postID'];
                $newUploadedFiles = $param['newUploadedFiles'];
                $custom_fields = $param['customfield'];   

                update_post_meta($postID,WPVC_VOTES_POST,$custom_values);         
                if(is_array($custom_values)){
                    foreach($custom_values as $key=> $post_meta){
                        update_post_meta($postID, $key, $post_meta);	
                    }
                }
                if(!empty($custom_fields)){               
                    if(is_array($custom_fields)){
                        foreach($custom_fields as $cus_key => $fields){
                            $system_name = $fields['system_name'];
                            if($fields['question_type']=='FILE' && $newUploadedFiles[$system_name] != ""){
                                                        
                            $uploads = wp_upload_dir();
                            $filename = str_replace( $uploads['baseurl'], $uploads['basedir'],$newUploadedFiles[$system_name]);
                            $wp_filetype = wp_check_filetype($filename, null );

                            $attachment = array(
                                    'post_mime_type' => $wp_filetype['type'],
                                    'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
                                    'post_content' => '',
                                    'post_status' => 'inherit' 
                                );
                                $attach_id = attachment_url_to_postid($newUploadedFiles[$system_name]);

                                if($system_name === 'contestant-ow_video_upload_url'){
                                    $value = wp_get_attachment_url( $attach_id );
                                    update_post_meta($postID, 'contestant-ow_video_url', $newUploadedFiles[$system_name]);
                                    update_post_meta($postID, '_ow_video_upload_url', $newUploadedFiles[$system_name]);
                                }
                        
                                if($system_name === 'contestant-ow_music_url'){
                                    $value = wp_get_attachment_url( $attach_id );
                                    update_post_meta($postID, '_ow_music_upload_attachment', $attach_id);
                                    update_post_meta($postID, 'contestant-ow_video_url', $newUploadedFiles[$system_name]);
                                    update_post_meta($postID, '_ow_music_upload_url', $newUploadedFiles[$system_name]); 
                                    update_post_meta($postID, 'contestant-ow_music_url_link', $newUploadedFiles[$system_name]);    
                                }

                                require_once(ABSPATH . 'wp-admin/includes/image.php');
                                require_once( ABSPATH . 'wp-admin/includes/media.php' );
                                $attach_data = wp_generate_attachment_metadata( $attach_id, $filename );        
                                $res1   = wp_update_attachment_metadata( $attach_id, $attach_data );

                                if($system_name !== 'contestant-image'){
                                    $value = wp_get_attachment_url( $attach_id );
                                    $custom_attachment = array(
                                        'file'  => $filename,
                                        'url'   => $value,
                                        'type'  => $wp_filetype['type'],
                                        'error' => false,
                                    );
                                    update_post_meta($postID, 'ow_custom_attachment_'.$system_name, $custom_attachment);
                                }                            
                            }
                            
                            if($system_name === 'contestant-ow_video_url'){
                                update_post_meta($postID, 'contestant-ow_video_url', $custom_values['contestant-ow_video_url']);
                            }

                            //Remove the Extra fields attached to the contestant-ow_music_url
                            if($fields['question_type']=='FILE' && $newUploadedFiles[$system_name] == ""){ 
                                if($system_name === 'contestant-ow_music_url'){  
                                    delete_post_meta($postID, '_ow_music_upload_attachment');
                                    delete_post_meta($postID, 'contestant-ow_video_url');
                                    delete_post_meta($postID, '_ow_music_upload_url' );                                
                                }
                            }

                            //Delete Post Meta If no data
                            if($custom_values[$system_name] == ""){
                                delete_post_meta($postID, 'ow_custom_attachment_'.$system_name);
                            }
                        }                    
                    }
                }               
                return new WP_REST_Response($custom_values,200);
            }else{
                die(json_encode(array('no_cheating' => "You have no permission to access Voting contest")));
            }   
        }

        /*************************Registration Field ********************************/
        
        public static function wpvc_callback_reg_assign_custom($request_data){
            if($_SERVER['HTTP_AUTHORIZE_WPVC']!='' && strtolower($_SERVER['HTTP_AUTHORIZE_WPVC']) == $_COOKIE['wpvc_voting_authorize'] && $_SERVER['HTTP_AUTHORIZE_WPVC_REQUEST']=='xmlhttprequest')
            { 
                global $wpdb;
                $result = $request_data->get_params();	
                $data = $result['insertData'];
                update_option(WPVC_VOTES_REG_ASSIGN,$data);
                return new WP_REST_Response($result,200);
            }else{
                die(json_encode(array('no_cheating' => "You have no permission to access Voting contest")));
            }   
        }

        public static function wpvc_callback_get_reg_assign_custom($request_data){
            if($_SERVER['HTTP_AUTHORIZE_WPVC']!='' && strtolower($_SERVER['HTTP_AUTHORIZE_WPVC']) == $_COOKIE['wpvc_voting_authorize'] && $_SERVER['HTTP_AUTHORIZE_WPVC_REQUEST']=='xmlhttprequest')
            { 
                global $wpdb;
                $result = $request_data->get_params();	
                $options =get_option(WPVC_VOTES_REG_ASSIGN,$result);
                $selected_items= array('selected_items'=>$options);
                return new WP_REST_Response($selected_items,200);
            }else{
                die(json_encode(array('no_cheating' => "You have no permission to access Voting contest")));
            }   
        }

        public static function wpvc_callback_plugin_reg_custom_field(){
            if($_SERVER['HTTP_AUTHORIZE_WPVC']!='' && strtolower($_SERVER['HTTP_AUTHORIZE_WPVC']) == $_COOKIE['wpvc_voting_authorize'] && $_SERVER['HTTP_AUTHORIZE_WPVC_REQUEST']=='xmlhttprequest')
            { 
                $custom= Wpvc_Settings_Model::wpvc_reg_custom_fields_json();
                $new_field= Wpvc_Common_State_Controller::wpvc_new_reg_custom_fields_state();
                $return_val = array('custom'=>$custom,'new_customfield'=>$new_field,'insert'=>0,'deleted'=>0,'updated'=>0);
                return new WP_REST_Response($return_val,200);
            }else{
                die(json_encode(array('no_cheating' => "You have no permission to access Voting contest")));
            }   
        }

        public static function wpvc_callback_update_reg_customfield($request_data){
            if($_SERVER['HTTP_AUTHORIZE_WPVC']!='' && strtolower($_SERVER['HTTP_AUTHORIZE_WPVC']) == $_COOKIE['wpvc_voting_authorize'] && $_SERVER['HTTP_AUTHORIZE_WPVC_REQUEST']=='xmlhttprequest')
            { 
                $param = $request_data->get_params();	
                $data = $param['insertData'];
                $update_id = $param['id'];
                $added = Wpvc_Settings_Model::wpvc_update_reg_custom_field($update_id,$data);
                //Get values again
                $custom= Wpvc_Settings_Model::wpvc_reg_custom_fields_json();
                $new_field= Wpvc_Common_State_Controller::wpvc_new_reg_custom_fields_state();
                $return_val = array('custom'=>$custom,'new_customfield'=>$new_field,'insert'=>0,'deleted'=>0,'updated'=>$added);
                return new WP_REST_Response($return_val,200);
            }else{
                die(json_encode(array('no_cheating' => "You have no permission to access Voting contest")));
            }   
        }


        public static function wpvc_callback_save_reg_customfield($request_data){
            if($_SERVER['HTTP_AUTHORIZE_WPVC']!='' && strtolower($_SERVER['HTTP_AUTHORIZE_WPVC']) == $_COOKIE['wpvc_voting_authorize'] && $_SERVER['HTTP_AUTHORIZE_WPVC_REQUEST']=='xmlhttprequest')
            { 
                $param = $request_data->get_params();	
                $added = Wpvc_Settings_Model::wpvc_insert_reg_custom_field($param);
                //Get values again
                $custom= Wpvc_Settings_Model::wpvc_reg_custom_fields_json();
                $new_field= Wpvc_Common_State_Controller::wpvc_new_reg_custom_fields_state();
                $return_val = array('custom'=>$custom,'new_customfield'=>$new_field,'insert'=>$added,'deleted'=>0,'updated'=>0);
                return new WP_REST_Response($return_val,200);
            }else{
                die(json_encode(array('no_cheating' => "You have no permission to access Voting contest")));
            }   
        }

        public static function wpvc_callback_delete_reg_customfield($request_data){
            if($_SERVER['HTTP_AUTHORIZE_WPVC']!='' && strtolower($_SERVER['HTTP_AUTHORIZE_WPVC']) == $_COOKIE['wpvc_voting_authorize'] && $_SERVER['HTTP_AUTHORIZE_WPVC_REQUEST']=='xmlhttprequest')
            { 
                $param = $request_data->get_params();	
                $deleted = Wpvc_Settings_Model::wpvc_delete_reg_custom_field($param);
                //Get values again
                $custom= Wpvc_Settings_Model::wpvc_reg_custom_fields_json();
                $new_field= Wpvc_Common_State_Controller::wpvc_new_reg_custom_fields_state();

                $data = get_option(WPVC_VOTES_REG_ASSIGN);
                if(is_array($data)){
                    foreach($data as $key => $opt){
                        $get_original = Wpvc_Settings_Model::wpvc_user_custom_fields_get_original($opt['id']);
                        if(empty($get_original)){
                            unset($data[$key]);
                        }
                    }
                }
                if(is_array($data)){
                    $datas = array_values($data);
                    update_option(WPVC_VOTES_REG_ASSIGN,$datas);  
                }       
                
                $return_val = array('custom'=>$custom,'new_customfield'=>$new_field,'insert'=>0,'deleted'=>$deleted,'updated'=>0);
                return new WP_REST_Response($return_val,200);
            }else{
                die(json_encode(array('no_cheating' => "You have no permission to access Voting contest")));
            }   
        }

        /***************** Translations ***************/
        
        public static function wpvc_callback_plugin_translations_data(){
            if($_SERVER['HTTP_AUTHORIZE_WPVC']!='' && strtolower($_SERVER['HTTP_AUTHORIZE_WPVC']) == $_COOKIE['wpvc_voting_authorize'] && $_SERVER['HTTP_AUTHORIZE_WPVC_REQUEST']=='xmlhttprequest')
            { 
                $translations = Wpvc_Common_State_Controller::wpvc_translations_state();
                $languages = Wpvc_Settings_Model::wpvc_languages_list();
                $return_val = array('languages' => $languages, 'translations' => $translations);
                return new WP_REST_Response($return_val,200);
            }else{
                die(json_encode(array('no_cheating' => "You have no permission to access Voting contest")));
            }   
        }

        public static function wpvc_callback_create_translations_file($request_data){
            if($_SERVER['HTTP_AUTHORIZE_WPVC']!='' && strtolower($_SERVER['HTTP_AUTHORIZE_WPVC']) == $_COOKIE['wpvc_voting_authorize'] && $_SERVER['HTTP_AUTHORIZE_WPVC_REQUEST']=='xmlhttprequest')
            { 
                $param = $request_data->get_params();	
                
                $translations = Wpvc_Settings_Model::wpvc_translations_create($param['lang']);
                $return_val = array('translations' => $translations);
                return new WP_REST_Response($return_val,200);
            }else{
                die(json_encode(array('no_cheating' => "You have no permission to access Voting contest")));
            }   
        }

        public static function wpvc_callback_save_translations_file($request_data){
            if($_SERVER['HTTP_AUTHORIZE_WPVC']!='' && strtolower($_SERVER['HTTP_AUTHORIZE_WPVC']) == $_COOKIE['wpvc_voting_authorize'] && $_SERVER['HTTP_AUTHORIZE_WPVC_REQUEST']=='xmlhttprequest')
            { 
                $param = $request_data->get_params();	
                
                $translations = Wpvc_Settings_Model::wpvc_translations_save($param['insertData']);
                $return_val = array('translations' => $translations);
                return new WP_REST_Response($return_val,200);
            }else{
                die(json_encode(array('no_cheating' => "You have no permission to access Voting contest")));
            }   
        }

        public static function wpvc_callback_site_translations_data(){
            if($_SERVER['HTTP_AUTHORIZE_WPVC']!='' && strtolower($_SERVER['HTTP_AUTHORIZE_WPVC']) == $_COOKIE['wpvc_voting_authorize'] && $_SERVER['HTTP_AUTHORIZE_WPVC_REQUEST']=='xmlhttprequest')
            { 
                $translations = Wpvc_Settings_Model::wpvc_get_translations();
                $return_val = array('site_translations' => $translations);
                return new WP_REST_Response($return_val,200);
            }else{
                die(json_encode(array('no_cheating' => "You have no permission to access Voting contest")));
            }   
        }
				
    }
}else
die("<h2>".__('Failed to load Voting Rest Actions Controller','voting-contest')."</h2>");


return new Wpvc_Rest_Actions_Controller();
