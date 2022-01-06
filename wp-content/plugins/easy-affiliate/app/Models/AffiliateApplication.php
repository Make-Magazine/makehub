<?php

namespace EasyAffiliate\Models;

use EasyAffiliate\Lib\CptModel;
use EasyAffiliate\Lib\Utils;

class AffiliateApplication extends CptModel {
  public static $cpt = 'esaf-application';
  public $statuses;

  public function __construct($obj = null) {
    $default_uuid = Utils::random_string(16);
    $this->load_cpt(
      $obj,
      self::$cpt,
      [
        'uuid'        => ['default' => $default_uuid, 'type' => 'string'],
        'first_name'  => ['default' => '',            'type' => 'string'],
        'last_name'   => ['default' => '',            'type' => 'string'],
        'email'       => ['default' => '',            'type' => 'string'],
        'websites'    => ['default' => '',            'type' => 'string'],
        'strategy'    => ['default' => '',            'type' => 'string'],
        'social'      => ['default' => '',            'type' => 'string'],
        'status'      => ['default' => 'pending',     'type' => 'string'],
        'affiliate'   => ['default' => 0,             'type' => 'integer'],
      ]
    );

    $this->statuses = [
      'pending', 'approved', 'ignored'
    ];
  }

  public function validate() {
    $this->validate_not_empty($this->first_name, 'first_name');
    $this->validate_not_empty($this->last_name, 'last_name');
    $this->validate_not_empty($this->email, 'email');
    $this->validate_is_email($this->email, 'email');

    if(apply_filters('esaf_application_websites_required', true)) {
      $this->validate_not_empty($this->websites, 'websites');
    }

    if(apply_filters('esaf_application_strategy_required', true)) {
      $this->validate_not_empty($this->strategy, 'strategy');
    }

    $this->validate_is_in_array($this->status, $this->statuses, 'status');
    $this->validate_is_numeric($this->affiliate, 0, null, 'affiliate');
  }

  /** STATIC CRUD METHODS **/
  public static function get_one($args) {
    return self::get_one_by_class(AffiliateApplication::class, $args);
  }

  public static function get_all($order_by = '', $limit = '', $args = []) {
    return self::get_all_by_class(AffiliateApplication::class, $order_by, $limit, $args);
  }

  public static function get_count($args = []) {
    return self::get_count_by_class(AffiliateApplication::class, $args);
  }

  public static function get_one_by_uuid($uuid) {
    global $wpdb;

    $app = new AffiliateApplication();

    $q = $wpdb->prepare("
        SELECT pm.post_id
          FROM {$wpdb->postmeta} AS pm
         WHERE pm.meta_key=%s
           AND pm.meta_value=%s
         LIMIT 1
      ",
      $app->uuid_str,
      $uuid
    );

    $app_id = $wpdb->get_var($q);

    if(empty($app_id)) {
      return false;
    }
    else {
      return new AffiliateApplication($app_id);
    }
  }

  public static function get_one_by_affiliate($affiliate) {
    global $wpdb;

    $app = new AffiliateApplication();

    $q = $wpdb->prepare("
        SELECT pm.post_id
          FROM {$wpdb->postmeta} AS pm
         WHERE pm.meta_key=%s
           AND pm.meta_value=%s
         LIMIT 1
      ",
      $app->affiliate_str,
      $affiliate
    );

    $app_id = $wpdb->get_var($q);

    if(empty($app_id)) {
      return false;
    }
    else {
      return new AffiliateApplication($app_id);
    }
  }

  /** Get the count of affiliate applications with a given status */
  public static function get_status_count($status = 'pending') {
    global $wpdb;

    $app = new AffiliateApplication();

    $q = $wpdb->prepare("
        SELECT COUNT(*)
          FROM (
            SELECT DISTINCT pm.post_id
              FROM {$wpdb->postmeta} AS pm
             WHERE pm.meta_key=%s
               AND pm.meta_value=%s
               AND pm.post_id IN (
                 SELECT p.ID
                   FROM {$wpdb->posts} AS p
                  WHERE p.post_type=%s
                    AND p.post_status='publish'
               )
          ) AS app_count
      ",
      $app->status_str,
      $status,
      self::$cpt
    );

    $count = $wpdb->get_var($q);

    return (int)$count;
  }

  public static function get_available_statuses() {
    global $wpdb;

    $app = new AffiliateApplication();

    $q = $wpdb->prepare("
      SELECT DISTINCT pm.meta_value
        FROM {$wpdb->postmeta} AS pm
       WHERE pm.meta_key=%s
         AND pm.post_id IN (
           SELECT p.ID
             FROM {$wpdb->posts} AS p
            WHERE p.post_type=%s
              AND p.post_status='publish')
      ",
      $app->status_str,
      self::$cpt
    );

    $statuses = $wpdb->get_col($q);

    return $statuses;
  }

  public function signup_url() {
    return Utils::signup_url(['application' => urlencode($this->uuid)]);
  }

  public function edit_url() {
    return admin_url("post.php?post={$this->ID}&action=edit");
  }

  /** This method will return true if the application is ready to be used for signup */
  public function ready() {
    $user = false;
    if($this->affiliate > 0) {
      $user = new User($this->affiliate);
    }

    return ($this->status=='approved' && (false===$user || !$user->is_affiliate));
  }

  /**
   * Validate the affiliate application form values
   *
   * @param array $values
   * @return array
   */
  public static function validate_affiliate_application_form($values) {
    $application = new AffiliateApplication();
    $errors = [];

    if(empty($values[$application->first_name_str])) {
      $errors[] = __('You must enter a First Name','easy-affiliate');
    }

    if(empty($values[$application->last_name_str])) {
      $errors[] = __('You must enter a Last Name','easy-affiliate');
    }

    if(empty($values[$application->email_str])) {
      $errors[] = __('You must enter an Email Address','easy-affiliate');
    }
    elseif(!is_email($values[$application->email_str])) {
      $errors[] = __('Email must be a real and properly formatted email address','easy-affiliate');
    }

    if(empty($values[$application->websites_str])) {
      if(apply_filters('esaf_application_websites_required', true)) {
        $errors[] = __('You must enter at least one Promotion Website','easy-affiliate');
      }
    }
    else {
      $websites = array_map('trim', explode("\n", $values[$application->websites_str]));

      foreach($websites as $website) {
        if($website !== '' && !Utils::is_url($website)) {
          $errors[] = __('Each Promotion Website must be a valid URL starting with https:// or http://','easy-affiliate');
          break;
        }
      }
    }

    if(empty($values[$application->strategy_str])) {
      if(apply_filters('esaf_application_strategy_required', true)) {
        $errors[] = __('You must enter a Promotion Strategy', 'easy-affiliate');
      }
    }

    if(!empty($values['wafp_honeypot'])) {
      $errors[] = __('You must be a human to sign up for this site', 'easy-affiliate');
    }

    return $errors;
  }
}
