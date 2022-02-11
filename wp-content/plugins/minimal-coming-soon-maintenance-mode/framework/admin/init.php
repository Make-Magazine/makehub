<?php

/**
 * WordPress management panel.
 *
 * @link       http://www.webfactoryltd.com
 * @since      0.1
 * @package    Signals_Maintenance_Mode
 */

// Menu for the support and about panel
function csmm_add_menu()
{

    if (current_user_can('manage_options')) {
        // Adding to the plugin panel link to the settings menu
        $page_title = csmm_get_rebranding('name');
        if ($page_title === false || empty($plugin_name)) {
            $page_title = 'Coming Soon & Maintenance Mode PRO';
        }

        $menu_title = csmm_get_rebranding('short_name');
        if ($menu_title === false || empty($menu_title)) {
            $menu_title = 'Coming Soon <span style="color: #fe2929;">PRO</span>';
        }

        $signals_csmm_menu = add_options_page(
            __($page_title, 'signals'),
            $menu_title,
            'manage_options',
            'maintenance_mode_options',
            'csmm_admin_settings'
        );

        // Loading the JS conditionally
        add_action('load-' . $signals_csmm_menu, 'csmm_load_scripts');
    }
}
add_action('admin_menu', 'csmm_add_menu');

/**
 * Test if we're on CSMM's admin page
 *
 * @return bool
 */
function csmm_is_plugin_page()
{
    $current_screen = get_current_screen();

    if ($current_screen->id == 'settings_page_maintenance_mode_options') {
        return true;
    } else {
        return false;
    }
} // is_plugin_page


