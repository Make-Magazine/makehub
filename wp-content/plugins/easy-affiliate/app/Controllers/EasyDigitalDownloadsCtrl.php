<?php

namespace EasyAffiliate\Controllers;

use EasyAffiliate\Lib\BaseCtrl;
use EasyAffiliate\Lib\Cookie;
use EasyAffiliate\Lib\Track;
use EasyAffiliate\Models\Options;
use EasyAffiliate\Models\Transaction;
use EasyAffiliate\Models\User;

/** Handles all the integration hooks into Easy Digital Downloads
  */

class EasyDigitalDownloadsCtrl extends BaseCtrl {

  public function load_hooks() {
    $options = Options::fetch();

    if(!in_array('easy_digital_downloads', $options->integration) || !self::is_plugin_active()) {
      return;
    }

    add_action('edd_insert_payment', [self::class, 'set_wafp_click_order_meta'], 10, 2);
    add_action('edd_update_payment_status', [self::class, 'track_order'], 10, 3);
    add_action('edd_update_payment_status', [self::class, 'track_refund'], 400, 3);
    add_filter('esaf_transaction_source_label', [self::class, 'transaction_source_label'], 10, 2);
  }

  /**
   * Is Easy Digital Downloads active?
   *
   * @return bool
   */
  public static function is_plugin_active() {
    return class_exists('Easy_Digital_Downloads');
  }

  public static function set_wafp_click_order_meta($payment_id, $status) {
    $user_id = (int) get_post_meta($payment_id, '_edd_payment_user_id', true);

    if(Cookie::get_affiliate_id() > 0) {
      if($user_id) {
        $user = new User($user_id);
        $user->referrer = Cookie::get_affiliate_id();
        $user->store();
      }

      // We're not guaranteed to have a user_id so lets store it in the post_meta for the order
      update_post_meta($payment_id, '_esaf_affiliate_id', Cookie::get_affiliate_id());
      update_post_meta($payment_id, '_esaf_click_id', Cookie::get_click_id());
    }
  }

  public static function track_refund($payment_id, $new_status, $old_status) {
    if( 'refunded' != $new_status ) {
      return;
    }

    $transactions = Transaction::get_all('', '', ['order_id' => $payment_id, 'source' => 'easy_digital_downloads']);
    foreach ($transactions as $transaction) {
      $transaction->apply_refund( $transaction->sale_amount );
      $transaction->store();
    }
  }

  public static function track_order($payment_id, $new_status, $old_status) {
    // Check if the payment was already set to complete
    if($old_status == 'publish' || $old_status == 'complete') {
      return;
    }

    // Make sure the receipt is only sent when new status is complete
    if($new_status != 'publish' && $new_status != 'complete') {
      return;
    }

    $payment = edd_get_payment($payment_id);

    if($payment instanceof \EDD_Payment) {
      $affiliate_id = (int) get_post_meta($payment_id, '_esaf_affiliate_id', true);
      $click_id = (int) get_post_meta($payment_id, '_esaf_click_id', true);
      $cart_items = edd_get_payment_meta_cart_details($payment->ID);

      if($affiliate_id && !empty($cart_items)) {
        Cookie::override($affiliate_id, $click_id);
        $coupon = !empty($payment->discounts) && is_string($payment->discounts) && $payment->discounts != 'none' ? $payment->discounts : null;

        foreach($cart_items as $index => $cart_item) {
          Track::sale(
            'easy_digital_downloads', /* source */
            $cart_item['price'], /* sale_amount */
            $payment->key . '-' . ($index + 1), /* trans_num */
            $cart_item['id'], /* item_id */
            $cart_item['name'], /* item_name */
            (int) $payment->ID, /* order_id */
            $coupon, /* coupon */
            (int) $payment->user_id, /* user_id */
            '', /* sub_num */
            0 /* sub_paynum */
          );
        }
      }
    }
  }

  /**
   * Link the transaction source label to the EDD Order if applicable
   *
   * @param  string    $label The original transaction source label (already escaped)
   * @param  \stdClass $rec   The transaction rec object
   * @return string
   */
  public static function transaction_source_label($label, $rec) {
    $source = isset($rec->source) && is_string($rec->source) ? $rec->source : '';
    $order_id = isset($rec->order_id) && is_numeric($rec->order_id) ? (int) $rec->order_id : 0;

    if($source == 'easy_digital_downloads' && $order_id && class_exists('Easy_Digital_Downloads')) {
      $label = sprintf(
        '<a href="%s">%s</a>',
        esc_url(admin_url("edit.php?post_type=download&page=edd-payment-history&view=view-order-details&id={$order_id}")),
        $label
      );
    }

    return $label;
  }
} //End class
