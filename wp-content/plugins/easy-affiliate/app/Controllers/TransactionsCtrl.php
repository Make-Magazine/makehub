<?php

namespace EasyAffiliate\Controllers;

use EasyAffiliate\Helpers\AppHelper;
use EasyAffiliate\Lib\BaseCtrl;
use EasyAffiliate\Lib\TransactionsTable;
use EasyAffiliate\Lib\Utils;
use EasyAffiliate\Models\Commission;
use EasyAffiliate\Models\Options;
use EasyAffiliate\Models\Transaction;
use EasyAffiliate\Models\User;

class TransactionsCtrl extends BaseCtrl {
  public function load_hooks() {
    add_action('admin_enqueue_scripts', [self::class, 'admin_enqueue_scripts']);
    add_action('wp_ajax_esaf_delete_transaction', [self::class, 'delete_transaction']);
    add_action('wp_ajax_wafp_delete_commission', [self::class, 'delete_commission']);
    add_action('admin_init', [self::class, 'process_bulk_actions']);
    add_action('current_screen', [self::class, 'add_transactions_screen_options']);
    add_filter('set-screen-option', [self::class, 'set_transactions_screen_options'], 10, 3);
    add_action('esaf_event_transaction-completed', [self::class, 'event_transaction_completed']);
  }

  public static function admin_enqueue_scripts() {
    if(preg_match('/_page_easy-affiliate-transactions$/', Utils::get_current_screen_id())) {
      wp_enqueue_style('esaf-admin-transactions', ESAF_CSS_URL . '/admin-transactions.css', [], ESAF_VERSION);
      wp_enqueue_script('esaf-admin-transactions', ESAF_JS_URL . '/admin-transactions.js', ['jquery'], ESAF_VERSION, true);

      $l10n = [
        'add_commssion_level_button_html' => self::get_add_commission_level_button_html(),
        'new_commission_form' => self::get_new_commission_form(),
        'commission_levels_enabled' => Utils::is_addon_active('commission-levels'),
        'ajax_url' => admin_url('admin-ajax.php'),
        'delete_are_you_sure' => __('Are you sure you want to delete this transaction?', 'easy-affiliate'),
        'delete_transaction_nonce' => wp_create_nonce('esaf_delete_transaction'),
        'error_deleting_transaction' => __('An error occurred deleting the transaction', 'easy-affiliate'),
      ];

      wp_localize_script(
        'esaf-admin-transactions',
        'EsafTransactions',
        ['l10n_print_after' => 'EsafTransactions = ' . wp_json_encode($l10n)]
      );
    }
  }

  public static function route() {
    if(strtolower($_SERVER['REQUEST_METHOD'])=='post') {
      if(isset($_POST['id']) and is_numeric($_POST['id'])) {
        self::update((int) $_POST['id']);
      }
      else {
        self::create();
      }
    }
    else {
      if(isset($_GET['action']) and strtolower($_GET['action'])=='new') {
        self::display_new();
      }
      elseif(isset($_GET['action'], $_GET['id']) and strtolower($_GET['action'])=='edit') {
        self::display_edit($_GET['id']);
      }
      else {
        self::display_list();
      }
    }
  }

  public static function display_list() {
    $list_table = new TransactionsTable();
    $list_table->prepare_items();

    require ESAF_VIEWS_PATH . '/transactions/list.php';
  }

  public static function display_new($message = '', $errors = [], $values = []) {
    $options = Options::fetch();
    $txn = new Transaction();
    $txn->load_from_sanitized_array($values);
    $referrer = (isset($_REQUEST['referrer']) ? sanitize_user(wp_unslash($_REQUEST['referrer'])) : '');
    require ESAF_VIEWS_PATH . '/transactions/new.php';
  }

  public static function display_edit($id, $message = '', $errors = [], $values = []) {
    $txn = Transaction::get_one($id);

    if($txn) {
      $txn->load_from_sanitized_array($values);
      $robj = new User($txn->affiliate_id);
      $options = Options::fetch();
      $referrer = (isset($_REQUEST['referrer']) ? sanitize_user(wp_unslash($_REQUEST['referrer'])) : $robj->user_login);
      $commissions = Commission::get_all_by_transaction_id($id, 'commission_level');
      require ESAF_VIEWS_PATH . '/transactions/edit.php';
    }
    else {
      $errors = [__('Transaction not found', 'easy-affiliate')];
      require ESAF_VIEWS_PATH . '/shared/errors.php';
    }
  }