// Registering JS and CSS files over here
function csmm_admin_scripts()
{
    global $csmm_lc;
    $license = $csmm_lc->get_license();
    $current_user = wp_get_current_user();
    $options = csmm_get_options();

    wp_enqueue_script('jquery.fonticonpicker.min.js', CSMM_URL . '/framework/admin/js/jquery.fonticonpicker.min.js', array('jquery'), csmm_get_plugin_version(), true);
    wp_enqueue_script('raphael-2.1.4.min.js', CSMM_URL . '/framework/admin/js/raphael-2.1.4.min.js', array('jquery'), csmm_get_plugin_version(), true);
    wp_enqueue_script('justgage.js', CSMM_URL . '/framework/admin/js/justgage.js', array('jquery'), csmm_get_plugin_version(), true);

    wp_enqueue_style('jquery.fonticonpicker.min.css', CSMM_URL . '/framework/admin/css/fonticonpicker/jquery.fonticonpicker.min.css', array(), csmm_get_plugin_version());
    wp_enqueue_style('jquery.fonticonpicker.darkgrey.min.css', CSMM_URL . '/framework/admin/css/fonticonpicker/themes/dark-grey-theme/jquery.fonticonpicker.darkgrey.min.css', array(), csmm_get_plugin_version());
    wp_enqueue_style('icomoon.css', CSMM_URL . '/framework/admin/css/fonticonpicker/icomoon/style.css', array(), csmm_get_plugin_version());
    wp_enqueue_style('csmm-spectrum', CSMM_URL . '/framework/admin/css/spectrum.css', array(), csmm_get_plugin_version());


    wp_register_style('csmm-admin-base', CSMM_URL . '/framework/admin/css/admin.css', false, csmm_get_plugin_version());
    wp_register_style('csmm-admin-filters', CSMM_URL . '/framework/admin/css/image-filters.css', false, csmm_get_plugin_version());
    wp_register_style('csmm-sweetalert2', CSMM_URL . '/framework/admin/css/sweetalert2.min.css', array(), csmm_get_plugin_version());
    wp_register_script('csmm-webfonts', '//ajax.googleapis.com/ajax/libs/webfont/1.4.7/webfont.js', false);
    wp_register_script('csmm-admin-editor', CSMM_URL . '/framework/admin/js/editor/ace.js', false, csmm_get_plugin_version(), true);
    wp_register_script('csmm-admin-plugins', CSMM_URL . '/framework/admin/js/plugins.js', 'jquery', csmm_get_plugin_version(), true);
    wp_register_script('csmm-admin-base', CSMM_URL . '/framework/admin/js/admin.js', 'jquery', csmm_get_plugin_version(), true);

    wp_enqueue_script('csmm-chart', CSMM_URL . '/framework/admin/js/chart.min.js', array(), csmm_get_plugin_version(), true);
    wp_enqueue_script('csmm-moment', CSMM_URL . '/framework/admin/js/moment.min.js', array(), csmm_get_plugin_version(), true);
    wp_enqueue_script('csmm-spectrum', CSMM_URL . '/framework/admin/js/spectrum.js', array(), csmm_get_plugin_version(), true);

    // Calling the files
    wp_enqueue_style('csmm-admin-base');
    wp_enqueue_style('csmm-admin-filters');
    wp_enqueue_style('csmm-sweetalert2');
    wp_enqueue_style('wp-jquery-ui-dialog');
    wp_enqueue_script('jquery-ui-dialog');
    wp_enqueue_script('jquery-ui-tabs');

    wp_enqueue_script('csmm-webfonts');
    wp_enqueue_script('csmm-admin-editor');
    wp_enqueue_script('csmm-admin-color');
    wp_enqueue_script('csmm-admin-plugins');
    wp_enqueue_script('csmm-admin-base');
    wp_enqueue_script('jquery-ui-slider');
    wp_enqueue_script('jquery-ui-sortable');

    wp_enqueue_media();

    // fix for aggressive plugins
    wp_dequeue_style('uiStyleSheet');
    wp_dequeue_style('wpcufpnAdmin');
    wp_dequeue_style('unifStyleSheet');
    wp_dequeue_style('wpcufpn_codemirror');
    wp_dequeue_style('wpcufpn_codemirrorTheme');
    wp_dequeue_style('collapse-admin-css');
    wp_dequeue_style('jquery-ui-css');
    wp_dequeue_style('tribe-common-admin');
    wp_dequeue_style('file-manager__jquery-ui-css');
    wp_dequeue_style('file-manager__jquery-ui-css-theme');
    wp_dequeue_style('wpmegmaps-jqueryui');
    wp_dequeue_style('wp-botwatch-css');
    wp_dequeue_style('jquery-ui-css');

    $support_text = 'My site details: WP ' . get_bloginfo('version') . ', MM v' . csmm_get_plugin_version() . ', ';
    if (!empty($license['license_key'])) {
        $support_text .= 'license key: ' . $license['license_key'] . '.';
    } else {
        $support_text .= 'no license info.';
    }

    if (strtolower($current_user->display_name) != 'admin' && strtolower($current_user->display_name) != 'administrator') {
        $support_name = $current_user->display_name;
    } else {
        $support_name = '';
    }

    $vars = array();
    $vars['nonce_save_settings'] = wp_create_nonce('csmm_save_settings');
    $vars['is_activated'] = $csmm_lc->is_active();
    $vars['lc_version'] = csmm_get_plugin_version();
    $vars['lc_site'] = get_home_url();
    $vars['nonce_activate_license_key'] = wp_create_nonce('csmm_activate_license_key');
    $vars['nonce_save_license_key'] = wp_create_nonce('csmm_save_license_key');
    $vars['csmm_is_plugin_page'] = csmm_is_plugin_page();
    $vars['support_name'] = $support_name;
    $vars['support_text'] = $support_text;

    if (csmm_get_rebranding() !== false) {
        $vars['loader_image'] = CSMM_URL . '/framework/admin/img/loader.gif';
    } else {
        $vars['loader_image'] = CSMM_URL . '/framework/admin/img/anim_logo.gif';
    }

    if ($options['track_stats'] === 1) {
        $vars['stats'] = csmm_stats::get_stats();
        $vars['stats_devices'] = csmm_stats::get_device_stats();
    }

    $vars['rebranding'] = csmm_get_rebranding();
    $vars['whitelabel'] = csmm_whitelabel_filter();
    $vars['settings_url'] = admin_url('options-general.php?page=maintenance_mode_options');

    if (csmm_get_rebranding() !== false) {
        $brand_color = str_replace('#', '', csmm_get_rebranding('color'));
        if (empty($brand_color)) {
            $brand_color = 'fe2929';
        }
        $vars['chart_colors'] = array($brand_color, csmm_color_luminance($brand_color, 90));
    } else {
        $vars['chart_colors'] = array('fe2929', 'ffa0a0');
    }
    wp_localize_script('csmm-admin-base', 'csmm', $vars);
} // admin_scripts

function csmm_color_luminance($hex, $percent)
{

    // validate hex string

    $hex = preg_replace('/[^0-9a-f]/i', '', $hex);
    $new_hex = '#';

    if (strlen($hex) < 6) {
        $hex = $hex[0] + $hex[0] + $hex[1] + $hex[1] + $hex[2] + $hex[2];
    }

    // convert to decimal and change luminosity
    for ($i = 0; $i < 3; $i++) {
        $dec = hexdec(substr($hex, $i * 2, 2));
        $dec = min(max(0, $dec + $dec * $percent), 255);
        $new_hex .= str_pad(dechex($dec), 2, 0, STR_PAD_LEFT);
    }

    return $new_hex;
}

function csmm_hex2rgba($color)
{
    $default = 'rgba(255, 255, 255, 0)';

    if (empty($color)) {
        return $default;
    }

    if ($color[0] == '#') {
        $color = substr($color, 1);
    }

    $opacity = false;

    if (strlen($color) == 8) {
        $hex = array($color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5]);
        $opacity = hexdec($color[6] . $color[7]) / 255;
    } elseif (strlen($color) == 6) {
        $hex = array($color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5]);
    } elseif (strlen($color) == 3) {
        $hex = array($color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]);
    } else {
        return $default;
    }

    $rgb = array_map('hexdec', $hex);

    if (strlen($color) == 8 && $opacity === 0) {
        $output = 'rgba( ' . implode(",", $rgb) . ',' . $opacity . ' )';
    } else if ($opacity) {
        if (abs($opacity) > 1) {
            $opacity = 1.0;
        }
        $output = 'rgba( ' . implode(",", $rgb) . ',' . $opacity . ' )';
    } else {
        $output = 'rgb( ' . implode(",", $rgb) . ' )';
    }

    return $output;
}

