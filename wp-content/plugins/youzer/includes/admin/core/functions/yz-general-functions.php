<?php

/**
 * Check Is Youzer Panel Page.
 */
function is_youzer_panel_page( $page_name ) {

    // Is Panel.
    $is_panel = isset( $_GET['page'] ) && $_GET['page'] == $page_name ? true : false;

    return apply_filters( 'is_youzer_panel_page', $is_panel, $page_name );
}

/**
 * Check Is Youzer Panel Page.
 */
function is_youzer_panel_tab( $tab_name ) {

    // Is Panel.
    $is_tab = isset( $_GET['tab'] ) && $_GET['tab'] == $tab_name ? true : false;

    return apply_filters( 'is_youzer_panel_tab', $is_tab, $tab_name );
}

/**
 * Top Bar Youzer Icon Css
 */
function yz_bar_icons_css() {

    // Show "Youzer Panel" Bar Icon
    if ( is_super_admin() ) {

        echo '<style>
            #adminmenu .toplevel_page_youzer-panel img {
                padding-top: 5px !important;
            }
            </style>';
    }

}

add_action( 'wp_head','yz_bar_icons_css' );
add_action( 'admin_head','yz_bar_icons_css' );


/**
* Add Documentation Submenu.
*/
function yz_add_documentation_submenu() {

    global $submenu;
    
    // Add Documentation Url
    $documentation_url = 'http://kainelabs.com/docs/youzer/';

    // Add Documentation Menu.
    $submenu['youzer-panel'][] = array(
        __( 'Documentation','youzer' ),
        'manage_options',
        $documentation_url
    );

}

add_action( 'admin_menu', 'yz_add_documentation_submenu', 9999 );

/**
 * Check if page is an admin page  tab
 */
function yz_is_panel_tab( $page_name, $tab_name ) {

    if ( is_admin() && isset( $_GET['page'] ) && isset( $_GET['tab'] ) && $_GET['page'] == $page_name && $_GET['tab'] == $tab_name ) {
        return true;
    }

    return false;
}


/**
 * Get Panel Profile Fields.
 */
function yz_get_panel_profile_fields() {

    // Init Panel Fields.
    $panel_fields = array();

    // Get All Fields.
    $all_fields = yz_get_all_profile_fields();

    foreach ( $all_fields as $field ) {

        // Get ID.
        $field_id = $field['id'];

        // Add Data.
        $panel_fields[ $field_id ] = $field['name'];

    }

    // Add User Login Option Data.
    $panel_fields['user_login'] = __( 'Username', 'youzer' );

    return $panel_fields;
}

/**
 * Get Panel Profile Fields.
 */
function yz_get_user_tags_xprofile_fields() {

    // Init Panel Fields.
    $xprofile_fields = array();

    // Get xprofile Fields.
    $fields = yz_get_bp_profile_fields();

    foreach ( $fields as $field ) {

        // Get ID.
        $field_id = $field['id'];

        // Add Data.
        $xprofile_fields[ $field_id ] = $field['name'];

    }

    return $xprofile_fields;
}

/**
 * Run WP TO BP Patch Notice.
 */