  /**
   * Sanitize the given transaction values
   *
   * @param   array  $values
   * @return  array
   */
  private static function sanitize($values) {
    $values['referrer'] = isset($values['referrer']) && is_string($values['referrer']) ? sanitize_user($values['referrer']) : '';
    $values['_wafp_transaction_cust_name'] = isset($values['_wafp_transaction_cust_name']) && is_string($values['_wafp_transaction_cust_name']) ? sanitize_text_field($values['_wafp_transaction_cust_name']) : '';
    $values['_wafp_transaction_cust_email'] = isset($values['_wafp_transaction_cust_email']) && is_string($values['_wafp_transaction_cust_email']) ? sanitize_text_field($values['_wafp_transaction_cust_email']) : '';
    $values['_wafp_transaction_item_name'] = isset($values['_wafp_transaction_item_name']) && is_string($values['_wafp_transaction_item_name']) ? sanitize_text_field($values['_wafp_transaction_item_name']) : '';
    $values['_wafp_transaction_trans_num'] = isset($values['_wafp_transaction_trans_num']) && is_string($values['_wafp_transaction_trans_num']) ? sanitize_text_field($values['_wafp_transaction_trans_num']) : '';
    $values['_wafp_transaction_source'] = isset($values['_wafp_transaction_source']) && is_string($values['_wafp_transaction_source']) ? sanitize_key($values['_wafp_transaction_source']) : 'general';
    $values['_wafp_transaction_flag'] =
      isset($values['_wafp_transaction_flag'])
      && is_string($values['_wafp_transaction_flag'])
      && in_array($values['_wafp_transaction_flag'], ['own-referral', 'conversion-rate'])
        ? sanitize_key($values['_wafp_transaction_flag']) : '';
    $values['_wafp_transaction_sale_amount'] = isset($values['_wafp_transaction_sale_amount']) && is_numeric($values['_wafp_transaction_sale_amount']) ? Utils::format_float($values['_wafp_transaction_sale_amount']) : '0.00';
    $values['_wafp_transaction_refund_amount'] = isset($values['_wafp_transaction_refund_amount']) && is_numeric($values['_wafp_transaction_refund_amount']) ? Utils::format_float($values['_wafp_transaction_refund_amount']) : '0.00';
    $values['commissions'] = isset($values['commissions']) && is_array($values['commissions']) ? self::sanitize_commissions($values['commissions']) : [];
    $values['new_commissions'] = isset($values['new_commissions']) && is_array($values['new_commissions']) ? self::sanitize_new_commissions($values['new_commissions']) : [];

    return $values;
  }

  /**
   * Sanitize the given commissions array
   *
   * @param   array  $commissions
   * @return  array
   */
  private static function sanitize_commissions(array $commissions) {
    $sanitized = [];

    foreach ($commissions as $id => $commission) {
      if (is_numeric($id) && $id > 0 && is_array($commission)) {
        $sanitized[$id] = [
          'commission_level' => isset($commission['commission_level']) && is_numeric($commission['commission_level']) ? (int) $commission['commission_level'] : 0,
          'referrer' => isset($commission['referrer']) && is_string($commission['referrer']) ? sanitize_user($commission['referrer']) : '',
          'commission_type' => isset($commission['commission_type']) && is_string($commission['commission_type']) && in_array($commission['commission_type'], array('percentage', 'fixed')) ? $commission['commission_type'] : 'percentage',
          'commission_percentage' => isset($commission['commission_percentage']) && is_numeric($commission['commission_percentage']) ? Utils::format_float($commission['commission_percentage']) : ''
        ];
      }
    }

    return $sanitized;
  }