// Scripts & styles for the plugin
function csmm_load_scripts()
{
    add_action('admin_enqueue_scripts', 'csmm_admin_scripts');
}


// add settings link to plugins page
function csmm_plugin_action_links($links)
{
    $plugin_name = csmm_get_rebranding('name');
    if ($plugin_name === false) {
        $plugin_name = 'Minimal Coming Soon &amp; Maintenance Mode Settings';
    }
    $settings_link = '<a href="' . admin_url('options-general.php?page=maintenance_mode_options') . '" title="' . $plugin_name . '">Settings</a>';

    array_unshift($links, $settings_link);

    return $links;
} // csmm_plugin_action_links


// add links to plugin's description in plugins table
function csmm_plugin_meta_links($links, $file)
{
    if ($file == CSMM_BASENAME && !csmm_whitelabel_filter()) {
        unset($links[1]);
        unset($links[2]);
        return $links;
    }

    if ($file == CSMM_BASENAME) {
        if (csmm_get_rebranding() !== false) {
            unset($links[1]);
            unset($links[2]);

            $links[] = '<a target="_blank" href="' . csmm_get_rebranding('company_url') . '" title="Get help">' . csmm_get_rebranding('company_name') . '</a>';
            $links[] = '<a target="_blank" href="' . csmm_get_rebranding('url') . '" title="Get help">Support</a>';
        } else {
            $links[] = '<a target="_blank" href="https://comingsoonwp.com/contact/" title="Get help">Support</a>';
        }
    }

    return $links;
} // csmm_plugin_meta_links


// permanently dismiss a pointer
function csmm_dismiss_pointer_ajax()
{
    check_ajax_referer('csmm_dismiss_pointer');

    $disabled_pointers = get_option(CSMM_POINTERS);
    $pointer = trim($_POST['pointer']);

    $disabled_pointers[$pointer] = true;
    update_option(CSMM_POINTERS, $disabled_pointers);

    wp_send_json_success();
} // dismiss_pointer_ajax


// reset all pointers to default state - visible
function csmm_get_pointers()
{
    $pointers = array();
    $plugin_name = csmm_get_rebranding('name');
    if ($plugin_name === false) {
        $plugin_name = 'Minimal Coming Soon &amp; Maintenance Mode Settings';
    }

    $pointers['welcome'] = array('target' => '#menu-settings', 'edge' => 'left', 'align' => 'right', 'content' => 'Thank you for installing the <b style="font-weight: 800;">' . $plugin_name . '</b> plugin! Please open <a href="' . admin_url('options-general.php?page=maintenance_mode_options') . '">Settings - Coming Soon PRO</a> to get started.');
    $pointers['getting_started'] = array('target' => '#header-status', 'edge' => 'top', 'align' => 'left', 'content' => 'Make sure you <b>enable Coming Soon Mode</b> so it\'s visible to your visitors. If you just want to preview it, use the preview button on the bottom of the page.');

    return $pointers;
} // csmm_get_pointers


function csmm_enqueue_pointers($hook)
{
    $pointers = array();
    $all_pointers = csmm_get_pointers();
    $disabled_pointers = get_option(CSMM_POINTERS);


    // auto remove welcome pointer when options are opened
    // disabled
    if (false && empty($disabled_pointers['welcome']) && 'settings_page_maintenance_mode_options' == $hook) {
        $disabled_pointers['welcome'] = true;
        update_option(CSMM_POINTERS, $disabled_pointers);
    }

    // temp remove
    if ('settings_page_maintenance_mode_options' == $hook) {
        $disabled_pointers['welcome'] = true;
    }

    foreach ($all_pointers as $tmp_key => $tmp_val) {
        if (empty($disabled_pointers[$tmp_key])) {
            $pointers[$tmp_key] = $tmp_val;
        }
    } // foreach

    if (empty($pointers)) {
        return;
    }

    $pointers['_nonce_dismiss_pointer'] = wp_create_nonce('csmm_dismiss_pointer');
    wp_enqueue_script('wp-pointer');
    wp_enqueue_script('csmm-pointers', CSMM_URL . '/framework/admin/js/pointers.js', array('jquery'), csmm_get_plugin_version(), true);
    wp_enqueue_style('wp-pointer');
    wp_localize_script('wp-pointer', 'csmm_pointers', $pointers);
} // csmm_enqueue_pointers

function csmm_rebranding_js($hook)
{
    if ($hook != 'plugins.php') {
        return false;
    }

    $rebranding = csmm_get_rebranding();
    if (false === $rebranding) {
        return false;
    }

    wp_enqueue_script('csmm-branding', CSMM_URL . '/framework/admin/js/branding.js', array('jquery'), csmm_get_plugin_version(), true);
    wp_localize_script('csmm-branding', 'csmm_rebranding', $rebranding);
}

