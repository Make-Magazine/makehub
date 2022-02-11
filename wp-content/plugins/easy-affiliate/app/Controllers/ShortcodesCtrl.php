<?php

namespace EasyAffiliate\Controllers;

use EasyAffiliate\Helpers\DashboardHelper;
use EasyAffiliate\Lib\BaseCtrl;
use EasyAffiliate\Lib\Cookie;
use EasyAffiliate\Lib\Track;
use EasyAffiliate\Lib\Utils;
use EasyAffiliate\Models\Transaction;
use EasyAffiliate\Models\User;

class ShortcodesCtrl extends BaseCtrl {
  public function load_hooks() {
    // Deprecated wafp_* shortcodes
    add_shortcode('wafp_ipn', [self::class, 'get_ipn']);
    add_shortcode('wafp_custom_args', [self::class, 'get_custom_args']);
    add_shortcode('wafp_show_if_referred', [self::class, 'show_if_referred']);
    add_shortcode('wafp_show_if_is_affiliate', [self::class, 'show_if_is_affiliate']);
    add_shortcode('wafp_show_affiliate_info', [self::class, 'show_affiliate_info']);
    add_shortcode('wafp_dashboard_nav', [self::class, 'show_nav']);
    // End Deprecated wafp_* shortcodes

    // PayPal Integration shortcodes
    add_shortcode('esaf_ipn', [self::class, 'get_ipn']);
    add_shortcode('esaf_custom_args', [self::class, 'get_custom_args']);

    // Affiliate-related shortcodes
    add_shortcode('esaf_show_if_referred', [self::class, 'show_if_referred']);
    add_shortcode('esaf_show_if_is_affiliate', [self::class, 'show_if_is_affiliate']);
    add_shortcode('esaf_show_affiliate_info', [self::class, 'show_affiliate_info']);
    add_shortcode('esaf_affiliate_id', [self::class, 'affiliate_id']);

    // Content-related shortcodes
    add_shortcode('esaf_dashboard_nav', [self::class, 'show_nav']);
    add_shortcode('esaf_dashboard', [self::class, 'dashboard']);
    add_shortcode('esaf_signup', [self::class, 'signup']);
    add_shortcode('esaf_login', [self::class, 'login']);
    add_shortcode('esaf_affiliate_application', [self::class, 'affiliate_application']);
  }

  public static function dashboard() {
    // Currently we only allow the [esaf_dashboard] shortcode on the Dashboard page
    if(!Utils::is_dashboard_page()) {
      return '';
    }

    ob_start();

    $dashboard_ctrl = new DashboardCtrl();
    $dashboard_ctrl->route();

    return ob_get_clean();
  }

  public static function signup() {
    ob_start();

    $signup_ctrl = new SignupCtrl();
    $signup_ctrl->route();

    return ob_get_clean();
  }

  public static function login() {
    ob_start();

    $login_ctrl = new LoginCtrl();
    $login_ctrl->route();

    return ob_get_clean();
  }

  public static function affiliate_application() {
    ob_start();

    $affiliate_application_ctrl = new AffiliateApplicationCtrl();
    $affiliate_application_ctrl->route();

    return ob_get_clean();
  }

  public static function show_nav($atts, $content = '') {
    DashboardHelper::nav();
  }

  // Shows text wrapped in this shortcode if an affiliate cookie is set
  public static function show_if_referred($atts, $content = '') {
    if(Cookie::get_affiliate_id() > 0) {
      return $content;
    }

    return false;
  }

  // Shows affiliate info if wafp_click is set
  // show="" can be anything listed here: http://codex.wordpress.org/Function_Reference/get_userdata
  public static function show_affiliate_info($atts, $content = '') {
    if(Cookie::get_affiliate_id() > 0) {
      $user_data = get_userdata(Cookie::get_affiliate_id());

      $show = $atts['show'];

      if(isset($user_data->{$show}) && !empty($user_data->{$show})) {
        return $user_data->{$show};
      }
    }

    return '';
  }

  public static function affiliate_id() {
    return Cookie::get_affiliate_id();
  }

  public static function show_if_is_affiliate($atts, $content = '') {
    global $user_ID;

    //User isn't logged in so don't show the content
    if(!isset($user_ID) || (int)$user_ID <= 0) {
      return '';
    }

    $wafp_user = new User($user_ID);

    if($wafp_user->is_affiliate) {
      return $content;
    }

    return '';
  }

  public static function get_ipn($atts) {
    $ipn = ESAF_SCRIPT_URL . "&controller=paypal&action=ipn";

    $ipn = apply_filters('esaf_paypal_ipn', $ipn, $atts);

    if(isset($atts['urlencode']) && $atts['urlencode'] == 'true') {
      return urlencode($ipn);
    }

    return '<input type="hidden" name="notify_url" value="' . esc_attr($ipn) . '" />';
  }

  public static function get_custom_args($atts) {
    $custom_args = '';
    $ip_addr = $_SERVER['REMOTE_ADDR'];

    // Setup the cookie artificially if the affiliate URL parameter is present
    $affiliate_id = Track::get_affiliate_id();

    if(!empty($affiliate_id)) {
      Cookie::override($affiliate_id);
    }

    $custom_args = [];
    $affiliate_id = Cookie::get_affiliate_id();
    $click_id = Cookie::get_click_id();

    if($affiliate_id > 0) {
      $custom_args[] = "aff_id={$affiliate_id}";
    }

    if($click_id > 0) {
      $custom_args[] = "click_id={$click_id}";
    }

    if(!empty($ip_addr)) {
      $custom_args[] = "ip_addr={$ip_addr}";
    }

    $custom_args = join('&', $custom_args);
    $custom_args = apply_filters('esaf_paypal_custom_args', $custom_args, $atts);

    if(isset($atts['urlencode']) && $atts['urlencode'] == 'true') {
      return urlencode($custom_args);
    }

    return '<input type="hidden" name="custom" value="' . esc_attr($custom_args) . '" />';
  }
} //End class
