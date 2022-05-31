<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if(!class_exists('Wpvc_Contestant_Post_Controller')){
    class Wpvc_Contestant_Post_Controller{

        public function __construct(){
            //Tab menu on contestant
            add_action( 'wp_after_admin_bar_render', array($this,'wpvc_contestant_custom_menu_bar'));
            add_filter('manage_edit-' . WPVC_VOTES_TYPE . '_columns',array($this,'wpvc_contestant_post_add_columns'));
            add_filter('manage_edit-' . WPVC_VOTES_TYPE . '_sortable_columns',array($this,'wpvc_votes_custom_post_page_sort'), 10, 2);
            //Get the values of the custom added fields
			add_action('manage_' . WPVC_VOTES_TYPE . '_posts_custom_column', array($this,'wpvc_custom_new_votes_column'), 10, 2);
            add_action( 'edit_user_profile', array($this,'wpvc_edituser_meta_box_contestant'));
            add_action('edit_user_profile_update', array($this,'wpvc_update_extra_profile_fields'));
            //Custom contestant meta boxes
			add_action('add_meta_boxes', array($this,'wpvc_custom_meta_box_contestant'));

            //Add Sorting Code in the Contestants
            add_action( 'pre_get_posts', array($this,'wpvc_manage_wp_posts_be_qe_pre_get_posts'), 1 );
            add_filter( 'posts_clauses', array($this,'wpvc_contest_category_clauses'), 10, 2 );

            //Manual Votes Ajax
			add_action('wp_ajax_wpvc_add_manual_votes', array($this,'wpvc_add_manual_votes'));
			add_action('wp_ajax_nopriv_wpvc_add_manual_votes', array($this,'wpvc_add_manual_votes'));

            //Bulk Approval
			add_action('admin_footer-edit.php', array($this,'wpvc_bulk_add_approve'));
			add_action('load-edit.php', array($this,'wpvc_bulk_add_approve_action'));
			add_action('admin_notices',array($this,'wpvc_bulk_add_approve_notices'));
        }

        public function wpvc_manage_wp_posts_be_qe_pre_get_posts($query){
            if ( is_admin() && ! wp_doing_ajax() ) {
               
                if (isset($query->query_vars['post_type'])) {
                    if ($query->query_vars['post_type'] == 'contestants' && $query->query_vars['orderby']=='votes' && $query->query_vars['post_status']=='publish') {
                        $query->set('meta_key', WPVC_VOTES_CUSTOMFIELD);
                        $query->set('orderby', 'meta_value_num');
                    }
                }
                return $query;
            }
		}

        public function wpvc_contest_category_clauses($clauses, $wp_query){
			global $wpdb;

            if (isset($wp_query->query_vars['post_type'])) {
                if ($wp_query->query_vars['post_type'] == 'contestants') {
                    if ( isset( $wp_query->query['orderby'] ) && 'contest_category' == $wp_query->query['orderby'] ) {
                        $clauses['join'] .= " LEFT JOIN (
                            SELECT object_id, GROUP_CONCAT(name ORDER BY name ASC) AS color
                            FROM $wpdb->term_relationships
                            INNER JOIN $wpdb->term_taxonomy USING (term_taxonomy_id)
                            INNER JOIN $wpdb->terms USING (term_id)
                            WHERE taxonomy = 'contest_category'
                            GROUP BY object_id
                        ) AS color_terms ON ($wpdb->posts.ID = color_terms.object_id)";
                        $clauses['orderby'] = 'color_terms.color ';
                        $clauses['orderby'] .= ( 'ASC' == strtoupper( $wp_query->get('order') ) ) ? 'ASC' : 'DESC';
                    }elseif(isset( $wp_query->query['orderby'] ) && 'title' == $wp_query->query['orderby']){
                        $clauses['orderby'] = 'post_title ';
                        $clauses['orderby'] .= ( 'ASC' == strtoupper( $wp_query->get('order') ) ) ? 'ASC' : 'DESC';
                    }
                    elseif(isset( $wp_query->query['orderby'] ) && 'name' == $wp_query->query['orderby']){
                        $clauses['orderby'] = 'post_title ';
                        $clauses['orderby'] .= ( 'ASC' == strtoupper( $wp_query->get('order') ) ) ? 'ASC' : 'DESC';
                    }
                    elseif(isset( $wp_query->query['orderby'] ) && 'votes' == $wp_query->query['orderby'] && $wp_query->query_vars['post_status']=='publish'){
                        $clauses['orderby'] = 'CAST(meta_value as unsigned)';
                        $clauses['order'] .= ( 'ASC' == strtoupper( $wp_query->get('order') ) ) ? 'ASC' : 'DESC';
                    }
                    elseif(isset( $wp_query->query['orderby'] ) && 'votes' == $wp_query->query['orderby']){
                        $post_table = $wpdb->prefix.'posts';
                        $order = ( 'ASC' == strtoupper( $wp_query->get('order') ) ) ? 'ASC' : 'DESC';
                        $clauses['where'] .= " AND $wpdb->postmeta.meta_key='votes_count'";
                        $clauses['join'] .= " LEFT JOIN $wpdb->postmeta ON $post_table.ID = $wpdb->postmeta.post_id";
                        $clauses['orderby'] = "$wpdb->postmeta.meta_value + 0 $order";
                    }
                }
            }
			return $clauses;
		}

        public function wpvc_bulk_add_approve(){
            global $post_type;
			if($post_type == WPVC_VOTES_TYPE && (@$_REQUEST['post_status'] == '' || @$_REQUEST['post_status'] == 'all' || @$_REQUEST['post_status'] == 'pending' || @$_REQUEST['post_status'] == 'pending_approval' || @$_REQUEST['post_status'] == 'payment_pending' || @$_REQUEST['post_status'] == 'draft')) {
			  ?>
			  <script type="text/javascript">
				jQuery(document).ready(function() {
				  jQuery('<option>').val('contestant_approve').text('<?php _e('Approve')?>').appendTo("select[name='action']");
				  jQuery('<option>').val('contestant_approve').text('<?php _e('Approve')?>').appendTo("select[name='action2']");
				});
			  </script>
			  <?php
			}
        }

        public function wpvc_bulk_add_approve_action(){
            $screen = get_current_screen();
			if (!isset($screen->post_type) || WPVC_VOTES_TYPE !== $screen->post_type) {
				return;
			}
			$wp_list_table = _get_list_table('WP_Posts_List_Table');
			$action = $wp_list_table->current_action();
			$approved = 0;

			switch($action)
			{
				case 'contestant_approve':
					// make sure ids are submitted.  depending on the resource type, this may be 'media' or 'ids'
					if(isset($_REQUEST['post'])) {
							$post_ids = array_map('intval', $_REQUEST['post']);
					}
					if(empty($post_ids)) return;

					$sendback = remove_query_arg( array('exported', 'untrashed', 'deleted', 'ids'), wp_get_referer() );
					if ( ! $sendback )
						$sendback = admin_url( "edit.php?post_type=".WPVC_VOTES_TYPE );

					$pagenum = $wp_list_table->get_pagenum();
					$sendback = add_query_arg( 'paged', $pagenum, $sendback );
					$exploded_ids = implode(',',$post_ids);
					$result_ids = Wpvc_Settings_Model::wpvc_contestant_bulk_pending($exploded_ids);

					//Change the Status of the Contestants
					foreach($post_ids as $pid):
						$contestants = array( 'ID' => $pid, 'post_status' => 'publish' );
						if(get_post_status( $pid ) != 'publish'){
							wp_update_post($contestants);
                            update_post_meta($pid, WPVC_VOTES_CUSTOMFIELD, 0);
						}
					endforeach;

					$sendback = add_query_arg( array('approved' => $approved, 'ids' => count($result_ids) ), $sendback );
                    wp_redirect($sendback);
                    exit;
				break;

				default: return;
			}
        }

        public function wpvc_bulk_add_approve_notices(){
			global $post_type, $pagenow;
			if($pagenow == 'edit.php' && $post_type == WPVC_VOTES_TYPE) {
				if (isset($_REQUEST['approved'])) {
					//Print notice in admin bar
                    $message = sprintf( _n( 'Contestants approved.', '%s Contestants approved.', $_REQUEST['approved'] ), number_format_i18n( $_REQUEST['ids']) ) ;
					if(!empty($message)) {
							echo "<div class=\"updated\"><p>{$message}</p></div>";
					}
				}
			}
		}

        public function wpvc_add_manual_votes(){
			if($_POST['action'] == 'wpvc_add_manual_votes'){
				$pid = $_POST['pid'];
				$termid = $_POST['termid'];
				$vote_count = $_POST['no_votes'];
                $votesetting = get_option(WPVC_VOTES_SETTINGS);
                if(is_array($votesetting)){
                    $contest_setting = $votesetting['contest'];
                    $response = Wpvc_Voting_Model::wpvc_save_votes($pid,$termid,$contest_setting,$vote_count,'manual');
                }
               
            }
        }
        public static function wpvc_contestant_custom_menu_bar(){
            global $post_type, $pagenow;
            $page = isset($_GET['page'])?$_GET['page']:'';
            if(($page=='contestants' || strpos($page, 'wpvc') !== false || (isset($_GET['post_type']) && $_GET['post_type']=='contestants') || $post_type =='contestants'))
			{
                require_once(WPVC_VIEW_PATH.'wpvc_contestant_admin_view.php');
                wpvc_contestant_topbar();
            }
        }
        

        //Add columns to custom post(contestants)
		public function wpvc_contestant_post_add_columns($add_columns)
		{
			unset($add_columns['author']);
			unset($add_columns['taxonomy-contest_category']);
			unset($add_columns['comments']);
			unset($add_columns['title']);
			unset($add_columns['date']);
			$add_columns['cb'] = '<input type="checkbox" />';
			$add_columns['cb'] = '<input type="checkbox" />';
			$add_columns['image'] = __('Featured Image', 'voting-contest');
			$add_columns['title'] = __('Title', 'voting-contest');
			$add_columns['info'] = __('Info', 'voting-contest');
			$add_columns[WPVC_VOTES_TAXONOMY] = __('Contest Category', 'voting-contest');
			$add_columns['manual_votes'] = __('Add Manual Votes', 'voting-contest');
			$add_columns['votes'] = __('Votes', 'voting-contest');

			return $add_columns;
		}
     	//Specify the columns that need to be sortable
		public function wpvc_votes_custom_post_page_sort($columns) {
			$columns[WPVC_VOTES_TAXONOMY]= 'contest_category';
			$columns['votes']='votes';
			return $columns;
        }

        public function wpvc_edituser_meta_box_contestant($user){
            if(!empty($user)){
                $user_id = $user->ID;
                if($user_id !=''){
                   $custom = get_option(WPVC_VOTES_REG_ASSIGN);
                   $user_values = get_user_meta($user_id,WPVC_VOTES_USER_META);
                   require_once(WPVC_VIEW_PATH.'wpvc_settings_view.php');
                   wpvc_useredit_view($custom,$user_values);
                }
            }
        }

        public function wpvc_update_extra_profile_fields($user_id){
            $custom = get_option(WPVC_VOTES_REG_ASSIGN);
            $user_values = get_user_meta($user_id,WPVC_VOTES_USER_META);
            $post_values = array();
            if(!empty($custom)){
                foreach($custom as $cus){
                    $system_name = $cus['system_name'];
                    if(isset($_POST[$system_name])){
                        $post_values[$system_name] = $_POST[$system_name];
                        unset($user_values[$system_name]);
                    }
                }
            }

            if(!empty($post_values)){
                if(empty($user_values)){
                    $user_values = array();
                }
                $whole_val = array_merge($user_values,$post_values);
                update_user_meta($user_id,WPVC_VOTES_USER_META,$whole_val);
            }
        }

        public function wpvc_custom_new_votes_column($column, $post_id) {
            add_thickbox();
            wp_register_script('ow_admin_js', WPVC_ASSETS_JS_PATH . 'wpvc_vote_admin_js.js');
			wp_enqueue_script('ow_admin_js',array('jquery'));
            $terms = get_the_terms($post_id, WPVC_VOTES_TAXONOMY);
			if (!empty($terms)) {
                $current_term_id = $terms[0]->term_id;
                $imgcontest =  get_term_meta($current_term_id,'imgcontest',true);
			}
			else{
				$current_term_id = $imgcontest = '';
            }

            switch ($column) {

                case 'voteid':
                    echo $post_id;
                break;

                case WPVC_VOTES_TAXONOMY:
                    if (!empty($terms)) {
                    $out = array();
                    foreach ($terms as $c) {
                        $_taxonomy_title = esc_html(sanitize_term_field('name', $c->name, $c->term_id, 'category', 'display'));
                        $out[] = "<a href='edit.php?" . WPVC_VOTES_TAXONOMY . "=$c->slug&post_type=" . WPVC_VOTES_TYPE . "'>$_taxonomy_title</a>";
                    }
                    echo join(', ', $out);
                    } else {
                        _e('Uncategorized','voting-contest');
                    }
                break;

                case 'image':
                    if($imgcontest  == 'video'){
                        $get_post_metas = get_post_meta($post_id, WPVC_VOTES_POST,TRUE);
                        if(is_array($get_post_metas) && array_key_exists('contestant-ow_video_url',$get_post_metas)){
                            $video_url = $get_post_metas['contestant-ow_video_url'];
                            if($video_url==''){
                                $video_url = get_post_meta($post_id,'_ow_video_upload_url',true); 
                            }
                        }else{
                            $video_url = get_post_meta($post_id, 'contestant-ow_video_url', TRUE);
                            if($video_url==''){
                                $video_url = get_post_meta($post_id,'_ow_video_upload_url',true); 
                            }
                        }
                        echo '<div id="wpvc_contestant_video_url-'.$post_id.'" data-postid="'.$post_id.'" data-featuredimage="" data-contest="'.$imgcontest.'" data-url="'.$video_url.'"></div>';
                    }

                    if($imgcontest  == 'music'){
                        $get_post_metas = get_post_meta($post_id, WPVC_VOTES_POST,TRUE);
                        if(is_array($get_post_metas) && array_key_exists('contestant-ow_music_url_link',$get_post_metas)){
                            $music_url = $get_post_metas['contestant-ow_music_url_link'];
                            
                            if($music_url==''){
                                $music_url = get_post_meta($post_id, '_ow_music_upload_url', TRUE);
                            }
                            if($music_url==''){
                                $music_url = get_post_meta($post_id,'contestant-ow_video_url',TRUE); 
                            }
                            if($music_url==''){
                                $music_url = get_post_meta($post_id,'_ow_music_upload_attachment',TRUE); 
                            }
                        }else{
                            $music_url = get_post_meta($post_id, '_ow_music_upload_url', TRUE);
                            if($music_url==''){
                                $music_url = get_post_meta($post_id, 'contestant-ow_music_url_link', TRUE);
                            }
                            if($music_url==''){
                                $music_url = get_post_meta($post_id,'contestant-ow_video_url',TRUE); 
                            }
                            if($music_url==''){
                                $music_url = get_post_meta($post_id,'_ow_music_upload_attachment',TRUE); 
                            }
                        }
                        if (has_post_thumbnail($post_id)) {
                            $image_arr = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'thumbnail');
                            $image_src = htmlspecialchars($image_arr[0]);
                        }else{
                            $image_src = WPVC_NO_IMAGE_CONTEST;
                        }
                        $musicfileenable = get_term_meta($current_term_id,'musicfileenable',true);
                        echo '<div id="wpvc_contestant_video_url-'.$post_id.'" data-postid="'.$post_id.'" data-featuredimage="'.$image_src.'" data-fileenable="'.$musicfileenable.'" data-url="'.$music_url.'"></div>';
                    }

                    if($imgcontest  == 'photo' || $imgcontest  == 'essay'){
                        if (!has_post_thumbnail($post_id)) {
                            $attimages = get_attached_media('image', $post_id);
                            foreach ($attimages as $image) {
                                if($image->menu_order == 0){
                                    set_post_thumbnail( $post_id, $image->ID);
                                }
                            }
                        }
                        if (has_post_thumbnail($post_id)) {
                        $image_arr = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'thumbnail');
                        $image_src = htmlspecialchars($image_arr[0]);
                            echo "<img src='".$image_src."' width='200' height='150' class='left-img-thumb' />";
                        } else {
                            echo "<img src=".WPVC_NO_IMAGE_CONTEST." width='200px' class='left-img-thumb' />";
                        }
                    }

                break;


                case 'manual_votes':
                    $votes = get_post_meta($post_id, WPVC_VOTES_CUSTOMFIELD,'true');
                    if ( get_post_status ( $post_id ) == 'publish' ) {
                        ?>
                        <div id="add_manual_vote_<?php echo $post_id; ?>" style="display:none;" class="ow_manual_votes_thickbox">
                            <h3><?php _e('Add Manual Votes to the ','voting-contest');echo '"'.get_the_title($post_id).'"'; ?></h3>
                            <h4><i><?php _e('Total Number of Votes : ','voting-contest');echo ($votes == null)?0:$votes; ?></i></h4>
                            <label for="ow_manual_votes"><?php _e('Add Manual Votes','voting-contest'); ?></label> 		 <span class="wpvc_admin_error"></span>
                            <input type="number" class="manual_votes_text" name="ow_manual_votes" id="ow_manual_votes<?php echo $post_id; ?>"/>
                            <a href='javascript:' class='ow_add_manualvotes' data-id="<?php echo $post_id; ?>" data-term="<?php echo $current_term_id; ?>"><?php _e('Submit','voting-contest'); ?></a>
                        </div>
                        <?php
                        echo '<a class="ow_add_manualvotes thickbox" href="#TB_inline?width=400&height=250&inlineId=add_manual_vote_'.$post_id.'">Add Manual Votes</a>';
                    }
                    else{
                        echo '<a class="ow_add_manualvotes">Not allowed</a>';
                    }
                   
                break;

                case 'votes':
                    if($current_term_id!='')
                    $votes = get_post_meta($post_id, WPVC_VOTES_CUSTOMFIELD,'true');
                    echo ($votes == null)?0:$votes;
                break;

                case 'info':
                    echo "<i class='owvotingicon owicon-date'></i><span class='ow_admininfo'>".get_the_date().'</span> <br/>';
                    $author = get_the_author();
                    $author = ($author != null)?$author:'Admin';
                    echo "<i class='owvotingicon owicon-authors'></i><span class='ow_admininfo'>".$author.'</span> <br/>';
                    echo "<i class='owvotingicon owicon-imgcontest'></i><span class='ow_admininfo'>".ucfirst($imgcontest).'</span>';
                break;
            }
		}
        //Add the custom meta boxes on add/edit
		public function wpvc_custom_meta_box_contestant(){
			add_meta_box('votesstatus', __('Votes For this Contestant','voting-contest'), array($this,'wpvc_votes_count_meta_box'), WPVC_VOTES_TYPE, 'normal', 'high',array( '__block_editor_compatible_meta_box' => true));
			add_meta_box('votecustomfields', __('Custom Fields','voting-contest'), array($this,'wpvc_votes_contestant_custom_field_meta_box'), WPVC_VOTES_TYPE, 'normal', 'high');
			add_meta_box('votescustomlink', __('Custom Link for Redirection','voting-contest'), array($this,'wpvc_votes_custom_link'), WPVC_VOTES_TYPE, 'normal', 'high');
        }

        //Votes count metabox
		public function wpvc_votes_count_meta_box($post) {			
			$cnt = get_post_meta($post->ID,WPVC_VOTES_CUSTOMFIELD,true);
            ?>
                <h1> <?php echo  $cnt ? $cnt.' ' : '0'.' '; _e('Votes','voting-contest'); ?> </h1> 
                <?php $cnt = ($cnt == null)?0:$cnt; ?>                
            <?php  
		}

        //Custom Field Metabox
        public function wpvc_votes_contestant_custom_field_meta_box($post){
            global $post;
			global $pagenow;
        	$page =  in_array( $pagenow, array( 'post.php',  ) ) ? 'edit' : 'new';	

            echo "<div id='wpvc_admin_custom'  data-url='".site_url()."'>
                    <input type='hidden' value='".$page."' id='wpvcPageNow' />
                    <input type='hidden' value='contestants' id='currentwpvcPage' />
                </div>";
        }

        public function wpvc_votes_custom_link($post){
            $custom_link = get_post_meta($post->ID,WPVC_CONTESTANT_LINK,true);
            ?>
            <div class="ow_contestants-row">
                <div class="ow_contestants-label">
                    <label for="ow_contestant_link"><h3><?php _e('Custom Link for Redirection','voting-contest'); ?></h3></label>
                </div>
                <div class="ow_contestants-field">
                    <input type="text" name="ow_contestant_link" id="ow_contestant_link" value="<?php echo $custom_link; ?>" style="width: 100%;" /> 
                </div>                    
            </div>
            <?php
        }
        
      
    }
}else
die("<h2>".__('Failed to load Voting Contestant Post Controller','voting-contest')."</h2>");


return new Wpvc_Contestant_Post_Controller();