function csmm_plugin_admin_init()
{
    if (!is_admin()) {
        return;
    }


    add_action('admin_action_csmm_export_settings', 'csmm_export_settings');
    add_action('admin_action_csmm_reset_settings', 'csmm_reset_settings');
    add_action('admin_action_csmm_reset_stats', 'csmm_reset_stats');
    add_action('admin_action_csmm_activate_theme', 'csmm_activate_theme');
    add_action('admin_action_csmm_delete_theme', 'csmm_delete_theme');

    if (isset($_GET['csmm_wl'])) {
        $settings = csmm_get_options();
        if ($_GET['csmm_wl'] == 'true') {
            $settings['whitelabel'] = 1;
        } else {
            $settings['whitelabel'] = 0;
        }
        update_option('signals_csmm_options', $settings);
    }

    $meta = csmm_get_meta();
    if (!isset($meta['first_version']) || !isset($meta['first_install'])) {
        $meta['first_version'] = csmm_get_plugin_version();
        $meta['first_install_gmt'] = time();
        $meta['first_install'] = current_time('timestamp');
        update_option('signals_csmm_meta', $meta);
    }

    add_filter('plugin_action_links_' . CSMM_BASENAME, 'csmm_plugin_action_links');
    add_filter('plugin_row_meta', 'csmm_plugin_meta_links', 10, 2);

    add_action('admin_enqueue_scripts', 'csmm_enqueue_pointers', 100, 1);
    add_action('admin_enqueue_scripts', 'csmm_rebranding_js', 100, 1);

    //add_filter('mce_buttons', 'csmm_modify_mce', 5);
} // csmm_plugin_admin_init

add_action('init', 'csmm_plugin_admin_init');

function csmm_whitelabel_filter()
{
    global $csmm_lc;
    $settings = csmm_get_options();

    if (!$csmm_lc->is_active('white_label')) {
        return true;
    }

    if ($settings['whitelabel'] != 1) {
        return true;
    }

    return false;
}

// Including file for the management panel
require_once CSMM_PATH . 'framework/admin/settings.php';

function mcsm_media_sideload_image($url = null, $post_id = null, $thumb = null, $filename = null, $return = 'id')
{
    if (!$url) return new WP_Error('missing', "Need a valid URL and post ID...");
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    // Download file to temp location, returns full server path to temp file, ex; /home/user/public_html/mysite/wp-content/26192277_640.tmp
    add_filter('https_local_ssl_verify', '__return_false');
    add_filter('https_ssl_verify', '__return_false');

    $tmp = download_url($url);
    // If error storing temporarily, unlink
    if (is_wp_error($tmp)) {
        @unlink($file_array['tmp_name']);   // clean up
        $file_array['tmp_name'] = '';
        return $tmp; // output wp_error
    }
    preg_match('/[^\?]+\.(jpg|JPG|jpe|JPE|jpeg|JPEG|gif|GIF|png|PNG)/', $url, $matches);    // fix file filename for query strings
    $url_filename = basename($matches[0]);                                                  // extract filename from url for title
    $url_type = wp_check_filetype($url_filename);                                           // determine file type (ext and mime/type)
    // override filename if given, reconstruct server path
    if (!empty($filename)) {
        $filename = sanitize_file_name($filename);
        $tmppath = pathinfo($tmp);                                                        // extract path parts
        $new = $tmppath['dirname'] . "/" . $filename . "." . $tmppath['extension'];          // build new path
        rename($tmp, $new);                                                                 // renames temp file on server
        $tmp = $new;                                                                        // push new filename (in path) to be used in file array later
    }
    // assemble file data (should be built like $_FILES since wp_handle_sideload() will be using)
    $file_array['tmp_name'] = $tmp;                                                         // full server path to temp file
    if (!empty($filename)) {
        $file_array['name'] = $filename . "." . $url_type['ext'];                           // user given filename for title, add original URL extension
    } else {
        $file_array['name'] = $url_filename;                                                // just use original URL filename
    }
    // set additional wp_posts columns
    if (empty($post_data['post_title'])) {
        $post_data['post_title'] = basename($url_filename, "." . $url_type['ext']);         // just use the original filename (no extension)
    }
    // make sure gets tied to parent
    if (empty($post_data['post_parent'])) {
        $post_data['post_parent'] = $post_id;
    }
    // required libraries for media_handle_sideload
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    // do the validation and storage stuff
    $att_id = media_handle_sideload($file_array, $post_id, null, $post_data);             // $post_data can override the items saved to wp_posts table, like post_mime_type, guid, post_parent, post_title, post_content, post_status
    // If error storing permanently, unlink
    if (is_wp_error($att_id)) {
        @unlink($file_array['tmp_name']);   // clean up
        return $att_id; // output wp_error
    }
    // set as post thumbnail if desired
    if ($thumb) {
        set_post_thumbnail($post_id, $att_id);
    }
    if ($return == 'src') {
        return wp_get_attachment_url($att_id);
    }
    return $att_id;
}

