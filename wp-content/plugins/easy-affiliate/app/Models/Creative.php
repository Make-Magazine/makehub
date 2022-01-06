<?php

namespace EasyAffiliate\Models;

use EasyAffiliate\Lib\CptModel;

class Creative extends CptModel {
  public static $cpt = 'esaf-creative';
  public $link_types;

  public function __construct($obj = null) {
    $this->load_cpt(
      $obj,
      self::$cpt,
      [
        'url'          => ['default' => '',    'type' => 'string'],
        'link_text'    => ['default' => '',    'type' => 'string'],
        'link_info'    => ['default' => '',    'type' => 'string'],
        'link_type'    => ['default' => '',    'type' => 'string'],
        'is_hidden'    => ['default' => false, 'type' => 'bool'],
        'image'        => ['default' => '',    'type' => 'string'],
        'image_height' => ['default' => 0,     'type' => 'integer'],
        'image_width'  => ['default' => 0,     'type' => 'integer'],
        'image_alt'    => ['default' => '',    'type' => 'string'],
        'image_title'  => ['default' => '',    'type' => 'string']
      ]
    );

    $this->link_types = [
      'text', 'banner'
    ];
  }

  public function validate() {
    $this->validate_not_empty($this->url, 'url');
    $this->validate_is_url($this->url, 'url');

    $this->validate_not_empty($this->link_type, 'link_type');
    $this->validate_is_in_array($this->link_type, $this->link_types, 'link_type');

    if($this->link_type == 'banner') {
      $this->validate_not_empty($this->image, 'image');
      $this->validate_not_empty($this->image_height, 'image_height');
      $this->validate_not_empty($this->image_width, 'image_width');
      /* $this->validate_not_empty($this->image_alt, 'image_alt'); */
      /* $this->validate_not_empty($this->image_title, 'image_title'); */
      $this->validate_is_numeric($this->image_height, 0, null, 'image_height');
      $this->validate_is_numeric($this->image_width, 0, null, 'image_width');
    }
    elseif($this->link_type=='text') {
      $this->validate_not_empty($this->link_text, 'link_text');
    }
  }

  /** STATIC CRUD METHODS **/
  public static function get_one($args) {
    return self::get_one_by_class(Creative::class, $args);
  }

  public static function get_all($order_by = '', $limit = '', $args = []) {
    return self::get_all_by_class(Creative::class, $order_by, $limit, $args);
  }

  public static function get_count($args = []) {
    return self::get_count_by_class(Creative::class, $args);
  }

  public static function get_one_by_slug($slug) {
    global $wpdb;

    $q = $wpdb->prepare("
        SELECT post_id
          FROM {$wpdb->postmeta}
         WHERE meta_key = '_wafp_creative_slug'
           AND meta_value = %s
      ",
      $slug
    );

    $post_id = $wpdb->get_var($q);

    if(!empty($post_id)) {
      return new Creative($post_id);
    }

    return false;
  }

  /**
   * @param bool $link_type
   * @param bool $campaign_slug
   * @param bool $get_count
   * @param int $page
   * @param int $perpage
   *
   * @return self[]
   */
  public static function get_all_visible($link_type = false, $campaign_slug = false, $get_count = false, $page = 1, $perpage = 25 ) {
    global $wpdb;

    $creative = new Creative();

    $link_type_where = '';
    if(!empty($link_type)) {
      if(is_string($link_type)) {
        $link_type_where = $wpdb->prepare('AND pm_link_type.meta_value = %s', $link_type);
      }
      elseif(is_array($link_type)) {
        $link_type_where_array = [];

        foreach($link_type as $type) {
          $link_type_where_array[] = $wpdb->prepare('pm_link_type.meta_value = %s', $type);
        }

        $link_type_where = 'AND (' . join(' OR ', $link_type_where_array) . ')';
      }
    }

    $campaign_where = '';
    if(!empty($campaign_slug)) {
      $campaign_where = $wpdb->prepare("
          AND (
            SELECT COUNT(*)
              FROM {$wpdb->terms} AS term
              JOIN {$wpdb->term_taxonomy} AS tax
                ON term.term_id=tax.term_id
               AND tax.taxonomy=%s
              JOIN {$wpdb->term_relationships} AS rel
                ON rel.term_taxonomy_id=tax.term_taxonomy_id
             WHERE rel.object_id=p.ID
               AND term.slug=%s
          ) > 0
        ",
        Campaign::$ctax,
        $campaign_slug
      );
    }

    $q = $wpdb->prepare("
          FROM {$wpdb->posts} AS p
          JOIN {$wpdb->postmeta} AS pm_is_hidden
            ON pm_is_hidden.post_id = p.ID
           AND pm_is_hidden.meta_key = %s
          JOIN {$wpdb->postmeta} AS pm_link_type
            ON pm_link_type.post_id = p.ID
           AND pm_link_type.meta_key = %s
         WHERE p.post_status = 'publish'
           AND ( pm_is_hidden.meta_value IS NULL
                 OR pm_is_hidden.meta_value = ''
                 OR pm_is_hidden.meta_value = '0'
                 OR pm_is_hidden.meta_value = 0 )
           {$link_type_where}
           {$campaign_where}
      ",
      $creative->is_hidden_str,
      $creative->link_type_str
    );

    if($get_count) {
      $q = "
        SELECT COUNT(*)
        {$q}
      ";

      return $wpdb->get_var($q);
    }

    $q = $wpdb->prepare("
        SELECT p.ID
        {$q}
         ORDER BY p.post_modified DESC
         LIMIT %d
        OFFSET %d
      ",
      $perpage,
      (((int)$page - 1) * $perpage)
    );

    $ids = $wpdb->get_col($q);

    if(empty($ids)) {
      return false;
    }

    $objs = [];
    foreach($ids as $id) {
      $objs[] = new Creative($id);
    }

    return $objs;
  }

