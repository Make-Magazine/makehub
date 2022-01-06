<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

use EasyAffiliate\Helpers\AppHelper;
use EasyAffiliate\Lib\Config;
use EasyAffiliate\Lib\Utils;
use EasyAffiliate\Models\Commission;
use EasyAffiliate\Models\User;

if(!empty($records)) {
  $row_index = 0;
  foreach($records as $rec) {
    $alternate = ( $row_index++ % 2 ? '' : 'alternate' );

    //Open the line
    ?>
    <tr id="record_<?php echo esc_attr($rec->id); ?>" class="<?php echo esc_attr($alternate); ?> <?php echo empty($rec->flag) ? '' : 'esaf-flagged-txn esaf-' . $rec->flag; ?>">
    <?php
    foreach( $columns as $column_name => $column_display_name ) {
      //Style attributes for each col
      $attributes = 'class="' . esc_attr("$column_name column-$column_name") . (in_array($column_name, $hidden) ? ' hidden' : '') . '"';
      $editlink = admin_url( 'user-edit.php?user_id=' . (int)$rec->affiliate_id );

      //Display the cell
      switch( $column_name ) {
        case 'col_id':
          ?>
          <td <?php echo $attributes; ?>>
            <?php do_action('esaf_transaction_column_id', $rec); ?>
            <?php echo esc_html($rec->id); ?>
          </td>
          <?php
          break;
        case 'col_created_at':
          ?>
          <td <?php echo $attributes; ?>><?php echo esc_html(Utils::format_datetime($rec->created_at)); ?></td>
          <?php
          break;
        case 'col_user_login':
          ?>
          <td <?php echo $attributes; ?>><a href="<?php echo esc_url($editlink); ?>"><?php echo esc_html($rec->user_login); ?></a></td>
          <?php
          break;
        case 'col_item_name':
          ?>
          <td <?php echo $attributes; ?>><?php echo esc_html($rec->item_name); ?></td>
          <?php
          break;
        case 'col_trans_num':
          ?>
          <td <?php echo $attributes; ?>><?php echo esc_html(apply_filters('esaf-invoice-num', $rec->trans_num, $rec->id)); ?></td>
          <?php
          break;
        case 'col_source':
          $integrations = Config::get('integrations');

          if(is_wp_error($integrations) || !isset($integrations[$rec->source])) {
            if($rec->source == 'general') {
              $source = esc_html__('General', 'easy-affiliate');
            }
            else {
              $source = esc_html__('Unknown', 'easy-affiliate');
            }
          }
          else {
            $source = esc_html($integrations[$rec->source]['label']);
            $source = apply_filters('esaf_transaction_source_label', $source, $rec);
          }
          ?>
          <td <?php echo $attributes; ?>><?php echo $source; ?></td>
          <?php
          break;
        case 'col_sale_amount':
          ?>
          <td <?php echo $attributes; ?>><?php echo esc_html(AppHelper::format_currency( (float)$rec->sale_amount)); ?></td>
          <?php
          break;
        case 'col_refund_amount':
          ?>
          <td <?php echo $attributes; ?>><?php echo esc_html(AppHelper::format_currency( (float)$rec->refund_amount )); ?></td>
          <?php
          break;
        case 'col_total_amount':
          ?>
          <td <?php echo $attributes; ?>><?php echo esc_html(AppHelper::format_currency( (float)$rec->total_amount )); ?></td>
          <?php
          break;
        case 'col_referring_page':
          ?>
          <td <?php echo $attributes; ?>>
            <?php if (!empty($rec->referring_page)) : ?>
              <a href="<?php echo esc_url($rec->referring_page); ?>" target="_blank"><?php echo esc_url($rec->referring_page); ?></a>
            <?php endif; ?>
          </td>
          <?php
          break;
        case 'col_commission_amount':
          ?>
          <td <?php echo $attributes; ?>><?php echo esc_html(AppHelper::format_currency( (float)$rec->commission_amount )); ?></td>
          <?php
          break;
        case 'col_actions':
          ?>
          <td <?php echo $attributes; ?>>
            <i class="ea-icon ea-icon-eye esaf-view-transaction-details" title="<?php esc_attr_e('View details', 'easy-affiliate'); ?>"></i>
            <a href="<?php echo esc_url(admin_url("admin.php?page=easy-affiliate-transactions&action=edit&id={$rec->id}")); ?>"><i class="ea-icon ea-icon-pencil" title="<?php esc_attr_e('Edit', 'easy-affiliate'); ?>"></i></a>
            <i class="ea-icon ea-icon-trash esaf-delete-transaction" data-transaction-id="<?php echo esc_attr($rec->id); ?>" title="<?php esc_attr_e('Delete', 'easy-affiliate'); ?>"></i>
            <div class="esaf-popup esaf-view-transaction-details-popup mfp-hide">
              <div class="esaf-popup-content">
                <h3><?php esc_html_e('Recorded Commissions', 'easy-affiliate'); ?></h3>
                <?php
                  $commissions = Commission::get_all_by_transaction_id($rec->id, 'commission_level');
                  $commissions_count = count($commissions);
                ?>
                <table class="wp-list-table widefat fixed wp_list_wafp_transactions">
                  <thead>
                    <tr>
                      <?php if($commissions_count > 1) : ?>
                        <th scope="col" class="manage-column"><?php esc_html_e('Level','easy-affiliate'); ?></th>
                      <?php endif; ?>
                      <th scope="col" class="manage-column"><?php esc_html_e('Affiliate','easy-affiliate'); ?></th>
                      <th scope="col" class="manage-column"><?php esc_html_e('Commissions','easy-affiliate'); ?></th>
                      <th scope="col" class="manage-column"><?php esc_html_e('Commission Amount','easy-affiliate'); ?></th>
                      <th scope="col" class="manage-column"><?php esc_html_e('Voided Amount','easy-affiliate'); ?></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach($commissions as $commish) : ?>
                      <?php $aff = new User($commish->affiliate_id); ?>
                      <tr>
                        <?php if($commissions_count > 1) : ?>
                          <td class="manage-column"><?php echo esc_html($commish->commission_level + 1); ?></td>
                        <?php endif; ?>
                        <td class="manage-column"><a href="<?php echo esc_url(admin_url("user-edit.php?user_id={$commish->affiliate_id}", 'relative')); ?>"><?php echo esc_html($aff->full_name() . ' (' . $aff->user_login . ')'); ?></a></td>
                        <td class="manage-column"><?php echo ( $commish->commission_type == 'fixed' ? esc_html(AppHelper::format_currency($commish->commission_percentage)) : esc_html(Utils::format_float($commish->commission_percentage) . '%' )); ?></td>
                        <td class="manage-column"><?php echo esc_html(AppHelper::format_currency($commish->commission_amount)); ?></td>
                        <td class="manage-column"><?php echo esc_html(AppHelper::format_currency($commish->correction_amount)); ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </td>
          <?php
          break;
      }
    }
  }
}
