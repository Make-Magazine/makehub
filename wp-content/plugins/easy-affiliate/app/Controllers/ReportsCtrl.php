<?php

namespace EasyAffiliate\Controllers;

use EasyAffiliate\Helpers\ExportHelper;
use EasyAffiliate\Helpers\ReportsHelper;
use EasyAffiliate\Lib\BaseCtrl;
use EasyAffiliate\Lib\Report;
use EasyAffiliate\Lib\Utils;

class ReportsCtrl extends BaseCtrl {
  public function load_hooks() {
    add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);
    add_action('wp_ajax_esaf_get_report_data', [$this, 'get_report_data']);
    add_action('wp_ajax_esaf_report_stats_export_csv', [$this, 'report_stats_export_csv']);
  }

  public function admin_enqueue_scripts() {
    if(preg_match('/_page_easy-affiliate$/', Utils::get_current_screen_id())) {
      wp_enqueue_style('date-range-picker', ESAF_CSS_URL . '/library/daterangepicker.css', [], ESAF_VERSION);
      wp_enqueue_script('momentjs', ESAF_JS_URL . '/library/moment.min.js', [], ESAF_VERSION, true);
      wp_enqueue_script('chart-min-js', ESAF_JS_URL . '/library/chart.min.js', ['jquery', 'momentjs'], ESAF_VERSION, true);
      wp_enqueue_script('date-range-picker', ESAF_JS_URL . '/library/daterangepicker.min.js', ['jquery'], ESAF_VERSION, true);
      wp_enqueue_script('esaf-admin-reports', ESAF_JS_URL . '/admin-reports.js', ['jquery', 'momentjs', 'chart-min-js', 'date-range-picker'], ESAF_VERSION, true);

      wp_localize_script('esaf-admin-reports', 'EsafAdminReportsL10n', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'clicks' => __('Clicks', 'easy-affiliate'),
        'uniques' => __('Uniques', 'easy-affiliate'),
        'sales' => __('Sales', 'easy-affiliate'),
        'last_30_days' => __('Last 30 Days', 'easy-affiliate'),
        'last_month' => __('Last Month', 'easy-affiliate'),
        'two_months_ago' => __('2 Months Ago', 'easy-affiliate'),
        'three_months_ago' => __('3 Months Ago', 'easy-affiliate'),
        'last_6_months' => __('Last 6 Months', 'easy-affiliate'),
        'this_year' => __('This Year', 'easy-affiliate'),
        'last_year' => __('Last Year', 'easy-affiliate'),
        'get_report_data_nonce' => wp_create_nonce('esaf_get_report_data'),
        'error_updating_report_data' => __('An error occurred updating the report data', 'easy-affiliate'),
      ]);
    }
  }

  public static function route() {
    try {
      $start = new \DateTimeImmutable('-29 days', new \DateTimeZone('UTC'));
      $end = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
      $stats = Report::get_report_stats_in_period($start, $end);

      require ESAF_VIEWS_PATH . '/reports/stats.php';
    } catch (\Exception $e) {
      $errors = [__('Sorry, the reports could not be displayed due to a critical error', 'easy-affiliate')];
      require ESAF_VIEWS_PATH . '/shared/errors.php';
    }
  }

  public function get_report_data() {
    if(empty($_GET['start']) || empty($_GET['end']) || !is_string($_GET['start']) || !is_string($_GET['end'])) {
      wp_send_json_error(__('Bad request', 'easy-affiliate'));
    }

    if(!Utils::is_logged_in_and_an_admin()) {
      wp_send_json_error(__('Sorry, you don\'t have permission to do this.', 'easy-affiliate'));
    }

    if(!check_ajax_referer('esaf_get_report_data', false, false)) {
      wp_send_json_error(__('Security check failed.', 'easy-affiliate'));
    }

    try {
      $start = new \DateTimeImmutable(sanitize_text_field(wp_unslash($_GET['start'])), new \DateTimeZone('UTC'));
      $end = new \DateTimeImmutable(sanitize_text_field(wp_unslash($_GET['end'])), new \DateTimeZone('UTC'));

      $stats = Report::get_report_stats_in_period($start, $end);
      $table_html = ReportsHelper::get_affiliate_stats_table_html($stats, $start, $end);

      wp_send_json_success(compact('stats', 'table_html'));
    } catch (\Exception $e) {
      wp_send_json_error(__('The given dates were invalid', 'easy-affiliate'));
    }
  }

  public function report_stats_export_csv() {
    if(empty($_GET['start']) || empty($_GET['end']) || !is_string($_GET['start']) || !is_string($_GET['end'])) {
      wp_die(__('Bad request', 'easy-affiliate'));
    }

    if(!Utils::is_logged_in_and_an_admin()) {
      wp_die(__('Sorry, you don\'t have permission to do this.', 'easy-affiliate'));
    }

    if(!check_ajax_referer('esaf_report_stats_export_csv', false, false)) {
      wp_die(__('Security check failed.', 'easy-affiliate'));
    }

    try {
      $start = new \DateTimeImmutable(sanitize_text_field(wp_unslash($_GET['start'])), new \DateTimeZone('UTC'));
      $end = new \DateTimeImmutable(sanitize_text_field(wp_unslash($_GET['end'])), new \DateTimeZone('UTC'));

      $stats = Report::get_report_stats_in_period($start, $end);

      ExportHelper::render_csv($stats, 'esaf-affiliate-stats-' . time());
    } catch (\Exception $e) {
      wp_die(__('The given dates were invalid', 'easy-affiliate'));
    }
  }
}
