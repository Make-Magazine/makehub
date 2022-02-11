<?php

class CSMM
{
    static function init()
    {
        if (is_admin()) {
            add_action('admin_action_csmm_change_status', array(__CLASS__, 'change_status'));
        }

        // admin bar notice for frontend & backend
        add_action('wp_before_admin_bar_render', array(__CLASS__, 'admin_bar'));
        add_action('wp_head', array(__CLASS__, 'admin_bar_style'));
        add_action('admin_head', array(__CLASS__, 'admin_bar_style'));
    }

    static function admin_bar_style()
    {
        global $csmm_lc;
        $options = csmm_get_options();

        // admin bar has to be anabled, user an admin and custom filter true
        if ($options['disable_adminbar'] || csmm_plugin_page() || !$csmm_lc->is_active() || false === is_admin_bar_showing() || false === current_user_can('administrator') || false === apply_filters('csmm_show_admin_bar', true)) {
            return;
        }

        // no sense in loading a new CSS file for 2 lines of CSS
        $custom_css = '<style type="text/css">#wpadminbar i.csmm-status-dot { font-size: 17px; margin-top: -7px; color: #02ca02; height: 17px; display: inline-block; } #wpadminbar i.csmm-status-dot-enabled { color: #64bd63; } #wpadminbar i.csmm-status-dot-disabled { color: #FE2D2D; } #wpadminbar #csmm-status-wrapper { display: inline; border: 1px solid rgba(240,245,250,0.7); padding: 0; margin: 0 0 0 5px; background: rgb(35, 40, 45); } #wpadminbar .csmm-status-btn { padding: 0 7px; color: #fff; } #wpadminbar #csmm-status-wrapper.off #csmm-status-off { background: #FE2D2D;} #wpadminbar #csmm-status-wrapper.on #csmm-status-on { background: #64bd63; }#wp-admin-bar-csmm img.logo { height: 17px; margin-bottom: 4px; padding-right: 3px; } #wp-admin-bar-csmm a img { height: 17px; margin-bottom: -2px; padding-right: 3px; display:inline-block; } #wpadminbar #wp-admin-bar-csmm-status .ab-empty-item { margin-bottom: 2px; }</style>';

        echo $custom_css;
    } // admin_bar_style

