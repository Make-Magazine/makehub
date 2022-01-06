<?php

namespace EasyAffiliate\Lib\Migrator;

use EasyAffiliate\Helpers\OptionsHelper;
use EasyAffiliate\Lib\Db;
use EasyAffiliate\Lib\Utils;
use EasyAffiliate\Models\Commission;
use EasyAffiliate\Models\Creative;
use EasyAffiliate\Models\Options;
use EasyAffiliate\Models\Transaction;
use EasyAffiliate\Models\User;

class AffiliateWP {
  const TIMEOUT = 30;
  const CHUNK_SIZE = 1000;

  /**
   * Do the migration based on the given data
   *
   * @param array $data The data array for the current step
   */
  public static function migrate(array $data) {
    switch($data['step']) {
      case 'settings':
        self::check_prerequisites();
        self::migrate_settings();
        break;
      case 'affiliates':
        self::migrate_affiliates($data);
        break;
      case 'transactions':
        self::migrate_transactions($data);
        break;
      case 'clicks':
        self::migrate_clicks($data);
        break;
      case 'payments':
        self::migrate_payments($data);
        break;
      case 'creatives':
        self::migrate_creatives($data);
        break;
      case 'coupons':
        self::migrate_coupons($data);
        break;
    }

    wp_send_json_error(__('Bad request', 'easy-affiliate'));
  }

  /**
   * Check that the migrator prerequisites are met
   *
   * Make sure that the tables exist for our later queries.
   */
  private static function check_prerequisites() {
    global $wpdb;
    $db = Db::fetch();

    $tables = [
      $wpdb->prefix . 'affiliate_wp_affiliatemeta',
      $wpdb->prefix . 'affiliate_wp_affiliates',
      $wpdb->prefix . 'affiliate_wp_creatives',
      $wpdb->prefix . 'affiliate_wp_customermeta',
      $wpdb->prefix . 'affiliate_wp_customers',
      $wpdb->prefix . 'affiliate_wp_payouts',
      $wpdb->prefix . 'affiliate_wp_referrals',
      $wpdb->prefix . 'affiliate_wp_sales',
      $wpdb->prefix . 'affiliate_wp_visits'
    ];

    foreach($tables as $table) {
      if(!$db->table_exists($table)) {
        wp_send_json_error(sprintf('Migration not started: table `%s` not found, try updating AffiliateWP to the latest version first.', $table));
      }
    }
  }

  /**
   * Migrate settings
   */
  private static function migrate_settings() {
    $affwp_settings = get_option('affwp_settings', array());

    if(empty($affwp_settings) || !is_array($affwp_settings)) {
      wp_send_json_error(__('AffiliateWP settings not found', 'easy-affiliate'));
    }

    $options = Options::fetch();

    if(isset($affwp_settings['referral_rate_type']) && is_string($affwp_settings['referral_rate_type'])) {
      if($affwp_settings['referral_rate_type'] == 'percentage') {
        $options->commission_type = 'percentage';
      }
      elseif($affwp_settings['referral_rate_type'] == 'flat') {
        $options->commission_type = 'fixed';
      }
    }

    if(isset($affwp_settings['referral_rate']) && is_numeric($affwp_settings['referral_rate'])) {
      $options->commission = [$affwp_settings['referral_rate']];
    }

    if(isset($affwp_settings['cookie_exp']) && is_numeric($affwp_settings['cookie_exp'])) {
      $options->expire_after_days = $affwp_settings['cookie_exp'];
    }

    if(isset($affwp_settings['currency']) && is_string($affwp_settings['currency'])) {
      $options->currency_code = $affwp_settings['currency'] == 'KIP' ? 'LAK' : $affwp_settings['currency'];
      $options->currency_symbol = self::get_currency_symbol($options->currency_code);
    }

    if(isset($affwp_settings['currency_position']) && in_array($affwp_settings['currency_position'], ['before', 'after'])) {
      $options->currency_symbol_after_amount = $affwp_settings['currency_position'] == 'after';
    }

    if(isset($affwp_settings['thousands_separator'], $affwp_settings['decimal_separator']) && is_string($affwp_settings['thousands_separator']) && is_string($affwp_settings['decimal_separator'])) {
      $seps = $affwp_settings['thousands_separator'] . $affwp_settings['decimal_separator'];

      if($seps == ',.') {
        $options->number_format = '#,###.##';
      }
      elseif($seps == '.,') {
        $options->number_format = '#.###,##';
      }
    }

    if(isset($affwp_settings['integrations']) && is_array($affwp_settings['integrations'])) {
      $integrations = [];

      foreach($affwp_settings['integrations'] as $key => $label) {
        if($key == 'memberpress') {
          $integrations[] = 'memberpress';
        }
        elseif($key == 'woocommerce') {
          $integrations[] = 'woocommerce';
        }
        elseif($key == 'edd') {
          $integrations[] = 'easy_digital_downloads';
        }
      }

      $options->integration = $integrations;
    }

    if(isset($affwp_settings['allow_affiliate_registration']) && $affwp_settings['allow_affiliate_registration']) {
      if(isset($affwp_settings['require_approval']) && $affwp_settings['require_approval']) {
        $options->registration_type = 'application';
      }
      else {
        $options->registration_type = 'public';
      }
    }
    else {
      $options->registration_type = 'private';
    }

    if(isset($affwp_settings['auto_register'])) {
      $options->make_new_users_affiliates = (bool) $affwp_settings['auto_register'];
    }

    if(isset($affwp_settings['referral_pretty_urls'])) {
      $options->pretty_affiliate_links = (bool) $affwp_settings['referral_pretty_urls'];
    }

    if(isset($affwp_settings['referral_var']) && is_string($affwp_settings['referral_var']) && $affwp_settings['referral_var'] !== '') {
      update_option('esaf_affiliate_wp_referral_var', $affwp_settings['referral_var']);
    }

    $options->store();

    update_option('esaf_flush_rewrite_rules', '1');

    wp_send_json_success([
      'step' => 'affiliates',
      'status_text' => __('Migrating Affiliates', 'easy-affiliate'),
      'progress' => 14
    ]);
  }

