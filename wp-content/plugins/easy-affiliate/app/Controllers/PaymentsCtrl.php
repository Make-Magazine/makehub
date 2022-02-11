<?php

namespace EasyAffiliate\Controllers;

use EasyAffiliate\Lib\BaseCtrl;
use EasyAffiliate\Lib\PaymentsTable;
use EasyAffiliate\Lib\Report;
use EasyAffiliate\Lib\Utils;
use EasyAffiliate\Models\Options;
use EasyAffiliate\Models\Payment;
use EasyAffiliate\Lib\Paypal\PayPalClient;
use EasyAffiliate\Models\User;
use PaypalPayoutsSDK\Payouts\PayoutsPostRequest;

class PaymentsCtrl extends BaseCtrl {
  public function load_hooks() {
    add_action('current_screen', [self::class, 'add_payments_screen_options']);
    add_action('admin_init', [$this, 'process_bulk_actions']);
    add_action('admin_notices', [$this, 'admin_notices']);
    add_filter('set-screen-option', [self::class, 'set_payments_screen_options'], 10, 3);
    add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);
    add_action('wp_ajax_esaf_payout_paypal_mass_payment_file', [$this, 'payout_paypal_mass_payment_file']);
    add_action('wp_ajax_esaf_payout_paypal_one_click', [$this, 'payout_paypal_one_click']);
    add_action('wp_ajax_esaf_payout_manual', [$this, 'payout_manual']);
    add_action('wp_ajax_esaf_delete_payment', [$this, 'delete_payment']);
  }

  public function admin_enqueue_scripts() {
    $screen_id = Utils::get_current_screen_id();

    if(preg_match('/easy-affiliate-pay-affiliates$/', $screen_id) || preg_match('/easy-affiliate-payments$/', $screen_id)) {
      wp_enqueue_style('magnific-popup', ESAF_CSS_URL . '/magnific-popup.min.css', [], '1.1.0');
      wp_enqueue_script('magnific-popup', ESAF_JS_URL . '/jquery.magnific-popup.min.js', ['jquery'], '1.1.0', true);
      wp_enqueue_script('esaf-payments', ESAF_JS_URL . '/admin-payments.js', ['jquery'], ESAF_VERSION, true);

      wp_localize_script('esaf-payments', 'EsafPaymentsL10n', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'paypal_one_click_request_error' => __('An error occurred during payout, please check the PayPal account to confirm that the affiliates were paid before trying again.', 'easy-affiliate'),
        'manual_payout_request_error' => __('An error occurred creating the payments, please return to the Pay Affiliates page and try again.', 'easy-affiliate'),
        'confirm_delete_payment' => __('Are you sure you want to permanently delete this payment?', 'easy-affiliate'),
        'delete_payment_nonce' => wp_create_nonce('esaf_delete_payment'),
        'error_deleting_payment' => __('An error occurred deleting the payment', 'easy-affiliate'),
        'confirm_delete_payments' => __('Are you sure you want to permanently delete the selected payments?', 'easy-affiliate'),
      ]);
    }
  }

  public static function route() {
    if(isset($_REQUEST['page']) && $_REQUEST['page'] == 'easy-affiliate-pay-affiliates') {
      if(isset($_POST['esaf_process_payouts']) && $_POST['esaf_process_payouts'] == 'Y')
        self::process_update_payments();
      else
        self::admin_affiliates_owed();
    }
    else if(isset($_REQUEST['page']) && $_REQUEST['page'] == 'easy-affiliate-payments') {
      self::display_list();
    }
  }

  public static function admin_affiliates_owed($period = 'current') {
    if($period=='current' || empty($period)) {
      $period = mktime(0, 0, 0, date('n'), 1, date('Y'));
    }

    $payments = Payment::affiliates_owed( $period );

    extract($payments);

    require ESAF_VIEWS_PATH . '/payments/owed.php';
  }

  public static function build_paypal_payout_request_body($paypal_receipts, $sender_batch_id)
  {
    $options = Options::fetch();
    $request = [
      'sender_batch_header' => [
        "sender_batch_id" => $sender_batch_id,
        "email_subject" => sprintf(__("Your Commission Payout on %s", "easy-affiliate"), Utils::site_domain()),
      ],
      'items' => [],
    ];

    foreach ($paypal_receipts as $receipt) {
      $request['items'][] = [
        "recipient_type" => "EMAIL",
        "receiver"       => $receipt['email'],
        "sender_item_id" => $sender_batch_id . '_aff_' . $receipt['affiliate_id'],
        "amount"         => [
          "currency" => $options->currency_code,
          "value"    => $receipt['amount'],
        ]
      ];
    }

    return $request;
  }

  public static function create_paypal_payout_request($paypal_receipts, $sender_batch_id) {
    $request       = new PayoutsPostRequest();
    $request->body = self::build_paypal_payout_request_body($paypal_receipts, $sender_batch_id);
    $client        = PayPalClient::client();
    $response      = $client->execute($request);

    return $response;
  }

  public static function process_update_payments() {
    if(Utils::is_post_request() && !empty($_POST['esaf_payouts']) && is_array($_POST['esaf_payouts'])) {
      self::admin_affiliate_payment_receipt();
    }
    else {
      self::admin_affiliates_owed();
    }
  }

  public static function admin_affiliate_payment_receipt() {
    $options = Options::fetch();

    $period = isset($_POST['esaf_payout_period']) && is_numeric($_POST['esaf_payout_period']) ? (int) $_POST['esaf_payout_period'] : 0;
    $payouts = isset($_POST['esaf_payouts']) && is_array($_POST['esaf_payouts']) ? self::sanitize_payouts($_POST['esaf_payouts']) : [];

    require ESAF_VIEWS_PATH . '/payments/receipt.php';
  }

  public static function sanitize_payouts(array $payouts) {
    $sanitized = [];

    foreach($payouts as $affiliate_id => $amount) {
      if(is_numeric($affiliate_id) && $affiliate_id > 0 && is_numeric($amount) && $amount > 0) {
        $sanitized[$affiliate_id] = Utils::format_float($amount);
      }
    }

    return $sanitized;
  }

  public static function admin_paypal_bulk_file($payment_id) {
    $options = Options::fetch();
    $blogname = Utils::blogname();
    $blogurl = Utils::blogurl();
    $bulk_totals = Report::affiliate_bulk_file_totals($payment_id);

    require ESAF_VIEWS_PATH . '/payments/paypal_bulk_file.php';
  }

  public static function admin_manual_bulk_csv_download($payment_id) {
    $options = Options::fetch();
    $blogname = Utils::blogname();
    $blogurl = Utils::blogurl();
    $bulk_totals = Report::affiliate_bulk_file_totals($payment_id);

    require ESAF_VIEWS_PATH . '/payments/manual_bulk_file.php';
  }

  public function payout_paypal_mass_payment_file() {
    if(!Utils::is_post_request()) {
      wp_send_json_error(__('Bad request.', 'easy-affiliate'));
    }

    if(!Utils::is_logged_in_and_an_admin()) {
      wp_send_json_error(__('Sorry, you don\'t have permission to do this.', 'easy-affiliate'));
    }

    if(!check_ajax_referer('esaf_payout', false, false)) {
      wp_send_json_error(__('Security check failed.', 'easy-affiliate'));
    }

    $payouts = isset($_POST['payouts']) && is_array($_POST['payouts']) ? self::sanitize_payouts($_POST['payouts']) : [];
    $period = isset($_POST['period']) && is_numeric($_POST['period']) && $_POST['period'] > 0 ? (int) $_POST['period'] : 0;
    $batch_id = isset($_POST['batch_id']) && is_string($_POST['batch_id']) ? sanitize_text_field(wp_unslash($_POST['batch_id'])) : 'EA_Payouts_' . time();

    if(empty($payouts) || empty($period)) {
      wp_send_json_error(__('Bad request.', 'easy-affiliate'));
    }

    $payment_ids = [];

    foreach($payouts as $affiliate_id => $payment_amount) {
      $affiliate = new User($affiliate_id);

      if(
        $affiliate->ID > 0
        && !empty($affiliate->paypal_email)
        && is_email($affiliate->paypal_email)
        && $payment_amount > 0
      ) {
        $payment = new Payment();
        $payment->affiliate_id = $affiliate_id;
        $payment->amount = $payment_amount;
        $payment->payout_method = 'paypal';
        $payment->batch_id = $batch_id;
        $payment->store();

        Payment::update_transactions($payment->id, $payment->affiliate_id, $period);

        if($payment->id > 0) {
          $payment_ids[] = $payment->id;
        }
      }
    }

    $payment_ids = implode(',', $payment_ids);

    wp_send_json_success([
      'download_file_url' => ESAF_SCRIPT_URL . '&controller=payments&action=manual_bulk_csv_download&id=' . $payment_ids,
      'download_mass_pay_file_url' => ESAF_SCRIPT_URL . '&controller=payments&action=paypal_bulk_file&id=' . $payment_ids
    ]);
  }

  public function payout_paypal_one_click() {
    if(!Utils::is_post_request()) {
      wp_send_json_error(__('Bad request.', 'easy-affiliate'));
    }

    if(!Utils::is_logged_in_and_an_admin()) {
      wp_send_json_error(__('Sorry, you don\'t have permission to do this.', 'easy-affiliate'));
    }

    if(!check_ajax_referer('esaf_payout', false, false)) {
      wp_send_json_error(__('Security check failed.', 'easy-affiliate'));
    }

    $payouts = isset($_POST['payouts']) && is_array($_POST['payouts']) ? self::sanitize_payouts($_POST['payouts']) : [];
    $period = isset($_POST['period']) && is_numeric($_POST['period']) && $_POST['period'] > 0 ? (int) $_POST['period'] : 0;
    $batch_id = isset($_POST['batch_id']) && is_string($_POST['batch_id']) ? sanitize_text_field(wp_unslash($_POST['batch_id'])) : 'EA_Payouts_' . time();

    if(empty($payouts) || empty($period)) {
      wp_send_json_error(__('Bad request.', 'easy-affiliate'));
    }

    $paypal_receipts = [];
    $paid_affiliates = [];

    foreach($payouts as $affiliate_id => $pay_amount) {
      $affiliate = new User($affiliate_id);

      if(
        $affiliate->ID > 0
        && !empty($affiliate->paypal_email)
        && is_email($affiliate->paypal_email)
        && $pay_amount > 0
      ) {
        $paypal_receipts[] = [
          'email'  => $affiliate->paypal_email,
          'amount' => $pay_amount,
          'affiliate_id' => $affiliate->ID,
        ];

        $paid_affiliates[$affiliate->ID] = $pay_amount;
      }
    }

    if(empty($paypal_receipts)) {
      wp_send_json_error(__('No valid payouts found.', 'easy-affiliate'));
    }
    elseif(count($paypal_receipts) > 15000) {
      wp_send_json_error(__('The number of payouts exceeds the limit of 15000, please split them into smaller batches.', 'easy-affiliate'));
    }

    try {
      $payout_response = self::create_paypal_payout_request($paypal_receipts, $batch_id);
    } catch (\Exception $e) {
      $message = $e->getMessage();

      if(strpos($message, '{') === 0) {
        $decoded = json_decode($message, true);

        if(is_array($decoded) && isset($decoded['error_description'])) {
          $message = $decoded['error_description'];
        }
      }

      wp_send_json_error($message);
    }

    if(
      !isset($payout_response) ||
      !isset($payout_response->result) ||
      !in_array($payout_response->statusCode, [200, 201])
    ) {
      wp_send_json_error(__('Invalid data', 'easy-affiliate'));
    }

    $payment_ids = [];

    foreach($paid_affiliates as $affiliate_id => $payment_amount) {
      $affiliate_id = (int) $affiliate_id;
      $payment = new Payment();
      $payment->affiliate_id = $affiliate_id;
      $payment->amount = $payment_amount;
      $payment->payout_method = 'paypal-1-click';
      $payment->batch_id = $batch_id;
      $payment->store();

      Payment::update_transactions($payment->id, $payment->affiliate_id, $period);

      if($payment->id > 0) {
        $payment_ids[] = $payment->id;
      }
    }

    $payment_ids = implode(',', $payment_ids);

    wp_send_json_success([
      'download_file_url' => ESAF_SCRIPT_URL . '&controller=payments&action=manual_bulk_csv_download&id=' . $payment_ids
    ]);
  }

  public function payout_manual() {
    if(!Utils::is_post_request()) {
      wp_send_json_error(__('Bad request.', 'easy-affiliate'));
    }

    if(!Utils::is_logged_in_and_an_admin()) {
      wp_send_json_error(__('Sorry, you don\'t have permission to do this.', 'easy-affiliate'));
    }

    if(!check_ajax_referer('esaf_payout', false, false)) {
      wp_send_json_error(__('Security check failed.', 'easy-affiliate'));
    }

    $payouts = isset($_POST['payouts']) && is_array($_POST['payouts']) ? self::sanitize_payouts($_POST['payouts']) : [];
    $period = isset($_POST['period']) && is_numeric($_POST['period']) && $_POST['period'] > 0 ? (int) $_POST['period'] : 0;
    $batch_id = isset($_POST['batch_id']) && is_string($_POST['batch_id']) ? sanitize_text_field(wp_unslash($_POST['batch_id'])) : 'EA_Payouts_' . time();

    if(empty($payouts) || empty($period)) {
      wp_send_json_error(__('Bad request.', 'easy-affiliate'));
    }

    $payment_ids = [];

    foreach($payouts as $affiliate_id => $payment_amount) {
      $affiliate = new User($affiliate_id);

      if($affiliate->ID > 0 && $payment_amount > 0) {
        $payment = new Payment();
        $payment->affiliate_id = $affiliate_id;
        $payment->amount = $payment_amount;
        $payment->payout_method = 'manual';
        $payment->batch_id = $batch_id;
        $payment->store();

        Payment::update_transactions($payment->id, $payment->affiliate_id, $period);

        if($payment->id > 0) {
          $payment_ids[] = $payment->id;
        }
      }
    }

    $payment_ids = implode(',', $payment_ids);

    wp_send_json_success([
      'download_file_url' => ESAF_SCRIPT_URL . '&controller=payments&action=manual_bulk_csv_download&id=' . $payment_ids
    ]);
  }

  public static function display_list() {
    $list_table = new PaymentsTable();
    $list_table->prepare_items();

    require ESAF_VIEWS_PATH . '/payments/list.php';
  }

  public function admin_notices() {
    if(isset($_GET['deleted']) &&
       !empty($_GET['deleted']) &&
       isset($_GET['page']) &&
       $_GET['page'] == 'easy-affiliate-payments'
    ) {
      $deleted = intval($_GET['deleted']);
    } else {
      return;
    }

    $class = 'notice notice-success is-dismissible';
    $message = sprintf(_n('%s payment permanently deleted.', '%s payments permanently deleted.', $deleted, 'easy-affiliate'), $deleted);

    printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
  }

  public function delete_payment() {
    if(!Utils::is_post_request() || empty($_POST['payment_id']) || !is_string($_POST['payment_id']) || !is_numeric($_POST['payment_id'])) {
      wp_send_json_error(__('Bad request', 'easy-affiliate'));
    }

    if(!Utils::is_logged_in_and_an_admin()) {
      wp_send_json_error(__('Sorry, you don\'t have permission to do this.', 'easy-affiliate'));
    }

    if(!check_ajax_referer('esaf_delete_payment', false, false)) {
      wp_send_json_error(__('Security check failed.', 'easy-affiliate'));
    }

    $payment = new Payment((int) $_POST['payment_id']);

    if($payment->id > 0) {
      $payment->destroy();
    }

    wp_send_json_success();
  }

  public function process_bulk_actions()
  {
    if(!isset($_GET['page']) || empty($_GET['page']) || $_GET['page'] != 'easy-affiliate-payments') {
      return;
    }

    $action = isset($_GET['action']) && !empty($_GET['action']) && $_GET['action'] != '-1' ? $_GET['action'] : null;

    if(empty($action)) {
      $action = isset($_GET['action2']) && !empty($_GET['action2']) && $_GET['action2'] != '-1' ? $_GET['action2'] : null;
    }

    if(!empty($action)) {
      check_admin_referer('bulk-wp_list_wafp_payments');

      if(!Utils::is_logged_in_and_an_admin()) {
        wp_die(__( 'Sorry, you are not allowed to do this.', 'easy-affiliate'), 403);
      }

      $sendback = remove_query_arg(['trashed', 'untrashed', 'deleted', 'locked', 'ids', '_wpnonce'], wp_get_referer());

      $posts = isset($_GET['post']) ? array_map('intval', $_GET['post']) : [];

      if(empty($posts)) {
        wp_redirect($sendback);
        exit;
      }

      switch($action) {
        case 'delete':
          $deleted = 0;

          foreach($posts as $id) {
            $payment = new Payment($id);

            if($payment->id > 0) {
              $payment->destroy();
              $deleted++;
            }
          }

          $sendback = add_query_arg('deleted', $deleted, $sendback);
          break;
      }

      wp_redirect($sendback);
      exit;
    }
    elseif(!empty($_GET['_wp_http_referer'])) {
      wp_redirect(remove_query_arg(['_wp_http_referer', '_wpnonce'], wp_unslash($_SERVER['REQUEST_URI'])));
      exit;
    }
  }

  /**
   * Add Screen Options to the Payments list page
   *
   * @param \WP_Screen $screen
   */
  public static function add_payments_screen_options($screen) {
    if($screen instanceof \WP_Screen && preg_match('/_page_easy-affiliate-payments$/', $screen->id)) {
      add_screen_option('per_page', [
        'label' => esc_html__('Payments per page', 'easy-affiliate'),
        'default' => 10,
        'option' => 'esaf_payments_per_page'
      ]);

      add_filter("manage_{$screen->id}_columns", [PaymentsTable::class, 'get_column_headers']);
    }
  }

  /**
   * Save the Screen Options on the Payments list page
   *
   * @param  bool     $keep
   * @param  string   $option
   * @param  string   $value
   * @return int|bool
   */
  public static function set_payments_screen_options($keep, $option, $value) {
    if ($option == 'esaf_payments_per_page' && is_numeric($value)) {
      return Utils::clamp((int) $value, 1, 999);
    }

    return $keep;
  }
}
