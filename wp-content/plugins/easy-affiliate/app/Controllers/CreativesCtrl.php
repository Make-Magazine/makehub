<?php

namespace EasyAffiliate\Controllers;

use EasyAffiliate\Lib\CptCtrl;
use EasyAffiliate\Lib\Utils;
use EasyAffiliate\Models\Campaign;
use EasyAffiliate\Models\Creative;

class CreativesCtrl extends CptCtrl {
  public function load_hooks() {
    $this->ctaxes = [Campaign::$ctax];

    add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);

    // Save post meta data
    add_action('save_post_' . Creative::$cpt, [$this, 'save_postdata']);

    // Add Normal and Sortable Columns
    add_action('manage_' . Creative::$cpt . '_posts_custom_column', [$this, 'custom_columns'], 10, 2);
    add_filter('manage_edit-' . Creative::$cpt . '_columns', [$this, 'columns']);
    add_filter('manage_edit-' . Creative::$cpt . '_sortable_columns', [$this, 'sortable_columns']);

    // Add View/Filter Links at the top of the List page
    add_filter('views_edit-' . Creative::$cpt, [$this, 'list_views']);

    // Add Filter dropdown at the top of the List page
    add_action('restrict_manage_posts', [$this, 'list_table_filters']);

    // Modify query to filter and add custom orderby's for sortable columns
    add_filter('parse_query', [$this, 'list_table_query']);