function yz_move_wp_fields_to_bp_notice() {

    $patch_url = add_query_arg( array( 'page' => 'youzer-panel&tab=patches' ), admin_url( 'admin.php' ) );

    $media_already_installed = is_multisite() ? get_blog_option( BP_ROOT_BLOG, 'yz_patch_new_media_system' ) : get_option( 'yz_patch_new_media_system' );

    if ( ! $media_already_installed ) { ?>

        <div class="notice notice-warning">
            <p><?php echo sprintf( __( "<strong>Youzer - New Media System Important Patch :<br> </strong>Please Run The Following Patch <strong><a href='%1s'>Migrate to The New Youzer Media System.</a></strong> This operation will move all the old activity posts media ( images, videos, audios, files ) to a new database more organized and structured.", 'youzer' ), $patch_url ); ?></p>
        </div>
        
        <?php

    }

    if ( get_option( 'install_youzer_2.1.5_options' ) ) {

        $already_installed = is_multisite() ? get_blog_option( BP_ROOT_BLOG, 'yz_patch_move_wptobp' ) : get_option( 'yz_patch_move_wptobp' );
        
        if ( ! $already_installed ) { ?>

        <div class="notice notice-warning">
            <p><?php echo sprintf( __( "<strong>Youzer - Important Patch :<br> </strong>Please Run The Following Patch <strong><a href='%1s'>Move Wordpress Fields To The Buddypress Xprofile Fields.</a></strong> This patch will move all the previews users fields values to the new created buddypress fields so now you can have the full control over profile info tab and contact info tab fields also : Re-order them, Control their visibility or even remove them if you want.</strong>", 'youzer' ), $patch_url ); ?></p>
        </div>
        
        <?php

        }
    }

}

add_action( 'admin_notices', 'yz_move_wp_fields_to_bp_notice' );

/**
 * Mark Xprofile Component as a "Must-Use" Component
 */
function yz_mark_xprofile_component_as_must_use( $components, $type ) {

    if ( 'required' == $type ) {
        
        $components['xprofile'] = array(
            'title'       => __( 'Extended Profiles', 'buddypress' ),
            'description' => __( 'Customize your community with fully editable profile fields that allow your users to describe themselves.', 'buddypress' )
        );

        $components['settings'] = array(
            'title'       => __( 'Account Settings', 'buddypress' ),
            'description' => __( 'Allow your users to modify their account and notification settings directly from within their profiles.', 'buddypress' )
        );

    }

    return $components;
}

add_filter( 'bp_core_get_components', 'yz_mark_xprofile_component_as_must_use', 10, 2 );

/**
 * New Extension Notice
 **/
function yz_display_new_extension_notice() {
    
    $yzea_notice = 'yz_hide_yzea_notice';
    $yzpc_notice = 'yz_hide_yzpc_notice';

    if ( isset( $_GET['yz-dismiss-extension-notice'] ) ) {

        if ( $_GET['yz-dismiss-extension-notice'] == $yzea_notice ) {
            update_option( $yzea_notice, 1 );   
        }

        if ( $_GET['yz-dismiss-extension-notice'] == $yzpc_notice ) {
            update_option( $yzpc_notice, 1 );   
        }

    }

    if ( ! get_option( $yzea_notice ) ) {

        $data = array(
            'notice_id' => $yzea_notice,
            'utm_campaign' => 'youzer-edit-activity',
            'utm_medium' => 'admin-banner',
            'utm_source' => 'clients-site',
            'title' => 'Youzer - Buddypress Edit Activity',
            'link' => 'https://www.kainelabs.com/downloads/buddypress-edit-activity/',
            'buy_now' => 'https://www.kainelabs.com/checkout/?edd_action=add_to_cart&download_id=22081&edd_options%5Bprice_id%5D=1',
            'image' => 'https://www.kainelabs.com/wp-content/uploads/edd/2019/05/Untitled-1-870x300.png',
            'description' => 'Allow members to edit their activity posts, comment and replies from the front-end with real time modifications. Set users that can edit their own activities and moderators by role and control editable activities by post type and set a timeout for how long they should remain editable and much more ...',
         );

        // Get Extension.
        yz_get_notice_addon( $data );
    }

    if ( ! get_option( $yzpc_notice ) ) {

        $data2 = array(
            'notice_id' => $yzpc_notice,
            'utm_campaign' => 'youzer-profile-completeness',
            'utm_medium' => 'admin-banner',
            'utm_source' => 'clients-site',
            'title' => 'Youzer - Buddypress Profile Completeness',
            'link' => 'https://www.kainelabs.com/downloads/buddypress-profile-completeness/',
            'buy_now' => 'https://www.kainelabs.com/?edd_action=add_to_cart&download_id=21146&edd_options%5Bprice_id%5D=1',
            'image' => 'https://www.kainelabs.com/wp-content/uploads/edd/2019/05/youzer-profile-completeness.png',
            'description' => 'Say good bye to the blank profiles, buddypress profile completeness is the best way to force or encourage users to complete their profile fields, profile widgets and more. also gives you the ability to apply restrictions on incomplete profiles.',
         );

        // Get Extension.
        yz_get_notice_addon( $data2 );
    }

}

