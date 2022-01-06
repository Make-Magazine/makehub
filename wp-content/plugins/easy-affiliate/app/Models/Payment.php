<?php

namespace EasyAffiliate\Models;

use EasyAffiliate\Lib\BaseModel;
use EasyAffiliate\Lib\Db;

class Payment extends BaseModel {

  public function __construct($obj = null) {
    $this->initialize(
      [
        'id'            => ['default' => 0,    'type' => 'integer'],
        'affiliate_id'  => ['default' => 0,    'type' => 'integer'],
        'amount'        => ['default' => 0.00, 'type' => 'float'],
        'payout_method' => ['default' => null, 'type' => 'string'],
        'batch_id'      => ['default' => null, 'type' => 'string'],
        'created_at'    => ['default' => null, 'type' => 'datetime'],
      ],
      $obj
    );
  }

  public function validate() {
    $this->validate_not_empty($this->affiliate_id, 'affiliate_id');
    $this->validate_is_numeric($this->affiliate_id, 0, null, 'affiliate_id');
    $this->validate_is_numeric($this->amount, 0.00, null, 'amount');
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
      $this->id = self::update($this);
      Event::record('payment-updated', $this);
    }
    else {
      $this->id = self::create($this);
      Event::record('payment-added', $this);
    }

    do_action('esaf_payment_store', $this);