    add_filter('pre_get_posts', [$this, 'pre_get_posts'], 10, 1);
    add_filter('posts_search', [$this, 'posts_search'], 10, 2);
  }

  public function posts_search($sql, $query) {
    global $pagenow;

    if ($pagenow != 'edit.php') {
      return $sql;
    }

    $q = $query->query_vars;

    if ($q['post_type'] == Creative::$cpt && !empty($q['s'])) {
      // Remove search by post_title and post_content statement
      return '';
    }

    return $sql;
  }

  public function pre_get_posts(&$query) {
    global $pagenow;

    if ($pagenow != 'edit.php') {
      return $query;
    }

    $q = $query->query_vars;

    if ($q['post_type'] == Creative::$cpt && !empty($q['s'])) {
      // Search by meta
      $meta_query = [];
      $meta_query['relation'] = 'OR';

      foreach (
        [
          '_wafp_creative_image_title',
          '_wafp_creative_link_text',
          '_wafp_creative_link_info',
          '_wafp_creative_url',
        ] as $key) {
        $meta_query[] = [
          'key'       => $key,
          'value'     => $q['s'],
          'compare'   => 'LIKE',
        ];
      }

      $query->query_vars['meta_query'] = $meta_query;
    }

    return $query;
  }

  public function register_post_type() {
    $this->cpt = (object) [
      'slug' => Creative::$cpt,
      'config' => [
        'labels' => [
          'name' => esc_html__('Creatives', 'easy-affiliate'),
          'singular_name' => esc_html__('Creative', 'easy-affiliate'),
          'add_new_item' => esc_html__('Add New Creative', 'easy-affiliate'),
          'edit_item' => esc_html__('Edit Creative', 'easy-affiliate'),
          'new_item' => esc_html__('New Creative', 'easy-affiliate'),
          'view_item' => esc_html__('View Creative', 'easy-affiliate'),
          'search_items' => esc_html__('Search Creatives', 'easy-affiliate'),
          'not_found' => esc_html__('No Creatives found', 'easy-affiliate'),
          'not_found_in_trash' => esc_html__('No Creatives found in Trash', 'easy-affiliate'),
          'parent_item_colon' => esc_html__('Parent Creative:', 'easy-affiliate')
        ],
        'public' => false,
        'publicly_queryable' => false,
        'show_ui' => true,
        'show_in_rest' => false,
        'show_in_menu' => false,
        'has_archive' => false,
        'capability_type' => 'post',
        'hierarchical' => false,
        'register_meta_box_cb' => [$this, 'add_meta_boxes'],
        'rewrite' => false,
        'supports' => ['title'],
        'taxonomies' => $this->ctaxes
      ]
    ];

    register_post_type(Creative::$cpt, $this->cpt->config);
  }

  public function columns() {
    return [
      'cb' => '<input type="checkbox" />',
      'esaf-creative-ID' => esc_html__('ID', 'easy-affiliate'),
      'title' => esc_html__('Title', 'easy-affiliate'),
      'esaf-creative-hidden' => esc_html__('Hidden', 'easy-affiliate'),
      'esaf-creative-link_type' => esc_html__('Type', 'easy-affiliate'),
      'esaf-creative-url' => esc_html__('URL', 'easy-affiliate'),
      'esaf-creative-banner_image' => esc_html__('Image', 'easy-affiliate'),
      'esaf-creative-banner_width' => esc_html__('Image Width', 'easy-affiliate'),
      'esaf-creative-banner_height' => esc_html__('Image Height', 'easy-affiliate'),
      'taxonomy-' . Campaign::$ctax => esc_html__('Campaigns', 'easy-affiliate'),
      'esaf-creative-date' => esc_html__('Created', 'easy-affiliate')
    ];
  }

  public function custom_columns($column, $post_id) {
    $creative = new Creative($post_id);

    if($creative->ID !== null) {
      switch($column) {
        case 'esaf-creative-ID':
          echo esc_html($creative->ID);
          break;
        case 'esaf-creative-link_type':
          echo esc_html($creative->link_type);
          break;
        case 'esaf-creative-url':
          echo esc_url($creative->url);
          break;
        case 'esaf-creative-date':
          echo esc_html(Utils::format_datetime($creative->post_date_gmt));
          break;
        case 'esaf-creative-hidden':
          if($creative->is_hidden) {
            echo "<b>X</b>";
          }
          break;
        case 'esaf-creative-banner_image':
          if($creative->link_type=='banner' && !empty($creative->image)) {
            ?><img src="<?php echo esc_url($creative->image); ?>" style="max-width: 100%; max-height: 50px;" /><?php
          }
          else {
            esc_html_e('N/A', 'easy-affiliate');
          }
          break;
        case 'esaf-creative-banner_width':
          if($creative->link_type=='banner' && !empty($creative->image_width)) {
            echo esc_html($creative->image_width);
          }
          else {
            esc_html_e('N/A', 'easy-affiliate');
          }
          break;
        case 'esaf-creative-banner_height':
          if($creative->link_type=='banner' && !empty($creative->image_height)) {
            echo esc_html($creative->image_height);
          }
          else {
            esc_html_e('N/A', 'easy-affiliate');
          }
          break;
      }
    }
  }

  public function sortable_columns() {
    return [
      'esaf-creative-link_type' => 'link_type',
      'esaf-creative-banner_width' => 'image_width',
      'esaf-creative-banner_height' => 'image_height',
      'esaf-creative-date' => 'date'
    ];
  }

  public function add_meta_boxes() {
    add_meta_box('easf-creative-options', esc_html__('Creative Options', 'easy-affiliate'), [$this, 'trigger_meta_box'], Creative::$cpt, 'normal');
  }

  public function trigger_meta_box($object) {
    $creative = new Creative($object->ID);

    require ESAF_VIEWS_PATH . '/creatives/options.php';
  }

  public function enqueue_scripts($hook) {
    global $current_screen, $post_ID;

    if($current_screen->post_type == Creative::$cpt) {
      wp_enqueue_media();
      wp_enqueue_style('esaf-creatives', ESAF_CSS_URL . '/admin-creatives.css', [], ESAF_VERSION);

      wp_register_script('esaf-form-validator', ESAF_JS_URL . '/jquery.form-validator.min.js', ['jquery'], '2.3.26');
      wp_enqueue_script('esaf-creatives', ESAF_JS_URL . '/admin-creatives.js', ['jquery', 'esaf-form-validator'], ESAF_VERSION);

      wp_localize_script('esaf-creatives', 'EsafCreatives', [
        'submit_button_text' => __('Update', 'easy-affiliate'),
        'title' => __('Choose or Upload a Banner', 'easy-affiliate'),
        'button' => __('Use this image', 'easy-affiliate'),
      ]);
    }
  }

  public function save_postdata($post_id) {
    if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
      return;
    }

    if(defined('DOING_AJAX')) {
      return;
    }

    if(Utils::is_post_request()) {
      $creative = new Creative($post_id);
      $values = self::sanitize(wp_unslash($_POST));
      $creative->load_from_sanitized_array($values);
      $creative->store_meta();
    }
  }

  /**
   * Sanitize the given link values
   *
   * @param   array  $values
   * @return  array
   */
  private static function sanitize($values) {
    $values['_wafp_creative_url'] = isset($values['_wafp_creative_url']) && is_string($values['_wafp_creative_url']) ? sanitize_text_field($values['_wafp_creative_url']) : '';
    $values['_wafp_creative_link_type'] = isset($values['_wafp_creative_link_type']) && is_string($values['_wafp_creative_link_type']) && in_array($values['_wafp_creative_link_type'], ['banner', 'text']) ? $values['_wafp_creative_link_type'] : 'banner';
    $values['_wafp_creative_image'] = isset($values['_wafp_creative_image']) && is_string($values['_wafp_creative_image']) ? sanitize_text_field($values['_wafp_creative_image']) : '';
    $values['_wafp_creative_image_alt'] = isset($values['_wafp_creative_image_alt']) && is_string($values['_wafp_creative_image_alt']) ? sanitize_text_field($values['_wafp_creative_image_alt']) : '';
    $values['_wafp_creative_image_title'] = isset($values['_wafp_creative_image_title']) && is_string($values['_wafp_creative_image_title']) ? sanitize_text_field($values['_wafp_creative_image_title']) : '';
    $values['_wafp_creative_image_width'] = isset($values['_wafp_creative_image_width']) && is_numeric($values['_wafp_creative_image_width']) ? max(0, (int) $values['_wafp_creative_image_width']) : 0;
    $values['_wafp_creative_image_height'] = isset($values['_wafp_creative_image_height']) && is_numeric($values['_wafp_creative_image_height']) ? max(0, (int) $values['_wafp_creative_image_height']) : 0;
    $values['_wafp_creative_link_text'] = isset($values['_wafp_creative_link_text']) && is_string($values['_wafp_creative_link_text']) ? sanitize_text_field($values['_wafp_creative_link_text']) : '';
    $values['_wafp_creative_is_hidden'] = isset($values['_wafp_creative_is_hidden']);
    $values['_wafp_creative_link_info'] = isset($values['_wafp_creative_link_info']) && is_string($values['_wafp_creative_link_info']) ? Utils::sanitize_textarea_field($values['_wafp_creative_link_info']) : '';

    return $values;
  }

  private function get_view_link($args, $anchor_text, $count=0, $selected=false) {
    if($selected) {
      return sprintf(
        '<span><span class="esaf-black esaf-bold">%s</span> <span class="count">(%s)</span></span>',
        esc_html($anchor_text),
        esc_html($count)
      );
    }
    else {
      return sprintf(
        '<a href="%s">%s <span class="count">(%s)</span></a>',
        esc_url('edit.php?post_type=' . Creative::$cpt . '&' . http_build_query($args)),
        esc_html($anchor_text),
        esc_html($count)
      );
    }
  }

  public function list_views($views) {
    $custom_views = [];

    if(isset($views['all'])) {
      $text_count   = Creative::get_type_count('text');
      $banner_count = Creative::get_type_count('banner');

      $custom_views['all'] = $views['all'];

      if($text_count > 0) {
        $selected = (isset($_GET['link_type']) && $_GET['link_type']=='text');
        $custom_views['text'] = $this->get_view_link(['link_type'=>'text'], __('Text', 'easy-affiliate'), $text_count, $selected);
      }

      if($banner_count > 0) {
        $selected = (isset($_GET['link_type']) && $_GET['link_type']=='banner');
        $custom_views['banner'] = $this->get_view_link(['link_type'=>'banner'], __('Banner', 'easy-affiliate'), $banner_count, $selected);
      }

      if(isset($views['trash'])) {
        $custom_views['trash'] = $views['trash'];
      }

      return $custom_views;
    }

    return $views;
  }

  public function list_table_filters() {
    global $current_screen;

    if($current_screen->post_type == Creative::$cpt) {
      $link_types = Creative::get_available_link_types();

      $dropdown = [];
      $dropdown[0] = [
        'label' => __('Show all link types', 'easy-affiliate'),
        'selected' => false,
      ];

      $creative = new Creative();

      foreach($link_types as $link_type) {
        if(!in_array($link_type, $creative->link_types)) { continue; }
        $dropdown[$link_type] = [];
        $dropdown[$link_type]['selected'] = (!empty($_GET['link_type']) && $_GET['link_type'] == $link_type);

        if($link_type=='text') {
          $dropdown[$link_type]['label'] = __('Text', 'easy-affiliate');
        }
        else if($link_type=='banner') {
          $dropdown[$link_type]['label'] = __('Banner', 'easy-affiliate');
        }
      }

      ?>
      <select name="link_type" id="filter-by-link-type">
        <?php foreach($dropdown as $link_type => $config): ?>
          <option value="<?php echo esc_attr($link_type); ?>" <?php selected($config['selected']); ?>><?php echo esc_html($config['label']); ?></option>
        <?php endforeach; ?>
      </select>
      <?php
    }
  }

  public function list_table_query( $query ) {
    if( is_admin() && isset($query->query) && isset($query->query['post_type']) &&
        $query->query['post_type'] == Creative::$cpt ) {
      $qv = &$query->query_vars;
      $qv['meta_query'] = [];

      $creative = new Creative();

      if(isset($_GET['link_type']) && is_string($_GET['link_type']) && !empty($_GET['link_type'])) {
        $qv['meta_query'][] = [
          'field' => $creative->link_type_str,
          'value' => sanitize_key($_GET['link_type']),
          'compare' => '=',
          'type' => 'STRING'
        ];
      }

      if(!empty($_GET['order']) && is_string($_GET['order']) && !empty($_GET['orderby']) && is_string($_GET['orderby']) && $_GET['orderby'] == 'link_type') {
        $qv['orderby'] = 'meta_value';
        $qv['meta_key'] = $creative->link_type_str;
        $qv['order'] = strtoupper($_GET['order']) == 'DESC' ? 'DESC' : 'ASC';
      }

      if(!empty($_GET['order']) && is_string($_GET['order']) && !empty($_GET['orderby']) && is_string($_GET['orderby']) && $_GET['orderby'] == 'image_width') {
        $qv['orderby'] = 'meta_value_num';
        $qv['meta_key'] = $creative->image_width_str;
        $qv['order'] = strtoupper($_GET['order']) == 'DESC' ? 'DESC' : 'ASC';
      }

      if(!empty($_GET['order']) && is_string($_GET['order']) && !empty($_GET['orderby']) && is_string($_GET['orderby']) && $_GET['orderby'] == 'image_height') {
        $qv['orderby'] = 'meta_value_num';
        $qv['meta_key'] = $creative->image_height_str;
        $qv['order'] = strtoupper($_GET['order']) == 'DESC' ? 'DESC' : 'ASC';
      }
    }
  }
}
