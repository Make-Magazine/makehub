<?php
if(!class_exists('Wpvc_Export_Model')){
	class Wpvc_Export_Model {

		public static function wpvc_render_export($objPHPExcel,$custom_fields,$terms=NULL,$term_id=NULL){
            $rowCount = 3;

            if(is_array($terms)){
                foreach($terms as $term_id){
                    $post_entries = SELF::wpvc_voting_export_contestants($term_id);
                    $imgcontest = get_term_meta($term_id,'imgcontest',true);
                    if(!empty($post_entries)){
                        $string_colmn='A';
                        foreach($post_entries as $pos_val){
                            $rowCount++;
							$posted_date = date('Y-m-d',strtotime($pos_val->post_date));
                            $user_author = SELF::wpvc_voting_get_author_contestant($pos_val);
                            $category = SELF::wpvc_voting_get_contest_name($pos_val);
                            $post_title = $pos_val->post_title;
                            $post_content = preg_replace('/[\n\r]+/',' ',trim($pos_val->post_content));
                            $post_status = $pos_val->post_status;
                            $cat_name = $category->name;
                            $votes_count_val = get_post_meta($pos_val->ID, WPVC_VOTES_CUSTOMFIELD, true);
                            $get_post_metas = get_post_meta($pos_val->ID, WPVC_VOTES_POST,TRUE);
                            $votes_count_val = ($votes_count_val != '')?$votes_count_val:0;
                            $image_url = get_the_post_thumbnail_url($pos_val->ID,'full');
                            $image_url = ($image_url != '')?$image_url:'';

                            $objPHPExcel->getActiveSheet()->SetCellValue($string_colmn.$rowCount, $post_title);
                            $objPHPExcel->getActiveSheet()->SetCellValue(++$string_colmn.$rowCount, $post_status);
                            $objPHPExcel->getActiveSheet()->SetCellValue(++$string_colmn.$rowCount, $cat_name);
                            $objPHPExcel->getActiveSheet()->SetCellValue(++$string_colmn.$rowCount, $votes_count_val);
                            $objPHPExcel->getActiveSheet()->SetCellValue(++$string_colmn.$rowCount, $posted_date);
                            $objPHPExcel->getActiveSheet()->SetCellValue(++$string_colmn.$rowCount, $image_url);
                            $objPHPExcel->getActiveSheet()->SetCellValue(++$string_colmn.$rowCount,$user_author->user_email);
                            $objPHPExcel->getActiveSheet()->SetCellValue(++$string_colmn.$rowCount,$user_author->display_name);

                            if(!empty($custom_fields)){
                                foreach($custom_fields as $ques_val){
                                    $system_name = $ques_val['system_name'];
                                    if($system_name != 'contestant-title' && $system_name != 'contestant-image'){

                                    
                                        if($ques_val['question_type']=='FILE'){
                                            if($system_name == 'contestant-ow_music_url'){
                                                $music_url = get_post_meta($pos_val->ID, '_ow_music_upload_url', TRUE);
                                                if($imgcontest=='music'){
                                                    $music_url = ($music_url !='')?$music_url:'';
                                                    $objPHPExcel->getActiveSheet()->SetCellValue(++$string_colmn.$rowCount, $music_url);
                                                }
                                            }
                                            elseif($system_name == 'contestant-ow_video_upload_url'){
                                                $video_url = get_post_meta($pos_val->ID, 'contestant-ow_video_url', TRUE);
                                                if($imgcontest=='video'){
                                                    $video_url = ($video_url !='')?$video_url:'';
                                                    $objPHPExcel->getActiveSheet()->SetCellValue(++$string_colmn.$rowCount,$video_url);
                                                }
                                            }
                                            elseif($system_name == "contestant-desc"){
                                                $post_content = ($post_content !='')?$post_content:'';
                                                $objPHPExcel->getActiveSheet()->SetCellValue(++$string_colmn.$rowCount,$post_content);
                                            }
                                            else{
                                                $post_image = get_post_meta($pos_val->ID, 'ow_custom_attachment_'.$system_name, TRUE);
                                                $values= empty($post_image)?'':$post_image['url'];
                                                $objPHPExcel->getActiveSheet()->SetCellValue(++$string_colmn.$rowCount,$values);
                                            }
                                        }else{
                                            $vaal = ($get_post_metas[$system_name]!='')?$get_post_metas[$system_name]:'';
                                            $objPHPExcel->getActiveSheet()->SetCellValue(++$string_colmn.$rowCount,$get_post_metas[$system_name]);
                                        }
                                    }
                                }

                            }

                            $string_colmn='A';

                        }
                    }
                }
            }else{
                $post_entries = SELF::wpvc_voting_export_contestants($term_id);
                $imgcontest = get_term_meta($term_id,'imgcontest',true);
                if(!empty($post_entries)){
                    $string_colmn='A';
                    foreach($post_entries as $pos_val){
                        $rowCount++;
                        $posted_date = date('Y-m-d',strtotime($pos_val->post_date));
                        $user_author = SELF::wpvc_voting_get_author_contestant($pos_val);
                        $category = SELF::wpvc_voting_get_contest_name($pos_val);
                        $post_title = $pos_val->post_title;
                        $post_content = preg_replace('/[\n\r]+/',' ',trim($pos_val->post_content));
                        $post_status = $pos_val->post_status;
                        $cat_name = $category->name;
                        $votes_count_val = get_post_meta($pos_val->ID, WPVC_VOTES_CUSTOMFIELD, true);
                        $get_post_metas = get_post_meta($pos_val->ID, WPVC_VOTES_POST,TRUE);
                        $votes_count_val = ($votes_count_val != '')?$votes_count_val:0;
                        $image_url = get_the_post_thumbnail_url($pos_val->ID,'full');
                        $image_url = ($image_url != '')?$image_url:'';

                        $objPHPExcel->getActiveSheet()->SetCellValue($string_colmn.$rowCount, $post_title);
                        $objPHPExcel->getActiveSheet()->SetCellValue(++$string_colmn.$rowCount, $post_status);
                        $objPHPExcel->getActiveSheet()->SetCellValue(++$string_colmn.$rowCount, $cat_name);
                        $objPHPExcel->getActiveSheet()->SetCellValue(++$string_colmn.$rowCount, $votes_count_val);
                        $objPHPExcel->getActiveSheet()->SetCellValue(++$string_colmn.$rowCount, $posted_date);
                        $objPHPExcel->getActiveSheet()->SetCellValue(++$string_colmn.$rowCount, $image_url);
                        $objPHPExcel->getActiveSheet()->SetCellValue(++$string_colmn.$rowCount,$user_author->user_email);
                        $objPHPExcel->getActiveSheet()->SetCellValue(++$string_colmn.$rowCount,$user_author->display_name);

                        if(!empty($custom_fields)){
                            foreach($custom_fields as $ques_val){
                                $system_name = $ques_val['system_name'];
                                if($system_name != 'contestant-title' && $system_name != 'contestant-image'){

                                
                                    if($ques_val['question_type']=='FILE'){
                                        if($system_name == 'contestant-ow_music_url'){
                                            $music_url = get_post_meta($pos_val->ID, '_ow_music_upload_url', TRUE);
                                            if($imgcontest=='music'){
                                                $music_url = ($music_url !='')?$music_url:'';
                                                $objPHPExcel->getActiveSheet()->SetCellValue(++$string_colmn.$rowCount, $music_url);
                                            }
                                        }
                                        elseif($system_name == 'contestant-ow_video_upload_url'){
                                            $video_url = get_post_meta($pos_val->ID, 'contestant-ow_video_url', TRUE);
                                            if($imgcontest=='video'){
                                                $video_url = ($video_url !='')?$video_url:'';
                                                $objPHPExcel->getActiveSheet()->SetCellValue(++$string_colmn.$rowCount,$video_url);
                                            }
                                        }
                                        elseif($system_name == "contestant-desc"){
                                            $post_content = ($post_content !='')?$post_content:'';
                                            $objPHPExcel->getActiveSheet()->SetCellValue(++$string_colmn.$rowCount,$post_content);
                                        }
                                        else{
                                            $post_image = get_post_meta($pos_val->ID, 'ow_custom_attachment_'.$system_name, TRUE);
                                            $values= empty($post_image)?'':$post_image['url'];
                                            $objPHPExcel->getActiveSheet()->SetCellValue(++$string_colmn.$rowCount,$values);
                                        }
                                    }else{
                                        $vaal = isset($get_post_metas[$system_name])?$get_post_metas[$system_name]:'';
                                        $objPHPExcel->getActiveSheet()->SetCellValue(++$string_colmn.$rowCount,$vaal);
                                    }
                                }
                            }

                        }

                        $string_colmn='A';

                    }
                }
            }
            return $objPHPExcel;
        }	
		

        public static function wpvc_voting_get_author_contestant($pos_val){
			global $wpdb;
			return $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."users where ID='".$pos_val->post_author."'");
	    }

        public static function wpvc_voting_get_contest_name($pos_val){
			global $wpdb;
			return $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."terms where term_id='".$pos_val->term_taxonomy_id."'");
	    }

        public static function wpvc_voting_export_contestants($term_id){
			global $wpdb;
			$where_con ='';
			if($term_id!='' && $term_id > 0){
				$where_con .= ' AND tt.term_id='.$term_id;
			}

			$sql1 = "SELECT * FROM ".$wpdb->prefix."posts "." as pos
			   LEFT JOIN ".$wpdb->prefix."term_relationships as relterm ON (pos.ID=relterm.object_id)
			   LEFT JOIN ".$wpdb->prefix."term_taxonomy as tt ON (relterm.term_taxonomy_id = tt.term_taxonomy_id)
			   WHERE pos.post_type = '".WPVC_VOTES_TYPE."' AND pos.post_status!='auto-draft' AND pos.post_status!='trash' ".$where_con." Group by pos.ID";

			$post_entries = $wpdb->get_results($sql1);

			return $post_entries;
	    }

        public static function wpvc_get_all_term_custom_fields($terms){
            global $wpdb;
            if(is_array($terms)){
                $custom_fields = array();
                $check_array = array();
                foreach($terms as $term_id){
                    $category_options = get_term_meta($term_id,'contest_category_assign_custom',true);
                    $catcustom_fields = maybe_unserialize($category_options);
                    if(!empty($catcustom_fields)){
                        foreach($catcustom_fields as $catfield){
                            if(!in_array($catfield['system_name'],$check_array)){
                                $check_array[] = $catfield['system_name'];
                                $custom_fields[] = $catfield['original'];
                            }
                        }                        
                    }else{
                        $imgcontest = get_term_meta($term_id,'imgcontest',true);
                        $musicfileenable = get_term_meta($term_id,'musicfileenable',true);
                        $getcustom = Wpvc_Shortcode_Model::wpvc_custom_fields_by_contest($imgcontest,$musicfileenable);
                        $create_custom = maybe_unserialize($getcustom);
                        if(!empty($create_custom)){
                            foreach($create_custom as $catfield){
                                if(!in_array($catfield['system_name'],$check_array)){
                                    $check_array[] = $catfield['system_name'];
                                    $custom_fields[] =$catfield;
                                }
                            }            
                        }
                    }
                }
                return $custom_fields;
            }else{
                $sql = "SELECT * FROM " .WPVC_VOTES_ENTRY_CUSTOM_TABLE." WHERE delete_time = 0 order by sequence";
                $custom_fields = $wpdb->get_results($sql,ARRAY_A);
                return $custom_fields;
            }           
		}

        public static function wpvc_import_contestants($category_id,$insertdata,$headers){
			global $user_ID;	
            if(!empty($headers)){
                foreach($headers as $key => $head){
                    $data[$head] = $insertdata[$key];
                }
            }	
			$args = array(
				'post_author' => get_current_user_id(),
				'post_content' => $data['contestant-desc'],
				'post_status' => $data['contest_status'] ,
				'post_type' => WPVC_VOTES_TYPE,
				'post_title' => $data['contest_title']
			);
            
			$post_id = wp_insert_post($args);
			wp_set_post_terms( $post_id, $category_id , WPVC_VOTES_TAXONOMY);
			update_post_meta($post_id, WPVC_VOTES_POST, $data);

            //Set featured image
            if(isset($data['featured_image_url'])){
                SELF::wpvc_voting_create_or_set_featured_image($data['featured_image_url'],$post_id);
            }
            //Update votes count 
            $votesetting = get_option(WPVC_VOTES_SETTINGS);
            if(is_array($votesetting)){
                $contest_setting = $votesetting['contest'];
                $response = Wpvc_Voting_Model::wpvc_save_votes($post_id,$category_id,$contest_setting,$data['contest_votes'],'manual');
            }
			return $post_id;
		}

        public static function wpvc_voting_create_or_set_featured_image($url, $post_id) {
			$file_name = basename($url);
			$upload = wp_upload_bits($file_name, null, file_get_contents($url));
			$wp_filetype = wp_check_filetype(basename($url), null);
			$wp_upload_dir = wp_upload_dir();
			$attachment = array(
				'guid' => _wp_relative_upload_path($upload['url']),
				'post_mime_type' => $wp_filetype['type'],
				'post_title' => preg_replace('/\.[^.]+$/', '', $file_name),
				'post_content' => '',
				'post_status' => 'inherit'
			);
			$attach_id = wp_insert_attachment($attachment, false, $post_id);
			update_post_meta($post_id, '_thumbnail_id', $attach_id);
			$wp_attached_file = substr($wp_upload_dir['subdir'], 1) . '/' . $file_name;
			update_post_meta($attach_id, '_wp_attached_file', $wp_attached_file);
			$image_150 = SELF::wpvc_voting_csv_resize($attach_id, '', 150, 150, true);
			$image_300 = SELF::wpvc_voting_csv_resize($attach_id, '', 300, 0);
			$ds_image_ico = SELF::wpvc_voting_csv_resize($attach_id, '', 80, 80, true);
			$ds_image_medium = SELF::wpvc_voting_csv_resize($attach_id, '', 800, 0);
			$file_path = get_attached_file($attach_id);
			$orig_size = getimagesize(realpath($file_path));

			$wp_attachment_array = array(
			'width' => $orig_size[0],
			'height' => $orig_size[1],
			'hwstring_small' => "height='96' width='96'",
			'file' => $wp_attached_file,
			'sizes' => Array
				(
				'thumbnail' => Array
				(
				'file' => basename($image_150['url']),
				'width' => $image_150['width'],
				'height' => $image_150['height']
				),
				'medium' => Array
				(
				'file' => basename($image_300['url']),
				'width' => $image_300['width'],
				'height' => $image_300['height']
				),
				'post-thumbnail' => Array
				(
				'file' => basename($image_300['url']),
				'width' => $image_300['width'],
				'height' => $image_300['height']
				)
			),
			'image_meta' => Array
				(
				'aperture' => 0,
				'credit' => '',
				'camera' => '',
				'caption' => '',
				'created_timestamp' => 0,
				'copyright' => '',
				'focal_length' => 0,
				'iso' => 0,
				'shutter_speed' => 0,
				'title' => ''
			)
			);
			update_post_meta($attach_id, '_wp_attachment_metadata', $wp_attachment_array);

			if ($attach_id) {
			    return true;
			} else {
			    return false;
			}
		}

        public static function wpvc_voting_csv_resize( $attach_id = null, $img_url = null, $width= null, $height= null, $crop = false ) {

			// this is an attachment, so we have the ID
			if ( $attach_id ) {
				$image_src = wp_get_attachment_image_src( $attach_id, 'full' );
				$file_path = get_attached_file( $attach_id );
				// this is not an attachment, let's use the image url
			} else if ( $img_url ) {
				$file_path = parse_url( $img_url );
				$file_path = ltrim( $file_path['path'], '/' );
				$orig_size = @getimagesize( ABSPATH.$file_path );
				$image_src[0] = $img_url;
				$image_src[1] = $orig_size[0];
				$image_src[2] = $orig_size[1];
			}
			if($image_src[1]==0){
				$orig_size = @getimagesize( realpath($file_path ));
				$image_src[1] = $orig_size[0];
				$image_src[2] = $orig_size[1];
			}

			if($width == 0){
				$width = $image_src[1];
			}
			if($height == 0){
				$height = $image_src[2];
			}
			$file_info = pathinfo( $file_path );
			$extension = '.'. $file_info['extension'];

			// the image path without the extension
			$no_ext_path = $file_info['dirname'].'/'.$file_info['filename'];
			$cropped_img_path = $no_ext_path.'-'.$width.'x'.$height.$extension;

			// checking if the file size is larger than the target size
			// if it is smaller or the same size, stop right here and return
			if ( $image_src[1] > $width || $image_src[2] > $height ) {

				// the file is larger, check if the resized version already exists (for crop = true but will also work for crop = false if the sizes match)
				if ( file_exists( $cropped_img_path ) ) {
					$cropped_img_url = str_replace( basename( $image_src[0] ), basename( $cropped_img_path ), $image_src[0] );
					$vt_image = array (
						'url' => $cropped_img_url,
						'width' => $width,
						'height' => $height
					);
					return $vt_image;
				}

				// crop = false
				if ( $crop == false ) {
					// calculate the size proportionaly
					$proportional_size = wp_constrain_dimensions( $image_src[1], $image_src[2], $width, $height );
					$resized_img_path = $no_ext_path.'-'.$proportional_size[0].'x'.$proportional_size[1].$extension;

					// checking if the file already exists
					if ( file_exists( $resized_img_path ) ) {
					$resized_img_url = str_replace( basename( $image_src[0] ), basename( $resized_img_path ), $image_src[0] );
					$vt_image = array (
						'url' => $resized_img_url,
						'width' => $new_img_size[0],
						'height' => $new_img_size[1]
					);
					return $vt_image;
					}
				}

				// no cached files - let's finally resize it
				$new_img_path = wp_get_image_editor( $file_path, $width, $height, $crop );
				$new_img_size = is_wp_error($new_img_path) ? array('height'=>0, 'width'=>0) : $new_img_path->get_size();
				$new_img = str_replace( basename( $image_src[0] ), basename($file_path), $image_src[0] );

				// resized output
				$vt_image = array (
					'url' => $new_img,
					'width' => $new_img_size['width'],
					'height' => $new_img_size['height']
				);

				return $vt_image;
			}

			// default output - without resizing
			$vt_image = array (
				'url' => $image_src[0],
				'width' => $image_src[1],
				'height' => $image_src[2]
			);
			return $vt_image;
		}


	}
}else
die("<h2>".__('Failed to load Voting Export model')."</h2>");

return new Wpvc_Export_Model();
?>