    return $this->id;
  }

  public function destroy() {
    $db = Db::fetch();

    do_action('esaf_payment_destroy', $this);

    self::remove_payment_from_commission($this->id);

    $res = $db->delete_records($db->payments, ['id' => $this->id]);
    Event::record('payment-deleted', $this);

    return $res;
  }

  /** STATIC CRUD METHODS **/
  public static function get_one($args) {
    return self::get_one_by_class(Payment::class, $args);
  }

  public static function remove_payment_from_commission($payment_id) {
    global $wpdb;
    $db = Db::fetch();

    $query = $wpdb->prepare(
      "UPDATE `{$db->commissions}` SET `payment_id` = 0 WHERE `payment_id` = %d;",
      $payment_id
    );

    return $wpdb->query($query);
  }

  public static function get_all($order_by = '', $limit = '', $args = []) {
    return self::get_all_by_class(Payment::class, $order_by, $limit, $args);
  }

  public static function get_count($args = []) {
    return self::get_count_by_class(Payment::class, $args);
  }

  public static function create($payment) {
    $db = Db::fetch();

    $args = (array) $payment->get_values();
    unset($args['id']);

    return $db->create_record($db->payments, $args);
  }

  public static function update($payment) {
    $db = Db::fetch();
    return $db->update_record($db->payments, $payment->id, (array) $payment->get_values());
  }

  public static function update_transactions($payment_id, $affiliate_id, $period) {
    global $wpdb;
    $db = Db::fetch();

    $num_days_in_month = (int)(date( 't', $period )) - 1;
    $seconds_in_month  = 60*60*24*(int)$num_days_in_month;

    $day_start = date( 'Y-m-d 00:00:00', $period );
    $day_end   = date( 'Y-m-d 23:59:59', ( $period + $seconds_in_month ) );

    $query_str = "UPDATE {$db->commissions} SET payment_id=%d WHERE affiliate_id=%d AND payment_id=0 AND created_at <= %s";
    $query = $wpdb->prepare( $query_str, $payment_id, $affiliate_id, $day_end );

    return $wpdb->query($query);
  }

  public static function get_count_by_affiliate_id($affiliate_id) {
    return self::get_count(compact('affiliate_id'));
  }

  public static function get_all_by_affiliate_id($affiliate_id, $order_by = '', $limit = '') {
    return self::get_all($order_by, $limit, compact('affiliate_id'));
  }

  public static function get_all_ids_by_affiliate_id($affiliate_id, $order_by = '', $limit = '') {
    global $wpdb;
    $db = Db::fetch();
    $query = "SELECT id FROM {$db->payments} WHERE affiliate_id=%d {$order_by}{$limit}";
    $query = $wpdb->prepare($query, $affiliate_id);
    return $wpdb->get_col($query);
  }

  public static function affiliates_owed( $period ) {
    $db = Db::fetch();
    global $wpdb;

    $num_days_in_month = (int)(date( 't', $period )) - 1;
    $seconds_in_month  = 60*60*24*$num_days_in_month;

    $day_end   = date( 'Y-m-d 23:59:59', ( $period + $seconds_in_month ) );

    $query_select_clause = "
      SELECT aff.ID AS aff_id,
             aff.user_login AS aff_login,
             (
               SELECT SUM(co1.commission_amount)
               FROM {$db->commissions} co1
               LEFT JOIN {$db->transactions} txn1
               ON co1.transaction_id = txn1.id
               WHERE co1.affiliate_id = aff.ID
               AND co1.created_at <= %s
               AND txn1.status = 'complete'
             ) AS commission_amount,
             (
               SELECT SUM(co2.correction_amount)
               FROM {$db->commissions} co2
               LEFT JOIN {$db->transactions} txn2
               ON co2.transaction_id = txn2.id
               WHERE co2.affiliate_id = aff.ID
               AND co2.created_at <= %s
               AND txn2.status = 'complete'
             ) AS correction_amount,
             (
               SELECT SUM(amount)
               FROM {$db->payments}
               WHERE affiliate_id = aff.ID
             ) AS payment_amount
    ";

    $query_select_clause = $wpdb->prepare($query_select_clause, $day_end, $day_end);

    $query_select_clause = apply_filters('esaf_affiliates_owed_query_select_clause', $query_select_clause, $day_end);

    $query = $query_select_clause .
                 " FROM {$wpdb->users} aff
                   ORDER BY commission_amount DESC, aff_login";

    $results_array = $wpdb->get_results($query);

    $totals = [];
    $results = [];
    foreach( $results_array as $result ) {
      $total_amount = ($result->commission_amount - $result->correction_amount - $result->payment_amount);

      // if it doesn't round up to one cent then it's zero
      if((float) $total_amount >= 0.01) {
        $totals[$result->aff_id] = $total_amount;
        $results[$result->aff_id] = $result;
      }
    }

    arsort($totals); //changed from $totals_hash
    return compact( 'totals', 'results' );
  }

  /**
   * How much is the affiliate owed on the given date?
   *
   * @param  int                $affiliate_id The affiliate ID
   * @param  \DateTimeInterface $date         The end period date and time for the calculation
   * @return float
   */
  public static function affiliate_owed_on_date($affiliate_id, \DateTimeInterface $date) {
    global $wpdb;
    $db = Db::fetch();
    $date = $date->format('Y-m-d H:i:s');

    $query = "
      SELECT (
               SELECT SUM(co1.commission_amount)
               FROM {$db->commissions} co1
               LEFT JOIN {$db->transactions} txn1
               ON co1.transaction_id = txn1.id
               WHERE co1.affiliate_id = %d
               AND co1.created_at <= %s
               AND txn1.status = 'complete'
             ) AS commission_amount,
             (
               SELECT SUM(co2.correction_amount)
               FROM {$db->commissions} co2
               LEFT JOIN {$db->transactions} txn2
               ON co2.transaction_id = txn2.id
               WHERE co2.affiliate_id = %d
               AND co2.created_at <= %s
               AND txn2.status = 'complete'
             ) AS correction_amount,
             (
               SELECT SUM(amount)
               FROM {$db->payments}
               WHERE affiliate_id = %d
             ) AS payment_amount
    ";

    $row = $wpdb->get_row($wpdb->prepare($query, $affiliate_id, $date, $affiliate_id, $date, $affiliate_id));

    if($row) {
      $owed = (float) ($row->commission_amount - $row->correction_amount - $row->payment_amount);

      if($owed >= 0.01) {
        return $owed;
      }
    }

    return 0.00;
  }

  /**
   * Has the affiliate been paid within the given period?
   *
   * @param  int                $affiliate_id The affiliate ID
   * @param  \DateTimeInterface $start        The start date of the period
   * @param  \DateTimeInterface $end          The end date of the period
   * @return bool
   */
  public static function affiliate_paid_in_period($affiliate_id, \DateTimeInterface $start, \DateTimeInterface $end) {
    global $wpdb;
    $db = Db::fetch();

    $query = $wpdb->prepare(
      "SELECT id FROM {$db->payments} WHERE affiliate_id = %d AND created_at >= %s AND created_at <= %s",
      $affiliate_id,
      $start->format('Y-m-d H:i:s'),
      $end->format('Y-m-d H:i:s')
    );

    return $wpdb->get_var($query) !== null;
  }

  public static function list_table( $order_by = '',
                                     $order = '',
                                     $paged = '',
                                     $search = '',
                                     $perpage = 10 ) {
    $db = Db::fetch();
    global $wpdb;

    $cols = [
      'id' => 'pm.id',
      'affiliate_id' => 'pm.affiliate_id',
      'affiliate' => 'u.user_login',
      'created_at' => 'pm.created_at',
      'amount' => 'pm.amount',
      'sales_count' => "(SELECT COUNT(*) FROM {$db->commissions} co WHERE co.payment_id = pm.id)",
      'net_sales_amount' => "(SELECT SUM(txn.sale_amount - txn.refund_amount) FROM {$db->transactions} txn JOIN {$db->commissions} co2 ON txn.id = co2.transaction_id WHERE co2.payment_id = pm.id AND txn.status = 'complete')"
    ];

    $search_cols = [
      'u.user_login'
    ];

    $from = "{$db->payments} AS pm";
    $args = [];
    $joins = ["JOIN {$wpdb->users} AS u ON u.ID=pm.affiliate_id"];

    return Db::list_table($cols, $from, $joins, $args, $order_by, $order, $paged, $search, $perpage, false, $search_cols);
  }

}
