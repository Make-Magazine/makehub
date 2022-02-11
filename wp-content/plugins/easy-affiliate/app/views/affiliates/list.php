<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
use EasyAffiliate\Helpers\AppHelper;
use EasyAffiliate\Helpers\ExportHelper;
/** @var \EasyAffiliate\Lib\AffiliatesTable $aff_table */
?>
<div class="wrap">
  <?php AppHelper::plugin_title(__('Affiliates', 'easy-affiliate')); ?>
  <form method="get">
    <input type="hidden" name="page" value="easy-affiliate-affiliates">
    <?php
      $aff_table->search_box(esc_html__('Search Affiliates', 'easy-affiliate'), 'esaf-search-affiliates');
      $aff_table->display();
    ?>
  </form>
  <div id="esaf-affiliate-notes-popup" class="esaf-popup mfp-hide">
    <div class="esaf-popup-content">
      <h2></h2>
      <textarea></textarea>
      <input type="hidden">
      <button type="button" id="esaf-affiliate-notes-save" class="button button-primary"><?php esc_html_e('Save', 'easy-affiliate'); ?></button>
    </div>
  </div>
  <?php
    ExportHelper::export_table_link('esaf_affiliate_export_csv', 'export_affiliates', 'esaf_affiliates_nonce', count($aff_table->items));
  ?> |
  <?php
    ExportHelper::export_table_link('esaf_affiliate_export_csv', 'export_affiliates', 'esaf_affiliates_nonce', $aff_table->get_pagination_arg('total_items'), true);
  ?>
</div>
