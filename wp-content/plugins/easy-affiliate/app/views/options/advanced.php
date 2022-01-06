<?php
/** @var \EasyAffiliate\Models\Options $options */
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
use EasyAffiliate\Helpers\AppHelper;
use EasyAffiliate\Helpers\OptionsHelper;

$emails = (object) [
  'welcome' => (object) [
    'tooltip_title' => __('Send a Welcome Email to Affiliates', 'easy-affiliate'),
    'tooltip_body' => __('When this is checked, Easy Affiliate will send a welcome email to each affiliate when they\'re accepted into your affiliate program.', 'easy-affiliate'),
    'id' => $options->welcome_email_str,
    'send_label' => __('Send Welcome Email', 'easy-affiliate'),
    'send' => $options->welcome_email,
    'subject_id' => $options->welcome_email_subject_str,
    'subject' => $options->welcome_email_subject,
    'body_id' => $options->welcome_email_body_str,
    'body' => $options->welcome_email_body,
    'use_template' => $options->welcome_email_use_template,
    'use_template_id' => $options->welcome_email_use_template_str
  ],
  'affiliate' => (object) [
    'tooltip_title' => __('Send a Sale Notification Email to Affiliates', 'easy-affiliate'),
    'tooltip_body' => __('When this is checked, Easy Affiliate will send a sale notification email to each affiliate when they\'ve referred a sale and are entitled to a commission.', 'easy-affiliate'),
    'id' => $options->affiliate_email_str,
    'send_label' => __('Send Affiliate Sale Email', 'easy-affiliate'),
    'send' => $options->affiliate_email,
    'subject_id' => $options->affiliate_email_subject_str,
    'subject' => $options->affiliate_email_subject,
    'body_id' => $options->affiliate_email_body_str,
    'body' => $options->affiliate_email_body,
    'use_template' => $options->affiliate_email_use_template,
    'use_template_id' => $options->affiliate_email_use_template_str
  ],
  'admin' => (object) [
    'tooltip_title' => __('Send an Affiliate Commission Notification Email to the Admin', 'easy-affiliate'),
    'tooltip_body' => __('When this is checked, Easy Affiliate will send a commission notification email to the Admin when an affiliate has referred a sale.', 'easy-affiliate'),
    'id' => $options->admin_email_str,
    'send_label' => __('Send Admin Commission Email', 'easy-affiliate'),
    'send' => $options->admin_email,
    'subject_id' => $options->admin_email_subject_str,
    'subject' => $options->admin_email_subject,
    'body_id' => $options->admin_email_body_str,
    'body' => $options->admin_email_body,
    'use_template' => $options->admin_email_use_template,
    'use_template_id' => $options->admin_email_use_template_str
  ],
];
?>
<div class="esaf-settings-section">
  <div class="esaf-settings-section-title">
    <?php esc_html_e('Email Notifications', 'easy-affiliate'); ?>
  </div>
  <div class="esaf-settings-section-content">
    <?php
      foreach($emails as $slug => $email) {
        echo OptionsHelper::get_email_editor_html($slug, $email);
      }
    ?>
  </div>