    // add admin bar menu and status
    static function admin_bar()
    {
        global $wp_admin_bar, $csmm_lc;
        $options = csmm_get_options();

        // only show to admins
        if ($options['disable_adminbar'] || csmm_plugin_page() || !$csmm_lc->is_active() || false === current_user_can('administrator') || false === apply_filters('csmm_show_admin_bar', true)) {
            return;
        }

        $options = csmm_get_options();

        if (isset($_POST['signals_csmm_submit'])) {
            $options['status'] = (string) $_POST['signals_csmm_status'];
        }

        $short_name = csmm_get_rebranding('short_name');
        if ($short_name === false || empty($short_name)) {
            $short_name = 'Coming Soon';
            $show_icon = true;
        } else {
            $show_icon = false;
        }

        if ($options['status'] == '1') {
            $main_label = ($show_icon ? '<img src="' . CSMM_URL . '/framework/admin/img/mm-icon.png" alt="Coming Soon mode is enabled" title="Coming Soon mode is enabled">' : '') . '<span class="ab-label">' . $short_name . ' <i class="csmm-status-dot csmm-status-dot-enabled">&#9679;</i></span>';
            $class = 'csmm-enabled';
            $action_url = add_query_arg(array('action' => 'csmm_change_status', 'new_status' => 'disabled', 'redirect' => urlencode($_SERVER['REQUEST_URI'])), admin_url('admin.php'));
            $action_url = wp_nonce_url($action_url, 'csmm_change_status');
            $action = 'Coming Soon mode';
            $action .= '<a href="' . $action_url . '" id="csmm-status-wrapper" class="on"><span id="csmm-status-off" class="csmm-status-btn">OFF</span><span id="csmm-status-on" class="csmm-status-btn">ON</span></a>';
        } else {
            $main_label = ($show_icon ? '<img src="' . CSMM_URL . '/framework/admin/img/mm-icon.png" alt="Coming Soon mode is enabled" title="Coming Soon mode is enabled">' : '') . '<span class="ab-label">' . $short_name . ' <i class="csmm-status-dot csmm-status-dot-disabled">&#9679;</i></span>';
            $class = 'csmm-disabled';
            $action_url = add_query_arg(array('action' => 'csmm_change_status', 'new_status' => 'enabled', 'redirect' => urlencode($_SERVER['REQUEST_URI'])), admin_url('admin.php'));
            $action_url = wp_nonce_url($action_url, 'csmm_change_status');
            $action = 'Coming Soon mode';
            $action .= '<a href="' . $action_url . '" id="csmm-status-wrapper" class="off"><span id="csmm-status-off" class="csmm-status-btn">OFF</span><span id="csmm-status-on" class="csmm-status-btn">ON</span></a>';
        }

        $wp_admin_bar->add_menu(array(
            'parent' => '',
            'id'     => 'csmm',
            'title'  => $main_label,
            'href'   => admin_url('options-general.php?page=maintenance_mode_options'),
            'meta'   => array('class' => $class)
        ));
        $wp_admin_bar->add_node(array(
            'id'    => 'csmm-status',
            'title' => $action,
            'href'  => false,
            'parent' => 'csmm'
        ));
        $wp_admin_bar->add_node(array(
            'id'     => 'csmm-preview',
            'title'  => 'Preview',
            'href'   => home_url('/') . '?csmm_preview',
            'parent' => 'csmm',
            'meta'   => array('target' => '_blank')
        ));
        $wp_admin_bar->add_node(array(
            'id'     => 'csmm-settings',
            'title'  => 'Settings',
            'href'   => admin_url('options-general.php?page=maintenance_mode_options'),
            'parent' => 'csmm'
        ));
    } // admin_bar


    // change status via admin bar
    static function change_status()
    {
        check_admin_referer('csmm_change_status');

        if (empty($_GET['new_status'])) {
            wp_safe_redirect(admin_url());
            exit;
        }

        $options = csmm_get_options();

        if ($_GET['new_status'] == 'enabled') {
            $options['status'] = '1';
        } else {
            $options['status'] = '0';
        }

        update_option('signals_csmm_options', $options);

        if (!empty($_GET['redirect'])) {
            wp_safe_redirect($_GET['redirect']);
        } else {
            wp_safe_redirect(admin_url());
        }

        exit;
    } // change_status
} // class csmm

require_once CSMM_PATH . '/framework/admin/stats.php';

function csmm_get_rebranding($key = false)
{
    global $csmm_lc;
    $license = $csmm_lc->get_license();
    if (array_key_exists('rebrand', $license['meta']) && !empty($license['meta']['rebrand'])) {
        if (!empty($key)) {
            return $license['meta']['rebrand'][$key];
        }
        return $license['meta']['rebrand'];
    } else {
        return false;
    }
}