  /**
   * Sanitize the given new commissions array
   *
   * @param   array  $commissions
   * @return  array
   */
  private static function sanitize_new_commissions(array $commissions) {
    $sanitized = [];

    foreach (Utils::array_invert($commissions) as $key => $commission) {
      $sanitized['commission_level'][$key] = isset($commission['commission_level']) && is_numeric($commission['commission_level']) ? (int) $commission['commission_level'] : 0;
      $sanitized['referrer'][$key] = isset($commission['referrer']) && is_string($commission['referrer']) ? sanitize_user($commission['referrer']) : '';
      $sanitized['commission_type'][$key] = isset($commission['commission_type']) && is_string($commission['commission_type']) && in_array($commission['commission_type'], ['percentage', 'fixed']) ? $commission['commission_type'] : 'percentage';
      $sanitized['commission_percentage'][$key] = isset($commission['commission_percentage']) && is_numeric($commission['commission_percentage']) ? Utils::format_float($commission['commission_percentage']) : '';
    }

    return $sanitized;
  }

  public static function validate($values = []) {
    $errors = [];

    if(empty($values['referrer'])) {
      $errors[] = __('Affiliate referrer must not be empty', 'easy-affiliate');
    } else {
      $ex = username_exists($values['referrer']);

      if(empty($ex)) {
        $errors[] = __('Affiliate referrer must be a valid WordPress user', 'easy-affiliate');
      }
    }

    if (!empty($values['_wafp_transaction_cust_email']) && !Utils::is_email($values['_wafp_transaction_cust_email'])) {
      $errors[] = __('The customer email address is not valid', 'easy-affiliate');
    }

    if (empty($values['_wafp_transaction_item_name'])) {
      $errors[] = __('The product must not be empty', 'easy-affiliate');
    }

    if (empty($values['_wafp_transaction_trans_num'])) {
      $errors[] = __('The unique order ID must not be empty', 'easy-affiliate');
    }

    if (empty($values['_wafp_transaction_source'])) {
      $errors[] = __('The source must not be empty', 'easy-affiliate');
    }

    if ($values['_wafp_transaction_sale_amount'] < 0) {
      $errors[] = __('The sale amount cannot be less than zero', 'easy-affiliate');
    }

    if ($values['_wafp_transaction_refund_amount'] < 0) {
      $errors[] = __('The refund amount cannot be less than zero', 'easy-affiliate');
    }

    $levels = [];
    $commissions = [];

    if(count($values['commissions'])) {
      $commissions = array_values($values['commissions']);
    }

    if(count($values['new_commissions'])) {
      $new_commissions = Utils::array_invert($values['new_commissions']);
      $commissions = array_merge($commissions, $new_commissions);
    }

    foreach($commissions as $crec) {
      if(in_array($crec['commission_level'], $levels)) {
        $errors[] = __('Commission Levels within a transaction must be unique', 'easy-affiliate');
      }

      if($crec['commission_level'] < 1) {
        $errors[] = __('Commission level must be a number greater than zero', 'easy-affiliate');
      }

      if (empty($crec['referrer'])) {
        $errors[] = __('Commission affiliate referrer must not be empty', 'easy-affiliate');
      } else {
        $ex = username_exists($crec['referrer']);

        if(empty($ex)) {
          $errors[] = __('Commission affiliate referrer must be a valid WordPress user', 'easy-affiliate');
        }
      }

      if(empty($crec['commission_percentage'])) {
        $errors[] = __('Commission must not be empty', 'easy-affiliate');
      }

      if(!preg_match('!^\d+(\.\d{2})?$!', trim($crec['commission_percentage']))) {
        $errors[] = __('Commission must be formatted #.##', 'easy-affiliate');
      }

      $levels[] = $crec['commission_level'];
    }

    return $errors;
  }

  public static function create() {
    $values = self::sanitize(wp_unslash($_POST));
    $errors = self::validate($values);

    if(empty($errors)) {
      $aff = new User();
      $aff->load_user_data_by_login($values['referrer']);

      $transaction = new Transaction();
      $transaction->load_from_sanitized_array($values);
      $transaction->affiliate_id = $aff->ID;
      $transaction->apply_refund($values['_wafp_transaction_refund_amount']);

      $id = $transaction->store();

      if(is_wp_error($id)) {
        $errors[] = $id->get_error_message();
      }
    }

    if(empty($errors)) {
      self::display_edit($id, __('Your transaction was created successfully', 'easy-affiliate'));
    }
    else {
      self::display_new('', $errors, $values);
    }
  }

