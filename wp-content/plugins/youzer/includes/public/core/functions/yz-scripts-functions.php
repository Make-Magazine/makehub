<?php

/**
 * # Register Public Scripts .
 */
function yz_public_scripts() {

    // Get Data.
    $jquery = array( 'jquery' );
    $extra_args = array( 'jquery-ui-sortable', 'jquery-ui-draggable' );

    // Youzer Global Script
    wp_enqueue_script( 'youzer', YZ_PA . 'js/youzer.min.js', $jquery, YZ_Version, true );

    // Font.
    wp_enqueue_style( 'yz-opensans', 'https://fonts.googleapis.com/css?family=Open+Sans:400,600', array(), YZ_Version );

    // Youzer Css.
    wp_enqueue_style( 'youzer', YZ_PA . 'css/youzer.min.css', array(), YZ_Version );

    // Get Youzer Script Variables
    $youzer_vars = youzer_scripts_vars();

    if ( ! is_user_logged_in() ) {
        $youzer_vars['ajax_enabled'] = yz_options( 'yz_enable_ajax_login' );
        $youzer_vars['login_popup'] = yz_options( 'yz_enable_login_popup' );
    }

    wp_localize_script( 'youzer', 'Youzer', $youzer_vars );

    // Tag Editor Script
    wp_register_script( 'yz-scrolltotop', YZ_PA .'js/yz-scrolltotop.min.js', $jquery, YZ_Version, true );

    // Call Fonts.
    wp_enqueue_style( 'yz-roboto', 'https://fonts.googleapis.com/css?family=Roboto:400', array(), YZ_Version );
    wp_enqueue_style( 'yz-lato', 'https://fonts.googleapis.com/css?family=Lato:400', array(), YZ_Version );

    // Wall Form Uploader CSS.
    wp_register_style( 'yz-bp-uploader', YZ_PA . 'css/yz-bp-uploader.min.css', array(), YZ_Version );

    // Headers Css
    wp_enqueue_style( 'yz-headers', YZ_PA . 'css/yz-headers.min.css', array(), YZ_Version );

    // Profile Css.
    wp_register_style( 'yz-profile', YZ_PA . 'css/yz-profile-style.min.css', array(), YZ_Version );
    
    // Get Plugin Scheme.
    $youzer_scheme = yz_options( 'yz_profile_scheme' );

    // Profile Color Schemes Css.f
    wp_enqueue_style( 'yz-scheme', YZ_PA . 'css/schemes/' . $youzer_scheme .'.min.css', array(), YZ_Version );

    // Group Pages CSS
    if ( bp_is_groups_component() && ! bp_is_groups_directory() ) {
       wp_enqueue_style( 'yz-groups', YZ_PA .'css/yz-groups.min.css', array( 'yz-bp-uploader' ), YZ_Version );
    }
    
    // Member Pages CSS
    if ( ! bp_is_members_directory() && ! bp_is_groups_directory()  ) {
        wp_enqueue_style( 'yz-social', YZ_PA .'css/yz-social.min.css', array( 'dashicons' ), YZ_Version );
    }

    // Members & Groups Directories CSS
    if ( bp_is_members_directory() || bp_is_groups_directory() ) {
        wp_enqueue_script( 'masonry' );
        wp_enqueue_style( 'yz-directories', YZ_PA . 'css/yz-directories.min.css', array( 'dashicons' ), YZ_Version );
        wp_enqueue_script( 'yz-directories', YZ_PA .'js/yz-directories.min.js', $jquery, YZ_Version, true );
    }
    
    if ( bp_current_component() ) {
        yz_common_scripts();
    }

    // Global Youzer JS
    wp_enqueue_style( 'yz-icons' );

}

add_action( 'wp_enqueue_scripts', 'yz_public_scripts' );

/**
 * Common Scripts
 */
function yz_common_scripts() {
    
    $jquery = array( 'jquery' );

    // Nice Selector 
    wp_enqueue_script( 'yz-nice-selector', YZ_PA .'js/jquery.nice-select.min.js', $jquery, YZ_Version, false );
    // Textarea AutoSizing
    wp_enqueue_script( 'yz-textarea-autosize', YZ_PA .'js/autosize.min.js', $jquery, YZ_Version, true );
    
    // Load Light Box CSS and JS.
    wp_enqueue_style( 'yz-lightbox', YZ_PA . 'css/lightbox.min.css', array(), YZ_Version );
    wp_enqueue_script( 'yz-lightbox', YZ_PA . 'js/lightbox.min.js', $jquery, YZ_Version, true );

}

