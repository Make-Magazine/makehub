<?php

/*
Plugin Name: MailOptin - Pro
Plugin URI: https://mailoptin.io
Description: Best lead generation, email automation & newsletter plugin.
Version: 2.2.65.0
Author: MailOptin Team
Contributors: collizo4sky
Author URI: https://mailoptin.io
Text Domain: mailoptin
Domain Path: /languages
*/

require __DIR__ . '/vendor/autoload.php';

define('MAILOPTIN_SYSTEM_FILE_PATH', __FILE__);
define('MAILOPTIN_VERSION_NUMBER', '2.2.65.0');
define('EDD_MO_ITEM_ID', '5591');

add_action('init', 'mo_mailoptin_load_plugin_textdomain', 0);
function mo_mailoptin_load_plugin_textdomain()
{
    load_plugin_textdomain('mailoptin', false, dirname(plugin_basename(__FILE__)) . '/languages');
}

MailOptin\Core\Core::init();
MailOptin\Connections\Init::init();
MailOptin\Libsodium\Libsodium::get_instance()->libsodium()->libprodium();