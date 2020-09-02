<?php

/**
 * Youzer Activity Edit Form.
 */
class Youzer_Activity_Edit_Form {
	
	function __construct() {

		// Call Edit Form Modal.
		add_action( 'wp_ajax_yz_get_edit_activity_form', array( $this, 'modal' ) );

		// Save Content Edits.
		add_action( 'wp_ajax_yz_save_activity_edit_form', array( $this, 'update_activity' ) );
	
		// Add Activity Post "Edit" Button.
		add_action( 'bp_activity_entry_meta', array( $this, 'add_edit_activity_button' ) );

		// Add Activity Comment "Edit" Button.
		add_action( 'bp_activity_comment_options',	array( $this, 'add_edit_activity_comment_button' ) );
		
		// Add Edit Form Css.
		if ( ! is_admin() && ! is_network_admin() ) {
			add_action( 'yz_activity_scripts', array( $this, 'assets' ) );
		}
		
		// Add Edit Post Tool
		add_filter( 'yz_activity_tools', array( $this, 'edit_activity_tool' ), 20, 2 );

	}


	/**
	 * Add Delete Activity Tool.
	 */
	function edit_activity_tool( $tools, $post_id ) {
		
		$activity = new BP_Activity_Activity( $post_id );

		if ( ! yzea_is_activity_editable( $activity ) ) {
			return $tools;
		}

		// Get Tool Data.
		$tools[] = array(
			'icon' => 'fas fa-edit',
			'title' =>  __( 'Edit', 'youzer' ),
			'action' => 'edit-activity',
			'class' => array( 'yz-edit-tool', 'yz-edit-post' ),
			'attributes' => array( 'activity-type' => $activity->type )
		);

		return $tools;
	}

	/**
	 * Edit Form Modal.
	 */
	function modal() {

 		// Check Nonce Security
 		check_ajax_referer( 'youzer-nonce', 'yz_edit_activity_nonce' );

		// Get Activity ID.
		$activity_id = isset( $_POST['activity_id'] ) ? (int) $_POST['activity_id'] : false;
		
		if ( ! $activity_id ) {
			die( json_encode( array( 'remove_button' => true, 'error' => __( 'Nothing found!', 'youzer-edit-activity' ) ) ) );
		}

		// Get Activity.
		$activity = new BP_Activity_Activity( $activity_id );

		if ( ! yzea_is_activity_editable( $activity ) ) {
			$response['remove_button'] = true;
			$response['error'] = __( "You don't have the permission to process this edition!", 'youzer-edit-activity' );
			die( json_encode( $response ) );
		}

	    // Get Data.
	    $activity_type = isset( $_POST['activity_type'] ) ? $_POST['activity_type'] : null; 

	    // Args
	    $modal_args = array(
	        'modal_type' => 'div',
	        'hide-action' => true,
	        'show_close' => false,
	        'id'        => 'yz-edit-activity-form',
	        'button_id' => 'yz-edit-activity',
	    );

	    // Get Modal Title.
	    if ( $activity_type == 'activity_comment' ) {
		    $modal_args['title'] = __( 'edit comment', 'youzer-edit-activity' );
	    } else {
		    $modal_args['title'] = __( 'edit Activity', 'youzer-edit-activity' );
	    }

	    // Set Modal Data
	    $modal_args['title_icon'] = 'far fa-edit';
	    $modal_args['button_title'] = __( 'save changes', 'youzer-edit-activity' );
		$response = array();


		// Get Activity ID.
		$activity_id = isset( $_POST['activity_id'] ) ? (int) $_POST['activity_id'] : false;
		
		ob_start();

	    // Get User Review Form.
	    yz_modal( $modal_args, array( $this, 'form_content' ) );

	    $form = ob_get_contents();

	    ob_end_clean();

		$response['form'] = $form;

	    $response = array_merge( $response, $this->get_activity_content( $activity_id ) );

		die( json_encode( $response ) );
	    
	}

