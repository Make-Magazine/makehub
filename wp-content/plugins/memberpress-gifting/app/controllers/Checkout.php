<?php
namespace memberpress\gifting\controllers;

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

use memberpress\gifting as base;
use memberpress\gifting\lib as lib;
use memberpress\gifting\models as models;
use memberpress\gifting\helpers as helpers;

class Checkout extends lib\BaseCtrl {

  public function load_hooks() {
    add_action('mepr-checkout-before-coupon-field', array($this, 'show_gift_signup_checkbox'));
    add_filter('mepr-thankyou-page-url', array($this, 'redirect_to_gift_tab'), 10, 2);
    add_action('mepr-txn-store', array($this, 'save_gift_pending_meta'), 10, 2);
    add_action('mepr-txn-store', array($this, 'create_gift'), 20, 2);
    add_action('mepr-txn-store', array($this, 'claim_gift'), 20);
    add_filter('mepr_display_invoice_sub', array($this, 'maybe_cancel_tmpsub_trial'), 10, 2 );
    add_filter('mepr_checkout_show_terms', array($this, 'hide_payment_terms'), 10, 2 );
    add_filter('mepr_signup_payment_required', array($this, 'hide_form_payment_methods'), 10, 2 );
    add_filter('mepr_signup_product_is_gift', array($this, 'signup_product_is_free'), 10, 2 );
    add_filter('maybe_show_broken_sub_message_override', array($this, 'subscription_message_override'), 10, 2 );
    add_filter('mepr-can-you-buy-me-override', array($this, 'gift_product_override'), 10, 2 );
    add_filter('mepr-can-you-buy-me-override', array($this, 'gifter_cannot_buy_their_gift'), 20, 2 );
    add_action('mepr-above-checkout-form',  array($this,  'show_gift_only_message'));
    add_filter('mepr_send_email_disable', array($this, 'maybe_disable_admin_recurring_email'), 10, 4 );
    add_filter('mepr-active-inactive-hooks-skip', array($this, 'handle_active_inactive_hooks'), 10, 2);
  }


  /**
   * Adds "Is this a gift?" checkbox to signup form
   * @param mixed $prd_id
   *
   * @return mixed
   */
  public function show_gift_signup_checkbox($prd_id){
    $prd    = new \MeprProduct($prd_id);
    $user   = \MeprUtils::get_currentuserinfo();

    static $unique_suffix = 0;
    $unique_suffix++;

    if ( isset( $_GET['coupon'] ) && models\Gift::is_valid_coupon( $_GET['coupon'], $prd_id ) ) {
      $coupon = \MeprCoupon::get_one_from_code( $_GET['coupon'] );

      // Dont show checkbox if there is a valid gift coupon
      if ( $coupon && models\Gift::is_gift_coupon( $coupon->ID, $prd_id ) ) {
        return false;
      }
    }

    if( models\Gift::is_product_giftable($prd) ){
      $force_checked = models\Gift::is_gift_purchase_only($prd);
      \ob_start();
        require(base\VIEWS_PATH . '/checkout/gift-signup-checkbox.php');
      $content = \ob_get_clean();
      echo $content;
    }
  }

  /**
   * If "Is this a gift?" is checked and transaction is pending, save transaction meta
   * Useful especially in 2-Page checkout
   *
   * @param mixed $new_txn
   * @param mixed $old_txn
   *
   * @return [type]
   */
  public function save_gift_pending_meta($txn, $old_txn) {
    if($txn->status !== \MeprTransaction::$pending_str || $old_txn->status !== \MeprTransaction::$pending_str){
      return;
    }

    $product = $txn->product();
    $gift_checkbox = self::get_param('mpgft-signup-gift-checkbox');
    if( "on" == $gift_checkbox && models\Gift::is_product_giftable($product) ){
      $txn->add_meta(models\Gift::$is_gift_pending_str, true, true);
    }
  }

  /**
   * If gift purchase transaction is completed/confirmed, create a gift
   *
   * @param mixed $txn
   * @param mixed $old_txn
   *
   * @return [type]
   */
  public function create_gift($txn, $old_txn) {
    $store = false;

    // Exit if transaction is pending or not valid
    if(($txn->status != \MeprTransaction::$complete_str && $txn->status != \MeprTransaction::$confirmed_str) || !self::is_valid_transaction($txn)) {
      return;
    }

    // Add meta to confirmed transaction with subscription
    if( $txn->get_meta(models\Gift::$is_gift_pending_str, true) && ($sub = $txn->subscription()) ){
      $sub->add_meta(models\Gift::$is_gift_complete_str, true, true);
      $store = true;
      \MeprTransaction::update_partial($txn->id, array('expires_at' => $txn->created_at)); // Disable subscription
    }

    // Add meta to completed transaction with subscription
    if($txn->status == \MeprTransaction::$complete_str && ($sub = $txn->subscription())){
      if($sub->get_meta(models\Gift::$is_gift_complete_str, true, true)){
        $first_txn = $sub->first_txn();
        if($first_txn->get_meta(models\Gift::$is_gift_pending_str, true) && $first_txn->status == \MeprTransaction::$confirmed_str ){
          models\Gift::migrate_meta($first_txn, $txn);
          \MeprTransaction::update_partial($txn->id, array('expires_at' => $txn->created_at)); // Disable subscription
        }
      }
    }

    // Add meta to one-time transactions
    if($txn->get_meta(models\Gift::$is_gift_pending_str, true) && $txn->status == \MeprTransaction::$complete_str){
      $store = true;

      \MeprTransaction::update_partial($txn->id, array('expires_at' => $txn->created_at)); // Disable subscription
    }

    if($store){
      models\Gift::store_meta($txn);
    }
  }

