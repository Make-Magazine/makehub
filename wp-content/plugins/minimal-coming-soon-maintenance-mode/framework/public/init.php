<?php

/**
 * Public side of the plugin.
 *
 * @link       http://www.webfactoryltd.com
 * @since      0.1
 */

use MaxMind\Db\Reader;

/* Include important files. */
require_once 'include/functions.php';
require_once 'include/vendor/autoload.php';
require_once 'include/misc/geoip/autoload.php';


/**
 * Get headers function if not found in php
 *
 * @since 6.0.7
 * 
 * 
 */
if (!function_exists('getallheaders')) {
    function getallheaders() {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}

/**
 * Parse user agent to return an array with info
 *
 * @since 6.0
 * 
 * @param string $user_agent
 * @return array user agent data
 */
function csmm_parse_user_agent_array($user_agent = false)
{
    if (!$user_agent) {
        $user_agent = getallheaders();
    }
    $user_agent = new WhichBrowser\Parser($user_agent);

    if (!is_null($user_agent)) {

        $agent['device'] = '';

        if ($user_agent->isType('mobile')) {
            $agent['device'] = 'mobile';
        } else if ($user_agent->isType('tablet')) {
            $agent['device'] = 'tablet';
        } else if ($user_agent->isType('desktop')) {
            $agent['device'] = 'desktop';
        } else {
            $agent['device'] = 'bot';
        }

        if(isset($user_agent->browser->name)){
            $agent['browser'] = $user_agent->browser->name;
        } else {
            $agent['browser'] = '';
        }

        if ($agent['device'] != 'bot') {
            $version = explode('.', $user_agent->browser->version->value);
            $agent['browser_ver'] = $version[0];
            $agent['os'] = $user_agent->os->name;
            if (!empty($user_agent->os->version->nickname)) {
                $agent['os_ver'] = $user_agent->os->version->nickname;
            } else if (!empty($user_agent->os->version->alias)) {
                $agent['os_ver'] = $user_agent->os->version->alias;
            } else {
                if(isset($user_agent->os->version->value)){
                    $agent['os_ver'] = $user_agent->os->version->value;
                } else {
                    $agent['os_ver'] = ''; 
                }
            }
        } else {
            $agent['bot'] = $agent['browser'];
            $agent['browser_ver'] = '';
            $agent['os'] = '';
            $agent['browser'] = '';
        }

        return $agent;
    } else {
        return array('browser' => '', 'device' => '', 'browser_ver' => '', 'os' => '', 'bot' => '');
    }
} // parse_user_agent_array


/**
 * Get user IP
 *
 * @since 5.0
 * 
 * @return string userip
 * 
 */
function csmm_get_user_ip()
{
    if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',') > 0) {
            $addr = explode(",", $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($addr[0]);
        } else {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
    } else if (!empty($_SERVER['REMOTE_ADDR'])) {
        return $_SERVER['REMOTE_ADDR'];
    } else {
        return 'unknown.ip';
    }
} // getUserIP


/**
 * Get user country
 *
 * @since 5.0
 * 
 * @return string country
 * 
 */
function get_user_country($ip = false)
{
    if (!$ip) {
        $ip = csmm_get_user_ip();
    }
    $reader = new Reader(CSMM_PATH . 'framework/public/include/misc/geo-country.mmdb');
    $ip_data = $reader->get($ip);
    $country = isset($ip_data) ? $ip_data['country']['names']['en'] : '';
    $reader->close();

    return $country;
}