function csmm_default_options()
{
    $default_options = array(
        'status'        => '0',
        'mode'          => 'layout',
        'csmm_page'    => 0,
        'title'         => get_bloginfo('name') . ' is coming soon',
        'description'   => 'We are doing some work on our site. Please come back later. We\'ll be up and running in no time.',
        'header_text'       => 'Our new site is coming soon!',
        'secondary_text'     => '<p>We are doing some work on our site. It won\'t take long, we promise. Come back and visit us again in a few days. Thank you for your patience!</p>',
        'content_1col_font'     => 'Karla',
        'content_1col_font_size'   => '16',
        'content_1col_font_color'   => 'CCCCCC',
        'content_2col_text_left'     => '',
        'content_2col_text_right'     => '',
        'content_2col_font'     => 'Karla',
        'content_2col_font_size'   => '16',
        'content_2col_font_color'   => 'CCCCCC',
        'content_2col_divider_width'   => '1',
        'content_2col_divider_color'   => 'CCCCCC',
        'content_2col_padding'   => '10',

        'divider_height'   => '1',
        'divider_font_color'   => 'CCCCCC',
        'divider_margin_top'   => '10',
        'divider_margin_bottom'   => '10',

        'antispam_text'     => 'Yes, we hate spam too!',
        'custom_login_url'     => '',
        'show_logged_in'     => '1',
        'exclude_se'      => '0',
        'block_se' => '0',
        'arrange'         => 'logo,header,content,video,social',
        'analytics'       => '',
        'tracking_pixel'   => '',
        'target_keyword'    => 'coming soon',
        'mailchimp_api'      => '',
        'mailchimp_list'     => '',
        'message_noemail'     => 'Please provide a valid email address.',
        'message_subscribed'   => 'You are already subscribed!',
        'message_wrong'     => 'Something went wrong. Please reload the page and try again.',
        'message_done'       => 'Thank you! We\'ll be in touch!',

        'logo'          => CSMM_ASSETS . '/themes/default/csmm-logo.png',
        'favicon'        => CSMM_ASSETS . '/themes/default/csmm-favicon.png',
        'social_preview'        => CSMM_ASSETS . '/themes/default/csmm-social-preview.jpg',
        'bg_cover'         => CSMM_ASSETS . '/themes/default/mountain-bg.jpg',
        'content_overlay'     => 1,
        'content_overlay_mobile' => '0',
        'content_width'      => '600',
        'bg_color'         => 'FFFFFF',
        'content_position'    => 'center',
        'header_font'       => 'Karla',
        'secondary_font'     => 'Karla',
        'header_font_size'     => '28',
        'secondary_font_size'   => '16',
        'header_font_color'   => 'FFFFFF',
        'secondary_font_color'   => 'FFFFFF',
        'link_color'   => '0096ff',
        'link_hover_color'   => '57baff',
        'antispam_font_size'   => '12',
        'antispam_font_color'   => 'BBBBBB',
        'form_placeholder_color'   => '8F8F8F',

        'logo_link_url' => '',

        'input_text'       => 'Your best email address',
        'gdpr_text'        => '', //I understand the site\'s privacy policy and am willingly sharing my email address
        'gdpr_policy_text' => '',
        'gdpr_error_text' => 'Please confirm the subscription terms with the checkbox below',
        'button_text'      => 'Subscribe',
        'ignore_form_styles'   => 1,
        'submit_align' => 'left',
        'input_font_size'    => '13',
        'button_font_size'    => '12',
        'input_font_color'    => '696969',
        'button_font_color'    => 'FFFFFF',
        'input_bg'        => 'FFFFFF',
        'button_bg'        => '0F0F0F',
        'input_bg_hover'    => 'EEEEEE',
        'button_bg_hover'    => '0A0A0A',
        'contact_message_text' => 'Write your awesome message',
        'input_border'      => 'EEEEEE',
        'button_border'      => '0F0F0F',
        'input_border_hover'  => 'BBBBBB',
        'button_border_hover'  => '0A0A0A',
        'success_background'   => '90C695',
        'success_color'     => 'FFFFFF',
        'error_background'     => 'E08283',
        'error_color'       => 'FFFFFF',

        'disable_settings'     => '0',
        'disable_adminbar' => '0',
        'forcessl' => '0',
        'track_stats' => 1,
        'wprest' => '0',
        'wpm_replace'      => '0',
        'wpm_title'      => 'We\'re doing some maintenance and will be back in a few minutes',
        'wpm_content'      => '',
        'custom_html'      => '',
        'custom_html_layout'  => '',
        'custom_css'      => '',

        'mail_system_to_use'      => 'mc',
        'mail_debug' => '0',
        'mc_lists' => false,
        'signals_double_optin'      => '',
        'signals_show_name'      => '1',
        'signal_zapier_action_url'      => '',
        'signal_ua_action_url'      => '',
        'signal_ua_method'      => '',
        'signal_ua_email_field_name'      => '',
        'signal_ua_name_field_name'      => '',
        'signal_ua_additional_data'      => '',
        'signals_autoconfigure'      => '',
        'signals_csmm_message_noname'      => 'Your name',
        'message_no_name'      => 'Please enter your name',

        'signals_ip_whitelist'      => '',
        'per_url_settings'      => '',
        'per_url_enable_disable'      => '',
        'direct_access_link'      => '',
        'url_based_rules'      => '',
        'direct_access_password'     => '',
        'site_password'     => '',
        'password_button'     => '0',
        'login_button' => '0',
        'login_button_text' => 'Access the Site',
        'login_message' => 'Please enter the password to access the site',
        'login_wrong_password_text' => 'Wrong password',
        'wplogin_button_tooltip' => 'Access WordPress admin',
        'login_button_tooltip' => 'Direct Access login',

        'nocache' => '1',

        'icon_size'      => 'medium',
        'social_icons_color'      => 'FFFFFF',
        'social_list_url' => array('#', '#', '#', '#'),
        'social_list_icon' => array('57706', '57706', '57710', '57702'),

        'countdown_color' => '000000',
        'countdown_labels_color' => '444444',
        'countdown_size' => '25',
        'countdown_labels_size' => '12',
        'countdown_date' => date('Y-m-d', time() + DAY_IN_SECONDS * 30) . ' 12:00',
        'countdown_days' => 'days',
        'countdown_hours' => 'hours',
        'countdown_minutes' => 'min',
        'countdown_seconds' => 'sec',

        'map_address' => 'New York, USA',
        'map_zoom' => '15',
        'map_height' => '250',
        'map_api_key' => 'AIzaSyAAznHhsGtU4J4ua8btTu_qHH2KBngDT3A',

        'video_type' => 'youtube',
        'video_id' => 'YE7VzlLtp-4',
        'video_autoplay' => '0',
        'video_mute' => '0',
        'video_minimal' => '1',
        'video_embed_code' => '',

        'progress_percentage' => '33',
        'progress_height' => '35',
        'progress_label_size' => '20',
        'progress_color' => 'FF0000',
        'progress_label_color' => 'FFFFFF',

        'overlay_color'      => '000000',
        'transparency_level'      => '60',

        'module_margin'      => '25',
        'logo_max_height'      => '150',
        'logo_title'      => 'Our new site is coming soon',

        'custom_head_code'      => '',
        'custom_foot_code'      => '',

        'background_type' => 'image',
        'background_video' => '',
        'background_video_filter' => '',
        'background_size_opt'      => 'cover',
        'background_position'      => 'center center',
        'background_image_filter'  => '',
        'background_video_fallback' => '',

        'animation' => '',

        'twitter_site' => '',
        'facebook_site' => '',

        'whitelabel' => 0,
        'contact_show_name' => '1',
        'contact_email_subject' => 'New message from your Coming Soon page',
        'contact_admin_email' => get_bloginfo('admin_email'),
        'contact_message_noname' => 'Your name',
        'contact_input_text' => 'Your best email address',
        'contact_button_text' => 'Send Message',
        'contact_input_color' => '000000',
        'contact_input_bg_hover' => 'EEEEEE',
        'contact_gdpr_text' => 'I understand the site\'s privacy policy and am willingly sharing my email address',
        'contact_gdpr_error_text' => 'Please confirm the subscription terms with the checkbox below',
        'contact_gdpr_policy_text' => '',
        'contact_antispam' => 'Yes, we hate spam too!',
        'contact_submit_align' => 'left',
        'contact_antispam_size' => '12',
        'contact_antispam_color' => 'BBBBBB',
        'contact_success_bg' => '90C695',
        'contact_success_color' => 'FFFFFF',
        'contact_error_bg' => 'E08283',
        'contact_error_color' => 'FFFFFF',
        'contact_placeholder_color' => '8F8F8F',
        'contact_ignore_styles' => 1,
        'contact_input_size' => '13',
        'contact_button_size' => '12',
        'contact_button_color' => 'FFFFFF',
        'contact_input_bg' => 'FFFFFF',
        'contact_button_bg' => '0F0F0F',
        'contact_button_bg_hover' => '0A0A0A',
        'contact_input_border' => 'EEEEEE',
        'contact_button_border' => '0F0F0F',
        'contact_input_border_hover' => 'BBBBBB',
        'contact_button_border_hover' => '0A0A0A',

        'recaptcha' => 'disabled',
        'recaptcha_site_key' => '',
        'recaptcha_secret_key' => '',
    );

    return $default_options;
} // csmm_default_options