  /**
   * Adjust transactions and subscriptions when a gift is claimed
   * @param mixed $txn
   *
   * @return void
   */
  public function claim_gift($txn){

    if($txn->status != \MeprTransaction::$complete_str || $txn->gateway !== \MeprTransaction::$free_gateway_str) {
      return;
    }

    // Let's check if the coupon is a GIFT coupon
    if($txn->coupon_id <= 0){
      return;
    }

    $product = $txn->product();
    $coupon = new \MeprCoupon($txn->coupon_id);

    if( models\Gift::is_gift_coupon($txn->coupon_id, $product->ID) ){
      $gifter_txn = models\Gift::find_gifter_txn_by_coupon_id($coupon->ID);

      // Update transaction meta
      if($gifter_txn->id > 0){
        $gifter_txn->update_meta(models\Gift::$status_str, models\Gift::$claimed_str);
        $txn->add_meta(models\Gift::$gifter_txn_str, $gifter_txn->id);
      }

      // Set transaction expiration
      $expires_at_ts = $product->get_expires_at(strtotime($txn->created_at));
      if(is_null($expires_at_ts) || empty($expires_at_ts)) {
        $txn->expires_at = lib\Utils::db_lifetime();
      }
      else {
        $txn->expires_at = lib\Utils::ts_to_mysql_date($expires_at_ts, 'Y-m-d 23:59:59');
      }
      \MeprTransaction::update($txn);

      // Trash Coupon
      wp_trash_post($coupon->ID);
    }
  }

  /**
   * Checks to see if the transaction is valid
   * @param mixed $transaction
   *
   * @return [type]
   */
  public static function is_valid_transaction($transaction) {
    return (
      $transaction->txn_type == 'payment' ||
      $transaction->txn_type == 'subscription_confirmation'
    );
  }

  /**
   * Redirect successful gift purchase to the Gift Tab in the Account page
   *
   * @param mixed $thankyou_url
   * @param mixed $args
   *
   * @return string
   */
  public function redirect_to_gift_tab($thankyou_url, $args) {
    if (isset($args['transaction_id'])) {
      $txn = new \MeprTransaction($args['transaction_id']);
    } else {
      $txn = \MeprTransaction::get_one_by_trans_num($args['trans_num']);
      $txn = new \MeprTransaction($txn->id);
    }

    if($txn->get_meta(models\Gift::$is_gift_pending_str, true)){
      $mepr_options = \MeprOptions::fetch();
      $account_url = $mepr_options->account_page_url();

      $thankyou_url = \add_query_arg( array(
        'action' => 'gifts',
        'txn' => $txn->id,
      ), $account_url );
    }

    return $thankyou_url;
  }

  /**
   * If the Membership being gifted has a free or paid trial period,
   * the trial period should be ignored (dropped) when purchasing by the gifter. (AJAX Request)
   *
   * @param mixed $sub
   * @see MeprProductHelper::display_invoice()
   * @return object
   */
  public function maybe_cancel_tmpsub_trial($sub){
    $prd    = $sub->product();
    $user   = \MeprUtils::get_currentuserinfo();
    // If user is purchasing a gift, cancel trial
    if(isset($_POST['mpgft_gift_checkbox']) && "true" == $_POST['mpgft_gift_checkbox']){
      $coupon = $sub->coupon();
      $discount_mode = $coupon ? $coupon->discount_mode : null;

      if('first-payment' !== $discount_mode){
        $sub->trial = 0;
      }
    }

    return $sub;
  }



  /**
   * If payment is not required, that is, gift coupon is used, hide payment terms string
   * @param mixed $payment_required
   * @param mixed $product
   *
   * @return bool
   */
  public function hide_payment_terms($terms, $product){
    $terms = self::payment_not_required($terms, $product);
    return $terms;
  }

  /**
   * If payment is not required, that is, gift coupon is used, hide payment methods
   * @param mixed $payment_required
   * @param mixed $product
   *
   * @return bool
   */
  public function hide_form_payment_methods($payment_required, $product){
    $payment_required = self::payment_not_required($payment_required, $product);
    return $payment_required;
  }