	/**
	 * Form Content
	 */
	function form_content() {
		// Limit Form Post Type.
		add_filter( 'yz_wall_form_post_types_buttons', array( $this, 'set_form_activity_type' ) );

		// Add Form Custom Fields.
		add_action( 'bp_activity_post_form_options', array( $this,'add_edit_form_fields' ) );
		?>

		<div id="youzer-edit-activity-wrapper">
			<?php bp_get_template_part( 'activity/post-form' ); ?>
		</div>

		<?php

		// Limit Form Post Type.
		remove_filter( 'yz_wall_form_post_types_buttons', array( $this, 'set_form_activity_type' ) );

	}

	/**
	 * Get Activity Content.
	 */
	public function get_activity_content( $activity_id ) {

		// Init Retval.
		$retval = array(
			'status'	=> false,
			'content'	=> __( 'Nothing found!', 'youzer-edit-activity' ),
		);
		
		global $YZ_upload_url;
		
		// Get Activity.
		$activity = new BP_Activity_Activity( $activity_id );

		// Replace Emojis
		$activity->content = $this->replace_emojis_with_code( $activity->content );

		// Get Emojis Values.
        $retval['posts_emojis'] = yz_options( 'yz_enable_posts_emoji' );
        $retval['comments_emojis'] = yz_options( 'yz_enable_comments_emoji' );

		// Get Activity Content.
		$retval['content'] = strip_tags( stripslashes_deep( $activity->content ), '<img>' );

		// Get Activity Meta.
		$retval['meta'] = $this->get_activity_meta( $activity_id, $activity->type );

		// Get Activity Attachments.
		$retval['attachments'] = $this->get_activity_attachments( $activity_id, $activity->type );

		// Get Live Preview Data.
		$retval['url_preview'] = $this->get_activity_url_preview( $activity_id, $activity->content );

		// Get Post Privacy.
		$retval['privacy'] = $this->get_privacy( $activity_id );

		// Get Post Mood.
		$retval['mood'] = $this->get_mood( $activity_id );

		// Get Tagged Friends
		$retval['tagged_friends'] = $this->get_tagged_friends( $activity_id );

		$retval['status'] = true;
		
		return $retval;
	}
	
	/**
	 * Replace Emojis with their code.
	 */
	function replace_emojis_with_code( $content ) {

		$dom = new DOMDocument();
		
		// since you have a fragment, wrap it in a <body>
		$dom->loadHTML( '<?xml encoding="utf-8" ?><body>'.$content . '</body>' );
		
		$links = $dom->getElementsByTagName( 'img' );
		
		while ( $link = $links[0] ) {
			$link->parentNode->insertBefore( new DOMText( $link->getAttribute( 'alt' ) ) , $link );
			$link->parentNode->removeChild($link);
		}

		$result = $dom->saveHTML( $dom->getElementsByTagName( 'body' )[0] );

		$output = substr( $result, strlen( '<body>' ), -strlen( '</body>' ) );

		return $output;
	}

	/**
	 * Get Activity Mood.
	 */
	function get_mood( $activity_id ) {
		
		// Get Post Tagged Friends.
		$mood = bp_activity_get_meta( $activity_id, 'mood' );

		if ( empty( $mood ) ) {
			return;
		}

		$mood_data = yz_wall_mood_categories();

		if ( $mood['type'] == 'feeling' ) {
			$class = '';
			$image = $mood['type'] == 'feeling' ? "<div class='yz-item-img' style='background-image: url(" .  yz_get_mood_emojis_image( $mood['value'] ) . ");'></div>": '';
		} else {
			$class = 'yz-selected-item-no-image';
			$image = '';
		}
			
		ob_start(); ?>
			<div class="yz-selected-item yz-feeling-selected-item <?php echo $class; ?>"><?php echo $image;?><div class="yz-item-title"><?php echo $mood['value']; ?></div>
				<i class="fas fa-trash-alt yz-selected-item-tool yz-list-delete-item"></i>
				<input type="hidden" name="mood_value" value="<?php echo $mood['value']; ?>">
			</div>
		
		<?php
		
		$content = ob_get_clean();

		return array( 'title' => $mood_data[ $mood['type'] ]['title'], 'content' => $content, 'type' => $mood['type'] );

	}