  /**
   * Get the currency symbol for the given currency code
   *
   * @param string $currency_code
   * @return string
   */
  private static function get_currency_symbol($currency_code) {
    switch($currency_code) {
      default:
        return '$';
      case 'EUR':
        return '€';
      case 'GBP':
        return '£';
      case 'AED':
        return 'د.إ';
      case 'AUD':
        return 'A$';
      case 'BDT':
        return 'Tk';
      case 'BTC':
        return '₿';
      case 'BRL':
        return 'R$';
      case 'BGN':
        return 'лв';
      case 'CAD':
        return 'C$';
      case 'CHF':
        return 'SFr.';
      case 'CNY':
      case 'JPY':
        return '¥';
      case 'CZK':
        return 'Kč';
      case 'DKK':
      case 'ISK':
      case 'NOK':
      case 'SEK':
        return 'kr';
      case 'EGP':
        return 'E£';
      case 'HRK':
        return 'kn';
      case 'HUF':
        return 'Ft';
      case 'IDR':
        return 'Rp';
      case 'ILS':
        return '₪';
      case 'INR':
        return '₹';
      case 'IRR':
        return '﷼';
      case 'KES':
        return 'Ksh';
      case 'KRW':
        return '₩';
      case 'KZT':
        return '₸';
      case 'LAK':
        return '₭';
      case 'MXN':
        return 'Mex$';
      case 'MYR':
        return 'RM';
      case 'NGN':
        return '₦';
      case 'NPR':
      case 'PKR':
        return '₨';
      case 'PHP':
        return '₱';
      case 'PLN':
        return 'zł';
      case 'PYG':
        return '₲';
      case 'RON':
        return 'L';
      case 'RUB':
        return 'руб';
      case 'SAR':
        return 'SR';
      case 'SGD':
        return 'S$';
      case 'THB':
        return '฿';
      case 'TND':
        return 'DT';
      case 'TRY':
        return 'TL';
      case 'TWD':
        return 'NT$';
      case 'UAH':
        return '₴';
      case 'VND':
        return '₫';
      case 'ZAR':
        return 'R';
    }
  }

