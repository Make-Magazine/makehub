<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
use EasyAffiliate\Helpers\AppHelper;
use EasyAffiliate\Helpers\ExportHelper;
/** @var \EasyAffiliate\Lib\ClicksTable $list_table */
?>
<div class="wrap">
  <?php AppHelper::plugin_title(__('Clicks','easy-affiliate')); ?>
  <form method="get">
    <input type="hidden" name="page" value="easy-affiliate-clicks">
    <?php
      $list_table->search_box(esc_html__('Search Clicks', 'easy-affiliate'), 'esaf-search-clicks');
      $list_table->display();
    ?>
  </form>
  <?php
    ExportHelper::export_table_link('esaf_click_export_csv', 'export_clicks', 'esaf_clicks_nonce', count($list_table->items));
  ?> |
  <?php
    ExportHelper::export_table_link('esaf_click_export_csv', 'export_clicks', 'esaf_clicks_nonce', $list_table->get_pagination_arg('total_items'), true);
  ?>
</div>
