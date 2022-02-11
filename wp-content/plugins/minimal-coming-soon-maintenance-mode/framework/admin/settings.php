<?php

/**
 * Settings page for the plugin
 *
 * @link       http://www.webfactoryltd.com
 * @since      0.1
 */

require_once 'include/classes/class-mailchimp.php';

// main settings function
function csmm_admin_settings()
{

    if (isset($_POST['export-theme'])) { // import settings
        do_action('csmm_export_theme');
    } elseif (isset($_POST['submit-import'])) { // import settings
        unset($_POST['submit-import']);

        $import_data = csmm_validate_import_file();
        if (is_wp_error($import_data)) {
            set_transient('csmm_error_msg', '<div class="csmm-alert csmm-alert-info"><strong>' . $import_data->get_error_message() . '</strong><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>', 1);
        } else {
            $options = $import_data['data'];
            update_option('signals_csmm_options', $options);
            set_transient('csmm_error_msg', '<div class="csmm-alert csmm-alert-info"><strong>Settings have been imported.</strong><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>', 1);
        }
    } else if (!empty($_POST['signals_doing_save'])) {
        csmm_process_save();
        set_transient('signals_csmm_err_' . get_current_user_id(), '<div class="csmm-alert csmm-alert-success"><strong>Great!</strong> Settings have been saved.<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>', 60);
    } // if submit

    $signals_csmm_options = csmm_get_options();

    // all settings panels
    require 'views/settings.php';

    if (csmm_get_rebranding() === false && csmm_whitelabel_filter()) {
        echo '<script type="text/javascript">!function(e,t,n){function a(){var e=t.getElementsByTagName("script")[0],n=t.createElement("script");n.type="text/javascript",n.async=!0,n.src="https://beacon-v2.helpscout.net",e.parentNode.insertBefore(n,e)}if(e.Beacon=n=function(t,n,a){e.Beacon.readyQueue.push({method:t,options:n,data:a})},n.readyQueue=[],"complete"===t.readyState)return a();e.attachEvent?e.attachEvent("onload",a):e.addEventListener("load",a,!1)}(window,document,window.Beacon||function(){});</script>';
    }
} // csmm_admin_settings


// AJAX request for user support
function csmm_ajax_support()
{
    global $csmm_lc;
    // We are going to store the response in the $response() array
    $response = array(
        'code'         => 'error',
        'response'     => __('Please fill in all fields to create your support ticket.', 'signals')
    );


    // Filtering and sanitizing the support issue
    if (!empty($_POST['signals_support_email']) && !empty($_POST['signals_support_issue'])) {


        $urg_message = '';
        if (!empty($_POST['signals_support_email'])) {
            // urgency
            switch ($_POST['signals_support_urgency']) {
                case "low":
                    $urg_message = __('Low', 'signals');
                    break;
                case "normal":
                    $urg_message = __('Normal', 'signals');
                    break;
                case "urgent":
                    $urg_message = __('Urgent', 'signals');
                    break;
            }
        }


        $theme = wp_get_theme();
        $admin_email     = sanitize_text_field($_POST['signals_support_email']);
        $issue             = $_POST['signals_support_issue'];
        $options = get_option('signals_csmm_options');

        $subject         = '[Maintenance Mode PRO Ticket] [' . $urg_message . '] by ' . $admin_email;

        $body             = "Email: $admin_email \r\nIssue: $issue";
        $headers         = 'From: ' . $admin_email . "\r\n" . 'Reply-To: ' . $admin_email;

        $body .= "\r\n\r\nSite details:\r\n";
        $body .= '  WordPress version: ' . get_bloginfo('version') . "\r\n";
        $body .= '  CSMM PRO version: ' . csmm_get_plugin_version() . "\r\n";
        $body .= '  PHP version: ' . PHP_VERSION . "\r\n";
        $body .= '  Site URL: ' . get_bloginfo('url') . "\r\n";
        $body .= '  WordPress URL: ' . get_bloginfo('wpurl') . "\r\n";
        $body .= '  Theme: ' . $theme->get('Name') . ' v' . $theme->get('Version') . "\r\n";
        if ($csmm_lc->is_active()) {
            $body .= '  License key: ' . $csmm_lc->get_license('license_key') . "\r\n";;
            $body .= '  License details: ' . $csmm_lc->get_license('name') . ', expires on ' . $csmm_lc->get_license('valid_until') . "\r\n";;
        } else {
            $body .= '  License key: ' . (empty($csmm_lc->get_license('license_key')) ? 'n/a' : $csmm_lc->get_license('license_key')) . "\r\n";
        }
        $body .= '  Options: ' . "\r\n" . serialize($options) . "\r\n";


        // Sending the mail to the support email
        if (true === wp_mail('csmm@webfactoryltd.com', $subject, $body, $headers)) {
            // Sending the success response
            $response = array(
                'code'         => 'success',
                'response'     => __('We\'ve received your support ticket and will get back to you ASAP!', 'signals')
            );
        } else {
            // Sending the failure response
            $response = array(
                'code'         => 'error',
                'response'     => __('There was an error creating the support ticket. You can try again later or send us an email directly to <strong>csmm@webfactoryltd.com</strong>', 'signals')
            );
        }
    }


    // Sending proper headers and sending the response back in the JSON format
    header("Content-Type: application/json");
    echo json_encode($response);


    // Exiting the AJAX function. This is always required
    exit();
}
add_action('wp_ajax_signals_csmm_support', 'csmm_ajax_support');
add_action('wp_ajax_csmm_dismiss_pointer', 'csmm_dismiss_pointer_ajax');
add_action('wp_ajax_csmm_save_settings', 'csmm_ajax_save_settings');


