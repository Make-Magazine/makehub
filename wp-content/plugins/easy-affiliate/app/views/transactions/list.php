<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
use EasyAffiliate\Helpers\AppHelper;
use EasyAffiliate\Helpers\ExportHelper;
/** @var \EasyAffiliate\Lib\TransactionsTable $list_table */
?>
<div class="wrap">
  <?php
    AppHelper::plugin_title(
      __('Transactions referred by Affiliates', 'easy-affiliate'),
      sprintf(
        '<a href="%s" class="add-new-h2">%s</a>',
        esc_url(admin_url('admin.php?page=easy-affiliate-transactions&action=new')),
        esc_html__('Add New', 'easy-affiliate')
      )
    );
  ?>
  <form method="get">
    <input type="hidden" name="page" value="easy-affiliate-transactions">
    <?php
      $list_table->search_box(esc_html__('Search Transactions', 'easy-affiliate'), 'esaf-search-transactions');
      $list_table->display();
    ?>
  </form>
  <?php
    ExportHelper::export_table_link('esaf_transaction_export_csv', 'export_transactions', 'esaf_transactions_nonce', count($list_table->items));
  ?> |
  <?php
    ExportHelper::export_table_link('esaf_transaction_export_csv', 'export_transactions', 'esaf_transactions_nonce', $list_table->get_pagination_arg('total_items'), true);
  ?>
</div>
