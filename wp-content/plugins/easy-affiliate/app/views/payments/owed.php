<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

use EasyAffiliate\Helpers\AppHelper;
use EasyAffiliate\Helpers\ReportsHelper;
use EasyAffiliate\Lib\Utils;
use EasyAffiliate\Models\Options;
use EasyAffiliate\Models\User;

$options = Options::fetch();
$paypal_on = $options->is_payout_method_paypal();

?>
<div id="wafp-admin-affiliate-panel" class="wrap">
  <?php AppHelper::plugin_title(__('Pay Affiliates','easy-affiliate')); ?>
  <?php do_action('esaf_affiliate_payment_list_notice', $totals, $results); ?>
<form action="" method="post">
<input type="hidden" name="esaf_process_payouts" value="Y" />
<input type="hidden" name="esaf_payout_period" value="<?php echo esc_attr($period); ?>" />
<p><?php esc_html_e('Select the period you want to view', 'easy-affiliate'); ?>:<br/><?php ReportsHelper::periods_dropdown('wafp-report-period', $period, 'javascript:EsafAdmin.view_admin_affiliate_page( \'admin_affiliate_payments\', this.value, 1, \'\', true);'); ?>&nbsp;&nbsp;<img src="<?php echo esc_url(admin_url('images/loading.gif')); ?>" alt="<?php esc_attr_e('Loading...', 'easy-affiliate'); ?>" style="display: none;" class="wafp-stats-loader" /></p>
<table class="widefat post fixed wafp-owed-payments-table" cellspacing="0">
<thead>
  <tr>
    <th class="manage-column wafp-pay-affiliate-col"><?php esc_html_e('Affiliate', 'easy-affiliate'); ?></th>
    <th class="manage-column wafp-pay-name-col"><?php esc_html_e('Name', 'easy-affiliate'); ?></th>
    <th class="manage-column wafp-status-col"><?php esc_html_e('Status', 'easy-affiliate'); ?></th>
    <?php if( $paypal_on ): ?>
      <th class="manage-column wafp-pay-paypal-col"><?php esc_html_e('PayPal Email', 'easy-affiliate'); ?></th>
    <?php endif; ?>
    <th class="manage-column wafp-pay-payment-col"><?php esc_html_e('Net Commissions', 'easy-affiliate'); ?></th>
    <th class="manage-column wafp-pay-paid-col"><?php esc_html_e('Pay', 'easy-affiliate'); ?></th>
  </tr>