	/**
	 * Get Activity Tagged Friends.
	 */
	function get_tagged_friends( $activity_id ) {
		
		// Get Post Tagged Friends.
		$users = bp_activity_get_meta( $activity_id, 'tagged_users' );

		if ( empty( $users ) ) {
			return;
		}

		ob_start();

		foreach ( $users as $user_id ) { ?>

		<div class="yz-selected-item yz-tagged-user">
			<div class="yz-item-img" style="background-image: url( <?php echo bp_core_fetch_avatar( array( 'item_id' => $user_id, 'type' => 'thumb', 'html' => false ) ); ?>);"></div>
			<div class="yz-item-title"><?php echo bp_core_get_user_displayname( $user_id ); ?></div><i class="fas fa-trash-alt yz-selected-item-tool yz-list-delete-item yz-tagusers-delete-user" data-user-id="<?php echo $user_id; ?>"></i>
			<input type="hidden" name="tagged_users[]" value="<?php echo $user_id; ?>">
		</div>
		
		<?php
		
		}

		$content = ob_get_clean();

		return $content;

	}

	/**
	 * Get Activity Privacy.
	 */
	function get_privacy( $activity_id ) {

		global $wpdb, $bp;

		// Prepare SQL
		$sql = $wpdb->prepare( "SELECT privacy from {$bp->activity->table_name} WHERE id = %d", $activity_id );

		// Update Privacy
		return $wpdb->get_var( $sql );

	}

	/**
	 * Get Activity Url Preview.
	 */
	function get_activity_url_preview( $activity_id, $activity_content = null ) {

		global $Youzer;

		// Get Url Data.
		$url_data = yz_get_activity_url_preview_meta( $activity_id );

		if ( empty( $url_data ) || ! $Youzer->wall->show_url_preview( $url_data, $activity_content ) ) {
			$url_data = false;
		}
		
		return apply_filters( 'yzea_get_activity_url_preview', $url_data );

	}

	/**
	 * Get Activity Content By Type.
	 */
	function get_activity_attachments( $activity_id, $activity_type ) {

		if ( ! yzea_is_user_can_edit_attachments() ) {
			return 'hide';
		}

		// Init Vars
		$item = '';

		// Get Attachments.
		$attachments = yz_get_activity_attachments( $activity_id, '*' );

		// Unserialze Attachments.
		// $attachments = unserialize( $attachments );

		if ( ! empty( $attachments ) ) {

			// Get Photos Extension.
			$photos_extensions = array( 'jpg', 'png', 'gif', 'jpeg' ); 

			// Get Upload Directory.
			$dir = wp_get_upload_dir();

			foreach ( $attachments as $attachment )  {

				$src = maybe_unserialize( $attachment['src'] );
				$data = maybe_unserialize( $attachment['data'] );

				// Get File Url.
				$url = yz_get_wall_file_url( $src );	
					
				// Get Uploaded File extension
				$ext = strtolower( pathinfo( $src['original'], PATHINFO_EXTENSION ) );

				// Get File Data.
				$data = '[{"real_name": "' . $data['real_name'] . '", "file_size": "' . $data['file_size'] . '", "original": "'. $src['original'] . '"}]';

				if ( in_array( $ext, $photos_extensions ) ) {
					$item = $item . "<div data-item-id='{$attachment['id']}' class='yz-attachment-item yz-image-preview' style='background-image: url($url);'><div class='yz-attachment-details'><i class='fas fa-trash-alt yz-delete-attachment'></i></div><input type='hidden' class='yz-attachment-data' name='attachments_files[]' value='$data'></div>";
				} else {
					$item = $item . "<div class='yz-attachment-item yz-file-preview'><div class='yz-attachment-details'><i class='fas fa-paperclip yz-file-icon'></i><span class='yz-file-name'>{$src['real_name']}</span><i class='fas fa-trash-alt yz-delete-attachment'></i></div><input type='hidden' class='yz-attachment-data' name='attachments_files[]' value='$data'></div>";
				}

			}

		}

		return apply_filters( 'yzea_get_activity_attachments', $item, $activity_id, $activity_type );

	}

