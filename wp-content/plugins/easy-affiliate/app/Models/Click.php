<?php

namespace EasyAffiliate\Models;

use EasyAffiliate\Lib\BaseModel;
use EasyAffiliate\Lib\Db;
use EasyAffiliate\Lib\Utils;

class Click extends BaseModel {

  public function __construct($obj = null) {
    $attrs = apply_filters('esaf_click_attrs', [
      'id'           => ['default' => 0,    'type' => 'integer'],
      'ip'           => ['default' => '',   'type' => 'string'],
      'browser'      => ['default' => '',   'type' => 'string'],
      'referrer'     => ['default' => '',   'type' => 'string'],
      'uri'          => ['default' => '',   'type' => 'string'],
      'robot'        => ['default' => 0,    'type' => 'bool'],
      'first_click'  => ['default' => 0,    'type' => 'bool'],
      'created_at'   => ['default' => null, 'type' => 'datetime'],
      'link_id'      => ['default' => 0,    'type' => 'integer'],
      'affiliate_id' => ['default' => 0,    'type' => 'integer'],
    ]);

    $this->initialize($attrs, $obj);
  }

  public function validate() {
    if(!empty($this->ip)) {
      $this->validate_is_ip_addr($this->ip, 'ip');
    }

    $this->validate_not_empty($this->uri, 'uri');
    $this->validate_is_bool($this->robot, 'robot');
    $this->validate_is_bool($this->first_click, 'first_click');
    $this->validate_is_numeric($this->link_id, 0, null, 'link_id');
    $this->validate_is_numeric($this->affiliate_id, 0, null, 'affiliate_id');
  }

  public function store($validate = true) {
    if($validate) {
      try {
        $this->validate();
      }
      catch(\Exception $e) {
        return new \WP_Error(get_class($e), $e->getMessage());
      }
    }

    if(isset($this->id) && !is_null($this->id) && (int)$this->id > 0) {
      $this->id = self::update($this);
    }
    else {
      $this->id = self::create($this);
    }

    do_action('esaf_click_store', $this);

    return $this->id;
  }

  public function destroy() {
    $db = Db::fetch();
    do_action('esaf_click_destroy', $this);
    return $db->delete_records($db->clicks, ['id' => $this->id]);
  }

  public function user() {
    if(!is_numeric($this->affiliate_id) || $this->affiliate_id <= 0) {
      return false;
    }

    $user = new User($this->affiliate_id);

    if($user->ID <= 0) {
      return false;
    }

    return $user;
  }

  /** STATIC CRUD METHODS **/
  public static function get_one($args) {
    return self::get_one_by_class(Click::class, $args);
  }

  public static function get_all($order_by = '', $limit = '', $args = []) {
    return self::get_all_by_class(Click::class, $order_by, $limit, $args);
  }

  public static function get_count($args = []) {
    return self::get_count_by_class(Click::class, $args);
  }

  public static function create($click) {
    $db = Db::fetch();

    $args = (array) $click->get_values();
    unset($args['id']);

    return $db->create_record($db->clicks,$args);
  }

  public static function update($click) {
    $db = Db::fetch();
    return $db->update_record($db->clicks,$click->id,(array) $click->get_values());
  }

  public static function delete_by_affiliate_id($affiliate_id) {
    $db = Db::fetch();
    $args = compact( 'affiliate_id' );
    return $db->delete_records($db->clicks, $args);
  }

  public static function get_count_by_affiliate_id($affiliate_id) {
    $db = Db::fetch();
    return $db->get_count($db->clicks, compact('affiliate_id'));
  }

  public static function get_all_by_affiliate_id( $affiliate_id, $order_by='', $limit='' ) {
    return self::get_all($order_by, $limit, compact('affiliate_id'));
  }

  public static function get_all_by_link_id( $link_id, $order_by='', $limit='' ) {
    return self::get_all($order_by, $limit, compact('link_id'));
  }

  public static function get_all_ids_by_affiliate_id( $affiliate_id, $order_by='', $limit='' ) {
    global $wpdb;
    $db = Db::fetch();
    $query = "SELECT id FROM {$db->clicks} WHERE affiliate_id=%d {$order_by}{$limit}";
    $query = $wpdb->prepare($query, $affiliate_id);
    return $wpdb->get_col($query);
  }

  public static function get_first_click() {
    global $wpdb;
    $db = Db::fetch();

    $query = "
      SELECT *
        FROM {$db->clicks}
       ORDER BY created_at
       LIMIT 1
    ";

    $row = $wpdb->get_row($query);

    $click = new Click();
    $click->load_from_array((array) $row);

    return $click;
  }

  public static function list_table($order_by = '', $order = '', $paged = '', $search = '', $perpage = 10) {
    $db = Db::fetch();
    global $wpdb;

    $cols = apply_filters('esaf_click_list_table_cols', [
      'id' => 'cl.id',
      'ip' => 'cl.ip',
      'referrer' => 'cl.referrer',
      'created_at' => 'cl.created_at',
      'target_url' => 'COALESCE(pm_link_url.meta_value,cl.uri)',
      'affiliate_id' => 'usr.ID',
      'user_login' => 'usr.user_login'
    ]);

    $search_cols = [
      'cl.ip',
      'COALESCE(pm_link_url.meta_value,cl.uri)',
      'usr.user_login'
    ];

    $from = "{$db->clicks} AS cl";
    $args = [];

    //We're not filtering by affiliate yet, maybe later
    // if( is_numeric($affiliate_id) and (int)$affiliate_id > 0 )
    // $args[] = $wpdb->prepare( "cl.affiliate_id=%d", $affiliate_id );

    $creative = new Creative();
    $joins = [
      "JOIN {$wpdb->users} as usr ON cl.affiliate_id=usr.ID",
      $wpdb->prepare("LEFT JOIN {$wpdb->postmeta} AS pm_link_url ON cl.link_id=pm_link_url.post_id AND pm_link_url.meta_key=%s", $creative->url_str)
    ];

    return Db::list_table($cols, $from, $joins, $args, $order_by, $order, $paged, $search, $perpage, false, $search_cols);
  }

  protected function mgm_created_at_ts($mgm, $val = '') {
    global $wpdb;
    $db = Db::fetch();
    $where = '';

    switch($mgm) {
      case 'get':
        $ts = Utils::db_date_to_ts($this->created_at);
        return $ts;
      case 'set':
        $this->rec->created_at = Utils::ts_to_mysql_date($val);
      default:
        return 0;
    }
  }

}