  public static function update($id) {
    $transaction = Transaction::get_one($id);
    $values = self::sanitize(wp_unslash($_POST));
    $errors = self::validate($values);

    if( empty($errors) ) {
      $aff = new User();
      $aff->load_user_data_by_login( $values['referrer'] );

      // Force the cookie here -- WHY? Blair W 5-25-2017

      $transaction->load_from_sanitized_array($values);
      $transaction->affiliate_id = $aff->ID;
      $transaction->apply_refund($values['_wafp_transaction_refund_amount']);

      // Update the commissions for this record
      foreach($values['commissions'] as $cid => $crec) {
        // Ensure that the transaction level affiliate_id is updated properly for the first commission
        if( $crec['commission_level'] <= 1 ) {
          $ref = $aff;
        }
        else {
          $ref = new User();
          $ref->load_user_data_by_login( $crec['referrer'] );
        }

        $commission = Commission::get_one($cid);
        if($commission) {
          $commission->affiliate_id = $ref->ID;
          $commission->transaction_id = $transaction->id;

          // stored in the database as zero based but displayed as 1 based
          $commission->commission_level = ($crec['commission_level']-1);

          $commission->commission_percentage = $crec['commission_percentage'];
          $commission->commission_amount = self::calculate_custom_commission(
            Utils::format_float($transaction->sale_amount),
            Utils::format_float($crec['commission_percentage']),
            $crec['commission_type']
          );
          $commission->commission_type = $crec['commission_type'];

          // Just in case there's been a change in the refund amount
          $commission->apply_refund($transaction->refund_amount);

          $commission->store();
        }
      }

      // If any new commissions have been added then create those here
      if (count($values['new_commissions'])) {
        $new_commissions = Utils::array_invert($values['new_commissions']);
        foreach($new_commissions as $crec) {
          $ref = new User();
          $ref->load_user_data_by_login( $crec['referrer'] );

          // Ensure that the transaction's affiliate id matches the first commission level's
          if(($crec['commission_level'] <= 1) && ($transaction->affiliate_id != $ref->ID)) {
            $transaction->affiliate_id = $ref->ID;
          }

          $commission = new Commission();
          $commission->affiliate_id = $ref->ID;
          $commission->transaction_id = $transaction->id;

          // stored in the database as zero based but displayed as 1 based
          $commission->commission_level = ($crec['commission_level']-1);

          $commission->commission_percentage = $crec['commission_percentage'];
          $commission->commission_amount = self::calculate_custom_commission(
            Utils::format_float($transaction->sale_amount),
            Utils::format_float($crec['commission_percentage']),
            $crec['commission_type']
          );

          $commission->commission_type = $crec['commission_type'];

          // Just in case there's been a change in the refund amount
          $commission->apply_refund($transaction->refund_amount);

          $commission->store();
        }
      }

      $transaction->store();

      self::display_edit($id, __('Your transaction was updated successfully', 'easy-affiliate'));
    }
    else {
      self::display_edit($id, '', $errors, $values);
    }
  }

  public static function calculate_custom_commission($sale_amount, $commission_percentage, $commission_type) {
    if($commission_type=='percentage') {
      return ($sale_amount * $commission_percentage / 100);
    }
    else {
      return $commission_percentage;
    }
  }

  public static function delete_transaction() {
    if(!Utils::is_post_request() || empty($_POST['transaction_id']) || !is_string($_POST['transaction_id']) || !is_numeric($_POST['transaction_id'])) {
      wp_send_json_error(__('Bad request', 'easy-affiliate'));
    }

    if(!Utils::is_logged_in_and_an_admin()) {
      wp_send_json_error(__('Sorry, you don\'t have permission to do this.', 'easy-affiliate'));
    }

    if(!check_ajax_referer('esaf_delete_transaction', false, false)) {
      wp_send_json_error(__('Security check failed.', 'easy-affiliate'));
    }

    $txn = new Transaction((int) $_POST['transaction_id']);

    if($txn->id > 0) {
      $txn->destroy();
    }

    wp_send_json_success();
  }

