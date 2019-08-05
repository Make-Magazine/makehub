<?php

/**
 * # Save Settings.
 */

add_action( 'wp_ajax_youzer_profile_settings_save_data', 'yz_account_save_settings' );

function yz_account_save_settings() {
        
    // If its xprofile fields exit.
    if ( isset( $_POST['field_ids'] ) ) {
        return;
    }

    // if its not Profile Settings go out.
    if ( isset( $_POST['action'] ) && 'youzer_profile_settings_save_data' == $_POST['action'] ) {

    // Check Nonce Security
    check_admin_referer( 'yz_nonce_security', 'security' );

    // Before Save Settings Action.
    do_action( 'youzer_before_save_user_settings', $_POST );

    // Get Form Data
    $data = $_POST;

    unset( $data['security'], $data['action'] );
    
    $die = isset( $_POST['die'] ) ? true : false;

    // Delete Photo
    if ( isset( $data['delete_photo'] ) && ! empty( $data['delete_photo'] ) ) {
        $die = isset( $data['die_after_delete'] ) ? true : false;
        yz_account_delete_photo( $data['delete_photo'], $die );
    }

    // Call Update Profile Settings Function
    if ( isset( $data['youzer_options'] ) ) {
        yz_account_save_profile_settings( $data['youzer_options'] );
    }

    // Save Notification Settings
    if ( isset( $data['youzer_notifications'] ) ) {
        yz_account_save_notifications_settings( $data['youzer_notifications'] );
    }

    // Save Skills
    if ( isset( $data['yz_data']['yz-skills-data'] ) ) {
        yz_account_save_items(
            array(
                'option_name' => 'youzer_skills',
                'max_items'   => 'yz_wg_max_skills',
                'items'       => isset( $data['youzer_skills'] ) ? $data['youzer_skills'] : null,
                'data_keys'   => array( 'title', 'barpercent' )
            )
        );
    }

    // Save Services.
    if ( isset( $data['yz_data']['yz-services-data'] ) ) {
        yz_account_save_items(
            array(
                'option_name' => 'youzer_services',
                'max_items'   => 'yz_wg_max_services',
                'items'       => isset( $data['youzer_services'] ) ? $data['youzer_services'] : null,
                'data_keys'   => array( 'title' )
            )
        );
    }

    // Save Portfolio
    if ( isset( $data['yz_data']['yz-portfolio-data'] ) ) {
        yz_account_save_items(
            array(
                'option_name' => 'youzer_portfolio',
                'items'       => isset( $data['youzer_portfolio'] ) ? $data['youzer_portfolio'] : null,
                'max_items'   => 'yz_wg_max_portfolio_items',
                'data_keys'   => array( 'original', 'thumbnail' )
            ),
            $die
        );
    }

    // Save SlideShow
    if ( isset( $data['yz_data']['yz-slideshow-data'] ) ) {
        yz_account_save_items(
            array(
                'option_name' => 'youzer_slideshow',
                'items'       => isset( $data['youzer_slideshow'] ) ? $data['youzer_slideshow'] : null,
                'max_items'   => 'yz_wg_max_slideshow_items',
                'data_keys'   => array( 'original', 'thumbnail' )
            ),
            $die
        );
    }

    // After Save Settings Action.
    do_action( 'youzer_after_save_user_settings', $data );


    if ( ! $die ) {
        // Redirect.
        yz_account_redirect( 'success', __( 'Changes saved.', 'youzer' ) );
    }
    
    }

}

// Save Settings
add_action( 'init', 'yz_account_save_settings' );

/**
 * #  Save Profile Settings.
 */
function yz_account_save_profile_settings( $profile_options ) {

    if ( empty( $profile_options ) ) {
        return false;
    }

    $user_id = bp_displayed_user_id();

    // Remove Tags.
    if ( isset( $profile_options['wg_project_title'] ) ) {
        
        // Get Tags Fields.
        $tags_fields = array( 'wg_project_tags', 'wg_project_categories' );

        foreach ( $tags_fields as $tag_id ) {
            if ( ! isset( $profile_options[ $tag_id ] ) ) {
                delete_user_meta( $user_id, $tag_id ); 
            }
        }
        
    }

    foreach ( $profile_options as $option => $value ) {

        if ( ! is_array( $value ) ) {
            $the_value = stripslashes( $value );
        } else {
            $the_value = $value;
        }

        if ( isset( $option ) ) {

            // Check For Empty 
            if ( is_array( $the_value ) && isset( $the_value['original'] ) && empty( $the_value['original'] ) ) {
                $the_value = null;
            }

            if ( 'wg_flickr_account_id' == $option ) {

                // Delete Flickr ID.
                if ( empty( $the_value ) ) {
                    $update_options = delete_user_meta( $user_id, 'wg_flickr_account_id' );
                } else {
                    // Check Flickr ID format
                    if ( false === strpos( $the_value, '@N' ) ) {
                        yz_account_redirect( 'error', __( 'Flickr ID format is invalid', 'youzer' ) );
                    } else {
                        // Update Flickr
                        $update_options = update_user_meta( $user_id, $option, $the_value );
                    }   
                }

                if ( $update_options ) {
                    do_action( 'yz_after_saving_account_options', $user_id, $option, $the_value );
                }

            } else {
                // Update Options
                $update_options = update_user_meta( $user_id, $option, $the_value );
                if ( $update_options ) {
                    do_action( 'yz_after_saving_account_options', $user_id, $option, $the_value );
                }
            }

        } else {
            delete_user_meta( $user_id, $option );
        }

    }

}

