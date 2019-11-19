<?php

/**
 * # Add Patches Settings Tab
 */
function yz_patches_settings_menu( $tabs ) {

	$tabs['patches'] = array(
   	    'id'    => 'patches',
   	    // 'hide_menu' => true,
   	    'icon'  => 'fas fa-magic',
   	    'function' => 'yz_patches_settings',
   	    'title' => __( 'Patches settings', 'youzer' ),
    );
    
    return $tabs;

}

add_filter( 'yz_panel_general_settings_menus', 'yz_patches_settings_menu', 9999 );

/**
 * # Add Patches Settings Tab
 */
function yz_patches_settings() {

	do_action( 'yz_patches_settings' );
	wp_enqueue_script( 'jquery' );

	?>

	<script type="text/javascript">
	
	( function( $ ) {

		/**
		 * Process Updating Fields.
		 */
		$.yzc_patch_process_step = function( current_button, action, step, perstep, total, self ) {
					console.log ( action );
					console.log ( step );
					console.log ( perstep );
					console.log ( total );
			$.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {
					action: action,
					step: step,
					total: total,
					perstep: perstep,
				},
				dataType: "json",
				success: function( response ) {
					console.log( response );
					if ( 'done' == response.step ) {

						current_button.addClass( 'yz-is-updated' );

						// window.location = response.url;
						current_button.html( '<i class="fas fa-check"></i>Done !' );

					} else {

						current_button.find( '.yz-button-progress' ).animate({
							width: response.percentage + '%',
						}, 50, function() {
							// Animation complete.
						});

						var total_items = ( response.step * response.perstep ) - response.perstep,
							items = total_items < response.total ? total_items : response.total;

						current_button.find( '.yz-items-count' ).html( items );

						$.yzc_patch_process_step( current_button, action, parseInt( response.step ), parseInt( response.perstep ), parseInt( response.total ), self );

					}

				}
			}).fail( function ( response ) {
				if ( window.console && window.console.log ) {
					console.log( response );
				}
			});

		}

		$( 'body' ).on( 'click', 'a[data-run-patch="true"]', function(e) {

			if ( $( this ).hasClass( 'yz-is-updated' ) ) {
				return;
			}

			e.preventDefault();

			var per_step = $( this ).data( 'perstep' );
			var total = $( this ).data( 'total' );
			var action = $( this ).data( 'action' );

			$( this ).html( '<i class="fas fa-spinner fa-spin"></i>Updating <div class="yz-button-progress"></div><span class="yz-items-count">' + 1 + '</span>' + ' / ' + total + ' ' + $( this ).data( 'items' ) );

			// Start The process.
			$.yzc_patch_process_step( $( this ), action, 1, per_step, total, self );

		});

	})( jQuery );

	</script>

	<?php	

}

/**
 * Move WP Fields To Buddypress Xprofile Fields
 */