add_action('wp_ajax_mcsm_editor_unsplash_download', 'mcsm_editor_unsplash_download');
function mcsm_editor_unsplash_download()
{
    global $csmm_lc;

    $params['request'] = 'photos';
    $params['action'] = 'get_images';
    $params['image_id'] = $_POST['image_id'];

    $response = $csmm_lc->query_licensing_server('unsplash_api', array('request_details' => serialize($params)));

    if (is_wp_error($response)) {
        wp_send_json_error('Images API is temporarily not available. ' . $url);
    } else {
        $unsplash_image_link = json_decode($response['data']);
        $image_url = $unsplash_image_link->url;
        $image_name = $_POST['image_name'];
        $image_query = '&w=4000&h=4000&q=75';
        $image_src = mcsm_media_sideload_image($image_url . '&format=.jpg' . $image_query, 0, false, $image_name, 'src');
        if (!is_wp_error($image_src)) {
            wp_send_json_success($image_src);
        } else {
            wp_send_json_error($image_src->get_error_message());
        }
    }
    die();
} // ucp_editor_unsplash_download

add_action('wp_ajax_mcsm_editor_unsplash_api', 'mcsm_editor_unsplash_api');
function mcsm_editor_unsplash_api()
{
    global $csmm_lc;

    $params['request'] = 'photos';
    $params['page'] = (int) $_POST['page'];
    $params['per_page'] = (int) $_POST['per_page'];
    $params['search'] = substr(trim($_POST['search']), 0, 128);
    $params['action'] = 'get_images';
    $response = $csmm_lc->query_licensing_server('unsplash_api', array('request_details' => serialize($params)));

    if (is_wp_error($response)) {
        wp_send_json_error('Images API is temporarily not available. ' . $url);
    } else {
        $photos_unsplash_response = json_decode($response['data']);

        $photos_response = array();
        $total_pages = false;
        $total_results = false;

        if (isset($photos_unsplash_response->total)) {
            $total_results = $photos_unsplash_response->total;
            $total_pages = $photos_unsplash_response->total_pages;
            $photos_unsplash = $photos_unsplash_response->results;
        } else {
            $photos_unsplash = $photos_unsplash_response;
        }
        foreach ($photos_unsplash as $photo_data) {
            $image_name = $photo_data->id;
            if (strlen($photo_data->description) > 0) {
                $image_name = sanitize_title(substr($photo_data->description, 0, 50));
            }
            $photo_response[] = array('id' => $photo_data->id, 'name' => $image_name, 'thumb' => $photo_data->urls->thumb, 'full' => $photo_data->urls->full, 'user' => '<a class="unsplash-user" href="https://unsplash.com/@' . $photo_data->user->username . '/?utm_source=Coming+Soon+demo&utm_medium=referral" target="_blank">' . $photo_data->user->name . '</a>');
        }
        if (count($photo_response) == 0) {
            wp_send_json_error('Images API is temporarily not available.');
        } else {
            wp_send_json_success(array('results' => json_encode($photo_response), 'total_pages' => $total_pages, 'total_results' => $total_results));
        }
    }
    die();
} // ucp_editor_unsplash_api


add_action('wp_ajax_mcsm_editor_depositphotos_api', 'mcsm_editor_depositphotos_api');
function mcsm_editor_depositphotos_api()
{
    global $csmm_lc;

    $params['request'] = 'photos';
    $params['page'] = (int) $_POST['page'];
    $params['per_page'] = (int) $_POST['per_page'];
    $params['search'] = substr(trim($_POST['search']), 0, 128);
    $params['action'] = 'get_images';

    $response = $csmm_lc->query_licensing_server('depositphotos_api', array('request_details' => serialize($params)));

    if (is_wp_error($response)) {
        wp_send_json_error('Images API is temporarily not available. ' . $url);
    } else {
        $photos_depositphotos_response = json_decode($response['data']);

        $photo_response = array();
        $total_pages = false;
        $total_results = false;

        if (isset($photos_depositphotos_response->count)) {
            $total_results = $photos_depositphotos_response->count;
            $total_pages = $photos_depositphotos_response->count / 100;
            $photos_depositphotos = $photos_depositphotos_response->result;
        } else {
            $photos_depositphotos = $photos_depositphotos_response;
        }
        $count = 0;
        foreach ($photos_depositphotos as $photo_data) {
            $image_name = $photo_data->id;
            if (strlen($photo_data->description) > 0) {
                $image_name = sanitize_title(substr($photo_data->description, 0, 50));
            }
            $count++;
            $photo_response[] = array('id' => $photo_data->id, 'name' => $image_name, 'thumb' => $photo_data->large_thumb, 'full' => $photo_data->url_max_qa, 'itemurl' => $photo_data->itemurl . '?ref=30484348');
        }
        if (count($photo_response) == 0) {
            wp_send_json_error('Images API is temporarily not available.');
        } else {
            wp_send_json_success(array('results' => json_encode($photo_response), 'total_pages' => $total_pages, 'total_results' => $total_results));
        }
    }
    die();
} // ucp_editor_depositphotos_api


