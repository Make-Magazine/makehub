<?php

namespace EasyAffiliate\Controllers;

use EasyAffiliate\Lib\BaseCtrl;
use EasyAffiliate\Lib\Cookie;
use EasyAffiliate\Lib\Track;
use EasyAffiliate\Lib\Utils;
use EasyAffiliate\Models\Options;
use EasyAffiliate\Models\Transaction;
use EasyAffiliate\Models\User;
use MeprCoupon;
use MeprOptions;
use MeprProduct;
use MeprSubscription;
use MeprTransaction;
use MeprUser;

/** This is a special controller that handles all of the MemberPress specific
  * public static functions for the Affiliate Program.
  */
class MemberPressCtrl extends BaseCtrl {
  public function load_hooks() {
    $options = Options::fetch();

    if(!in_array('memberpress', $options->integration) || !self::is_plugin_active()) {
      return;
    }

    add_action('mepr-event-subscription-created', [self::class, 'add_subscription_meta']);
    add_action('mepr-event-offline-payment-pending', [self::class, 'add_transaction_meta']);
    add_action('mepr-txn-status-complete', [self::class, 'track_transaction']);
    add_action('mepr-txn-status-refunded', [self::class, 'refund_transaction']);

    // MemberPress Product Group Commission meta box integration
    add_action('mepr-product-meta-boxes', [self::class, 'product_meta_boxes']);
    add_action('mepr-product-save-meta', [self::class, 'save_product']);

    // MemberPress Product Group Commission calculations
    add_filter('esaf_commission_percentages', [self::class, 'commission_percentages'], 10, 2);
    add_filter('esaf_commission_type', [self::class, 'commission_type'], 10, 2);
    add_filter('esaf_commission_source', [self::class, 'commission_source'], 10, 2);
    add_filter('esaf_subscription_commissions', [self::class, 'subscription_commissions'], 10, 2);

    // Affiliate Based Coupons
    add_action('mepr-coupon-meta-boxes', [self::class, 'coupon_meta_boxes']);
    add_action('mepr-coupon-save-meta', [self::class, 'save_coupon']);
    add_action('mepr-coupon-admin-enqueue-script', [self::class, 'enqueue_coupon_scripts']);
    add_filter('esaf_dashboard_coupon_count', [self::class, 'coupon_count']);
    add_action('esaf_creatives_coupons', [self::class, 'display_my_coupons']);

    // Link the EA Transaction to the MP Transaction
    add_filter('esaf_transaction_source_label', [self::class, 'transaction_source_label'], 10, 2);
  }

  /**
   * Is MemberPress active?
   *
   * @return bool
   */
  public static function is_plugin_active() {
    return defined('MEPR_VERSION');
  }

  public static function add_subscription_meta($event) {
    $sub = $event->get_data();
    $affiliate_id = Cookie::get_affiliate_id();
    $click_id = Cookie::get_click_id();

    if($sub instanceof MeprSubscription && !is_super_admin()) {
      // Override the affiliate if a coupon was used that is associated with an affiliate
      if(($coupon = $sub->coupon()) && $coupon instanceof MeprCoupon) {
        $enabled = (isset($coupon->ID) && $coupon->ID) ? get_post_meta($coupon->ID, 'wafp_coupon_affiliate_enabled', true) : false;

        if($enabled && ($coupon_affiliate_id = get_post_meta($coupon->ID, 'wafp_coupon_affiliate', true))) {
          $affiliate_id = (int) $coupon_affiliate_id;
          $click_id = 0;
        }
      }

      if($affiliate_id > 0) {
        $sub->update_meta('_esaf_affiliate_id', $affiliate_id);
        $sub->update_meta('_esaf_click_id', $click_id);
      }
    }
  }

