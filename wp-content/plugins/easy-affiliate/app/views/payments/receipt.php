<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
use EasyAffiliate\Helpers\AppHelper;
use EasyAffiliate\Models\User;
?>
<div id="wafp-admin-affiliate-panel" class="wrap">
  <?php AppHelper::plugin_title(__('Affiliate Commission Summary','easy-affiliate')); ?>
  <?php if($options->payment_type === 'paypal-1-click' && (empty($options->paypal_secret_id) || empty($options->paypal_client_id))) : ?>
    <div class="notice notice-error">
      <p>
        <?php
          printf(
            // translators: %1$s: open link tag, %2$s: close link tag
            esc_html__('You must enter a PayPal Client ID and Secret Key in the %1$sEasy Affiliate settings%2$s to use PayPal 1-Click payouts.', 'easy-affiliate'),
            sprintf('<a href="%s">', esc_html(admin_url('admin.php?page=easy-affiliate-settings#esaf-commissions'))),
            '</a>'
          );
        ?>
      </p>
    </div>
  <?php endif; ?>
  <table id="esaf-admin-payouts-table" class="widefat fixed" data-payouts="<?php echo esc_attr(wp_json_encode($payouts)); ?>" data-period="<?php echo esc_attr($period); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce('esaf_payout')); ?>" data-batch-id="<?php echo esc_attr('EA_Payouts_' . time()); ?>">
    <thead>
      <tr>
        <th class="manage-column wafp-pay-affiliate-col"><?php esc_html_e('Affiliate', 'easy-affiliate'); ?></th>
        <th class="manage-column wafp-pay-name-col"><?php esc_html_e('Name', 'easy-affiliate'); ?></th>
        <th class="manage-column wafp-pay-paypal-col"><?php esc_html_e('PayPal Email', 'easy-affiliate'); ?></th>
        <th class="manage-column wafp-pay-paid-col"><?php esc_html_e('Amount', 'easy-affiliate'); ?></th>
      </tr>
    </thead>
    <tbody>
      <?php
        $total_to_pay = 0;

        foreach($payouts as $affiliate_id => $amount) :
          $affiliate_id = (int) $affiliate_id;
          $affiliate    = new User($affiliate_id);
          $amount       = (float) $amount;
          $paypal_email = $affiliate->paypal_email;
          $total_to_pay += $amount;
      ?>
      <tr>
        <td><?php echo esc_html($affiliate->user_login); ?></td>
        <td><?php echo esc_html($affiliate->first_name . ' ' . $affiliate->last_name); ?></td>
        <td><?php echo empty($paypal_email) ? esc_html__('none', 'easy-affiliate') : esc_html($paypal_email); ?></td>
        <td><?php echo esc_html(AppHelper::format_currency($amount)); ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr>
        <td></td>
        <td></td>
        <td class="esaf-text-align-right"><?php esc_html_e('Total', 'easy-affiliate'); ?></td>
        <td><?php echo esc_html(AppHelper::format_currency($total_to_pay)); ?></td>
      </tr>
    </tfoot>
  </table>
  <?php if($options->payment_type === 'paypal') : ?>
    <p class="wafp-trans-submit-wrap">
      <button type="button" class="button button-primary esaf-affiliate-payout-button" id="esaf-paypal-bulk-payment">
        <?php esc_html_e('Mark Commissions as Paid', 'easy-affiliate'); ?>
      </button>
    </p>
  <?php elseif($options->payment_type === 'manual') : ?>
    <p class="wafp-trans-submit-wrap">
      <button type="button" class="button button-primary esaf-affiliate-payout-button" id="esaf-manual-bulk-payment">
        <?php esc_html_e('Mark Commissions as Paid', 'easy-affiliate'); ?>
      </button>
    </p>
  <?php elseif($options->payment_type === 'paypal-1-click') : ?>
    <p class="wafp-trans-submit-wrap">
      <button type="button" class="button button-primary esaf-affiliate-payout-button" id="esaf-paypal-1-click-bulk-payment" <?php disabled(empty($options->paypal_secret_id) || empty($options->paypal_client_id)); ?> >
        <?php esc_html_e('Pay with PayPal 1-Click', 'easy-affiliate'); ?>
      </button>
    </p>
  <?php endif; ?>
</div>
<div align="center" id="esaf-success-popup-manual-payment" class="esaf-popup mfp-hide">
  <div class="notice notice-success inline">
    <p><?php esc_html_e('Success! You\'ve just marked your Affiliates commission as paid.', 'easy-affiliate'); ?></p>
  </div>
  <p align="center">
    <a id="esaf-download-manual-pay-file-link" class="button"><?php esc_html_e('Download Payouts to CSV', 'easy-affiliate'); ?></a>
    <br>
    <br>
    <a href="<?php echo esc_url(admin_url('admin.php?page=easy-affiliate-payments')); ?>" class="button-primary"><?php esc_html_e('Finish', 'easy-affiliate'); ?></a>
  </p>
</div>
<div align="center" id="esaf-success-popup-manual-paypal-payment" class="esaf-popup mfp-hide">
  <div class="notice notice-success inline">
    <p><?php esc_html_e('Success! You\'ve just marked your Affiliates commission as paid.', 'easy-affiliate'); ?></p>
  </div>
  <p align="center">
    <a id="esaf-download-manual-paypal-mass-pay-file-link" class="button"><?php esc_html_e('Download PayPal Mass Payment File', 'easy-affiliate'); ?></a>
    <a id="esaf-download-manual-paypal-pay-file-link" class="button"><?php esc_html_e('Download Payouts to CSV', 'easy-affiliate'); ?></a>
    <br>
    <br>
    <a href="<?php echo esc_url(admin_url('admin.php?page=easy-affiliate-payments')); ?>" class="button-primary"><?php esc_html_e('Finish', 'easy-affiliate'); ?></a>
  </p>
</div>
<div align="center" id="esaf-success-popup-auto-paypal-payment" class="esaf-popup mfp-hide">
  <div class="notice notice-success inline">
    <p><?php esc_html_e('Success! You\'ve just paid your Affiliates\' commissions.', 'easy-affiliate'); ?></p>
  </div>
  <p align="center">
    <a id="esaf-download-auto-paypal-pay-file-link" class="button"><?php esc_html_e('Download Payouts to CSV', 'easy-affiliate'); ?></a>
    <br>
    <br>
    <a href="<?php echo esc_url(admin_url('admin.php?page=easy-affiliate-payments')); ?>" class="button-primary"><?php esc_html_e('Finish', 'easy-affiliate'); ?></a>
  </p>
</div>
<div align="center" id="esaf-fail-popup-auto-paypal-payment" class="esaf-popup mfp-hide">
  <div class="notice notice-error inline">
    <p><strong><?php esc_html_e('Oops! There was an error paying with PayPal', 'easy-affiliate'); ?></strong></p>
    <p class="esaf-hidden"></p>
  </div>
  <p align="center">
    <button id="esaf-auto-paypal-fail-try-again" class="button-primary"><?php esc_html_e('Try Again', 'easy-affiliate'); ?></button>
    <button id="esaf-auto-paypal-fail-pay-with-mass-file" class="button-primary"><?php esc_html_e('Pay with Mass Payment File', 'easy-affiliate'); ?></button>
  </p>
</div>
<div align="center" id="esaf-fail-popup-payment" class="esaf-popup mfp-hide">
  <div class="notice notice-error inline">
    <p><strong><?php esc_html_e('Sorry, there was an error marking the commissions as paid.', 'easy-affiliate'); ?></strong></p>
    <p class="esaf-hidden"></p>
  </div>
</div>
