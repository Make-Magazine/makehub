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

add_action( 'admin_menu', 'yz_add_documentation_submenu', 100 );

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
    $yzbm_notice = 'yz_hide_yzbm_notice';
    $load_lightbox = false;
    if ( isset( $_GET['yz-dismiss-extension-notice'] ) ) {

        if ( $_GET['yz-dismiss-extension-notice'] == $yzea_notice ) {
            update_option( $yzea_notice, 1 );   
        }

        if ( $_GET['yz-dismiss-extension-notice'] == $yzpc_notice ) {
            update_option( $yzpc_notice, 1 );   
        }

        if ( $_GET['yz-dismiss-extension-notice'] == $yzbm_notice ) {
            update_option( $yzbm_notice, 1 );   
        }

    }

    if ( ! get_option( $yzea_notice ) ) {
        $load_lightbox = true;
        $data = array(
            'notice_id' => $yzea_notice,
            'utm_campaign' => 'youzer-edit-activity',
            'utm_medium' => 'admin-banner',
            'utm_source' => 'clients-site',
            'title' => 'Youzer - Buddypress Edit Activity',
            'link' => 'https://www.kainelabs.com/downloads/buddypress-edit-activity/',
            'buy_now' => 'https://www.kainelabs.com/checkout/?edd_action=add_to_cart&download_id=22081&edd_options%5Bprice_id%5D=1',
            'image' => 'https://www.kainelabs.com/wp-content/uploads/edd/2019/05/Untitled-1.png',
            'description' => 'Allow members to edit their activity posts, comment and replies from the front-end with real time modifications. Set users that can edit their own activities and moderators by role and control editable activities by post type and set a timeout for how long they should remain editable and much more ...',
            'features' => array(
                'Set Members That Can Edit Their Own Activities and Comments by Role.',
                'Set Editable Activities By Post Type.',
                'Set Moderators That Can Edit All The Site Activities by Role.',
                'Set Edit Button Timeout ( How long activities should remain editable ).',
                'Enable / Disable Attachments Edition.',
                'Enable / Disable Comments & Replies Edition.',
                'Real Time Modifications. No Refresh Page Required !'
            ),
            'images' => array(
                array( 'title' => 'Post & Comments Edit Buttons', 'link' => 'https://www.kainelabs.com/wp-content/uploads/edd/2019/05/normal-post.png' ),
                array( 'title' => 'Photos Post Edit Form', 'link' => 'https://www.kainelabs.com/wp-content/uploads/edd/2019/05/photospost.png' ),
                array( 'title' => 'Live URL Preview Post Edit Form', 'link' => 'https://www.kainelabs.com/wp-content/uploads/edd/2019/05/link.png' ),
                array( 'title' => 'Quote Post Edit Form', 'link' => 'https://www.kainelabs.com/wp-content/uploads/edd/2019/05/edit-quote-form.png' ),
                array( 'title' => 'Quote Post Edit Button', 'link' => 'https://www.kainelabs.com/wp-content/uploads/edd/2019/05/quote-post-edit-buttton.png' ),
                array( 'title' => 'Link Post Edit Form', 'link' => 'https://www.kainelabs.com/wp-content/uploads/edd/2019/05/link-post-edit.png' )
            )
         );

        // Get Extension.
        yz_get_notice_addon( $data );
    }

    if ( ! get_option( $yzpc_notice ) ) {
        $load_lightbox = true;
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
            'features' => array(
                '3 Fields Status ( Forced, Required, Optional ).',
                'Apply Profile Completeness System For Specific Roles.',
                'Enable / Disable Hiding Incomplete Profiles from Members Directory.',
                'Enable / Disable Marking Complete Profiles as Verified.',
                'Enable / Disable The following Actions For Incomplete Profiles : Posts, Comments, Replies, Follows, Groups, Messages ...',
                'Supported Fields : All Buddypress Fields, Youzer Widgets, Buddypress Avatar & Cover Images.',
                'Profile Completeness Shortcode : [youzer_profile_completeness].',
                'Ajaxed Profile Completeness Widget.'
            ),
            'images' => array(
                array( 'title' => 'Profile Completeness Widget', 'link' => 'https://www.kainelabs.com/wp-content/uploads/edd/2019/06/complete-profile.png' ),
                array( 'title' => 'Profile Completeness – Profile Info', 'link' => 'https://www.kainelabs.com/wp-content/uploads/edd/2019/06/profile-info.png' ),
                array( 'title' => 'Profile Completeness – Profile Images', 'link' => 'https://www.kainelabs.com/wp-content/uploads/edd/2019/06/upload-images.png' ),
                array( 'title' => 'Profile Completeness – Widgets Settings', 'link' => 'https://www.kainelabs.com/wp-content/uploads/edd/2019/06/widgets-settings.png' ),
                array( 'title' => 'Profile Completeness – Account Restrictions', 'link' => 'https://www.kainelabs.com/wp-content/uploads/edd/2019/06/account-restrictions.png' )
            )
         );

        // Get Extension.
        yz_get_notice_addon( $data2 );
    }

    if ( ! get_option( $yzbm_notice ) ) {
        $load_lightbox = true;
        $data3 = array(
            'notice_id' => $yzbm_notice,
            'utm_campaign' => 'youzer-buddypress-moderation',
            'utm_medium' => 'admin-banner',
            'utm_source' => 'clients-site',
            'title' => 'Youzer - Buddypress Moderation',
            'link' => 'https://www.kainelabs.com/downloads/buddypress-moderation-plugin/',
            'buy_now' => 'https://www.kainelabs.com/?edd_action=add_to_cart&download_id=27779&edd_options%5Bprice_id%5D=1',
            'image' => 'https://cldup.com/m9j-9YtX-C.png',
            'description' => "Moderating your online community is not an option — it’s a must. Meet the most complete buddyPress moderation solution with an advanced features to take the full control over your community and keep it safe with automatic moderation features and automatic restrictions.",
            'features' => array(
                'Moderation Components : Members, Activities, Comments, Messages, Groups.',
                'Set What Roles Can Reports Items & Moderator Roles.',
                'Automatic Moderation After an item reach a certain numner of reports.',
                'Apply Temporary or Official Restrictions for Specific Periods. ( Disable posts, comments, messages, friends, follows ... )',
                'Allow Visitors to Report Items & Add Unlimited Reports Subjects.',
                'Customizable Notification Emails when a New Reports is Added, Restored, Deleted, Hidden & More ...',
                'Advanced Moderation Table With Bulk and Single Actions : View, Close, Restore, Delete, Delete & Punish, Mark as Spammer & More ...',
                'And Many Many Other Features You Can Check Them On The Extension Page.'
            ),
            'images' => array(
                array( 'title' => 'Reports Table ( Moderation Queue )', 'link' => 'https://www.kainelabs.com/wp-content/uploads/edd/2019/11/reports2.png' ),
                array( 'title' => 'Restrictions Table', 'link' => 'https://www.kainelabs.com/wp-content/uploads/edd/2019/11/restrictions.png' ),
                array( 'title' => 'Activity Posts & Comments Report Button', 'link' => 'https://www.kainelabs.com/wp-content/uploads/edd/2019/11/posts-comments.png' ),
                array( 'title' => 'Members Directory – User Report Button', 'link' => 'https://www.kainelabs.com/wp-content/uploads/edd/2019/11/members.png' ),
                array( 'title' => 'User Profile Report Button', 'link' => 'https://www.kainelabs.com/wp-content/uploads/edd/2019/11/user-profile.png' ),
                array( 'title' => 'Groups Directory – Group Report Button', 'link' => 'https://www.kainelabs.com/wp-content/uploads/edd/2019/11/groups.png' ),
                array( 'title' => 'Single Group Page Report Button', 'link' => 'https://www.kainelabs.com/wp-content/uploads/edd/2019/11/groups-single-page.png' ),
                array( 'title' => 'Messages Report Button', 'link' => 'https://www.kainelabs.com/wp-content/uploads/edd/2019/11/message.png' ),
                array( 'title' => 'Logged-In Users Report Form', 'link' => 'https://www.kainelabs.com/wp-content/uploads/edd/2019/11/members-report-form.png' ),
                array( 'title' => 'Visitors Report Form', 'link' => 'https://www.kainelabs.com/wp-content/uploads/edd/2019/11/visitors-report.png' )
            )
         );

        // Get Extension.
        yz_get_notice_addon( $data3 );
    }

    if ( $load_lightbox ) {
        // Load Light Box CSS and JS.
        wp_enqueue_style( 'yz-lightbox', YZ_PA . 'css/lightbox.min.css', array(), YZ_Version );
        wp_enqueue_script( 'yz-lightbox', YZ_PA . 'js/lightbox.min.js', array( 'jquery' ), YZ_Version, true );
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
            max-width: 870px;
            position: relative;
            margin-bottom: 15px;
            margin: 0 auto 15px !important;
        }

        .yz-addon-notice .yz-addon-notice-content {
            /*float: left;*/
            /*width: 80%;*/
            /*margin-left: 20%;*/
            padding: 25px 35px;
        }