function csmm_get_mc_lists($api_key)
{
    $lists = array();
    try {
        $mc = new csmm_MailChimp($api_key);
    } catch (Exception $e) {
        set_transient('csmm_error_msg', '<div class="csmm-alert csmm-alert-info"><strong>' . $e->getMessage() . '</strong><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>', 1);
        return false;
    }


    $raw_lists = $mc->get('lists', array('count' => 99));
    if ($mc->success()) {
        foreach ($raw_lists['lists'] as $list) {
            $lists[] = array('val' => $list['id'], 'label' => $list['name']);
        } // foreach list
        usort($lists, 'csmm_sort_select_options');
    } else {
        $lists = false;
    } // if success

    return $lists;
} // csmm_get_mc_lists


function csmm_sort_select_options($item1, $item2)
{
    return strnatcmp(strtoupper($item1['label']), strtoupper($item2['label']));
} // sort_select_list



function csmm_ajax_save_settings()
{
    check_ajax_referer('csmm_save_settings');
    $user_theme = false;
    if (isset($_POST['user_theme'])) {
        if (strlen($_POST['user_theme']) < 4) {
            wp_send_json_error('Invalid theme name');
        }

        if ($_POST['overwrite'] !== 'true') {
            $uploads = wp_upload_dir();
            $csmm_themes_folder = $uploads['basedir'] . '/coming-soon-themes/';
            clearstatcache();

            if (file_exists($csmm_themes_folder)) {
                $user_themes = array_diff(scandir($csmm_themes_folder), array('..', '.'));
                foreach ($user_themes as $theme_folder) {
                    if ($theme_folder == sanitize_title($_POST['user_theme'])) {
                        wp_send_json_error('overwrite');
                    }
                }
            }
        }

        $user_theme = $_POST['user_theme'];
    }

    parse_str($_POST['form_data'], $data);
    csmm_process_save($data);

    if (!empty($user_theme)) {
        $save_theme = csmm_save_theme($user_theme);
        if (is_wp_error($save_theme)) {
            return wp_send_json_error($save_theme->get_error_message());
        }
    }

    wp_send_json_success();
} // csmm_ajax_save_settings