add_action( 'admin_notices', 'yz_display_new_extension_notice' );


/**
 * Get Notice Add-on
 */
function yz_get_notice_addon( $data ) {
    ?>
    
    <style type="text/css">

        body .yz-addon-notice {
            padding: 0;
            border: none;
            overflow: hidden;
            box-shadow: none;
            margin-top: 15px;
            position: relative;
            margin-bottom: 15px;
        }

        .yz-addon-notice .yz-addon-notice-content {
            float: left;
            width: 80%;
            margin-left: 20%;
            padding: 25px 35px;
        }

        .yz-addon-notice .yz-addon-notice-img {
            display: block;    
            background-size: cover;
            background-position: center;
            float: left;
            width: 20%;
            height: 100%;
            position: absolute;
        }

        .yz-addon-notice .yz-addon-notice-title {
            font-size: 14px;
            font-weight: 600;
            color: #646464;
            margin-bottom: 10px;
        }

        .yz-addon-notice .yz-addon-notice-title .yz-addon-notice-tag {
            color: #fff;
            display: inline-block;
            text-transform: uppercase;
            font-weight: 600;
            margin-left: 8px;
            font-size: 10px;
            padding: 0px 8px;
            border-radius: 2px;
            background-color: #FFC107;
        }

        .yz-addon-notice .yz-addon-notice-description {
            font-size: 13px;
            color: #646464;
            line-height: 24px;
            margin-bottom: 15px;
        }

        .yz-addon-notice .yz-addon-notice-buttons a {
            color: #fff;
            font-weight: 600;
            padding: 8px 15px;
            border-radius: 2px;
            display: inline-block;
            vertical-align: middle;
            text-decoration: none;
        }
        
        .yz-addon-notice .notice-dismiss {
            text-decoration: none;
        }

        .yz-addon-notice .yz-addon-notice-buttons a.yz-addon-view-features {
            background-color: #03A9F4;
            margin-right: 8px;
        }

        .yz-addon-notice .yz-addon-notice-buttons a.yz-addon-buy-now {
            background-color: #8bc34a;
        }

    </style>

    <?php

        $link = $data['link'] .'?utm_campaign=' . $data['utm_campaign'] . '&utm_medium=' . $data['utm_medium'] . '&utm_source=' . $data['utm_source'] . '&utm_content=view-all-features';

        $buy = $data['buy_now'] .'&utm_campaign=' . $data['utm_campaign'] . '&utm_medium=' . $data['utm_medium'] . '&utm_source=' . $data['utm_source'] . '&utm_content=buy-now';

        ?>
    
    <div class="yz-addon-notice updated notice notice-success">
        <div class="yz-addon-notice-img" style="background-image:url(<?php echo $data['image']; ?>);"></div>
        <div class="yz-addon-notice-content">
            <div class="yz-addon-notice-title"><?php echo $data['title']; ?><span class="yz-addon-notice-tag">New</span></div>
            <div class="yz-addon-notice-description"><?php echo $data['description']; ?></div>
            <div class="yz-addon-notice-buttons">
                <a href="<?php echo $link; ?>" class="yz-addon-view-features">View All Features</a>
                <a href="<?php echo $buy;  ?>" class="yz-addon-buy-now">Buy Now</a>
            </div>
            <a href="<?php echo add_query_arg( 'yz-dismiss-extension-notice', $data['notice_id'], yz_get_current_page_url() ); ?>" type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></a>
        </div>
    </div>

    <?php
}