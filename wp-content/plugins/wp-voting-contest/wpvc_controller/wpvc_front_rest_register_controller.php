<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if(!class_exists('Wpvc_Front_Rest_Register_Controller')){
    class Wpvc_Front_Rest_Register_Controller{

      public function __construct(){
          add_action( 'rest_api_init', array($this,'wpvc_front_rest_api_register'));
          add_filter('rest_prepare_contestants', array($this,'wpvc_contestant_meta_filters'), 10, 3);
          add_filter('rest_contestants_collection_params',array($this,'wpvc_filter_add_rest_orderby_params'), 10, 2);
          add_filter('rest_contestants_query', array($this,'wpvc_filter_rest_contestants_query'), 10, 2);

          include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
          if(is_plugin_active('js_composer/js_composer.php') && defined( 'WPB_VC_VERSION' ))
          {            
            WPBMap::addAllMappedShortcodes();
          }
      }

      public function wpvc_front_rest_api_register(){               
        $GLOBALS['wpvc_user_id'] = get_current_user_id();
        /****************************** Front end Rest ****************************/
        register_rest_route('wpvc-voting/v1', '/wpvcgetshowcontestant', [
          'methods'  => WP_REST_Server::ALLMETHODS,
          'callback' => array('Wpvc_Front_Rest_Actions_Controller','wpvc_callback_showallcontestants'),		
          'show_in_index' => FALSE
        ]);
               
        register_rest_route('wpvc-voting/v1', '/wpvcsubmitentry', [
          'methods'  => WP_REST_Server::CREATABLE,
          'callback' => array('Wpvc_Front_Rest_Actions_Controller','wpvc_callback_submit_entry'),		
          'show_in_index' => FALSE
        ]);

        register_rest_route('wpvc-voting/v1', '/wpvcupdatesubmitentry', [
          'methods'  => WP_REST_Server::CREATABLE,
          'callback' => array('Wpvc_Front_Rest_Actions_Controller','wpvc_callback_update_submit_entry'),	
          'show_in_index' => FALSE	
        ]);
        
        register_rest_route('wpvc-voting/v1', '/wpvcsavevotes', [
          'methods'  => WP_REST_Server::CREATABLE,
          'callback' => array('Wpvc_Front_Rest_Actions_Controller','wpvc_callback_save_votes'),		
          'show_in_index' => FALSE
        ]);

        register_rest_route('wpvc-voting/v1', '/wpvcsendemail', [
          'methods'  => WP_REST_Server::CREATABLE,
          'callback' => array('Wpvc_Front_Rest_Actions_Controller','wpvc_callback_send_email'),	
          'show_in_index' => FALSE	
        ]);

        register_rest_route('wpvc-voting/v1', '/wpvcsendemailverify', [
          'methods'  => WP_REST_Server::CREATABLE,
          'callback' => array('Wpvc_Front_Rest_Actions_Controller','wpvc_callback_send_email_verification'),	
          'show_in_index' => FALSE	
        ]);
        
        register_rest_route('wpvc-voting/v1', '/wpvcverifycaptcha', [
          'methods'  => WP_REST_Server::CREATABLE,
          'callback' => array('Wpvc_Front_Rest_Actions_Controller','wpvc_callback_captcha_verify'),	
          'show_in_index' => FALSE	
        ]);

        register_rest_route('wpvc-voting/v1', '/wpvclogon', [
          'methods'  => WP_REST_Server::CREATABLE,
          'callback' => array('Wpvc_Front_Rest_Actions_Controller','wpvc_callback_user_logon'),		
          'show_in_index' => FALSE
        ]);        
        
        register_rest_route('wpvc-voting/v1', '/wpvcregister', [
          'methods'  => WP_REST_Server::CREATABLE,
          'callback' => array('Wpvc_Front_Rest_Actions_Controller','wpvc_callback_user_register'),
          'show_in_index' => FALSE		
        ]);  

        register_rest_route('wpvc-voting/v1', '/wpvcgetregister', [
          'methods'  => WP_REST_Server::CREATABLE,
          'callback' => array('Wpvc_Front_Rest_Actions_Controller','wpvc_callback_user_get_register'),	
          'show_in_index' => FALSE	
        ]);  

        register_rest_route('wpvc-voting/v1', '/wpvcresetpassword', [
          'methods'  => WP_REST_Server::CREATABLE,
          'callback' => array('Wpvc_Front_Rest_Actions_Controller','wpvc_callback_reset_password'),	
          'show_in_index' => FALSE	
        ]); 

        register_rest_route('wpvc-voting/v1', '/wpvcdeletecontestant', [
          'methods'  => WP_REST_Server::CREATABLE,
          'callback' => array('Wpvc_Front_Rest_Actions_Controller','wpvc_callback_delete_contestant'),		
          'show_in_index' => FALSE
        ]);  

        register_rest_route('wpvc-voting/v1', '/wpvceditcontestant', [
          'methods'  => WP_REST_Server::CREATABLE,
          'callback' => array('Wpvc_Front_Rest_Actions_Controller','wpvc_callback_edit_contestant'),
          'show_in_index' => FALSE		
        ]);          

        register_rest_route('wpvc-voting/v1', '/wpvcbuyvotesentry', [
          'methods'  => WP_REST_Server::CREATABLE,
          'callback' => array('Wpvc_Front_Rest_Actions_Controller','wpvc_callback_buyvotesentry'),
          'show_in_index' => FALSE
        ]);

        register_rest_route('wpvc-voting/v1', '/wpvcgetstripesecret', [
          'methods'  => WP_REST_Server::CREATABLE,
          'callback' => array('Wpvc_Front_Rest_Actions_Controller','wpvc_buyvotes_stripe_secrect'),	
          'show_in_index' => FALSE	
        ]); 

        register_rest_route('wpvc-voting/v1', '/wpvcfacebooklogin', [
          'methods'  => WP_REST_Server::CREATABLE,
          'callback' => array('Wpvc_Front_Rest_Actions_Controller','wpvc_callback_user_register_facebook'),	
          'show_in_index' => FALSE	
        ]);

        register_rest_route('wpvc-voting/v1', '/wpvctwitterlogin', [
          'methods'  => WP_REST_Server::CREATABLE,
          'callback' => array('Wpvc_Front_Rest_Actions_Controller','wpvc_callback_user_register_twitter'),
          'show_in_index' => FALSE		
        ]);

        register_rest_route('wpvc-voting/v1', '/wpvcgooglelogin', [
          'methods'  => WP_REST_Server::CREATABLE,
          'callback' => array('Wpvc_Front_Rest_Actions_Controller','wpvc_callback_user_register_google'),	
          'show_in_index' => FALSE	
        ]);       

      }				

      public function wpvc_filter_add_rest_orderby_params($params){
        $params['orderby']['enum'][] = WPVC_VOTES_CUSTOMFIELD;
        $params['orderby']['enum'][] = 'votes';
        $params['orderby']['enum'][] = 'name';
        $params['orderby']['enum'][] = 'wpvc_judge_score';
        $params['orderby']['enum'][] = 'rand';
        $params['orderby']['enum'][] = 'menu_order';
      	return $params;
      }

      public function wpvc_filter_rest_contestants_query($query_vars, $request) { 
        $orderby = $request->get_param('orderby');
        $search = $request->get_param('search');
        $contst_cat = $request->get_param('contest_category');

        if($search != ''){
          //Empty s to stop search on post title
          $query_vars["s"] = '';
          if(is_array($contst_cat)){
            $imgcontest = get_term_meta($contst_cat[0],'imgcontest',true);
            $options = get_term_meta($contst_cat[0],WPVC_VOTES_TAXONOMY_ASSIGN,true);
            $meta_search = array('relation'=>'OR');
            if(!empty($options)){
              foreach($options as $key=>$cate){
                $system_name = $cate['system_name'];
                $meta_search[$system_name] = array(
                  'key'     => $system_name,
                  'value'   => $search,
                  'compare' => 'RLIKE'
                );
              }
            }else{
              $not_saved = Wpvc_Shortcode_Model::wpvc_custom_fields_by_contest('contest');
              if(!empty($not_saved)){
                foreach($not_saved as $key=>$cate){
                  $system_name = $cate['system_name'];
                  $meta_search[$system_name] = array(
                    'key'     => $system_name,
                    'value'   => $search,
                    'compare' => 'RLIKE'
                  );
                }
              }
            }
          }else{
            $not_saved = Wpvc_Shortcode_Model::wpvc_custom_fields_by_contest('contest');
              if(!empty($not_saved)){
                foreach($not_saved as $key=>$cate){
                  $system_name = $cate['system_name'];
                  $meta_search[$system_name] = array(
                    'key'     => $system_name,
                    'value'   => $search,
                    'compare' => 'RLIKE'
                  );
                }
              }
          }

          $query_vars['meta_query'] = $meta_search;
        }

        if (isset($orderby) && ($orderby === 'votes_count' || $orderby === 'votes')) {
            $query_vars["meta_key"] = WPVC_VOTES_CUSTOMFIELD;
            $query_vars["orderby"] = "meta_value_num"; 
        }
        session_start();
        if(isset($orderby) && ($orderby === 'rand')){
          $seed = ($_SESSION['wpvc_contestant_seed']!=0)?$_SESSION['wpvc_contestant_seed']:"";
          if ($seed == '') {
            $seed = rand();
            $_SESSION['wpvc_contestant_seed'] = $seed;
          }
          $query_vars["orderby"] = 'RAND('.$seed.')';
        }else{
          $_SESSION['wpvc_contestant_seed'] = 0;
        }
        session_write_close();
        if (isset($orderby) && $orderby === 'wpvc_judge_score') {
            $query_vars["meta_key"] = 'wpvc_judge_score';
            $query_vars["orderby"] = "meta_value_num"; 
        }
        
        return $query_vars;
      }

      public function wpvc_contestant_meta_filters($data, $post, $context){
        $vote_opt = get_option(WPVC_VOTES_SETTINGS);
        $check_param = $context->get_params();
        $data->data['post_title'] = html_entity_decode(get_the_title($post->ID),ENT_QUOTES | ENT_HTML5);
        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        if(is_plugin_active('js_composer/js_composer.php') && defined( 'WPB_VC_VERSION' ))
        {		
          $content = strip_tags( do_shortcode(get_the_excerpt($post->ID)));
          $full_content = apply_filters( 'the_content' ,  $post->post_content) ;
        }else{
          $content = html_entity_decode(get_the_excerpt($post->ID),ENT_QUOTES | ENT_HTML5);
          $full_content =html_entity_decode(get_the_content($post->ID),ENT_QUOTES | ENT_HTML5);
        }

        $data->data['post_excerpt'] = (has_excerpt($post) != null)?$post->post_excerpt:$content;
        $data->data['full_content'] = $full_content;

        $featured_image_id = $data->data['featured_media']; // get featured image id
        $featured_image_url = wp_get_attachment_image_src($featured_image_id, 'medium'); // get url of the original size
        $featured_image_large = wp_get_attachment_image_src($featured_image_id, 'large');
        if ($featured_image_url) {
          $data->data['featured_image_url'] = $featured_image_url[0];
        }else{
          $data->data['featured_image_url'] = WPVC_NO_IMAGE_CONTEST;
        }
        
        if ($featured_image_large) {
          $data->data['featured_image_large_url'] = $featured_image_large[0];
        }else{
          $data->data['featured_image_url'] = WPVC_NO_IMAGE_CONTEST;
        }

        //short_cont_image - Listing Page
        if($vote_opt['common']['short_cont_image'] != null){
        	$short_cont_image = wp_get_attachment_image_src($featured_image_id, $vote_opt['common']['short_cont_image']);
        	$data->data['short_cont_image'] = $short_cont_image[0];
        }
		    //page_cont_image - Single Contestant
        if($vote_opt['common']['page_cont_image'] != null){
          $page_cont_image = wp_get_attachment_image_src($featured_image_id, $vote_opt['common']['page_cont_image']);
          $data->data['page_cont_image'] = $page_cont_image[0];
        }
        
        $term = get_the_terms($post->ID, WPVC_VOTES_TAXONOMY);
        if ($term) {
          $data->data['term_id'] = $termid = $term[0]->term_id;
          $data->data['term_name'] = $term[0]->name;
          $data->data['vote_button_status']= Wpvc_Voting_Model::wpvc_check_before_post($post->ID,$termid);
          $imgcontest = get_term_meta( $termid,'imgcontest',true);
          $data->data['img_contest'] = $imgcontest;
          if($_SERVER['HTTP_AUTHORIZE_WPVC_REQUESTMETHOD']=='authorprofile'){
            $category_options = get_term_meta($termid);
            $align_category = array();
            $imgcontest = get_term_meta($termid,'imgcontest',true);
            $musicfileenable = get_term_meta($termid,'musicfileenable',true);
            if(is_array($category_options)){
              foreach($category_options as $key=>$val){
                if($key=='contest_rules'){
                  $align_category[$key] = format_to_edit($val[0],TRUE);
                }else
                  $align_category[$key] = maybe_unserialize($val[0]);
              }
            }
            $data->data['allcatoption'] = $align_category;
            $custom_fields = Wpvc_Front_Rest_Actions_Controller::wpvc_get_custom_fields($termid,$post->ID);
            $data->data['custom_fields'] = $custom_fields;
          }
        }

        // Votes Count
        $votes_count = get_post_meta($post->ID, WPVC_VOTES_CUSTOMFIELD, true);
        if ($votes_count) {
          $data->data[WPVC_VOTES_CUSTOMFIELD] = $votes_count;
        } else {
          $data->data[WPVC_VOTES_CUSTOMFIELD] = 0;
        }

        $wpvc_video_extension = get_option('_ow_video_extension');
        
        //Get Custom_fields
        $get_custom_fields = Wpvc_Front_Rest_Actions_Controller::wpvc_get_custom_fields($termid);
        $get_post_metas = get_post_meta($post->ID, WPVC_VOTES_POST,TRUE);
        if(!empty($get_custom_fields)){
          $custom_values = array();
          $custom_fields = $get_custom_fields['custom_field'];
		  $musicfileenable = get_term_meta($termid,'musicfileenable',true);
          if(is_array($custom_fields)){
            foreach($custom_fields as $cus_key => $fields){
              $system_name = $fields['system_name'];
              //System file type upload / file
              if($fields['question_type']=='FILE'){
                if($system_name == 'contestant-ow_music_url'){ 
					if($musicfileenable == 'on') {
						$music_url = get_post_meta($post->ID, '_ow_music_upload_url', TRUE); 
					}
					else{
						$music_url = get_post_meta($post->ID, 'contestant-ow_music_url_link', TRUE);
					}				
                  
                  $custom_values[$system_name]= empty($music_url)?'':html_entity_decode($music_url,ENT_QUOTES | ENT_HTML5);
                }
                elseif($system_name == 'contestant-ow_video_upload_url'){
                  if($wpvc_video_extension != 1){
                    continue;
                  }
                  $video_url = get_post_meta($post->ID, 'contestant-ow_video_url', TRUE);
                  if($video_url==''){
                    $video_url = get_post_meta($post->ID,'_ow_video_upload_url',true); 
                  }
                  if(isset($get_post_metas['contestant-ow_video_url']) && $video_url==''){
                    $video_url = $get_post_metas['contestant-ow_video_url'];
                  }
                  //$custom_values[$system_name]= empty($video_url)?'':html_entity_decode($video_url,ENT_QUOTES | ENT_HTML5);
                  $custom_values['contestant-ow_video_url']= empty($video_url)?'':html_entity_decode($video_url,ENT_QUOTES | ENT_HTML5);
                }
                else{
                  $post_image = get_post_meta($post->ID, 'ow_custom_attachment_'.$system_name, TRUE);
                  $custom_values[$system_name]= empty($post_image)?'':$post_image['url'];
                }
              }else{
                //If not file upload
                if($system_name == 'contestant-ow_video_url'){
                  if($wpvc_video_extension == 1){
                    $video_url = get_post_meta($post->ID, 'contestant-ow_video_url', TRUE);
                    if($video_url==''){
                      $video_url = get_post_meta($post->ID,'_ow_video_upload_url',true); 
                    }
                    $custom_values[$system_name]= empty($video_url)?'':html_entity_decode($video_url,ENT_QUOTES | ENT_HTML5);
                  }else{
                    $video_url = get_post_meta($post->ID, 'contestant-ow_video_url', TRUE);
                    if($video_url==''){
                      $video_url = get_post_meta($post->ID,'_ow_video_upload_url',true); 
                    }
                    if(isset($get_post_metas['contestant-ow_video_url']) && $video_url==''){
                      $video_url = $get_post_metas['contestant-ow_video_url'];
                    }
                    $custom_values[$system_name]= empty($video_url)?'':html_entity_decode($video_url,ENT_QUOTES | ENT_HTML5);
                  }
                }elseif($system_name == 'contestant-ow_music_url' || $system_name == 'contestant-ow_music_url_link'){			  
				  
				  if($musicfileenable == 'on') {
                  	$music_url = get_post_meta($post->ID, '_ow_music_upload_url', TRUE);
				  }
                  if($music_url==''){ 
                    $music_url = get_post_meta($post->ID,'contestant-ow_music_url_link',TRUE); 
                  }
                  if($music_url==''){
                    $music_url = get_post_meta($post->ID,'contestant-ow_video_url',TRUE); 
                  }
                  if($music_url==''){
                    $music_url = get_post_meta($post->ID,'_ow_music_upload_attachment',TRUE); 
                  }
                  $custom_values[$system_name]= empty($music_url)?'':html_entity_decode($music_url,ENT_QUOTES | ENT_HTML5);
                }else{
                  if(is_array($get_post_metas) && array_key_exists($system_name,$get_post_metas))
                    $custom_values[$system_name] = $get_post_metas[$system_name];
                }
              }
            }
            $data->data['custom_fields_value'] = $custom_values;
          }
        }

        //get author
        $author = get_the_author();
        $author_email = get_the_author_meta( 'user_email' );
        $data->data['author_name'] = $author;
        $data->data['author_email'] = $author_email;
    
        // Viewers Count
        $views_count = get_post_meta($post->ID,WPVC_VOTES_VIEWERS, true);
        if ($views_count) {
          $data->data[WPVC_VOTES_VIEWERS] = $views_count;
        } else {
          $data->data[WPVC_VOTES_VIEWERS] = 0;
        }

        $next_post = get_next_post(TRUE,'',WPVC_VOTES_TAXONOMY);
        $previous_post = get_previous_post(TRUE,'',WPVC_VOTES_TAXONOMY);
        if(!empty($next_post)){
          $data->data['next_link'] = get_permalink($next_post->ID);
          $data->data['next_title'] =$next_post->post_title;
        }else{
          $data->data['next_link'] = NULL;
          $data->data['next_title'] = NULL;
        }

        if(!empty($previous_post)){
          $data->data['previous_link'] = get_permalink($previous_post->ID);
          $data->data['previous_title'] =$previous_post->post_title;
        }else{
          $data->data['previous_link'] = NULL;
          $data->data['previous_title'] = NULL;
        }
        
        return $data;
      }
    }
}else
die("<h2>".__('Failed to load Voting Front Rest Actions Controller','voting-contest')."</h2>");

return new Wpvc_Front_Rest_Register_Controller();