</thead>
<tbody>
<?php
  if(empty($totals)):
   ?>
   <tr>
     <td colspan="<?php if( $paypal_on ) echo "6"; else echo "5"; ?>"><?php esc_html_e('No Payments are due for this period.', 'easy-affiliate'); ?></td>
   </tr>
   <?php
  else:
    $row_index = 0;
    foreach($totals as $key => $total):
      $row = $results[$key];

      $paypal_email = get_user_meta($row->aff_id, 'wafp_paypal_email', true);
      $first_name   = get_user_meta($row->aff_id, 'first_name', true);
      $last_name    = get_user_meta($row->aff_id, 'last_name', true);
      $address_one  = get_user_meta($row->aff_id, 'wafp_user_address_one', true);
      $address_two  = get_user_meta($row->aff_id, 'wafp_user_address_two', true);
      $city         = get_user_meta($row->aff_id, 'wafp_user_city', true);
      $state        = get_user_meta($row->aff_id, 'wafp_user_state', true);
      $zip          = get_user_meta($row->aff_id, 'wafp_user_zip', true);
      $country      = get_user_meta($row->aff_id, 'wafp_user_country', true);
      $is_blocked   = get_user_meta($row->aff_id, 'wafp_is_blocked', true);
      $is_affiliate = get_user_meta($row->aff_id, 'wafp_is_affiliate', true);

      if(isset($row->flagged_clicks, $row->flagged_transactions)) {
        $flagged = $row->flagged_clicks + $row->flagged_transactions;
      }
      else {
        $flagged = 0;
      }

      if((float)$row->correction_amount > 0.00)
        $correction = "<span style=\"color: red\">(" . esc_html(AppHelper::format_currency( (float)$row->correction_amount)) . ")</span>";
      else
        $correction = esc_html(AppHelper::format_currency( (float)$row->correction_amount));

      $alternate = ( $row_index++ % 2 ? '' : 'alternate' );

      $error = ( !$is_affiliate or $is_blocked or
                 ( $paypal_on and empty($paypal_email) ) or
                 ( $paypal_on && !is_email($paypal_email) ) or
                 ( $options->minimum > (float)$total ) ) ? 'esaf-error' : '';

      if( !$is_affiliate ) {
        $status = '<strong>'. esc_html__('Not Affiliate', 'easy-affiliate') . '</strong>';
      }
      else if( $is_blocked ) {
        $status = '<strong>' . esc_html__('Blocked', 'easy-affiliate') . '</strong>';
      }
      else if( $paypal_on and empty($paypal_email) ) {
        $status = '<strong>' . esc_html__('No PayPal Email', 'easy-affiliate') . '</strong>';
      }
      else if($paypal_on && !is_email($paypal_email)) {
        $status = '<strong>' . esc_html__('Invalid PayPal Email', 'easy-affiliate') . '</strong>';
      }
      else if( $options->minimum > (float)$total ) {
        $status = '<strong>' . esc_html__('Below Minimum', 'easy-affiliate') . '</strong>';
      }
      else {
        $status = esc_html__('Eligible', 'easy-affiliate');
      }

    $aff = new User();
    $aff->load_user_data_by_login( $row->aff_login );

    $profile_url = add_query_arg(['user_id' => (int) $aff->ID], admin_url('user-edit.php'));
    $clicks_url  = add_query_arg(['s' => urlencode($row->aff_login)], admin_url('admin.php?page=easy-affiliate-clicks'));
    $txns_url    = add_query_arg(['s' => urlencode($row->aff_login)], admin_url('admin.php?page=easy-affiliate-transactions'));

    ?>
  <tr class="<?php echo esc_attr("{$alternate} {$error}"); if(!empty($flagged)) echo " flagged-txn-or-click "; ?>">
    <td>
      <?php do_action('esaf_owned_column_login', $flagged, $key); ?>
      <a href="<?php echo esc_url($profile_url); ?>"><strong><?php echo esc_html($row->aff_login); ?></strong></a>
      <div class="wafp-row-actions"><a href="<?php echo esc_url($clicks_url); ?>"><?php esc_html_e('Clicks', 'easy-affiliate'); ?></a> | <a href="<?php echo esc_url($txns_url); ?>"><?php esc_html_e('Sales', 'easy-affiliate'); ?></a></div></td>
    <td><?php echo esc_html("{$first_name} {$last_name}"); ?></td>
    <td><?php echo $status; ?></td>
    <?php if($paypal_on) : ?>
      <td><?php echo empty($paypal_email) ? esc_html__('none', 'easy-affiliate') : esc_html($paypal_email); ?></td>
    <?php endif; ?>
    <td><?php echo esc_html(AppHelper::format_currency( (float) $total)); ?></td>
    <td><input type="checkbox" class="esaf-payouts-checkbox" name="esaf_payouts[<?php echo esc_attr($row->aff_id); ?>]" value="<?php echo esc_attr(Utils::format_float((float) $total)); ?>" /></td>
  </tr>
<?php endforeach; ?>
<?php endif; ?>
</tbody>
</table>
<p class="wafp-trans-submit-wrap">
  <input type="submit" id="esaf-pay-selected-commissions" class="wafp-trans-submit button-primary" value="<?php esc_attr_e('Pay Selected Commissions &rarr;', 'easy-affiliate'); ?>" name="submit" />
</p>
</form>
<?php
if(isset($prev_page))
{
  ?>
<span style="float: right;"><a href="javascript:EsafAdmin.view_admin_affiliate_page('admin_affiliate_payments',<?php echo esc_js($period); ?>,<?php echo esc_js($prev_page); ?>);"><?php esc_html_e('Previous Payments', 'easy-affiliate'); ?></a>&nbsp;&raquo;</span>
  <?php
}

if(isset($next_page))
{
  ?>
<span>&laquo;&nbsp;<a href="javascript:EsafAdmin.view_admin_affiliate_page('admin_affiliate_payments',<?php echo esc_js($period); ?>,<?php echo esc_js($next_page); ?>);"><?php esc_html_e('Next Payments', 'easy-affiliate'); ?></a></span>
  <?php
}
?>
</div>
