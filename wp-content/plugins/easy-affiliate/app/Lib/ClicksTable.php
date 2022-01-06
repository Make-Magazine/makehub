<?php

namespace EasyAffiliate\Lib;

use EasyAffiliate\Models\Click;

class ClicksTable extends \WP_List_Table {
  public function __construct() {
    parent::__construct([
      'singular'=> 'wp_list_wafp_click', //Singular label
      'plural' => 'wp_list_wafp_clicks', //plural label, also this well be one of the table css class
      'ajax'  => false //We won't support Ajax for this table
    ]);
  }

  public function get_columns() {
    return [
      'col_created_at' => esc_html__('Time', 'easy-affiliate'),
      'col_user_login'=> esc_html__('Affiliate', 'easy-affiliate'),
      'col_target_url'=> esc_html__('URL', 'easy-affiliate'),
      'col_ip'=> esc_html__('IP', 'easy-affiliate'),
      'col_referrer'=> esc_html__('Referrer', 'easy-affiliate')
    ];
  }

  public function get_sortable_columns() {
    return $sortable = [
      'col_created_at' => ['created_at', true],
      'col_user_login' => ['user_login', true],
      'col_target_url' => ['target_url', true],
      'col_ip' => ['ip', true],
      'col_referrer' => ['referrer', true]
    ];
  }

  public function prepare_items() {
    $valid_orderby = ['created_at', 'user_login', 'target_url', 'ip', 'referrer'];
    $orderby = isset($_GET['orderby']) && is_string($_GET['orderby']) && in_array($_GET['orderby'], $valid_orderby) ? $_GET['orderby'] : 'created_at';
    $order = isset($_GET['order']) && is_string($_GET['order']) && strtoupper($_GET['order']) == 'ASC' ? 'ASC' : 'DESC';
    $paged = isset($_GET['paged']) && is_numeric($_GET['paged']) ? max((int) $_GET['paged'], 1) : 1;
    $search = isset($_GET['s']) && is_string($_GET['s']) ? sanitize_text_field(wp_unslash($_GET['s'])) : '';
    $perpage = Utils::get_per_page_screen_option('esaf_clicks_per_page');

    $list_table = Click::list_table($orderby, $order, $paged, $search, $perpage);

    $totalitems = $list_table['count'];

    //How many pages do we have in total?
    $totalpages = ceil($totalitems / $perpage);

    /* -- Register the pagination -- */
    $this->set_pagination_args([
      'total_items' => $totalitems,
      'total_pages' => $totalpages,
      'per_page' => $perpage
    ]);

    /* -- Register the Columns -- */
    $columns = $this->get_columns();
    $hidden = [];
    $sortable = $this->get_sortable_columns();
    $this->_column_headers = [$columns, $hidden, $sortable];

    /* -- Fetch the items -- */
    $this->items = $list_table['results'];
  }

  public function display_rows() {
    //Get the records registered in the prepare_items method
    $records = $this->items;

    //Get the columns registered in the get_columns and get_sortable_columns methods
    list($columns, $hidden) = $this->get_column_info();

    require ESAF_VIEWS_PATH . '/clicks/row.php';
  }
}
