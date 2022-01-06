<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
use EasyAffiliate\Helpers\AppHelper;
/** @var \EasyAffiliate\Models\Options $options */
?>
<div class="esaf-wizard-white-box">
  <div class="esaf-wizard-box-title">
    <h2><?php esc_html_e('Business Information', 'easy-affiliate'); ?></h2>
    <p><?php esc_html_e('Set up your Easy Affiliate business information.', 'easy-affiliate'); ?></p>
  </div>
  <div class="esaf-wizard-box-content esaf-wizard-box-content-business">
    <table class="form-table">
      <tbody>
        <tr>
          <th scope="row">
            <label for="<?php echo esc_attr($options->business_name_str); ?>"><?php esc_html_e('Business Name', 'easy-affiliate') ?>*</label>
          </th>
          <td>
            <input type="text" id="<?php echo esc_attr($options->business_name_str); ?>" name="<?php echo esc_attr($options->business_name_str); ?>" value="<?php echo esc_attr($options->business_name); ?>">
          </td>
        </tr>
        <tr>
          <th scope="row">
            <label for="<?php echo esc_attr($options->business_address_one_str); ?>"><?php esc_html_e('Address Line 1', 'easy-affiliate') ?>*</label>
          </th>
          <td>
            <input type="text" id="<?php echo esc_attr($options->business_address_one_str); ?>" name="<?php echo esc_attr($options->business_address_one_str); ?>" value="<?php echo esc_attr($options->business_address_one); ?>">
          </td>
        </tr>
        <tr>
          <th scope="row">
            <label for="<?php echo esc_attr($options->business_address_two_str); ?>"><?php esc_html_e('Address Line 2', 'easy-affiliate') ?></label>
          </th>
          <td>
            <input type="text" id="<?php echo esc_attr($options->business_address_two_str); ?>" name="<?php echo esc_attr($options->business_address_two_str); ?>" value="<?php echo esc_attr($options->business_address_two); ?>">
          </td>
        </tr>
        <tr>
          <th scope="row">
            <label for="<?php echo esc_attr($options->business_address_city_str); ?>"><?php esc_html_e('City', 'easy-affiliate') ?>*</label>
          </th>
          <td>
            <input type="text" id="<?php echo esc_attr($options->business_address_city_str); ?>" name="<?php echo esc_attr($options->business_address_city_str); ?>" value="<?php echo esc_attr($options->business_address_city); ?>">
          </td>
        </tr>
        <tr>
          <th scope="row">
            <label for="<?php echo esc_attr($options->business_address_state_str); ?>"><?php esc_html_e('State', 'easy-affiliate') ?>*</label>
          </th>
          <td>
            <input type="text" id="<?php echo esc_attr($options->business_address_state_str); ?>" name="<?php echo esc_attr($options->business_address_state_str); ?>" value="<?php echo esc_attr($options->business_address_state); ?>">
          </td>
        </tr>
        <tr>
          <th scope="row">
            <label for="<?php echo esc_attr($options->business_address_zip_str); ?>"><?php esc_html_e('Postcode', 'easy-affiliate') ?>*</label>
          </th>
          <td>
            <input type="text" id="<?php echo esc_attr($options->business_address_zip_str); ?>" name="<?php echo esc_attr($options->business_address_zip_str); ?>" value="<?php echo esc_attr($options->business_address_zip); ?>">
          </td>
        </tr>
        <tr>
          <th scope="row">
            <label for="<?php echo esc_attr($options->business_address_country_str); ?>"><?php esc_html_e('Country', 'easy-affiliate') ?>*</label>
          </th>
          <td>
            <?php echo AppHelper::countries_dropdown($options->business_address_country_str, $options->business_address_country_str, $options->business_address_country); ?>
          </td>
        </tr>
        <tr>
          <th scope="row">
            <label for="<?php echo esc_attr($options->business_tax_id_str); ?>"><?php esc_html_e('SSN/EIN', 'easy-affiliate') ?></label>
          </th>
          <td>
            <?php if(empty($options->business_tax_id)) : ?>
              <input type="text" id="<?php echo esc_attr($options->business_tax_id_str); ?>" name="<?php echo esc_attr($options->business_tax_id_str); ?>">
            <?php else : ?>
              <button type="button" id="esaf-change-saved-ssn" class="button button-secondary"><?php esc_html_e('Change SSN/EIN', 'easy-affiliate') ?></button>
            <?php endif; ?>
          </td>
        </tr>
      </tbody>
    </table>
    <div class="esaf-wizard-save-and-continue">
      <button type="button" id="esaf-wizard-business-save-and-continue" class="button button-primary button-hero"><?php esc_html_e('Save and Continue &rarr;', 'easy-affiliate'); ?></button>
    </div>
  </div>
</div>
