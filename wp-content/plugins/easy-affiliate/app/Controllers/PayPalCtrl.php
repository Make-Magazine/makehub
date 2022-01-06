<?php

namespace EasyAffiliate\Controllers;

use EasyAffiliate\Lib\BaseCtrl;
use EasyAffiliate\Lib\Cookie;
use EasyAffiliate\Lib\Track;
use EasyAffiliate\Lib\Utils;
use EasyAffiliate\Models\Options;
use EasyAffiliate\Models\Transaction;
use EasyAffiliate\Models\User;

/** This is a special controller that handles all of the PayPal specific
  * public static functions for the Affiliate Program.
  */
class PayPalCtrl extends BaseCtrl {
  public static $sandbox_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
  public static $live_url = 'https://www.paypal.com/cgi-bin/webscr';

  public function load_hooks() {
    $options = Options::fetch();

    if(!in_array('paypal', $options->integration)) {
      return;
    }

    add_action('esaf_process_route', [self::class, 'listener']);
  }

  public static function listener() {
    if(isset($_REQUEST['plugin']) && $_REQUEST['plugin'] == 'wafp' &&
       isset($_REQUEST['controller']) && $_REQUEST['controller'] == 'paypal' &&
       isset($_REQUEST['action']) && $_REQUEST['action'] == 'ipn') {
      $_POST = stripslashes_deep($_POST);

      if((self::_valid_ip() || self::_validate_message()) && self::_valid_email()) {
        self::_process_message();
        self::_pass_along_message();
        do_action('esaf_pass_ipn_message');
      }

      exit;
    }
  }

  public static function _process_message() {
    $affiliate_id = 0;
    $click_id = 0;
    $remote_ip_addr = '';

    // Try to get the affiliate ID from the custom variable
    if(isset($_POST['custom'])) {
      $custom = wp_parse_args($_POST['custom']);

      if(isset($custom['aff_id']) && is_numeric($custom['aff_id'])) {
        $affiliate_id = (int) $custom['aff_id'];
      }

      if(isset($custom['click_id']) && is_numeric($custom['click_id'])) {
        $click_id = (int) $custom['click_id'];
      }

      if(isset($custom['ip_addr']) && is_string($custom['ip_addr'])) {
        $remote_ip_addr = sanitize_text_field($custom['ip_addr']);
      }
    }

    if(
      !empty($_POST['subscr_id']) &&
      is_string($_POST['subscr_id']) &&
      !empty($_POST['txn_type']) &&
      is_string($_POST['txn_type']) &&
      $_POST['txn_type'] == 'subscr_payment'
    ) {
      $subscr_id = sanitize_text_field($_POST['subscr_id']);
      $first_transaction = Transaction::get_first_by_subscription_id($subscr_id);
      $subscr_paynum = Transaction::get_count_by_subscription_id($subscr_id) + 1;

      if($first_transaction instanceof Transaction) {
        if(empty($affiliate_id)) {
          $affiliate_id = (int) $first_transaction->affiliate_id;
        }

        if(empty($click_id)) {
          $click_id = (int) $first_transaction->click_id;
        }

        if(empty($remote_ip_addr)) {
          $remote_ip_addr = $first_transaction->ip_addr;
        }
      }
    }

    if(empty($affiliate_id) || empty($_POST['payment_status'])) {
      return false;
    }

    if($_POST['payment_status'] == 'Completed') {
      $affiliate = new User($affiliate_id);

      if($affiliate->ID > 0 && $affiliate->is_affiliate) {
        Cookie::override($affiliate->ID, $click_id);

        $gross = isset($_POST['mc_gross']) ? (float) $_POST['mc_gross'] : 0.00;
        $shipping = isset($_POST['shipping']) ? (float) $_POST['shipping'] : 0.00;
        $tax = isset($_POST['tax']) ? (float) $_POST['tax'] : 0.00;

        Track::sale(
          'paypal',
          ($gross - ($shipping + $tax)),
          $_POST['txn_id'],
          '',
          sanitize_text_field($_POST['item_name']),
          0,
          '',
          0,
          !empty($subscr_id) ? $subscr_id : '',
          !empty($subscr_paynum) ? $subscr_paynum : 0,
          0.00,
          $remote_ip_addr,
          '',
          false,
          sanitize_text_field($_POST['first_name']) . ' ' . sanitize_text_field($_POST['last_name']),
          sanitize_text_field($_POST['payer_email'])
        );
      }
    }
    elseif($_POST['payment_status'] == 'Refunded') {
      $og_transaction = Transaction::get_one_by_trans_num($_POST['parent_txn_id']);

      if($og_transaction instanceof Transaction) {
        // Because the "Refunded" IPN data does not include the tax amount we cannot accurately track partial refunds
        // so we'll just void the entire transaction.
        $og_transaction->apply_refund($og_transaction->sale_amount);
        $og_transaction->store();
      }
    }

    return true;
  }

