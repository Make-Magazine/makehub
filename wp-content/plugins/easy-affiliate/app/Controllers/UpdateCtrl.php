<?php

namespace EasyAffiliate\Controllers;

use EasyAffiliate\Lib\BaseCtrl;
use EasyAffiliate\Lib\Utils;
use EasyAffiliate\Models\Options;

class UpdateCtrl extends BaseCtrl
{
  public static $activate_from_define_response;

  public function load_hooks() {
    add_filter('pre_set_site_transient_update_plugins', [self::class, 'queue_update']);
    add_filter('plugins_api', [self::class, 'plugin_info'], 11, 3);
    add_action('admin_notices', [self::class, 'activation_warning']);
    add_action('admin_init', [self::class, 'activate_from_define']);
  }

  public static function activate_from_define() {
    $options = Options::fetch();

    if(defined('EASY_AFFILIATE_LICENSE_KEY') && $options->mothership_license != EASY_AFFILIATE_LICENSE_KEY ) {
      $lk = EASY_AFFILIATE_LICENSE_KEY;
    }
    elseif(defined('MEMBERPRESS_LICENSE_KEY') && $options->mothership_license != MEMBERPRESS_LICENSE_KEY ) {
      $lk = MEMBERPRESS_LICENSE_KEY;
    }
    else {
      return;
    }

    $message = '';
    $errors = [];
    $options->mothership_license = stripslashes($lk);
    $domain = urlencode(Utils::site_domain());

    try {
      $args = compact('domain');

      if(!empty($options->mothership_license)) {
        $act = self::send_mothership_request("/license_keys/deactivate/{$options->mothership_license}", $args, 'post');
      }

      $act = self::send_mothership_request("/license_keys/activate/{$lk}", $args, 'post');

      self::manually_queue_update();

      // If we're using defines then we have to do this with defines too
      $options->edge_updates = false;
      $options->store();

      $message = $act['message'];

      self::$activate_from_define_response = [
        'type' => 'message',
        'message' => $message
      ];
    }
    catch(\Exception $e) {
      self::$activate_from_define_response = [
        'type' => 'error',
        'error' => $e->getMessage()
      ];
    }

    add_action('admin_notices', [self::class, 'activate_from_define_notice']);
  }

  public static function activate_from_define_notice() {
    if(is_array(self::$activate_from_define_response)) {
      if(self::$activate_from_define_response['type'] == 'message') {
        $message = self::$activate_from_define_response['message'];
        require ESAF_VIEWS_PATH . '/shared/errors.php';
      }
      elseif (self::$activate_from_define_response['type'] == 'error') {
        $error = self::$activate_from_define_response['error'];
        require ESAF_VIEWS_PATH . '/update/activation_warning.php';
      }
    }
  }

