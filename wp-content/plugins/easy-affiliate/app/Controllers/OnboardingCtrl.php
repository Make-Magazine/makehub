<?php

namespace EasyAffiliate\Controllers;

use EasyAffiliate\Lib\BaseCtrl;
use EasyAffiliate\Lib\Utils;

class OnboardingCtrl extends BaseCtrl {
  public function load_hooks() {
    add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);
    add_action('admin_notices', [$this, 'remove_all_admin_notices'], 0);
  }

  public function admin_enqueue_scripts() {
    if(preg_match('/_page_easy-affiliate-onboarding$/', Utils::get_current_screen_id())) {
      wp_enqueue_style('esaf-onboarding', ESAF_CSS_URL . '/admin-onboarding.css', [], ESAF_VERSION);
    }
  }

  public function remove_all_admin_notices() {
    if(preg_match('/_page_easy-affiliate-onboarding$/', Utils::get_current_screen_id())) {
      remove_all_actions('admin_notices');
    }
  }

  public static function route() {
    global $wpdb;

    $wpdb->query("INSERT INTO {$wpdb->options} (option_name, option_value) VALUES('esaf_onboarded', '1') ON DUPLICATE KEY UPDATE option_value = VALUES(option_value);");

    require ESAF_VIEWS_PATH . '/admin/onboarding.php';
  }
}