	/**
	 * Get Activity Content By Type.
	 */
	function get_activity_meta( $activity_id, $activity_type ) {

		// Init Data.
		$data = array();

		switch ( $activity_type ) {

			case 'activity_quote':
				// Get Quote Data.
				$data['quote_text'] = bp_activity_get_meta( $activity_id, 'yz-quote-text' );
				$data['quote_owner'] = bp_activity_get_meta( $activity_id, 'yz-quote-owner' );
				break;
			
				// Get Link Data.
			case 'activity_link':
				$data['link_url'] = bp_activity_get_meta( $activity_id, 'yz-link-url' );
				$data['link_title'] = bp_activity_get_meta( $activity_id, 'yz-link-title' );
				$data['link_desc'] = bp_activity_get_meta( $activity_id, 'yz-link-desc' );
				break;
			
				// Get Giphy Data.
			case 'activity_giphy':
				$data['giphy_image'] = bp_activity_get_meta( $activity_id, 'giphy_image' );
				break;
			
			default:
				break;
		}

		return apply_filters( 'yz_get_edit_activity_content_by_type', $data, $activity_type, $activity_id );
	}

	/**
	 * Set Form Activity Type.
	 */
	function set_form_activity_type( $post_types ) {

		// Get Activity Type.
		$activity_type = isset( $_POST['activity_type'] ) ? $_POST['activity_type'] : null;

		if ( empty( $activity_type ) ) {
			return $post_types;
		}

		foreach ( $post_types as $key => $post_type ) {
			if ( $post_type['id'] != $activity_type ) {
				unset( $post_types[ $key ] );
			}
		}

		return $post_types;
	}


	private function filter_activity_content( $activity_id ) {

		$activity = new BP_Activity_Activity( $activity_id );
		
		if ( ! $activity || is_wp_error( $activity ) ) {
			return false;
		}
		
		if ( ! $this->can_edit_activity( $activity ) ) {
			return false;
		}
		
		$content = stripslashes( $activity->content );
		
		//convert @mention anchor tags into plain text
		$content = $this->strip_mention_tags( $content );
		
		// remove surrounding <p> tags
		if ( substr( $content, 0, strlen( "<p>" ) ) == "<p>" ) {
			$content = substr( $content, strlen( "<p>" ) );
		} 

		if ( substr( $content,-strlen( "</p>" ) )=== "</p>" ){
			$content = substr( $content, 0, strlen( $content )-strlen( "</p>" ) );
		}
		
		return apply_filters( 'yzea_get_activity_content', $content );
	}

	/**
	 * Add Form Hidden Fields.
	 */
	public function add_edit_form_fields() {

		?>

		<input type="hidden" name="yz_edit_activity_nonce" value="<?php echo wp_create_nonce( 'youzer-edit-activity' ); ?>">

		<?php

	}

	/**
	 * Add Edit Activity Button.
	 */
	public function add_edit_activity_button() {

		if ( ! apply_filters( 'yz_show_activity_meta_edit_button', false ) ) {
			return;
		}

		global $activities_template;

		// Get Activity.
		if ( yzea_is_activity_editable( $activities_template->activity ) ) {

			?>
			
			<a href="#" class="button bp-secondary-action yz-edit-activity" data-activity-id="<?php bp_activity_id() ;?>" data-activity-type="<?php echo $activities_template->activity->type; ?>"><?php _e( 'Edit', 'youzer-edit-activity' ); ?>
			</a>

			<?php
		}

	}
	
	/**
	 * Add Edit Activity Comment Button.
	 */
	public function add_edit_activity_comment_button() {

		global $activities_template;
		
		if ( yzea_is_activity_editable( $activities_template->activity ) ) {

			?>

			<a class="bp-secondary-action yz-edit-activity" data-activity-id="<?php echo $activities_template->activity->current_comment->id;?>" data-activity-type="activity_comment"><?php _e( 'Edit', 'youzer-edit-activity' ); ?>
			</a>

			<?php 
		}
	}

	/**
	 * Js.
	 */
	public function assets() {

		// Edit Activity Script.
		wp_enqueue_script( 'youzer-edit-activity', YZEA_URL . 'assets/js/yz-edit-activity.min.js', array( 'jquery' ), YZEA_VERSION, true );
		
	}
	
