<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
use EasyAffiliate\Helpers\AppHelper;
use EasyAffiliate\Models\Options;

$options = Options::fetch();
$statuses = [
  'processing' => __('Processing', 'easy-affiliate'),
  'completed' => __('Completed', 'easy-affiliate'),
];
?>
<div class="esaf-payment-integration-extra-config">
  <label for="<?php echo esc_attr($options->woocommerce_integration_order_status_str); ?>">
    <?php esc_html_e('Order status', 'easy-affiliate'); ?>
    <?php AppHelper::info_tooltip(
      'esaf-payment-integration-woocommerce-order-status',
      esc_html__('The order status in WooCommerce which will generate the affiliate commission', 'easy-affiliate')
    );?>
  </label>
  <div class="esaf-extra-config-input">
    <select id="<?php echo esc_attr($options->woocommerce_integration_order_status_str); ?>" name="<?php echo esc_attr($options->woocommerce_integration_order_status_str); ?>">
      <?php foreach ($statuses as $key => $status) : ?>
        <option <?php selected($options->woocommerce_integration_order_status, $key); ?> value="<?php echo esc_attr($key); ?>"><?php echo esc_html($status); ?></option>
      <?php endforeach; ?>
    </select>
  </div>
</div>