/*
        .yz-addon-notice.yz-horizontal-layout .yz-addon-notice-img {
            display: block;    
            background-size: cover;
            background-position: center;
            float: left;
            width: 20%;
            height: 100%;
            position: absolute;
        }
*/
        .yz-addon-notice .yz-addon-notice-img {
            display: block;
            background-size: cover;
            background-position: center;
            width: 100%;
        }

        .yz-addon-notice .yz-addon-notice-title {
            font-size: 17px;
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
            font-size: 14px;
            font-weight: 600;
            min-width: 110px;
            text-align: center;
            margin-right: 12px;
            padding: 15px 25px;
            border-radius: 3px;
            display: inline-block;
            vertical-align: middle;
            text-decoration: none;
            text-transform: capitalize;
        }
        
        .yz-addon-notice .yz-addon-notice-buttons a:focus {
            box-shadow: none !important;
        }
        
        .yz-addon-notice .notice-dismiss {
            text-decoration: none;
        }

        .yz-addon-notice .yz-addon-notice-buttons a.yz-addon-view-features {
            background-color: #03A9F4;
        }

        .yz-addon-notice .yz-addon-notice-buttons a.yz-addon-buy-now {
            background-color: #8bc34a;
        }


        .yz-addon-notice .yz-addon-notice-buttons a.yz-addon-delete-notice {
            background: #F44336;
        }

        .yz-addon-features {
            margin-bottom: 25px;
        }

        .yz-addon-notice .yz-addon-features p {
            margin: 0 0 12px;
        }

        .yz-addon-notice .yz-addon-features p:last-of-type {
            margin-bottom: 0;
        }

        .yz-addon-screenshots {
            margin-bottom: 20px; 
        }

        .yz-addon-screenshots .yz-screenshot-item {
            width: 60px;
            height: 60px;
            border-radius: 3px;
            /*margin-right: 10px;*/
            /*margin-bottom: 5px;*/
            margin: 5px 10px 5px 0;
            display: inline-block;
            background-size: cover;
        }

        .yz-addon-section-title {
            color: #646464;
            font-size: 13px;
            font-weight: 600;
            background: #eee;
            border-radius: 3px;
            margin-bottom: 15px;
            display: inline-block;
            padding: 4px 12px 5px;
        }

    </style>

    <?php

        // <a href="<?php echo add_query_arg( 'yz-dismiss-extension-notice', $data['notice_id'], yz_get_current_page_url() ); ? >" type="button" class="notice-dismiss">Dismiss.</a>
        $link = $data['link'] .'?utm_campaign=' . $data['utm_campaign'] . '&utm_medium=' . $data['utm_medium'] . '&utm_source=' . $data['utm_source'] . '&utm_content=view-all-features';

        $buy = $data['buy_now'] .'&utm_campaign=' . $data['utm_campaign'] . '&utm_medium=' . $data['utm_medium'] . '&utm_source=' . $data['utm_source'] . '&utm_content=buy-now';

        ?>
    
    <div class="yz-addon-notice updated notice notice-success">
        <!-- <div class="yz-addon-notice-img" style="background-image:url(<?php echo $data['image']; ?>);"></div> -->
        <img class="yz-addon-notice-img" src="<?php echo $data['image']; ?>" alt="">
        <div class="yz-addon-notice-content">
            <div class="yz-addon-notice-title"><?php echo $data['title']; ?><span class="yz-addon-notice-tag">New</span></div>
            <div class="yz-addon-notice-description"><?php echo $data['description']; ?></div>
            <?php if ( isset( $data['features'] ) ) : ?>
            <div class="yz-addon-features">
                <div class="yz-addon-section-title">Features</div><br>
                <?php foreach ( $data['features'] as $feature ) : ?>
                <p>- <?php echo $feature; ?></p>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <?php if ( isset( $data['images'] ) ) : ?>
            <div class="yz-addon-screenshots" data-lightbox="<?php echo $data['notice_id']; ?>">
                <div class="yz-addon-section-title">Screenshots</div><br>
                <?php foreach ( $data['images'] as $image ) : ?>
                <a href="<?php echo $image['link']; ?>" data-lightbox="<?php echo $data['notice_id']; ?>" data-title="<?php echo $image['title']; ?>"><div class="yz-screenshot-item" style="background-image: url(<?php echo $image['link']; ?>)"></div></a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <div class="yz-addon-notice-buttons">
                <a href="<?php echo $link; ?>" class="yz-addon-view-features">View All Features</a>
                <a href="<?php echo $buy; ?>" class="yz-addon-buy-now">Buy Now</a>
                <a href="<?php echo add_query_arg( 'yz-dismiss-extension-notice', $data['notice_id'], yz_get_current_page_url() ); ?>" type="button" class="yz-addon-delete-notice">Delete Notice</a>
            </div>
        </div>
    </div>

    <?php
}