function csmm_create_select_options($options, $selected = null, $output = true)
{
    $out = "\n";

    if (!is_array($selected)) {
        $selected = array($selected);
    }

    foreach ($options as $tmp) {
        $data = '';
        if (isset($tmp['disabled'])) {
            $data .= ' disabled="disabled" ';
        }
        if (in_array($tmp['val'], $selected)) {
            $out .= "<option selected=\"selected\" value=\"{$tmp['val']}\"{$data}>{$tmp['label']}&nbsp;</option>\n";
        } else {
            $out .= "<option value=\"{$tmp['val']}\"{$data}>{$tmp['label']}&nbsp;</option>\n";
        }
    } // foreach

    if ($output) {
        echo $out;
    } else {
        return $out;
    }
} // csmm_create_select_options


function csmm_export_settings()
{
    $filename = str_replace(array('http://', 'https://'), '', home_url());
    $filename = str_replace(array('/', '\\', '.'), '-', $filename);
    $filename .= '-' . date('Y-m-d') . '-coming-soon-pro.txt';

    $options = csmm_get_options();
    unset($options['none']);
    $options = apply_filters('csmm_options_pre_export', $options);

    $out = array('type' => 'CSMM PRO', 'version' => csmm_get_plugin_version(), 'data' => $options);
    $out = json_encode($out);

    header('Content-Type: text/plain');
    header('Content-Disposition: attachment; filename=' . $filename);
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . strlen($out));

    @ob_end_clean();
    flush();

    echo $out;
    exit;
} // export_settings


function csmm_reset_settings($redirect = true)
{
    update_option('signals_csmm_options', csmm_default_options());

    if (false === $redirect) {
        return true;
    }

    set_transient('csmm_error_msg', '<div class="csmm-alert csmm-alert-info"><strong>Settings have been reset.</strong><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>', 1);

    if (!empty($_GET['redirect'])) {
        wp_safe_redirect($_GET['redirect']);
    } else {
        wp_safe_redirect(admin_url());
    }

    exit;
} // reset_setings


function csmm_reset_stats($redirect = true)
{
    update_option(CSMM_STATS, array(
        'general' => array(
            'countries' => array('unknown' => 0),
            'browsers' => array('unknown' => 0),
            'devices' => array('unknown' => 0),
            'traffic' => array('unknown' => 0),
        ),
        'visits' => array(),
    ));

    if (false === $redirect) {
        return true;
    }

    set_transient('csmm_error_msg', '<div class="csmm-alert csmm-alert-info"><strong>Stats have been reset.</strong><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>', 1);

    if (!empty($_GET['redirect'])) {
        wp_safe_redirect($_GET['redirect']);
    } else {
        wp_safe_redirect(admin_url());
    }

    exit;
} // reset_stats


function csmm_activate_theme()
{
    $theme = basename(trim(@$_GET['theme']));
    $uploads = wp_upload_dir();
    $csmm_themes_folder = $uploads['basedir'] . '/coming-soon-themes/';
    clearstatcache();
    if (!file_exists(CSMM_PATH . 'framework/admin/themes/' . $theme . '.txt') && !file_exists($csmm_themes_folder . $theme)) {
        set_transient('csmm_error_msg', '<div class="csmm-alert csmm-alert-info"><strong>Error loading theme! Theme file not found. Please contact support.</strong><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>', 1);
    } else {
        //User theme
        if (file_exists($csmm_themes_folder . $theme)) {
            $theme_file = glob($csmm_themes_folder . trailingslashit($theme) . '*.txt');
            $content = file_get_contents($theme_file[0]);
            $content = json_decode($content, true);

            if (!empty($content['data']['bg_cover'])) {
                $content['data']['bg_cover'] = mcsm_media_sideload_image($content['data']['bg_cover'], 0, false, '', 'src');
            }
        } else {
            $content = file_get_contents(CSMM_PATH . 'framework/admin/themes/' . $theme . '.txt');
            $content = json_decode($content, true);

            if (!isset($content['data']['content_1col_font'])) {
                $content['data']['content_1col_font'] = $content['data']['secondary_font'];
                $content['data']['content_1col_font_size'] = $content['data']['secondary_font_size'];
                $content['data']['content_1col_font_color'] = $content['data']['secondary_font_color'];
            }
        }

        if (!isset($content['meta']['type']) || !isset($content['meta']['version']) || ($content['meta']['type'] != 'CSMM PRO' && $content['meta']['type'] != 'CSMM USER') || sizeof($content['data']) < 20) {
            set_transient('csmm_error_msg', '<div class="csmm-alert csmm-alert-info"><strong>Error loading theme! Theme file is broken. Please contact support.</strong><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>', 1);
        } else {
            $settings = csmm_get_options();
            unset($content['data']['contact_admin_email']);
            $settings = array_merge($settings, $content['data']);
            if (!array_key_exists('mode', $settings)) {
                $settings['mode'] = 'layout';
            }
            update_option('signals_csmm_options', $settings);

            set_transient('csmm_error_msg', '<div class="csmm-alert csmm-alert-info"><strong>' . $content['meta']['name'] . ' theme has been activated!</strong><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>', 1);
        }
    }

    if (!empty($_GET['redirect'])) {
        wp_safe_redirect($_GET['redirect']);
    } else {
        wp_safe_redirect(admin_url());
    }

    exit;
} // activate_theme

