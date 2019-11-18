<?php

/**
 * Wall Attachments.
 */
class Youzer_Wall_Attachments {

	function __construct() {
		
		// Ajax - Upload Attachments
		add_action( 'wp_ajax_yz_upload_wall_attachments', array( &$this, 'upload_attachments' ) );

		// Ajax - Delete Attachments
		add_action( 'wp_ajax_yz_delete_wall_attachment', array( &$this, 'delete_attachment' ) );

		// Save Attachments.
		add_action( 'yz_after_adding_wall_post', array( $this, 'save_attachments' ), 10, 2 );
		add_action( 'yz_after_adding_wall_post', array( $this, 'save_embeds_videos' ), 10, 2 );

		// Delete Hashtags On Post Delete.
		add_action( 'bp_before_activity_delete', array( $this, 'delete_attachments' ) );

		// Copy Uploaded Avatar & Cover to The Youzer Upload Directory.
		add_action( 'bp_activity_after_save', array( $this, 'set_new_avatar_activity' ) );
		add_action( 'xprofile_cover_image_uploaded', array( $this, 'set_new_cover_activity' ) );

	}

	/**
	 * Save Activity Attachments
	 */
	function save_attachments( $activity_id, $data ) {


		if ( empty( $data['attachments_files'] ) ) {
			if ( ! empty( bp_activity_get_meta( $activity_id, 'attachments' ) ) ) {
				bp_activity_delete_meta( $activity_id, 'attachments' );
			}
		} else {

			// Get Attachment.
			$atts = $this->move_attachments( $data['attachments_files'] );

			if ( isset( $data['post_type'] ) && $data['post_type'] == 'activity_video' ) {

				$atts = maybe_unserialize( $atts );
				
				// Set Video As Uploaded Localy.
				$atts[0]['provider'] = 'local';
				 
				// Get Attachments.
				$attachments = json_decode( stripcslashes( $data['attachments_files'][0] ), true );

				if ( isset( $attachments[0]['video_thumbnail'] ) ) {

					// Get Video Thumbnail.
					$video_thumbnail = $this->upload_video_thumbnail( $attachments[0]['video_thumbnail'] );
					
					// Append Thumbnail Data.
					if ( ! empty( $video_thumbnail ) ) {
						$atts[0]['thumbnail'] = $video_thumbnail['thumbnail'];
						$atts[0]['thumbnail_data'] = $video_thumbnail['thumbnail_data'];
					}
				}

				$atts = serialize( $atts );
			}

			// Save Attachment.
			$this->save_activity_attachments( $activity_id, $atts );

		}

	}
	
