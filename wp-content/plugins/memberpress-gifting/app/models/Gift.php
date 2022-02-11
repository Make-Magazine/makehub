<?php
namespace memberpress\gifting\models;

if ( ! defined( 'ABSPATH' ) ) {
  die( 'You are not allowed to call this page directly.' );}

use memberpress\gifting as base,
  memberpress\gifting\lib as lib,
  memberpress\gifting\models as models;

class Gift extends lib\BaseModel {

  public static $meta_table   = 'transaction_meta';
  public $attrs = array();
  public $transaction = null;

  // Strings
  public static $is_gift_complete_str = '_is_gift_complete';
  public static $is_gift_pending_str = '_is_gift_pending';
  public static $status_str = '_gift_status';
  public static $coupon_id_str = '_gift_coupon_id';
  public static $gifter_id_str = '_gifter_id';
  public static $gifter_txn_str = '_gifter_txn';

  public static $unclaimed_str = 'unclaimed';
  public static $claimed_str  = 'claimed';

  public function __construct( $obj = null ) {
    $this->initialize(
      array(
        'coupon_id'   => array(
          'default' => 0,
          'type' => 'int',
        ),
        'status'   => array(
          'default' => self::$unclaimed_str,
          'type' => 'string',
        ),
        'is_gift_complete'   => array(
          'default' => 0,
          'type' => 'int',
        ),
        'is_gift_pending'   => array(
          'default' => 0,
          'type' => 'int',
        ),
      ),
      $obj
    );
  }

  public function initialize( $attrs, $obj = null ) {
    $this->rec = $this->get_defaults_from_attrs( $attrs );
    $this->attrs = $attrs;


    if ( ! is_null( $obj ) ) {

      if ( is_numeric( $obj ) && $obj > 0 ) {
        $this->transaction_id = $obj;
      }
      if ( is_object( $obj ) && ( $obj instanceof \MeprTransaction ) ) {
        $this->transaction_id = $obj->id;
      }

      $rec = array();
      $rec['transaction_id'] = $this->transaction_id;

      $txn = new \MeprTransaction( $this->transaction_id );

      $mepr_db = new \MeprDb();
      global $wpdb;
      $query = $wpdb->prepare( "SELECT `meta_key`, `meta_value` FROM {$mepr_db->transaction_meta} WHERE transaction_id = %s", $txn->id );
      $result = $wpdb->get_results($query, ARRAY_A);
      $metas = array();

      foreach ($result as $row) {
        $metas[$row['meta_key']] = array($row['meta_value']);
      }

      // Unserialize and set appropriately
      foreach( $this->attrs as $akey => $config ) {
        $rclass = new \ReflectionClass($this);
        // This requires that the static variable have the same name
        // as the attribute key with "_str" appended
        $rkey = $rclass->getStaticPropertyValue("{$akey}_str");
        if(isset($metas[$rkey])) {
          if(count($metas[$rkey]) > 1) {
            $rec[$akey] = array();
            foreach($metas[$rkey] as $skey => $sval)
              $rec[$akey][$skey] = maybe_unserialize($sval);
          }
          else {
            $mval = $metas[$rkey][0];
            if($mval==='' and strpos($config['type'],'bool')===0)
              $rec[$akey] = false;
            else
              $rec[$akey] = maybe_unserialize($mval);
          }
        }
      }
      $this->rec = (object)array_merge((array)$this->rec ,$rec);
    }
  }

  /**
   * Get transaction object
   *
   * @return [type]
   */
  public function transaction(){
    if($this->transaction_id > 0){
      return new \MeprTransaction($this->transaction_id);
    }
    return new \MeprTransaction();
  }

  /**
   * Checks if the product can be purchased as a gift
   * @param mixed $product
   *
   * @return [type]
   */
  public static function is_product_giftable( $product ) {
    $can_you_buy_me = self::can_you_buy_me($product);
    return $can_you_buy_me;
  }

