<?php

namespace EasyAffiliate\Models;

use EasyAffiliate\Lib\BaseModel;
use EasyAffiliate\Lib\Config;
use EasyAffiliate\Lib\Db;
use EasyAffiliate\Lib\Utils;

class Transaction extends BaseModel {
  public $transaction_types, $transaction_statuses, $transaction_sources;

  public function __construct($obj = null) {
    $this->transaction_types = [
      'commission',
      'no_commission'
    ];

    $this->transaction_statuses = [
      'pending',
      'complete',
      'refunded',
      'failed'
    ];

    $integrations = Config::get('integrations');

    if(is_wp_error($integrations)) {
      $this->transaction_sources = [];
    }
    else {
      $this->transaction_sources = array_keys($integrations);
    }

    $this->transaction_sources[] = 'general';

    $attrs = apply_filters('esaf_transaction_attrs', [
      'id'            => ['default' => 0,            'type' => 'integer'],
      'affiliate_id'  => ['default' => 0,            'type' => 'integer'],
      'click_id'      => ['default' => 0,            'type' => 'integer'],
      'item_id'       => ['default' => '',           'type' => 'string'],
      'item_name'     => ['default' => '',           'type' => 'string'],
      'coupon'        => ['default' => '',           'type' => 'string'],
      'sale_amount'   => ['default' => 0.00,         'type' => 'float'],
      'refund_amount' => ['default' => 0.00,         'type' => 'float'],
      'subscr_id'     => ['default' => '',           'type' => 'string'],
      'subscr_paynum' => ['default' => 0,            'type' => 'integer'],
      'ip_addr'       => ['default' => '',           'type' => 'string'],
      'cust_email'    => ['default' => '',           'type' => 'string'],
      'cust_name'     => ['default' => '',           'type' => 'string'],
      'trans_num'     => ['default' => '',           'type' => 'string'],
      'type'          => ['default' => 'commission', 'type' => 'string'],
      'source'        => ['default' => 'general',    'type' => 'string'],
      'order_id'      => ['default' => 0,            'type' => 'integer'],
      'status'        => ['default' => 'complete',   'type' => 'string'],
      'rebill'        => ['default' => false,        'type' => 'boolean'],
      'created_at'    => ['default' => null,         'type' => 'datetime'],
    ]);

    $this->initialize($attrs, $obj);
  }

  public function validate() {
    if(!empty($this->ip_addr)) {
      $this->validate_is_ip_addr($this->ip_addr, 'ip_addr');
    }

    $this->validate_is_numeric($this->affiliate_id, 0, null, 'affiliate_id');
    $this->validate_is_numeric($this->click_id, 0, null, 'click_id');
    $this->validate_is_numeric($this->sale_amount, 0.00, null, 'sale_amount');
    $this->validate_is_numeric($this->refund_amount, 0.00, null, 'refund_amount');

    if(!empty($this->cust_email)) {
      $this->validate_is_email($this->cust_email, 'cust_email');
    }

    $this->validate_not_empty($this->trans_num, 'trans_num');
    $this->validate_is_in_array($this->type, $this->transaction_types, 'type');
    $this->validate_is_in_array($this->status, $this->transaction_statuses, 'status');
    $this->validate_is_in_array($this->source, $this->transaction_sources, 'source');
  }

  public function store($validate = true) {
    if($validate) {
      try {
        $this->validate();
      }
      catch(\Exception $e) {
        return new \WP_Error(get_class($e), $e->getMessage());
      }
    }

    if(isset($this->id) && !is_null($this->id) && (int)$this->id > 0) {
      $old_transaction = new Transaction($this->id);
      do_action('esaf_transaction_pre_update', $this, $old_transaction);
      $this->id = self::update($this);
      Event::record('transaction-updated', $this);
      $record_transaction_completed_event = $this->status == 'complete' && $old_transaction->status != 'complete';
    }
    else {
      // No longer store a transaction at all if it's not commissionable
      if($this->type == 'no_commission') {
        return false;
      }
      $this->id = self::create($this);
      Event::record('transaction-recorded', $this);
      $record_transaction_completed_event = $this->status == 'complete';
    }

    do_action('esaf_transaction_store', $this);

    if($this->type == 'no_commission') {
      Commission::destroy_all_by_transaction_id($this->id);
    }
    else {
      $commissions = Commission::get_all_by_transaction_id($this->id);

      foreach($commissions as $commission) {
        $commission->apply_refund($this->refund_amount);
        $commission->store();
      }
    }

    if($record_transaction_completed_event) {
      Event::record('transaction-completed', $this);
    }

    return $this->id;
  }

  public function destroy() {
    $db = Db::fetch();
    do_action('esaf_transaction_destroy', $this);

    // TODO: Move this to the commissions controller/model to happen on action
    Commission::destroy_all_by_transaction_id($this->id);

    $res = $db->delete_records($db->transactions, ['id' => $this->id]);
    Event::record('transaction-deleted', $this);

    return $res;
  }

  public function __extend_model_set_sale_amount($value) {
    return Utils::format_float($value);
  }

  public function __extend_model_set_refund_amount($value) {
    //make sure we can't enter a negative or non-numeric value
    if($value <= 0 || !is_numeric($value)) {
      $value = 0;
    }

    return Utils::format_float($value);
  }

  public function affiliate() {
    return (new User($this->affiliate_id));
  }

