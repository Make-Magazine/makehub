<?php

namespace EasyAffiliate\Controllers;

use EasyAffiliate\Lib\BaseCtrl;
use EasyAffiliate\Lib\ClicksTable;
use EasyAffiliate\Lib\Utils;

class ClicksCtrl extends BaseCtrl {
  public function load_hooks() {
    add_action('admin_init', [self::class, 'process_bulk_actions']);
    add_action('current_screen', [self::class, 'add_clicks_screen_options']);
    add_filter('set-screen-option', [self::class, 'set_clicks_screen_options'], 10, 3);
  }

  public static function route() {
    self::display_list();
  }

  public static function display_list() {
    $list_table = new ClicksTable();
    $list_table->prepare_items();

    require ESAF_VIEWS_PATH . '/clicks/list.php';
  }

  public static function process_bulk_actions() {
    if(empty($_GET['page']) || $_GET['page'] != 'easy-affiliate-clicks') {
      return;
    }

    if(!empty($_GET['_wp_http_referer'])) {
      wp_redirect(remove_query_arg(['_wp_http_referer', '_wpnonce'], wp_unslash($_SERVER['REQUEST_URI'])));
      exit;
    }
  }

  /**
   * Add Screen Options to the Clicks list page
   *
   * @param \WP_Screen $screen
   */
  public static function add_clicks_screen_options($screen) {
    if ($screen instanceof \WP_Screen && preg_match('/_page_easy-affiliate-clicks$/', $screen->id)) {
      add_screen_option('per_page', [
        'label' => esc_html__('Clicks per page', 'easy-affiliate'),
        'default' => 10,
        'option' => 'esaf_clicks_per_page'
      ]);
    }
  }

  /**
   * Save the Screen Options on the Clicks list page
   *
   * @param  bool     $keep
   * @param  string   $option
   * @param  string   $value
   * @return int|bool
   */
  public static function set_clicks_screen_options($keep, $option, $value) {
    if ($option == 'esaf_clicks_per_page' && is_numeric($value)) {
      return Utils::clamp((int) $value, 1, 999);
    }

    return $keep;
  }
}
