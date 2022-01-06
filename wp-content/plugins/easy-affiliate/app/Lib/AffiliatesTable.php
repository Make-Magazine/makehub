<?php

namespace EasyAffiliate\Lib;

use EasyAffiliate\Models\User;

class AffiliatesTable extends \WP_List_Table {
  public function __construct() {
    parent::__construct([
      'singular'=> 'wp_list_wafp_affiliate', //Singular label
      'plural' => 'wp_list_wafp_affiliates', //plural label, also this well be one of the table css class
      'ajax'  => false //We won't support Ajax for this table
    ]);
  }

  public function get_columns() {
    return [
      'col_ID' => esc_html__('ID', 'easy-affiliate'),
      'col_username' => esc_html__('Username', 'easy-affiliate'),
      'col_name' => esc_html__('Name', 'easy-affiliate'),
      'col_status' => esc_html__('Status', 'easy-affiliate'),
      'col_mtd_clicks' => esc_html__('MTD Clicks', 'easy-affiliate'),
      'col_ytd_clicks' => esc_html__('YTD Clicks', 'easy-affiliate'),
      'col_mtd_commissions' => esc_html__('MTD Commissions', 'easy-affiliate'),
      'col_ytd_commissions' => esc_html__('YTD Commissions', 'easy-affiliate'),
      'col_signup_date' => esc_html__('Signup Date', 'easy-affiliate'),
      'col_parent_name' => esc_html__('Referrer', 'easy-affiliate'),
      'col_notes' => esc_html__('Notes', 'easy-affiliate'),
    ];
  }

  public function get_sortable_columns() {
    return [
      'col_ID' => ['ID', true],
      'col_signup_date' => ['signup_date', true],
      'col_username' => ['username', true],
      'col_name' => ['name', true],
      'col_status' => ['status', true],
      'col_mtd_clicks' => ['mtd_clicks', true],
      'col_ytd_clicks' => ['ytd_clicks', true],
      'col_mtd_commissions' => ['mtd_commissions', true],
      'col_ytd_commissions' => ['ytd_commissions', true],
      'col_parent_name' => ['parent_name', true],
      'col_notes' => ['notes', true],
    ];
  }

  public function prepare_items() {
    $valid_orderby = ['signup_date', 'username', 'name', 'status', 'ID', 'mtd_clicks', 'ytd_clicks', 'mtd_commissions', 'ytd_commissions', 'parent_name', 'notes'];
    $orderby = isset($_GET['orderby']) && is_string($_GET['orderby']) && in_array($_GET['orderby'], $valid_orderby) ? $_GET['orderby'] : 'signup_date';
    $order = isset($_GET['order']) && is_string($_GET['order']) && strtoupper($_GET['order']) == 'ASC' ? 'ASC' : 'DESC';
    $paged = isset($_GET['paged']) && is_numeric($_GET['paged']) ? max((int) $_GET['paged'], 1) : 1;
    $search = isset($_GET['s']) && is_string($_GET['s']) ? sanitize_text_field(wp_unslash($_GET['s'])) : '';
    $perpage = Utils::get_per_page_screen_option('esaf_affiliates_per_page');

    $list_table = User::affiliate_list_table($orderby, $order, $paged, $search, $perpage);
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

    require ESAF_VIEWS_PATH . '/affiliates/row.php';
  }
}