  /* Tracks when a transaction completes */
  public static function track_transaction($txn) {
    //Kill it if it's not a payment type
    if($txn->txn_type != MeprTransaction::$payment_str) {
      return;
    }

    // Check if we've already processed this transaction
    $existing_transaction = Transaction::get_one(['source' => 'memberpress', 'order_id' => $txn->id]);

    if($existing_transaction instanceof Transaction) {
      if($existing_transaction->trans_num != $txn->trans_num) {
        $existing_transaction->trans_num = $txn->trans_num;
        $existing_transaction->store();
      }

      return;
    }

    $subscr_id = '';
    $txn_count = 0;

    //If the admin is manually completing a txn or creating a new txn
    //we need to unset the cookie that may be in their browser so a false
    //commission doesn't get paid.
    if(is_super_admin() && Cookie::get_affiliate_id() > 0) {
      Cookie::clear();
    }

    // Track the coupon to an affiliate if a coupon exists and that coupon is tied to an affiliate
    self::track_coupon($txn);

    $sub = $txn->subscription();

    if($sub instanceof MeprSubscription) {
      $subscr_id = $sub->subscr_id;
      $txn_count = $sub->txn_count;

      // Override the affiliate to the one who referred the creation of the subscription
      // since the cookie is not present during a webhook/IPN request.
      $affiliate_id = (int) $sub->get_meta('_esaf_affiliate_id', true);

      if($affiliate_id > 0) {
        $click_id = (int) $sub->get_meta('_esaf_click_id', true);
        Cookie::override($affiliate_id, $click_id);
      }
    }
    else {
      // Check if this is an offline transaction and override the affiliate to the one who referred the creation of it
      // since the cookie is not present when the transaction is completed.
      $affiliate_id = (int) $txn->get_meta('_esaf_affiliate_id', true);

      if($affiliate_id > 0) {
        $click_id = (int) $txn->get_meta('_esaf_click_id', true);
        Cookie::override($affiliate_id, $click_id);
      }
    }

    // If we don't have an affiliate ID at this point, check the referrer usermeta for recurring commission
    // backwards compatibility.
    if(!(Cookie::get_affiliate_id() > 0)) {
      $customer = new User($txn->user_id);

      if($customer->ID > 0) {
        $referrer = (int) $customer->referrer;

        if($referrer > 0) {
          Cookie::override($referrer);
        }
      }
    }

    if($txn->amount > 0.00) {
      $prd = $txn->product();
      $_REQUEST['mepr_product_for_wafp'] = $prd; //Don't delete this $_REQUEST item - I use it down the line in wafp-calculate-commission filter for some folks

      $coupon_code = '';
      if(($coupon = $txn->coupon())) {
        $coupon_code = $coupon->post_title;
      }

      Track::sale(
        'memberpress',
        $txn->amount,
        $txn->trans_num,
        $prd->ID,
        $prd->post_title,
        $txn->id,
        $coupon_code,
        $txn->user_id,
        $subscr_id,
        $txn_count
      );
    }
  }

  public static function add_transaction_meta($event) {
    $txn = $event->get_data();

    // Kill it if it's not a payment type
    if(!($txn instanceof MeprTransaction) || $txn->txn_type != MeprTransaction::$payment_str) {
      return;
    }

    $sub = $txn->subscription();

    // Subscriptions are already tracked separately
    if($sub instanceof MeprSubscription) {
      return;
    }

    // Make sure not to track an admin manually adding a transaction
    if(is_super_admin()) {
      return;
    }

    $affiliate_id = Cookie::get_affiliate_id();
    $click_id = Cookie::get_click_id();

    // Override the affiliate if a coupon was used that is associated with an affiliate
    if(($coupon = $txn->coupon()) && $coupon instanceof MeprCoupon) {
      $enabled = (isset($coupon->ID) && $coupon->ID) ? get_post_meta($coupon->ID, 'wafp_coupon_affiliate_enabled', true) : false;

      if($enabled && ($coupon_affiliate_id = get_post_meta($coupon->ID, 'wafp_coupon_affiliate', true))) {
        $affiliate_id = (int) $coupon_affiliate_id;
        $click_id = 0;
      }
    }

    if($affiliate_id > 0) {
      $txn->update_meta('_esaf_affiliate_id', $affiliate_id);
      $txn->update_meta('_esaf_click_id', $click_id);
    }
  }

  public static function refund_transaction($txn) {
    if(($transaction = Transaction::get_one_by_trans_num($txn->trans_num))) {
      $transaction->apply_refund($txn->amount);
      $transaction->store();
    }
  }

  public static function product_meta_boxes($product) {
    add_meta_box(
      'memberpress-easy-affiliate-options',
      __('Easy Affiliate Commissions', 'easy-affiliate'),
      [self::class, 'product_meta_box'],
      MeprProduct::$cpt,
      'side',
      'default',
      ['product' => $product]
    );
  }

