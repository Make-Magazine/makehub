<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
use EasyAffiliate\Helpers\AppHelper;
?>
<h3><?php esc_html_e('Payment History', 'easy-affiliate'); ?></h3>
<div class="esaf-dashboard-payments-header">
  <?php echo file_get_contents(ESAF_IMAGES_PATH . '/payments.svg'); ?>
  <div class="esaf-dashboard-payments-header-label"><?php esc_html_e('Current Balance', 'easy-affiliate'); ?></div>
  <div class="esaf-dashboard-payments-header-value"><?php echo esc_html(AppHelper::format_currency($owed)); ?></div>
</div>
<?php if(is_array($payments) && count($payments)) : ?>
  <table id="esaf-affilate-payments-table" cellspacing="0">
    <thead>
      <tr>
        <th class="manage-column"><?php esc_html_e('Date', 'easy-affiliate'); ?></th>
        <th class="manage-column"><?php esc_html_e('Payout', 'easy-affiliate'); ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($payments as $payment) : ?>
        <?php
          $date = $payment->year . '-' . $payment->month . '-01';
          $date = strtotime($date);
        ?>
        <tr>
          <td><?php echo date('M', $date) . ' ' . date('Y', $date); ?></td>
          <td><?php echo esc_html(AppHelper::format_currency( (float)$payment->paid )); ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php else : ?>
  <p><?php esc_html_e('No payments found.', 'easy-affiliate'); ?></p>
<?php endif; ?>