  /**
   * Migrate affiliates
   *
   * @param array $data The data array for the current step
   */
  private static function migrate_affiliates(array $data) {
    global $wpdb;

    $start = time();
    $limit = self::CHUNK_SIZE;
    $offset = isset($data['offset']) && is_numeric($data['offset']) && $data['offset'] > 0 ? (int) $data['offset'] : 0;
    $query = "SELECT * FROM {$wpdb->prefix}affiliate_wp_affiliates ORDER BY affiliate_id ASC LIMIT {$limit} OFFSET %d;";
    $affiliates = $wpdb->get_results($wpdb->prepare($query, $offset));

    while(is_array($affiliates) && count($affiliates) > 0 && (time() - $start < self::TIMEOUT)) {
      $affiliates = $wpdb->get_results($wpdb->prepare($query, $offset));

      foreach($affiliates as $affiliate) {
        self::migrate_affiliate($affiliate);
      }

      $offset = $offset + self::CHUNK_SIZE;
      $affiliates = $wpdb->get_results($wpdb->prepare($query, $offset));
    }

    if(!empty($affiliates)) {
      $response = [
        'step' => 'affiliates',
        'status_text' => __('Migrating Affiliates', 'easy-affiliate'),
        'offset' => $offset
      ];
    }
    else {
      $response = [
        'step' => 'transactions',
        'status_text' => __('Migrating Transactions', 'easy-affiliate'),
        'progress' => 28
      ];
    }

    wp_send_json_success($response);
  }

  /**
   * Migrate a single affiliate
   *
   * @param \stdClass $affiliate
   */
  private static function migrate_affiliate($affiliate) {
    global $wpdb;

    $user = new User($affiliate->user_id);

    if($user->ID > 0 && $affiliate->status == 'active') {
      if(get_user_meta($user->ID, 'esaf_affiliate_wp_affiliate_id', true)) {
        if(empty($user->referrer)) {
          $referrer = self::get_affiliate_referrer($affiliate);

          if(!empty($referrer)) {
            $user->referrer = $referrer;
            $user->store(false);
          }
        }

        return;
      }

      $user->is_affiliate = true;

      if($affiliate->rate_type) {
        update_user_meta($user->ID, 'wafp_commission_type', $affiliate->rate_type == 'percentage' ? 'percentage' : 'fixed');
        update_user_meta($user->ID, 'wafp_override', [$affiliate->rate]);
      }

      if($affiliate->payment_email) {
        $user->paypal_email = $affiliate->payment_email;
      }

      $notes_query = $wpdb->prepare(
        "SELECT meta_value FROM {$wpdb->prefix}affiliate_wp_affiliatemeta WHERE affiliate_id = %d AND meta_key = 'notes';",
        $affiliate->affiliate_id
      );

      if($notes = $wpdb->get_var($notes_query)) {
        $user->notes = $notes;
      }

      $referrer = self::get_affiliate_referrer($affiliate);

      if(empty($user->referrer) && !empty($referrer)) {
        $user->referrer = $referrer;
      }

      $user->store(false);
      update_user_meta($user->ID, 'esaf_affiliate_wp_affiliate_id', $affiliate->affiliate_id);
    }
  }

  /**
   * Get the referrer of the given affiliate
   *
   * @param \stdClass $affiliate The AffiliateWP affiliate data
   * @return int
   */
  private static function get_affiliate_referrer($affiliate) {
    global $wpdb;

    // For the MemberPress integration the customer's user_id is 0, so we'll also do the customer lookup by email
    $user = get_userdata($affiliate->user_id);

    if(!$user instanceof \WP_User || empty($user->user_email)) {
      return 0;
    }

    $query = $wpdb->prepare(
      "SELECT cusmeta.meta_value
      FROM {$wpdb->prefix}affiliate_wp_customers cus
      INNER JOIN {$wpdb->prefix}affiliate_wp_customermeta cusmeta
      ON cusmeta.affwp_customer_id = cus.customer_id
      WHERE (cus.user_id = %d OR cus.email = %s) AND cusmeta.meta_key = 'affiliate_id'
      ORDER BY cus.customer_id ASC
      LIMIT 1",
      $affiliate->user_id,
      $user->user_email
    );

    $referrer = (int) $wpdb->get_var($query);

    return $referrer;
  }

