<?php
add_action( 'admin_init', 'ldvc_custom_image_sizes' );
function ldvc_custom_image_sizes() {

    $lds_sizes = array(
        array(
            'name'      =>  'course_icon',
            'width'     =>  150,
            'height'    =>  150,
            'crop'      =>  true,
        )
    );

    foreach( $lds_sizes as $size ) {
        add_image_size( $size['name'], $size['width'], $size['height'], $size['crop'] );
    }

}

add_action( 'wp_enqueue_scripts', 'ldvc_bundled_theme_assets', 1 );
function ldvc_bundled_theme_assets( $ldvc_theme = null, $ldvc_template = null ) {

     if( function_exists('buddyboss_theme') ) {
          wp_register_style( 'ldvc', LDS_URL . '/assets/css/ldvc.css', array('learndash-front','buddyboss-theme-learndash'), LDS_VER );
     } else {
          wp_register_style( 'ldvc', LDS_URL . '/assets/css/ldvc.css', array('learndash-front'), LDS_VER );
     }

     wp_register_style( 'ldvc-bb', LDS_URL . '/assets/css/buddyboss.css', array( 'buddyboss-theme-learndash' ), LDS_VER );
     wp_register_style( 'ldvc-course-grid', LDS_URL . '/assets/css/course-grid.css', array(), LDS_VER );


   /**
    * [if description]
    * @var [type]
    */
    if( function_exists('buddyboss_theme') ) {

        wp_enqueue_style('ldvc-bb');

    }

    wp_enqueue_style('ldvc');

    // Ecosystem support

    wp_register_script( 'ldvc', LDS_URL . '/assets/js/ldvc.js', array('jquery','learndash-front'), LDS_VER, true );
    wp_enqueue_script('ldvc');

    if( defined('LEARNDASH_COURSE_GRID_VERSION') && is_singular() ) {

        global $post;

        if( has_shortcode( $post->post_content, 'ld_course_list') ) {
            wp_enqueue_style( 'ldvc-course-grid' );
        }

    }

    /**
     * Enqueue any custom theme stylesheets if they exist
     * @var [type]
     */

    $ldvc_theme     = ( $ldvc_theme == null ? get_option('lds_skin') : $ldvc_theme );
    $ldvc_template  = ( $ldvc_template == null ? get_option('lds_listing_style') : $ldvc_template );

    if( $ldvc_theme != 'default' && !empty($ldvc_theme) ) {

        $file = apply_filters( 'lds_theme_css', array(
            'path'  => LDS_PATH . 'assets/css/themes/' . $ldvc_theme . '.css',
            'url'   => LDS_URL . '/assets/css/themes/' . $ldvc_theme . '.css'
        ), $ldvc_theme );

        if( file_exists($file['path']) ) {
            wp_register_style( 'ldvc-' . $ldvc_theme, $file['url'], array(), LDS_VER );
            wp_enqueue_style( 'ldvc-' . $ldvc_theme );
        }

    }

    wp_localize_script( 'ldvc', 'ldvc', array(
        'theme'     =>  $ldvc_theme,
        'template'  => $ldvc_template
    ) );

    /**
     * Enqueue any custom template stylesheets if they exist
     * @var [type]
     */

    if( $ldvc_template != 'default' && !empty($ldvc_template) ) {

        $file = apply_filters( 'lds_template_css', array(
            'path'  => LDS_PATH . 'assets/css/templates/' . $ldvc_template . '.css',
            'url'   => LDS_URL . '/assets/css/templates/' . $ldvc_template . '.css'
        ), $ldvc_theme );

        if( file_exists($file['path']) ) {
            wp_register_style( 'ldvc-template-' . $ldvc_template, $file['url'], array(), LDS_VER );
            wp_enqueue_style( 'ldvc-template-' . $ldvc_template );
        }

    }

    $fa_version = get_option('lds_fontawesome_ver');

    if( $fa_version == '4' ) {

        wp_register_style('fontawesome', LDS_URL . '/assets/css/vendor/fontawesome/v4/font-awesome-4.min.css');
        wp_enqueue_style('fontawesome');

    } elseif( $fa_version == '5' ) {

        wp_register_script( 'fontawesome', LDS_URL . '/assets/js/vendor/fontawesome/all.min.js', LDS_VER );
        wp_register_script('fontawesome-shims', LDS_URL . '/assets/js/vendor/fontawesome/v4-shims.min.js');

        wp_enqueue_script('fontawesome');
        wp_enqueue_script('fontawesome-shims');

    }

    /**
     * Custom Google Fonts
     * @var [type]
     */

    $font_options = apply_filters( 'ldvc_enqeue_font_options', array(
        'body'  =>  get_option('lds_font_family'),
        'heading' => get_option('lds_heading_font_family')
    ) );

    $fonts = array();

    foreach( $font_options as $slug => $font_option ) {
        if( $font_option && $font_option != '' ) {
            $fonts[] = $font_option;
        }
    }

    $fonts = array_unique($fonts);

    if( !empty($fonts) ) {

        foreach( $fonts as $font_family ) {

            $font     = ldvc_get_google_font($font_family);
            $font_url = 'https://fonts.googleapis.com/css?family=' . urlencode($font_family);

            if( isset($font->variants) ) {
                foreach( $font->variants as $variant ) {
                    $font_url .= ':' . $variant;
                }
            }

            wp_register_style(  $font_family, $font_url );
            wp_enqueue_style( $font_family );

        }

    }

    /**
      * Focus Themes
      */

    $focus_theme = get_option('lds_focus_theme');

    if( $focus_theme && $focus_theme != 'default' ) {

         wp_register_style( 'lds_focus_theme-' . $focus_theme, LDS_URL . '/assets/css/themes/focus/' . $focus_theme . '.css', LDS_VER );
         wp_enqueue_style( 'lds_focus_theme-' . $focus_theme );

    }

}


