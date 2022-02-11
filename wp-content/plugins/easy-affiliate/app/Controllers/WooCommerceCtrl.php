<?php

namespace EasyAffiliate\Controllers;

use EasyAffiliate\Lib\BaseCtrl;
use EasyAffiliate\Lib\Cookie;
use EasyAffiliate\Lib\Track;
use EasyAffiliate\Models\Options;
use EasyAffiliate\Models\Transaction;

/** Handles all the integration hooks into Woocommerce
  */

class WooCommerceCtrl extends BaseCtrl {
  public function load_hooks() {
    $options = Options::fetch();

    if(!in_array('woocommerce', $options->integration) || !self::is_plugin_active()) {
      return;
    }

    if($options->woocommerce_integration_order_status == 'processing') {
      add_action('woocommerce_order_status_processing', [self::class, 'track'], 10, 2);
    }
    else {
      add_action('woocommerce_order_status_completed', [self::class, 'track'], 10, 2);
    }

    add_action('woocommerce_checkout_update_order_meta', [self::class, 'setup']);
    add_action('__experimental_woocommerce_blocks_checkout_update_order_meta', [self::class, 'setup']);
    add_action('woocommerce_order_refunded', [self::class, 'order_refunded'], 10, 2);
    add_filter('esaf_transaction_source_label', [self::class, 'transaction_source_label'], 10, 2);
  }

  /**
   * Mark EA transaction as refunded if the order is refunded from WooCommerce UI
   *
   * @param $order_id
   * @param $refund_id
   */
  public static function order_refunded($order_id, $refund_id) {
    $order = wc_get_order($order_id);
    $refund = wc_get_order($refund_id);

    if($order instanceof \WC_Order && $refund instanceof \WC_Order_Refund) {
      $items = $refund->get_items();
      $order_key = method_exists($order, 'get_order_key') ? $order->get_order_key() : $order->order_key;

      if(is_array($items)) {
        foreach($items as $item) {
          if($item instanceof \WC_Order_Item) {
            $trans_num = $order_key . '-' . $item->get_meta('_refunded_item_id');
            $transaction = Transaction::get_one(['trans_num' => $trans_num]);

            if($transaction instanceof Transaction) {
              $transaction->apply_refund(abs($item->get_total()));
              $transaction->store();
            }
          }
        }
      }
    }
  }

  /**
   * Is WooCommerce active?
   *
   * @return bool
   */
  public static function is_plugin_active() {
    return class_exists('WooCommerce');
  }

  /**
   * Store the affiliate ID with a postmeta item associated with the WooCommerce Order
   *
   * @param int|\WC_Order $order_id
   */
  public static function setup($order_id) {
    $order = wc_get_order($order_id);

    if(!$order instanceof \WC_Order) {
      return;
    }

    $order_id = $order->get_id();

    // Don't track admin users
    if(is_super_admin()) {
      return;
    }

    // When the order is created record the affiliate id in some post meta ...
    if(Cookie::get_affiliate_id() > 0) {
      $existing_affiliate = get_post_meta($order_id, 'ar_affiliate', true);

      // Don't override an existing affiliate
      if(!$existing_affiliate) {
        update_post_meta($order_id, 'ar_affiliate', Cookie::get_affiliate_id());
        update_post_meta($order_id, '_esaf_click_id', Cookie::get_click_id());
      }
    }
  }

  /**
   * Tracks commissions for an order
   *
   * @param int $order_id
   * @param \WC_Order $order
   */
  public static function track($order_id, $order) {
    $order_key = method_exists($order, 'get_order_key') ? $order->get_order_key() : $order->order_key;
    $user = $order->get_user();

    // Don't use the admins cookie ever! We'll get the real cookie below
    Cookie::clear();

    // Set the cookie from what's stored in the post meta for this Order
    if($affiliate_id = get_post_meta($order->get_id(), 'ar_affiliate', true)) {
      $click_id = (int) get_post_meta($order->get_id(), '_esaf_click_id', true);
      Cookie::override($affiliate_id, $click_id);
    }

    $coupon = '';
    if(method_exists($order, 'get_coupon_codes')) {
      $coupon = join(', ', $order->get_coupon_codes());
    }

    // Track each item in the order
    foreach($order->get_items() as $item) {
      if($item instanceof \WC_Order_Item_Product) {
        $product = $item->get_product();

        if(class_exists('WC_Subscription') && $product instanceof \WC_Product_Subscription) {
          list($sub_num, $sub_paynum) = self::get_subscription_data($order, $product);
        }
        else {
          $sub_num = '';
          $sub_paynum = 0;
        }

        Track::sale(
          'woocommerce',
          $item->get_total(),
          $order_key . '-' . $item->get_id(),
          $product ? $product->get_id() : null,
          $product ? $product->get_name() : null,
          $order->get_id(),
          $coupon,
          ($user !== false ? $user->ID : null),
          $sub_num,
          $sub_paynum
        );
      }
    }
  }

  /**
   * Get the subscription number and payment count
   *
   * @param \WC_Order $order
   * @param \WC_Product_Subscription $product
   * @return array
   */
  private static function get_subscription_data($order, $product) {
    $subscriptions = wcs_get_subscriptions_for_order($order, ['product_id' => $product->get_id(), 'order_type' => 'any']);

    foreach($subscriptions as $subscription) {
      if(method_exists($subscription, 'get_order_key')) {
        return [$subscription->get_order_key() . '-' . $product->get_id(), $subscription->get_payment_count()];
      }
    }

    return ['', 0];
  }

  /**
   * Link the transaction source label to the WC Order if applicable
   *
   * @param  string    $label The original transaction source label (already escaped)
   * @param  \stdClass $rec   The transaction rec object
   * @return string
   */
  public static function transaction_source_label($label, $rec) {
    $source = isset($rec->source) && is_string($rec->source) ? $rec->source : '';
    $order_id = isset($rec->order_id) && is_numeric($rec->order_id) ? (int) $rec->order_id : 0;

    if($source == 'woocommerce' && $order_id && function_exists('WC')) {
      $label = sprintf(
        '<a href="%s">%s</a>',
        esc_url(admin_url("post.php?post={$order_id}&action=edit")),
        $label
      );
    }

    return $label;
  }
} //End class
