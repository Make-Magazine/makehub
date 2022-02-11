<?php

/**
 * Required functions for the plugin.
 *
 * @link       http://www.webfactoryltd.com
 * @since      1.0
 */

function csmm_render_template($options)
{

    if (ob_get_length() > 0) {
        ob_flush();
    }

    if ($options['nocache']) {
        nocache_headers();
    }

    // Checking for options required for the plugin
    if (empty($options['title'])) :    $options['title']                 = __('Maintainance Mode', 'signals');
    endif;
    if (empty($options['input_text'])) :    $options['input_text']             = __('Enter your email address..', 'signals');
    endif;
    if (empty($options['button_text'])) :    $options['button_text']         = __('Subscribe', 'signals');
    endif;

    // Response message
    if (empty($options['message_noemail'])) :    $options['message_noemail']     = __('Oops! Something went wrong.', 'signals');
    endif;
    if (empty($options['message_subscribed'])) :    $options['message_subscribed']     = __('You are already subscribed!', 'signals');
    endif;
    if (empty($options['message_wrong'])) :    $options['message_wrong']         = __('Oops! Something went wrong.', 'signals');
    endif;
    if (empty($options['message_done'])) :    $options['message_done']         = __('Thank you! We\'ll be in touch!', 'signals');
    endif;


    // Template file
    if ('1' == $options['disable_settings'] || $options['mode'] == 'html') {
        require_once CSMM_PATH . 'framework/public/views/blank.php';
    } else {
        require_once CSMM_PATH . 'framework/public/views/html.php';
    }

    exit();
}


// To check the referrer
function csmm_check_referrer()
{

    // List of crawlers to check for
    $crawlers = array(
        'Abacho'              =>     'AbachoBOT',
        'Accoona'             =>     'Acoon',
        'AcoiRobot'           =>     'AcoiRobot',
        'Adidxbot'            =>     'adidxbot',
        'AltaVista robot'     =>     'Altavista',
        'Altavista robot'     =>     'Scooter',
        'ASPSeek'             =>     'ASPSeek',
        'Atomz'               =>     'Atomz',
        'Bing'                =>     'bingbot',
        'BingPreview'         =>     'BingPreview',
        'CrocCrawler'         =>     'CrocCrawler',
        'Dumbot'             =>     'Dumbot',
        'eStyle Bot'         =>     'eStyle',
        'FAST-WebCrawler'    =>     'FAST-WebCrawler',
        'GeonaBot'           =>     'GeonaBot',
        'Gigabot'            =>     'Gigabot',
        'Google'             =>     'Googlebot',
        'ID-Search Bot'      =>     'IDBot',
        'Lycos spider'       =>     'Lycos',
        'MSN'                =>     'msnbot',
        'MSRBOT'             =>     'MSRBOT',
        'Rambler'            =>     'Rambler',
        'Scrubby robot'      =>     'Scrubby',
        'Yahoo'               =>     'Yahoo'
    );


    // Checking for the crawler over here
    if (csmm_string_to_array($_SERVER['HTTP_USER_AGENT'], $crawlers)) {
        return true;
    }


    return false;
}


// Function to match the user agent with the crawlers array
function csmm_string_to_array($str, $array)
{

    $regexp = '~(' . implode('|', array_values($array)) . ')~i';
    return (bool) preg_match($regexp, $str);
}


function csmm_slashit($url)
{
    if (strpos($url, '?') === false && substr($url, -4, 1) != '.') {
        $url = trailingslashit($url);
    }

    if ($url != '/') {
        $url = '/' . ltrim($url, '/');
    }

    return $url;
} // slashit