function yz_patche_move_wp_fields_to_bp_settings() {

    if ( ! get_option( 'install_youzer_2.1.5_options' ) ) {
        return false;
    }

    global $Yz_Settings;
	
	$already_installed = is_multisite() ? get_blog_option( BP_ROOT_BLOG, 'yz_patch_move_wptobp' ) : get_option( 'yz_patch_move_wptobp' );
	
	$patche_status = $already_installed ? '<span class="yz-title-status">' . __( 'Installed', 'youzer' ) . '</span>' : '';

    $Yz_Settings->get_field(
        array(
            'title' => sprintf( __( 'Move WP Fields To Buddypress Xprofile Fields. %s', 'youzer' ), $patche_status ),
            'type'  => 'openBox'
        )
    );
    
    // Get User Total Count.
	$user_count_query = count_users();
	$button_args = array(
    	'class' => 'yz-wild-field',
        'desc'  => __( 'This is a required step to move all the previous profile & contact fields values to the new generated xprofile fields. please note that this operation might take a long time to finish because it will go through all the users in database one by one and update their fields.', 'youzer' ),
        'button_title' => __( 'Update Fields', 'youzer' ),
        'button_data' => array(
        	'step' => 1,
        	'total' => $user_count_query['total_users'],
        	'perstep' => apply_filters( 'yz_patch_move_wptobp_per_step', 5 ),
        ),
        'id'    => 'yz-run-wptobp-patch',
        'type'  => 'button'
    );
	
	if ( $already_installed ) {
		unset( $button_args['button_title'] );
	}

    $Yz_Settings->get_field( $button_args );

	// Check is Profile Fields Are Installed.
    $xprofile_fields_installed = is_multisite() ? get_blog_option( BP_ROOT_BLOG, 'yz_install_xprofile_groups' ) : get_option( 'yz_install_xprofile_groups' );

    if ( ! $xprofile_fields_installed ) {

	    // Include Setup File.    
	    require_once dirname( YOUZER_FILE ) .  '/includes/public/core/class-yz-setup.php';
	    
	    // Init Setup Class.
	    $Youzer_Setup = new Youzer_Setup();

	    // Install Xprofile Fields.
	    $Youzer_Setup->create_xprofile_groups();

    }
    
    $Yz_Settings->get_field( array( 'type' => 'closeBox' ) );

	?>

	<script type="text/javascript">
( function( $ ) {

        jQuery( document ).ready( function(){

		/**
		 * Process Updating Fields.
		 */
		$.process_step  = function( step, perstep, total, self ) {

			$.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {
					action: 'yz_patche_move_wp_fields_to_bp',
					step: step,
					total: total,
					perstep: perstep,
				},
				dataType: "json",
				success: function( response ) {

					var export_form = $( '#yz-run-wptobp-patch' );

					if ( 'done' == response.step ) {

						export_form.addClass( 'yz-is-updated' );

						// window.location = response.url;
						export_form.html( '<i class="fas fa-check"></i>Done !' );

					} else {

						$('.yz-button-progress').animate({
							width: response.percentage + '%',
						}, 50, function() {
							// Animation complete.
						});

						var total_users = ( response.step * response.perstep ) - response.perstep,
							users = total_users < response.total ? total_users : response.total;

						export_form.find( '.yz-items-count' ).html(users);

						$.process_step( parseInt( response.step ), parseInt( response.perstep ), parseInt( response.total ), self );

					}

				}
			}).fail( function ( response ) {
				if ( window.console && window.console.log ) {
					console.log( response );
				}
			});

		}

		$( 'body' ).on( 'click', '#yz-run-wptobp-patch', function(e) {

			if ( $( this ).hasClass( 'yz-is-updated' ) ) {
				return;
			}

			e.preventDefault();

			var per_step = $( this ).data( 'perstep' );
			var total = $( this ).data( 'total' );

			$( this ).html( '<i class="fas fa-spinner fa-spin"></i>Updating <div class="yz-button-progress"></div><span class="yz-items-count">' + 1 + '</span>' + ' / ' + total + ' Users' );

			// Start The process.
			$.process_step( 1, per_step, total, self );

		});

	});

})( jQuery );
	</script>

	<?php
}

add_action( 'yz_patches_settings', 'yz_patche_move_wp_fields_to_bp_settings', 99 );


/**
 * Process batch exports via ajax
 */
function yz_patche_move_wp_fields_to_bp() {
	
	// Init Vars.	
	$total = isset( $_POST['total'] ) ? absint( $_POST['total'] ): null;
	$step = isset( $_POST['step'] ) ? absint( $_POST['step'] ) : null;
	$perstep = isset( $_POST['perstep'] ) ? absint( $_POST['perstep'] ) : null;

	// $ret = $export->process_step( $step );
	$ret = yz_patch_move_wptobp_process_step( $step, $perstep, $total );

	// Get Percentage.
	$percentage = ( $step * $perstep / $total ) * 100;

	if ( $ret ) {
		$step += 1;
		echo json_encode( array( 'users' => $ret, 'step' => $step, 'total'=> $total, 'perstep' => $perstep, 'percentage' => $percentage ) ); exit;
	} else {
		echo json_encode( array( 'step' => 'done' ) ); exit;
	}

}

add_action( 'wp_ajax_yz_patche_move_wp_fields_to_bp', 'yz_patche_move_wp_fields_to_bp' );


