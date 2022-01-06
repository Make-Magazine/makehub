<?php
/*
Plugin Name: Easy Affiliate Basic
Plugin URI: https://easyaffiliate.com/
Description: A complete Affiliate Program plugin for WordPress. Use it to start an Affiliate Program for your products to dramatically increase traffic, attention and sales.
Version: 1.2.3
Author: Caseproof, LLC
Author URI: https://caseproof.com/
Text Domain: easy-affiliate
Copyright: 2004-2020, Caseproof, LLC

GNU General Public License, Free Software Foundation <http://creativecommons.org/licenses/GPL/2.0/>
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }

define('ESAF_MINIMUM_PHP_VERSION', '5.5.0');
define('ESAF_DB_VERSION', 35); // this is the version of the database we're moving to
define('ESAF_PLUGIN_FILE', __FILE__);
define('ESAF_PATH', dirname(ESAF_PLUGIN_FILE));
define('ESAF_PLUGIN_NAME', basename(ESAF_PATH));
define('ESAF_PLUGIN_SLUG', ESAF_PLUGIN_NAME . '/' . basename(ESAF_PLUGIN_FILE));
define('ESAF_IMAGES_PATH', ESAF_PATH . '/images');
define('ESAF_CSS_PATH', ESAF_PATH . '/css');
define('ESAF_JS_PATH', ESAF_PATH . '/js');
define('ESAF_I18N_PATH', ESAF_PATH . '/i18n');
define('ESAF_MODELS_PATH', ESAF_PATH . '/app/Models');
define('ESAF_CTRLS_PATH', ESAF_PATH . '/app/Controllers');
define('ESAF_JOBS_PATH', ESAF_PATH . '/app/Jobs');
define('ESAF_LIB_PATH', ESAF_PATH . '/app/Lib');
define('ESAF_CONFIG_PATH', ESAF_PATH . '/app/config');
define('ESAF_VIEWS_PATH', ESAF_PATH . '/app/views');
define('ESAF_HELPERS_PATH', ESAF_PATH . '/app/Helpers');
define('ESAF_TESTS_PATH', ESAF_PATH . '/tests');
define('ESAF_URL', plugins_url(ESAF_PLUGIN_NAME));
define('ESAF_IMAGES_URL', ESAF_URL . '/images');
define('ESAF_CSS_URL', ESAF_URL . '/css');
define('ESAF_JS_URL', ESAF_URL . '/js');
define('ESAF_SCRIPT_URL', get_site_url(null, 'index.php?plugin=wafp'));
define('ESAF_EDITION', 'easy-affiliate-basic');
define('ESAF_VERSION', esaf_plugin_info('Version'));
define('ESAF_DISPLAY_NAME', esaf_plugin_info('Name'));
define('ESAF_AUTHOR', esaf_plugin_info('Author'));
define('ESAF_AUTHOR_NAME', esaf_plugin_info('AuthorName'));
define('ESAF_AUTHOR_URI', esaf_plugin_info('AuthorURI'));
define('ESAF_DESCRIPTION', esaf_plugin_info('Description'));

/**
 * Returns a value from the current plugin header comment.
 *
 * @param  string $field Which value to retrieve
 * @return string
 */
function esaf_plugin_info($field) {
  static $plugin_data;

  if(!isset($plugin_data)) {
    if(!function_exists('get_plugin_data')) {
      require_once ABSPATH . '/wp-admin/includes/plugin.php';
    }

    $plugin_data = get_plugin_data(ESAF_PLUGIN_FILE);
  }

  if(isset($plugin_data[$field])) {
    return $plugin_data[$field];
  }

  return '';
}

function esaf_insecure_php_version_notice() {
  ?>
  <div class="notice notice-error">
    <p>
      <?php
        printf(
          esc_html__('Your site is running an %1$sinsecure version%2$s of PHP that is no longer supported. Please contact your web hosting provider to update your PHP version. %1$sEasy Affiliate is disabled%2$s on your site until you fix the issue.', 'easy-affiliate'),
          '<strong>',
          '</strong>'
        );
      ?>
    </p>
  </div>
  <?php
}

if(version_compare(phpversion(), ESAF_MINIMUM_PHP_VERSION, '<')) {
  add_action('admin_notices', 'esaf_insecure_php_version_notice');
  return;
}

require_once ESAF_PATH . '/main.php';