/**
 * Activity Scripts
 */

add_action( 'wp_enqueue_scripts', 'yz_activity_scripts' );

function yz_activity_scripts() {
    
    if ( ! yz_is_activity_component() ) {
        return;
    }


    // Wall JS.
    wp_enqueue_script( 'yz-wall', YZ_PA . 'js/yz-wall.min.js', array( 'jquery' ), YZ_Version );

    // Wall Css
    wp_enqueue_style( 'yz-wall', YZ_PA . 'css/yz-wall.min.css', array(), YZ_Version );

    // Load Profile Style
    wp_enqueue_style( 'yz-profile' );

    // Enable Url Live Preview
    $enable_url_preview = yz_options( 'yz_enable_wall_url_preview' );

    if ( 'on' == $enable_url_preview ) {
        // URL Preview CSS
        wp_enqueue_script( 'yz-url-preview', YZ_PA . 'js/yz-url-preview.min.js', array( 'jquery' ), YZ_Version );
        wp_enqueue_style( 'yz-url-preview', YZ_PA . 'css/yz-url-preview.min.css', array(), YZ_Version );
    }

    // Load Carousel CSS and JS.
    wp_enqueue_style( 'yz-carousel-css', YZ_PA . 'css/owl.carousel.min.css', array(), YZ_Version );
    wp_enqueue_script( 'yz-carousel-js', YZ_PA . 'js/owl.carousel.min.js', array(), YZ_Version );
    wp_enqueue_script( 'yz-slider', YZ_PA . 'js/yz-slider.min.js', array(), YZ_Version );

    if ( is_user_logged_in() && yz_is_wall_posting_form_active() ) {
        
        global $Youzer, $YZ_upload_url;

        // Wall Uploader
        wp_enqueue_script( 'yz-wall-form', YZ_PA . 'js/yz-wall-form.min.js', array( 'jquery' ), YZ_Version, true );

        $wall_args = apply_filters( 'yz_wall_js_args', array(
                'invalid_image_ext' => $Youzer->wall->msg( 'invalid_image_extension' ),
                'invalid_video_ext' => $Youzer->wall->msg( 'invalid_video_extension' ),
                'invalid_audio_ext' => $Youzer->wall->msg( 'invalid_audio_extension' ),
                'invalid_file_ext'  => $Youzer->wall->msg( 'invalid_file_extension' ),
                'max_size'          => yz_options( 'yz_attachments_max_size' ),
                'default_extentions'=> yz_get_allowed_extentions( 'default' ),
                'image_extentions'  => yz_get_allowed_extentions( 'image' ),
                'video_extentions'  => yz_get_allowed_extentions( 'video' ),
                'audio_extentions'  => yz_get_allowed_extentions( 'audio' ),
                'file_extentions'   => yz_get_allowed_extentions( 'file' ),
                'max_files_number'  => $Youzer->wall->msg( 'max_files_number' ),
                'invalid_file_size' => $Youzer->wall->msg( 'invalid_file_size' ),
                'max_one_file'      => $Youzer->wall->msg( 'max_one_file' ),
                'base_url'          => $YZ_upload_url,
                'giphy_limit'       => 12,
            ) );

        // Localize Script.
        wp_localize_script( 'yz-wall-form', 'Yz_Wall', $wall_args );

        if ( 'on' == yz_options( 'yz_enable_wall_giphy' ) ) {
            // Giphy Script.
            wp_enqueue_script( 'yz-giphy', YZ_PA . 'js/yz-giphy.min.js', array( 'jquery', 'masonry' ), YZ_Version, true );
        }

    }
    
    if ( yz_enable_wall_posts_effect() ) {
        // Load View Port Checker Script
        wp_enqueue_script( 'yz-viewchecker', YZ_PA . 'js/yz-viewportChecker.min.js', array( 'jquery' ), YZ_Version, true  );
    }
    
    yz_common_scripts();
    
    do_action( 'yz_activity_scripts' );

}


/**
 * Add Groups Custom CSS.
 */
