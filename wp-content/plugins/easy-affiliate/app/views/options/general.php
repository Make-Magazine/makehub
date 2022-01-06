<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
use EasyAffiliate\Helpers\AppHelper;
use EasyAffiliate\Helpers\OptionsHelper;
/** @var \EasyAffiliate\Models\Options $options */
?>
<div class="esaf-settings-section">
  <div class="esaf-settings-section-title">
    <?php esc_html_e('License', 'easy-affiliate'); ?>
  </div>
  <div id="esaf-license" class="esaf-settings-section-content">
    <?php
      if(!empty($license) && is_array($license)) {
        echo OptionsHelper::get_active_license_html($license);
      }
      else {
        echo OptionsHelper::get_license_key_field_html();
      }
    ?>
  </div>
</div>
<div class="esaf-settings-section">
  <div class="esaf-settings-section-title">
    <?php esc_html_e('Business Info', 'easy-affiliate'); ?>
  </div>
  <div class="esaf-settings-section-content">
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
            <label<?php echo empty($options->business_tax_id) ? ' for="' . esc_attr($options->business_tax_id_str) . '"' : ''; ?>><?php esc_html_e('SSN/EIN', 'easy-affiliate') ?></label>
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
  </div>
</div>
<div class="esaf-settings-section">
  <div class="esaf-settings-section-title">
    <?php esc_html_e('Affiliate Pages', 'easy-affiliate'); ?>
  </div>
  <div class="esaf-settings-section-content">
    <table class="form-table">
      <tbody>
        <tr>
          <th scope="row">
            <label for="<?php echo esc_attr($options->dashboard_page_id_str); ?>"><?php esc_html_e('Dashboard Page', 'easy-affiliate') ?>*</label>
            <?php
              AppHelper::info_tooltip(
                'esaf-options-pages-dashboard',
                esc_html__('This is the WordPress page that Easy Affiliate will use as the Affiliate\'s Dashboard.', 'easy-affiliate')
              );
            ?>
          </th>
          <td>
            <?php echo OptionsHelper::wp_pages_dropdown($options->dashboard_page_id_str, $options->dashboard_page_id); ?>
          </td>
        </tr>
        <tr>
          <th scope="row">
            <label for="<?php echo esc_attr($options->signup_page_id_str); ?>"><?php esc_html_e('Signup Page', 'easy-affiliate') ?>*</label>
            <?php
              AppHelper::info_tooltip(
                'esaf-options-pages-signup',
                esc_html__('This is the WordPress page that Easy Affiliate will use as the affiliate signup page.', 'easy-affiliate')
              );
            ?>
          </th>
          <td>
            <?php echo OptionsHelper::wp_pages_dropdown($options->signup_page_id_str, $options->signup_page_id); ?>
          </td>
        </tr>
        <tr>
          <th scope="row">
            <label for="<?php echo esc_attr($options->login_page_id_str); ?>"><?php esc_html_e('Login Page', 'easy-affiliate') ?>*</label>
            <?php
              AppHelper::info_tooltip(
                'esaf-options-pages-login',
                esc_html__('This is the WordPress page that Easy Affiliate will use as the affiliate login page.', 'easy-affiliate')
              );
            ?>
          </th>
          <td>
            <?php echo OptionsHelper::wp_pages_dropdown($options->login_page_id_str, $options->login_page_id); ?>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
<div class="esaf-settings-section">
  <div class="esaf-settings-section-title">
    <?php esc_html_e('Links', 'easy-affiliate'); ?>
  </div>
  <div class="esaf-settings-section-content">
    <table class="form-table">
      <tbody>
        <tr>
          <th scope="row">
            <input type="checkbox" class="esaf-toggle-switch esaf-toggle-switch-before-label" name="<?php echo esc_attr($options->utm_affiliate_links_str); ?>" id="<?php echo esc_attr($options->utm_affiliate_links_str); ?>"<?php checked($options->utm_affiliate_links); ?> />
            <label for="<?php echo esc_attr($options->utm_affiliate_links_str); ?>"></label>
            <label for="<?php echo esc_attr($options->utm_affiliate_links_str); ?>"><?php esc_html_e('UTM Affiliate Link Tracking', 'easy-affiliate'); ?></label>
            <?php
              AppHelper::info_tooltip(
                'esaf-options-utm-affiliate-links',
                esc_html__('This will enable your affiliate links to be tracked with UTM parameters in Google Analytics. No need to set UTM parameters yourself, Easy Affiliate has it taken care of.', 'easy-affiliate')
              );
            ?>
          </th>
        </tr>
        <?php if($options->pretty_affiliate_links) : ?>
          <tr>
            <th scope="row">
              <input type="checkbox" class="esaf-toggle-checkbox esaf-toggle-switch esaf-toggle-switch-before-label" name="<?php echo esc_attr($options->pretty_affiliate_links_str); ?>" id="<?php echo esc_attr($options->pretty_affiliate_links_str); ?>"<?php checked($options->pretty_affiliate_links); ?> />
              <label for="<?php echo esc_attr($options->pretty_affiliate_links_str); ?>"></label>
              <label for="<?php echo esc_attr($options->pretty_affiliate_links_str); ?>"><?php esc_html_e('Pretty Affiliate Links', 'easy-affiliate'); ?></label>
              <?php
              AppHelper::info_tooltip(
                'esaf-options-pretty-affiliate-links',
                esc_html__('Enable Pretty Affiliate Links', 'easy-affiliate')
              );
              ?>
            </th>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<div class="esaf-settings-section">
  <div class="esaf-settings-section-title">
    <?php esc_html_e('Setup Wizard', 'easy-affiliate'); ?>
  </div>
  <div class="esaf-settings-section-content">
    <p><?php esc_html_e('Use our configuration wizard to properly setup Easy Affiliate (with just a few clicks).', 'easy-affiliate'); ?></p>
    <p>
      <a href="<?php echo esc_url(admin_url('admin.php?page=easy-affiliate-wizard')); ?>" class="button button-primary button-hero"><?php esc_html_e('Launch Setup Wizard', 'easy-affiliate'); ?></a>
    </p>
  </div>
</div>