function csmm_get_options()
{
    $signals_csmm_options = get_option('signals_csmm_options', array());
    if (!isset($signals_csmm_options['content_1col_font'])) {
        if (!empty($signals_csmm_options['secondary_font'])) $signals_csmm_options['content_1col_font'] = $signals_csmm_options['secondary_font'];
        if (!empty($signals_csmm_options['secondary_font_size'])) $signals_csmm_options['content_1col_font_size'] = $signals_csmm_options['secondary_font_size'];
        if (!empty($signals_csmm_options['secondary_font_color'])) $signals_csmm_options['content_1col_font_color'] = $signals_csmm_options['secondary_font_color'];
    }
    $signals_csmm_options = array_merge(csmm_default_options(), $signals_csmm_options);

    // legacy issues for people coming from free ver
    if ($signals_csmm_options['status'] == '2') {
        $signals_csmm_options['status'] = '0';
    }
    $signals_csmm_options['arrange'] = str_replace('secondary', 'content', $signals_csmm_options['arrange']);

    $signals_csmm_options = apply_filters('csmm_get_options', $signals_csmm_options);

    return $signals_csmm_options;
} // csmm_get_options

function csmm_get_meta()
{
    $default['license_type'] = '';
    $default['license_expires'] = '';
    $default['license_active'] = false;
    $default['license_key'] = '';

    $meta = get_option('signals_csmm_meta', array());
    $meta = array_merge($default, $meta);

    return $meta;
} // csmm_get_options


