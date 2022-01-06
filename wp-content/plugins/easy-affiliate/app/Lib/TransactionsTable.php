<?php

namespace EasyAffiliate\Lib;

use EasyAffiliate\Models\Transaction;

class TransactionsTable extends \WP_List_Table {
  public function __construct() {
    parent::__construct([
        'singular'=> 'wp_list_wafp_transaction', //Singular label
        'plural' => 'wp_list_wafp_transactions', //plural label, also this well be one of the table css class
        'ajax'  => false //We won't support Ajax for this table
    ]);
  }

  /**
   * Get the columns for this table
   *
   * This is also used by TransactionsCtrl to show the hidden column checkboxes in Screen Options.
   *
   * @return array
   */
  public static function get_column_headers() {
    return [
      'col_id' => esc_html__('ID', 'easy-affiliate'),
      'col_created_at' => esc_html__('Time', 'easy-affiliate'),
      'col_user_login' => esc_html__('Affiliate', 'easy-affiliate'),
      'col_trans_num' => esc_html__('Invoice', 'easy-affiliate'),
      'col_source' => esc_html__('Source', 'easy-affiliate'),
      'col_item_name' => esc_html__('Product', 'easy-affiliate'),
      'col_total_amount' => esc_html__('Total', 'easy-affiliate'),
      'col_commission_amount' => esc_html__('Commission', 'easy-affiliate'),
      'col_referring_page' => esc_html__('Referrer', 'easy-affiliate'),
      'col_actions' => esc_html__('Actions', 'easy-affiliate')
    ];
  }

  public function get_columns() {
    return self::get_column_headers();
  }

  public function get_sortable_columns() {
    return [
      'col_id' => ['id', true],
      'col_created_at' => ['created_at', true],
      'col_user_login' => ['user_login', true],
      'col_item_name' => ['item_name', true],
      'col_trans_num' => ['trans_num', true],
      'col_source' => ['source', true],
      'col_sale_amount' => ['sale_amount', true],
      'col_refund_amount' => ['refund_amount', true],
      'col_total_amount' => ['total_amount', true],
      'col_commission_amount' => ['commission_amount', true],
    ];
  }

  public function prepare_items() {
    $valid_orderby = ['id', 'created_at', 'user_login', 'item_name', 'trans_num', 'source', 'sale_amount', 'refund_amount', 'total_amount', 'commission_amount'];
    $orderby = isset($_GET['orderby']) && is_string($_GET['orderby']) && in_array($_GET['orderby'], $valid_orderby) ? $_GET['orderby'] : 'created_at';
    $order = isset($_GET['order']) && is_string($_GET['order']) && strtoupper($_GET['order']) == 'ASC' ? 'ASC' : 'DESC';
    $paged = isset($_GET['paged']) && is_numeric($_GET['paged']) ? max((int) $_GET['paged'], 1) : 1;
    $search = isset($_GET['s']) && is_string($_GET['s']) ? sanitize_text_field(wp_unslash($_GET['s'])) : '';
    $perpage = Utils::get_per_page_screen_option('esaf_transactions_per_page');
    $include_pending = isset($_GET['include_pending']);

    $list_table = Transaction::list_table($orderby, $order, $paged, $search, $perpage, $include_pending);
    $totalitems = $list_table['count'];

    //How many pages do we have in total?
    $totalpages = ceil($totalitems/$perpage);

    /* -- Register the pagination -- */
    $this->set_pagination_args([
      'total_items' => $totalitems,
      'total_pages' => $totalpages,
      'per_page' => $perpage
    ]);

    /* -- Register the Columns -- */
    $columns = $this->get_columns();
    $hidden = get_hidden_columns($this->screen);
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

    require ESAF_VIEWS_PATH . '/transactions/row.php';
  }
}
