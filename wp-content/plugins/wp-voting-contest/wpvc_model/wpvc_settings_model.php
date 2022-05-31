<?php
if(!class_exists('Wpvc_Settings_Model')){
	class Wpvc_Settings_Model {

		public static function wpvc_settings_page_json(){
            global $wpdb;

            $image_drop  = Wpvc_Settings_Model::wpvc_image_settings();
            $sidebar  = Wpvc_Settings_Model::wpvc_sidebar_setting();
            $order_by = Wpvc_Settings_Model::wpvc_order_by_drop();
            $track = Wpvc_Settings_Model::wpvc_vote_tracking();
            $vote_freq = Wpvc_Settings_Model::wpvc_vote_frequency();
            $page_navi = Wpvc_Settings_Model::wpvc_pagi_navi();
            $options = Wpvc_Settings_Model::wpvc_setting_values();
            $custom_fields = Wpvc_Settings_Model::wpvc_custom_fields_json();
            $return_val = array(
                'image_drop'=>$image_drop,'sidebar_d'=>$sidebar,'order_d'=>$order_by,'track'=>$track,'freq'=>$vote_freq, 'pagin'=>$page_navi,
                'settings'=>$options,'admin_custom_fields'=>$custom_fields
            );
            return $return_val;
        }
        
        public static function wpvc_setting_values(){
            global $wpdb;
            $options = get_option(WPVC_VOTES_SETTINGS);
            return $options;
        }

        public static function wpvc_sidebar_setting(){
            $sidebars = array();
            $registered_bar = $GLOBALS['wp_registered_sidebars'];
            if(!empty($registered_bar)){
                foreach ( $registered_bar as $sid ) {
                    $sidebars[$sid['id']] = $sid['name'];
                }
            }
            return $sidebars;
            
        }
        public static function wpvc_image_settings(){
            global $_wp_additional_image_sizes;
            $sizes = array();
            foreach( get_intermediate_image_sizes() as $s ){
                $sizes[ $s ] = array( 0, 0 );
                if( in_array( $s, array( 'thumbnail', 'medium', 'large' ) ) ){
                    $sizes[ $s ][0] = get_option( $s . '_size_w' );
                    $sizes[ $s ][1] = get_option( $s . '_size_h' );
                }else{
                    if( isset( $_wp_additional_image_sizes ) && isset( $_wp_additional_image_sizes[ $s ] ) )
                        $sizes[ $s ] = array( $_wp_additional_image_sizes[ $s ]['width'], $_wp_additional_image_sizes[ $s ]['height'], );
                }
            }
            $all_sizes = array();
            foreach( $sizes as $size => $atts ){
                $all_sizes[$size] = $size . ' - ' .implode( 'x', $atts );
            }
            return $all_sizes;
        }

        public static function wpvc_order_by_drop(){
            $order_by = array('author'=>'Author','date'=>'Date','title'=>'Title','modified'=>'Modified','menu_order'=>'Menu Order',
            'parent'=>'Parent','id'=>'ID','votes'=>'Votes','rand'=>'Random');
            return $order_by;
        }
        
        public static function wpvc_vote_tracking(){
            $track = array(
                'ip_traced'		=> 'IP Traced',
                'cookie_traced'	=> 'Cookie Traced',
                'email_verify' 	=> 'Email Verification',
                'ip_email' 		=> 'IP + Email Verification'
            );
            return $track;
        }

        public static function wpvc_vote_frequency(){
            $freq = array(
                '0'		=> 'No Limit',
                '2'	=> 'Every _____ Hours',
                '1' 	=> 'per Calendar Day',
                '11' 		=> 'per Category'
            );
            return $freq;
        }

        public static function wpvc_pagi_navi(){
            $pagin = array(
                '1' => 'Normal',
                '2'	=> 'Drop-down List',
                '3' => 'Loadmore Option',
                '4' => 'Infinite Scroll'
            );
            return $pagin;
        }

        /************************** Custom Fields  ***************************/
        public static function wpvc_custom_fields_get_original($custom_id){
            global $wpdb;
            $sql = "SELECT * FROM " .WPVC_VOTES_ENTRY_CUSTOM_TABLE." WHERE delete_time = 0 && id = ".$custom_id." order by id DESC";
            $custom_fields = $wpdb->get_row($sql,ARRAY_A);
            return $custom_fields;
        }

        public static function wpvc_custom_fields_json(){
            global $wpdb;
            $video_extension = get_option('_ow_video_extension');
            $where ='';
            if($video_extension !=1){
                $where = ' AND system_name!="contestant-ow_video_upload_url" ';
            }
            $sql = "SELECT * FROM " .WPVC_VOTES_ENTRY_CUSTOM_TABLE." WHERE delete_time = 0 ".$where."order by id DESC";
            $custom_fields = $wpdb->get_results($sql,ARRAY_A);
            $ret_data = array();
            if(count($custom_fields) > 0){
                foreach($custom_fields as $custom){
                    $react_val = json_decode($custom['react_val']);
                    unset($custom['react_val']);
                    $custom['react_val'] = $react_val;
                    $ret_data[] = $custom;
                }
            }           
            return $ret_data;
        }

        public static function wpvc_video_extension_field_change($values){
            global $wpdb;
            $sql = "SELECT * FROM " .WPVC_VOTES_ENTRY_CUSTOM_TABLE." WHERE delete_time = 0 AND system_name='contestant-ow_video_upload_url'".$where."order by id DESC";
            $custom_fields = $wpdb->get_row($sql,ARRAY_A);
            $ret_data = array();
            $create_val = array();
            if(count($custom_fields) > 0){
                foreach($values as $val){
                    if($val['system_name'] == 'contestant-ow_video_url'){
                        $create_val['id']=$custom_fields['id'];
                        $create_val['question_type']=$custom_fields['question_type'];
                        $create_val['question']=$custom_fields['question'];
                        $create_val['system_name']=$custom_fields['system_name'];

                        //decode react values
                        $react_val = json_decode($custom_fields['react_val']);
                        unset($custom_fields['react_val']);
                        $custom_fields['react_val'] = $react_val;

                        $create_val['original']=$custom_fields;
                        
                        $ret_data[] = $create_val;
                    }else{
                        $ret_data[] = $val;
                    }
                    
                }
            }
            return $ret_data;
        }

        public static function wpvc_insert_contestant_custom_field($input_data){
            global $wpdb,$current_user;

            $data = array('text_field_type'=>$input_data['text_field_type'],'text_area_row'=>$input_data['text_area_row'],'value_placement'=>$input_data['value_placement'],'upload_icon'=>$input_data['upload_icon'],'datepicker_only'=>$input_data['datepicker_only']);
            $other_react_values = json_encode($data);
            $input_data['system_name'] = uniqid();
            $input_data['sequence'] = 0;
            $input_data['current_id']=$current_user->ID;
            $input_data['react_val']=$other_react_values;

			$insert = $wpdb->query("INSERT INTO ".WPVC_VOTES_ENTRY_CUSTOM_TABLE." (question_type, question, system_name, response, required, admin_only,required_text, sequence,wp_user,admin_view,pretty_view,ow_file_size,grid_only,list_only,show_labels,show_labels_single,set_limit,limit_count,description,react_val)"
					 . " VALUES ('" . $input_data['form_type'] . "', '" . $input_data['field_name'] . "', '" . $input_data['system_name'] . "', '"  . $input_data['values'] . "', '" . $input_data['required'] . "', '" . $input_data['show_form'] . "', '" . $input_data['required_text'] . "', '" . $input_data['sequence'] . "','".$input_data['current_id']. "','".$input_data['show_val_single']."','".$input_data['show_val_modal']."','".$input_data['ow_file_size']."','".$input_data['show_val_grid']."','".$input_data['show_val_list']."','".$input_data['show_label_both']."','".$input_data['show_label_single']."','".$input_data['set_word_limit']."','".$input_data['set_word_limit_chars']."','" . $input_data['field_description'] ."','" . $input_data['react_val'] . "')");
			return $insert;
        }
        
        public static function wpvc_update_contestant_custom_field($custom_fields_id,$input_data){
            global $wpdb,$current_user;     
            
            $data = array('text_field_type'=>$input_data['react_val']['text_field_type'],'text_area_row'=>$input_data['react_val']['text_area_row'],'value_placement'=>$input_data['react_val']['value_placement'],'upload_icon'=>$input_data['react_val']['upload_icon'],'datepicker_only'=>$input_data['react_val']['datepicker_only']);
            $other_react_values = json_encode($data);
            $input_data['sequence'] = 0;
            $input_data['current_id']=$current_user->ID;
            $input_data['react_val']=$other_react_values;

            $update = $wpdb->query("UPDATE " . WPVC_VOTES_ENTRY_CUSTOM_TABLE . " SET question_type = '" . $input_data['question_type'] . "', question = '" . $input_data['question'] . "',description = '" . $input_data['description'] . "', response = '" . $input_data['response'] . "', required = '" . $input_data['required'] . "',admin_only = '" . $input_data['admin_only'] . "', required_text = '" . $input_data['required_text'] . "',pretty_view = '" . $input_data['pretty_view']  . "', ow_file_size = '" . $input_data['ow_file_size'] . "', sequence = '" . $input_data['sequence'] . "',admin_view = '" . $input_data['admin_view'] . "',grid_only = '" . $input_data['grid_only'] . "',list_only = '" . $input_data['list_only'] . "',show_labels = '" . $input_data['show_labels']. "',show_labels_single = '" . $input_data['show_labels_single'] . "',set_limit = '" . $input_data['set_word_limit'] . "',limit_count = '" . $input_data['set_word_limit_chars'] . "',react_val = '" . $input_data['react_val'] . "' WHERE id = '" . $custom_fields_id . "'");
			return $update;
        }

        public static function wpvc_delete_contestant_custom_field($custom_fields_id)
		{
            global $wpdb;
            if(is_array($custom_fields_id)){
                $delete_val = strtotime("now");
                $update = $wpdb->query("UPDATE " . WPVC_VOTES_ENTRY_CUSTOM_TABLE . " SET delete_time = '" . $delete_val . "' WHERE id = '" .$custom_fields_id[0]. "'");
                return $update;
            }else{
                return 0;
            }
        }

        public static function wpvc_get_total_voting_count(){
            global $wpdb;
            $sql = "SELECT log.*,pst.post_title,pst.post_author,user.display_name FROM " . WPVC_VOTES_TBL ." as log LEFT JOIN ".$wpdb->prefix."posts as pst on log.post_id=pst.ID LEFT JOIN ".$wpdb->prefix."users as user on pst.post_author=user.ID ORDER BY id DESC";
            $result = $wpdb->get_results($sql,ARRAY_A);
            $total_rows = $wpdb->num_rows;    
            return $total_rows;
        }

        public static function wpvc_votings_get_paged($paged){
            global $wpdb;
            if($paged==0){
                $offset = 0;
            }else{
                $offset = $paged * 20;
            }
            $sql = "SELECT log.*,pst.post_title,pst.post_author,user.display_name FROM " . WPVC_VOTES_TBL ." as log LEFT JOIN ".$wpdb->prefix."posts as pst on log.post_id=pst.ID LEFT JOIN ".$wpdb->prefix."users as user on pst.post_author=user.ID ORDER BY id DESC LIMIT ".$offset.",20";
            $votinglogs = $wpdb->get_results($sql,ARRAY_A);          
            return $votinglogs;
        }

        public static function wpvc_votings_get(){
            global $wpdb;
            $sql = "SELECT log.*,pst.post_title,pst.post_author,user.display_name FROM " . WPVC_VOTES_TBL ." as log LEFT JOIN ".$wpdb->prefix."posts as pst on log.post_id=pst.ID LEFT JOIN ".$wpdb->prefix."users as user on pst.post_author=user.ID ORDER BY id DESC";
            $votinglogs = $wpdb->get_results($sql,ARRAY_A);          
            return $votinglogs;
        }

        public static function wpvc_delete_contestant_voting($vote_id){
			global $wpdb;
			//Get the Count of Votes
            if(!empty($vote_id)){
                $sql = "SELECT * FROM " .WPVC_VOTES_TBL." WHERE id = '".$vote_id."'";
                $votes = $wpdb->get_row($sql,ARRAY_A);
                if(is_array($votes)){
                    $post_id = $votes['post_id'];
                    $no_votes = $wpdb->get_var( "SELECT votes FROM ".WPVC_VOTES_TBL." WHERE id = ".$vote_id);
                    $wpdb->delete( WPVC_VOTES_TBL, array( 'id' => $vote_id ), array( '%d' )  );
                    $vote_count = get_post_meta( $post_id, WPVC_VOTES_CUSTOMFIELD, true );
                    if($vote_count != 0){
                        update_post_meta($post_id, WPVC_VOTES_CUSTOMFIELD, $vote_count - $no_votes, $vote_count);
                    }
                }            
            }
		}

        public static function wpvc_multiple_delete_contestant_voting($vote_id){
			global $wpdb;
			//Get the Count of Votes
            if(!empty($vote_id)){
                foreach($vote_id as $vid){
                    $sql = "SELECT * FROM " .WPVC_VOTES_TBL." WHERE id = '".$vid."'";
                    $votes = $wpdb->get_row($sql,ARRAY_A);
                    if(is_array($votes)){
                        $post_id = $votes['post_id'];
                        $no_votes = $wpdb->get_var( "SELECT votes FROM ".WPVC_VOTES_TBL." WHERE id = ".$vid);
                        $wpdb->delete( WPVC_VOTES_TBL, array( 'id' => $vid ), array( '%d' )  );
                        $vote_count = get_post_meta( $post_id, WPVC_VOTES_CUSTOMFIELD, true );
                        if($vote_count != 0){
                            update_post_meta($post_id, WPVC_VOTES_CUSTOMFIELD, $vote_count - $no_votes, $vote_count);
                        }
                    }     
                }       
            }
		}

        public static function wpvc_category_get_allTerms($term_meta){
            $terms = get_terms( array(
                'taxonomy' => WPVC_VOTES_TAXONOMY,
                'hide_empty' => false,
            ));
            unset($term_meta['category_name']); 
            unset($term_meta['category_name_error']);
            unset($term_meta['datepicker_only']);
            $updated_terms = array();
            foreach($terms as $key => $term){
                $updated_terms[$key]['term_id'] =   $term->term_id;
                $updated_terms[$key]['category_name'] =   $term->name;
                $updated_terms[$key]['slug'] =   $term->slug;
                $updated_terms[$key]['count'] =   $term->count;
                $setting = Wpvc_Settings_Model::wpvc_settings_page_json();
                foreach($term_meta as $innerKey => $value){
                    if($innerKey == 'color'){
                        $color = get_term_meta($term->term_id,'color',true);
                        if($color == null){
                            if(is_array($setting))    
                            $color = $setting['settings']['color'];    
                        }
                        $updated_terms[$key]['color'] =   $color;                          
                    }
                    else if($innerKey == 'style'){         
                        $style = get_term_meta($term->term_id,'style',true);               
                        if($style == null){ 
                            if(is_array($setting)) 
                                $style = $setting['settings']['style'];  
                        }                
                        $updated_terms[$key]['style'] =   $style;                          
                    }
                    else{
                        $updated_terms[$key][$innerKey] =   get_term_meta($term->term_id,$innerKey,true);
                    }                  
                }
                
            }
            return $updated_terms;              
        }
        
        public static function wpvc_category_insert($result){
            $term_name = $result['category_name']; 
            unset($result['category_name']); 
            unset($result['category_name_error']);
            unset($result['datepicker_only']);
            $inserted_term = wp_insert_term($term_name,WPVC_VOTES_TAXONOMY);            
            if ( !is_wp_error( $inserted_term ) ) {               
                $term_id = $inserted_term['term_id'];
                //Update each elements to term meta
                foreach($result as $key => $value){
                    if($key=='contest_rules'){
                        $values = format_to_edit($value,TRUE);
                        update_term_meta($term_id, $key, $values);
                    }else{
                        update_term_meta($term_id, $key, $value);
                    }
                }
            }
            else{              
                return 0;
            }
        }

        public static function wpvc_category_edit($result,$term_id){
            $term_name = $result['category_name']; 
            $slug = $result['slug']; 
            unset($result['category_name']); 
            unset($result['category_name_error']);
            unset($result['category_slug_error']);
            unset($result['datepicker_only']);
            $get_category = get_term_by('slug', $slug, 'contest_category'); 
            $slug_id = $get_category->term_id;
            if($term_id != $slug_id){
                return 1;
            }
            $update = wp_update_term( $term_id, WPVC_VOTES_TAXONOMY, array(
                'name' => $term_name,
                'slug' => $slug,
            ) );
           
            if ( ! is_wp_error( $update ) ) {
                //Update each elements to term meta
                foreach($result as $key => $value){
                    if($key=='contest_rules'){
                        $values = format_to_edit($value,TRUE);
                        update_term_meta($term_id, $key, $values);
                    }else{
                        update_term_meta($term_id, $key, $value);
                    }
                }
            }
            else{
                return 0;
            }

        }
        
        /************************** Custom Reg Fields  ***************************/
        public static function wpvc_user_custom_fields_get_original($custom_id){
            global $wpdb;
            $sql = "SELECT * FROM " .WPVC_VOTES_USER_CUSTOM_TABLE." WHERE delete_time = 0 && id = ".$custom_id." order by id DESC";
            $custom_fields = $wpdb->get_row($sql,ARRAY_A);
            return $custom_fields;
        }

        public static function wpvc_reg_custom_fields_json(){
            global $wpdb;
            $sql = "SELECT * FROM " .WPVC_VOTES_USER_CUSTOM_TABLE." WHERE delete_time = 0 order by id DESC";
            $custom_fields = $wpdb->get_results($sql,ARRAY_A);
            $ret_data = array();
            if(count($custom_fields) > 0){
                foreach($custom_fields as $custom){
                    $react_val = json_decode($custom['react_val']);
                    unset($custom['react_val']);
                    $custom['react_val'] = $react_val;
                    $ret_data[] = $custom;
                }
            }           
            return $ret_data;
        }

        public static function wpvc_insert_reg_custom_field($input_data){
            global $wpdb,$current_user;

            $data = array('text_field_type'=>$input_data['text_field_type'],'text_area_row'=>$input_data['text_area_row'],'value_placement'=>$input_data['value_placement'],'datepicker_only'=>$input_data['datepicker_only']);
            $other_react_values = json_encode($data);
            $input_data['system_name'] = uniqid();
            $input_data['sequence'] = 0;
            $input_data['current_id']=$current_user->ID;
            $input_data['react_val']=$other_react_values;

			$insert = $wpdb->query("INSERT INTO ".WPVC_VOTES_USER_CUSTOM_TABLE." (sequence,question_type, question, system_name, response, required,required_text, admin_only,react_val,wp_user)"
					 . " VALUES ('" . $input_data['sequence'] . "', '" . $input_data['form_type'] . "', '" . $input_data['field_name'] . "', '" . $input_data['system_name'] . "', '"  . $input_data['values'] . "', '" . $input_data['required'] . "', '" . $input_data['required_text'] . "', '" . $input_data['show_form'] . "', '" . $input_data['react_val'] . "','".$input_data['current_id']."')");
			return $insert;
        }
        
        public static function wpvc_update_reg_custom_field($custom_fields_id,$input_data){
            global $wpdb,$current_user;     
            
            $data = array('text_field_type'=>$input_data['react_val']['text_field_type'],'text_area_row'=>$input_data['react_val']['text_area_row'],'value_placement'=>$input_data['react_val']['value_placement'],'datepicker_only'=>$input_data['react_val']['datepicker_only']);
            $other_react_values = json_encode($data);
            $input_data['sequence'] = 0;
            $input_data['current_id']=$current_user->ID;
            $input_data['react_val']=$other_react_values;

            $update = $wpdb->query("UPDATE " . WPVC_VOTES_USER_CUSTOM_TABLE . " SET question_type = '" . $input_data['question_type'] . "', question = '" . $input_data['question'] . "', response = '" . $input_data['response'] . "', required = '" . $input_data['required'] . "',admin_only = '" . $input_data['admin_only'] . "', required_text = '" . $input_data['required_text'] . "', sequence = '" . $input_data['sequence'] . "',react_val = '" . $input_data['react_val'] . "' WHERE id = '" . $custom_fields_id . "'");
			return $update;
        }

        public static function wpvc_delete_reg_custom_field($custom_fields_id)
		{
            global $wpdb;
            if(is_array($custom_fields_id)){
                $delete_val = strtotime("now");
                $update = $wpdb->query("UPDATE " . WPVC_VOTES_USER_CUSTOM_TABLE . " SET delete_time = '" . $delete_val . "' WHERE id = '" .$custom_fields_id[0]. "'");
                return $update;
            }else{
                return 0;
            }
        }
        
        
        public static function wpvc_languages_list(){
            
            require_once ABSPATH . 'wp-admin/includes/translation-install.php';
            $translations = wp_get_available_translations();

            $default = file_get_contents( WPVC_VIEWS. 'locales/en/translation.json');
            foreach ($translations as $l) {
                $settings['languages_list'][$l['language']] = $l['native_name'] ;

                $file = WPVC_VIEWS. 'locales/'. $l['language'] .'.json';
                if(!file_exists($file)){
                    file_put_contents($file, $default);
                }
                
            }
            
            require_once(ABSPATH.'wp-admin/includes/file.php');

            $files = list_files(WPVC_VIEWS.'locales', 0);

            foreach ($files as $file) {
                $settings['available'][basename($file, ".json")] = $file;
            }

            return $settings;
        }

        public static function wpvc_translations_create($lang){
            
            $default = file_get_contents( WPVC_VIEWS. 'locales/en/translation.json');

            $file = WPVC_VIEWS. 'locales/'. $lang .'.json';
            if(!file_exists($file)){
                file_put_contents($file, $default);
            }

            $current = file_get_contents( $file );
            
            $settings = array(
                'language' => $lang,
                'default_values' => json_decode($default, true),
                'current_values' => json_decode($current, true)
            );
            
            return $settings;
        }

        public static function wpvc_translations_save($input_data){
            
            $default = file_get_contents( WPVC_VIEWS. 'locales/en/translation.json');

            $file = WPVC_VIEWS. 'locales/'. $input_data['language'] .'.json';
            
            file_put_contents($file, json_encode($input_data['current_values']));
            
            $current = file_get_contents( $file );

            $settings = array(
                'language' => $input_data['language'],
                'default_values' => json_decode($default, true),
                'current_values' => json_decode($current, true)
            );

            return $settings;
        }

        public static function wpvc_get_translations(){
            
            $default = file_get_contents( WPVC_VIEWS. 'locales/en/translation.json');

            $lang = get_locale();
            $file = WPVC_VIEWS. 'locales/'. $lang .'.json';
            if(!file_exists($file)){
                file_put_contents($file, $default);
            }

            $current = file_get_contents( $file );
            if($current == ''){
                $current = $default;
            }
            $settings = array(
                'language' => $lang,
                'translations' => json_decode($current, true)
            );
            
            return $settings;
        }

        public static function wpvc_vote_clear_category($term_id){
            global $wpdb;
            $where = '';
            
            if(isset($term_id) && $term_id > 0){
                   $where .= ' WHERE  `post_id` IN (SELECT DISTINCT `object_id` FROM `'.$wpdb->prefix.'term_relationships` INNER JOIN `'.$wpdb->prefix.'terms` as terms ON `term_id` = '.$term_id.' AND `term_taxonomy_id` = terms.term_id )';
            }
                
            $cnt = 'SELECT  `post_id` , count(*) as cnt FROM ' . WPVC_VOTES_TBL .$where.' GROUP BY `post_id`';
            $delquery = 'DELETE FROM ' . WPVC_VOTES_TBL .$where;
            $cntresult = $wpdb->get_results($cnt);
            $result = $wpdb->query($delquery);
        
            foreach ($cntresult as $indpost) {
               /*$exvote = get_post_meta($indpost->post_id, WPVC_VOTES_CUSTOMFIELD);
               if ($exvote[0] > $indpost->cnt)
                    update_post_meta($indpost->post_id, WPVC_VOTES_CUSTOMFIELD, ($exvote[0] - $indpost->cnt));
                else*/
                    update_post_meta($indpost->post_id, WPVC_VOTES_CUSTOMFIELD, 0);
            }
            
            return $cntresult;	
        }

        public static function wpvc_contestant_bulk_pending($exploded_ids){
			global $wpdb;
			 //Get the Status Changing Contestants
            $query = "SELECT ID FROM $wpdb->posts WHERE ID IN ({$exploded_ids}) AND post_status = 'pending'";
            $result_ids = $wpdb->get_results($query,'ARRAY_A');
			return $result_ids;
		}
	}
}else
die("<h2>".__('Failed to load Voting Settings model')."</h2>");

return new Wpvc_Settings_Model();
?>