/**
 * For the plugin activation & de-activation.
 * We are doing nothing over here.
 */

function csmm_plugin_activation()
{

    // Checking if the options exist in the database
    $signals_csmm_options = csmm_get_options();

    // Default options for the plugin on activation
    $default_options = csmm_default_options();

    // If the options are not there in the database, then create the default options for the plugin
    if (!$signals_csmm_options) {
        update_option('signals_csmm_options', $default_options);
    } else {
        // If present in the database, merge with the default ones
        // This is to provide compatibility with earlier versions. Although it doesn't serve the purpose completely
        $default_options = array_merge($default_options, $signals_csmm_options);
        update_option('signals_csmm_options', $default_options);
    }

    // set some meta data
    $meta = csmm_get_meta();
    if (!isset($meta['first_version']) || !isset($meta['first_install'])) {
        $meta['first_version'] = csmm_get_plugin_version();
        $meta['first_install_gmt'] = time();
        $meta['first_install'] = current_time('timestamp');

        $meta['license_type'] = '';
        $meta['license_expires'] = '';
        $meta['license_active'] = false;
        $meta['license_key'] = '';

        update_option('signals_csmm_meta', $meta);
    }
} // csmm_plugin_activation
register_activation_hook(CSMM_FILE, 'csmm_plugin_activation');


/* Hook for the plugin deactivation. */
function csmm_plugin_deactivation()
{
    csmm_clear_3rd_party_cache();
    delete_option(CSMM_POINTERS);
}
register_deactivation_hook(CSMM_FILE, 'csmm_plugin_deactivation');