function csmm_delete_folder($folder)
{
    $files = array_diff(scandir($folder), array('.', '..'));

    foreach ($files as $file) {
        if (is_dir($folder . DIRECTORY_SEPARATOR . $file)) {
            csmm_delete_folder($folder . DIRECTORY_SEPARATOR . $file);
        } else {
            $tmp = @unlink($folder . DIRECTORY_SEPARATOR . $file);
        }
    } // foreach

    return @rmdir($folder);
} // delete_folder

function csmm_delete_theme()
{
    $theme = basename(trim(@$_GET['theme']));
    $uploads = wp_upload_dir();
    $csmm_themes_folder = $uploads['basedir'] . '/coming-soon-themes/';
    clearstatcache();
    if (!file_exists($csmm_themes_folder . $theme)) {
        set_transient('csmm_error_msg', '<div class="csmm-alert csmm-alert-info"><strong>Error deleting theme! Theme file not found. Please contact support.</strong><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>', 1);
    } else {
        csmm_delete_folder($csmm_themes_folder . $theme);
    }

    if (!empty($_GET['redirect'])) {
        wp_safe_redirect($_GET['redirect']);
    } else {
        wp_safe_redirect(admin_url());
    }
}

// validate import file after upload
function csmm_validate_import_file()
{
    if (empty($_POST) || empty($_FILES['csmm_settings_import'])) {
        return new WP_Error(1, 'No import file uploaded.');
    }

    $plugin_name = csmm_get_rebranding('short_name');
    if ($plugin_name === false) {
        $plugin_name = 'Coming Soon PRO';
    }
    $uploaded_file = $_FILES['csmm_settings_import'];

    if (mime_content_type($uploaded_file['tmp_name']) == 'text/plain' && substr($uploaded_file['name'], -4, 4) != '.txt') {
        return new WP_Error(1, 'Please 2 upload a <i>TXT</i> file generated by ' . $plugin_name . ' plugin.');
    }

    if ($uploaded_file['size'] < 500) {
        return new WP_Error(1, 'Uploaded file is too small. Please verify that you have uploaded the right export file.');
    }

    if ($uploaded_file['size'] > 100000) {
        return new WP_Error(1, 'Uploaded file is too large to process. Please verify that you have uploaded the right export file.');
    }

    $content = file_get_contents($uploaded_file['tmp_name']);
    $content = json_decode($content, true);
    if (
        !isset($content['type']) || !isset($content['version']) || !isset($content['data']) ||
        $content['type'] != 'CSMM PRO' || !is_array($content['data']) || sizeof($content['data']) < 20
    ) {
        return new WP_Error(1, 'Uploaded file is not a ' . $plugin_name . ' export file. Please verify that you have uploaded the right file.');
    }

    return $content;
} // validate_import_file

function csmm_modify_mce($buttons)
{
    unset($buttons[0]);
    $buttons = array_values($buttons);
    $buttons = array_slice($buttons, 0, sizeof($buttons) - 4);

    $position = array_search('alignright', $buttons);

    if (!is_int($position)) {
        return array_merge($buttons, array('alignjustify'));
    }

    return array_merge(
        array_slice($buttons, 0, $position + 1),
        array('alignjustify'),
        array_slice($buttons, $position + 1)
    );
} // csmm_modify_mce


