<?php

namespace EasyAffiliate\Lib;

use EasyAffiliate\Models\Click;
use EasyAffiliate\Models\Commission;
use EasyAffiliate\Models\Creative;
use EasyAffiliate\Models\Transaction;
use EasyAffiliate\Models\User;

/** Class used for tracking clicks, sales and more */
class Track {
  /** Tracks a click where it is appropriate to do so */
  public static function click($affiliate_id, $creative_id = 0) {
    $user_agent = isset($_SERVER['HTTP_USER_AGENT']) && is_string($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

    if(Utils::is_user_agent_bot($user_agent)) {
      return false;
    }

    $click = new Click();
    $click->affiliate_id = $affiliate_id;
    $click->link_id = $creative_id;

    if(($user = $click->user()) && $user->is_affiliate && !$user->is_blocked) {
      $click->ip = $_SERVER['REMOTE_ADDR'];
      $click->referrer = isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'';

      $click->uri = $_SERVER['REQUEST_URI'];
      $click->browser = $user_agent;

      $old_affiliate_id = Cookie::get_affiliate_id();
      $click->first_click = !$old_affiliate_id || $old_affiliate_id != $affiliate_id;

      do_action('esaf_pre_track_click', $click, $user);

      if(!empty($click->affiliate_id)) {
        $click->store();
        Cookie::set($affiliate_id, $click->id);
        do_action('esaf-setcookie', $affiliate_id, $click->id);
      }

      return $click->id;
    }

    return false;
  }

  public static function redirect($creative_id, $affiliate_id) {
    $creative = new Creative($creative_id);

    Track::click($affiliate_id, $creative_id);

    // 301's can interfere with tracking by caching the redirect so we're doing a 307/302 here
    if($_SERVER['SERVER_PROTOCOL'] == 'HTTP/1.0') {
      header('HTTP/1.1 302 Found');
    }
    else {
      header('HTTP/1.1 307 Temporary Redirect');
    }

    header('Location: ' . apply_filters('esaf_affiliate_target_url', $creative->url, $affiliate_id, $creative_id));
    exit;
  }

  /** Attempt to track a sale */
  public static function sale( $source,
                               $sale_amount,
                               $trans_num,
                               $item_id='',
                               $item_name='',
                               $order_id=0,
                               $coupon='',
                               $user_id=0,
                               $sub_num='',
                               $sub_paynum=0,
                               $refund_amount=0.00,
                               $ip_addr='',
                               $timeout='',
                               $delete_cookie=false,
                               $cust_name='',
                               $cust_email='',
                               $status = 'complete') {

    //need an amount and an order_id/trans_num
    if(empty($source) || empty($sale_amount) || $sale_amount <= 0 || empty($trans_num)) { return false; }

    // We'll maybe save this during the tracking process
    $transaction = new Transaction();
    $transaction->source        = $source;
    $transaction->sale_amount   = $sale_amount;
    $transaction->trans_num     = $trans_num;
    $transaction->order_id      = $order_id;
    $transaction->item_id       = $item_id;
    $transaction->item_name     = $item_name;
    $transaction->coupon        = $coupon;
    $transaction->subscr_id     = $sub_num;
    $transaction->subscr_paynum = $sub_paynum;
    $transaction->rebill        = $sub_paynum > 1;
    $transaction->cust_name     = $cust_name;
    $transaction->cust_email    = $cust_email;
    $transaction->status        = $status;
    $transaction->ip_addr       = !empty($ip_addr) ? $ip_addr : $_SERVER['REMOTE_ADDR'];
    $transaction->affiliate_id  = Cookie::get_affiliate_id();
    $transaction->click_id      = Cookie::get_click_id();

    // If it already exists, why are we here?
    if(Transaction::get_one_by_trans_num($transaction->trans_num)) {
      return false;
    }

    do_action('esaf_pre_track_sale', $transaction, $user_id);

    // Store the affiliate_id with the usermeta if no referrer already exists
    if($user_id && is_numeric($user_id)) {
      $customer = new User($user_id);

      if($customer->ID > 0) {
        $transaction->cust_name = $customer->full_name();
        $transaction->cust_email = $customer->user_email;

        if(empty($customer->referrer) && !empty($transaction->affiliate_id) && is_numeric($transaction->affiliate_id)) {
          $customer->referrer = $transaction->affiliate_id;
          $customer->store();
        }
      }
    }

    // If there's a timeout value set then make sure we know its set
    // Timeouts are here to prevent users from refreshing the page and having commissions tracked
    if(!empty($timeout) && is_numeric($timeout) && isset($_COOKIE['esaf_timeout'])) {
      return false;
    }

    if(empty($transaction->affiliate_id) || !is_numeric($transaction->affiliate_id)) {
      return false;
    }

    $affiliate = new User($transaction->affiliate_id);

    if(empty($affiliate->ID) || !$affiliate->is_affiliate || $affiliate->is_blocked) {
      return false;
    }

    $commission_amount = Commission::calculate_total($affiliate, $transaction);

    if(Commission::should_pay($affiliate, $transaction)) {
      if(number_format($commission_amount,2) != '0.00') { // Accept positive or negative amount
        $transaction->type = 'commission';

        if($delete_cookie) {
          Cookie::delete();
        }
        elseif(!empty($timeout) && is_numeric($timeout)) {
          Utils::set_cookie('esaf_timeout', '1', time() + $timeout);
        }
      }
      else {
        return false; // No commission due
      }
    }
    else {
      return false;
    }

    $transaction->apply_refund($refund_amount);
    $transaction_id = $transaction->store();

    if(is_wp_error($transaction_id)) {
      Utils::error_log($transaction_id->get_error_message());
      return false;
    }

    return $transaction_id;
  }

  /**
   * Create a pending transaction
   */
  public static function pending_sale(
    $source,
    $sale_amount,
    $trans_num,
    $item_id='',
    $item_name='',
    $order_id=0,
    $coupon='',
    $user_id=0,
    $sub_num='',
    $sub_paynum=0,
    $refund_amount=0.00,
    $ip_addr='',
    $timeout='',
    $delete_cookie=false,
    $cust_name='',
    $cust_email=''
  ) {
    Track::sale(
      $source,
      $sale_amount,
      $trans_num,
      $item_id,
      $item_name,
      $order_id,
      $coupon,
      $user_id,
      $sub_num,
      $sub_paynum,
      $refund_amount,
      $ip_addr,
      $timeout,
      $delete_cookie,
      $cust_name,
      $cust_email,
      'pending'
    );
  }

  /**
   * Get the affiliate ID from the URL tracking parameter
   *
   * @return int|null The affiliate ID, or null if not found
   */
  public static function get_affiliate_id() {
    if(isset($_REQUEST['aff']) && is_string($_REQUEST['aff'])) {
      $id_or_login = sanitize_text_field(wp_unslash($_REQUEST['aff']));

      if(is_numeric($id_or_login)) {
        $affiliate = new User($id_or_login);
      }
      else {
        $affiliate = new User();
        $affiliate->load_user_data_by_login($id_or_login);
      }

      // Check that the user exists and is an affiliate that isn't blocked
      if(!empty($affiliate->ID) && $affiliate->is_affiliate && !$affiliate->is_blocked) {
        return $affiliate->ID;
      }
    }

    // For backwards compatibility with AffiliateWP
    $affiliate_wp_referral_var = get_option('esaf_affiliate_wp_referral_var');

    if(!empty($affiliate_wp_referral_var)) {
      $affiliate_wp_id_or_login = 0;

      if(isset($_REQUEST[$affiliate_wp_referral_var]) && is_string($_REQUEST[$affiliate_wp_referral_var])) {
        $affiliate_wp_id_or_login = sanitize_text_field(wp_unslash($_REQUEST[$affiliate_wp_referral_var]));
      }
      else {
        $request_uri = !empty($_SERVER['REQUEST_URI' ]) ? $_SERVER['REQUEST_URI' ] : '';

        if(false !== strpos($request_uri, $affiliate_wp_referral_var . '/')) {
          $pieces = explode('/', str_replace('?', '/', $request_uri));
          $key = array_search($affiliate_wp_referral_var, $pieces);

          if($key && isset($pieces[$key + 1])) {
            $affiliate_wp_id_or_login = sanitize_text_field(urldecode($pieces[$key + 1]));
          }
        }
      }

      if($affiliate_wp_id_or_login) {
        if(is_numeric($affiliate_wp_id_or_login)) {
          global $wpdb;

          $affiliate_id = $wpdb->get_var($wpdb->prepare("SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key = 'esaf_affiliate_wp_affiliate_id' AND meta_value = %d", $affiliate_wp_id_or_login));

          if(empty($affiliate_id)) {
            return null;
          }

          $affiliate = new User($affiliate_id);
        }
        else {
          $affiliate = new User();
          $affiliate->load_user_data_by_login($affiliate_wp_id_or_login);
        }

        // Check that the user exists and is an affiliate that isn't blocked
        if(!empty($affiliate->ID) && $affiliate->is_affiliate && !$affiliate->is_blocked) {
          return $affiliate->ID;
        }
      }
    }

    return null;
  }
}