  /**
   * Migrate transactions
   *
   * @param array $data The data array for the current step
   */
  private static function migrate_transactions(array $data) {
    global $wpdb;

    $start = time();
    $limit = self::CHUNK_SIZE;
    $offset = isset($data['offset']) && is_numeric($data['offset']) && $data['offset'] > 0 ? (int) $data['offset'] : 0;

    $query = "SELECT ref.*, aff.user_id, sale.order_total, cust.email AS cust_email, cust.first_name AS cust_first_name, cust.last_name AS cust_last_name
      FROM {$wpdb->prefix}affiliate_wp_referrals ref
      LEFT JOIN {$wpdb->prefix}affiliate_wp_affiliates aff ON ref.affiliate_id = aff.affiliate_id
      LEFT JOIN {$wpdb->prefix}affiliate_wp_sales sale ON ref.referral_id = sale.referral_id
      LEFT JOIN {$wpdb->prefix}affiliate_wp_customers cust ON ref.customer_id = cust.customer_id
      WHERE ref.type = 'sale'
      AND ref.status NOT IN ('pending', 'rejected')
      ORDER BY referral_id ASC LIMIT {$limit} OFFSET %d;";

    $referrals = $wpdb->get_results($wpdb->prepare($query, $offset));

    while(is_array($referrals) && count($referrals) > 0 && (time() - $start < self::TIMEOUT)) {
      foreach($referrals as $referral) {
        self::migrate_referral($referral);
      }

      $offset = $offset + self::CHUNK_SIZE;
      $referrals = $wpdb->get_results($wpdb->prepare($query, $offset));
    }

    if(!empty($referrals)) {
      $response = [
        'step' => 'transactions',
        'status_text' => __('Migrating Transactions', 'easy-affiliate'),
        'offset' => $offset
      ];
    }
    else {
      $response = [
        'step' => 'clicks',
        'status_text' => __('Migrating Clicks', 'easy-affiliate'),
        'progress' => 42
      ];
    }

    wp_send_json_success($response);
  }

  /**
   * Migrate a single referral
   *
   * @param \stdClass $referral The referral data
   */
  private static function migrate_referral($referral) {
    $affiliate_id = (int) $referral->user_id;

    if(!($affiliate_id > 0)) {
      return; // bail if there isn't an affiliate ID
    }

    $trans_num = 'affiliatewp-' . $referral->referral_id;
    $existing_transaction = Transaction::get_one_by_trans_num($trans_num);
    list($subscr_id, $subscr_paynum) = self::get_transaction_subscription_data($referral);

    if($existing_transaction instanceof Transaction) {
      if(!empty($subscr_id)) {
        if(empty($existing_transaction->subscr_id)) {
          $existing_transaction->subscr_id = $subscr_id;
          $existing_transaction->subscr_paynum = $subscr_paynum;
          $existing_transaction->store();
        }

        self::update_subscription_meta($referral, $affiliate_id);
      }

      return;
    }

    global $wpdb;
    $db = Db::fetch();
    $sale_amount = self::get_transaction_sale_amount($referral);

    if(!($sale_amount > 0)) {
      return; // bail if there isn't a positive sale_amount
    }

    $click_id = 0;

    if($referral->visit_id > 0) {
      // Migrate the visit to a click first, so we'll have the click ID
      $visit = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}affiliate_wp_visits WHERE visit_id = %d", $referral->visit_id));

      if($visit && $visit->referral_id == $referral->referral_id && $visit->affiliate_id == $referral->affiliate_id) {
        $click_id = self::migrate_visit($visit, $affiliate_id);
      }
    }

    $transaction = [
      'source' => self::get_transaction_source($referral),
      'sale_amount' => $sale_amount,
      'trans_num' => $trans_num,
      'order_id' => self::get_transaction_order_id($referral),
      'item_id' => null,
      'item_name' => mb_substr($referral->description, 0 , 255),
      'coupon' => '',
      'subscr_id' => $subscr_id,
      'subscr_paynum' => $subscr_paynum,
      'rebill' => $subscr_paynum > 1,
      'cust_name' => isset($referral->cust_first_name, $referral->cust_last_name) ? mb_substr(trim(join(' ', [$referral->cust_first_name, $referral->cust_last_name])), 0, 255) : null,
      'cust_email' => isset($referral->cust_email) ? mb_substr($referral->cust_email, 0, 255) : null,
      'ip_addr' => '',
      'type' => 'commission',
      'status' => 'complete',
      'affiliate_id' => $affiliate_id,
      'click_id' => $click_id,
      'created_at' => $referral->date,
    ];

    $wpdb->insert($db->transactions, $transaction);
    $transaction_id = $wpdb->insert_id;

    if(!empty($subscr_id)) {
      self::update_subscription_meta($referral, $affiliate_id);
    }

    if($transaction_id > 0) {
      $payment_id = 0;

      if($referral->status == 'paid' && $referral->payout_id > 0) {
        // Migrate the payout to a payment first, so we'll have the payment ID
        $payout = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}affiliate_wp_payouts WHERE payout_id = %d", $referral->payout_id));

        if($payout) {
          $payment_id = self::migrate_payout($payout, $affiliate_id);
        }
      }