  public static function is_gift_purchase_only($product){
    $can_you_buy_me = self::can_you_buy_me($product);
    $group = $product->group();

    $is_existing_user = lib\Utils::is_user_logged_in();
    if($is_existing_user) {
      $usr = \MeprUtils::get_currentuserinfo();
    }
    else { // If new user we've got to create them and sign them in
      $usr = new \MeprUser();
    }

    // If product does not allow simultaneous subscription, it's a gift purchase only
    // But if product belongs to a group don't show message
    if(
      $can_you_buy_me &&
      (!$product->simultaneous_subscriptions && $usr->is_already_subscribed_to($product->ID)) &&
      (!$group || ($group && $usr->is_already_subscribed_to($product->ID))) &&
      false == ($product->is_one_time_payment() && $product->allow_renewal) ) {
      return true;
    }

    return false;
  }

  /**
   * @param mixed $product
   *
   * @return [type]
   */
  public static function already_subscribed_to_group($product){
    $group = $product->group();
    if(\MeprUtils::is_user_logged_in()) {
      $usr = \MeprUtils::get_currentuserinfo();
    }
    else { // If new user we've got to create them and sign them in
      $usr = new \MeprUser();
    }

    if($group && $usr->is_already_subscribed_to($product->ID)){
      return true;
    }
  }

  /**
   * Checks if product can be purchased
   * @param mixed $product
   *
   * @return [type]
   */
  public static function can_you_buy_me($product) {
    global $user_ID;

    // Admins can see & purchase anything
    if(lib\Utils::is_logged_in_and_an_admin()) {
      return true;
    }

    if(lib\Utils::is_user_logged_in()) {
      $user = new \MeprUser($user_ID);
    }

    if ( $product->price <= 0.00 || 'on' !== $product->allow_gifting) {
      return false;
    }


    if(empty($product->who_can_purchase)) {
      return true; //No rules exist so everyone can purchase
    }


    foreach($product->who_can_purchase as $who) {
      if($who->user_type == 'disabled') {
        return false;
      }

      if($who->user_type == 'everyone') {
        return true;
      }

      if($who->user_type == 'guests' && !\MeprUtils::is_user_logged_in()) {
        return true; //If not a logged in member they can purchase
      }
    }

    return false; //If we make it here, nothing applied so let's return false
  }


  /**
   * Generates a First Payment Discount after a successful purchase
   *
   * @return [type]
   */
  public static function generate_coupon( $prd_id ) {
    $args = array(
      'post_title' => 'GIFT-' . \MeprUtils::random_string( 10, false, true ),
      'post_type' => \MeprCoupon::$cpt,
      'post_status' => 'publish',
    );
    $coupon_id = wp_insert_post( $args );

    $coupon = new \MeprCoupon( $coupon_id );

    $coupon->first_payment_discount_amount = 100;
    $coupon->first_payment_discount_type = 'percent';
    $coupon->discount_amount = 0;
    $coupon->discount_mode = 'first-payment';
    $coupon->valid_products = array( $prd_id );
    $coupon->usage_amount = 1;
    $coupon->store_meta();

    add_post_meta( $coupon->ID, models\Gift::$is_gift_complete_str, true );

    return $coupon;
  }

  /**
   * Checks whether a coupon is valid
   *
   * @param mixed $coupon
   * @param mixed $product
   *
   * @return bool
   */
  public static function is_valid_coupon( $coupon, $product_id ) {
    return apply_filters( 'mepr_gifting_is_valid_coupon', \MeprCoupon::is_valid_coupon_code( $coupon, $product_id ), $coupon, $product_id );
  }

  /**
   * Checks whether a coupon has the required meta
   *
   * @param mixed $coupon_id
   * @param mixed $product_id
   *
   * @return bool
   */
  public static function is_gift_coupon( $coupon_id, $product_id ) {
    $coupon_is_gift = get_post_meta( $coupon_id, models\Gift::$is_gift_complete_str, true );
    if ( ! $coupon_is_gift ) {
      return false;
    }

    return true;
  }