/**
 * #  Save Options.
 */
function yz_account_save_items( $args, $die = null ) {

    // Get User ID
    $user_id = bp_displayed_user_id();

    // Get items Data.
    $items = ! empty( $args['items'] ) ? $args['items'] : null;

    if ( empty( $items ) ) {
        $update_option = delete_user_meta( $user_id, $args['option_name'] );
    } else {        

        // Get Maximum Number OF Allowed Items
        $max_items = yz_options( $args['max_items'] );

        // Re-order & Remove Empty Items
        $items = yz_account_filter_items( $items, $args['data_keys'] );

        // Save Options
        if ( count( $items ) <= $max_items ) {
            $update_option = update_user_meta( $user_id, $args['option_name'], $items );
        }
    }

    if ( $update_option ) {
        // Hook
        do_action( 'yz_account_save_widget_item', $user_id, $args['option_name'], $items );
        // Redirect.
        if ( ! $die ) {
            yz_account_redirect( 'success', __( 'Changes saved.', 'youzer' ) );
        }
    }

}

/**
 * #  Save Notifications Settings.
 */
function yz_account_save_notifications_settings( $notifications ) {

    // Init New Array();
    $bp_notification = array();

    // Change 'On' To 'Yes'. 
    foreach ( $notifications as $key => $value ) {

        // Get Notification Key
        $notification_key = str_replace( 'yz_', '', $key );

        // Get Notification Value.
        $bp_notification[ $notification_key ] = ( 'on' == $value ) ? 'yes': 'no'; 

    }

    // Update Buddypress Notification Settings.
    bp_settings_update_notification_settings( bp_displayed_user_id(), (array) $bp_notification );

    // Save Youzer Options
    yz_account_save_profile_settings( $notifications );

}

/**
 * #  Re-order & Remove Empty Items.
 */
function yz_account_filter_items( $items, $keys ) {
    
    // Re-Order Items
    $items = array_combine( range( 1, count( $items ) ), array_values( $items ) );
    
    // Remove Empty items
    foreach ( $items as $item_key => $item ) {
        foreach ( $keys as $key ) {
            if ( empty( $item[ $key ] ) ) {
                unset( $items[ $item_key ] );
            }
        }
    }
    
    return $items;
}

/**
 * #  Delete Photo.
 */
function yz_account_delete_photo( $photo, $die = false ) {

    // Before Delete Photo Action.
    do_action( 'yz_account_before_delete_photo' );

    // Get Photo Directory Path.
    $upload_dir = wp_upload_dir();
    // Get Photo Path.
    $photo_path = $upload_dir['basedir'] . '/youzer/' . wp_basename( $photo );
    // Delete Photo.
    if ( yz_is_image_exists( $photo ) ) {
        unlink( $photo_path );
    }

    do_action( 'yz_account_after_delete_photo' );
    
    // Return.
    if ( $die ) {
        die();
    }
}

/**
 * Redirect User.
 */
function yz_account_redirect( $action, $msg, $redirect_to = null ) {

    // Get Reidrect page.
    $redirect_to = ! empty( $redirect_to ) ? $redirect_to : yz_get_current_page_url();

    // Add Message.
    bp_core_add_message( $msg, $action );

    // Redirect User.
    bp_core_redirect( $redirect_to );

}

/**
 * Get Profile Settings Page Content.
 */
function yz_get_profile_settings_page() {
    bp_core_load_template( 'buddypress/members/single/plugins' );
}

/**
 * Get Change Avatar Template
 */
function yz_filter_change_avatar_template( $template ) {
    return 'members/single/plugins';
}


/**
 * Redirect User From Settings Pages
 */
function yz_redirect_user_from_settings_pages() {

    // Get Current Page Url.
    $current_page = yz_get_current_page_url();

    if ( ! is_user_logged_in() ) {

        // Get Login Page Url
        $login_url = yz_get_login_page_url();

        // Get Redirect Url.
        $redirect_url = add_query_arg( 'redirect_to', $_REQUEST['redirect_to'], $login_url );

    } else {

        // Get Displayed User ID.
        $user_id = bp_displayed_user_id();

        // Get Current User Domain
        $user_profile = bp_core_get_user_domain( $user_id );

        // Set User Profile as Redirect Page Url.
        $redirect_url = $user_profile;

    }

    // Redirect User.
    wp_redirect( $redirect_url );
   
    exit;
}