  //Don't use $post here, it is null on new product - use args instead
  public static function product_meta_box($post, $args) {
    $mepr_options = MeprOptions::fetch();
    $product = $args['args']['product'];
    $commission_groups_enabled = false;
    $commission_type = 'percentage';
    $commission_levels = ['0.00'];
    $subscription_commissions = 'all';

    $levels = get_post_meta($product->ID, 'wafp_commissions', true);

    if(is_array($levels) && $levels > 0) {
      $commission_groups_enabled = get_post_meta($product->ID, 'wafp_commission_groups_enabled', true);
      $commission_type = get_post_meta($product->ID, 'wafp_commission_type', true);
      $commission_levels = $levels;
      $subscription_commissions = get_post_meta($product->ID, 'wafp_recurring', true) ? 'all' : 'first-only';
    }

    require ESAF_VIEWS_PATH . '/options/memberpress_product_meta_box.php';
  }

  public static function save_product($product) {
    $options = Options::fetch();

    $enabled = isset($_POST['wafp_enable_commission_group']);
    $commission_type = isset($_POST['wafp-commission-type']) && is_string($_POST['wafp-commission-type']) && $_POST['wafp-commission-type'] == 'fixed' ? 'fixed' : 'percentage';
    $commission_levels = isset($_POST['wafp-commission']) && is_array($_POST['wafp-commission']) ? $options->sanitize_commissions($_POST['wafp-commission'], $commission_type) : [];
    $recurring = isset($_POST['wafp-subscription-commissions']) && is_string($_POST['wafp-subscription-commissions']) && $_POST['wafp-subscription-commissions'] == 'all';

    update_post_meta($product->ID, 'wafp_commission_groups_enabled', $enabled);
    update_post_meta($product->ID, 'wafp_commission_type', $commission_type);
    update_post_meta($product->ID, 'wafp_commissions', $commission_levels);
    update_post_meta($product->ID, 'wafp_recurring', $recurring);
  }

  public static function commission_percentages($commissions, $affiliate) {
    if($group = self::get_commission_group($affiliate->ID)) {
      $commissions = $group->commissions;
    }

    return $commissions;
  }

  public static function get_commission_group($user_id) {
    if(class_exists('MeprUser')) {
      $usr = new MeprUser($user_id);
      $pids = $usr->active_product_subscriptions();

      foreach($pids as $pid) {
        $commission_groups_enabled = get_post_meta($pid, 'wafp_commission_groups_enabled', true);

        // Just short circuit once we find our first product with groups enabled
        if($commission_groups_enabled) {
          $product = new MeprProduct($pid);

          return (object) [
            'commission_type' => get_post_meta($pid, 'wafp_commission_type', true),
            'commission_source' => [
              'slug' => "product-{$pid}",
              'label' => sprintf(__('%s Commission Group', 'easy-affiliate'), $product->post_title)
            ],
            'commissions' => get_post_meta($pid, 'wafp_commissions', true),
            'recurring' => get_post_meta($pid, 'wafp_recurring', true)
          ];
        }
      }
    }

    return false;
  }

  public static function commission_type($commission_type, $affiliate) {
    if($group = self::get_commission_group($affiliate->ID)) {
      $commission_type = $group->commission_type;
    }

    return $commission_type;
  }

  public static function commission_source($source, $affiliate) {
    if($group = self::get_commission_group($affiliate->ID)) {
      $source = $group->commission_source;
    }

    return $source;
  }

  public static function subscription_commissions($subscription_commissions, $affiliate) {
    if($group = self::get_commission_group($affiliate->ID)) {
      $subscription_commissions = $group->recurring ? 'all' : 'first-only';
    }

    return $subscription_commissions;
  }

  //COUPON STUFF
  public static function track_coupon($txn) {
    if(($coupon = $txn->coupon()) && $coupon instanceof MeprCoupon) {
      $enabled = (isset($coupon->ID) && $coupon->ID)?get_post_meta($coupon->ID, 'wafp_coupon_affiliate_enabled', true):false;

      if($enabled && ($affiliate_id = get_post_meta($coupon->ID, 'wafp_coupon_affiliate', true))) {
        // Override the affiliate if there's a coupon associated with an affiliate
        Cookie::override($affiliate_id);
      }
    }
  }

