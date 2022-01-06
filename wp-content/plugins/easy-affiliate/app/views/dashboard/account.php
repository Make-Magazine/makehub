<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
use EasyAffiliate\Helpers\AppHelper;
/** @var \EasyAffiliate\Models\Options $options */
/** @var array $values */
?>
<h3><?php esc_html_e('Affiliate Profile', 'easy-affiliate'); ?></h3>
<form class="esaf-form esaf-account-form" method="post">
  <input type="hidden" name="esaf_process_account_form" value="1">

  <?php if(isset($account_saved) && $account_saved) : ?>
    <div id="esaf-account-saved"><?php esc_html_e('Your account was successfully saved', 'easy-affiliate'); ?></div>
  <?php endif; ?>

  <?php if(isset($errors) && is_array($errors) && count($errors) > 0) : ?>
    <div class="esaf-form-errors">
      <?php foreach($errors as $error) : ?>
        <div class="esaf-form-error"><strong><?php esc_html_e('ERROR', 'easy-affiliate'); ?></strong>: <?php echo esc_html($error); ?></div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <div class="esaf-form-row">
    <div class="esaf-form-label">
      <label for="esaf-dashboard-first-name"><?php esc_html_e('First Name', 'easy-affiliate'); ?><span class="esaf-required">*</span></label>
    </div>
    <input type="text" id="esaf-dashboard-first-name" name="wafp_dashboard_first_name" value="<?php echo esc_attr($values['wafp_dashboard_first_name']); ?>" required />
  </div>
  <div class="esaf-form-row">
    <div class="esaf-form-label">
      <label for="esaf-dashboard-last-name"><?php esc_html_e('Last Name', 'easy-affiliate'); ?><span class="esaf-required">*</span></label>
    </div>
    <input type="text" id="esaf-dashboard-last-name" name="wafp_dashboard_last_name" value="<?php echo esc_attr($values['wafp_dashboard_last_name']); ?>" required />
  </div>

  <?php if($options->is_payout_method_paypal()) : ?>
    <div class="esaf-form-row">
      <div class="esaf-form-label">
        <label for="esaf-dashboard-paypal-email"><?php esc_html_e('PayPal Email', 'easy-affiliate'); ?><span class="esaf-required">*</span></label>
      </div>
      <input type="email" id="esaf-dashboard-paypal-email" name="wafp_dashboard_paypal" value="<?php echo esc_attr($values['wafp_dashboard_paypal']); ?>" required />
    </div>
  <?php endif; ?>

  <?php if($options->show_address_fields_account) : ?>
    <div class="esaf-form-row">
      <div class="esaf-form-label">
        <label for="esaf-dashboard-address-one"><?php esc_html_e('Address Line 1', 'easy-affiliate'); echo $options->require_address_fields ? '<span class="esaf-required">*</span>' : ''; ?></label>
      </div>
      <input type="text" id="esaf-dashboard-address-one" name="wafp_dashboard_address_one" value="<?php echo esc_attr($values['wafp_dashboard_address_one']); ?>"<?php echo $options->require_address_fields ? ' required' : ''; ?> />
    </div>
    <div class="esaf-form-row">
      <div class="esaf-form-label">
        <label for="esaf-dashboard-address-two"><?php esc_html_e('Address Line 2', 'easy-affiliate'); ?></label>
      </div>
      <input type="text" id="esaf-dashboard-address-two" name="wafp_dashboard_address_two" value="<?php echo esc_attr($values['wafp_dashboard_address_two']); ?>" />
    </div>
    <div class="esaf-form-row">
      <div class="esaf-form-label">
        <label for="esaf-dashboard-address-city"><?php esc_html_e('City', 'easy-affiliate'); echo $options->require_address_fields ? '<span class="esaf-required">*</span>' : ''; ?></label>
      </div>
      <input type="text" id="esaf-dashboard-address-city" name="wafp_dashboard_city" value="<?php echo esc_attr($values['wafp_dashboard_city']); ?>"<?php echo $options->require_address_fields ? ' required' : ''; ?> />
    </div>
    <div class="esaf-form-row">
      <div class="esaf-form-label">
        <label for="esaf-dashboard-address-state"><?php esc_html_e('State/Province', 'easy-affiliate'); echo $options->require_address_fields ? '<span class="esaf-required">*</span>' : ''; ?></label>
      </div>
      <input type="text" id="esaf-dashboard-address-state" name="wafp_dashboard_state" value="<?php echo esc_attr($values['wafp_dashboard_state']); ?>"<?php echo $options->require_address_fields ? ' required' : ''; ?> />
    </div>
    <div class="esaf-form-row">
      <div class="esaf-form-label">
        <label for="esaf-dashboard-address-zip"><?php esc_html_e('Zip/Postal Code', 'easy-affiliate'); echo $options->require_address_fields ? '<span class="esaf-required">*</span>' : ''; ?></label>
      </div>
      <input type="text" id="esaf-dashboard-address-zip" name="wafp_dashboard_zip" value="<?php echo esc_attr($values['wafp_dashboard_zip']); ?>"<?php echo $options->require_address_fields ? ' required' : ''; ?> />
    </div>
    <div class="esaf-form-row">
      <div class="esaf-form-label">
        <label for="esaf-dashboard-address-country"><?php esc_html_e('Country', 'easy-affiliate'); echo $options->require_address_fields ? '<span class="esaf-required">*</span>' : ''; ?></label>
      </div>
      <?php echo AppHelper::countries_dropdown('wafp_dashboard_country', 'esaf-dashboard-address-country', $values['wafp_dashboard_country'], $options->require_address_fields); ?>
    </div>
  <?php endif; ?>

  <?php if($options->show_tax_id_fields_account) : ?>
    <div class="esaf-form-row esaf_tax_id_us">
      <div class="esaf-form-label">
        <label for="esaf-dashboard-address-tax-id-us"><?php esc_html_e('SSN / Tax ID', 'easy-affiliate'); ?></label>
      </div>
      <input type="text" id="esaf-dashboard-address-tax-id-us" name="wafp_dashboard_tax_id_us" value="<?php echo esc_attr($values['wafp_dashboard_tax_id_us']); ?>" />
      <p class="esaf-form-field-description"><?php esc_html_e('US Residents', 'easy-affiliate'); ?></p>
    </div>
    <div class="esaf-form-row esaf_tax_id_int">
      <div class="esaf-form-label">
        <label for="esaf-dashboard-address-tax-id-int"><?php esc_html_e('International Tax ID', 'easy-affiliate'); ?></label>
      </div>
      <input type="text" id="esaf-dashboard-address-tax-id-int" name="wafp_dashboard_tax_id_int" value="<?php echo esc_attr($values['wafp_dashboard_tax_id_int']); ?>" />
      <p class="esaf-form-field-description"><?php esc_html_e('Non-US Residents', 'easy-affiliate'); ?></p>
    </div>
  <?php endif; ?>

  <div class="esaf-form-row esaf-checkbox-row">
    <input type="checkbox" id="esaf-dashboard-unsubscribed" name="wafp_dashboard_unsubscribed" value="1"<?php echo checked($values['wafp_dashboard_unsubscribed']); ?> />
    <label for="esaf-dashboard-unsubscribed"><?php esc_html_e('Unsubscribe from commission notification emails', 'easy-affiliate'); ?></label>
  </div>

  <?php do_action('esaf-dashboard-account-fields'); ?>

  <div class="esaf-form-button-row">
    <button><?php esc_html_e('Save Profile', 'easy-affiliate'); ?></button>
  </div>
</form>
