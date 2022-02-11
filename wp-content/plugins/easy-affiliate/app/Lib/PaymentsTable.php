<?php

namespace EasyAffiliate\Lib;

use EasyAffiliate\Models\Payment;

class PaymentsTable extends \WP_List_Table {
  const DEFAULT_PER_PAGE = 10;

  public function __construct() {
    parent::__construct([
      'singular'=> 'wp_list_wafp_payment', //Singular label
      'plural' => 'wp_list_wafp_payments', //plural label, also this well be one of the table css class
      'ajax'  => false //We won't support Ajax for this table
    ]);
  }

  /**
   * Get the columns for this table
   *
   * This is also used by PaymentsCtrl to show the hidden column checkboxes in Screen Options.
   *
   * @return array
   */
  public static function get_column_headers() {
    return [
      'cb' => '<input type="checkbox" />',
      'col_created_at' => esc_html__('Time', 'easy-affiliate'),
      'col_sales_count' => esc_html__('Number of Sales', 'easy-affiliate'),
      'col_net_sales_amount' => esc_html__('Net Sales Amount', 'easy-affiliate'),
      'col_amount' => esc_html__('Payout Amount', 'easy-affiliate'),
      'col_affiliate' => esc_html__('Affiliate', 'easy-affiliate'),
      'col_actions' => esc_html__('Actions', 'easy-affiliate'),
    ];
  }

  /**
   * Get the bulk actions for this table
   *
   * @return array
   */
  protected function get_bulk_actions() {
    return [
      'delete' => __('Delete permanently', 'easy-affiliate')
    ];
  }

  public function get_columns() {
    return self::get_column_headers();
  }

  /**
   * Handles the checkbox column output.
   *
   * @param \stdClass $row
   */
  public function column_cb($row) {
    ?>
    <label class="screen-reader-text" for="cb-select-<?php echo esc_attr($row->id); ?>">
      <?php esc_html_e('Select payment', 'easy-affiliate'); ?>
    </label>
    <input id="cb-select-<?php echo esc_attr($row->id); ?>" type="checkbox" name="post[]" value="<?php echo esc_attr($row->id); ?>" />
    <?php
  }

  public function get_sortable_columns() {
    return [
      'col_created_at' => ['created_at', true],
      'col_sales_count' => ['sales_count', true],
      'col_net_sales_amount' => ['net_sales_amount', true],
      'col_amount'=> ['amount', true],
      'col_affiliate'=> ['affiliate', true],
    ];
  }

  public function prepare_items() {
    $valid_orderby = ['created_at', 'sales_count', 'net_sales_amount', 'amount', 'affiliate'];
    $orderby = isset($_GET['orderby']) && is_string($_GET['orderby']) && in_array($_GET['orderby'], $valid_orderby) ? $_GET['orderby'] : 'created_at';
    $order = isset($_GET['order']) && is_string($_GET['order']) && strtoupper($_GET['order']) == 'ASC' ? 'ASC' : 'DESC';
    $paged = isset($_GET['paged']) && is_numeric($_GET['paged']) ? max((int) $_GET['paged'], 1) : 1;
    $search = isset($_GET['s']) && is_string($_GET['s']) ? sanitize_text_field(wp_unslash($_GET['s'])) : '';
    $perpage = Utils::get_per_page_screen_option('esaf_payments_per_page');

    if(empty($perpage)) {
      $perpage = self::DEFAULT_PER_PAGE;
    }

    $list_table = Payment::list_table($orderby, $order, $paged, $search, $perpage);
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

    require ESAF_VIEWS_PATH . '/payments/row.php';
  }
}
