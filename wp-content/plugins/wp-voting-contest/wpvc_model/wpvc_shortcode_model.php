<?php
if(!class_exists('Wpvc_Shortcode_Model')){
	class Wpvc_Shortcode_Model {

		public static function wpvc_settings_page_json($category_id=NULL){
			global $wpdb;
			$options = get_option(WPVC_VOTES_SETTINGS);
			$options['wpvc_video_extension'] = get_option('_ow_video_extension');
			if($category_id != ''){
				$color_options = get_term_meta($category_id,'color',true);
				$syle_options = get_term_meta($category_id,'style',true);
				if(!empty($color_options)){
					unset($options['color']);
					$options = array_merge($options, array("color"=>$color_options));
				}
	
				if(!empty($syle_options)){
					$vote_count_showhide = (isset($options['style']['vote_count_showhide']))?$options['style']['vote_count_showhide']:'off';
					unset($options['style']);
					if (!array_key_exists("vote_count_showhide",$syle_options)){
						$syle_options['vote_count_showhide']=$vote_count_showhide;
					}
					$options = array_merge($options, array("style"=>$syle_options));
				}
			}
            return $options;
		}

		public static function wpvc_show_contestant_page_json($show_cont_args){
			global $wpdb;

			if (isset($show_cont_args['paged']) && $show_cont_args['paged'] > 0)
				$paged = $show_cont_args['paged'];
			else{
				$paged = 1;
			}

			//exclude attribute
			if(isset($show_cont_args['exclude']) && $show_cont_args['exclude'] != null):
				$excluded_ids = explode(',',$show_cont_args['exclude']);
			else:
				$excluded_ids = array();
			endif;

			//include attribute
			if(isset($show_cont_args['include']) && $show_cont_args['include'] != null):
				$included_ids = explode(',',$show_cont_args['include']);
			else:
				$included_ids = array();
			endif;

			
			$postargs = array(
				'post_type' => WPVC_VOTES_TYPE,
				'post_status' => 'publish',
				'posts_per_page' => $show_cont_args['postperpage'],
				'tax_query' => array(
					array(
							'taxonomy' => $show_cont_args['taxonomy'],
							'field' => 'id',
							'terms' => $show_cont_args['id'],
							'include_children' => false
						)
				),
				'paged' => $paged,
				'post__not_in' => $excluded_ids,
				'post__in' => $included_ids,
				//'search_prod_title' => $search_string		
			);

			if($show_cont_args['orderby'] == 'votes') {
				$postargs['meta_key'] = OW_VOTES_CUSTOMFIELD;
				$postargs['orderby'] = 'meta_value_num';
				$postargs['order'] = $show_args;
			}
			elseif($show_cont_args['orderby'] == 'top') {
				$postargs['meta_key'] = OW_VOTES_CUSTOMFIELD;
				$postargs['orderby'] = 'meta_value_num';
				$postargs['order'] = 'DESC';
			}
			elseif($show_cont_args['orderby'] == 'bottom') {
				$postargs['meta_key'] = OW_VOTES_CUSTOMFIELD;
				$postargs['orderby'] = 'meta_value_num';
				$postargs['order'] = 'ASC';
			}else{
				$postargs['orderby'] = $show_cont_args['orderby'];
				$postargs['order'] = $show_args;
			}
			$contest_post = new WP_Query($postargs);
			return $contest_post;
		}

		public static function wpvc_custom_fields_by_id($id){ 
            global $wpdb;
            $sql = "SELECT * FROM " .WPVC_VOTES_ENTRY_CUSTOM_TABLE." WHERE delete_time = 0 AND id=". $id ." AND admin_only='Y'";
			$custom_fields = $wpdb->get_row($sql,ARRAY_A);
			return $custom_fields;
		}

		public static function wpvc_custom_fields_by_name($name){
            global $wpdb;
            $sql = "SELECT * FROM " .WPVC_VOTES_ENTRY_CUSTOM_TABLE." WHERE delete_time = 0 AND system_name='". $name ."' AND admin_only='Y'";
			$custom_fields = $wpdb->get_row($sql,ARRAY_A);
			return $custom_fields;
		}

		public static function wpvc_custom_fields_files($get_array_val){
            global $wpdb;
			$where_in = "'".implode("','",$get_array_val)."'";
            $sql = "SELECT system_name FROM " .WPVC_VOTES_ENTRY_CUSTOM_TABLE." WHERE system_name IN (".$where_in.") AND delete_time = 0 AND question_type='FILE' AND admin_only='Y'";
			$custom_fields = $wpdb->get_results($sql,ARRAY_A);
			return $custom_fields;
		}

		public static function wpvc_reg_custom_fields(){
            global $wpdb;
			$get_opt = get_option(WPVC_VOTES_REG_ASSIGN);
			$get_ids = array();
			if(!empty($get_opt)){
				foreach($get_opt as $get_reg){
					$get_ids[] = $get_reg['id'];
				}
			}
			$where_in = "'".implode("','",$get_ids)."'";
            $sql = "SELECT * FROM " .WPVC_VOTES_USER_CUSTOM_TABLE." WHERE id IN (".$where_in.") AND  delete_time = 0 AND admin_only='Y'";
			$custom_fields = $wpdb->get_results($sql,ARRAY_A);
			return $custom_fields;
		}
		
		public static function wpvc_custom_fields_by_contest($contest,$musicfileenable=NULL){
			global $wpdb;
			$sql = "SELECT * FROM " .WPVC_VOTES_ENTRY_CUSTOM_TABLE." WHERE system_name LIKE '%contestant%' AND delete_time = 0 order by sequence ASC";
			$custom_fields = $wpdb->get_results($sql,ARRAY_A);
			$wpvc_video_extension = get_option('_ow_video_extension');

			$return_array=array();
			if(!empty($custom_fields)){
				foreach($custom_fields as $custom){
					$needed_array=array('contestant-title','contestant-image','contestant-desc');
					switch($contest){
						case 'music':
							if($musicfileenable=='on'){
								$needed_array = array('contestant-title','contestant-ow_music_url');
							}else{
								$needed_array = array('contestant-title','contestant-ow_music_url_link');
							}
							
						break;
		
						case 'video':
							if($wpvc_video_extension == 1){
								$needed_array = array('contestant-title','contestant-ow_video_upload_url', 'contestant-ow_video_url');
							}else{
								$needed_array = array('contestant-title','contestant-ow_video_url');
							}
						break;

						case 'contest':
							$needed_array = array('contestant-title');
						break;
					}

					if(in_array($custom['system_name'],$needed_array)){
						$react_values = json_decode($custom['react_val']);
						$comma_sep_values = explode(',',$custom['response']);
						$custom = array_merge($custom, array('react_values' =>$react_values,'drop_values'=>$comma_sep_values));
						$return_array[]= $custom;
					}
					
				}
			}
			return $return_array;
		}

		public static function wpvc_insert_contestants($category_id,$insertdata,$userdata="",$post_status=""){
			global $wpvc_user_id;
			$vote_opt = get_option(WPVC_VOTES_SETTINGS);

			if($post_status == ""){
				if($vote_opt){
					$vote_publishing_type = ($vote_opt['contest']['vote_publishing_type'] == 'on')?'publish':WPVC_DEF_PUBLISHING;
				}else{
					$vote_publishing_type = WPVC_DEF_PUBLISHING;
				}
			}
			else{
				$vote_publishing_type = $post_status;
			}

			$args = array(
				'post_author' => $wpvc_user_id,
				'post_content' => $insertdata['contestant-desc'],
				'post_status' => $vote_publishing_type ,
				'post_type' => WPVC_VOTES_TYPE,
				'post_title' => $insertdata['contestant-title']
			);
			$post_id = wp_insert_post($args);
			wp_set_post_terms( $post_id, $category_id , WPVC_VOTES_TAXONOMY);
			update_post_meta($post_id, WPVC_VOTES_POST, $insertdata);	

			if(is_array($insertdata)){
				foreach($insertdata as $key=> $post_meta){
					update_post_meta($post_id, $key, $post_meta);	
				}
			}
			
			if(array_key_exists('contestant-ow_video_url',$insertdata)){
				update_post_meta($post_id,'contestant-ow_video_url', $insertdata['contestant-ow_video_url']);
			}
			
			//Insert for Tracking
			self::wpvc_insert_post_entry_track($userdata['user_id_profile'],$category_id,$post_id);
			
			return $post_id;
		}

		//WPVC_VOTES_ENTRY_TRACK 
		public static function wpvc_get_post_entry_track($user_ID,$cat_id){			
		    global $wpdb;
			if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARTDED_FOR'] != '') {
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
				$ip = $_SERVER['REMOTE_ADDR'];
			}

			$where = " where user_id_map='".$user_ID."' and ip='".$ip."' and ow_term_id='".$cat_id."'";
			$sql = "SELECT COUNT(*) from ".WPVC_VOTES_ENTRY_TRACK.$where;
			$track = $wpdb->get_var($sql);
			return $track;
		}

		//Accssed in Wpvc_Email_Controller
		public static function wpvc_get_post_entry_track_by_where($where){
			global $wpdb;
			$sql = "SELECT * from ".WPVC_VOTES_ENTRY_TRACK.$where;
			$track = $wpdb->get_row($sql);
			return $track;
		}


		public static function wpvc_insert_post_entry_track($user_ID,$term_id,$post_id){
			global $wpdb;
			if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARTDED_FOR'] != '') {
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
				$ip = $_SERVER['REMOTE_ADDR'];
			}
			$save_sql = 'INSERT INTO `' .WPVC_VOTES_ENTRY_TRACK. '` (`user_id_map`,`ip`,`count_post`,`ow_term_id`,`post_id`) VALUES ("' . $user_ID . '", "' . $ip . '", 1 , "'.$term_id.'","'.$post_id.'") ';
			$wpdb->query($save_sql);
		}
		
		public static function wpvc_delete_post_entry_track($post_id){
			global $wpdb;
			$wpdb->delete(
				WPVC_VOTES_ENTRY_TRACK, 
				array(
					'post_id' => $post_id 
				),
				array(
					'%d' 
				)
			);
		}

		public static function wpvc_update_contestants($post_id,$insertdata){
			$vote_opt = get_option(WPVC_VOTES_SETTINGS);
			if($vote_opt){
				$vote_publishing_type = ($vote_opt['contest']['vote_publishing_type'] == 'on')?'publish':WPVC_DEF_PUBLISHING;
			}else{
				$vote_publishing_type = WPVC_DEF_PUBLISHING;
			}
			
			$args = array(
				'ID' =>$post_id,
				'post_content' => $insertdata['contestant-desc'],
				'post_status' => $vote_publishing_type ,
				'post_type' => WPVC_VOTES_TYPE,
				'post_title' => $insertdata['contestant-title']
			);
			$post_id = wp_update_post($args);
			update_post_meta($post_id, WPVC_VOTES_POST, $insertdata);
			if(is_array($insertdata)){
				foreach($insertdata as $key=> $post_meta){
					update_post_meta($post_id, $key, $post_meta);	
				}
			}
			
			if(array_key_exists('contestant-ow_video_url',$insertdata)){
				update_post_meta($post_id,'contestant-ow_video_url', $insertdata['contestant-ow_video_url']);
			}
			
			return $post_id;
		}

		public static function wpvc_get_total_post_count($term_id=null,$post_per_page=null){
			if($term_id !=null){
				$postargs = array(
					'post_type' => WPVC_VOTES_TYPE,
					'post_status' => 'publish',
					'tax_query' => array(
						array('taxonomy' => WPVC_VOTES_TAXONOMY,
						'field' => 'id',
						'terms' => $term_id,
						'include_children' => false)
					),
					'numberposts' => -1
				);
			}else{
				$postargs = array(
					'post_type' => WPVC_VOTES_TYPE,
					'post_status' => 'published',
					'numberposts' => -1
				);
			}
			if($post_per_page !=0 ){
				$total_num = count( get_posts( $postargs ) );
				$pagin_array = array();
				$count_pagers= ceil($total_num/$post_per_page);
				if($total_num > $post_per_page){
					$pagin_array = array('total_posts'=>$total_num,'per_page_post'=>$post_per_page,'page_nums'=>$count_pagers);
				}
			}else{
				$pagin_array = array('total_posts'=>0,'per_page_post'=>0,'page_nums'=>0);
			}
			return $pagin_array;
		}

		public static function wpvc_get_category_options_and_values($show_cont_args,$post_id=NULL,$flag=NULL){ 
			//To get category options
			if($flag=='profile'){
				$profile_id = $show_cont_args['contests'];
				if($profile_id!=''){
					$show_cont_args['id']=$profile_id;
				}
			}

           	if($post_id!=NULL && empty($show_cont_args)){
				$term = get_the_terms($post_id,WPVC_VOTES_TAXONOMY);
				if(is_array($term)){
					$show_cont_args['id'] = $term[0]->term_id;
				}
			}
			
			$vote_opt = get_option(WPVC_VOTES_SETTINGS);
			if($vote_opt){
				$category_options = get_term_meta($show_cont_args['id']);
				$align_category = array();
				$imgcontest = get_term_meta($show_cont_args['id'],'imgcontest',true);
				$musicfileenable = get_term_meta($show_cont_args['id'],'musicfileenable',true);
				if(is_array($category_options)){
					foreach($category_options as $key=>$val){
						if($key=='contest_rules'){
							$align_category[$key] = format_to_edit($val[0],TRUE);
						}else
							$align_category[$key] = maybe_unserialize($val[0]);
					}
				}

				$inter = Wpvc_Common_Shortcode_Controller::Wpvc_vote_get_thumbnail_sizes($vote_opt['common']['short_cont_image']);
				$height_tr =explode('--',$inter);
				$width_t =$height_tr[0];
				$height_t = $height_tr[1];
				$height = $height_t ? $height_t : '';
				$width = $width_t ? $width_t : '';

				$title = $vote_opt['common']['title'] ? $vote_opt['common']['title'] : NULL;
				$orderby = $vote_opt['common']['orderby'] ? $vote_opt['common']['orderby'] : 'votes_count';
				$order = $vote_opt['common']['order'] ? $vote_opt['common']['order'] : 'desc';
				$onlyloggedinuser = ($vote_opt['contest']['onlyloggedinuser']=='on') ? 1 : 0;
				$onlyloggedsubmit = ($vote_opt['contest']['vote_onlyloggedcansubmit']=='on') ? 1 : 0;
				$pagination_option =  $vote_opt['pagination']['contestant_per_page']; 
				$openform = $vote_opt['common']['vote_entry_form'];

				$votes_start_time = $align_category['votes_starttime'];
				$votes_expiration = $align_category['votes_expiration'];
				$tax_hide_photos_live = ($align_category['tax_hide_photos_live']!='')?$align_category['tax_hide_photos_live']:'off';
				$topten = $align_category['top_ten_count'];

				if($show_cont_args['orderby']=="")
				$show_cont_args['orderby'] = $orderby;
				if($show_cont_args['order']=="")
				$show_cont_args['order'] = $order;

				$show_form = 0;
				$current_time = current_time( 'timestamp', 0 );
				if($tax_hide_photos_live == 'on'){
					//Until live check with current time
					if(($votes_start_time !='' && strtotime($votes_start_time) > $current_time)){
						$category_thumb = 0 ;
					}elseif($votes_start_time==''){
						$category_thumb = 1;
						$show_form = 1;
					}
					else{
						$category_thumb = 1;	
					}
				}
				elseif(($votes_start_time != '' && strtotime($votes_start_time) > $current_time) || $votes_start_time==''){
					$category_thumb = 1;
					$show_form = 1;
				}
				elseif(($votes_expiration != '' && strtotime($votes_expiration) < $current_time)){
					$show_form = 0;
				}
				else{
					$category_thumb = 1;
				}
				
				$category_term_disp = ($align_category['termdisplay']=='on')?1:0;

				$skip_payment_array = array('ow_category_paypal_settings','payment_paypal_entry_amount','ow_category_stripe_settings','payment_stripe_entry_amount','ow_category_paystack_settings','payment_paystack_entry_amount','category_coupon_settings');
				$align_category = array_diff_key($align_category, array_flip($skip_payment_array));

				$sort_by = isset($show_cont_args['orderby'])?0:1;
				$search = isset($show_cont_args['search'])?1:0;

				
				$pagination_count = ($show_cont_args['postperpage']!='')?$show_cont_args['postperpage']:$vote_opt['pagination']['contestant_per_page']; 
				$pagin_response = SELF::wpvc_get_total_post_count($show_cont_args['id'],$pagination_count);

				if($show_cont_args['orderby']=='votes'){
					$show_cont_args['orderby']='votes_count';
					$show_cont_args['order']= $show_cont_args['order'] ? strtolower($show_cont_args['order']) : $vote_opt['common']['order'];
				}
				
				$showcount=10;
				//For upcoming contestants
				if($flag=='upcoming'){
					if($votes_start_time == '' || strtotime($votes_start_time) < $current_time){
						return 'no_upcoming_contest';
					}
				}
				
				if($flag=='endcontest'){
					if($votes_expiration == '' || strtotime($votes_expiration) > $current_time){
						return 'no_ended_contest';
					}
				}
				if($flag=='profile'){
					$show_form = $show_cont_args['form'];
				}
				
				if($flag=='topcontest'){
					$showcount = $show_cont_args['showcount'];
				}
				session_start();
				// If session has started, this data will be stored.
				$_SESSION['wpvc_contestant_seed'] = 0;
				session_write_close();
				if($show_cont_args['order']!='')
					$show_cont_args['order']=strtolower($show_cont_args['order']);
				//Check with settings and update same
				$show_cont_args = wp_parse_args($show_cont_args, array(
					'height'	 => $height,
					'width'		 => $width,
					'title' 	 => $title,
					'orderby'	 => $orderby,
					'order'		 => strtolower($order),
					'postperpage' => $pagination_option,
					'taxonomy'	  => WPVC_VOTES_TAXONOMY,
					'id'		 => 0,
					'paged'		 => 1,
					'ajaxcontent' => 0,				
					'showtimer'	 => 1,
					'showform' 	 => $show_form,
					
					//Newly added
					'openform'=>$openform,
					'showgallery'=>1,
					'showrules'=>0,
					'showtop'=>$topten,
					'showprofile'=>1,
					'flag_code'=>$flag,
					'showcount'=>$showcount,

					'forcedisplay' => 1,
					'thumb' 		=> $category_thumb,
					'termdisplay' 	=> $category_term_disp,
					'pagination'	=> 1,
					'sort_by'		=> $sort_by,
					'onlyloggedinuser' => $onlyloggedinuser,
					'onlyloggedsubmit'=>$onlyloggedsubmit,
					'tax_hide_photos_live' => $tax_hide_photos_live,
					'navbar'	 	=> 1,
					'contest_type'	=> $align_category['imgcontest'],
					'view' => 'grid',
					'search' => $search,
					'start_time' => $votes_start_time,
					'end_time'=> $votes_expiration,
					'showcontestants'=> 1,
					'server_time'=>wp_timezone(),
					
					//For react
					'user_logged'=>is_user_logged_in(),
					'user_id_profile'=>get_current_user_id(),
					'user_can_register'=>get_option('users_can_register'),
					'allcatoption'=> apply_filters('cat_extension_update_values',$align_category,$show_cont_args['id']),
					"pagin_response"=>$pagin_response,
					'contest_url'=>$_COOKIE['wpvc_contestant_URL'],
					'override_view'=>$_GET['view'],
					'twitter_auth' => Wpvc_Shortcode_Model::wpvc_twitter_login_url(),
					'extension_values' => apply_filters('extension_values_hook',array())
				));
			}
            
            
            return $show_cont_args;
        }

		public static function wpvc_get_widget_photo_leaders($show_cont_args){
			//Check with settings and update same
			$show_cont_args['id']=$show_cont_args['contest_tax'];
			$show_cont_args['showcount']=$show_cont_args['no_of_conts'];
			$orderby = 'votes_count';
			$order = 'desc';
			
			$vote_opt = get_option(WPVC_VOTES_SETTINGS);
			$category_options = get_term_meta($show_cont_args['id']);
			$align_category = array();
			if(is_array($category_options)){
				foreach($category_options as $key=>$val){
					if($key=='contest_rules'){
						$align_category[$key] = format_to_edit($val[0],TRUE);
					}else
						$align_category[$key] = maybe_unserialize($val[0]);
				}
			}
			$inter = Wpvc_Common_Shortcode_Controller::Wpvc_vote_get_thumbnail_sizes($vote_opt['common']['short_cont_image']);
			$height_tr =explode('--',$inter);
			$width_t =$height_tr[0];
			$height_t = $height_tr[1];
			$height = $height_t ? $height_t : '';
			$width = $width_t ? $width_t : '';

			$term = get_term($show_cont_args['contest_tax'], WPVC_VOTES_TAXONOMY);
			if ($term) {
			  $cat_title = $term->name;
			}

			if($show_cont_args['order']!='')
				$show_cont_args['order']=strtolower($show_cont_args['order']);

			$show_cont_args = wp_parse_args($show_cont_args, array(
				'height'	 => $height,
				'width'		 => $width,
				'orderby'	 => $orderby,
				'order'		 => strtolower($order),
				'taxonomy'	  => WPVC_VOTES_TAXONOMY,
				'id'		 => 0,
				'title' =>$cat_title,
				
				//Newly added
				'contest_type'	=> $align_category['imgcontest'],
				'view' => 'grid',
				//For react
				'allcatoption'=> apply_filters('cat_extension_update_values',$align_category,$show_cont_args['id']),
				'extension_values' => apply_filters('extension_values_hook',array())
			));
            return $show_cont_args;

		}

		public static function wpvc_get_widget_recent_contestants($show_cont_args){
			$show_cont_args['showcount']=$show_cont_args['no_of_conts'];
			$orderby = 'date';
			$order = 'desc';
			
			$vote_opt = get_option(WPVC_VOTES_SETTINGS);
			$inter = Wpvc_Common_Shortcode_Controller::Wpvc_vote_get_thumbnail_sizes($vote_opt['common']['short_cont_image']);
			$height_tr =explode('--',$inter);
			$width_t =$height_tr[0];
			$height_t = $height_tr[1];
			$height = $height_t ? $height_t : '';
			$width = $width_t ? $width_t : '';

			if($show_cont_args['order']!='')
				$show_cont_args['order']=strtolower($show_cont_args['order']);
			$show_cont_args = wp_parse_args($show_cont_args, array(
				'height'	 => $height,
				'width'		 => $width,
				'orderby'	 => $orderby,
				'order'		 => strtolower($order),
				'taxonomy'	  => WPVC_VOTES_TAXONOMY,
				'id'		 => 0,
				'title' =>$cat_title
			));
			return $show_cont_args;
		}

		public static function wpvc_get_category_login_options($show_cont_args){
			//Check with settings and update same
			$show_cont_args = wp_parse_args($show_cont_args, array(
				'user_logged'=>is_user_logged_in(),
				'user_id_profile'=>get_current_user_id(),
				'user_can_register'=>get_option('users_can_register'),
				'contest_url'=>$_COOKIE['wpvc_contestant_URL'],
				'override_view'=>$_GET['view'],
				'wp_user_logout'=>wp_logout_url()
			));
            return $show_cont_args;
		}

		//For Add contestants
		public static function wpvc_get_category_options_and_values_addcontestants($show_cont_args,$post_id=NULL){

			if($post_id!=NULL && empty($show_cont_args)){
				$term = get_the_terms($post_id,WPVC_VOTES_TAXONOMY);
				if(is_array($term)){
					$show_cont_args['id'] = $term[0]->term_id;
				}
		 	}
		 
		 	$vote_opt = get_option(WPVC_VOTES_SETTINGS);
			if($vote_opt){
				$category_options = get_term_meta($show_cont_args['id']);
				$align_category = array();
				$imgcontest = get_term_meta($show_cont_args['id'],'imgcontest',true);
				$musicfileenable = get_term_meta($show_cont_args['id'],'musicfileenable',true);
				if(is_array($category_options)){
					foreach($category_options as $key=>$val){
						if($key=='contest_rules'){
							$align_category[$key] = format_to_edit($val[0],TRUE);
						}else
							$align_category[$key] = maybe_unserialize($val[0]);
					}
				}

				$title = $vote_opt['common']['title'] ? $vote_opt['common']['title'] : NULL;
				$orderby = $vote_opt['common']['orderby'] ? $vote_opt['common']['orderby'] : 'votes_count';
				$order = $vote_opt['common']['order'] ? $vote_opt['common']['order'] : 'desc';
				$onlyloggedinuser = ($vote_opt['contest']['onlyloggedinuser']=='on') ? 1 : 0;
				$onlyloggedsubmit = ($vote_opt['contest']['vote_onlyloggedcansubmit']=='on') ? 1 : 0;
				$pagination_option =  $vote_opt['pagination']['contestant_per_page']; 
				$openform = $vote_opt['common']['vote_entry_form'];

				if($show_cont_args['orderby']=="")
				$show_cont_args['orderby'] = $orderby;
				if($show_cont_args['order']=="")
				$show_cont_args['order'] = $order;

				$votes_start_time = $align_category['votes_starttime'];
				$votes_expiration = $align_category['votes_expiration'];
				$tax_hide_photos_live = ($align_category['tax_hide_photos_live']!='')?$align_category['tax_hide_photos_live']:'off';

				$show_form = 0;
				if($tax_hide_photos_live == 'on'){
					//Until live check with current time
					$current_time = current_time( 'timestamp', 0 );
					if(($votes_start_time !='' && strtotime($votes_start_time) > $current_time)){
						$category_thumb = 0 ;
					}
					else{
						$category_thumb = 1;
					}
				}elseif(($votes_start_time != '' && strtotime($votes_start_time) > $current_time) || $votes_start_time==''){
					$category_thumb = 1;
					$show_form = 1;
				}
				else{
					$category_thumb = 1;
				}

				$show_form =($show_cont_args['displayform'] !=null)?$show_cont_args['displayform']:$show_form;

				$category_term_disp = ($align_category['termdisplay']=='on')?1:0;

				$skip_payment_array = array('ow_category_paypal_settings','payment_paypal_entry_amount','ow_category_stripe_settings','payment_stripe_entry_amount','ow_category_paystack_settings','payment_paystack_entry_amount','category_coupon_settings');
				$align_category = array_diff_key($align_category, array_flip($skip_payment_array));

				$sort_by = isset($show_cont_args['orderby'])?0:1;
				$search = isset($show_cont_args['search'])?1:0;

				
				$pagination_count = ($show_cont_args['postperpage']!='')?$show_cont_args['postperpage']:$vote_opt['pagination']['contestant_per_page']; 
				$pagin_response = SELF::wpvc_get_total_post_count($show_cont_args['id'],$pagination_count);
				if($show_cont_args['orderby']=='votes'){
					$show_cont_args['orderby']='votes_count';
					$show_cont_args['order']=strtolower($show_cont_args['order']);
				}

				if($show_cont_args['order']!='')
					$show_cont_args['order']=strtolower($show_cont_args['order']);
				//Check with settings and update same
				$show_cont_args = wp_parse_args($show_cont_args, array(
					'title' 	 => $title,
					'orderby'	 => $orderby,
					'order'		 => strtolower($order),
					'postperpage' => $pagination_option,
					'taxonomy'	  => WPVC_VOTES_TAXONOMY,
					'id'		 => 0,
					'paged'		 => 1,
					'ajaxcontent' => 0,				
					'showtimer'	 => 1,
					'displayform' 	 => $show_form,
					//Newly added
					'openform'=>$openform,
					'showgallery'=>0,
					'showrules'=>0,
					'showtop'=>0,
					'showprofile'=>0,
					'showcontestants'=>0,

					'forcedisplay' => 1,
					'thumb' 		=> $category_thumb,
					'termdisplay' 	=> $category_term_disp,
					'pagination'	=> 1,
					'sort_by'		=> $sort_by,
					'onlyloggedinuser' => $onlyloggedinuser,
					'onlyloggedsubmit'=>$onlyloggedsubmit,
					'tax_hide_photos_live' => $tax_hide_photos_live,
					'navbar'	 	=> 1,
					'contest_type'	=> $align_category['imgcontest'],
					'view' => 'grid',
					'search' => $search,
					'start_time' => $votes_start_time,
					'end_time'=> $votes_expiration,
					
					//For react
					'user_logged'=>is_user_logged_in(),
					'user_id_profile'=>get_current_user_id(),
					'user_can_register'=>get_option('users_can_register'),
					'allcatoption'=> apply_filters('cat_extension_update_values',$align_category,$show_cont_args['id']),
					"pagin_response"=>$pagin_response,
					'contest_url'=>$_COOKIE['wpvc_contestant_URL'],
					'override_view'=>$_GET['view'],
					'twitter_auth' => Wpvc_Shortcode_Model::wpvc_twitter_login_url(),
					'extension_values' => apply_filters('extension_values_hook',array())
				));
			}
		 	return $show_cont_args;
	 }


	 	//For Addcontest
	 	public static function wpvc_get_category_options_and_values_addcontest($term_id=NULL){

			$vote_opt = get_option(WPVC_VOTES_SETTINGS);
			if($vote_opt){				
				$align_category = array();
				$show_form = 1;
				if($term_id != NULL){
					$category_options = get_term_meta($term_id);
					$imgcontest = get_term_meta($term_id,'imgcontest',true);
					$musicfileenable = get_term_meta($term_id,'musicfileenable',true);
					if(is_array($category_options)){
						foreach($category_options as $key=>$val){
							if($key=='contest_rules'){
								$align_category[$key] = format_to_edit($val[0],TRUE);
							}else
								$align_category[$key] = maybe_unserialize($val[0]);
						}
					}
					$pagin_response = SELF::wpvc_get_total_post_count($term_id,$pagination_count);

					$votes_start_time = $align_category['votes_starttime'];
					$votes_expiration = $align_category['votes_expiration'];
					$tax_hide_photos_live = ($align_category['tax_hide_photos_live']!='')?$align_category['tax_hide_photos_live']:'off';
					$show_form = 0;
					$skip_payment_array = array('ow_category_paypal_settings','payment_paypal_entry_amount','ow_category_stripe_settings','payment_stripe_entry_amount','ow_category_paystack_settings','payment_paystack_entry_amount','category_coupon_settings');
					$align_category = array_diff_key($align_category, array_flip($skip_payment_array));
				}

				$title = $vote_opt['common']['title'] ? $vote_opt['common']['title'] : NULL;
				$onlyloggedinuser = ($vote_opt['contest']['onlyloggedinuser']=='on') ? 1 : 0;
				$onlyloggedsubmit = ($vote_opt['contest']['vote_onlyloggedcansubmit']=='on') ? 1 : 0;
				$pagination_option =  $vote_opt['pagination']['contestant_per_page']; 
				$openform = $vote_opt['common']['vote_entry_form'];
					
				//Check with settings and update same 
				$show_cont_args = wp_parse_args($show_cont_args, array(
					'title' 	 => $title,
					'taxonomy'	  => WPVC_VOTES_TAXONOMY,
					'id'		 => $term_id,		
					'showtimer'	 => 1,
					'displayform' 	 => $show_form,
					//Newly added
					'openform'=>$openform,
					'showgallery'=>0,
					'showrules'=>0,
					'showtop'=>0,
					'showprofile'=>0,
					'showcontestants'=>0,
					'allcategory'=>1,

					'forcedisplay' => 1,
					'onlyloggedinuser' => $onlyloggedinuser,
					'onlyloggedsubmit'=>$onlyloggedsubmit,
					'navbar'	 	=> 1,
					'contest_type'	=> $align_category['imgcontest'],
					'view' => 'grid',
					'start_time' => $votes_start_time,
					'end_time'=> $votes_expiration,
					
					//For react
					'user_logged'=>is_user_logged_in(),
					'user_id_profile'=>get_current_user_id(),
					'user_can_register'=>get_option('users_can_register'),
					'allcatoption'=> apply_filters('cat_extension_update_values',$align_category,$show_cont_args['id']),
					"pagin_response"=>$pagin_response,
					'override_view'=>$_GET['view'],
					'twitter_auth' => Wpvc_Shortcode_Model::wpvc_twitter_login_url(),
					'extension_values' => apply_filters('extension_values_hook',array())
				));
			}
			return $show_cont_args;
 		}
		 
		//Show all contestants
		 public static function wpvc_get_category_options_and_values_showallcontestants($show_cont_args,$term_id=NULL){
			
			$vote_opt = get_option(WPVC_VOTES_SETTINGS);
			if($vote_opt){

				$title = $vote_opt['common']['title'] ? $vote_opt['common']['title'] : NULL;
				$orderby = $vote_opt['common']['orderby'] ? $vote_opt['common']['orderby'] : 'votes_count';
				$order = $vote_opt['common']['order'] ? $vote_opt['common']['order'] : 'desc';
				$pagination_option =  $vote_opt['pagination']['contestant_per_page']; 
				$onlyloggedinuser = ($vote_opt['contest']['onlyloggedinuser']=='on') ? 1 : 0;
				$onlyloggedsubmit = ($vote_opt['contest']['vote_onlyloggedcansubmit']=='on') ? 1 : 0;

				$pagination_count = ($show_cont_args['postperpage']!='')?$show_cont_args['postperpage']:$vote_opt['pagination']['contestant_per_page']; 
				$pagin_response = SELF::wpvc_get_total_post_count($term_id,$pagination_count);

				if($show_cont_args['orderby']=="")
				$show_cont_args['orderby'] = $orderby;
				if($show_cont_args['order']=="")
				$show_cont_args['order'] = $order;

				if($show_cont_args['orderby']=='votes'){
					$show_cont_args['orderby']='votes_count';
					$show_cont_args['order']=strtolower($show_cont_args['order']);
				}

				session_start();
				// If session has started, this data will be stored.
				$_SESSION['wpvc_contestant_seed'] = 0;
				session_write_close();
				
				if($show_cont_args['order']!='')
					$show_cont_args['order']=strtolower($show_cont_args['order']);			
				//Check with settings and update same
				$show_cont_args = wp_parse_args($show_cont_args, array(
					'title' 	 => $title,
					'orderby'	 => $orderby,
					'order'		 => strtolower($order),
					'postperpage' => $pagination_option,
					'taxonomy'	  => WPVC_VOTES_TAXONOMY,
					'id'		 => 0,
					'paged'		 => 1,
					'pagination'	=> 1,
					'view' => 'grid',
					'search_text'=>'',
					"pagin_response"=>$pagin_response,
					'onlyloggedinuser' => $onlyloggedinuser,
					'onlyloggedsubmit'=>$onlyloggedsubmit,
					'user_can_register'=>get_option('users_can_register'),
					'user_logged'=>is_user_logged_in(),
					'twitter_auth' => Wpvc_Shortcode_Model::wpvc_twitter_login_url(),
					'extension_values' => apply_filters('extension_values_hook',array())
				));
			}
						
			return $show_cont_args;
	 	}
		
		public static function wpvc_twitter_login_url(){			
			
			$votes_settings = get_option(WPVC_VOTES_SETTINGS);
			if($votes_settings['share']['twitter_login'] == 'on'){
				include_once WPVC_CONTROLLER_PATH.'wpvc_twitter_controller.php';
				$connection = new Wpvc_Twitter_Controller($votes_settings['share']['vote_tw_appid'] , $votes_settings['share']['vote_tw_secret'] );
				$request_token = $connection->getRequestToken(site_url());
				
				//received token info from twitter
				$twitter_auth['token'] = $request_token['oauth_token'];
				$twitter_auth['token_secret'] = $request_token['oauth_token_secret'];
				$twitter_auth['twitter_url'] = '';
				if($connection->http_code=='200'){
					$twitter_auth['twitter_url'] = $connection->getAuthorizeURL($request_token['oauth_token']);			   
				}
			}else{
				$twitter_auth = null;
			}
			return $twitter_auth;
		}
	}
}else
die("<h2>".__('Failed to load Voting Shortcode model')."</h2>");

return new Wpvc_Shortcode_Model();
?>