function csmm_plugin_page()
{
    if (!function_exists('get_current_screen')) {
        return false;
    }

    $current_screen = get_current_screen();

    if (@$current_screen->id == 'settings_page_maintenance_mode_options') {
        return true;
    } else {
        return false;
    }
} // csmm_plugin_page


function csmm_clear_3rd_party_cache()
{
    wp_cache_flush();

    if (function_exists('rocket_clean_domain')) {
        rocket_clean_domain();
    }

    if (function_exists('w3tc_pgcache_flush')) {
        w3tc_pgcache_flush();
    }

    if (function_exists('wpfc_clear_all_cache')) {
        wpfc_clear_all_cache();
    }
    if (function_exists('w3tc_flush_all')) {
        w3tc_flush_all();
    }
    if (function_exists('wp_cache_clear_cache')) {
        wp_cache_clear_cache();
    }
    if (method_exists('LiteSpeed_Cache_API', 'purge_all')) {
        LiteSpeed_Cache_API::purge_all();
    }
    if (class_exists('Endurance_Page_Cache')) {
        $epc = new Endurance_Page_Cache;
        $epc->purge_all();
    }
    if (class_exists('SG_CachePress_Supercacher') && method_exists('SG_CachePress_Supercacher', 'purge_cache')) {
        SG_CachePress_Supercacher::purge_cache(true);
    }
    if (class_exists('SiteGround_Optimizer\Supercacher\Supercacher')) {
        SiteGround_Optimizer\Supercacher\Supercacher::purge_cache();
    }
    if (isset($GLOBALS['wp_fastest_cache']) && method_exists($GLOBALS['wp_fastest_cache'], 'deleteCache')) {
        $GLOBALS['wp_fastest_cache']->deleteCache(true);
    }
    if (is_callable(array('Swift_Performance_Cache', 'clear_all_cache'))) {
        Swift_Performance_Cache::clear_all_cache();
    }
    if (is_callable(array('Hummingbird\WP_Hummingbird', 'flush_cache'))) {
        Hummingbird\WP_Hummingbird::flush_cache(true, false);
    }

    update_option('_mm_cache', 'Clear cache');
}

add_action('init', array('CSMM', 'init'));

add_action('wp_ajax_csmm_check_login', 'csmm_check_password_unlock_ajax');
add_action('wp_ajax_nopriv_csmm_check_login', 'csmm_check_password_unlock_ajax');

function csmm_access_rules_enabled()
{
    $signals_csmm_options = csmm_get_options();

    if (!empty($signals_csmm_options['direct_access_link'])) {
        return true;
    }

    if (!empty($signals_csmm_options['direct_access_password'])) {
        return true;
    }

    if (!empty($signals_csmm_options['site_password'])) {
        return true;
    }

    return false;
}

function csmm_check_password_unlock_ajax()
{
    $options = csmm_get_options();
    $password = $_POST['pass'];
    @session_start();

    if ($password == $options['direct_access_password']) {
        $_SESSION['csmm_direct_access_password'] = true;
        wp_send_json_success();
    } else if ($password == $options['site_password']) {
        $_SESSION['csmm_access_password'] = true;
        wp_send_json_success();
    } else {
        wp_send_json_error($options['login_wrong_password_text']);
    }
}


function csmm_get_plugin_version()
{
    $plugin_data = get_file_data(CSMM_FILE, array('version' => 'Version'), 'plugin');

    return $plugin_data['version'];
} // csmm_get_plugin_version


function csmm_zapier_send($fields = array())
{
    $options = csmm_get_options();

    if (empty($options['signal_zapier_action_url'])) {
        return false;
    }

    $res = wp_remote_post($options['signal_zapier_action_url'], array('sslverify' => false, 'body' => $fields));

    if (!is_wp_error($res)) {
        return true;
    } else {
        return new WP_Error('external_api', 'Unable to send Zap. ' . $res->get_error_message());
    }
}

