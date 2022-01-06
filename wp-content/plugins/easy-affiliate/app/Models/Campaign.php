<?php

namespace EasyAffiliate\Models;

use EasyAffiliate\Lib\CtaxModel;

class Campaign extends CtaxModel {
  public static $ctax = 'esaf-campaign';

  public function __construct($obj = null) {
    $this->load_ctax(
      $obj,
      self::$ctax,
      [
        'expires_at'   => ['default' => '',    'type' => 'datetime'],
        'user_limited' => ['default' => false, 'type' => 'bool'],
        'user_id'      => ['default' => 0,     'type' => 'integer']
      ]
    );
  }

  public function validate() {
    $this->validate_is_bool($this->user_limited, 'user_limited');

    if($this->user_limited) {
      $this->validate_is_numeric($this->user_id, 1, null, 'user_id');
    }
  }

  public static function get_one($args) {
    return self::get_one_by_class(Campaign::class, $args);
  }

  public static function get_all($order_by = '', $limit = '', $args = []) {
    return self::get_all_by_class(Campaign::class, $order_by, $limit, $args);
  }

  public static function get_count($args = []) {
    return self::get_count_by_class(Campaign::class, $args);
  }

  public static function get_one_by_slug($slug) {
    return self::get_one(['t.slug'=>$slug]);
  }

  public static function get_all_by_creative_id($creative_id) {
    global $wpdb;

    $q = $wpdb->prepare("
        SELECT term.term_id
          FROM {$wpdb->terms} AS term
          JOIN {$wpdb->term_taxonomy} AS tax
            ON term.term_id=tax.term_id
           AND tax.taxonomy=%s
          JOIN {$wpdb->term_relationships} AS rel
            ON rel.term_taxonomy_id=tax.term_taxonomy_id
         WHERE rel.object_id=%d
      ",
      Campaign::$ctax,
      $creative_id
    );

    $term_ids = $wpdb->get_col($q);

    $campaigns = [];
    if(!empty($term_ids)) {
      foreach($term_ids as $term_id) {
        $campaigns[] = new Campaign($term_id);
      }
    }

    return $campaigns;
  }
}