</div>
<div class="esaf-settings-section">
  <div class="esaf-settings-section-title">
    <?php esc_html_e('Email Settings', 'easy-affiliate'); ?>
  </div>
  <div class="esaf-settings-section-content">
    <table class="form-table">
      <tbody>
        <tr>
          <th scope="row">
            <label for="<?php echo esc_attr($options->admin_email_addresses_str); ?>"><?php esc_html_e('Admin Email Addresses', 'easy-affiliate'); ?></label>
            <?php
              AppHelper::info_tooltip(
                'esaf-options-admin-email-addresses',
                esc_html__('A comma separated list of email addresses that will receive admin notifications.', 'easy-affiliate')
              );
            ?>
          </th>
          <td>
            <input type="text" name="<?php echo esc_attr($options->admin_email_addresses_str); ?>" id="<?php echo esc_attr($options->admin_email_addresses_str); ?>" value="<?php echo esc_attr($options->admin_email_addresses); ?>" />
          </td>
        </tr>
        <tr>
          <th scope="row">
            <label for="<?php echo esc_attr($options->email_from_name_str); ?>"><?php esc_html_e('From Name', 'easy-affiliate'); ?></label>
            <?php
              AppHelper::info_tooltip(
                'esaf-options-email-from-name',
                esc_html__('The name of the email sender.', 'easy-affiliate')
              );
            ?>
          </th>
          <td>
            <input type="text" name="<?php echo esc_attr($options->email_from_name_str); ?>" id="<?php echo esc_attr($options->email_from_name_str); ?>" value="<?php echo esc_attr($options->email_from_name); ?>" />
          </td>
        </tr>
        <tr>
          <th scope="row">
            <label for="<?php echo esc_attr($options->email_from_address_str); ?>"><?php esc_html_e('From Email', 'easy-affiliate'); ?></label>
            <?php
              AppHelper::info_tooltip(
                'esaf-options-email-from-email',
                esc_html__('The email address of the email sender.', 'easy-affiliate')
              );
            ?>
          </th>
          <td>
            <input type="text" name="<?php echo esc_attr($options->email_from_address_str); ?>" id="<?php echo esc_attr($options->email_from_address_str); ?>" value="<?php echo esc_attr($options->email_from_address); ?>" />
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
<div class="esaf-settings-section">
  <div class="esaf-settings-section-title">
    <?php esc_html_e('International Settings', 'easy-affiliate'); ?>
  </div>
  <div class="esaf-settings-section-content">
    <table class="form-table">
      <tbody>
        <tr>
          <th scope="row">
            <label for="<?php echo esc_attr($options->currency_code_str); ?>"><?php esc_html_e('Currency Code', 'easy-affiliate'); ?></label>
          </th>
          <td>
            <?php OptionsHelper::payment_currency_code_dropdown($options->currency_code_str, $options->currency_code); ?>
          </td>
        </tr>
        <tr>
          <th scope="row">
            <label for="<?php echo esc_attr($options->currency_symbol_str); ?>"><?php esc_html_e('Currency Symbol', 'easy-affiliate'); ?></label>
          </th>
          <td>
            <?php OptionsHelper::payment_currencies_dropdown($options->currency_symbol_str, $options->currency_symbol); ?>
          </td>
        </tr>
        <tr>
          <th scope="row" colspan="2">
            <input type="checkbox" class="esaf-toggle-switch esaf-toggle-switch-before-label" name="<?php echo esc_attr($options->currency_symbol_after_amount_str); ?>" id="<?php echo esc_attr($options->currency_symbol_after_amount_str); ?>"<?php checked($options->currency_symbol_after_amount); ?> />
            <label for="<?php echo esc_attr($options->currency_symbol_after_amount_str); ?>"></label>
            <label for="<?php echo esc_attr($options->currency_symbol_after_amount_str); ?>"><?php esc_html_e('Currency Symbol After Amount', 'easy-affiliate'); ?></label>
            <?php
              AppHelper::info_tooltip(
                'esaf-options-currency-symbol-after-amount',
                esc_html__('Display the currency symbol after the amount, for example 5.00$ instead of $5.00.', 'easy-affiliate')
              );
            ?>
          </th>
        </tr>
        <tr>
          <th scope="row">
            <label for="<?php echo esc_attr($options->number_format_str); ?>"><?php esc_html_e('Currency Format', 'easy-affiliate'); ?></label>
          </th>
          <td>
            <?php OptionsHelper::payment_format_dropdown($options->number_format_str, $options->number_format); ?>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
<div class="esaf-settings-section">
  <div class="esaf-settings-section-title">
    <?php esc_html_e('Dashboard Navigation', 'easy-affiliate'); ?>
  </div>
  <div class="esaf-settings-section-content">
    <table class="form-table">
      <tbody>
        <tr>
          <th scope="row">
            <label>
              <?php
                esc_html_e('Custom Nav Pages', 'easy-affiliate');
                AppHelper::info_tooltip(
                  'esaf-options-custom-nav-pages',
                  esc_html__('Customize Nav page links that will appear on the Affiliate Dashboard.', 'easy-affiliate')
                );
              ?>
            </label>
          </th>
          <td>
            <ol id="wafp-dash-pages" data-index="0"></ol>
            <a href="javascript:" id="wafp_add_nav_pages" class="button"><?php esc_html_e('Add Page', 'easy-affiliate'); ?></a>
            <a href="javascript:" id="wafp_remove_nav_pages" class="wafp-hidden button"><?php esc_html_e('Remove Page', 'easy-affiliate'); ?></a>
            <div id="wafp-data-selected" class="wafp-hidden"><?php echo json_encode(array_values($options->dash_nav)); ?></div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
<div class="esaf-settings-section">
  <div class="esaf-settings-section-title">
    <?php esc_html_e('Updates', 'easy-affiliate'); ?>
  </div>
  <div class="esaf-settings-section-content">
    <table class="form-table">
      <tbody>
        <tr>
          <th scope="row">
            <input type="checkbox" class="esaf-toggle-switch esaf-toggle-switch-before-label" name="<?php echo esc_attr($options->edge_updates_str); ?>" id="<?php echo esc_attr($options->edge_updates_str); ?>"<?php checked($options->edge_updates); ?> />
            <label for="<?php echo esc_attr($options->edge_updates_str); ?>"></label>
            <label for="<?php echo esc_attr($options->edge_updates_str); ?>"><?php esc_html_e('Include Easy Affiliate edge (development) releases in automatic updates', 'easy-affiliate'); ?></label>
            <?php
              AppHelper::info_tooltip(
                'esaf-options-edge-releases',
                esc_html__('When checked, automatic updates will include edge (development) releases (not recommended for production websites).', 'easy-affiliate')
              );
            ?>
          </th>
        </tr>
      </tbody>
    </table>
  </div>
</div>
