<?php

namespace EasyAffiliate\Models;

use EasyAffiliate\Helpers\AppHelper;
use EasyAffiliate\Lib\BaseModel;
use EasyAffiliate\Lib\Db;
use EasyAffiliate\Lib\Utils;

class Commission extends BaseModel {

  public $commission_types;

  public function __construct($obj = null) {
    $this->commission_types = [
      'percentage',
      'fixed'
    ];

    $this->initialize(
      [
        'id'                    => ['default' => 0,            'type' => 'integer'],
        'affiliate_id'          => ['default' => 0,            'type' => 'integer'],
        'transaction_id'        => ['default' => 0,            'type' => 'integer'],
        'commission_level'      => ['default' => 0,            'type' => 'integer'],
        'commission_percentage' => ['default' => null,         'type' => 'float'],
        'commission_type'       => ['default' => 'percentage', 'type' => 'string'],
        'commission_amount'     => ['default' => null,         'type' => 'float'],
        'correction_amount'     => ['default' => 0.00,         'type' => 'float'],
        'payment_id'            => ['default' => 0,            'type' => 'datetime'],
        'created_at'            => ['default' => null,         'type' => 'datetime'],
      ],
      $obj
    );
  }

  public function validate() {
    $this->validate_is_numeric($this->affiliate_id, 0, null, 'affiliate_id');
    $this->validate_is_numeric($this->transaction_id, 0, null, 'transaction_id');
    $this->validate_is_numeric($this->payment_id, 0, null, 'payment_id');
    $this->validate_is_numeric($this->commission_level, 0, null, 'commission_level');
    $this->validate_is_numeric($this->commission_percentage, 0.00, null, 'commission_percentage');
    $this->validate_is_in_array($this->commission_type, $this->commission_types, 'commission_type');
    $this->validate_is_numeric($this->commission_amount, 0.00, null, 'commission_amount');
    $this->validate_is_numeric($this->correction_amount, 0.00, null, 'correction_amount');
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

    if(isset($this->id) && !is_null($this->id) && ((int) $this->id > 0)) {
      $old_commission = new Commission($this->id);
      do_action('esaf_commission_pre_update', $this, $old_commission);
      $this->id = self::update($this);
      Event::record('commission-updated', $this);
    }
    else {
      $this->id = self::create($this);
      Event::record('commission-recorded', $this);
    }

    do_action('esaf_commission_store', $this);

    return $this->id;
  }

  public function destroy() {
    $db = Db::fetch();

    do_action('esaf_commission_destroy', $this);

    $res = $db->delete_records($db->commissions, ['id' => $this->id]);
    Event::record('commission-deleted', $this);

    return $res;
  }

  public function affiliate() {
    return (new User($this->affiliate_id));
  }

  public function transaction() {
    return (new Transaction($this->transaction_id));
  }

  /** STATIC CRUD METHODS **/
  public static function get_one($args) {
    return self::get_one_by_class(Commission::class, $args);
  }

  public static function get_all($order_by = '', $limit = '', $args = []) {
    return self::get_all_by_class(Commission::class, $order_by, $limit, $args);
  }

  public static function get_count($args = []) {
    return self::get_count_by_class(Commission::class, $args);
  }

  public static function create($commission) {
    $db = Db::fetch();
    $commission->determine_commission_type();
    return $db->create_record($db->commissions, (array) $commission->get_values());
  }

  public static function update($commission) {
    $db = Db::fetch();
    $commission->determine_commission_type();

    return $db->update_record($db->commissions, $commission->id, (array) $commission->get_values());
  }

  /* Just ensure this is a floating point number */
  public function __extend_model_set_commission_percentage($value) {
    return Utils::format_float($value);
  }

  /* Just ensure this is a floating point number */
  public function __extend_model_set_commission_amount($value) {
    return Utils::format_float($value);
  }

  /* Just ensure this is a floating point number */
  public function __extend_model_set_correction_amount($value) {
    return Utils::format_float($value);
  }