  /**
   * Get the click for this transaction
   *
   * @return \EasyAffiliate\Models\Click
   */
  public function click() {
    return (new Click($this->click_id));
  }

  /** STATIC CRUD METHODS **/
  /**
   * @param $args
   *
   * @return self
   */
  public static function get_one($args) {
    return self::get_one_by_class(Transaction::class, $args);
  }

  /**
   * @param string $order_by
   * @param string $limit
   * @param array $args
   *
   * @return self[]
   */
  public static function get_all($order_by = '', $limit = '', $args = []) {
    return self::get_all_by_class(Transaction::class, $order_by, $limit, $args);
  }

  public static function get_count($args = []) {
    return self::get_count_by_class(Transaction::class, $args);
  }

  public static function create($transaction) {
    $db = Db::fetch();

    $transaction->id = $db->create_record($db->transactions, (array) $transaction->get_values());

    //Something broke
    if($transaction->id <= 0) {
      return;
    }

    Commission::record($transaction);

    return $transaction->id;
  }

  public static function update($transaction) {
    $db = Db::fetch();

    $db->update_record($db->transactions, $transaction->id, (array) $transaction->get_values());

    return $transaction->id;
  }

  public function apply_refund($refund_amount) {
    $this->refund_amount = $refund_amount;
  }

  public static function get_one_by_trans_num($trans_num) {
    return self::get_one(compact('trans_num'));
  }

  public static function get_one_by_subscription_id($subscr_id) {
    return self::get_one(compact('subscr_id'));
  }

  /**
   * Get the first transaction by subscription ID
   *
   * @param string $subscr_id
   * @return \EasyAffiliate\Models\Transaction|null
   */
  public static function get_first_by_subscription_id($subscr_id) {
    $transactions = self::get_all_by_subscription_id($subscr_id, 'created_at ASC', 1);

    if(isset($transactions[0]) && $transactions[0] instanceof self) {
      return $transactions[0];
    }

    return null;
  }

  public static function get_all_by_subscription_id($subscr_id, $order_by = '', $limit = '') {
    return self::get_all($order_by, $limit, compact('subscr_id'));
  }

  public static function get_all_by_affiliate_id($affiliate_id, $order_by = '', $limit = '') {
    return self::get_all($order_by, $limit, compact('affiliate_id'));
  }

  public static function get_search_count($search = '') {
    $db = Db::fetch();
    global $wpdb;
    $join = '';
    $where = '';

    if(!empty($search)) {
      $join  = " INNER JOIN {$wpdb->users} aff ON tr.affiliate_id=aff.id";
      $where = " AND ( aff.user_login LIKE '%{$search}%'" .
                      " OR tr.trans_num LIKE '%{$search}%'" .
                      " OR tr.sale_amount LIKE '%{$search}%'" .
                      " OR tr.refund_amount LIKE '%{$search}%'" .
                      " OR tr.status LIKE '%{$search}%'" .
                      " OR tr.source LIKE '%{$search}%'" .
                      " OR tr.item_id LIKE '%{$search}%'" .
                      " OR tr.item_name LIKE '%{$search}%'" .
                      " OR tr.coupon LIKE '%{$search}%'" .
                      " OR tr.created_at LIKE '%{$search}%' )";
    }

    $query = "SELECT COUNT(*) FROM {$db->transactions} tr{$join} WHERE tr.type='commission'{$where}";
    return $wpdb->get_var($query);
  }

  public static function get_count_by_affiliate_id($affiliate_id) {
    return self::get_count(compact('affiliate_id'));
  }

  public static function get_count_by_subscription_id($subscr_id) {
    return (int) self::get_count(compact('subscr_id'));
  }

  public static function list_table( $order_by = '',
                                     $order = '',
                                     $paged = '',
                                     $search = '',
                                     $perpage = 10,
                                     $include_pending = false ) {
    $db = Db::fetch();
    global $wpdb;

    $year = date('Y');
    $month = date('m');

    $cols = apply_filters('esaf_transaction_list_table_cols', [
      'id' => 'tr.id',
      'created_at' => 'tr.created_at',
      'user_login' => 'aff.user_login',
      'affiliate_id' => 'aff.ID',
      'trans_num' => 'tr.trans_num',
      'source' => 'tr.source',
      'order_id' => 'tr.order_id',
      'coupon' => 'tr.coupon',
      'item_name' => 'tr.item_name',
      'item_id' => 'tr.item_id',
      'sale_amount' => 'tr.sale_amount',
      'refund_amount' => 'tr.refund_amount',
      'commission_amount' => "(SELECT SUM(cm.commission_amount) - SUM(cm.correction_amount) FROM {$db->commissions} AS cm WHERE cm.transaction_id=tr.id)",
      'total_amount' => '(tr.sale_amount - tr.refund_amount)',
      'referring_page' => "(SELECT cl.referrer FROM {$db->clicks} cl WHERE cl.id=tr.click_id)"
    ]);

    $search_cols = [
      'tr.id',
      'aff.user_login',
      'tr.trans_num',
      'tr.source',
      'tr.coupon',
      'tr.item_name'
    ];

    $args = ["tr.type='commission'"];

    if(!$include_pending) {
      $args[] = "tr.status='complete'";
    }

    $joins = ["INNER JOIN {$wpdb->users} aff ON tr.affiliate_id=aff.id"];

    return Db::list_table($cols, "{$db->transactions} AS tr", $joins, $args, $order_by, $order, $paged, $search, $perpage, false, $search_cols);
  }
}