  public static function queue_update($transient, $force = false) {
    $options = Options::fetch();

    if($force || (false === ($update_info = get_site_transient('esaf_update_info')))) {
      $args = [];

      if($options->edge_updates || (defined('EASY_AFFILIATE_EDGE') && EASY_AFFILIATE_EDGE)) {
        $args['edge'] = 'true';
      }

      if(empty($options->mothership_license)) {
        try {
          // Just here to query for the current version
          $version_info = self::send_mothership_request('/versions/latest/' . ESAF_EDITION, $args);
          $curr_version = $version_info['version'];
          $download_url = '';
        }
        catch(\Exception $e) {
          if(isset($transient->response[ESAF_PLUGIN_SLUG])) {
            unset($transient->response[ESAF_PLUGIN_SLUG]);
          }

          return $transient;
        }
      }
      else {
        try {
          $args['domain'] = urlencode(Utils::site_domain());

          $license_info = self::send_mothership_request('/versions/info/' . ESAF_EDITION . "/{$options->mothership_license}", $args, 'get');
          $curr_version = $license_info['version'];
          $download_url = $license_info['url'];
          set_site_transient('wafp_license_info', $license_info, Utils::hours(12));
        }
        catch(\Exception $e) {
          try {
            // Just here to query for the current version
            $version_info = self::send_mothership_request('/versions/latest/' . ESAF_EDITION, $args);
            $curr_version = $version_info['version'];
            $download_url = '';
          }
          catch(\Exception $e) {
            if(isset($transient->response[ESAF_PLUGIN_SLUG])) {
              unset($transient->response[ESAF_PLUGIN_SLUG]);
            }

            return $transient;
          }
        }
      }

      set_site_transient(
        'esaf_update_info',
        compact('curr_version', 'download_url'),
        Utils::hours(12)
      );
    }
    else {
      extract($update_info);
    }

    if(isset($curr_version) && version_compare($curr_version, ESAF_VERSION, '>')) {
      global $wp_version;

      $transient->response[ESAF_PLUGIN_SLUG] = (object) [
        'id' => ESAF_PLUGIN_SLUG,
        'slug' => ESAF_PLUGIN_NAME,
        'plugin' => ESAF_PLUGIN_SLUG,
        'new_version' => $curr_version,
        'url' => 'https://easyaffiliate.com/',
        'package' => $download_url,
        'tested' => $wp_version
      ];
    }
    else {
      unset($transient->response[ESAF_PLUGIN_SLUG]);

      // Enables the "Enable auto-updates" link
      $transient->no_update[ESAF_PLUGIN_SLUG] = (object) [
        'id' => ESAF_PLUGIN_SLUG,
        'slug' => ESAF_PLUGIN_NAME,
        'plugin' => ESAF_PLUGIN_SLUG,
        'new_version' => ESAF_VERSION,
        'url' => 'https://easyaffiliate.com/',
        'package' => ''
      ];
    }

    return $transient;
  }

  public static function manually_queue_update() {
    $transient = get_site_transient("update_plugins");
    set_site_transient("update_plugins", self::queue_update($transient, true));
  }

  public static function plugin_info($api, $action, $args) {
    global $wp_version;

    if(!isset($action) || $action != 'plugin_information') {
      return $api;
    }

    if(!isset($args->slug) || $args->slug != ESAF_PLUGIN_NAME) {
      return $api;
    }

    $options = Options::fetch();
    $args = [];

    if($options->edge_updates || (defined('EASY_AFFILIATE_EDGE') && EASY_AFFILIATE_EDGE)) {
      $args['edge'] = 'true';
    }

    if(empty($options->mothership_license)) {
      try {
        // Just here to query for the current version
        $version_info = self::send_mothership_request('/versions/latest/' . ESAF_EDITION, $args);
        $curr_version = $version_info['version'];
        $version_date = $version_info['version_date'];
        $download_url = '';
      }
      catch(\Exception $e) {
        return $api;
      }
    }
    else {
      try {
        $args['domain'] = urlencode(Utils::site_domain());

        $license_info = self::send_mothership_request('/versions/info/' . ESAF_EDITION . "/{$options->mothership_license}", $args, 'get');
        $curr_version = $license_info['version'];
        $version_date = $license_info['version_date'];
        $download_url = $license_info['url'];
      }
      catch(\Exception $e) {
        try {
          // Just here to query for the current version
          $version_info = self::send_mothership_request("/versions/latest/".ESAF_EDITION, $args);
          $curr_version = $version_info['version'];
          $version_date = $version_info['version_date'];
          $download_url = '';
        }
        catch(\Exception $e) {
          return $api;
        }
      }
    }

    return (object) [
      'slug' => ESAF_PLUGIN_NAME,
      'name' => esc_html(ESAF_DISPLAY_NAME),
      'author' => ESAF_AUTHOR,
      'author_profile' => ESAF_AUTHOR_URI,
      'contributors' => [
        'caseproof' => [
          'profile' => ESAF_AUTHOR_URI,
          'avatar' => 'https://secure.gravatar.com/avatar/762b61e36276ff6dc0d7b03b8c19cfab?s=96&d=monsterid&r=g',
          'display_name' => ESAF_AUTHOR_NAME
        ]
      ],
      'homepage' => 'https://easyaffiliate.com/',
      'version' => $curr_version,
      'new_version' => $curr_version,
      'requires' => '5.2',
      'requires_php' => ESAF_MINIMUM_PHP_VERSION,
      'tested' => $wp_version,
      'compatibility' => [$wp_version => [$curr_version => [100, 0, 0]]],
      'rating' => '100.00',
      'num_ratings' => '1',
      'added' => '2012-12-02',
      'last_updated' => $version_date,
      'tags' => [
        'affiliate' => 'affiliate',
        'affiliate program' => 'affiliate program',
      ],
      'sections' => [
        'description' => '<p>' . ESAF_DESCRIPTION . '</p>',
        'faq' => '<p>' . sprintf(esc_html__('You can access in-depth information about Easy Affiliate at %1$sthe Easy Affiliate User Manual%2$s.', 'easy-affiliate'), '<a href="https://easyaffiliate.com/docs/">', '</a>') . '</p>',
        'changelog' => '<p>' . sprintf(esc_html__('See the %1$splugin changelog%2$s.', 'easy-affiliate'), '<a href="https://easyaffiliate.com/change-log/">', '</a>') . '</p>'
      ],
      'download_link' => $download_url
    ];
  }