function csmm_log_stats(){
    $options = csmm_get_options();
    if($options['track_stats'] !== 1){
        return false;
    }

    @session_start();
    if(isset($_SESSION['coming_soon'])){
        return false;
    }
    
    $_SESSION['coming_soon'] = true;
    $user_agent = csmm_parse_user_agent_array();
    $country = get_user_country();
    $stats = get_option(CSMM_STATS);
    if(false === $stats){
        $stats = array(
            'general' => array(
                'countries' => array('unknown' => 0),
                'browsers' => array('unknown' => 0),
                'devices' => array('unknown' => 0),
                'traffic' => array('unknown' => 0),
            ),
            'visits' => array(),
        );
    }

    if(!array_key_exists(current_time('Y-m-d'), $stats['visits'])){
        $stats['visits'][current_time('Y-m-d')] = 0;
    }
    $stats['visits'][current_time('Y-m-d')]++;

    $traffic = 'human';
    if(!empty($user_agent['device'])){
        if($user_agent['device'] == 'bot'){
            if(!empty($user_agent['browser'])){
                $traffic = $user_agent['browser'];
            } else {
                $traffic = 'unknown';
            }
        }

        if(!array_key_exists($user_agent['device'], $stats['general']['devices'])){
            $stats['general']['devices'][$user_agent['device']] = 0; 
        }
        $stats['general']['devices'][$user_agent['device']]++;
    } else {
        $stats['general']['devices']['unknown']++;
    }

    if($traffic == 'human'){
        if(!empty($user_agent['browser'])){
            if(!array_key_exists($user_agent['browser'], $stats['general']['browsers'])){
                $stats['general']['browsers'][$user_agent['browser']] = 0; 
            }
            $stats['general']['browsers'][$user_agent['browser']]++;
        } else {
            $stats['general']['browsers']['unknown']++;
        }
    }

    if(!array_key_exists($traffic, $stats['general']['traffic'])){
        $stats['general']['traffic'][$traffic] = 0; 
    }
    $stats['general']['traffic'][$traffic]++;

    if(!empty($country)){
        if(!array_key_exists($country, $stats['general']['countries'])){
            $stats['general']['countries'][$country] = 0; 
        }
        $stats['general']['countries'][$country]++;
    } else {
        $stats['general']['countries']['unknown']++;
    }
    
    update_option(CSMM_STATS, $stats);
}

function csmm_check_direct_access_password_unlock()
{
    if (isset($_SESSION['csmm_direct_access_password']) && $_SESSION['csmm_direct_access_password'] === true) {
        return true;
    } else {
        return false;
    }
}

function csmm_check_access_password_unlock()
{
    if (isset($_SESSION['csmm_access_password']) && $_SESSION['csmm_access_password'] === true) {
        return true;
    } else {
        return false;
    }
}

function csmm_check_password_locked()
{
    $options = csmm_get_options();
    if (isset($options['site_password']) && strlen($options['site_password']) > 0 && !csmm_check_access_password_unlock()) {
        return true;
    }

    return false;
}

function csmm_check_direct_access_locked()
{
    $options = csmm_get_options();
    if (isset($options['direct_access_password']) && strlen($options['direct_access_password']) > 0 && !csmm_check_direct_access_password_unlock()) {
        return true;
    }

    return false;
}