  public function determine_commission_type() {
    $options = Options::fetch();
    if( empty($this->commission_type) &&
        !empty($this->affiliate_id) &&
        !empty($this->transaction_id) &&
        is_numeric($this->affiliate_id) &&
        is_numeric($this->transaction_id) ) {
      $aff = $this->affiliate();
      $txn = $this->transaction();
      $this->commission_type = self::get_type($aff,$txn);
    }
    else if(empty($this->commission_type)) {
      $this->commission_type = $options->commission_type;
    }
  }

  public function apply_refund($refund_amount) {
    $this->correction_amount=0.00;

    if($this->commission_type=='percentage') {
      $this->correction_amount = ((float)$refund_amount * ((float)$this->commission_percentage / 100.0));
    }
    else if($this->commission_type=='fixed' && $refund_amount > 0) {
      $this->correction_amount = $this->commission_percentage; // Just void full commission
    }
  }

  public static function get_all_by_affiliate_id($affiliate_id, $order_by='', $limit='') {
    return self::get_all($order_by, $limit, compact('affiliate_id'));
  }

  public static function get_all_by_transaction_id($transaction_id, $order_by='', $limit='') {
    return self::get_all($order_by, $limit, compact('transaction_id'));
  }

  public static function destroy_all_by_transaction_id($transaction_id) {
    $commissions = Commission::get_all_by_transaction_id($transaction_id);

    if(!empty($commissions)) {
      foreach($commissions as $commission) {
        $commission->destroy();
      }
    }
  }

  public static function list_table(
    $order_by = '',
    $order = '',
    $paged = '',
    $search = '',
    $perpage = 10
  ) {
    $db = Db::fetch();
    global $wpdb;

    $cols = [
      'id' => 'commission.id',
      'commission_amount' => 'commission.commission_amount',
      'created_at' => 'commission.created_at',
      'final_amount' => '(commission.commission_amount - commission.correction_amount)',
    ];

    $from = "{$db->commissions} AS commission";
    $args = [];

    //We're not filtering by affiliate yet, maybe later
//    if( is_numeric($affiliate_id) and (int)$affiliate_id > 0 ) {
//      $args[] = $wpdb->prepare("commission.affiliate_id=%d", $affiliate_id);
//    }

    return Db::list_table($cols, $from, [], $args, $order_by, $order, $paged, $search, $perpage);
  }

  /** Record commissions for a given transaction */
  public static function record($transaction) {
    if(empty($transaction->affiliate_id) || !is_numeric($transaction->affiliate_id)) {
      return; // We need a valid affiliate to record commissions
    }

    $affiliate = $transaction->affiliate();
    $affiliates = $affiliate->get_ancestors(true);
    $commission_total = 0.00;

    // Record commission for each affiliate who's getting some
    foreach($affiliates as $level => $aff) {
      $curr_percentage = $curr_amount = 0.00;
      $curr_type = 'percentage';

      if($aff->is_affiliate) {
        $curr_percentage = self::get_percentage($level, $aff, $transaction);
        $curr_type = self::get_type($aff, $transaction);
        $curr_amount = self::calculate($level, $aff, $transaction);
        $commission_total += $curr_amount;
      }

      if((float)$curr_percentage > 0.00) {
        $commission = new Commission();
        $commission->affiliate_id = $aff->ID;
        $commission->transaction_id = $transaction->id;
        $commission->commission_level = $level;
        $commission->commission_percentage = $curr_percentage;
        $commission->commission_type = $curr_type;
        $commission->commission_amount = $curr_amount;
        $commission->store();
      }
    }
  }

  /** UTILITY METHODS FOR CALCULATING COMMISSIONS BASED ON AN affiliate AND transaction **/

  /** Figures out the commission type */
  public static function get_type($affiliate, $transaction = null) {
    $options = Options::fetch();

    return apply_filters(
      'esaf_commission_type',
      $options->commission_type,
      $affiliate,
      $transaction
    );
  }

  /** Commission levels this user is eligible to receive. */
  public static function get_levels($affiliate, $transaction = null) {
    $options = Options::fetch();

    return apply_filters(
      'esaf_commission_percentages',
      $options->commission,
      $affiliate,
      $transaction
    );
  }

