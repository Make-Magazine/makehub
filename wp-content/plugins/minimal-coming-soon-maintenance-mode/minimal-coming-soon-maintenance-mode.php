<?php

/**
 * Plugin Name: Coming Soon & Maintenance Mode PRO
 * Plugin URI: https://comingsoonwp.com/
 * Description: Simply awesome coming soon & maintenance mode plugin. Super-simple to use.
 * Version: 6.44
 * Author: WebFactory
 * Author URI: https://www.webfactoryltd.com
 * License: Proprietary
 *
 *
 * Coming Soon & Maintenance Mode Plugin
 * Copyright (C) 2016 - 2019, Web Factory Ltd - csmm@webfactoryltd.com
 *
 * This program is NOT free software. Unauthorized distribution is strictly forbidden.
 */


if (!defined('WPINC')) {
    die;
}

define('CSMM_BASENAME', plugin_basename(__FILE__));
define('CSMM_FILE', __FILE__);
define('CSMM_URL', plugins_url('', __FILE__));
define('CSMM_PATH', plugin_dir_path(__FILE__));
define('CSMM_POINTERS', 'csmm_pointers');
define('CSMM_STATS', 'csmm_stats');
define('CSMM_ASSETS', 'https://assets.comingsoonwp.com/');

require CSMM_PATH . 'framework/init.php';
require CSMM_PATH . 'framework/wf-licensing.php';
require CSMM_PATH . 'framework/license.php';

if (is_admin()) {
    require CSMM_PATH . 'framework/admin/init.php';
} else {
    require CSMM_PATH . 'framework/public/init.php';
}