function yz_patch_move_wptobp_process_step( $step, $per_step, $total  ) {
	
	// Init Vars
	$more = false;
	// $done = false;
	$i      = 1;
	$offset = $step > 1 ? ( $per_step * ( $step - 1 ) ) : 0;

	$done = $offset > $total ? true :  false;
	
	if ( ! $done ) {

		$more = true;

		// main user query
		$args  = array(
		    'fields'    => 'id',
		    'number'    => $per_step,
		    'offset'    => $offset // skip the number of users that we have per page  
		);

		// Get the results
		$authors = get_users( $args );		

	    // Get Profile Fields.
		$profile_fields = is_multisite() ? get_blog_option( BP_ROOT_BLOG, 'yz_xprofile_contact_info_group_ids' ) : get_option( 'yz_xprofile_contact_info_group_ids' );
		$contact_fields = is_multisite() ? get_blog_option( BP_ROOT_BLOG, 'yz_xprofile_profile_info_group_ids' ) : get_option( 'yz_xprofile_profile_info_group_ids' );

		$all_fields = (array) $contact_fields + (array) $profile_fields;
			
		// Remove Group ID Field.
		unset( $all_fields['group_id'] );

		// Update Fields.
		foreach ( $authors as $user_id ) {
				
			foreach ( $all_fields as $user_meta => $field_id ) {
				
				// Get Old Value.
				$old_value = get_the_author_meta( $user_meta, $user_id );
				
				if ( empty( $old_value ) ) {
					continue;
				}

				// Set New Value.
		        xprofile_set_field_data( $field_id, $user_id, $old_value );

		        // Delete Old Value.
				// delete_user_meta( $user_id, $user_meta );
			
			}

		}

	} else {

	    if ( is_multisite() ) {
	        update_blog_option( BP_ROOT_BLOG, 'yz_patch_move_wptobp', true );
	    } else {
	        update_option( 'yz_patch_move_wptobp', true );
	    }

	}

	return $more;
}

/**
 * Move to the new media system
 **/

/***
 * check for youzer then check if the user media tables are installed.
 */
add_action( 'yz_patches_settings', 'yz_patch_move_to_new_media_system' );

function yz_patch_move_to_new_media_system() {


    // if ( ! get_option( 'yz_patch_new_media_system' ) ) {
    //     return false;
    // }
    global $Yz_Settings, $wpdb, $bp;

	$already_installed = is_multisite() ? get_blog_option( BP_ROOT_BLOG, 'yz_patch_new_media_system' ) : get_option( 'yz_patch_new_media_system' );

	$total = $wpdb->get_var( "SELECT count(*) FROM {$bp->activity->table_name} WHERE type IN ( 'activity_status', 'activity_photo', 'activity_link', 'activity_slideshow', 'activity_quote', 'activity_video', 'activity_audio', 'activity_file', 'new_cover', 'new_avatar')" );


	if ( ! $already_installed && $total == 0 ) {

	    if ( is_multisite() ) {
	        update_blog_option( BP_ROOT_BLOG, 'yz_patch_new_media_system', true );
	    } else {
	        update_option( 'yz_patch_new_media_system', true );
	    }
			
		// Install New Widget.
		$overview_widgets = yz_options( 'yz_profile_main_widgets' );
		$sidebar_widgets  = yz_options( 'yz_profile_sidebar_widgets' );
		$all_widgets      = array_merge( $overview_widgets, $sidebar_widgets );
		
		$install_widget = true;

		foreach ( $all_widgets as $widget ) {
			if ( key( $widget ) == 'wall_media' )  {
				$install_widget = false;
			}
		}

		if ( $install_widget == true ) {
			$sidebar_widgets[] = array( 'wall_media' => 'visible' );
			update_option( 'yz_profile_sidebar_widgets', $sidebar_widgets );
		}

	}

	$already_installed = is_multisite() ? get_blog_option( BP_ROOT_BLOG, 'yz_patch_new_media_system' ) : get_option( 'yz_patch_new_media_system' );
	
	
	$patche_status = $already_installed ? '<span class="yz-title-status">' . __( 'Installed', 'youzer' ) . '</span>' : '';

    $Yz_Settings->get_field(
        array(
            'title' => sprintf( __( 'Migrate to the new media system. %s', 'youzer' ), $patche_status ),
            'type'  => 'openBox'
        )
    );

	$button_args = array(
    	'class' => 'yz-wild-field',
        'desc'  => __( 'Please note that this operation might take a long time to finish because it will move all the old activity posts media ( images, videos, audios, files ) to a new database more organized and structured.<br><span style="color: red;text-transform: initial;">Make sure to enable the following functions on your server before running this patch : <b>CURL</b> and <b>getimagesize</b></span>', 'youzer' ),
        'button_title' => __( 'Migrate Now', 'youzer' ),
        'button_data' => array(
        	'run-patch' => 'true',
        	'step' => 1,
        	'items' => 'Posts',
        	'action' => 'yz_patche_move_to_new_media',
        	'total' => $total,
        	'perstep' => apply_filters( 'yz_patch_move_wptobp_per_step', 10 ),
        ),
        'id'    => 'yz-run-media-patch',
        'type'  => 'button'
    );
	
	if ( $already_installed ) {
		unset( $button_args['button_title'] );
	}

    $Yz_Settings->get_field( $button_args );

	// Check is Profile Fields Are Installed.
    $xprofile_fields_installed = is_multisite() ? get_blog_option( BP_ROOT_BLOG, 'yz_is_media_table_installed' ) : get_option( 'yz_is_media_table_installed' );

    if ( ! $xprofile_fields_installed ) {

	    // Include Setup File.    
	    require_once dirname( YOUZER_FILE ) .  '/includes/public/core/class-yz-setup.php';
	    
	    // Init Setup Class.
	    $Youzer_Setup = new Youzer_Setup();

	    // Build Database.
	    $Youzer_Setup->build_database_tables();

    }

    $Yz_Settings->get_field( array( 'type' => 'closeBox' ) );

}


