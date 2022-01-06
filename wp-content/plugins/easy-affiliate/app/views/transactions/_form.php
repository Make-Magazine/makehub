<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

use EasyAffiliate\Lib\Config;
use EasyAffiliate\Lib\Utils;

$integrations = Config::get('integrations');

if(is_wp_error($integrations)) {
  Utils::error_log($integrations->get_error_message());
  $integrations = array();
}
?>
<tr valign="top">
  <th scope="row"><label for="wafp-affiliate-referrer"><?php esc_html_e('Affiliate*:', 'easy-affiliate'); ?></label></th>
  <td>
    <?php wp_nonce_field( 'easy-affiliate-trans' ); ?>
    <input type="text" name="referrer" id="wafp-affiliate-referrer" class="regular-text" value="<?php echo esc_attr($referrer); ?>" autocomplete="off">
    <p class="description"><?php esc_html_e('The affiliate who referred this transaction.', 'easy-affiliate'); ?></p>
  </td>
</tr>

<tr valign="top">
  <th scope="row"><label for="<?php echo esc_attr($txn->cust_name_str); ?>"><?php esc_html_e('Customer Name (optional):', 'easy-affiliate'); ?></label></th>
  <td>
    <input type="text" name="<?php echo esc_attr($txn->cust_name_str); ?>" id="<?php echo esc_attr($txn->cust_name_str); ?>" class="regular-text" value="<?php echo esc_attr($txn->cust_name); ?>" autocomplete="off">
    <p class="description"><?php esc_html_e('The Customer\'s Full Name. Not required.', 'easy-affiliate'); ?></p>
  </td>
</tr>

<tr valign="top">
  <th scope="row"><label for="<?php echo esc_attr($txn->cust_email_str); ?>"><?php esc_html_e('Customer Email (optional):', 'easy-affiliate'); ?></label></th>
  <td>
    <input type="text" name="<?php echo esc_attr($txn->cust_email_str); ?>" id="<?php echo esc_attr($txn->cust_email_str); ?>" class="regular-text" value="<?php echo esc_attr($txn->cust_email); ?>" autocomplete="off">
    <p class="description"><?php esc_html_e('The Customer\'s Email Address. Not required.', 'easy-affiliate'); ?></p>
  </td>
</tr>

<tr valign="top">
  <th scope="row"><label for="<?php echo esc_attr($txn->item_name_str); ?>"><?php esc_html_e('Product*:', 'easy-affiliate'); ?></label></th>
  <td>
    <input type="text" name="<?php echo esc_attr($txn->item_name_str); ?>" id="<?php echo esc_attr($txn->item_name_str); ?>" value="<?php echo esc_attr($txn->item_name); ?>" class="regular-text" />
    <p class="description"><?php esc_html_e('The product that was purchased', 'easy-affiliate'); ?></p>
  </td>
</tr>

<tr valign="top">
  <th scope="row"><label for="<?php echo esc_attr($txn->trans_num_str); ?>"><?php esc_html_e('Unique Order ID*:', 'easy-affiliate'); ?></label></th>
  <td>
    <input type="text" name="<?php echo esc_attr($txn->trans_num_str); ?>" id="<?php echo esc_attr($txn->trans_num_str); ?>" value="<?php echo esc_attr($txn->trans_num); ?>" class="regular-text" />
    <p class="description"><?php esc_html_e('The unique order id of this transaction.', 'easy-affiliate'); ?></p>
  </td>
</tr>

<tr valign="top">
  <th scope="row"><label for="<?php echo esc_attr($txn->source_str); ?>"><?php esc_html_e('Source*:', 'easy-affiliate'); ?></label></th>
  <td>
    <select name="<?php echo esc_attr($txn->source_str); ?>" id="<?php echo esc_attr($txn->source_str); ?>">
      <option value="general"><?php esc_html_e('General', 'easy-affiliate'); ?></option>
      <?php foreach($integrations as $integration_slug => $integration): ?>
        <option value="<?php echo esc_attr($integration_slug); ?>" <?php selected($integration_slug, $txn->source); ?>><?php echo esc_html($integration['label']); ?></option>
      <?php endforeach; ?>
    </select>
    <p class="description"><?php esc_html_e('The source of this transaction.', 'easy-affiliate'); ?></p>
  </td>
</tr>

<tr valign="top">
  <th scope="row"><label for="<?php echo esc_attr($txn->sale_amount_str); ?>"><?php esc_html_e('Amount*:', 'easy-affiliate'); ?></label></th>
  <td>
    <span><?php echo esc_html($options->currency_symbol); ?></span>
    <input type="text" name="<?php echo esc_attr($txn->sale_amount_str); ?>" id="<?php echo esc_attr($txn->sale_amount_str); ?>" value="<?php echo esc_attr(Utils::format_float($txn->sale_amount)); ?>" class="regular-text" style="width:95px !important;"/>
    <p class="description"><?php esc_html_e('The sale amount of this transaction', 'easy-affiliate'); ?></p>
  </td>
</tr>

<tr valign="top">
  <th scope="row"><label for="<?php echo esc_attr($txn->refund_amount_str); ?>"><?php esc_html_e('Refund Amount*:', 'easy-affiliate'); ?></label></th>
  <td>
    <span><?php echo esc_html($options->currency_symbol); ?></span>
    <input type="text" name="<?php echo esc_attr($txn->refund_amount_str); ?>" id="<?php echo esc_attr($txn->refund_amount_str); ?>" value="<?php echo esc_attr(Utils::format_float($txn->refund_amount)); ?>" class="regular-text" style="width:95px !important;"/>
    <p class="description"><?php esc_html_e('The refund amount of this transaction', 'easy-affiliate'); ?></p>
  </td>
</tr>

<?php do_action('esaf_transaction_edit_form_bottom', $txn); ?>