	/**
	 * Update Activity.
	 */
	public function update_activity() {

		// Check Ajax Referer.
		check_ajax_referer( 'youzer-edit-activity', 'yz_edit_activity_nonce' );

		$retval = array(
			'status'	=> false,
			'content'	=> __( 'Error!', 'youzer-edit-activity' ),
		);

		// Get Activity ID.
		$activity_id = isset( $_POST['activity_id'] ) ? $_POST['activity_id'] : false;

		$target = isset( $_POST['target'] ) ? $_POST['target'] : false;
		
		if ( ! $activity_id ) {
			die( json_encode( $retval ) );
		}

		// Get Activity.
		$activity = new BP_Activity_Activity( $activity_id );

		// Get Old Content.
		$activity->old_content = $activity->content;

		if ( ! yzea_is_activity_editable( $activity ) ) {
			$retval['remove_modal'] = true;
			$retval['error'] = __( "You don't have the permission to process this edition!", 'youzer-edit-activity' );
			die( json_encode( $retval ) );
		}
		
		if( ! $activity || is_wp_error( $activity ) ) {
			return false;
		}

		$args = array(
			'activity_id' => $activity_id,
			'content'	 => isset( $_POST['content'] ) ? $_POST['content'] : '',
		); 

		// Disable Checking Acitivty Types.
		add_filter( 'yz_validate_wall_form_post_type', '__return_false' );

		// Init Wall Form.
		$wall_form = new Youzer_Wall_Form();

		// Validate Form.
		$wall_form->validate( $_POST, true );

		// Save Attachments
		if ( yzea_is_user_can_edit_attachments() ) {
    		
    		// Save Attacments.
    		$attachments = new Youzer_Wall_Attachments();
			$attachments->save_attachments( $activity_id, $_POST );
			
			if ( isset( $_POST['delete_attachments'] ) ) {
				// Delete Deleted Attachments.
				$attachments->delete_attachments_by_media_id( $_POST['delete_attachments'] );
			}

		} else {
			add_filter( 'yz_validate_wall_form_attachments', '__return_false' );
			add_filter( 'yz_validate_wall_form_slideshow', '__return_false' );
		}

		// Save Post Meta
        $activity->content = apply_filters( 'yzea_activity_content', $args['content'], $activity_id );

		if ( $activity->save() ) {

			// Save Form Data.
			$wall_form->save_meta( $activity_id, $_POST );

			// Save Live Preview.
			if ( ! empty( bp_activity_get_meta( $activity_id, 'url_preview' ) && ! isset( $_POST['url_preview_link'] ) ) ) {
				bp_activity_delete_meta( $activity_id, 'url_preview' );
			}

			// Save Mood.
			if ( ! empty( bp_activity_get_meta( $activity_id, 'mood' ) && ! isset( $_POST['mood_value'] ) ) ) {
				bp_activity_delete_meta( $activity_id, 'mood' );
			}

			// Save Mood.
			if ( ! empty( bp_activity_get_meta( $activity_id, 'tagged_users' ) && ! isset( $_POST['tagged_users'] ) ) ) {
				bp_activity_delete_meta( $activity_id, 'tagged_users' );
			}

			$wall_form->save_live_preview( $activity_id, $_POST );
			
			// Hook
			do_action( 'yz_after_editing_activity', $activity, $_POST );

			if ( $activity->type == 'activity_comment' && $target == 'comment' ) {
				$retval['content'] = apply_filters( 'bp_get_activity_content', $activity->content );
				die( json_encode( $retval ) );
			} else {
				
				// Get Activity Args.
				$activity_args = array( 'include' => $activity_id, 'display_comments' => 'stream' );
				
				// Get Activity Template.
				if ( bp_has_activities ( $activity_args ) ) {
					while ( bp_activities() ) {
						bp_the_activity();
						bp_get_template_part( 'activity/entry' );
						die;
					}
				}

			}

			// Enable Checking Activity Types.
			remove_filter( 'yz_validate_wall_form_post_type', '__return_false' );
		}

	}

}