	/**
	 * Save Posts Embeds Videos.
	 **/
	function save_embeds_videos( $activity_id, $data ) {

		if ( $data['post_type'] != 'activity_status' || empty( $data['content'] ) ) {
			return;
		}

		$embed_exists = false;

		// Init Array.
		$atts = array();

		$supported_videos = yz_attachments_embeds_videos();
	
		// Get Post Urls.
		if ( preg_match_all( '#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $data['content'], $match ) ) {

			foreach ( array_unique( $match[0] ) as $link ) {

				foreach ( $supported_videos as $provider => $domain ) {

					$video_id = yz_get_embed_video_id( $provider, $link );

					if ( ! empty( $video_id ) ) {
												
						$embed_exists = true;
						
						$video_data = array( 'provider' => $provider, 'original' => $video_id );
						
						$thumbnails = yz_get_embed_video_thumbnails( $provider, $video_id );
						
						if ( ! empty( $thumbnails ) ) {
							$video_data['thumbnail'] = $thumbnails;
							$video_data['file_size'] = 0;
						}

						$atts[] = $video_data;

					}

				}

			}

		}

		// Change Activity Type from status to video.
		if ( $embed_exists ) {
			$activity = new BP_Activity_Activity( $activity_id );
			$activity->type = 'activity_video';
			$activity->save();
		}

		// Save Attachment.
		$this->save_activity_attachments( $activity_id, $atts );

	}

	/**
	 * Upload Video Thumbnail.
	 **/
	function upload_video_thumbnail( $image = null ) {

		if ( empty( $image ) ) {
			return;
		}

		global $YZ_upload_dir;

		// Decode Image.
		$decoded_image = base64_decode( preg_replace( '#^data:image/\w+;base64,#i', '', $image ) );

		// Get Unique File Name.
		$filename = uniqid( 'file_' ) . '.jpg';

		// Get File Link.
		$file_link = $YZ_upload_dir . $filename;

		// Get Unique File Name for the file.
        while ( file_exists( $file_link ) ) {
			$filename = uniqid( 'file_' ) . '.' . $ext;
		}
		
		// Upload Image.
		$image_upload = file_put_contents( $file_link, $decoded_image );

		if ( $image_upload ) {

			// Get File Data.
			$file = array(
				'type' => 'image/jpeg',
				'file_size' => filesize( $file_link )
			);

			// Get File Size.
	    	$file_size = getimagesize( $file_link );

	    	if ( ! empty( $file_size ) ) {
	    		$file['size'] = array( 'width' => $file_size[0], 'height' => $file_size[1] );
	    	}

	    	return array( 'thumbnail' => $filename, 'thumbnail_data' => $file );

		}

		return false;

	}

	/**
	 * Save Attachments.
	 */
	function save_activity_attachments( $activity_id, $attachments ) {

		// Serialize Attachments Data.
		$attachments = maybe_unserialize( $attachments );

		if ( empty( $attachments ) ) {
			return;
		}

		global $wpdb, $Yz_media_table, $YZ_upload_dir;

		// Get Current Time.
		$time = current_time( 'mysql' );

		foreach ( $attachments as $attachment ) {

			// Get Data.
			$data = array( 'file_size' => $attachment['file_size'] );

			$original_image = isset( $attachment['original'] ) ? $attachment['original'] : ( isset( $attachment['file_name'] ) ? $attachment['file_name'] :  '' );

			if ( empty( $original_image ) ) {
				continue;
			}

			$src = array( 'original' => $original_image );

			$thumbnail_image = isset( $attachment['thumbnail'] ) ? $attachment['thumbnail'] : '';
			
			if ( ! empty( $thumbnail_image ) ) {
				$src['thumbnail'] = $thumbnail_image;
			}

			// Add Video Provider if Found.
			if ( isset( $attachment['provider'] ) ) {
				$src['provider'] = $attachment['provider'];
			}

			// Unset Original.
			if ( isset( $attachment['original'] ) ) {
				unset( $attachment['original'] );
			}
			
			// Unset Thumbnail Data.
			if ( isset( $attachment['thumbnail'] ) ) {
				unset( $attachment['thumbnail'] );
			}

			// Unset Thumbnail Data.
			if ( isset( $attachment['provider'] ) ) {
				unset( $attachment['provider'] );
			}
			
			$args = array(
				'src' => serialize( $src ),
				'data' => ! empty( $attachment ) ? serialize( $attachment ) : '',
				'item_id' => $activity_id,
				'privacy' => 'public',
				'time' => $time
			);
			
			// Insert Attachment.
			$result = $wpdb->insert( $Yz_media_table, $args );

		}

		if ( $result ) {
			// Return ID.
			return $wpdb->insert_id;
		}


		return false;

	}

	/**
	 * Move Temporary Files To The Main Attachments Directory.
	 */
    function move_attachments( $attachments ) {
    	
    	global $YZ_upload_dir;

    	// Get Maximum Files Number.
	    $max_files = yz_options( 'yz_attachments_max_nbr' );

		// Check attachments files number.	
	    if ( count( $attachments ) > $max_files ) {
			$data['error'] = $this->msg( 'max_files' );
			die( json_encode( $data ) );
	    }

    	// New Attachments List.
    	$new_attachments = array();

		// Get File Path.
		$temp_path = $YZ_upload_dir . 'temp/' ;

 			// die( print_r( $attachments));
 		foreach ( $attachments as $attachment ) {
 			// Get File Data.
	    	$attachment = json_decode( stripcslashes( $attachment ), true );

	    	// Get File Names.
	    	$filename = $attachment[0]['original'];

			// Get Uploaded File extension
			$ext = strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) );

	        // Get File New Name.
	        $new_name = $filename;

			// Get Unique File Name for the file.
	        while ( file_exists( $YZ_upload_dir . $new_name ) ) {
				$new_name = uniqid( 'file_' ) . '.' . $ext;
			}

			// Get Files Path.
			$old_file = $temp_path . $filename;
			$new_file = $YZ_upload_dir . $new_name; 

			// Move File From Temporary Directory to the Main Directory.
	        if ( file_exists( $old_file ) && rename( $old_file, $new_file ) ) {

	        	// Get Attachment Data.
	        	$atts_data = array( 
	        		'original' => $new_name,
	        		'real_name' => isset( $attachment[0]['real_name'] ) ? $attachment[0]['real_name'] : $new_name,
	        		'file_size' => isset( $attachment[0]['file_size'] ) ? $attachment[0]['file_size'] : 0,
	        		'size' 		=> isset( $attachment[0]['size'] ) ? $attachment[0]['size'] : ''
	        	);

	        	// Get Attchment Thumbnail.
	        	$atts_data['thumbnail'] = yz_save_image_thumbnail( $atts_data );

	        	$new_attachments[] = $atts_data;
	        }

 		}

