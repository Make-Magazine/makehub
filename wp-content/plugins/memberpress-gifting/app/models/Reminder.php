<?php
namespace memberpress\gifting\models;

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
use memberpress\gifting\lib as lib;
use memberpress\gifting\models as models;

class Reminder extends lib\BaseCptModel {

  public static $nonce_str    = 'mepr_reminders_nonce';
  public static $last_run_str = 'mepr_reminders_db_cleanup_last_run';

  public static $cpt = 'mp-reminder';

  public $trigger_intervals, $trigger_timings, $trigger_events, $event_actions;

  /*** Instance Methods ***/
  public function __construct($obj = null) {

    $this->custom_attr_keys = array(
      'trigger_length' => 'mepr_trigger_length',
      'trigger_interval' => 'mepr_trigger_interval',
      'trigger_timing' => 'mepr_trigger_timing',
      'trigger_event' => 'mepr_trigger_event',
      'filter_products' => '_mepr_reminder_filter_products_str',
      'products' => '_mepr_reminder_products',
      'emails' => '_mepr_emails',
    );

    $this->load_cpt(
      $obj,
      self::$cpt,
      array(
        'trigger_length'   => array('default' => 'days', 'type' => 'string'),
        'trigger_interval'   => array('default' => 'before', 'type' => 'string'),
        'trigger_event'   => array('default' => 'sub-expires', 'type' => 'string'),
        'trigger_timing'   => array('default' => 'before', 'type' => 'string'),
        'filter_products'   => array('default' => false, 'type' => 'bool'), //Send only for specific memberships?
        'products'   => array('default' => array(), 'type' => 'array'), //Empty array means ALL memberships
        'emails'   => array('default' => array(), 'type' => 'array'),
      )
    );

    $this->trigger_intervals = array('hours','days','weeks','months','years');
    $this->trigger_timings = array('before','after');
    $this->trigger_events = array(
      'gift-expires'
    );

    $this->event_actions = array();
    foreach($this->trigger_events as $e) {
      foreach($this->trigger_timings as $t) {
        $this->event_actions[] = "mepr-event-{$t}-{$e}-reminder";
      }
    }
  }

  public function validate() {
    $this->validate_is_numeric($this->trigger_length, 0, null, 'trigger_length');
    $this->validate_is_in_array($this->trigger_interval, $this->trigger_intervals, 'trigger_interval');
    $this->validate_is_in_array($this->trigger_timing, $this->trigger_timings, 'trigger_timings');
    $this->validate_is_in_array($this->trigger_event, $this->trigger_events, 'trigger_events');
    $this->validate_is_bool($this->filter_products, 'filter_products');
    $this->validate_is_array($this->products, 'products');
    $this->validate_is_array($this->emails, 'emails');
  }

  public function events() {
  }

  public function trigger_event_name() {
    switch ($this->trigger_event) {
      case 'gift-expires': return __('Gifted Membership Expires', 'memberpress-gifting');
      default: return $this->trigger_event;
    }
  }

  public function get_trigger_interval_str() {
    return \MeprUtils::period_type_name( $this->trigger_interval, $this->trigger_length );
  }

  public function store_meta() {
    global $wpdb;
    $skip_name_override = false;
    $id = $this->ID;

    if(isset($_POST["post_title"]) && !empty($_POST["post_title"])) {
      $skip_name_override = true;
    }

    if (!$skip_name_override) {
      $title = sprintf(__('%d %s %s %s', 'memberpress-gifting'),
        $this->trigger_length,
        strtolower($this->get_trigger_interval_str()),
        $this->trigger_timing,
        $this->trigger_event_name());

      // Direct SQL so we don't issue any actions / filters
      // in WP itself that could get us in an infinite loop
      $sql = "UPDATE {$wpdb->posts} SET post_title=%s WHERE ID=%d";
      $sql = $wpdb->prepare($sql, $title, $id);
      $wpdb->query($sql);
    }

    update_post_meta( $id, self::$trigger_length_str,   $this->trigger_length );
    update_post_meta( $id, self::$trigger_interval_str, $this->trigger_interval );
    update_post_meta( $id, self::$trigger_timing_str,   $this->trigger_timing );
    update_post_meta( $id, self::$trigger_event_str,    $this->trigger_event );
    update_post_meta( $id, self::$filter_products_str,  $this->filter_products );
    update_post_meta( $id, self::$products_str,         $this->products );
    update_post_meta( $id, self::$emails_str,           $this->emails );
  }

  // Singularize and capitalize
  private function db_trigger_interval() {
    return strtoupper( substr( $this->trigger_interval, 0, -1 ) );
  }

  public function get_formatted_products() {
    $formatted_array = array();

    if($this->filter_products && isset($this->products) && is_array($this->products) && !empty($this->products)) {
      foreach($this->products as $product_id) {
        $product = get_post($product_id);

        if(isset($product->post_title) && !empty($product->post_title)) {
          $formatted_array[] = $product->post_title;
        }
      }
    }
    else { //If empty, then All products
      $formatted_array[] = __("All Memberships", 'memberpress-gifting');
    }

    return $formatted_array;
  }