      // Add commission
      $commission = [
        'affiliate_id' => $affiliate_id,
        'transaction_id' => $transaction_id,
        'commission_level' => 0,
        'commission_percentage' => Utils::format_float($referral->amount),
        'commission_type' => 'fixed',
        'commission_amount' => Utils::format_float($referral->amount),
        'correction_amount' => 0,
        'payment_id' => $payment_id,
        'created_at' => $referral->date,
      ];

      $wpdb->insert($db->commissions, $commission);
    }
  }

  /**
   * Get the transaction source for the given referral
   *
   * @param \stdClass $referral The referral data
   * @return string             The transaction source
   */
  private static function get_transaction_source($referral) {
    switch($referral->context) {
      case 'memberpress':
      case 'woocommerce':
      case 'wpforms':
        return $referral->context;
      case 'edd':
        return 'easy_digital_downloads';
      case 'formidablepro':
        return 'formidable';
    }

    return 'general';
  }

  /**
   * Get the transaction sale amount for the given referral
   *
   * @param \stdClass $referral The referral data
   * @return string             The transaction sale amount
   */
  private static function get_transaction_sale_amount($referral) {
    if(isset($referral->order_total) && is_numeric($referral->order_total) && $referral->order_total > 0) {
      return Utils::format_float($referral->order_total);
    }
    elseif($referral->context == 'memberpress' && class_exists('MeprTransaction') && is_numeric($referral->reference) && $referral->reference > 0) {
      $transaction = new \MeprTransaction($referral->reference);

      if($transaction->id > 0 && $transaction->txn_type == \MeprTransaction::$payment_str) {
        return Utils::format_float($transaction->amount);
      }
    }

    // Some integrations do not store the total sale amount, let's try to calculate it from the commission amount
    $affiliate = new User((int) $referral->user_id);

    if($affiliate->ID > 0) {
      $commission_percentage = Commission::get_percentage(0, $affiliate);
      $commission_type = Commission::get_type($affiliate);

      if($commission_type == 'percentage') {
        if($commission_percentage > 0) {
          $sale_amount = (float) ($referral->amount / ($commission_percentage / 100));

          if($sale_amount > 0) {
            return Utils::format_float($sale_amount);
          }
        }
      }
      elseif($commission_type == 'fixed') {
        return Utils::format_float($referral->amount);
      }
    }

    return '0.00';
  }

  /**
   * Get the transaction order ID for the given referral
   *
   * @param \stdClass $referral
   * @return int
   */
  private static function get_transaction_order_id($referral) {
    switch($referral->context) {
      case 'memberpress':
      case 'woocommerce':
      case 'edd':
      case 'wpforms':
      case 'formidablepro':
        return (int) $referral->reference;
    }

    return 0;
  }

  /**
   * Get the subscription ID and payment number for the given referral
   *
   * @param \stdClass $referral
   * @return array
   */
  private static function get_transaction_subscription_data($referral) {
    if($referral->context == 'memberpress') {
      if(class_exists('MeprTransaction') && is_numeric($referral->reference) && $referral->reference > 0) {
        $transaction = new \MeprTransaction($referral->reference);

        if(!empty($transaction->id)) {
          $subscription = $transaction->subscription();

          if($subscription instanceof \MeprSubscription) {
            $payment_index = 1;

            if(method_exists($transaction, 'subscription_payment_index')) {
              $subscription_payment_index = $transaction->subscription_payment_index();

              if(is_numeric($subscription_payment_index) && $subscription_payment_index > 1) {
                $payment_index = $subscription_payment_index;
              }
            }

            return [
              $subscription->subscr_id,
              $payment_index
            ];
          }
        }
      }
    }
    elseif($referral->context == 'woocommerce') {
      if(function_exists('wc_get_order') && class_exists('WC_Subscription') && is_numeric($referral->reference) && $referral->reference > 0) {
        $order = wc_get_order($referral->reference);

        if($order instanceof \WC_Order) {
          foreach($order->get_items() as $item) {
            if($item instanceof \WC_Order_Item_Product) {
              $product = $item->get_product();

              if($product instanceof \WC_Product_Subscription) {
                $subscriptions = wcs_get_subscriptions_for_order($order, ['product_id' => $product->get_id(), 'order_type' => 'any']);

                foreach($subscriptions as $subscription) {
                  if(method_exists($subscription, 'get_order_key')) {
                    return [
                      $subscription->get_order_key() . '-' . $product->get_id(),
                      self::get_woocommerce_subscription_payment_number($order, $subscription)
                    ];
                  }
                }
              }
            }
          }
        }
      }
    }

    return ['', 0];
  }

  /**
   * Get the payment number for the given order and WooCommerce subscription
   *
   * @param \WC_Order $order
   * @param \WC_Subscription $subscription
   *
   * @return int
   */
  private static function get_woocommerce_subscription_payment_number($order, $subscription) {
    $related_orders = $subscription->get_related_orders('all', ['renewal']);

    if(is_array($related_orders) && count($related_orders) > 0) {
      ksort($related_orders);
      $previous_renewals = 0;

      foreach($related_orders as $related_order) {
        $previous_renewals++;

        if($related_order->get_id() == $order->get_id()) {
          return $previous_renewals + 1;
        }
      }
    }

    return 1;
  }

  /**
   * Add the affiliate ID to the subscription metadata
   *
   * @param \stdClass $referral
   * @param int $affiliate_id
   */
  private static function update_subscription_meta($referral, $affiliate_id) {
    if($referral->context == 'memberpress') {
      if(class_exists('MeprTransaction') && is_numeric($referral->reference) && $referral->reference > 0) {
        $transaction = new \MeprTransaction($referral->reference);

        if(!empty($transaction->id)) {
          $subscription = $transaction->subscription();

          if($subscription instanceof \MeprSubscription && !$subscription->get_meta('_esaf_affiliate_id', true)) {
            $subscription->update_meta('_esaf_affiliate_id', $affiliate_id);
          }
        }
      }
    }
    elseif($referral->context == 'woocommerce') {
      if(function_exists('wc_get_order') && class_exists('WC_Subscription') && is_numeric($referral->reference) && $referral->reference > 0) {
        $order = wc_get_order($referral->reference);

        if($order instanceof \WC_Order) {
          foreach($order->get_items() as $item) {
            if($item instanceof \WC_Order_Item_Product) {
              $product = $item->get_product();

              if($product instanceof \WC_Product_Subscription) {
                $subscriptions = wcs_get_subscriptions_for_order($order, ['product_id' => $product->get_id(), 'order_type' => 'any']);

                foreach($subscriptions as $subscription) {
                  if(!get_post_meta($subscription->get_id(), 'ar_affiliate', true)) {
                    update_post_meta($subscription->get_id(), 'ar_affiliate', $affiliate_id);
                  }
                  return;
                }
              }
            }
          }
        }
      }
    }
  }

  /**
   * Migrate clicks
   *
   * @param array $data The data array for the current step
   */
  private static function migrate_clicks(array $data) {
    global $wpdb;

    $start = time();
    $limit = self::CHUNK_SIZE;
    $offset = isset($data['offset']) && is_numeric($data['offset']) && $data['offset'] > 0 ? (int) $data['offset'] : 0;

    $query = "SELECT visits.*, aff.user_id FROM {$wpdb->prefix}affiliate_wp_visits visits
      LEFT JOIN {$wpdb->prefix}affiliate_wp_affiliates aff ON visits.affiliate_id = aff.affiliate_id
      ORDER BY visits.visit_id ASC LIMIT {$limit} OFFSET %d;";

    $visits = $wpdb->get_results($wpdb->prepare($query, $offset));

    while(is_array($visits) && count($visits) > 0 && (time() - $start < self::TIMEOUT)) {
      foreach($visits as $visit) {
        self::migrate_visit($visit, (int) $visit->user_id);
      }

      $offset = $offset + self::CHUNK_SIZE;
      $visits = $wpdb->get_results($wpdb->prepare($query, $offset));
    }

    if(!empty($visits)) {
      $response = [
        'step' => 'clicks',
        'status_text' => __('Migrating Clicks', 'easy-affiliate'),
        'offset' => $offset
      ];
    }
    else {
      $response = [
        'step' => 'payments',
        'status_text' => __('Migrating Payments', 'easy-affiliate'),
        'progress' => 56
      ];
    }

    wp_send_json_success($response);
  }

  /**
   * Migrate a visit to a click and return the click ID
   *
   * @param \stdClass $visit        The visit object
   * @param int       $affiliate_id The affiliate ID
   * @return int                    The created click ID
   */
  private static function migrate_visit($visit, $affiliate_id) {
    global $wpdb;
    $db = Db::fetch();

    $uri = mb_substr($visit->url, 0, 255);
    $ip = mb_substr($visit->ip, 0, 255);
    $referrer = mb_substr($visit->referrer, 0, 255);

    $existing_click_id = (int) $wpdb->get_var(
      $wpdb->prepare(
        "SELECT id FROM {$db->clicks} WHERE affiliate_id = %d AND created_at = %s AND uri = %s AND ip = %s AND referrer = %s;",
        $affiliate_id,
        $visit->date,
        $uri,
        $ip,
        $referrer
      )
    );

    if($existing_click_id > 0) {
      return $existing_click_id;
    }

    $click = [
      'affiliate_id' => $affiliate_id,
      'uri' => $uri,
      'ip' => $ip,
      'browser' => '',
      'referrer' => $referrer,
      'first_click' => 1,
      'created_at' => $visit->date,
    ];

    $wpdb->insert($db->clicks, $click);

    return $wpdb->insert_id;
  }

  /**
   * Migrate payments
   *
   * @param array $data The data array for the current step
   */
  private static function migrate_payments(array $data) {
    global $wpdb;

    $start = time();
    $limit = self::CHUNK_SIZE;
    $offset = isset($data['offset']) && is_numeric($data['offset']) && $data['offset'] > 0 ? (int) $data['offset'] : 0;

    $query = "SELECT payouts.*, affiliates.user_id FROM {$wpdb->prefix}affiliate_wp_payouts payouts
      LEFT JOIN {$wpdb->prefix}affiliate_wp_affiliates affiliates ON payouts.affiliate_id = affiliates.affiliate_id
      ORDER BY payouts.payout_id ASC LIMIT {$limit} OFFSET %d;";

    $payouts = $wpdb->get_results($wpdb->prepare($query, $offset));

    while(is_array($payouts) && count($payouts) > 0 && (time() - $start < self::TIMEOUT)) {
      foreach($payouts as $payout) {
        $affiliate_id = (int) $payout->user_id;

        if($affiliate_id > 0) {
          self::migrate_payout($payout, $affiliate_id);
        }
      }

      $offset = $offset + self::CHUNK_SIZE;
      $payouts = $wpdb->get_results($wpdb->prepare($query, $offset));
    }

    if(!empty($payouts)) {
      $response = [
        'step' => 'payments',
        'status_text' => __('Migrating Payments', 'easy-affiliate'),
        'offset' => $offset
      ];
    }
    else {
      $response = [
        'step' => 'creatives',
        'status_text' => __('Migrating Creatives', 'easy-affiliate'),
        'progress' => 70
      ];
    }

    wp_send_json_success($response);
  }

  /**
   * Migrate a payout to a payment and return the payment ID
   *
   * @param \stdClass $payout       The payout object
   * @param int       $affiliate_id The affiliate ID
   * @return int                    The created payment ID
   */
  private static function migrate_payout($payout, $affiliate_id) {
    global $wpdb;
    $db = Db::fetch();

    $batch_id = 'affiliatewp-' . $payout->payout_id;

    $existing_payment_id = (int) $wpdb->get_var($wpdb->prepare("SELECT id FROM {$db->payments} WHERE batch_id = %s", $batch_id));

    if($existing_payment_id > 0) {
      return $existing_payment_id;
    }

    $payment = [
      'affiliate_id' => $affiliate_id,
      'amount' => Utils::format_float($payout->amount),
      'batch_id' => $batch_id,
      'payout_method' => $payout->payout_method,
      'created_at' => $payout->date
    ];

    $wpdb->insert($db->payments, $payment);

    return $wpdb->insert_id;
  }

  /**
   * Migrate creatives
   *
   * @param array $data The data array for the current step
   */
  private static function migrate_creatives(array $data) {
    global $wpdb;

    $start = time();
    $limit = self::CHUNK_SIZE;
    $offset = isset($data['offset']) && is_numeric($data['offset']) && $data['offset'] > 0 ? (int) $data['offset'] : 0;

    $query = "SELECT * FROM {$wpdb->prefix}affiliate_wp_creatives ORDER BY creative_id ASC LIMIT {$limit} OFFSET %d;";

    $creatives = $wpdb->get_results($wpdb->prepare($query, $offset));

    while(is_array($creatives) && count($creatives) > 0 && (time() - $start < self::TIMEOUT)) {
      foreach($creatives as $creative) {
        self::migrate_creative($creative);
      }

      $offset = $offset + self::CHUNK_SIZE;
      $creatives = $wpdb->get_results($wpdb->prepare($query, $offset));
    }

    if(!empty($creatives)) {
      $response = [
        'step' => 'creatives',
        'status_text' => __('Migrating Creatives', 'easy-affiliate'),
        'offset' => $offset
      ];
    }
    else {
      $response = [
        'step' => 'coupons',
        'status_text' => __('Migrating Coupons', 'easy-affiliate'),
        'progress' => 84
      ];
    }

    wp_send_json_success($response);
  }

  /**
   * Migrate a single creative
   *
   * @param \stdClass $creative
   */
  private static function migrate_creative($creative) {
    global $wpdb;

    $existing_creative = Creative::get_one([
      $wpdb->prepare('post_title = %s', $creative->name),
      $wpdb->prepare('pm_link_info.meta_value = %s', $creative->description),
      $wpdb->prepare('pm_url.meta_value = %s', $creative->url),
      $wpdb->prepare('pm_link_text.meta_value = %s', $creative->text),
      $wpdb->prepare('pm_image.meta_value = %s', $creative->image),
      $wpdb->prepare('post_date = %s', $creative->date),
    ]);

    if($existing_creative) {
      return;
    }

    $c = new Creative();
    $c->post_title = $creative->name;
    $c->link_info = $creative->description;
    $c->url = $creative->url;
    $c->link_text = $creative->text;
    $c->image = $creative->image;
    $c->is_hidden = $creative->status != 'active';
    $c->post_date = $creative->date;
    $c->link_type = empty($creative->image) ? 'text' : 'banner';
    $c->image_width = 300;
    $c->image_height = 250;

    if(!empty($c->image) && Utils::is_url($c->image) && function_exists('attachment_url_to_postid') && function_exists('wp_get_attachment_image_src')) {
      $image_attributes = wp_get_attachment_image_src(attachment_url_to_postid($c->image), 'full');

      if($image_attributes) {
        $c->image_width = $image_attributes[1];
        $c->image_height = $image_attributes[2];
      }
    }

    $c->store();
  }

  /**
   * Migrate coupons
   *
   * @param array $data The data array for the current step
   */
  private static function migrate_coupons(array $data) {
    global $wpdb;

    $options = Options::fetch();
    $start = time();
    $limit = self::CHUNK_SIZE;
    $offset = isset($data['offset']) && is_numeric($data['offset']) && $data['offset'] > 0 ? (int) $data['offset'] : 0;

    $query = "SELECT * FROM {$wpdb->postmeta} postmeta LEFT JOIN {$wpdb->prefix}affiliate_wp_affiliates affiliates ON postmeta.meta_value = affiliates.affiliate_id WHERE meta_key = 'affwp_discount_affiliate' ORDER BY meta_id ASC LIMIT {$limit} OFFSET %d;";

    $coupons = $wpdb->get_results($wpdb->prepare($query, $offset));

    while(is_array($coupons) && count($coupons) > 0 && (time() - $start < self::TIMEOUT)) {
      foreach($coupons as $coupon) {
        $affiliate_id = (int) $coupon->user_id;
        $user = new User($affiliate_id);

        if($user->ID > 0 && $user->is_affiliate) {
          update_post_meta($coupon->post_id, 'wafp_coupon_affiliate_enabled', 1);
          update_post_meta($coupon->post_id, 'wafp_coupon_affiliate', $user->ID);
        }
      }

      $offset = $offset + self::CHUNK_SIZE;
      $coupons = $wpdb->get_results($wpdb->prepare($query, $offset));
    }

    if(!empty($coupons)) {
      $response = [
        'step' => 'coupons',
        'status_text' => __('Migrating Coupons', 'easy-affiliate'),
        'offset' => $offset
      ];
    }
    else {
      $response = [
        'status' => 'complete',
        'progress' => 100,
        'integration' => $options->integration,
        'registration_type' => $options->registration_type,
        'commission_type' => $options->commission_type,
        'commission_levels_html' => self::get_commission_levels_html()
      ];
    }

    wp_send_json_success($response);
  }

  /**
   * Get the HTML for the current commission levels
   *
   * @return string
   */
  private static function get_commission_levels_html() {
    $options = Options::fetch();
    $output = '';

    foreach($options->commission as $index => $amount) {
      $output .= OptionsHelper::get_commission_level_html($index + 1, $amount);
    }

    return $output;
  }
}