function yz_activity_custom_styling() {

    if ( 'off' == yz_options( 'yz_enable_activity_custom_styling' ) ) {
        return false;
    }

    // if its not the activity directory exit.
    if ( ! bp_is_activity_directory() ) {
        return false;
    }

    // Get CSS Code.
    $custom_css = yz_options( 'yz_activity_custom_styling' );

    if ( empty( $custom_css ) ) {
        return false;
    }

    // Custom Styling File.
    wp_enqueue_style( 'youzer-customStyle', YZ_AA . 'css/custom-script.css' );

    wp_add_inline_style( 'youzer-customStyle', $custom_css );
}

add_action( 'wp_enqueue_scripts', 'yz_activity_custom_styling' );

/**
 * Add Profile Custom CSS.
 */
function yz_profile_custom_styling() {

    if ( 'off' == yz_options( 'yz_enable_profile_custom_styling' ) ) {
        return false;
    }

    if ( ! bp_is_user() || yz_is_account_page() ) {
        return false;
    }

    // Get CSS Code.
    $custom_css = yz_options( 'yz_profile_custom_styling' );

    if ( empty( $custom_css ) ) {
        return false;
    }

    // Custom Styling File.
    wp_enqueue_style( 'youzer-customStyle', YZ_AA . 'css/custom-script.css' );

    wp_add_inline_style( 'youzer-customStyle', $custom_css );
}

add_action( 'wp_enqueue_scripts', 'yz_profile_custom_styling' );

/**
 * Add Groups Custom CSS.
 */
function yz_groups_custom_styling() {

    if ( 'off' == yz_options( 'yz_enable_groups_custom_styling' ) ) {
        return false;
    }

    if ( ! bp_is_groups_component() ) {
        return;
    }

    // Get CSS Code.
    $custom_css = yz_options( 'yz_groups_custom_styling' );

    if ( empty( $custom_css ) ) {
        return false;
    }

    // Custom Styling File.
    wp_enqueue_style( 'youzer-customStyle', YZ_AA . 'css/custom-script.css' );

    wp_add_inline_style( 'youzer-customStyle', $custom_css );
}

add_action( 'wp_enqueue_scripts', 'yz_groups_custom_styling' );

/**
 * Add Account Custom CSS.
 */
function yz_account_custom_styling() {

    if ( 'off' == yz_options( 'yz_enable_account_custom_styling' ) || ! yz_is_account_page() ) {
        return false;
    }

    // Get CSS Code.
    $custom_css = yz_options( 'yz_account_custom_styling' );

    if ( empty( $custom_css ) ) {
        return false;
    }

    // Custom Styling File.
    wp_enqueue_style( 'youzer-customStyle', YZ_AA . 'css/custom-script.css' );

    wp_add_inline_style( 'youzer-customStyle', $custom_css );
}
add_action( 'wp_enqueue_scripts', 'yz_account_custom_styling' );

/**
 * Add Members Directory Custom CSS.
 */
function yz_members_directory_custom_styling() {

    if ( 'off' == yz_options( 'yz_enable_members_directory_custom_styling' ) ) {
        return false;
    }

    if ( ! bp_is_members_directory() ) { 
        return false;
    }

    // Get CSS Code.
    $custom_css = yz_options( 'yz_members_directory_custom_styling' );

    if ( empty( $custom_css ) ) {
        return false;
    }

    // Custom Styling File.
    wp_enqueue_style( 'youzer-customStyle', YZ_AA . 'css/custom-script.css' );

    wp_add_inline_style( 'youzer-customStyle', $custom_css );
}
add_action( 'wp_enqueue_scripts', 'yz_members_directory_custom_styling' );

/**
 * Add Groups Directory Custom CSS.
 */
function yz_groups_directory_custom_styling() {

    if ( 'off' == yz_options( 'yz_enable_groups_directory_custom_styling' ) ) {
        return false;
    }

    if ( ! bp_is_groups_directory() ) { 
        return false;
    }

    // Get CSS Code.
    $custom_css = yz_options( 'yz_groups_directory_custom_styling' );

    if ( empty( $custom_css ) ) {
        return false;
    }

    // Custom Styling File.
    wp_enqueue_style( 'youzer-customStyle', YZ_AA . 'css/custom-script.css' );

    wp_add_inline_style( 'youzer-customStyle', $custom_css );
}
add_action( 'wp_enqueue_scripts', 'yz_groups_directory_custom_styling' );

/**
 * Add Global Custom CSS.
 */