  public static function send_mothership_request($endpoint, $args = [], $method = 'get', $blocking = true) {
    if(apply_filters('esaf_secure_mothership_request', true)) {
      $domain = 'https://mothership.caseproof.com';
    }
    else {
      $domain = 'http://mothership.caseproof.com';
    }

    $uri = "{$domain}{$endpoint}";

    $arg_array = [
      'method'    => strtoupper($method),
      'body'      => $args,
      'timeout'   => 15,
      'blocking'  => $blocking
    ];

    $resp = wp_remote_request($uri, $arg_array);

    // If we're not blocking then the response is irrelevant, so we'll just return true.
    if(!$blocking) {
      return true;
    }

    if(is_wp_error($resp)) {
      throw new \Exception(__('You had an HTTP error connecting to Caseproof\'s Mothership API', 'easy-affiliate'));
    }
    else {
      if(null !== ($json_res = json_decode($resp['body'], true))) {
        if(isset($json_res['error'])) {
          throw new \Exception($json_res['error']);
        }
        else {
          return $json_res;
        }
      }
      else {
        throw new \Exception(__('Your License Key was invalid', 'easy-affiliate'));
      }
    }
  }

  public static function activation_warning() {
    $options = Options::fetch();

    if(empty($options->mothership_license)) {
      require ESAF_VIEWS_PATH . '/update/activation_warning.php';
    }
  }

  public static function addons($return_object = false, $force = false, $all = false) {
    $options = Options::fetch();
    $license = $options->mothership_license;
    $transient = $all ? 'esaf_all_addons' : 'esaf_addons';

    if($force) {
      delete_site_transient($transient);
    }

    if($addons = get_site_transient($transient)) {
      $addons = json_decode($addons);
    }
    else {
      $addons = [];

      if(!empty($license)) {
        try {
          $domain = urlencode(Utils::site_domain());
          $args = compact('domain');

          if($all) {
            $args['all'] = 'true';
          }

          if($options->edge_updates || (defined("EASY_AFFILIATE_EDGE") && EASY_AFFILIATE_EDGE)) {
            $args['edge'] = 'true';
          }

          $addons = self::send_mothership_request('/versions/addons/' . ESAF_EDITION . "/{$license}", $args);
        }
        catch(\Exception $e) {
          // fail silently
        }
      }

      $json = json_encode($addons);
      set_site_transient($transient, $json, Utils::hours(12));

      if($return_object) {
        $addons = json_decode($json);
      }
    }

    return $addons;
  }
} //End class
