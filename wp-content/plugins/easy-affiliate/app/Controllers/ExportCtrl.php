<?php

namespace EasyAffiliate\Controllers;

use EasyAffiliate\Models\Payment;
use EasyAffiliate\Models\Click;
use EasyAffiliate\Models\User;
use EasyAffiliate\Models\Transaction;
use EasyAffiliate\Helpers\ExportHelper;
use EasyAffiliate\Lib\AffiliatesTable;
use EasyAffiliate\Lib\ClicksTable;
use EasyAffiliate\Lib\PaymentsTable;
use EasyAffiliate\Lib\TransactionsTable;
use EasyAffiliate\Lib\BaseCtrl;

class ExportCtrl extends BaseCtrl
{
  public function load_hooks() {
    add_action( 'wp_ajax_esaf_affiliate_export_csv', [$this, 'affiliate_export_csv']);
    add_action( 'wp_ajax_esaf_transaction_export_csv', [$this, 'transaction_export_csv']);
    add_action( 'wp_ajax_esaf_click_export_csv', [$this, 'click_export_csv']);
    add_action( 'wp_ajax_esaf_payment_export_csv', [$this, 'payment_export_csv']);
  }

  public function payment_export_csv() {
    $this->export_csv('export_payments', 'esaf_payments_nonce', PaymentsTable::class, [Payment::class, 'list_table']);
  }

  public function click_export_csv() {
    $this->export_csv('export_clicks', 'esaf_clicks_nonce', ClicksTable::class, [Click::class, 'list_table']);
  }

  public function transaction_export_csv() {
    $this->export_csv('export_transactions', 'esaf_transactions_nonce', TransactionsTable::class, [Transaction::class, 'list_table']);
  }

  public function affiliate_export_csv() {
    $this->export_csv('export_affiliates', 'esaf_affiliates_nonce', AffiliatesTable::class, [User::class, 'affiliate_list_table']);
  }

  /**
   * @param $action
   * @param $nonce
   * @param string $table
   * @param callable $list_callable
   */
  private function export_csv($action, $nonce, $table, $list_callable) {
    check_ajax_referer($action, $nonce);

    $filename = $action . '-' . time();

    // Since we're running WP_List_Table headless we need to do this
    $GLOBALS['hook_suffix'] = false;
    /** @var \WP_List_Table $tab */
    $tab = new $table( );

    if(isset($_REQUEST['all']) && !empty($_REQUEST['all'])) {
      $orderby = isset($_GET['orderby']) && is_string($_GET['orderby']) ? $_GET['orderby'] : '';
      $order = isset($_GET['order']) && is_string($_GET['order']) && strtoupper($_GET['order']) == 'ASC' ? 'ASC' : 'DESC';
      $search = isset($_GET['search']) && is_string($_GET['search']) ? sanitize_text_field(wp_unslash($_GET['search'])) : '';

      $list_table = $list_callable($orderby, $order, 0, $search, 0);

      ExportHelper::render_csv($list_table['results'], $filename);
    }
    else {
      $tab->prepare_items();

      ExportHelper::render_csv( $tab->items, $filename );
    }
  }
}