function yz_global_custom_styling() {
    
    if ( 'off' == yz_options( 'yz_enable_global_custom_styling' ) ) {
        return false;
    }

    // Get CSS Code.
    $custom_css = yz_options( 'yz_global_custom_styling' );

    if ( empty( $custom_css ) ) {
        return false;
    }

    // Custom Styling File.
    wp_enqueue_style( 'youzer-customStyle', YZ_AA . 'css/custom-script.css' );


    wp_add_inline_style( 'youzer-customStyle', $custom_css );
}

add_action( 'wp_enqueue_scripts', 'yz_global_custom_styling' );

/**
 * Remove Buddypress Default CSS.
 */
function yz_dequeue_buddypress_css() {
    wp_dequeue_style( 'bp-twentyten' );
    wp_dequeue_style( 'bp-twentyeleven' );
    wp_dequeue_style( 'bp-twentytwelve' );
    wp_dequeue_style( 'bp-twentythirteen' );
    wp_dequeue_style( 'bp-twentyfourteen' );
    wp_dequeue_style( 'bp-twentyfifteen' );
    wp_dequeue_style( 'bp-twentysixteen' );
    wp_dequeue_style( 'bp-twentyseventeen' );
}

add_action( 'wp_enqueue_scripts', 'yz_dequeue_buddypress_css', 999 );

/**
 * Emoji Scripts
 */
function yz_emoji_scripts() {

    if ( ! is_user_logged_in() ) {
        return false;
    }

    // Emojionearea Scripts
    wp_enqueue_script( 'yz-textcomplete', YZ_PA . 'js/textcomplete.min.js', array( 'jquery' ), YZ_Version );
    wp_enqueue_script( 'yz-emojionearea', YZ_PA . 'js/emojionearea.min.js', array( 'jquery' ), YZ_Version );
    wp_enqueue_style( 'yz-emoji',  YZ_PA . 'css/emojionearea.min.css', array(), YZ_Version );
    wp_enqueue_script( 'yz-emoji', YZ_PA . 'js/yz-emoji.min.js', array(), YZ_Version );

}

/**
 * Emoji Scripts
 */
function yz_call_emoji_scripts() {

    // Get Emoji Visibility
    $emoji_visibility = array();
    
    if ( bp_is_messages_conversation() || bp_is_messages_compose_screen() ) {
            
        // Get Visibility Options.
        $messages_emoji = yz_options( 'yz_enable_messages_emoji' );

        if ( 'on' == $messages_emoji ) {
            $emoji_visibility['messages_visibility'] = $messages_emoji;
        }

    }

    if ( yz_is_activity_component() ) {

        // Get Visibility Options.
        $posts_emoji    = yz_options( 'yz_enable_posts_emoji' );
        $comments_emoji = yz_options( 'yz_enable_comments_emoji' );
        
        if ( 'on' == $posts_emoji ) {
            $emoji_visibility['posts_visibility'] = $posts_emoji;
        }

        if ( 'on' == $comments_emoji ) {
            $emoji_visibility['comments_visibility'] = $comments_emoji;
        }

    }

    if ( empty( $emoji_visibility ) ) {
        return false;
    }

    yz_emoji_scripts();

    // Localize Emoji Script.
    wp_localize_script( 'yz-emoji', 'Yz_Emoji', $emoji_visibility );

}

add_action( 'wp_enqueue_scripts', 'yz_call_emoji_scripts' );

/**
 * Widgets Enqueue scripts.
 */
function yz_widgets_enqueue_scripts( $hook_suffix ) {

    if ( 'widgets.php' !== $hook_suffix ) {
        return;
    }

    wp_enqueue_style( 'yz-iconpicker' );
    wp_enqueue_script( 'yz-iconpicker' );

}

add_action( 'admin_enqueue_scripts', 'yz_widgets_enqueue_scripts' );


/**
 * Profile Posts & Comments Pagination
 */
function yz_profile_posts_comments_pagination() {

    // Profile Ajax Pagination Script
    wp_enqueue_script( 'yz-pagination', YZ_PA . 'js/yz-pagination.min.js', array( 'jquery') , YZ_Version, true );

    wp_localize_script( 'yz-pagination', 'ajaxpagination',
        array(
            'ajaxurl'    => admin_url( 'admin-ajax.php' ),
            'query_vars' => json_encode( array( 'yz_user' => bp_displayed_user_id() ) )
        )
    );

}