function csmm_save_theme($name)
{
    $export_vars = array(
        'mode',
        'header_text',
        'secondary_text',
        'antispam_text',
        'arrange',
        'message_noemail',
        'message_subscribed',
        'message_wrong',
        'message_done',
        'content_1col_font',
        'content_1col_font_size',
        'content_1col_font_color',
        'content_2col_text_left',
        'content_2col_text_right',
        'content_2col_font',
        'content_2col_font_size',
        'content_2col_font_color',
        'divider_height',
        'divider_font_color',
        'divider_margin_top',
        'divider_margin_bottom',
        'logo',
        'bg_cover',
        'content_overlay',
        'content_overlay_mobile',
        'content_width',
        'bg_color',
        'content_position',
        'header_font',
        'secondary_font',
        'header_font_size',
        'secondary_font_size',
        'header_font_color',
        'secondary_font_color',
        'logo_link_url',
        'link_color',
        'link_hover_color',
        'antispam_font_size',
        'antispam_font_color',
        'form_placeholder_color',
        'submit_align',
        'input_text',
        'button_text',
        'ignore_form_styles',
        'input_font_size',
        'button_font_size',
        'input_font_color',
        'button_font_color',
        'input_bg',
        'button_bg',
        'input_bg_hover',
        'button_bg_hover',
        'input_border',
        'button_border',
        'input_border_hover',
        'button_border_hover',
        'success_background',
        'success_color',
        'error_background',
        'error_color',
        'disable_settings',
        'custom_html',
        'custom_css',
        'custom_html_layout'  => '',
        'signals_show_name',
        'signals_csmm_message_noname',
        'signals_csmm_message_no_name',
        'icon_size',
        'social_icons_color',
        'social_list_url',
        'social_list_icon',
        'countdown_color',
        'countdown_labels_color',
        'countdown_size',
        'countdown_labels_size',
        'countdown_date',
        'countdown_days',
        'countdown_hours',
        'countdown_minutes',
        'countdown_seconds',
        'map_address',
        'map_zoom',
        'map_height',
        'video_type',
        'video_id',
        'video_autoplay',
        'video_mute',
        'video_minimal',
        'video_embed_code',
        'progress_percentage',
        'progress_height',
        'progress_label_size',
        'progress_color',
        'progress_label_color',
        'overlay_color',
        'transparency_level',
        'module_margin',
        'logo_max_height',
        'logo_title',
        'custom_head_code',
        'custom_foot_code',
        'background_size_opt',
        'background_position',
        'background_image_filter',
        'background_type',
        'background_video',
        'background_video_filter',
        'animation',
        'bg_cover',
    );

    $out = $meta = $tmp_vars = array();
    $options = csmm_get_options();
    $images_cnt = 0;

    foreach ($options as $tmp_key => $tmp_value) {
        if (in_array($tmp_key, $export_vars)) {
            $tmp_vars[$tmp_key] = $tmp_value;
        }
    }
    $options = $tmp_vars;

    $meta = array('type' => 'CSMM USER', 'version' => csmm_get_plugin_version());
    $meta['last_edit'] = date('r');
    $meta['name'] = str_replace('\'', '&#39;', trim($name));
    $meta['name_clean'] = sanitize_title($name);
    $meta['folder_name'] = $meta['name_clean'];


    // Create Coming Soon themes folder if it does not exist
    $uploads = wp_upload_dir();
    $user_themes_url = $uploads['baseurl'] . '/coming-soon-themes/';
    $user_themes_folder = $uploads['basedir'] . '/coming-soon-themes/';
    $folder = wp_mkdir_p($user_themes_folder);
    if (!$folder) {
        return new WP_Error(1, 'Unable to create user themes folder ' . $user_themes_folder);
    }

    // Create current theme folder
    $theme_url = $user_themes_url . $meta['folder_name'] . '/';
    $theme_folder = $user_themes_folder . $meta['folder_name'] . '/';
    $folder = wp_mkdir_p($theme_folder);
    if (!$folder) {
        return new WP_Error(1, 'Unable to create user theme folder ' . $theme_folder);
    }

    // handle images
    if (!empty($options['favicon'])) {
        $tmp = basename($options['favicon']);
        csmm_image_copy($options['favicon'], $theme_folder . $tmp);
        $options['favicon'] = $theme_url . $tmp;
        $images_cnt++;
    }
    if (!empty($options['bg_cover']) && stripos($options['bg_cover'], 'assets.comingsoonwp.com') === false) {
        $tmp = basename($options['bg_cover']);
        csmm_image_copy($options['bg_cover'], $theme_folder . $tmp);
        $options['bg_cover'] = $theme_url . $tmp;
        $images_cnt++;
    }
    if (!empty($options['logo'])) {
        $tmp = basename($options['logo']);
        csmm_image_copy($options['logo'], $theme_folder . $tmp);
        $options['logo'] = $theme_url . $tmp;
        $images_cnt++;
    }

    // content images
    if (!empty($options['secondary_text'])) {
        if (preg_match_all('/<img.*?src="([^"]*)"[^>]*>/i', $options['secondary_text'], $images, PREG_PATTERN_ORDER)) {
            foreach ($images[1] as $img) {
                $ext = pathinfo($img, PATHINFO_EXTENSION);
                if (!in_array(strtolower($ext), array('jpg', 'jpeg', 'png', 'gif'))) {
                    continue;
                }
                copy($img, $theme_folder . basename($img));
                $options['secondary_text'] = str_replace($img, $theme_url . basename($img), $options['secondary_text']);
                $images_cnt++;
            }
        }
    } // if images in content

    $out['meta'] = $meta;
    $out['data'] = $options;

    // main export in plugin
    $filename = $theme_folder . $meta['name_clean'] . '.txt';
    $fp = fopen($filename, 'w');
    fwrite($fp, json_encode($out));
    fclose($fp);

    set_transient('csmm_error_msg', '<div class="csmm-alert csmm-alert-info"><strong>User theme saved!</strong><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>', 1);
} // export_theme