function csmm_plugin_init()
{
    if(csmm_access_rules_enabled()){
        @session_start();
    }

    // just to be on the safe side
    if (defined('DOING_CRON') && DOING_CRON) {
        return false;
    }
    if (defined('DOING_AJAX') && DOING_AJAX) {
        return false;
    }
    if (defined('WP_CLI') && WP_CLI) {
        return false;
    }

    // Plugin options from the database
    $signals_csmm_options = csmm_get_options();


    // init custom enter no main url

    $custom_url_link = $signals_csmm_options['direct_access_link'];
    if (!empty($custom_url_link)) {

        if (isset($_GET[$custom_url_link])) {
            $_SESSION['skip_maintenance_mode'] = true;
        }
    }

    if (csmm_check_direct_access_password_unlock()) {
        $_SESSION['skip_maintenance_mode'] = true;
    }

    // Getting custom login URL for the admin
    $signals_login_url = wp_login_url();


    // Checking for the server protocol status
    if (isset($_SERVER['HTTPS']) === true) {
        $signals_protocol = 'https';
    } else {
        $signals_protocol = 'http';
    }


    // This is the server address of the current page
    //$signals_server_url = csmm_slashit($signals_protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    $request_uri = csmm_slashit(strtolower(@parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)));
    $signals_server_url = get_home_url() . $request_uri;

    // Checking for the custom_login_url value
    if (empty($signals_csmm_options['custom_login_url'])) {
        $signals_csmm_options['custom_login_url'] = '';
    } else {
        $signals_csmm_options['custom_login_url'] = get_home_url() . csmm_slashit($signals_csmm_options['custom_login_url']);
    }
    
    if(isset($_GET['csmm_preview_theme'])){
        $uploads = wp_upload_dir();
        $csmm_theme_folder = $uploads['basedir'] . '/coming-soon-themes/' . trailingslashit($_GET['csmm_preview_theme']);
        clearstatcache();

        if(file_exists($csmm_theme_folder)){
            $theme_file = glob($csmm_theme_folder . '*.txt');
            $tmp = @json_decode(@file_get_contents($theme_file[0]), true);
            if (!is_array($tmp['meta']) || !is_array($tmp['data']) || sizeof($tmp['data']) < 30) {
                // Do nothing
            } else {
                csmm_render_template(array_merge($signals_csmm_options, $tmp['data']));
            }
        }
    }

    if (!is_admin() || isset($_GET['csmm_preview'])) {
        if ('1' == $signals_csmm_options['status'] || apply_filters('csmm_force_display', false) || isset($_GET['csmm_preview'])) {
            
            if (
                false === strpos($signals_server_url, '/wp-login.php')
                && false === strpos($signals_server_url, '/wp-admin/')
                && false === strpos($signals_server_url, '/async-upload.php')
                && false === strpos($signals_server_url, '/upgrade.php')
                && false === strpos($signals_server_url, '/xmlrpc.php')
                && false === strpos($signals_server_url, '/wp-json/')
                && false === strpos($signals_server_url, $signals_login_url)
                && !isset($_GET['mainwpsignature'])
            ) {

                $show_maintenance_mode = 1;

                if (!empty($signals_csmm_options['custom_login_url']) && false !== strpos($signals_server_url, $signals_csmm_options['custom_login_url'])) {
                    $show_maintenance_mode = 0;
                }

                // search engines
                if ($signals_csmm_options['exclude_se'] == '1' && csmm_check_referrer()) {
                    $show_maintenance_mode = 0;
                }

                // if logged in
                if ($signals_csmm_options['show_logged_in'] == '1' && is_user_logged_in()) {
                    $show_maintenance_mode = 0;
                }

                // IP whitelist
                $all_ips = explode("\n", $signals_csmm_options['signals_ip_whitelist']);
                $all_ips = array_map('trim', $all_ips);
                $current_ip = $_SERVER['REMOTE_ADDR'];


                if (@in_array($current_ip, $all_ips)) {
                    $show_maintenance_mode = 0;
                }

                // urls list
                $all_urls = explode("\n", trim($signals_csmm_options['per_url_enable_disable']));
                $all_urls = array_map('trim', $all_urls);


                // whitelisted / blacklisted URLs
                if ($signals_csmm_options['per_url_settings'] == 'whitelist' && !empty($all_urls)) {
                    if (in_array($request_uri, $all_urls)) {
                        $show_maintenance_mode = 0;
                    }
                } elseif ($signals_csmm_options['per_url_settings'] == 'blacklist' && !empty($all_urls)) {
                    if (!in_array($request_uri, $all_urls)) {
                        $show_maintenance_mode = 0;
                    }
                }

                if (!empty($_SESSION['skip_maintenance_mode'])) {
                    $show_maintenance_mode = 0;
                }

                $show_maintenance_mode = apply_filters('csmm_force_display', $show_maintenance_mode);

                if ($show_maintenance_mode == 1 || isset($_GET['csmm_preview'])) {
                    //Track stat
                    csmm_log_stats();
                    
                    if ($signals_csmm_options['forcessl'] == 1 && !is_ssl() && !isset($_GET['redirected'])) {
                        wp_safe_redirect(str_replace('http://', 'https://', home_url()) . '?redirected=1', 301);
                        exit();
                    }
                    
                    if($signals_csmm_options['mode'] == 'page' && 'publish' == get_post_status ( $signals_csmm_options['csmm_page'] )){
                        add_action( 'request', 'csmm_set_csmm_page' );
                    } else {
                        csmm_render_template($signals_csmm_options);
                    }
                }
            }
        }
    }
}

add_action('init', 'csmm_plugin_init');

function csmm_set_csmm_page($request){
    global $query;
    // Plugin options from the database
    $signals_csmm_options = csmm_get_options();
    
    $csmm_query = new WP_Query();
    $csmm_query->parse_query( $request );
    unset( $request['pagename'] );
    $request['page_id'] = $signals_csmm_options['csmm_page'];
    return $request;
}

