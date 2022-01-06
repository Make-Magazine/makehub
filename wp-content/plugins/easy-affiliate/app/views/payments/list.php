<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
use EasyAffiliate\Helpers\AppHelper;
use EasyAffiliate\Helpers\ExportHelper;
/** @var \EasyAffiliate\Lib\PaymentsTable $list_table */
?>
<div class="wrap">
  <?php AppHelper::plugin_title(__('Payments sent to affiliates','easy-affiliate')); ?>
  <form id="esaf-payout-history-table" method="get" action="<?php echo esc_url(admin_url('admin.php?page=easy-affiliate-payments')); ?>">
    <input type="hidden" name="page" value="easy-affiliate-payments" />
    <?php
      // Remove action results from sortable headers and row actions
      $_SERVER['REQUEST_URI'] = remove_query_arg(['deleted'], $_SERVER['REQUEST_URI']);

      $list_table->search_box(esc_html__('Search Payments', 'easy-affiliate'), 'esaf-search-payments');
      $list_table->display();
    ?>
    <?php
      ExportHelper::export_table_link('esaf_payment_export_csv', 'export_payments', 'esaf_payments_nonce', count($list_table->items));
    ?> |
    <?php
      ExportHelper::export_table_link('esaf_payment_export_csv', 'export_payments', 'esaf_payments_nonce', $list_table->get_pagination_arg('total_items'), true);
    ?>
  </form>
</div>