		// Serialize Attachments.
		$new_attachments = ! empty( $new_attachments ) ? serialize( $new_attachments ) : false;
		
 		return $new_attachments;
    }

	/**
	 * #  Upload Attachment.
	 */
    function upload_attachments( $manual_files = null ) {

		/**
		 * These functions are for future debuging purpose :
		 *  echo json_encode( $uploaded_files ); // die( json_encode( array('error' => 'ok' ) ) );
		*/
		
    	global $Youzer, $YZ_upload_dir;

		// Before Upload User Files Action.
		do_action( 'yz_before_upload_wall_files' );

		// Check Nonce Security
		check_ajax_referer( 'yz_post_update', 'security' );

		// Get Files.
		$files = ! empty( $manual_files )? $manual_files : $_FILES;

	    if ( ! function_exists( 'wp_handle_upload' ) ) {
	        require_once( ABSPATH . 'wp-admin/includes/file.php' );
	    }

	    $upload_overrides = array( 'test_form' => false );

	    // Get Max File Size in Mega.
	    $max_size = yz_options( 'yz_attachments_max_size' );

		// Set max file size in bytes.
		$max_file_size = apply_filters( 'yz_wall_attachments_max_size', $max_size * 1048576 );

	    // Change Default Upload Directory to the Plugin Directory.
		add_filter( 'upload_dir' , 'yz_temporary_upload_directory' );

		// Create New Array
		$uploaded_files = array();

	    foreach ( $files as $file ) :

			// Get Uploaded File extension
			$ext = strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );

			// Check File has the Right Extension.
			if ( ! $this->validate_file_extension( $ext ) ) {
				$data['error'] = $this->msg( 'unpermitted_extension' );
				die( json_encode( $data ) );
			}

			// Check that the file is not too big.
		    if ( $file['size'] > $max_file_size ) {
				$data['error'] = $this->msg( 'max_size' );
				die( json_encode( $data ) );
		    }

			if ( $file['name'] ) {

				// Get Unique File Name.
				$filename = uniqid( 'file_' ) . '.' . $ext;


				// Get File Link.
				$file_link = $YZ_upload_dir . 'temp/' . $filename;

				// Get Unique File Name for the file.
		        while ( file_exists( $file_link ) ) {
					$filename = uniqid( 'file_' ) . '.' . $ext;
				}

				$uploadedfile = array( 
				    'name'     => apply_filters( 'yz_wall_attachment_filename', $filename, $ext ),
				    'size'     => $file['size'],
				    'type'     => $file['type'],
				    'error'    => $file['error'],
				    'tmp_name' => $file['tmp_name']
				);

		        // Upload File.
		        $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );

		        // Get Files Data.
		        if ( $movefile && ! isset( $movefile['error'] ) ) {
		        	
		        	$file_size = getimagesize( $file_link );
		        	$file_data['real_name'] = $file['name'];
		        	$file_data['file_size'] = $file['size'];
		        	$file_data['original'] = basename( $movefile['url'] );

		        	if ( ! empty( $file_size ) ) {
		        		$file_data['size'] = array( 'width' => $file_size[0], 'height' => $file_size[1] );
		        	}

		        	$uploaded_files[] = $file_data;
		        }

	    	}

	    endforeach;
		


	    // Change Upload Directory to the Default Directory .
		remove_filter( 'upload_dir' , 'yz_temporary_upload_directory' );

		if ( empty( $manual_files ) ) {
			echo json_encode( $uploaded_files );
			die();
		} else {
			return $uploadedfiles;
		}
    }

	/**
	 * #  Delete Attachment.
	 */
    function delete_attachment() {

    	global $YZ_upload_dir;

    	// Get Attachment File Name.
    	$filename = $_POST['attachment'];

		// Before Delete Attachment Action.
		do_action( 'yz_before_delete_attachment' );

		// Check Nonce Security
		check_ajax_referer( 'yz_post_update', 'security' );

		// Get Uploads Directory Path.
		$upload_dir = wp_upload_dir();

		// Get File Path.
		$file_path = $YZ_upload_dir . 'temp/' . wp_basename( $filename );

		// Delete File.
		if ( file_exists( $file_path ) ) {
			unlink( $file_path );
		}

		die();
    }

    /**
     * Delete Activity Attachments.
     */
    function delete_attachments( $args ) {

		// Get Activity Attachments.
		$attachments = yz_get_activity_attachments( $args['id'] );

    	// Check if the activity contains Attachments.
		if ( empty( $attachments ) ) {
			return;
		}

    	global $wpdb, $Yz_media_table;

		// Delete All Activity Attachments.
		$result = $wpdb->delete( $Yz_media_table, array( 'item_id' => $args['id'] ), array( '%d' ) );

		if ( $result ) {
			$this->delete_folder_attachments( $attachments );
		}

		return $result;

    }

    /**
     * Delete Attachments By Media ID.
     */
    function delete_attachments_by_media_id( $media_id = null ) {

    	global $wpdb, $Yz_media_table;

    	// Get Att
    	$attachments = yz_get_activity_attachments_by_media_id( $media_id );

    	if ( is_array( $media_id ) ) {
			// Delete Media Records.
			foreach ( $media_id as $id ) {
				$result = $wpdb->delete( $Yz_media_table, array( 'id' => $id ), array( '%d' ) );
			}
    	} else {
			$result = $wpdb->delete( $Yz_media_table, array( 'id' => $media_id ), array( '%d' ) );
    	}

		if ( $result ) {
			$this->delete_folder_attachments( $attachments );
		}

    }

    /**
     * Delete Attachments from Upload Folder.
     */
    function delete_folder_attachments( $attachments ) {

    	global $YZ_upload_dir;

		// Delete Attachments from the upload folder.
		foreach ( $attachments as $file ) {

			foreach ( $file as $file_name ) {

				if ( empty( $file_name ) ) {
					continue;
				}

				$file_path = $YZ_upload_dir . $file_name;

				// Delete File.
				if ( file_exists( $file_path ) ) {
					unlink( $file_path );
				}

			}

		}
	
    }

    /**
     * Validate file extension.
     */
    function validate_file_extension( $file_ext ) {
       
	   // Get a list of allowed mime types.
	   $mimes = get_allowed_mime_types();
	   
	    // Loop through and find the file extension icon.
	    foreach ( $mimes as $type => $mime ) {
	      if ( false !== strpos( $type, $file_ext ) ) {
	          return true;
	        }
	    }
	    
	    return false;
	}

	/**
	 * Add 'user uploaded new avatar' Post.
	 */
	function set_new_avatar_activity( $activity ) {
		
		if ( 'new_avatar' != $activity->type ) {
			return false;
		}

		// Get User Avatar.
		$avatar_url = bp_core_fetch_avatar( 
			array(
				'item_id' => $activity->user_id,
				'type'	  => 'full',
				'html' 	  => false,
			)
		);

		// Get Avatars Path.
		$avatars_path = xprofile_avatar_upload_dir();

		// Get Avatar.
		$bp_avatar = $avatars_path['path'] . '/' . basename( $avatar_url );

		// Get Cover New Url.
		$avatar_url = yz_copy_image_to_youzer_directory( $bp_avatar );

		if ( $avatar_url ) {

			// Get Avatar Args.
			$avatar_args[0] = $this->get_image_args( $avatar_url );

			// Save Attachment.
			$this->save_activity_attachments( $activity->id, $avatar_args );

		}

	}

	/**
	 * Add 'User Uploaded New Cover' Post.
	 */
	function set_new_cover_activity( $item_id ) {
		
		if ( ! bp_is_active( 'activity' ) ) {
			return;
		}

		// Get Activitiy ID.
		$activity_id = bp_activity_add(
			array(
				'type'      => 'new_cover',
				'user_id'   => bp_displayed_user_id(),
				'component' => buddypress()->activity->id,
			)
		);

		// Get Cover Photo Path.
	    $cover_path = bp_attachments_get_attachment( 'path', array(
		        'item_id' => $item_id,
		        'object_dir' => 'members'
	        )
	    );

		// Get Cover New Url.
		$cover_url = yz_copy_image_to_youzer_directory( $cover_path );

		// Save Cover Url.
		if ( $cover_url ) {

			$cover_args[0] = $this->get_image_args( $cover_url );

			// Save Attachment.
			$this->save_activity_attachments( $activity_id, $cover_args );

		}

	}

	/**
	 * Get Image Args For Media Database
	 */
	function get_image_args( $image_url ) {

		global $YZ_upload_dir;

		$image_name = basename( $image_url );

		$image_path = $YZ_upload_dir . $image_name;

		// Get Avatar Args.
		$args = array( 'original' => $image_name, 'file_size' => filesize( $image_path ), 'real_name' => $image_name );
		
		// Get File Size			
		$file_size = getimagesize( $image_path );

    	if ( ! empty( $file_size ) ) {
    		$args['size'] = array( 'width' => $file_size[0], 'height' => $file_size[1] );
    	}

    	return $args;

	}

}