/**
 * Migrating Ajax Call.
 */
function yz_patche_move_to_new_media_ajax() {
	
	// Init Vars.	
	$total = isset( $_POST['total'] ) ? absint( $_POST['total'] ): null;
	$step = isset( $_POST['step'] ) ? absint( $_POST['step'] ) : null;
	$perstep = isset( $_POST['perstep'] ) ? absint( $_POST['perstep'] ) : null;

	$ret = yz_patche_move_to_new_media_process_step( $step, $perstep, $total );

	// Get Percentage.
	$percentage = ( $step * $perstep / $total ) * 100;

	if ( $ret ) {
		$step += 1;
		echo json_encode( array( 'users' => $ret, 'step' => $step, 'total'=> $total, 'perstep' => $perstep, 'percentage' => $percentage ) ); exit;
	} else {
		echo json_encode( array( 'step' => 'done' ) ); exit;
	}

}

add_action( 'wp_ajax_yz_patche_move_to_new_media', 'yz_patche_move_to_new_media_ajax' );


/**
 * Migration Process.
 */
function yz_patche_move_to_new_media_process_step( $step, $per_step, $total  ) {
	// Init Vars
	$more = false;
	$i      = 1;
	$offset = $step > 1 ? ( $per_step * ( $step - 1 ) ) : 0;

	$done = $offset > $total ? true :  false;
	
	if ( ! $done ) {

		$more = true;

		global $bp, $wpdb;

	    // Get Global Request
		$posts = $wpdb->get_results( "SELECT id, content, type, date_recorded FROM {$bp->activity->table_name} WHERE type IN ( 'activity_status', 'activity_photo', 'activity_link', 'activity_slideshow', 'activity_quote', 'activity_video', 'activity_audio', 'new_avatar', 'new_cover', 'activity_file' ) LIMIT $per_step OFFSET $offset", ARRAY_A );
		
		if ( empty( $posts ) ) {
			return false;
		}
	

		global $YZ_upload_dir;

		// Image Extensions
		$images_ext = array( 'jpeg', 'jpg', 'png', 'gif' );

		foreach ( $posts as $post ) {

			$atts = array();
		
			// Get Attachments
			if ( $post['type'] == 'new_avatar' ) {

				$avatar = bp_activity_get_meta( $post['id'], 'yz-avatar' );

				if ( empty( $avatar ) ) {
					continue;
				}

				$atts[0] = yz_patch_move_media_get_image_args( $avatar );

				// yz_write_log( $atts );

			} elseif( $post['type'] == 'new_cover' ) {

				$cover = bp_activity_get_meta( $post['id'], 'yz-cover-image' );

				if ( empty( $cover ) ) {
					continue;
				}

				$atts[0] = yz_patch_move_media_get_image_args( $cover );
				
			} elseif( $post['type'] == 'activity_status' ) {

				if ( empty( $post['content'] ) ) {
					continue;
				}

				$embed_exists = false;

				$supported_videos = yz_attachments_embeds_videos();
			
				// Get Post Urls.
				if ( preg_match_all( '#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $post['content'], $match ) ) {

					foreach ( array_unique( $match[0] ) as $link ) {

						foreach ( $supported_videos as $provider => $domain ) {

							$video_id = yz_get_embed_video_id( $provider, $link );

							if ( ! empty( $video_id ) ) {
								
								$embed_exists = true;

								$video_data = array( 'provider' => $provider, 'original' => $video_id );

								$thumbnails = yz_get_embed_video_thumbnails( $provider, $video_id );

								if ( ! empty( $thumbnails ) ) {
									$video_data['thumbnail'] = $thumbnails;
								}

								$atts[] = $video_data;
							}

						}

					}

				}

				// Change Activity Type from status to video.
				if ( $embed_exists ) {
					$activity = new BP_Activity_Activity( $post['id'] );
					$activity->type = 'activity_video';
					$activity->save();
				}

			} else {
				$atts = bp_activity_get_meta( $post['id'], 'attachments' );
			}

			if ( empty( $atts ) ) {
				continue;
			}

			$atts = maybe_unserialize( $atts );

			foreach ( $atts as $attachment ) {

				// yz_write_log( $attachment );

				// Get Data.
				// $data = array( 'file_size' => $attachment['file_size'] );

				$original_image = isset( $attachment['original'] ) ? $attachment['original'] : ( isset( $attachment['file_name'] ) ? $attachment['file_name'] :  '' );

				if ( empty( $original_image ) ) {
					continue;
				}

				$src = array( 'original' => $original_image );

				$thumbnail_image = isset( $attachment['thumbnail'] ) && file_exists( $YZ_upload_dir . $attachment['thumbnail'] ) ? $attachment['thumbnail'] : '';
				
				if ( ! empty( $thumbnail_image ) ) {
					$src['thumbnail'] = $thumbnail_image;
				}

				// Add Video Provider if Found.
				if ( isset( $attachment['provider'] ) ) {
					$src['provider'] = $attachment['provider'];
				}

				if ( $post['type'] == 'activity_video' && ! isset( $src['provider'] ) ) {
					$src['provider'] = 'local';
				}

				$ext = strtolower( pathinfo( $original_image, PATHINFO_EXTENSION ) );

				// Add Image Resolutions.
				if ( ! in_array( $post['type'], array( 'activity_audio', 'activity_video', 'activity_file', 'activity_status' ) ) && in_array( $ext, $images_ext ) ) {
					$attachment['size'] = yz_get_image_size( $original_image );
				}

				if ( isset( $attachment['original'] ) ) {
					unset( $attachment['original'] );
				}

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
					'item_id' => $post['id'],
					'privacy' => 'public',
					'time' => $post['date_recorded'],
				);

				// Set New Hashtag Count.	
				$result = $wpdb->insert( $wpdb->prefix . 'yz_media', $args );

			}

		}

		// Delete Old Value Here.

	} else {

	    if ( is_multisite() ) {
	        update_blog_option( BP_ROOT_BLOG, 'yz_patch_new_media_system', true );
	    } else {
	        update_option( 'yz_patch_new_media_system', true );
	    }
			
		// Install New Widget.
		$overview_widgets = yz_options( 'yz_profile_main_widgets' );
		$sidebar_widgets  = yz_options( 'yz_profile_sidebar_widgets' );
		$all_widgets      = array_merge( $overview_widgets, $sidebar_widgets );
		
		$install_widget = true;

		foreach ( $all_widgets as $widget ) {
			if ( key( $widget ) == 'wall_media' )  {
				$install_widget = false;
			}
		}

		if ( $install_widget == true ) {
			$sidebar_widgets[] = array( 'wall_media' => 'visible' );
			update_option( 'yz_profile_sidebar_widgets', $sidebar_widgets );
		}

	}

	return $more;
}