function csmm_autoresponder_send($fields, $debug = false)
{
    $options = csmm_get_options();

    $query_data = array();
    if (isset($options['signal_ua_email_field_name'])) {
        $query_data[$options['signal_ua_email_field_name']] = $fields['email'];
    }

    if (isset($options['signal_ua_name_field_name'])) {
        $query_data[$options['signal_ua_name_field_name']] = $fields['name'];
    }

    if (isset($options['signal_ua_additional_data'])) {
        $extra_fields = explode('&', $options['signal_ua_additional_data']);
        foreach ($extra_fields as $extra_field) {
            $extra_field_pair = explode('=', $extra_field, 2);
            $query_data[$extra_field_pair[0]] = $extra_field_pair[1];
        }
    }

    if ($debug) {
        return array('url' => $options['signal_ua_action_url'], 'data' => $query_data);
    }

    if ($options['signal_ua_method'] == 'get') {
        $res = wp_remote_get(esc_url_raw($options['signal_ua_action_url']), $query_data);
    } else {
        $res = wp_remote_post($options['signal_ua_action_url'], array('sslverify' => false, 'body' => $query_data));
    }

    if (!is_wp_error($res)) {
        return true;
    } else {
        return new WP_Error('external_api', 'Unable to send autoresponder. ' . $res->get_error_message());
    }
}

function csmm_convert_ga($code)
{
    if (empty($code) || strpos($code, '<script') === false) {
        return $code;
    }

    preg_match_all('/(UA-[0-9]{3,10}-[0-9]{1,3})/i', $code, $matches, PREG_SET_ORDER, 0);
    if (!empty($matches[0][0])) {
        return $matches[0][0];
    } else {
        return '';
    }
} // csmm_convert_ga


// helper function to generate tagged buy links
function csmm_generate_web_link($placement = '', $page = '/', $params = array(), $anchor = '')
{
    $base_url = 'https://comingsoonwp.com';

    if ('/' != $page) {
        $page = '/' . trim($page, '/') . '/';
    }
    if ($page == '//') {
        $page = '/';
    }

    $parts = array_merge(array('utm_source' => 'csmm-pro', 'utm_medium' => 'plugin', 'utm_content' => $placement, 'utm_campaign' => 'csmm-pro-v' . csmm_get_plugin_version()), $params);

    if (!empty($anchor)) {
        $anchor = '#' . trim($anchor, '#');
    }

    $out = $base_url . $page . '?' . http_build_query($parts, '', '&amp;') . $anchor;

    return $out;
} // generate_web_link


add_filter('wf_licensing_csmm_query_server_meta', function ($meta, $action) {
    $options = csmm_get_options();
    return array('stats' => csmm_stats::prepare_stats(), 'status' => $options['status']);
}, 10, 2);

add_filter('wf_licensing_csmm_remote_actions', function ($actions) {
    $actions[] = 'change_csmm_status';

    return $actions;
}, 10, 1);

add_action('wf_licensing_csmm_remote_action_change_csmm_status', function ($new_status) {
    global $csmm_lc;
    $options = csmm_get_options();

    $options['status'] = (int) (bool) $new_status;
    update_option('signals_csmm_options', $options);

    $data = $csmm_lc->prepare_server_query_data('remote_change_csmm_status');

    wp_send_json_success($data);
}, 10, 1);

function csmm_is_mailpoet_active()
{
    if (!function_exists('is_plugin_active') || !function_exists('get_plugin_data')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    if (is_plugin_active('mailpoet/mailpoet.php')) {
        $mailpoet_info = get_plugin_data(ABSPATH . 'wp-content/plugins/mailpoet/mailpoet.php');
        if (version_compare($mailpoet_info['Version'], '3.47.9', '<')) {
            return false;
        } else {
            return true;
        }
    } else {
        return false;
    }
} // csmm_is_mailpoet_active