add_action( 'admin_enqueue_scripts', 'ldvc_admin_scripts' );
function ldvc_admin_scripts() {

    $post_types = array(
        'sfwd-courses',
        'sfwd-lessons',
        'sfwd-topic',
        'sfwd-quiz'
    );

    if( get_option( 'lds_fontawesome_ver', '5' ) == '5' ) {
        wp_register_style( 'fontawesome', LDS_URL . '/assets/css/vendor/fontawesome/v5/font-awesome-5.min.css', LDS_VER );
        wp_register_style( 'fontawesome-iconpicker', LDS_URL . '/assets/css/vendor/fontawesome/fontawesome-iconpicker.min.css', LDS_VER );
        wp_register_script( 'fontawesome-iconpicker', LDS_URL . '/assets/js/vendor/iconpicker/fontawesome-iconpicker-5.min.js', LDS_VER, array('jquery') );
    } elseif( get_option( 'lds_fontawesome_ver', '5' ) == '4' ) {
        wp_register_style( 'fontawesome', LDS_URL . '/assets/css/vendor/fontawesome/v4/font-awesome-4.min.css', LDS_VER );
        wp_register_style( 'fontawesome-iconpicker', LDS_URL . '/assets/css/vendor/fontawesome/fontawesome-iconpicker.min.css', LDS_VER );
        wp_register_script( 'fontawesome-iconpicker', LDS_URL . '/assets/js/vendor/iconpicker/fontawesome-iconpicker-4.min.js', LDS_VER, array('jquery') );

    }

    if( in_array( get_post_type(), $post_types ) && get_option( 'lds_fontawesome_ver', '5' ) != '0' ) {

        if( get_option('lds_fontawesome_ver', '5' ) == '4' && function_exists( 'is_gutenberg_page' ) && is_gutenberg_page() ) {
            return;
        }

        wp_enqueue_style('fontawesome');

        if( get_option( 'lds_disable_fa_picker', 'no' ) == 'no' ) {
            wp_enqueue_style('fontawesome-iconpicker');
            wp_enqueue_script('fontawesome-iconpicker');
        }

    }

}