function csmm_onboarding()
{
    echo '<div id="csmm-onboarding-tabs-wrapper" style="display:none;">';

    echo '<div class="wrap">
            <h1 class="csmm-logo-wrapper">';

    if (csmm_get_rebranding() !== false) {
        echo '<img src="' . csmm_get_rebranding('logo_url') . '" class="csmm-logo" style="max-width:none;max-height:50px;" />';
    } else {
        echo '<img src="' . CSMM_URL . '/framework/admin/img/mm-icon-dark.png" class="csmm-logo" />';
    }

    if (csmm_get_rebranding() !== false) {
        echo '<strong>' . csmm_get_rebranding('name') . '</strong>';
    } else {
        echo '<strong>Coming Soon &amp; Maintenance Mode </strong><strong style="color: #fe2929;">PRO</strong>';
    }

    if (csmm_get_rebranding() !== false) {
        $plugin_by = '<span>by <a href="' . csmm_get_rebranding('url') . '" target="_blank"> ' . csmm_get_rebranding('company_name') . '</a></span>';
    } else {
        $plugin_by = '<span>by <a href="https://www.webfactoryltd.com/" target="_blank">WebFactory Ltd</a></span>';
    }

    if (csmm_whitelabel_filter()) {
        echo $plugin_by;
    }

    echo '</h1>';

    echo '<h1 align="center">Setup Wizard</h1>';

    echo '<form method="post" action="options.php" enctype="multipart/form-data" id="csmm_form">';
    echo '<div id="csmm-onboarding-tabs" class="ui-tabs">';

    echo '<ul class="csmm-onboarding-tab">';
    echo '<li><a href="#csmm_onboarding_step1"><span class="csmm-onboarding-step-number">1</span><span class="label">Welcome</span></a></li>';
    echo '<li><a href="#csmm_onboarding_step2"><span class="csmm-onboarding-step-number">2</span><span class="label">Settings</span></a></li>';
    echo '<li><a href="#csmm_onboarding_step3"><span class="csmm-onboarding-step-number">3</span><span class="label">Logging</span></a></li>';
    echo '<li><a href="#csmm_onboarding_step4"><span class="csmm-onboarding-step-number">4</span><span class="label">Finish</span></a></li>';
    echo '<li class="csmm-onboarding-step-link"></li>';
    echo '</ul>';

    echo '<div style="display: none;" id="csmm_onboarding_step1" data-tab="0">';

    echo 'Step 1';

    echo '<div class="csmm-onboarding-tabs-nav">';
    echo '<div class="csmm-btn csmm-onboarding-tab-skip">Skip</div>';
    echo '<div class="csmm-btn csmm-btn-red csmm-onboarding-tab-next">Next</div>';
    echo '</div>';
    echo '</div>';

    echo '<div style="display: none;" id="csmm_onboarding_step2" data-tab="1">';
    echo 'Step 2';
    echo '<div class="csmm-onboarding-tabs-nav">';
    echo '<div class="csmm-btn csmm-onboarding-tab-skip">Skip</div>';
    echo '<div class="csmm-btn csmm-btn-red csmm-onboarding-tab-next">Next</div>';
    echo '</div>';
    echo '</div>';

    echo '<div style="display: none;" id="csmm_onboarding_step3" data-tab="2">';
    echo 'Step 3';
    echo '<div class="csmm-onboarding-tabs-nav">';
    echo '<div class="csmm-btn csmm-onboarding-tab-skip">Skip</div>';
    echo '<div class="csmm-btn csmm-btn-red csmm-onboarding-tab-next">Next</div>';
    echo '</div>';
    echo '</div>';

    echo '<div style="display: none;" id="csmm_onboarding_step4" data-tab="3">';
    echo 'Step 4';
    echo '<div class="csmm-onboarding-tabs-nav">';
    echo '<div class="csmm-btn csmm-btn-red csmm-onboarding-tab-skip">Finish</div>';
    echo '</div>';
    echo '</div>';

    echo '</div>';

    echo '</form>';

    echo '</div>';
} // csmm_onboarding


function csmm_brand_css()
{
    if (csmm_get_rebranding() !== false) {
        echo '<style>' . csmm_get_rebranding('admin_css_predefined') . csmm_get_rebranding('admin_css') . '</style>';
    }
}

add_action('admin_head', 'csmm_brand_css');


add_filter('install_plugins_table_api_args_featured', 'csmm_featured_plugins_tab');

/**
 * Helper function for adding plugins to featured list
 *
 * @return array
 */
function csmm_featured_plugins_tab($args)
{
    add_filter('plugins_api_result', 'csmm_plugins_api_result', 10, 3);
    return $args;
} // featured_plugins_tab


/**
 * Add plugins to featured plugins list
 *
 * @return object
 */
function csmm_plugins_api_result($res, $action, $args)
{
    remove_filter('plugins_api_result', 'csmm_plugins_api_result', 10, 3);

    $res = csmm_add_plugin_featured('eps-301-redirects', $res);
    $res = csmm_add_plugin_featured('wp-force-ssl', $res);

    return $res;
} // plugins_api_result


/**
 * Add single plugin to featured list
 *
 * @return object
 */
function csmm_add_plugin_featured($plugin_slug, $res)
{
    // check if plugin is already on the list
    if (!empty($res->plugins) && is_array($res->plugins)) {
        foreach ($res->plugins as $plugin) {
            if (is_object($plugin) && !empty($plugin->slug) && $plugin->slug == $plugin_slug) {
                return $res;
            }
        } // foreach
    }

    $plugin_info = get_transient('wf-plugin-info-' . $plugin_slug);

    if (!$plugin_info) {
        $plugin_info = plugins_api('plugin_information', array(
            'slug' => $plugin_slug,
            'is_ssl' => is_ssl(),
            'fields' => array(
                'banners' => true,
                'reviews' => true,
                'downloaded' => true,
                'active_installs' => true,
                'icons' => true,
                'short_description' => true,
            ),
        ));
        if (!is_wp_error($plugin_info)) {
            set_transient('wf-plugin-info-' . $plugin_slug, $plugin_info, DAY_IN_SECONDS * 7);
        }
    }

    if (!empty($res->plugins) && is_array($res->plugins) && $plugin_info && is_object($plugin_info)) {
        array_unshift($res->plugins, $plugin_info);
    }

    return $res;
} // add_plugin_featured