  /**
   * Returns true if payment is not required
   * @param mixed $is_gift
   * @param mixed $product
   *
   * @see MeprCheckoutCtrl::update_price_string()
   * @return [type]
   */
  public function signup_product_is_free($is_gift, $product){
    $is_gift = self::payment_not_required($is_gift, $product);
    return $is_gift;
  }


  /**
   * Checks whether payment is required or not
   * @param mixed $terms
   * @param mixed $product
   *
   * @return [type]
   */
  public static function payment_not_required($terms, $product){

    if(isset($_REQUEST['coupon'])){
      $coupon = $_REQUEST['coupon'];
    }
    elseif(isset($_REQUEST['code'])){
      $coupon = $_REQUEST['code'];
    }
    elseif(isset($_REQUEST['mepr_coupon_code'])){
      $coupon = $_REQUEST['mepr_coupon_code'];
    }

    if(!isset($coupon) || empty($coupon)) return $terms;

    if(models\Gift::is_valid_coupon($coupon, $product->ID)
      && models\Gift::is_gift_coupon($coupon, $product->ID)
    ){
      return false;
    }

    return $terms;
  }

  /**
   * Prevent gifter from using his GIFT coupon
   * @param mixed $override
   * @param mixed $product
   *
   * @return [type]
   */
  public function gifter_cannot_buy_their_gift($override, $product){
    $user  = lib\Utils::get_currentuserinfo();
    if(!lib\Utils::is_logged_in_and_an_admin() && lib\Utils::is_user_logged_in() && isset($_GET['coupon']) && !empty($_GET['coupon'])){
      $coupon = \MeprCoupon::get_one_from_code($_GET['coupon']);
      if($coupon){
        $coupon_author_id = get_post_field( 'post_author', $coupon->ID );
        if($user->ID == $coupon_author_id){
          $override = false;
        }
      }
    }

    return $override;
  }

  /**
   * Make a product that would otherwise be unpurchasable purchasable because it's giftable
   * @param mixed $override
   * @param mixed $product
   *
   * @return [type]
   */
  public function gift_product_override($override, $product){
    if( models\Gift::is_product_giftable($product) ){
      $override = true;
    }
    return $override;
  }

  /**
   * Prevent double subscription to a group product
   * @param mixed $override default is true
   * @param mixed $product
   *
   * @return [type]
   */
  public function subscription_message_override($override, $product){
    $group = $product->group();

    if($group){
      if(models\Gift::is_gift_purchase_only($product) && models\Gift::already_subscribed_to_group($product)){
        $override = false;
      }
    }
    else{
      if(models\Gift::is_gift_purchase_only($product)){
        $override = false;
      }
    }

    return $override;
  }

  /**
   * @param mixed $prd_id
   *
   * @return [type]
   */
  public function show_gift_only_message( $prd_id ) {
    $notices = array();
    $prd     = new \MeprProduct( $prd_id );

    if ( models\Gift::is_gift_purchase_only( $prd ) ) {
      $notice = _x( 'Since you already have an active membership, we are assuming that this is a gift purchase.', 'ui', 'memberpress-gifting' );
      // if ( wp_http_validate_url( $prd->access_url ) ) {
      //   $notice .= sprintf( '%s <a href="%s">%s</a>', _x( ' If you do not intend to purchase a gift, you can', 'ui', 'memberpress-gifting' ), esc_url($prd->access_url), _x( 'access the membership here.', 'ui', 'memberpress-gifting' ) );
      // }
      $notices[] = $notice;

      require_once base\VIEWS_PATH . '/shared/notice.php';
    }
  }



  /**
   * Admin should not get recurring email about the subscription
   *
   * @param mixed $disable_email
   * @param mixed $obj
   * @param mixed $user_class
   * @param mixed $admin_class
   *
   * @return bool
   */
  public function maybe_disable_admin_recurring_email($disable_email, $obj, $user_class, $admin_class){
    if("MeprAdminNewSubEmail" == $admin_class && $obj->get_meta(models\Gift::$is_gift_complete_str, true)){
      return true;
    }

    return $disable_email;
  }

  /**
   * Don't allow active/inactive hooks to run if the product is a gift
   *
   * @param boolean $skip The return value.
   * @param \MeprTransaction $txn The Transaction object.
   *
   * @return boolean
   */
  public function handle_active_inactive_hooks($skip, $txn){
    if( $txn->get_meta(models\Gift::$is_gift_complete_str, true) ){
      return true;
    }
    return $skip;
  }


  // Utility function to grab the parameter whether it's a get or post
  public static function get_param($param, $default = '') {
    return (isset($_REQUEST[$param])?$_REQUEST[$param]:$default);
  }

}