  /**
   * Pass along the IPN message if there are more destinations
   */
  public static function _pass_along_message() {
    $options = Options::fetch();
    if(empty($options->paypal_dst)) { return; }

    $params = [
      'body'    => $_POST,
      'sslverify' => false,
      'timeout'   => 30,
    ];

    $urls = array_map('trim',explode("\n", $options->paypal_dst));
    foreach ($urls as $url) {
      wp_remote_post($url, $params);
    }
  }

  /**
   * Validate the message by checking with PayPal to make sure they really
   * sent it
   */
  public static function _validate_message() {
    $options = Options::fetch();

    // Set the command that is used to validate the message
    $_POST['cmd'] = "_notify-validate";

    // We need to send the message back to PayPal just as we received it
    $params = [
      'method'      => 'POST',
      'body'        => $_POST,
      'headers'     => ['connection' => 'close'],
      'httpversion' => 1.1,
      'sslverify'   => true,
      'user-agent'  => 'EasyAffiliate/' . ESAF_VERSION,
      'timeout'     => 30
    ];

    $url = $options->paypal_sandbox?self::$sandbox_url:self::$live_url;

    $resp = wp_remote_post($url, $params);

    self::_email_status("PayPal IPN Server\n" . Utils::array_to_string($_SERVER, true) . "\n");
    self::_email_status("PayPal IPN Parameters\n" . Utils::array_to_string($params, true) . "\n");
    self::_email_status("PayPal IPN Response\n" . Utils::array_to_string($resp, true) . "\n");

    // Put the $_POST data back to how it was so we can pass it to the action
    unset($_POST['cmd']);

    // If the response was valid, check to see if the request was valid
    if( !is_wp_error($resp) and
        $resp['response']['code'] >= 200 and
        $resp['response']['code'] < 300 and
        (strcmp( $resp['body'], "VERIFIED") == 0) ) {
      return true;
    }

    self::_email_status("PayPal IPN Processing\n" . Utils::array_to_string($_POST, true) . "\n");

    return false;
  }

  /**
   * Validate REMOTE_ADDR
   */
  public static function _valid_ip() {
    $options = Options::fetch();

    if(empty($options->paypal_src)) {
      return false;
    }

    $ips = array_map('trim', explode(',', $options->paypal_src));
    $ip_valid = in_array($_SERVER['REMOTE_ADDR'], $ips);

    self::_email_status("IPs\n" . Utils::object_to_string($ips) . "\nREMOTE IP\n" . $_SERVER['REMOTE_ADDR'] . "\nREMOTE IP IN ARRAY\n" . ($ip_valid?"YES":"NO"));

    return $ip_valid;
  }

  public static function _valid_email() {
    $options = Options::fetch();

    if(empty($options->paypal_emails)) { return true; }

    $emails = array_map('trim', explode( ',', $options->paypal_emails ));
    $email_valid = (in_array( $_REQUEST['receiver_email'], $emails ) or in_array( $_REQUEST['business'], $emails ));
    self::_email_status("Emails\n" . Utils::object_to_string($emails) . "\n");

    return $email_valid;
  }

  public static function _email_status($message) {
    $wafp_blogname = Utils::blogname();

    $debug = get_option('wafp-paypal-debug');

    if($debug) {
      /* translators: In this string, %s is the Blog Name/Title */
      $subject = sprintf( __("[%s] PayPal Debug Email", 'easy-affiliate'), $wafp_blogname);

      Utils::wp_mail_to_admin($subject, $message);
    }
  }
}
