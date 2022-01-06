<?php

namespace EasyAffiliate\Lib;

use EasyAffiliate\Models\Options;

class Cookie {
  public static function set($affiliate_id, $click_id = 0) {
    $options = Options::fetch();
    $expires = time() + 60 * 60 * 24 * $options->expire_after_days;

    $value = $affiliate_id . '|' . $click_id . '|' . time() . '|' . $expires;
    $value .= '|' . self::get_hash($value);

    $secure = apply_filters('esaf_click_cookie_secure', is_ssl());
    $httponly = apply_filters('esaf_click_cookie_httponly', true);

    Utils::set_cookie('esaf_click', $value, $expires, $secure, $httponly);
  }

  public static function get_hash($data) {
    $algo = function_exists('hash') ? 'sha256' : 'sha1';
    $hash_key = hash_hmac($algo, $data, self::get_salt());
    $hash = hash_hmac($algo, $data, $hash_key);

    return $hash;
  }

  public static function override($affiliate_id, $click_id = 0) {
    $options = Options::fetch();
    $expires = time() + 60 * 60 * 24 * $options->expire_after_days;

    $value = $affiliate_id . '|' . $click_id . '|' . time() . '|' . $expires;
    $value .= '|' . self::get_hash($value);

    $_COOKIE['esaf_click'] = $value;
  }

  public static function clear() {
    unset($_COOKIE['esaf_click']);
  }

  public static function delete() {
    Utils::set_cookie('esaf_click', '', time() - HOUR_IN_SECONDS);
  }

  public static function parse() {
    if(!isset($_COOKIE['esaf_click']) && isset($_COOKIE['wafp_click'])) {
      return self::parse_legacy(); // Backwards compat
    }

    if(empty($_COOKIE['esaf_click']) || !is_string($_COOKIE['esaf_click'])) {
      return false;
    }

    $cookie = explode('|', wp_unslash($_COOKIE['esaf_click']));

    if(count($cookie) != 5) {
      return false;
    }

    list($affiliate_id, $click_id, $created, $expires, $hmac) = $cookie;

    if($expires < time()) {
      return false;
    }

    $value = $affiliate_id . '|' . $click_id . '|' . $created . '|' . $expires;
    $hash = self::get_hash($value);

    if(!hash_equals($hash, $hmac)) {
      return false;
    }

    return compact('affiliate_id', 'click_id', 'created', 'expires');
  }

  public static function parse_legacy() {
    if(empty($_COOKIE['wafp_click']) || !is_numeric($_COOKIE['wafp_click'])) {
      return false;
    }

    $install_time = get_option('esaf_install_time');

    if(!$install_time) {
      return false;
    }

    $options = Options::fetch();
    $expires = $install_time + 60 * 60 * 24 * $options->expire_after_days;

    if($expires < time()) {
      return false;
    }

    return [
      'affiliate_id' => $_COOKIE['wafp_click'],
      'click_id' => '0',
      'expires' => $expires
    ];
  }

  /**
   * Get the affiliate ID from the cookie
   *
   * @return int
   */
  public static function get_affiliate_id() {
    $cookie = self::parse();

    if($cookie) {
      return (int) $cookie['affiliate_id'];
    }

    return 0;
  }

  /**
   * Get the click ID from the cookie
   *
   * @return int
   */
  public static function get_click_id() {
    $cookie = self::parse();

    if($cookie) {
      return (int) $cookie['click_id'];
    }

    return 0;
  }

  public static function get_salt() {
    $salt = get_option('esaf_cookie_salt');

    if(!$salt) {
      $salt = wp_generate_password(128, true, true);
      update_option('esaf_cookie_salt', $salt);
    }

    return $salt;
  }
}