function yz_patch_move_media_get_image_args( $image_url ) {

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



/***
 * Optimize Data Baze.
 */
// add_action( 'yz_patches_settings', 'yz_patch_optimize_database' );

function yz_patch_optimize_database() {


    // if ( ! get_option( 'yz_patch_new_media_system' ) ) {
    //     return false;
    // }
delete_option( 'yz_patch_optimize_database' );
    global $Yz_Settings, $wpdb;
	
	$already_installed = is_multisite() ? get_blog_option( BP_ROOT_BLOG, 'yz_patch_optimize_database' ) : get_option( 'yz_patch_optimize_database' );
	
	$patche_status = $already_installed ? '<span class="yz-title-status">' . __( 'Installed', 'youzer' ) . '</span>' : '';

    $Yz_Settings->get_field(
        array(
            'title' => sprintf( __( 'Optimize Youzer Database. %s', 'youzer' ), $patche_status ),
            'type'  => 'openBox'
        )
    );

	$total = $wpdb->get_var( "SELECT COUNT(*) FROM " . $wpdb->prefix . "options WHERE autoload = 'yes' AND option_name Like 'yz_%'" );

	$button_args = array(
    	'class' => 'yz-wild-field',
        'desc'  => __( 'Before many youzer options were called on all the pages by running this patch they will be called only when needed. This operation will increase your website pages speed.', 'youzer' ),
        'button_title' => __( 'Optimize Now', 'youzer' ),
        'button_data' => array(
        	'run-patch' => 'true',
        	'step' => 1,
        	'items' => 'Options',
        	'action' => 'yz_patch_optimize_database',
        	'total' => $total,
        	'perstep' => apply_filters( 'yz_patch_move_wptobp_per_step', $total ),
        ),
        'id'    => 'yz-run-optimize-database-patch',
        'type'  => 'button'
    );
	
	if ( $already_installed ) {
		unset( $button_args['button_title'] );
	}

    $Yz_Settings->get_field( $button_args );

    $Yz_Settings->get_field( array( 'type' => 'closeBox' ) );

    	// $step = 1;
    	// $per_step = 100;
    	// $offset = $step > 1 ? ( $per_step * ( $step - 1 ) ) : 0;


	    // Get Global Request
		// $options = $wpdb->get_results( "SELECT option_id FROM " . $wpdb->prefix . "options WHERE autoload = 'yes' AND option_name LIKE 'yz_%' LIMIT $per_step OFFSET $offset", ARRAY_A );

		// $options = wp_list_pluck( $options, 'option_id' );

		// print_r($options);
	    // Get Global Request
		$options = $wpdb->get_results( "SELECT option_id FROM " . $wpdb->prefix . "options WHERE autoload = 'yes' AND option_name Like 'yz_%'", ARRAY_A );
		$options = wp_list_pluck( $options, 'option_id' );

		echo implode(',', $options);
}


/**
 * Migrating Ajax Call.
 */
function yz_patche_optimize_database_ajax() {
	
	// Init Vars.	
	$total = isset( $_POST['total'] ) ? absint( $_POST['total'] ): null;
	$step = isset( $_POST['step'] ) ? absint( $_POST['step'] ) : null;
	$perstep = isset( $_POST['perstep'] ) ? absint( $_POST['perstep'] ) : null;

	$ret = yz_patche_optimize_database_process_step( $step, $perstep, $total );

	// Get Percentage.
	$percentage = ( $step * $perstep / $total ) * 100;

	if ( $ret ) {
		$step += 1;
		echo json_encode( array( 'users' => $ret, 'step' => $step, 'total'=> $total, 'perstep' => $perstep, 'percentage' => $percentage ) ); exit;
	} else {
		echo json_encode( array( 'step' => 'done' ) ); exit;
	}

}

add_action( 'wp_ajax_yz_patch_optimize_database', 'yz_patche_optimize_database_ajax' );


/**
 * Migration Process.
 */
function yz_patche_optimize_database_process_step( $step, $per_step, $total  ) {
	// Init Vars
	$more = false;
	$i      = 1;
	$offset = $step > 1 ? ( $per_step * ( $step - 1 ) ) : 0;

	$done = $offset > $total ? true :  false;
	
	if ( ! $done ) {

		$more = true;

		global $bp, $wpdb;

	    // Get Global Request
		$options = $wpdb->get_results( "SELECT option_id FROM " . $wpdb->prefix . "options WHERE autoload = 'yes' AND option_name LIKE 'yz_%' LIMIT $per_step OFFSET $offset", ARRAY_A );

		$options = wp_list_pluck( $options, 'option_id' );

		if ( empty( $options ) ) {
			return false;
		}

		// foreach ( $options as $option_id ) {
		// 	$wpdb->update( 
		// 		$wpdb->prefix . 'options', 
		// 		array( 
		// 			'autoload' => 'no',	// string
		// 		), 
		// 		array( 'option_id' => $option_id ), 
		// 		array( 
		// 			'%s',	// value1
		// 		), 
		// 		array( '%d' ) 
		// 	);
		// }

		// $sql = $wpdb->prepare(,  );
		// yz_write_log( $sql );

		$wpdb->query( "UPDATE " . $wpdb->prefix . "options SET autoload = 'no' WHERE option_name LIKE 'yz_%' " );

	} else {

	    if ( is_multisite() ) {
	        update_blog_option( BP_ROOT_BLOG, 'yz_patch_optimize_database', true );
	    } else {
	        update_option( 'yz_patch_optimize_database', true );
	    }

	}

	return $more;
}