  public static function delete_commission() {
    if(!is_super_admin()) {
      die(__('You do not have access.', 'easy-affiliate'));
    }

    if(!isset($_POST['id']) || empty($_POST['id']) || !is_numeric($_POST['id'])) {
      die(__('Could not delete commission', 'easy-affiliate'));
    }

    $commission = new Commission((int) $_POST['id']);
    $commission->destroy();

    die('true'); //don't localize this string
  }

  /**
   * Get the HTML for the button for adding a new commission to a transaction
   */
  private static function get_add_commission_level_button_html() {
    ob_start();
    ?>
    <button type="button" class="button button-secondary"><i class="ea-icon ea-icon-plus"></i><?php esc_html_e('Add New Commission', 'easy-affiliate'); ?></button>
    <?php
    return ob_get_clean();
  }

  /**
   * Get the HTML for adding a new commission to a transaction
   *
   * @return string
   */
  private static function get_new_commission_form() {
    ob_start();

    include ESAF_VIEWS_PATH . '/transactions/new-commission.php';

    return ob_get_clean();
  }

  public static function process_bulk_actions() {
    if(empty($_GET['page']) || $_GET['page'] != 'easy-affiliate-transactions') {
      return;
    }

    if(!empty($_GET['_wp_http_referer'])) {
      wp_redirect(remove_query_arg(['_wp_http_referer', '_wpnonce'], wp_unslash($_SERVER['REQUEST_URI'])));
      exit;
    }
  }

  /**
   * Send the sale notification emails when a transaction is completed.
   *
   * @param \EasyAffiliate\Models\Event $event
   */
  public static function event_transaction_completed($event) {
    $transaction = $event->get_data();

    if(is_wp_error($transaction) || !$transaction instanceof Transaction) {
      return;
    }

    if(empty($transaction->affiliate_id) || !is_numeric($transaction->affiliate_id)) {
      return; // We need a valid affiliate to continue
    }

    $affiliate = $transaction->affiliate();
    $affiliates = $affiliate->get_ancestors(true);
    $commission_total = 0.00;

    foreach($affiliates as $level => $aff) {
      if($aff->is_affiliate) {
        $commission_total += Commission::calculate($level, $aff, $transaction);
      }
    }

    /* Prepare email variables */
    $variables = [
      'item_name' => $transaction->item_name,
      'trans_num' => $transaction->trans_num,
      'trans_type' => $transaction->type,
      'transaction_type' => empty($transaction->subscr_id) ? __('Payment', 'easy-affiliate') : __('Subscription Payment', 'easy-affiliate'),
      'payment_status' => $transaction->status,
      'remote_ip_addr' => empty($transaction->ip_addr) ? $_SERVER['REMOTE_ADDR'] : $transaction->ip_addr,
      'payment_amount_num' => $transaction->sale_amount,
      'payment_amount' => AppHelper::format_currency($transaction->sale_amount),
      'customer_name' => $transaction->cust_name,
      'customer_email' => $transaction->cust_email,
      'commission_total_num' => $commission_total,
      'commission_total' => AppHelper::format_currency($commission_total),
    ];

    Utils::send_admin_sale_notification($variables, $affiliate, $transaction);
    Utils::send_affiliate_sale_notifications($variables, $affiliates, $transaction);
  }

  /**
   * Add Screen Options to the Transactions list page
   *
   * @param \WP_Screen $screen
   */
  public static function add_transactions_screen_options($screen) {
    if($screen instanceof \WP_Screen && preg_match('/_page_easy-affiliate-transactions$/', $screen->id) && !isset($_GET['action'])) {
      add_screen_option('per_page', [
        'label' => esc_html__('Transactions per page', 'easy-affiliate'),
        'default' => 10,
        'option' => 'esaf_transactions_per_page'
      ]);

      add_filter("manage_{$screen->id}_columns", [TransactionsTable::class, 'get_column_headers']);
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
  public static function set_transactions_screen_options($keep, $option, $value) {
    if($option == 'esaf_transactions_per_page' && is_numeric($value)) {
      return Utils::clamp((int) $value, 1, 999);
    }

    return $keep;
  }
} //End class
