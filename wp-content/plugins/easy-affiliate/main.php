<?php

if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }

use EasyAffiliate\Controllers\AppCtrl;
use EasyAffiliate\Lib\CtrlFactory;

require_once ESAF_PATH . '/vendor/autoload.php';

add_action('activated_plugin', function ($plugin) {
  if($plugin == ESAF_PLUGIN_SLUG) {
    AppCtrl::activate();
  }
});

add_action('plugins_loaded', function () {
  // Deactivate Affiliate Royale if active
  if(defined('WAFP_PLUGIN_SLUG')) {
    if(!function_exists('deactivate_plugins')) {
      require_once ABSPATH . '/wp-admin/includes/plugin.php';
    }

    deactivate_plugins(WAFP_PLUGIN_SLUG);
  }

  CtrlFactory::all();

  AppCtrl::setup_menus();
});
