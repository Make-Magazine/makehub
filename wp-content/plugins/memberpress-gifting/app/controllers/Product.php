<?php
namespace memberpress\gifting\controllers;

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

use memberpress\gifting as base;
use memberpress\gifting\lib as lib;
use memberpress\gifting\helpers as helpers;
use memberpress\gifting\controllers\admin as ctrl;
use memberpress\gifting\models as models;

class Product extends lib\BaseCtrl {
  public function load_hooks() {
    add_action('mepr-product-registration-metabox', array($this, 'gift_enable_field'));
    add_action('mepr-membership-save-meta', array($this, 'save_gift_enable_field'));
    add_filter('mepr-get-model-attribute-allow_gifting', array($this, 'get_gift_enable_field'), 10, 2 );
    add_filter('mepr_adjusted_price', array($this, 'gift_product_is_free'), 10, 3 );
    add_filter('mepr_subscription_product', array($this, 'remove_gift_from_group'), 10, 2 );
    add_filter('mepr_transaction_product', array($this, 'remove_gift_from_group'), 10, 2 );
    add_filter('mepr_transaction_product', array($this, 'gift_product_one_time_payment'), 10, 2 );
    add_filter('mepr_product_is_one_time_payment', array($this, 'override_product_one_time_payment'));
  }


  /**
   * Remove gift subscription/transaction from upgrade group
   * @param mixed $product
   * @param mixed $object
   *
   * @return object
   */
  public function remove_gift_from_group($product, $object){
    if($object instanceof \MeprSubscription) {
      if($object->get_meta(models\Gift::$is_gift_complete_str, true)){
        $product->group_id = false;
      }
    }
    elseif($object instanceof \MeprTransaction) {
      if($object->get_meta(models\Gift::$is_gift_complete_str, true)  || $object->get_meta(models\Gift::$is_gift_pending_str, true) ){
        $product->group_id = false;
      }
    }

    return $product;
  }

  /**
   * Make gift products one-time-payment products
   * @param mixed $product
   * @param mixed $txn
   *
   * @return [type]
   */
  public function gift_product_one_time_payment($product, $txn){
    if(
      $txn->get_meta(models\Gift::$is_gift_complete_str, true) ||
      $txn->get_meta(models\Gift::$is_gift_pending_str, true) ||
      (isset($_POST['mpgft-signup-gift-checkbox']) && "on" == $_POST['mpgft-signup-gift-checkbox'])
    ){
      $product->period_type = 'lifetime';
    }
    return $product;
  }

  /**
   * Set the product to be a one-time payment when the gift checkbox is checked
   *
   * @param bool $is_one_time_payment
   * @return bool
   */
  public function override_product_one_time_payment($is_one_time_payment) {
    if(isset($_POST['mpgft-signup-gift-checkbox']) && "on" == $_POST['mpgft-signup-gift-checkbox']) {
      $is_one_time_payment = true;
    }
    elseif(isset($_POST['mpgft_gift_checkbox']) && "true" == $_POST['mpgft_gift_checkbox']) {
      $is_one_time_payment = true;
    }

    return $is_one_time_payment;
  }

  /**
   * Adjust product price
   *
   * @param mixed $price
   * @param mixed $coupon
   * @param mixed $product
   *
   * @return [type]
   */
  public function gift_product_is_free($price, $coupon_code, $product){
    $coupon = \MeprCoupon::get_one_from_code($coupon_code);

    // Price is set to product price (no coupon, proration) when "Is this a Gift" checkbox is checked
    if(isset($_POST['mpgft-signup-gift-checkbox']) && "on" == $_POST['mpgft-signup-gift-checkbox']){
      if (empty($coupon)) {
        $price = $product->price;
      }
    }

    // Price is 0 when GIFT coupon is applied
    if(!empty($coupon) && models\Gift::is_valid_coupon($coupon_code, $product->ID) && models\Gift::is_gift_coupon($coupon->ID, $product->ID)){
      $price = 0.00;
    }

    return $price;
  }

  /**
   * Add Gift allow checkbox to product page in the admin
   * @param mixed $product
   *
   * @return [type]
   */
  public function gift_enable_field($product){
    $allow_gifting = get_post_meta($product->ID, base\SLUG_KEY.'_allow_gifting', true);
    ?>
    <div id="mepr-product-allow-gifting-fields-wrap">
      <input type="checkbox" name="<?php echo base\SLUG_KEY.'_allow_gifting'; ?>" id="<?php echo base\SLUG_KEY.'_allow_gifting'; ?>" <?php checked($product->allow_gifting, 'on'); ?> />
      <label for="<?php echo base\SLUG_KEY.'_allow_gifting'; ?>"><?php _e('Allow this membership to be gifted', 'memberpress-gifting'); ?></label>
      <?php
        \MeprAppHelper::info_tooltip('mepr-product-disable-address-fields',
                                    __('Allow this membership to be gifted', 'memberpress-gifting'),
                                    __('When this box is checked, the Membership can be purchased as a gift for another member.', 'memberpress-gifting'));
      ?>
    </div>
    <?php
  }

  public function save_gift_enable_field($product){
    $product->allow_gifting = 'off';
    if(isset($_POST[base\SLUG_KEY.'_allow_gifting']) && 'on' == $_POST[base\SLUG_KEY.'_allow_gifting'] && $product->price > 0){
      $product->allow_gifting = 'on';
    }
    update_post_meta($product->ID, base\SLUG_KEY.'_allow_gifting', $product->allow_gifting);
  }

  public function get_gift_enable_field($value, $product){
    if($product instanceof \MeprProduct){
      $value = get_post_meta($product->ID, base\SLUG_KEY.'_allow_gifting', true);
    }
    return $value;
  }
}