  public static function coupon_meta_boxes($coupon) {
    add_meta_box(
      'memberpress-easy-affiliate-coupon-options',
      __('Associate Affiliate', 'easy-affiliate'),
      [self::class, 'coupon_meta_box'],
      MeprCoupon::$cpt,
      'side',
      'default',
      ['coupon' => $coupon]
    );
  }

  //Don't use $post here, it is null on new product - use args instead
  public static function coupon_meta_box($post, $args) {
    $mepr_options = MeprOptions::fetch();
    $coupon = $args['args']['coupon'];
    $enabled = (isset($coupon->ID) && $coupon->ID)?get_post_meta($coupon->ID, 'wafp_coupon_affiliate_enabled', true):false;
    $affiliate_login = ''; //We'll populate later
    $affiliate_id = (isset($coupon->ID) && $coupon->ID)?get_post_meta($coupon->ID, 'wafp_coupon_affiliate', true):false;

    if($affiliate_id) {
      $user = get_user_by('id', $affiliate_id);
      $affiliate_login = $user->user_login;
    }

    require ESAF_VIEWS_PATH . '/options/memberpress_coupon_meta_box.php';
  }

  public static function save_coupon($coupon) {
    if(isset($_POST['mepr-associate-affiliate-enable']) && !empty($_POST['mepr-associate-affiliate-username'])) {
      $username = stripslashes($_POST['mepr-associate-affiliate-username']);
      $user = get_user_by('login', $username);

      if($user instanceof \WP_User && isset($user->ID) && $user->ID && isset($coupon->ID) && $coupon->ID) {
        update_post_meta($coupon->ID, 'wafp_coupon_affiliate_enabled', 1);
        update_post_meta($coupon->ID, 'wafp_coupon_affiliate', $user->ID);
      }
    }
    else {
      if(isset($coupon->ID) && $coupon->ID) {
        update_post_meta($coupon->ID, 'wafp_coupon_affiliate_enabled', 0);
        update_post_meta($coupon->ID, 'wafp_coupon_affiliate', 0);
      }
    }
  }

  public static function enqueue_coupon_scripts($hook) {
      wp_enqueue_style( 'wafp-mp-coupons-css', ESAF_CSS_URL . '/memberpress-coupons.css', [], ESAF_VERSION);
      wp_enqueue_script( 'wafp-mp-coupons-js', ESAF_JS_URL . '/memberpress-coupons.js', ['jquery', 'suggest'], ESAF_VERSION);
  }

  public static function coupon_count($coupon_count) {
    global $wpdb;
    $affiliate = Utils::get_currentuserinfo();

    if($affiliate && $affiliate->is_affiliate) {
      $query = new \WP_Query([
        'post_type' => MeprCoupon::$cpt,
        'post_status' => 'publish',
        'meta_key' => 'wafp_coupon_affiliate',
        'meta_value' => $affiliate->ID,
      ]);

      $coupon_count += $query->found_posts;
    }

    return $coupon_count;
  }

  public static function display_my_coupons() {
    $affiliate = Utils::get_currentuserinfo();

    if($affiliate && $affiliate->is_affiliate) {
      $my_coupons = get_posts([
        'post_type' => MeprCoupon::$cpt,
        'post_status' => 'publish',
        'meta_key' => 'wafp_coupon_affiliate',
        'meta_value' => $affiliate->ID,
        'fields' => 'ids',
        'numberposts' => -1
      ]);

      if(!empty($my_coupons)) {
        require ESAF_VIEWS_PATH . '/dashboard/memberpress-coupons.php';
      }
    }
  }

  /**
   * Link the transaction source label to the MemberPress transaction if applicable
   *
   * @param  string    $label The original transaction source label (already escaped)
   * @param  \stdClass $rec   The transaction rec object
   * @return string
   */
  public static function transaction_source_label($label, $rec) {
    $source = isset($rec->source) && is_string($rec->source) ? $rec->source : '';
    $order_id = isset($rec->order_id) && is_numeric($rec->order_id) ? (int) $rec->order_id : 0;

    if($source == 'memberpress' && $order_id && class_exists('MeprUtils')) {
      $label = sprintf(
        '<a href="%s">%s</a>',
        esc_url(admin_url("admin.php?page=memberpress-trans&action=edit&id={$order_id}")),
        $label
      );
    }

    return $label;
  }
} //End class