  // How did this user become eligible for their current commission structure?
  public static function get_source($affiliate, $transaction = null) {
    $options = Options::fetch();

    $source = [
      'slug' => 'global',
      'label' => __('Global','easy-affiliate'),
      'object' => $options
    ];

    return apply_filters(
      'esaf_commission_source',
      $source,
      $affiliate,
      $transaction
    );
  }

  /** Calculates the commission percentage for the current user on the given level.
    * This can now either be an actual percentage ... or a fixed amount based on
    * what the user selected in the Easy Affiliate options.
    */
  public static function get_percentage($level, $affiliate, $transaction = null ) {
    $commissions = self::get_levels($affiliate, $transaction);

    if(!isset($commissions[$level])) {
      return false;
    }

    return (float) $commissions[$level];
  }

  /** Calculates the commission amount for the current user for the amount on a given level */
  public static function calculate($level, $affiliate, $transaction) {
    $commission_percentage = self::get_percentage($level, $affiliate, $transaction);
    $commission_type = self::get_type($affiliate, $transaction);
    $commission_source = self::get_source($affiliate, $transaction);

    if($commission_type == 'percentage') {
      $commission = (false !== $commission_percentage ? Utils::format_float((float)$transaction->sale_amount * $commission_percentage / 100.00) : false);
    }
    else if($commission_type == 'fixed') {
      $commission = (false !== $commission_percentage /* good place to check if amount is 0.00 ??? */ ? Utils::format_float($commission_percentage) : false);
    }

    return apply_filters('esaf_calculate_affiliate_commission', $commission, $level, $affiliate, $transaction);
  }

  /** Get commission percentages for the affiliates above the current user.
    * This is used when calculating percentages ... it gives an accurate commission
    * level for the current sale ... for all the affiliates who can get a commission. */
  public static function get_percentages($affiliate, $transaction = null, $compress_levels = false) {
    $commission_percentages = [];
    $affiliates = $affiliate->get_ancestors($compress_levels);
    $commissions = self::get_levels($affiliate, $transaction);

    foreach($affiliates as $level => $affiliate) {
      if(isset($commissions[$level])) {
        $commission_percentages[] = ($affiliate->is_affiliate ? (float) $commissions[$level] : 0.0);
      }
    }

    return $commission_percentages;
  }

  /** Get commission amounts for the affiliates above the current user given the total sale amount */
  public static function calculate_all($affiliate, $transaction, $compress_levels = false) {
    $commission_amounts = [];
    $affiliates = $affiliate->get_ancestors($compress_levels);

    foreach($affiliates as $level => $affiliate) {
      // Calculate the commission using the parent object ($this) rather than the sub-affiliate ...
      // this will ensure that overridden commission levels will still be accurate
      $commission_amount = ( $affiliate->is_affiliate ? self::calculate($level, $affiliate, $transaction) : 0.0 );

      if(false !== $commission_amount && !is_null($commission_amount) && is_numeric($commission_amount)) {
        $commission_amounts[] = $commission_amount;
      }
    }

    return $commission_amounts;
  }

  public static  function get_percentages_total($affiliate, $transaction = null, $compress_levels = false) {
    $percentages = self::get_percentages($affiliate, $transaction, $compress_levels);
    return (float) array_sum($percentages);
  }

  public static function calculate_total($affiliate, $transaction, $compress_levels = false) {
    $commissions = self::calculate_all($affiliate, $transaction, $compress_levels);
    return (float) array_sum($commissions);
  }

  public static function should_pay($affiliate, $transaction) {
    $options = Options::fetch();
    $pay_me = true;

    if($transaction->rebill) {
      $subscription_commissions = apply_filters(
        'esaf_subscription_commissions',
        $options->subscription_commissions,
        $affiliate,
        $transaction
      );

      $pay_me = $subscription_commissions != 'first-only';
    }

    return apply_filters('esaf_pay_commission', $pay_me, $affiliate, $transaction);
  }
}