  /** INSTANCE VARIABLES & METHODS **/
  public function display_url($affiliate_id) {
    $url = apply_filters('esaf_creative_display_url', home_url());
    $user = new User($affiliate_id);

    if(apply_filters('esaf_affiliate_param_use_id', is_email($user->user_login), $user)) {
      $username = $user->ID;
    }
    else {
      $username = $user->get_urlencoded_user_login();
    }

    $args = apply_filters('esaf_creative_display_url_args', [
      'aff' => $username,
      'p' => $this->ID
    ], $user, $this, $url);

    return add_query_arg($args, $url);
  }

  public function link_code($affiliate_id, $target = '', $variable_width = false) {
    if(!empty($target)) {
      $target = sprintf(' target="%s"', esc_attr($target));
    }

    if(isset($this->image) and !empty($this->image)) {
       $attrib = null;
       if(empty($variable_width) && $this->image_width) {
         $attrib .= sprintf(' width="%s"', esc_attr($this->image_width));
       }
       elseif($variable_width && empty($this->image_width)) {
         $attrib .= sprintf(' width="%s"', esc_attr($variable_width));
       }
       elseif($variable_width && !empty($this->image_width)) {
         $attrib .= sprintf(' style="max-width: %s"', esc_attr($variable_width));
       }

       if(empty($variable_width) && $this->image_height) {
         $attrib .= sprintf(' height="%s"', esc_attr($this->image_height));
       }

       if($this->image_alt) {
         $attrib .= sprintf(' alt="%s"', esc_attr($this->image_alt));
       }

       if($this->image_title) {
         $attrib .= sprintf(' title="%s"', esc_attr($this->image_title));
       }

       return apply_filters(
         'esaf_creative_code_image',
         "<a href=\"". esc_url($this->display_url($affiliate_id)) . "\"{$target}><img src=\"".esc_url($this->image)."\"$attrib /></a>",
         $affiliate_id,
         $target,
         $this->display_url($affiliate_id),
         $this->image,
         $attrib,
         $this
       );
    }
    else {
      $description = empty($this->link_text) ? __('Affiliate Link', 'easy-affiliate') : $this->link_text;

      return apply_filters(
        'esaf_creative_code_text',
        "<a href=\"". esc_url($this->display_url($affiliate_id)) . "\"{$target}>".esc_html($description)."</a>",
        $affiliate_id,
        $target,
        $this->display_url($affiliate_id),
        $description,
        $this
      );
    }
  }

  public static function get_type_count($link_type) {
    global $wpdb;

    $creative = new Creative();

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
          ) AS link_count
      ",
      $creative->link_type_str,
      $link_type,
      self::$cpt
    );

    $count = $wpdb->get_var($q);

    return (int) $count;
  }

  public static function get_available_link_types() {
    global $wpdb;

    $creative = new Creative();

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
      $creative->link_type_str,
      self::$cpt
    );

    $link_types = $wpdb->get_col($q);

    return $link_types;
  }

  /**
   * Get a comma separated list of campaigns for this creative
   *
   * @return string
   */
  public function get_campaign_list() {
    $campaigns = [];

    foreach($this->campaigns as $campaign) {
      $campaigns[] = $campaign->name;
    }

    if(empty($campaigns)) {
      return __('None', 'easy-affiliate');
    }

    return join(', ', $campaigns);
  }

  /***** MAGIC METHOD HANDLERS *****/
  protected function mgm_campaigns($mgm, $val = '') {
    switch($mgm) {
      case 'get':
        return Campaign::get_all_by_creative_id($this->ID);
      default:
        return true;
    }
  }

}