function csmm_image_copy($source, $dest)
{
    $uploads = wp_upload_dir();
    $image_path = $source;
    if (strpos($source, $uploads['baseurl']) === 0) {
        $image_path = str_replace($uploads['baseurl'], $uploads['basedir'], $source);
    }
    copy($image_path, $dest);
}

function csmm_process_save($data = null)
{
    if (!is_null($data)) {
        $_POST = $data;
    }

    $old_options = csmm_get_options();

    // Checking whether the user logged in option is checked or not
    if (isset($_POST['signals_csmm_showlogged'])) :
        $tmp_options['logged'] = absint($_POST['signals_csmm_showlogged']);
    else :
        $tmp_options['logged'] = '2';
    endif;


    // Checking whether the search engine exclusion option is checked or not
    if (isset($_POST['signals_csmm_excludese'])) :
        $tmp_options['exclude_se'] = absint($_POST['signals_csmm_excludese']);
    else :
        $tmp_options['exclude_se'] = '2';
    endif;


    // For the MailChimp list ID
    if (isset($_POST['signals_csmm_list'])) :
        $tmp_options['list'] = $_POST['signals_csmm_list'];
    else :
        $tmp_options['list'] = '';
    endif;


    // For content overlay
    if (isset($_POST['signals_csmm_overlay'])) :
        $tmp_options['overlay'] = absint($_POST['signals_csmm_overlay']);
    else :
        $tmp_options['overlay'] = '2';
    endif;

    if (isset($_POST['signals_csmm_overlay_mobile'])) :
        $tmp_options['overlay_mobile'] = absint($_POST['signals_csmm_overlay_mobile']);
    else :
        $tmp_options['overlay_mobile'] = '2';
    endif;

    // Checking whether the ignore form styles option is checked or not
    if (isset($_POST['signals_csmm_ignore_styles'])) :
        $tmp_options['form_styles'] = absint($_POST['signals_csmm_ignore_styles']);
    else :
        $tmp_options['form_styles'] = '2';
    endif;

    $mc_lists = $old_options['mc_lists'];
    if (!empty($_POST['signals_change_mc_api']) || (!empty($_POST['signals_csmm_api']) && ($_POST['signals_csmm_api'] != $old_options['mailchimp_api'] || empty($mc_lists)))) {
        try {
            $mc = new csmm_MailChimp($_POST['signals_csmm_api']);
        } catch (Exception $e) {
            set_transient('csmm_error_msg', '<div class="csmm-alert csmm-alert-info"><strong>' . $e->getMessage() . '</strong><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>', 1);
            return false;
        }


        $test = $mc->get('');
        if (false == $mc->success()) {
            $mc_lists = false;
        } else {
            $mc_lists = csmm_get_mc_lists($_POST['signals_csmm_api']);
        }
    } elseif (empty($_POST['signals_csmm_api'])) {
        $mc_lists = false;
    }

    if (isset($_POST['signals_csmm_wpm_replace']) && $_POST['signals_csmm_wpm_replace'] == '1') {
        $wpm_content = '<!DOCTYPE html>
            <html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
            <!-- CSMM -->
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                <meta name="viewport" content="width=device-width">
                <title>' . stripslashes($_POST['signals_csmm_wpm_title']) . '</title>
                <style type="text/css">
                html {
                    background: #f1f1f1;
                }
                body {
                    background: #fff;
                    color: #444;
                    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
                    margin: 2em auto;
                    padding: 1em 2em;
                    max-width: 700px;
                    -webkit-box-shadow: 0 1px 3px rgba(0, 0, 0, 0.13);
                    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.13);
                }
                h1 {
                    border-bottom: 1px solid #fe2d2d;
                    clear: both;
                    color: #666;
                    font-size: 24px;
                    margin: 30px 0 0 0;
                    padding: 0;
                    padding-bottom: 7px;
                }
                #error-page {
                    margin-top: 50px;
                }
                #error-page p,
                #error-page .wp-die-message {
                    font-size: 14px;
                    line-height: 1.5;
                    margin: 25px 0 20px;
                }
                #error-page code {
                    font-family: Consolas, Monaco, monospace;
                }
                ul li {
                    margin-bottom: 10px;
                    font-size: 14px ;
                }
                a {
                    color: #0073aa;
                }
                a:hover,
                a:active {
                    color: #00a0d2;
                }
                a:focus {
                    color: #124964;
                    -webkit-box-shadow:
                    0 0 0 1px #5b9dd9,
                    0 0 2px 1px rgba(30, 140, 190, 0.8);
                    box-shadow:
                    0 0 0 1px #5b9dd9,
                    0 0 2px 1px rgba(30, 140, 190, 0.8);
                    outline: none;
                }
                </style>
            </head>
            <body id="error-page">
                <div class="wp-die-message">
                <h1>' . stripslashes($_POST['signals_csmm_wpm_title']) . '</h1>
                <p>' . stripslashes($_POST['signals_csmm_wpm_content']) . '</p>
                </div>
            </body>
            </html>';

        if (false === file_put_contents(trailingslashit(WP_CONTENT_DIR) . 'maintenance.php', $wpm_content)) {
            set_transient('csmm_error_msg', '<div class="csmm-alert csmm-alert-info"><strong>Could not write the maintenance file to overwrite the default WordPress maintenance mode output.</strong><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>', 1);
            return false;
        }
    } else {
        if (file_exists(trailingslashit(WP_CONTENT_DIR) . 'maintenance.php')) {
            $maintenance_contents = file_get_contents(trailingslashit(WP_CONTENT_DIR) . 'maintenance.php');
            if (stripos($maintenance_contents, '<!-- CSMM -->') !== false) {
                unlink(trailingslashit(WP_CONTENT_DIR) . 'maintenance.php');
            }
        }
    }


    // Saving the record to the database
    $update_options = array(
        'mode'         => strip_tags($_POST['signals_csmm_mode']),
        'csmm_page'    => @absint($_POST['signals_csmm_page']),
        'mc_lists' => $mc_lists,
        'status'        => @absint($_POST['signals_csmm_status']),
        'title'         => strip_tags($_POST['signals_csmm_title']),
        'description'         => strip_tags($_POST['signals_csmm_description']),
        'header_text'       => $_POST['signals_csmm_header'],
        'secondary_text'     => trim($_POST['signals_csmm_secondary']),
        'content_1col_font'     => strip_tags($_POST['signals_csmm_content_1col_font']),
        'content_1col_font_size'   => (int) $_POST['signals_csmm_content_1col_size'],
        'content_1col_font_color'   => strip_tags($_POST['signals_csmm_content_1col_color']),
        'content_2col_text_left'     => trim($_POST['signals_csmm_content_2col_text_left']),
        'content_2col_text_right'     => trim($_POST['signals_csmm_content_2col_text_right']),
        'content_2col_font'     => strip_tags($_POST['signals_csmm_content_2col_font']),
        'content_2col_font_size'   => (int) $_POST['signals_csmm_content_2col_size'],
        'content_2col_font_color'   => strip_tags($_POST['signals_csmm_content_2col_color']),

        'content_2col_divider_width'   => strip_tags($_POST['signals_csmm_content_2col_divider_width']),
        'content_2col_divider_color'   => strip_tags($_POST['signals_csmm_content_2col_divider_color']),

        'content_2col_padding'    => strip_tags($_POST['signals_csmm_content_2col_padding']),

        'divider_height'   => (int) $_POST['signals_csmm_divider_height'],


        'divider_color'   => strip_tags($_POST['signals_csmm_divider_color']),
        'divider_margin_top'   => (int) @$_POST['signals_csmm_divider_margin_top'],
        'divider_margin_bottom'   => (int) $_POST['signals_csmm_divider_margin_bottom'],

        'antispam_text'     => strip_tags($_POST['signals_csmm_antispam']),
        'custom_login_url'     => strip_tags($_POST['signals_csmm_custom_login']),
        'show_logged_in'     => $tmp_options['logged'],
        'exclude_se'      => $tmp_options['exclude_se'],
        'block_se'      => (int) @$_POST['signals_csmm_blockse'],
        'arrange'         => strip_tags($_POST['signals_csmm_arrange']),
        'analytics'       => trim($_POST['signals_csmm_analytics']),
        'tracking_pixel'       => trim($_POST['tracking_pixel']),

        'mailchimp_api'      => strip_tags($_POST['signals_csmm_api']),
        'mail_system_to_use'      => strip_tags($_POST['mail_system_to_use']),
        'target_keyword'         => strip_tags(trim($_POST['signals_csmm_target_keyword'])),

        'signals_show_name'      => (int) @$_POST['signals_show_name'],

        'signal_zapier_action_url'      => strip_tags($_POST['signal_zapier_action_url']),
        'signal_ua_action_url'      => strip_tags($_POST['signal_ua_action_url']),
        'signal_ua_method'      => strip_tags($_POST['signal_ua_method']),
        'signal_ua_email_field_name'      => strip_tags($_POST['signal_ua_email_field_name']),
        'signal_ua_name_field_name'      => strip_tags($_POST['signal_ua_name_field_name']),
        'signal_ua_additional_data'      => strip_tags($_POST['signal_ua_additional_data']),
        'signals_double_optin'      => (int) @$_POST['signals_double_optin'],
        'mail_debug'      => (int) @$_POST['mail_debug'],

        'signals_csmm_message_noname'      => strip_tags($_POST['signals_csmm_message_noname']),


        'mailchimp_list'     => $tmp_options['list'],
        'message_noemail'     => strip_tags($_POST['signals_csmm_message_noemail']),
        'message_no_name'     => strip_tags($_POST['signals_csmm_message_no_name']),
        'message_subscribed'   => strip_tags($_POST['signals_csmm_message_subscribed']),
        'message_wrong'     => strip_tags($_POST['signals_csmm_message_wrong']),
        'message_done'       => strip_tags($_POST['signals_csmm_message_done']),

        'logo'          => strip_tags($_POST['signals_csmm_logo']),
        'favicon'        => strip_tags($_POST['signals_csmm_favicon']),
        'social_preview'        => strip_tags($_POST['signals_csmm_social_preview']),
        'bg_cover'         => strip_tags($_POST['signals_csmm_bg']),
        'background_video_fallback'         => strip_tags($_POST['signals_csmm_signals_fallback']),
        'content_overlay'     => $tmp_options['overlay'],
        'content_overlay_mobile' => absint($tmp_options['overlay_mobile']),
        'content_width'      => absint($_POST['signals_csmm_width']),
        'bg_color'         => strip_tags($_POST['signals_csmm_color']),
        'content_position'    => strip_tags($_POST['signals_csmm_position']),
        'header_font'       => strip_tags($_POST['signals_csmm_header_font']),
        'secondary_font'     => strip_tags($_POST['signals_csmm_secondary_font']),
        'header_font_size'     => strip_tags($_POST['signals_csmm_header_size']),
        'secondary_font_size'   => strip_tags($_POST['signals_csmm_secondary_size']),
        'header_font_color'   => strip_tags($_POST['signals_csmm_header_color']),
        'secondary_font_color'   => strip_tags($_POST['signals_csmm_secondary_color']),
        'link_color'   => strip_tags($_POST['signals_csmm_link_color']),
        'link_hover_color'   => strip_tags($_POST['signals_csmm_link_hover_color']),
        'antispam_font_size'   => strip_tags($_POST['signals_csmm_antispam_size']),
        'antispam_font_color'   => strip_tags($_POST['signals_csmm_antispam_color']),

        'logo_link_url' =>  strip_tags($_POST['csmm_logo_link_url']),

        'submit_align'       => $_POST['submit_align'],
        'input_text'       => strip_tags($_POST['signals_csmm_input_text']),
        'gdpr_text'        => trim($_POST['signals_csmm_gdpr_text']),
        'gdpr_policy_text'     => trim($_POST['signals_csmm_gdpr_policy_text']),
        'gdpr_error_text'     => trim($_POST['signals_csmm_gdpr_error_text']),
        'button_text'       => strip_tags($_POST['signals_csmm_button_text']),
        'ignore_form_styles'   => $tmp_options['form_styles'],
        'input_font_size'    => strip_tags($_POST['signals_csmm_input_size']),
        'button_font_size'    => strip_tags($_POST['signals_csmm_button_size']),
        'input_font_color'    => strip_tags($_POST['signals_csmm_input_color']),
        'button_font_color'    => strip_tags($_POST['signals_csmm_button_color']),
        'input_bg'        => strip_tags($_POST['signals_csmm_input_bg']),
        'button_bg'        => strip_tags($_POST['signals_csmm_button_bg']),
        'input_bg_hover'    => strip_tags($_POST['signals_csmm_input_bg_hover']),
        'button_bg_hover'    => strip_tags($_POST['signals_csmm_button_bg_hover']),
        'input_border'      => strip_tags($_POST['signals_csmm_input_border']),
        'button_border'      => strip_tags($_POST['signals_csmm_button_border']),
        'input_border_hover'  => strip_tags($_POST['signals_csmm_input_border_hover']),
        'button_border_hover'  => strip_tags($_POST['signals_csmm_button_border_hover']),
        'success_background'  => strip_tags($_POST['signals_csmm_success_bg']),
        'success_color'      => strip_tags($_POST['signals_csmm_success_color']),
        'error_background'    => strip_tags($_POST['signals_csmm_error_bg']),
        'error_color'      => strip_tags($_POST['signals_csmm_error_color']),
        'form_placeholder_color'      => strip_tags($_POST['form_placeholder_color']),

        'disable_settings'     => 0,
        'disable_adminbar'     => (int) @$_POST['signals_disable_adminbar'],
        'forcessl'     => (int) @$_POST['csmm_forcessl'],
        'track_stats'     => (int) @$_POST['csmm_track_stats'],
        'wprest'     => (int) @$_POST['csmm_wprest'],
        'wpm_replace'      => @$_POST['signals_csmm_wpm_replace'],
        'wpm_title'      => $_POST['signals_csmm_wpm_title'],
        'wpm_content'      => $_POST['signals_csmm_wpm_content'],
        'custom_html'      => $_POST['signals_csmm_html'],
        'custom_html_layout' => $_POST['signals_custom_html_layout'],
        'custom_css'      => $_POST['signals_csmm_css'],

        'signals_ip_whitelist'      => strip_tags($_POST['signals_ip_whitelist']),
        'per_url_settings'      => $_POST['per_url_settings'],
        'per_url_enable_disable'      => strip_tags($_POST['per_url_enable_disable']),
        'direct_access_link'      => strip_tags($_POST['direct_access_link']),
        'direct_access_password'      => strip_tags($_POST['signals_csmm_direct_access_password']),
        'site_password'      => strip_tags($_POST['signals_csmm_site_password']),
        'password_button'      => @absint($_POST['signals_csmm_password_button']),
        'login_button'      => strip_tags(@$_POST['signals_csmm_login_button']),
        'login_button_text'    => strip_tags($_POST['signals_csmm_login_button_text']),
        'login_message'    => strip_tags($_POST['signals_csmm_login_message']),
        'login_wrong_password_text' => strip_tags($_POST['signals_csmm_login_wrong_password_text']),
        'wplogin_button_tooltip'      => isset($_POST['signals_csmm_login_button_tooltip']) ? strip_tags($_POST['signals_csmm_wplogin_button_tooltip']) : 'Access WordPress admin',
        'login_button_tooltip'      => isset($_POST['signals_csmm_login_button_tooltip']) ? strip_tags($_POST['signals_csmm_login_button_tooltip']) : 'Direct Access login',

        'icon_size'      => strip_tags($_POST['icon_size']),
        'social_icons_color'      => strip_tags($_POST['social_icons_color']),
        'logo_max_height'      => (int) $_POST['logo_max_height'],
        'logo_title'      => trim(strip_tags($_POST['logo_title'])),

        'social_list_url'      => $_POST['social_list_url'],
        //'social_list_text'      => $_POST['social_list_text'],
        'social_list_icon'      => $_POST['social_list_icon'],

        'map_address' => trim(strip_tags($_POST['map_address'])),
        'map_zoom' => (int) $_POST['map_zoom'],
        'map_height' => (int) $_POST['map_height'],
        'map_api_key' => trim(strip_tags($_POST['map_api_key'])),

        'overlay_color'      => $_POST['overlay_color'],
        'transparency_level'      => $_POST['transparency_level'],

        'module_margin'      => (int) $_POST['module_margin'],
        'nocache'      => (int) @$_POST['csmm_nocache'],

        'video_type' => $_POST['video_type'],
        'video_id' => trim(@$_POST['video_id']),
        'video_autoplay' => (int) @$_POST['video_autoplay'],
        'video_mute' => (int) @$_POST['video_mute'],
        'video_minimal' => (int) @$_POST['video_minimal'],
        'video_embed_code' => trim(@$_POST['video_embed_code']),

        'custom_head_code'      => $_POST['custom_head_code'],
        'custom_foot_code'      => $_POST['custom_foot_code'],

        'progress_percentage' => (int) $_POST['progress_percentage'],
        'progress_height' => (int) $_POST['progress_height'],
        'progress_label_size' => (int) $_POST['progress_label_size'],
        'progress_color' => trim($_POST['progress_color']),
        'progress_label_color' => trim($_POST['progress_label_color']),

        'countdown_date' => trim(strip_tags($_POST['countdown_date'])),
        'countdown_color' => trim(strip_tags($_POST['countdown_color'])),
        'countdown_labels_color' => trim(strip_tags($_POST['countdown_labels_color'])),
        'countdown_size' => (int) $_POST['countdown_size'],
        'countdown_labels_size' => (int) $_POST['countdown_labels_size'],
        'countdown_days' => trim(strip_tags($_POST['countdown_days'])),
        'countdown_hours' => trim(strip_tags($_POST['countdown_hours'])),
        'countdown_minutes' => trim(strip_tags($_POST['countdown_minutes'])),
        'countdown_seconds' => trim(strip_tags($_POST['countdown_seconds'])),

        'background_type'      => $_POST['background_type'],
        'background_video_filter'      => $_POST['background_video_filter'],
        'background_video'      => trim(strip_tags($_POST['background_video'])),
        'background_size_opt'      => $_POST['background_size_opt'],
        'background_position'      => $_POST['background_position'],
        'background_image_filter'      => $_POST['background_image_filter'],

        'twitter_site' => trim(strip_tags($_POST['twitter_site'])),
        'facebook_site' => trim(strip_tags($_POST['facebook_site'])),

        'animation' => $_POST['animation'],
        'whitelabel' => $old_options['whitelabel'],

        'contact_show_name' => strip_tags($_POST['csmm_contact_show_name']),
        'contact_email_subject' => strip_tags($_POST['csmm_contact_email_subject']),
        'contact_admin_email' => strip_tags($_POST['csmm_contact_admin_email']),
        'contact_message_noname' => strip_tags($_POST['csmm_contact_message_noname']),
        'contact_input_text' => strip_tags($_POST['csmm_contact_input_text']),
        'contact_message_text' => strip_tags($_POST['csmm_contact_message_text']),
        'contact_button_text' => strip_tags($_POST['csmm_contact_button_text']),
        'contact_gdpr_text' => trim($_POST['csmm_contact_gdpr_text']),
        'contact_gdpr_error_text' => trim($_POST['csmm_contact_gdpr_error_text']),
        'contact_gdpr_policy_text' => trim($_POST['csmm_contact_gdpr_policy_text']),
        'contact_antispam' => strip_tags($_POST['csmm_contact_antispam']),
        'contact_submit_align' => strip_tags($_POST['csmm_contact_submit_align']),
        'contact_antispam_size' => strip_tags($_POST['csmm_contact_antispam_size']),
        'contact_antispam_color' => strip_tags($_POST['csmm_contact_antispam_color']),
        'contact_success_bg' => strip_tags($_POST['csmm_contact_success_bg']),
        'contact_success_color' => strip_tags($_POST['csmm_contact_success_color']),
        'contact_error_bg' => strip_tags($_POST['csmm_contact_error_bg']),
        'contact_error_color' => strip_tags($_POST['csmm_contact_error_color']),
        'contact_placeholder_color' => strip_tags($_POST['csmm_contact_placeholder_color']),
        'contact_ignore_styles' => strip_tags($_POST['csmm_contact_ignore_styles']),
        'contact_input_size' => strip_tags($_POST['csmm_contact_input_size']),
        'contact_button_size' => strip_tags($_POST['csmm_contact_button_size']),
        'contact_button_color' => strip_tags($_POST['csmm_contact_button_color']),
        'contact_input_bg' => strip_tags($_POST['csmm_contact_input_bg']),
        'contact_button_bg' => strip_tags($_POST['csmm_contact_button_bg']),
        'contact_button_bg_hover' => strip_tags($_POST['csmm_contact_button_bg_hover']),
        'contact_input_border' => strip_tags($_POST['csmm_contact_input_border']),
        'contact_button_border' => strip_tags($_POST['csmm_contact_button_border']),
        'contact_input_border_hover' => strip_tags($_POST['csmm_contact_input_border_hover']),
        'contact_button_border_hover' => strip_tags($_POST['csmm_contact_button_border_hover']),
        'contact_input_color' => strip_tags($_POST['csmm_contact_input_color']),

        'recaptcha' => strip_tags($_POST['csmm_recaptcha']),
        'recaptcha_site_key' => strip_tags($_POST['csmm_recaptcha_site_key']),
        'recaptcha_secret_key' => strip_tags($_POST['csmm_recaptcha_secret_key']),
    );

    $update_options = stripslashes_deep($update_options);

    update_option('signals_csmm_options', $update_options);

    if ($old_options['status'] != $update_options['status']) {
        global $csmm_lc;
        $csmm_lc->query_licensing_server('validate_license', array());
    }

    csmm_clear_3rd_party_cache();
} // csmm_process_save