add_action( 'load-admin_page_learndash-appearance', 'ldvc_admin_setting_scripts' );
function ldvc_admin_setting_scripts() {

    wp_enqueue_script('learndash-admin-settings-page');
    wp_enqueue_script('learndash-select2-jquery-style');

    wp_enqueue_script('select2');
    wp_register_script( 'ldvc-admin', LDS_URL . '/assets/js/admin/ldvc-admin.js', array(), true );
    wp_enqueue_script( 'ldvc-admin' );


}

add_action('admin_enqueue_scripts', 'ldvc_admin_global_scripts' );
function ldvc_admin_global_scripts() {
     wp_register_style( 'ldvc-admin', LDS_URL . '/assets/css/admin/ldvc-admin.css' );
     wp_enqueue_style( 'ldvc-admin' );
}

add_action( 'wp_enqueue_scripts', 'ldvc_custom_styling' );
function ldvc_custom_styling() {

    $custom_css = get_transient( 'lds_custom_css' );
    global $wp_customize;

    if( empty($custom_css) || isset($wp_customize) ):

        ob_start(); ?>

            <style type="text/css">

                <?php
                /**
                 * LD Colors
                 */
                 $colors = apply_filters( 'learndash_30_custom_colors', array(
                     'primary'   => LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Theme_LD30', 'color_primary' ),
                     'secondary' => LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Theme_LD30', 'color_secondary' ),
                     'tertiary'  => LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Theme_LD30', 'color_tertiary' ),
                 ) );
                /**
                 * Rounded corners
                 * @var array
                 */
                $border_radius = array(
                    'lists'     => strval(get_option('lds_content_list_border_radius')),
                    'button'    => strval(get_option('lds_button_border_radius')),
                    'status'    => strval(get_option('lds_status_border_radius')),
                    'pagination' => strval(get_option('lds_pagination_border_radius')),
                );

                if( isset($colors['primary']) && !empty($colors['primary']) ): ?>
                    .ld-course-list-items .ld_course_grid .thumbnail.course .ld_course_grid_price.ribbon-enrolled,
                    .ld-course-list-items .ld_course_grid .thumbnail.course a.btn-primary {
                        background: <?php echo $colors['primary']; ?>;
                        border-color: <?php echo $colors['primary']; ?>;
                    }
                    .ld-course-list-items .ld_course_grid .thumbnail.course .ld_course_grid_price.ribbon-enrolled:before {
                        border-top-color: <?php echo $colors['primary']; ?>;
                        border-right-color: <?php echo $colors['primary']; ?>;
                    }
                <?php
                endif;

                if( isset($colors['secondary']) && !empty($colors['secondary']) ): ?>
                    .ld-course-list-items .ld_course_grid .thumbnail.course .ld_course_grid_price {
                        background-color: <?php echo $colors['secondary']; ?>;
                    }
                    .ld-course-list-items .ld_course_grid .thumbnail.course .ld_course_grid_price:before {
                        border-top-color: <?php echo $colors['secondary']; ?>;
                        border-right-color: <?php echo $colors['secondary']; ?>;
                    }
                <?php
                endif;
                if( $border_radius['status'] == '0' || !empty($border_radius['status']) ): ?>
                    .learndash-wrapper .ld-status {
                        border-radius: <?php echo $border_radius['status']; ?>px;
                    }
                <?php
                endif;

                /**
                 * List items
                 * @var [type]
                 */
                if( $border_radius['lists'] == '0' || !empty($border_radius['lists']) ): ?>
                    .learndash-wrapper.ldvc-login .ld-login-modal-login,
                    .learndash-wrapper.ldvc-login .ld-login-modal-register,
                    .learndash-wrapper .wpProQuiz_content .wpProQuiz_questionListItem label,
                    .learndash-wrapper .ld-topic-status,
                    .learndash-wrapper .ld-course-status.ld-course-status-enrolled,
                    .learndash-wrapper .ld-course-status,
                    .learndash-wrapper .ld-course-navigation .ld-lesson-item-expanded,
                    .learndash-wrapper .ld-table-list,
                    .learndash-wrapper .ld-progress .ld-progress-bar,
                    .learndash-wrapper .ld-item-list .ld-item-list-item {
                        border-radius: <?php echo $border_radius['lists'] . 'px'; ?>;
                    }

                    .learndash-wrapper.lds-course-list.lds-course-list-grid-banners .ld-item-list .ld-item-list-item {
                        overflow: hidden;
                    }

                <?php
                endif;

                $button_colors = array(
                    'lds_button_bg'             =>  strval(get_option('lds_button_bg')),
                    'lds_button_txt'            =>  strval(get_option('lds_button_txt')),
                    'lds_complete_button_bg'    =>  strval(get_option('lds_complete_button_bg')),
                    'lds_complete_button_txt'   =>  strval(get_option('lds_complete_button_txt')),
                );

                if( lds_in_use($button_colors['lds_button_bg']) || lds_in_use($button_colors['lds_button_txt'] ) ): ?>

                    .learndash-wrapper .lds-btn,
                    .ld-course-list-items .ld_course_grid .thumbnail.course a.btn-primary,
                    .learndash-wrapper .ld-focus-comments .form-submit #submit,
                    .learndash-wrapper .btn-join,
                    .learndash-wrapper .wpProQuiz_content #btn-join,
                    .learndash-wrapper #quiz_continue_link,
                    .learndash-wrapper .wpProQuiz_content input[type='button'],
                    .learndash-wrapper .wpProQuiz_content input[type='button'].wpProQuiz_button:not(.wpProQuiz_button_reShowQuestion):not(.wpProQuiz_button_restartQuiz),
                    .learndash-wrapper .wpProQuiz_content input[type='button'].wpProQuiz_button2,
                    .learndash-wrapper .sfwd_lms_mark_complete,
                    .learndash-wrapper #sfwd_lms_mark_complete,
                    .learndash-wrapper .sfwd_lms_mark_complete input[type="submit"],
                    .learndash-wrapper #sfwd_lms_mark_complete input[type="submit"],
                    .learndash-wrapper .ld-button,
                    .learndash-wrapper .ld-section-heading .ld-expand-button {

                         <?php
                         if( lds_in_use($button_colors['lds_button_bg']) ): ?>
                              background-color: <?php echo $button_colors['lds_button_bg']; ?> !important;
                         <?php
                         endif;
                         if( lds_in_use($button_colors['lds_button_txt']) ): ?>
                              color: <?php echo $button_colors['lds_button_txt']; ?> !important;
                         <?php
                         endif; ?>
                    }
                    .learndash-wrapper .lds-btn:hover,
                    .ld-course-list-items .ld_course_grid .thumbnail.course a.btn-primary:hover,
                    .learndash-wrapper .ld-focus-comments .form-submit #submit:hover,
                    .learndash-wrapper .btn-join:hover,
                    .learndash-wrapper .wpProQuiz_content #btn-join:hover,
                    .learndash-wrapper #quiz_continue_link:hover,
                    .learndash-wrapper .wpProQuiz_content input[type='button'].wpProQuiz_button:hover,
                    .learndash-wrapper .wpProQuiz_content input[type='button'].wpProQuiz_button2:hover,
                    .learndash-wrapper .sfwd_lms_mark_complete:hover,
                    .learndash-wrapper #sfwd_lms_mark_complete:hover,
                    .learndash-wrapper .ld-section-heading .ld-section-heading,
                    .learndash-wrapper .sfwd_lms_mark_complete input[type="submit"]:hover,
                    .learndash-wrapper #sfwd_lms_mark_complete input[type="submit"]:hover, {
                         opacity: 0.8;
                         <?php
                         if( lds_in_use($button_colors['lds_button_bg']) ): ?>
                              background-color: <?php echo $button_colors['lds_button_bg']; ?> !important;
                         <?php endif;
                         if( lds_in_use($button_colors['lds_button_txt']) ): ?>
                              color: <?php echo $button_colors['lds_button_txt']; ?> !important;
                         <?php
                         endif; ?>
                    }
                <?php endif;

                if( lds_in_use($button_colors['lds_complete_button_bg']) || lds_in_use($button_colors['lds_complete_button_txt'] ) ): ?>
                    .learndash-wrapper input.learndash_mark_complete_button,
                     .learndash-wrapper .sfwd_lms_mark_complete,
                     .learndash-wrapper #sfwd_lms_mark_complete,
                     .learndash-wrapper .sfwd_lms_mark_complete input[type="submit"],
                     .learndash-wrapper #sfwd_lms_mark_complete input[type="submit"] {

                          <?php
                          if( lds_in_use($button_colors['lds_complete_button_bg']) ): ?>
                               background-color: <?php echo $button_colors['lds_complete_button_bg']; ?> !important;
                          <?php
                          endif;
                          if( lds_in_use($button_colors['lds_complete_button_txt']) ): ?>
                               color: <?php echo $button_colors['lds_complete_button_txt']; ?>;
                          <?php
                          endif; ?>

                     }
               <?php endif;
                /**
                 * Buttons
                 * @var [type]
                 */
                if( $border_radius['button'] == '0' || !empty($border_radius['button']) ): ?>

                    .ld-course-list-items .ld_course_grid .thumbnail.course a.btn-primary,
                    .learndash-wrapper .ld-focus-comments .form-submit #submit,
                    .learndash-wrapper .btn-join,
                    .learndash-wrapper .wpProQuiz_content #btn-join,
                    .learndash-wrapper #quiz_continue_link,
                    .learndash-wrapper .wpProQuiz_content .wpProQuiz_button,
                    .learndash-wrapper .wpProQuiz_content .wpProQuiz_button2,
                    .learndash-wrapper .sfwd_lms_mark_complete,
                    .learndash-wrapper #sfwd_lms_mark_complete,
                    .learndash-wrapper .sfwd_lms_mark_complete input[type="submit"],
                    .learndash-wrapper #sfwd_lms_mark_complete input[type="submit"],
                    .learndash-wrapper .ld-button,
                    .learndash-wrapper .wpProQuiz_content a#quiz_continue_link,
                    .learndash-wrapper .lds-btn,
                    .learndash-wrapper .ld-expand-button {
                        border-radius: <?php echo $border_radius['button'] . 'px'; ?>;
                    }
                <?php
                endif;

                if( $border_radius['pagination'] == '0' || !empty($border_radius['pagination']) ): ?>
                    .learndash-wrapper .ld-pagination .ld-pages {
                        border-radius: <?php echo $border_radius['pagination'] . 'px'; ?>;
                    }
                <?php
                endif;

                /**
                 * Content list items
                 * @var [type]
                 */

                 $list_item_bg = get_option('lds_content_item_bg');
                 if( $list_item_bg && $list_item_bg != '' ): ?>

                    /*
                    .learndash-wrapper .ld-item-list-item-preview  */

                    .learndash-wrapper .ld-item-list .ld-item-list-item .ld-item-list-item-expanded::before,
                    .learndash-wrapper .ld-item-list-item:not(.ld-item-list-item-course) {
                        background: <?php echo $list_item_bg; ?>;
                    }

                 <?php
                 endif;

                /* Icons */

                /* Shadows */

                /* Buttons */

                /* Text Colors */

                // Headings

                $heading_text_color = get_option('lds_heading_txt');

                if( $heading_text_color && $heading_text_color != '' ): ?>

                    .learndash-wrapper .ld-lesson-section-heading,
                    .learndash-wrapper .ld-section-heading h2 {
                        color: <?php echo $heading_text_color; ?>;
                    }

                <?php
                endif;

                $heading_bg = get_option('lds_heading_bg');

                if( $heading_bg && $heading_bg != '' ): ?>

                    .learndash-wrapper .ld-lesson-list .ld-section-heading {
                        background: <?php echo $heading_bg; ?>;
                        padding: 10px 20px;
                    }

                <?php
                endif;

                $sub_heading_text_color = get_option('lds_sub_heading_txt');

                if( $sub_heading_text_color && $sub_heading_text_color != '' ): ?>

                    .learndash-wrapper .ld-lesson-item-section-heading,
                    .learndash-wrapper .ld-item-list-section-heading {
                        color: <?php echo $sub_heading_text_color; ?>;
                    }

                <?php
                endif;

                $sub_heading_bg = get_option('lds_sub_heading_bg');

                if( $sub_heading_bg && $sub_heading_bg != '' ): ?>

                    .learndash-wrapper .ld-lesson-item-section-heading,
                    .learndash-wrapper .ld-item-list-section-heading {
                        background: <?php echo $sub_heading_bg; ?>;
                        padding: 10px 20px;
                    }

                <?php
                endif;
                /*
                 *
                 * Content items
                 */

                // Text Color

                $item_text_color = get_option('lds_item_txt');

                if( $item_text_color && $item_text_color != '' ): ?>

                    .learndash-wrapper .ld-topic-title,
                    .learndash-wrapper .ld-lesson-title,
                    .learndash-wrapper .ld-item-title {
                        color: <?php echo $item_text_color; ?>;
                    }

                <?php
                endif;

                // Border Color

                $item_border_color = get_option('lds_content_item_border');

                if( $item_border_color && $item_border_color != '' ): ?>

                    .learndash-wrapper .wpProQuiz_content .wpProQuiz_questionListItem label,
                    .learndash-wrapper .ld-table-list .ld-table-list-item,
                    .learndash-wrapper .ld-content-actions,
                    .learndash-wrapper .ld-table-list.ld-no-pagination,
                    .learndash-wrapper .ld-table-list .ld-table-list-items,
                    .learndash-wrapper .ld-assignment-list .ld-table-list-footer,
                    .learndash-wrapper .ld-item-list .ld-item-list-item {
                        border-color: <?php echo $item_border_color; ?>;
                    }

                    .learndash-wrapper .ld-table-list .ld-table-list-footer {
                        border-color: <?php echo $item_border_color; ?>;
                    }

                <?php
                endif;

                // Border Size

                $item_border_size = strval(get_option('lds_content_list_border_size'));

                if( ( $item_border_size != '0' || !empty($item_border_size) ) && $item_border_color ): ?>

                    .learndash-wrapper .wpProQuiz_content .wpProQuiz_questionListItem label,
                    .learndash-wrapper .ld-table-list .ld-table-list-items,
                    .learndash-wrapper .ld-item-list .ld-item-list-item {
                        border-width: <?php echo $item_border_size; ?>px;
                    }

                    .learndash-wrapper .ld-table-list .ld-table-list-footer {
                        border-bottom-style: solid;
                        border-bottom-width: <?php echo $item_border_size; ?>px;
                    }

                    .learndash-wrapper .ld-assignment-list.ld-table-list .ld-table-list-footer {
                        border-width: <?php echo $item_border_size; ?>px;
                        border-style: solid;
                        border-top: 0;
                    }

                <?php
                endif;

                // Drop shadow

                $item_drop_shadow = get_option('lds_content_list_drop_shadow');

                if($item_drop_shadow && $item_drop_shadow != '' && $item_drop_shadow != 'none' ): ?>

                    .learndash-wrapper .ld-user-status.ld-is-widget .ld-item-list .ld-item-list-item {
                        box-shadow: none;
                    }

                    .learndash-wrapper .ld-assignment-list,
                    .learndash-wrapper .ld-table-list.ld-topic-list,
                    .learndash-wrapper .ld-item-list .ld-item-list-item  {
                        <?php
                        switch($item_drop_shadow):
                            case('light'): ?>
                                box-shadow: 0 0 15px rgba(0,0,0,0.05);
                                <?php
                                break;
                            case('light-plus'): ?>
                                box-shadow: 0 0 15px rgba(0,0,0,0.1);
                                <?php
                                break;
                            case('medium'): ?>
                                box-shadow: 3px 5px 35px rgba(0,0,0,0.05);
                                <?php
                                break;
                            case('medium-plus'): ?>
                                box-shadow: 3px 5px 35px rgba(0,0,0,0.1);
                                <?php
                                break;
                            case('heavy'): ?>
                                box-shadow: 3px 5px 35px rgba(0,0,0,0.1);
                                <?php
                                break;
                            case('heavy-plus'): ?>
                                box-shadow: 3px 5px 40px rgba(0,0,0,0.15);
                                <?php
                                break;
                            endswitch; ?>
                    }

                <?php
                endif;

                // Item Spacing

                $content_item_spacing = strval(get_option('lds_content_list_spacing'));

                if( $content_item_spacing == '0' || !empty($content_item_spacing) ): ?>

                    .learndash-wrapper .ld-item-list .ld-item-list-item {
                        margin-bottom: <?php echo $content_item_spacing; ?>px;
                    }

                <?php
                endif;

                $content_item_padding = strval( get_option('lds_content_list_padding') );

                if( $content_item_padding == '0' || !empty($content_item_padding) ): ?>

                    .learndash-wrapper .ld-item-list .ld-item-list-item .ld-item-list-item-preview {
                        padding: <?php echo $content_item_padding; ?>px;
                    }

                <?php
                endif;


                // Expanded

                $expanded_content_bg = get_option('lds_expanded_content_bg');

                if( $expanded_content_bg && $expanded_content_bg != '' ): ?>

                    .learndash-wrapper .ld-table-list-items,
                    .learndash-wrapper .ld-item-list-item-expanded {
                        background: <?php echo $expanded_content_bg; ?> !important;
                    }

                <?php
                endif;

                // Tertiary Colors

                $tertiary_bg_color = get_option('lds_tertiary_bg_color');

                if( $tertiary_bg_color && $tertiary_bg_color != '' ): ?>

                    .learndash-wrapper .wpProQuiz_graded_points, .learndash-wrapper .wpProQuiz_points,
                    .learndash-wrapper .ld-item-list .ld-item-list-item .ld-item-list-item-expanded .ld-progress,
                    .learndash-wrapper .ld-breadcrumbs,
                    .learndash-wrapper .ld-topic-status,
                    .learndash-wrapper .ld-course-navigation .ld-pagination .ld-pages,
                    .learndash-wrapper .ld-course-status.ld-course-status-not-enrolled,
                    .learndash-wrapper .wpProQuiz_graded_points,
                    .learndash-wrapper .wpProQuiz_points,
                    .learndash-wrapper .ld-focus .ld-focus-sidebar .ld-course-navigation .ld-topic-list.ld-table-list,
                    .learndash-wrapper .ld-table-list .ld-table-list-footer,
                    .learndash-wrapper .ld-pagination .ld-pages,
                    .learndash-wrapper.learndash-widget .ld-progress .ld-progress-bar,
                    .learndash-wrapper .ld-tabs .ld-tabs-navigation::after,
                    .learndash-wrapper .ld-course-status.ld-course-status-enrolled {
                        background: <?php echo $tertiary_bg_color; ?>;
                    }

                    <?php // TODO: Write a function to auto darken this // ?>
                    .learndash-wrapper .wpProQuiz_graded_points,
                    .learndash-wrapper .wpProQuiz_points {
                        border-color: <?php echo $tertiary_bg_color; ?>
                    }

                    .learndash-wrapper .ld-focus .ld-focus-sidebar,
                    .learndash-wrapper .ld-focus .ld-focus-sidebar .ld-course-navigation .ld-lesson-item,
                    .learndash-wrapper .ld-course-navigation .ld-lesson-item,
                    .learndash-wrapper .ld-course-navigation .ld-course-navigation-heading {
                        border-color: <?php echo $tertiary_bg_color; ?>;
                    }

                <?php
                endif;

                $tertiary_txt_color = get_option('lds_tertiary_txt_color');

                if( $tertiary_txt_color && $tertiary_txt_color != '' ): ?>

                    .learndash-wrapper .ld-course-navigation .ld-pagination .ld-pages,
                    .learndash-wrapper .ld-course-navigation .ld-pagination .ld-pages a,
                    .learndash-wrapper .ld-course-navigation .ld-pagination .ld-pages span,
                    .learndash-wrapper .ld-course-status.ld-course-status-not-enrolled .ld-course-status-price,
                    .learndash-wrapper .ld-course-status.ld-course-status-not-enrolled .ld-course-status-label,
                    .learndash-wrapper .ld-tabs .ld-tabs-navigation .ld-tab,
                    .learndash-wrapper .ld-pagination,
                    .learndash-wrapper .ld-item-list .ld-item-list-item .ld-item-title .ld-item-components {
                        color: <?php echo $tertiary_txt_color; ?> !important;
                    }

                <?php
                endif;

                $styles = array(
                    'fonts',
                    'icons',
                    'expanded',
                    'quizzes',
                    'focus',
                    'hide'
                );
                foreach( $styles as $style ) {
                    include( 'assets/css/' . $style . '.php' );
                }

                if( defined('LEARNDASH_COURSE_GRID_VERSION') && is_singular() ) {

                    global $post;

                    if( has_shortcode( $post->post_content, 'ld_course_list') || has_block( 'ld-course-list', $post)) {
                        include( 'assets/css/course-grid.php' );
                    }

                }


                $ldvc_theme = get_option('lds_skin');

                if( file_exists( LDS_PATH . 'inc/assets/css/themes/' . $ldvc_theme . '.php' ) ) {
                    include( 'assets/css/themes/' . $ldvc_theme . '.php' );
                } ?>

            </style>

        <?php

        $custom_css = ob_get_clean();

        $custom_css = str_replace( '<style type="text/css">', '', $custom_css );
        $custom_css = str_replace( '</style>', '', $custom_css );

        set_transient( 'lds_custom_css', $custom_css, WEEK_IN_SECONDS );

    endif;

    wp_add_inline_style( 'learndash-front', $custom_css );

    ob_start();

    $scripts = array(
        'icons'
    );

    foreach( $scripts as $script ) {
        include( 'assets/js/' . $script . '.php' );
    }

    $custom_js = ob_get_clean();

    $custom_js = str_replace( '<script>', '', $custom_js );
    $custom_js = str_replace( '</script>', '', $custom_js );

    wp_add_inline_script( 'learndash-front', $custom_js );

}

add_action( 'customize_controls_enqueue_scripts', 'ldvc_customizer_scripts' );
function ldvc_customizer_scripts() {

    $styles = array(
        'ldvc-customizer' => array(
            'path'  => LDS_URL . '/assets/css/admin/ldvc-customizer.css',
            'deps'  =>  array(),
            'ver'   =>  rand()
        )
    );

    foreach( $styles as $handle => $settings ) {
        wp_register_style( $handle, $settings['path'], $settings['deps'], $settings['ver'] );
        wp_enqueue_style( $handle );
    }

    $scripts = array(
        'ldvc-customizer'        => LDS_URL . '/assets/js/admin/ldvc-customizer.js'
    );

    foreach( $scripts as $handle => $url ) {
        wp_register_script( $handle, $url );
        wp_enqueue_script( $handle );
    }

    $theme_settings = lds_get_themes_scaffolding();

    wp_localize_script( 'ldvc-customizer', 'ldvc_themes', $theme_settings );

}

add_action( 'init', 'lds_clear_transient_css', 100 );
function lds_clear_transient_css() {
  delete_transient( 'lds_custom_css' );
}