  public function get_query_products($join_name) {
    if($this->filter_products && is_array($this->products) && !empty($this->products)) {
      $product_ids = implode(',', $this->products);
      return "AND {$join_name} IN({$product_ids})";
    }

    return '';
  }

  // Used for Gift Membership Expiration Reminders
  public function get_next_expiring_gift_txn() {
    global $wpdb;
    $mepr_db = new \MeprDb();

    $unit = $this->db_trigger_interval();
    $op = ( $this->trigger_timing=='before' ? 'DATE_SUB' : 'DATE_ADD' );

    //Make sure we're only grabbing from valid product ID's for this reminder yo
    //If $this->products is empty, then we should send for all product_id's
    $and_products = $this->get_query_products('tr.product_id');

    $query = $wpdb->prepare(
      // Get all info about expiring transactions
      "SELECT tr.* FROM {$mepr_db->transactions} AS tr\n" .
      "LEFT JOIN {$mepr_db->transaction_meta} AS tr_meta ON tr.id=tr_meta.transaction_id \n" .

       // Lifetimes don't expire
       "WHERE tr.expires_at <> %s\n" .

         //Make sure only real users are grabbed
         "AND tr.user_id > 0\n" .

         // Make sure that only transactions that are
         // complete or (confirmed and in a free trial) get picked up
         "AND ( tr.status = %s
                OR ( tr.status = %s
                     AND ( SELECT sub.trial
                             FROM {$mepr_db->subscriptions} AS sub
                            WHERE sub.id = tr.subscription_id AND sub.trial_amount = 0.00 ) = 1 ) )\n" .

         // Determine if expiration is accurate based on the subscription
         // If sub_id is 0 then treat as expiration
         "AND ( tr.subscription_id = 0 OR
                     ( SELECT sub.status
                         FROM {$mepr_db->subscriptions} AS sub
                        WHERE sub.id = tr.subscription_id ) IN (%s, %s) )\n" .

         "AND ( SELECT tr_meta.meta_value
                  FROM {$mepr_db->transaction_meta} AS tr_meta
                WHERE tr_meta.transaction_id = tr.id AND tr_meta.meta_key = %s LIMIT 1 ) > 0 \n" .

         // Ensure that we're in the 2 day window after the expiration / trigger
         "AND {$op}( tr.expires_at, INTERVAL {$this->trigger_length} {$unit} ) <= %s
          AND DATE_ADD(
                {$op}( tr.expires_at, INTERVAL {$this->trigger_length} {$unit} ),
                INTERVAL 2 DAY
              ) >= %s\n" .

         // Make sure that if our timing is beforehand
         // then we don't send after the expiration
         ( $this->trigger_timing=='before' ? $wpdb->prepare("AND tr.expires_at >= %s\n", \MeprUtils::db_now()) : '' ) .

         // Let's make sure the reminder event hasn't already fired ...
         // This will ensure that we don't send a second reminder
         "AND ( SELECT ev.id
                  FROM {$mepr_db->events} AS ev
                 WHERE ev.evt_id=tr.id
                   AND ev.evt_id_type='transactions'
                   AND ev.event=%s
                   AND ev.args=%d
                 LIMIT 1 ) IS NULL\n" .

         // Let's make sure we're not sending expire reminders
         // when your subscription is being upgraded or downgraded
         "AND ( SELECT ev2.id
                  FROM {$mepr_db->events} AS ev2
                 WHERE ev2.evt_id=tr.id
                   AND ev2.evt_id_type='transactions'
                   AND ev2.event='subscription-changed'
                 LIMIT 1 ) IS NULL\n" .

         // Let's make sure this is the latest transaction for the subscription
         // in case there is a more recent transaction that expires later
         "AND ( tr.subscription_id = 0
                OR tr.id = ( SELECT tr2.id
                             FROM {$mepr_db->transactions} AS tr2
                             WHERE tr2.subscription_id = tr.subscription_id
                             ORDER BY tr2.expires_at DESC
                             LIMIT 1 ) )\n" .

         "{$and_products} " .

       // We're just getting one of these at a time ... we need the oldest one first
       "ORDER BY tr.expires_at
        LIMIT 1\n",

      \MeprUtils::db_lifetime(),
      \MeprTransaction::$complete_str,
      \MeprTransaction::$confirmed_str,
      \MeprSubscription::$cancelled_str,
      \MeprSubscription::$suspended_str,
      models\Gift::$gifter_txn_str,
      \MeprUtils::db_now(),
      \MeprUtils::db_now(),
      "{$this->trigger_timing}-{$this->trigger_event}-reminder",
      $this->ID
    );

    $res = $wpdb->get_row($query);

    return $res;
  }

} //End class
