<?php

namespace EasyAffiliate\Models;

use EasyAffiliate\Lib\BaseModel;
use EasyAffiliate\Lib\Db;
use EasyAffiliate\Lib\ModelFactory;

class Event extends BaseModel {
  public $event_types;

  // Supported event types
  public function __construct($obj = null) {
    $this->event_types = [
      'creative',
      'campaign',
      'user',
      'payment',
      'transaction',
      'commission',
      'affiliate_application',
    ];

    $this->initialize(
      [
        'id'          => ['default' => 0,      'type' => 'integer'],
        'ip'          => ['default' => null,   'type' => 'string'],
        'args'        => ['default' => null,   'type' => 'string'],
        'event'       => ['default' => '',     'type' => 'string'],
        'evt_id'      => ['default' => 0,      'type' => 'integer'],
        'evt_id_type' => ['default' => 'user', 'type' => 'string'],
        'created_at'  => ['default' => null,   'type' => 'datetime'],
      ],
      $obj
    );
  }

  public function validate() {
    if(!empty($this->ip)) {
      $this->validate_is_ip_addr($this->ip, 'ip');
    }

    $this->validate_is_numeric($this->evt_id, 0, null, 'evt_id');
    $this->validate_is_in_array($this->evt_id_type, $this->event_types, 'evt_id_type');
  }

  public static function get_count_by_event($event) {
    return self::get_count(compact('event'));
  }

  public static function get_count_by_evt_id_type($evt_id_type) {
    return self::get_count(compact('evt_id_type'));
  }

  public static function get_count_by_obj($event,$evt_id_type,$evt_id) {
    return self::get_count(compact('event','evt_id_type','evt_id'));
  }

  public static function get_all_by_event($event, $order_by = '', $limit = '') {
    $db = new Db();
    $args = ['event' => $event];
    return $db->get_records($db->events, $args, $order_by, $limit);
  }

  public static function get_all_by_evt_id_type($evt_id_type, $order_by = '', $limit = '') {
    $db = new Db();
    $args = ['evt_id_type' => $evt_id_type];
    return $db->get_records($db->events, $args, $order_by, $limit);
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

    $db = Db::fetch();

    do_action('esaf_event_pre_store', $this);

    $vals = (array) $this->get_record();
    unset($vals['created_at']); // let the db handle this

    if(isset($this->id) && (int) $this->id > 0) {
      $db->update_record( $db->events, $this->id, $vals );
      do_action('esaf_event_update', $this);
    }
    else {
      $vals['ip'] = ( empty($vals['ip']) ? $_SERVER['REMOTE_ADDR'] : $vals['ip'] );
      $this->id = $db->create_record( $db->events, $vals );
      do_action('esaf_event_create', $this);
      do_action('esaf_event',$this);
      do_action("esaf_event_{$this->event}",$this);
    }

    do_action('esaf_event_store', $this);

    return $this->id;
  }

  public function destroy() {
    $db = new Db();

    $id = $this->id;
    $args = compact('id');

    do_action('esaf_event_destroy', $this);

    return apply_filters('esaf_delete_event', $db->delete_records($db->events, $args), $args);
  }

  // TODO: This is a biggie ... we don't want to send the event object like this
  //       we need to send the object associated with the event instead.
  public function get_data() {
    return ModelFactory::fetch($this->evt_id_type, $this->evt_id);
  }

  public function get_args() {
    return json_decode($this->args);
  }

  public static function get_one($args) {
    return self::get_one_by_class(Event::class, $args);
  }

  public static function get_all($order_by = '', $limit = '', $args = []) {
    return self::get_all_by_class(Event::class, $order_by, $limit, $args);
  }

  public static function get_count($args = []) {
    return self::get_count_by_class(Event::class, $args);
  }

  public static function record($event, BaseModel $obj, $args='') {
    if( (!isset($obj->id) || !$obj->id) &&
        (!isset($obj->ID) || !$obj->ID) &&
        (!isset($obj->term_id) || !$obj->term_id) ) {
      return new \WP_Error(sprintf(__('%s doesn\'t appear to be a valid model.', 'easy-affiliate'), get_class($obj)));
    }

    //Utils::error_log("RECORDING {$event}");
    //Utils::error_log($obj);

    //Utils::error_log("{$event} BACKTRACE!");
    //$bt = debug_backtrace(0);
    //Utils::error_log($bt);

    $e = new Event();
    $e->event = $event;

    // Just turn objects into json for fun
    if(is_array($args) || is_object($args)) {
      $e->args = json_encode($args);
    }
    else {
      $e->args = $args;
    }

    $record = (object) $obj->get_record();

    if($obj instanceof Creative) {
      $e->evt_id = $record->ID;
      $e->evt_id_type = 'creative';
    }
    elseif($obj instanceof Campaign) {
      $e->evt_id = $record->term_id;
      $e->evt_id_type = 'campaign';
    }
    elseif($obj instanceof User) {
      $e->evt_id = $record->ID;
      $e->evt_id_type = 'user';
    }
    elseif($obj instanceof Payment) {
      $e->evt_id = $record->id;
      $e->evt_id_type = 'payment';
    }
    elseif($obj instanceof Transaction) {
      $e->evt_id = $record->id;
      $e->evt_id_type = 'transaction';
    }
    elseif($obj instanceof Commission) {
      $e->evt_id = $record->id;
      $e->evt_id_type = 'commission';
    }
    elseif($obj instanceof AffiliateApplication) {
      $e->evt_id = $record->ID;
      $e->evt_id_type = 'affiliate_application';
    }
    else { return new \WP_Error(sprintf(__('%s isn\'t a valid event model.', 'easy-affiliate'), get_class($obj))); }

    $e->store();

    return $e;
  }

  /** Get the latest object for a given event */
  public static function latest($event) {
    global $wpdb;
    $db = new Db();

    $q = $wpdb->prepare("
      SELECT id
        FROM {$db->events}
       WHERE event=%s
       ORDER BY id DESC
       LIMIT 1
    ", $event);

    if(($id = $wpdb->get_var($q))) {
      return new Event($id);
    }

    return false;
  }

} //End class