  /**
   * Search for gift transactions by user ID
   *
   * @param int $user_id
   *
   * @return [type]
   */
  public static function find_gifts_by_user_id( int $user_id ) {
    $mepr_db = \MeprDb::fetch();
    $txn_ids = $mepr_db->get_col(
      $mepr_db->{self::$meta_table},
      'transaction_id',
      array(
        'meta_key'    => models\Gift::$gifter_id_str,
        'meta_value'  => $user_id,
      )
    );

    return (array) $txn_ids;
  }


  /**
   * @param mixed $coupon_id
   *
   * @return [type]
   */
  public static function find_gifter_txn_by_coupon_id( $coupon_id ) {
    $mepr_db = \MeprDb::fetch();
    $txn_ids = $mepr_db->get_col(
      $mepr_db->{self::$meta_table},
      'transaction_id',
      array(
        'meta_key'    => models\Gift::$coupon_id_str,
        'meta_value'  => $coupon_id,
      )
    );

    if ( ! empty( $txn_ids ) ) {
      $txn = new \MeprTransaction( $txn_ids[0] );
      return $txn;
    }

    return new \MeprTransaction();
  }


  /**
   * @param mixed $txn
   * Stores gift complete meta
   * @return void
   */
  public static function store_meta($txn){
    $product = $txn->product();
    $coupon = self::generate_coupon($product->ID);
    $meta = array(
      self::$is_gift_complete_str  => true,
      self::$gifter_id_str         => $txn->user_id,
      self::$coupon_id_str         => $coupon->ID,
      self::$status_str            => self::$unclaimed_str
    );
    foreach ($meta as $key => $value) {
      $txn->add_meta($key, $value, true);
    }
  }


  /**
   * @param mixed $txn
   * Migrate meta from confirmed transaction to complete
   * @return void
   */
  public static function migrate_meta($first_txn, $txn){
    $meta = array(
      self::$is_gift_complete_str  => $first_txn->get_meta(self::$is_gift_complete_str, true),
      self::$gifter_id_str         => $first_txn->get_meta(self::$gifter_id_str, true),
      self::$coupon_id_str         => $first_txn->get_meta(self::$coupon_id_str, true),
      self::$status_str            => $first_txn->get_meta(self::$status_str, true),
    );
    foreach ($meta as $key => $value) {
      $txn->add_meta($key, $value, true);
      $first_txn->delete_meta($key);
    }
  }


  /**
   * @param mixed $obj
   * @param mixed $key
   *
   * @return [type]
   */
  public function has_meta($obj, $key){
    return $obj->get_meta($key, true, true);
  }


  /**
   * Returns Gift Claim URL
   * @return string
   */
  public function claim_url($txn = null) {
    if(null == $txn){
      $txn = new \MeprTransaction($this->transaction_id);
    }
    $url = '';
    if ( $txn->id > 0 ) {
      $prd = $txn->product();
      $url = $prd->url();
      if($this->coupon_id > 0) {
        $coupon = new \MeprCoupon($this->coupon_id);
        $url = \add_query_arg( 'coupon', $coupon->post_title, $url );
      }
    }
    return esc_url( $url );
  }

  /**
   * Returns the gift coupon's title.
   * @return string
   */
  public function get_coupon() {
    $txn = new \MeprTransaction($this->transaction_id);

    if($txn->id > 0 && $this->coupon_id > 0) {
      $coupon = new \MeprCoupon($this->coupon_id);
      return $coupon->post_title;
    } else {
      return '';
    }
  }

  public function store( $validate = true ) {}

  /**
   * Removes the transaction's gift metadata.
   * @return void
   */
  public function destroy() {
    $txn = new \MeprTransaction($this->transaction_id);
    $meta = array(
      self::$is_gift_complete_str,
      self::$is_gift_pending_str,
      self::$gifter_id_str,
      self::$coupon_id_str,
      self::$status_str,
    );

    foreach($meta as $key) {
      $txn->delete_meta($key);
    }
  }
}
