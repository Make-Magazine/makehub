<?php

namespace EasyAffiliate\Models;

use EasyAffiliate\Lib\BaseModel;
use EasyAffiliate\Lib\Db;
use EasyAffiliate\Lib\Utils;

class CustomLink extends BaseModel {
  public function __construct($obj = null) {
    $this->initialize(
      [
        'id'               => ['default' => 0, 'type' => 'integer'],
        'affiliate_id'     => ['default' => 0, 'type' => 'integer'],
        'pretty_link_id'   => ['default' => 0, 'type' => 'integer'],
        'destination_link' => ['default' => null, 'type' => 'string'],
        'created_at'       => ['default' => null, 'type' => 'datetime'],
      ],
      $obj
    );
  }

  public function destroy() {
    $db = Db::fetch();

    do_action('esaf_custom_link_destroy', $this);

    $res = $db->delete_records($db->customlinks, ['id' => $this->id]);
    Event::record('custom-link-deleted', $this);

    return $res;
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

    if(isset($this->id) && !is_null($this->id) && ((int) $this->id > 0)) {
      $this->add_to_pretty_link(true);
      $db->update_record($db->customlinks, $this->id, (array) $this->get_values());
    }
    else {
      $this->add_to_pretty_link();
      $this->id = $db->create_record($db->customlinks, (array) $this->get_values());
    }

    do_action('esaf_custom_link_store', $this);

    return $this->id;
  }

  private function add_to_pretty_link($update = false) {
    if(function_exists('prli_create_pretty_link')) {
      $user = Utils::get_currentuserinfo();
      $username = is_email($user->user_login) ? $user->ID : $user->user_login;
      $target_url = add_query_arg('aff', urlencode($username), $this->destination_link);
      $prli_update = new \PrliUpdateController();

      if(empty($this->pretty_link_id)) {
        $pretty_link = prli_get_link(prli_create_pretty_link($target_url));

        if($pretty_link) {
          $this->pretty_link_id = $pretty_link->id;
        }
      }
      elseif($update) {
        $pretty_link = prli_get_link($this->pretty_link_id);

        if($pretty_link) {
          prli_update_pretty_link($pretty_link->id, $target_url);
        }
      }

      if(isset($pretty_link) && $pretty_link && $prli_update->is_installed_and_activated()) {
        $term = term_exists('easy-affiliate', 'pretty-link-category');

        if($term === 0 || $term === null) {
          $term = wp_insert_term(
            'Easy Affiliate',   // the term
            'pretty-link-category',
            [
              'slug' => 'easy-affiliate',
            ]
          );
        }

        wp_set_post_terms((int) $pretty_link->link_cpt_id, [(int) $term['term_id']], 'pretty-link-category');
      }
    }
  }

  /** STATIC CRUD METHODS **/
  public static function get_one($args) {
    return self::get_one_by_class(CustomLink::class, $args);
  }

  public static function get_all($order_by = '', $limit = '', $args = []) {
    return self::get_all_by_class(CustomLink::class, $order_by, $limit, $args);
  }

  public static function get_count($args = []) {
    return self::get_count_by_class(CustomLink::class, $args);
  }
}
