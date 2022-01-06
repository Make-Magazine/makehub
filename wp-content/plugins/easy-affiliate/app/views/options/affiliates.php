<?php
/** @var \EasyAffiliate\Models\Options $options */
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
use EasyAffiliate\Helpers\AppHelper;
use EasyAffiliate\Helpers\OptionsHelper;

$affiliate_emails = (object) [
  'admin_aff_applied' => (object) [
    'tooltip_title' => __('Affiliate Applied Admin Notice', 'easy-affiliate'),
    'tooltip_body' => __('When this is checked, an email will be sent to the Admin when an affiliate applies to your Affiliate Program.', 'easy-affiliate'),
    'id' => $options->admin_aff_applied_email_enabled_str,
    'send_label' => __('Send Affiliate Applied Email', 'easy-affiliate'),
    'send' => $options->admin_aff_applied_email_enabled,
    'subject_id' => $options->admin_aff_applied_email_subject_str,
    'subject' => $options->admin_aff_applied_email_subject,
    'body_id' => $options->admin_aff_applied_email_body_str,
    'body' => $options->admin_aff_applied_email_body,
    'use_template' => $options->admin_aff_applied_email_use_template,
    'use_template_id' => $options->admin_aff_applied_email_use_template_str
  ],
  'aff_approved' => (object) [
    'tooltip_title' => __('Affiliate Approved Notice', 'easy-affiliate'),
    'tooltip_body' => __('When this is checked, an email will be sent to the Applicant when an Admin approves an affiliate application', 'easy-affiliate'),
    'id' => $options->aff_approved_email_enabled_str,
    'send_label' => __('Send Affiliate Approved Email', 'easy-affiliate'),
    'send' => $options->aff_approved_email_enabled,
    'subject_id' => $options->aff_approved_email_subject_str,
    'subject' => $options->aff_approved_email_subject,
    'body_id' => $options->aff_approved_email_body_str,
    'body' => $options->aff_approved_email_body,
    'use_template' => $options->aff_approved_email_use_template,
    'use_template_id' => $options->aff_approved_email_use_template_str
  ]
];
?>
<div class="esaf-settings-section">
  <div class="esaf-settings-section-title">
    <?php esc_html_e('Registration Type', 'easy-affiliate'); ?>
    <?php
      AppHelper::info_tooltip(
        'esaf-options-register-type',
        sprintf(
          // translators: %1$s: br tag
          esc_html__('When this option is set to \'Public\' anyone will be able to signup and automatically become an affiliate.%1$s%1$sWhen this option is set to \'Application\', affiliates will have to fill out an affiliate application before they can become an affiliate. You as an administrator will have to approve each application before the person can become an affiliate.%1$s%1$sWhen this option is set to \'Private\' the only way for an affiliate to register will be to have an administrator add them manually.', 'easy-affiliate'),
          '<br>'
        )
      );
    ?>
  </div>
  <div class="esaf-settings-section-content">
    <div role="radiogroup" class="esaf-form-field-tiles esaf-registration-type">
      <div class="esaf-form-field-tile">
        <input type="radio" id="<?php echo esc_attr($options->registration_type_str); ?>-application" name="<?php echo esc_attr($options->registration_type_str); ?>" value="application" <?php checked($options->registration_type, 'application'); ?> class="esaf-toggle-radio" data-box="esaf-affiliate-application-box">
        <label for="<?php echo esc_attr($options->registration_type_str); ?>-application" class="button"><?php echo file_get_contents(ESAF_IMAGES_PATH . '/affiliate-registration-application.svg'); ?><?php esc_html_e('Application', 'easy-affiliate'); ?></label>
      </div>
      <div class="esaf-form-field-tile">
        <input type="radio" id="<?php echo esc_attr($options->registration_type_str); ?>-private" name="<?php echo esc_attr($options->registration_type_str); ?>" value="private" <?php checked($options->registration_type, 'private'); ?> class="esaf-toggle-radio">
        <label for="<?php echo esc_attr($options->registration_type_str); ?>-private" class="button"><?php echo file_get_contents(ESAF_IMAGES_PATH . '/affiliate-registration-private.svg'); ?><?php esc_html_e('Private', 'easy-affiliate'); ?></label>
      </div>
      <div class="esaf-form-field-tile">
        <input type="radio" id="<?php echo esc_attr($options->registration_type_str); ?>-public" name="<?php echo esc_attr($options->registration_type_str); ?>" value="public" <?php checked($options->registration_type, 'public'); ?> class="esaf-toggle-radio">
        <label for="<?php echo esc_attr($options->registration_type_str); ?>-public" class="button"><?php echo file_get_contents(ESAF_IMAGES_PATH . '/affiliate-registration-public.svg'); ?><?php esc_html_e('Public', 'easy-affiliate'); ?></label>
      </div>
    </div>
    <div class="esaf-sub-box-white esaf-affiliate-application-box">
      <?php
        foreach($affiliate_emails as $affiliate_email_slug => $affiliate_email) {
          echo OptionsHelper::get_email_editor_html($affiliate_email_slug, $affiliate_email);
        }
      ?>
      <table class="form-table">
        <tbody>
          <tr>
            <th scope="row">
              <label for="<?php echo esc_attr($options->application_thank_you_str); ?>"><?php esc_html_e('Application Thank You', 'easy-affiliate'); ?></label>
              <?php
                AppHelper::info_tooltip(
                  'esaf-options-application-thank-you',
                  esc_html__('This will be the HTML that will be shown to the Affiliate applicant after they submit an application.', 'easy-affiliate')
                );
              ?>
            </th>
            <td>
              <?php wp_editor($options->application_thank_you, $options->application_thank_you_str, ['editor_height' => 300]); ?>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>
<div class="esaf-settings-section">
  <div class="esaf-settings-section-title">
    <?php esc_html_e('Form Fields', 'easy-affiliate'); ?>
  </div>
  <div class="esaf-settings-section-content">
    <table class="form-table">
      <tbody>
        <tr>
          <th scope="row">
            <label for="<?php echo esc_attr($options->show_address_fields_str); ?>"><?php esc_html_e('Show Address Fields', 'easy-affiliate'); ?></label>
            <?php
              AppHelper::info_tooltip(
                'esaf-options-address-info',
                esc_html__('Collect address information from your affiliates?', 'easy-affiliate')
              );
            ?>
          </th>
          <td>
            <div class="esaf-columns esaf-3-columns esaf-align-to-label esaf-clearfix">
              <div>
                <input type="checkbox" class="esaf-toggle-switch esaf-toggle-switch-before-label" name="<?php echo esc_attr($options->show_address_fields_str); ?>" id="<?php echo esc_attr($options->show_address_fields_str); ?>"<?php checked($options->show_address_fields); ?> />
                <label for="<?php echo esc_attr($options->show_address_fields_str); ?>"></label>
                <label for="<?php echo esc_attr($options->show_address_fields_str); ?>"><?php esc_html_e('On Registration', 'easy-affiliate'); ?></label>
              </div>
              <div>
                <input type="checkbox" class="esaf-toggle-switch esaf-toggle-switch-before-label" name="<?php echo esc_attr($options->show_address_fields_account_str); ?>" id="<?php echo esc_attr($options->show_address_fields_account_str); ?>"<?php checked($options->show_address_fields_account); ?> />
                <label for="<?php echo esc_attr($options->show_address_fields_account_str); ?>"></label>
                <label for="<?php echo esc_attr($options->show_address_fields_account_str); ?>"><?php esc_html_e('On Account', 'easy-affiliate'); ?></label>
              </div>
              <div>
                <input type="checkbox" class="esaf-toggle-switch esaf-toggle-switch-before-label" name="<?php echo esc_attr($options->require_address_fields_str); ?>" id="<?php echo esc_attr($options->require_address_fields_str); ?>"<?php checked($options->require_address_fields); ?> />
                <label for="<?php echo esc_attr($options->require_address_fields_str); ?>"></label>
                <label for="<?php echo esc_attr($options->require_address_fields_str); ?>"><?php esc_html_e('Require', 'easy-affiliate'); ?></label>
              </div>
            </div>
          </td>
        </tr>
        <tr>
          <th scope="row">
            <label for="<?php echo esc_attr($options->show_tax_id_fields_str); ?>"><?php esc_html_e('Show Tax ID Fields', 'easy-affiliate'); ?></label>
            <?php
              AppHelper::info_tooltip(
                'esaf-options-tax-id',
                esc_html__('Collect Tax ID #\'s from your affiliates?', 'easy-affiliate')
              );
            ?>
          </th>
          <td>
            <div class="esaf-columns esaf-3-columns esaf-align-to-label esaf-clearfix">
              <div>
                <input type="checkbox" class="esaf-toggle-switch esaf-toggle-switch-before-label" name="<?php echo esc_attr($options->show_tax_id_fields_str); ?>" id="<?php echo esc_attr($options->show_tax_id_fields_str); ?>"<?php checked($options->show_tax_id_fields); ?> />
                <label for="<?php echo esc_attr($options->show_tax_id_fields_str); ?>"></label>
                <label for="<?php echo esc_attr($options->show_tax_id_fields_str); ?>"><?php esc_html_e('On Registration', 'easy-affiliate'); ?></label>
              </div>
              <div>
                <input type="checkbox" class="esaf-toggle-switch esaf-toggle-switch-before-label" name="<?php echo esc_attr($options->show_tax_id_fields_account_str); ?>" id="<?php echo esc_attr($options->show_tax_id_fields_account_str); ?>"<?php checked($options->show_tax_id_fields_account); ?> />
                <label for="<?php echo esc_attr($options->show_tax_id_fields_account_str); ?>"></label>
                <label for="<?php echo esc_attr($options->show_tax_id_fields_account_str); ?>"><?php esc_html_e('On Account', 'easy-affiliate'); ?></label>
              </div>
              <div>
                <input type="checkbox" class="esaf-toggle-switch esaf-toggle-switch-before-label" name="<?php echo esc_attr($options->require_tax_id_fields_str); ?>" id="<?php echo esc_attr($options->require_tax_id_fields_str); ?>"<?php checked($options->require_tax_id_fields); ?> />
                <label for="<?php echo esc_attr($options->require_tax_id_fields_str); ?>"></label>
                <label for="<?php echo esc_attr($options->require_tax_id_fields_str); ?>"><?php esc_html_e('Require', 'easy-affiliate'); ?></label>
              </div>
            </div>
          </td>
        </tr>
        <tr>
          <th scope="row" colspan="2">
            <input type="checkbox" class="esaf-toggle-checkbox esaf-toggle-switch esaf-toggle-switch-before-label" name="<?php echo esc_attr($options->affiliate_agreement_enabled_str); ?>" id="<?php echo esc_attr($options->affiliate_agreement_enabled_str); ?>" data-box="esaf-options-affiliate-agreement-box"<?php checked($options->affiliate_agreement_enabled); ?> />
            <label for="<?php echo esc_attr($options->affiliate_agreement_enabled_str); ?>"></label>
            <label for="<?php echo esc_attr($options->affiliate_agreement_enabled_str); ?>"><?php esc_html_e('Show Affiliate Agreement on Registration Page', 'easy-affiliate'); ?></label>
            <?php
              AppHelper::info_tooltip(
                'esaf-options-affiliate-agreement',
                esc_html__('Display an Affiliate Signup Agreement on the affiliate registration page.', 'easy-affiliate')
              );
            ?>
          </th>
        </tr>
      </tbody>
    </table>
    <div class="esaf-sub-box-white esaf-options-affiliate-agreement-box">
      <table class="form-table">
        <tbody>
          <tr>
            <th scope="row">
              <label for="<?php echo esc_attr($options->affiliate_agreement_text_str); ?>"><?php esc_html_e('Affiliate Agreement', 'easy-affiliate'); ?></label>
            </th>
            <td>
              <div class="esaf-affiliate-agreement-field">
                <textarea name="<?php echo esc_attr($options->affiliate_agreement_text_str); ?>" id="<?php echo esc_attr($options->affiliate_agreement_text_str); ?>"><?php echo esc_textarea($options->affiliate_agreement_text); ?></textarea>
                <span class="esaf-html-allowed"><?php esc_html_e('HTML is allowed', 'easy-affiliate'); ?></span>
              </div>
              <?php include ESAF_VIEWS_PATH . '/options/affiliates/auto-generate-agreement.php'; ?>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <table class="form-table">
      <tbody>
        <tr>
          <th scope="row">
            <input type="checkbox" class="esaf-toggle-switch esaf-toggle-switch-before-label" name="<?php echo esc_attr($options->make_new_users_affiliates_str); ?>" id="<?php echo esc_attr($options->make_new_users_affiliates_str); ?>"<?php checked($options->make_new_users_affiliates); ?> />
            <label for="<?php echo esc_attr($options->make_new_users_affiliates_str); ?>"></label>
            <label for="<?php echo esc_attr($options->make_new_users_affiliates_str); ?>"><?php esc_html_e('Auto-Add Affiliates', 'easy-affiliate'); ?></label>
            <?php
              AppHelper::info_tooltip(
                'esaf-options-auto-add-affiliates',
                esc_html__('Automatically make each new user an Affiliate?', 'easy-affiliate')
              );
            ?>
          </th>
        </tr>
      </tbody>
    </table>
  </div>
</div>
<div class="esaf-settings-section">
  <div class="esaf-settings-section-title">
    <?php esc_html_e('Dashboard', 'easy-affiliate'); ?>
  </div>
  <div class="esaf-settings-section-content">
    <table class="form-table">
      <tbody>
        <tr>
          <th scope="row">
            <input type="checkbox" class="esaf-toggle-checkbox esaf-toggle-switch esaf-toggle-switch-before-label" name="<?php echo esc_attr($options->pro_dashboard_enabled_str); ?>" id="<?php echo esc_attr($options->pro_dashboard_enabled_str); ?>" data-box="esaf-options-pro-dashboard-box"<?php checked($options->pro_dashboard_enabled); ?> />
            <label for="<?php echo esc_attr($options->pro_dashboard_enabled_str); ?>"></label>
            <label for="<?php echo esc_attr($options->pro_dashboard_enabled_str); ?>"><?php esc_html_e('Enable Pro Dashboard', 'easy-affiliate'); ?></label>
            <?php
              AppHelper::info_tooltip(
                'esaf-options-pro-dashboard',
                esc_html__('Enables the Pro Affiliate Dashboard, which converts the affiliate dashboard to a full page layout.', 'easy-affiliate')
              );
            ?>
          </th>
        </tr>
      </tbody>
    </table>
    <div class="esaf-sub-box-white esaf-options-pro-dashboard-box">
      <table class="form-table">
        <tbody>
          <tr>
            <th scope="row">
              <label for="<?php echo esc_attr($options->pro_dashboard_brand_color_str); ?>"><?php esc_html_e('Brand Color', 'easy-affiliate'); ?></label>
              <?php
                AppHelper::info_tooltip(
                  'esaf-options-pro-dashboard-brand-color',
                  esc_html__('This controls the background color of the header and sidebar menu.', 'easy-affiliate')
                );
              ?>
            </th>
            <td>
              <div class="esaf-align-to-label">
                <input type="text" name="<?php echo esc_attr($options->pro_dashboard_brand_color_str); ?>" id="<?php echo esc_attr($options->pro_dashboard_brand_color_str); ?>" value="<?php echo esc_attr($options->pro_dashboard_brand_color); ?>" data-default-color="#32373b" class="esaf-color-picker">
              </div>
            </td>
          </tr>
          <tr>
            <th scope="row">
              <label for="<?php echo esc_attr($options->pro_dashboard_accent_color_str); ?>"><?php esc_html_e('Accent Color', 'easy-affiliate'); ?></label>
              <?php
                AppHelper::info_tooltip(
                  'esaf-options-pro-dashboard-accent-color',
                  esc_html__('This controls the background color of the highlighted menu item in the sidebar.', 'easy-affiliate')
                );
              ?>
            </th>
            <td>
              <div class="esaf-align-to-label">
                <input type="text" name="<?php echo esc_attr($options->pro_dashboard_accent_color_str); ?>" id="<?php echo esc_attr($options->pro_dashboard_accent_color_str); ?>" value="<?php echo esc_attr($options->pro_dashboard_accent_color); ?>" data-default-color="#222629" class="esaf-color-picker">
              </div>
            </td>
          </tr>
          <tr>
            <th scope="row">
              <label for="<?php echo esc_attr($options->pro_dashboard_menu_text_color_str); ?>"><?php esc_html_e('Menu Text Color', 'easy-affiliate'); ?></label>
              <?php
                AppHelper::info_tooltip(
                  'esaf-options-pro-dashboard-menu-text-color',
                  esc_html__('This controls the text color in the header and sidebar menu.', 'easy-affiliate')
                );
              ?>
            </th>
            <td>
              <div class="esaf-align-to-label">
                <input type="text" name="<?php echo esc_attr($options->pro_dashboard_menu_text_color_str); ?>" id="<?php echo esc_attr($options->pro_dashboard_menu_text_color_str); ?>" value="<?php echo esc_attr($options->pro_dashboard_menu_text_color); ?>" data-default-color="#b7bcc0" class="esaf-color-picker">
              </div>
            </td>
          </tr>
          <tr>
            <th scope="row">
              <label for="<?php echo esc_attr($options->pro_dashboard_menu_text_highlight_color_str); ?>"><?php esc_html_e('Menu Text Highlight Color', 'easy-affiliate'); ?></label>
              <?php
                AppHelper::info_tooltip(
                  'esaf-options-pro-dashboard-menu-text-highlight-color',
                  esc_html__('This controls the text color of the highlighted menu item in the sidebar.', 'easy-affiliate')
                );
              ?>
            </th>
            <td>
              <div class="esaf-align-to-label">
                <input type="text" name="<?php echo esc_attr($options->pro_dashboard_menu_text_highlight_color_str); ?>" id="<?php echo esc_attr($options->pro_dashboard_menu_text_highlight_color_str); ?>" value="<?php echo esc_attr($options->pro_dashboard_menu_text_highlight_color); ?>" data-default-color="#ffffff" class="esaf-color-picker">
              </div>
            </td>
          </tr>
          <tr>
            <th scope="row">
              <label><?php esc_html_e('Logo', 'easy-affiliate'); ?></label>
              <?php
                AppHelper::info_tooltip(
                  'esaf-options-pro-dashboard-logo-url',
                  esc_html__('Set a logo for the header, which will replace the site title displayed there.', 'easy-affiliate')
                );
              ?>
            </th>
            <td>
              <div class="esaf-align-to-label">
                <input type="hidden" id="<?php echo esc_attr($options->pro_dashboard_logo_url_str); ?>" name="<?php echo esc_attr($options->pro_dashboard_logo_url_str); ?>" value="<?php echo esc_attr($options->pro_dashboard_logo_url); ?>">
                <div id="esaf-pro-dashboard-logo-preview"<?php echo empty($options->pro_dashboard_logo_url) ? ' class="esaf-hidden"' : ''; ?>>
                  <?php if(!empty($options->pro_dashboard_logo_url)) : ?>
                    <img src="<?php echo esc_url($options->pro_dashboard_logo_url); ?>" alt="">
                  <?php endif; ?>
                </div>
                <div id="esaf-pro-dashboard-logo-remove"<?php echo empty($options->pro_dashboard_logo_url) ? ' class="esaf-hidden"' : ''; ?>>
                  <button type="button" class="button button-secondary" id="esaf-pro-dashboard-logo-remove-button"><?php esc_html_e('Remove', 'easy-affiliate'); ?></button>
                </div>
                <div id="esaf-pro-dashboard-logo-choose"<?php echo empty($options->pro_dashboard_logo_url) ? '' : ' class="esaf-hidden"'; ?>>
                  <button type="button" class="button button-secondary" id="esaf-pro-dashboard-logo-choose-button"><?php esc_html_e('Choose Image', 'easy-affiliate'); ?></button>
                </div>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <table class="form-table">
      <tbody>
        <tr>
          <th scope="row">
            <input type="checkbox" class="esaf-toggle-checkbox esaf-toggle-switch esaf-toggle-switch-before-label" name="<?php echo esc_attr($options->showcase_url_enabled_str); ?>" id="<?php echo esc_attr($options->showcase_url_enabled_str); ?>" data-box="esaf-options-showcase-url-box"<?php checked($options->showcase_url_enabled); ?> />
            <label for="<?php echo esc_attr($options->showcase_url_enabled_str); ?>"></label>
            <label for="<?php echo esc_attr($options->showcase_url_enabled_str); ?>"><?php esc_html_e('Enable Showcase URL', 'easy-affiliate'); ?></label>
            <?php
              AppHelper::info_tooltip(
                'esaf-options-showcase-url',
                esc_html__('Enables the Showcase URL, which provides easy copying/pasting of a special link.', 'easy-affiliate')
              );
            ?>
          </th>
        </tr>
      </tbody>
    </table>
    <div class="esaf-sub-box-white esaf-options-showcase-url-box">
      <table class="form-table">
        <tbody>
          <tr>
            <th scope="row">
              <label for="<?php echo esc_attr($options->showcase_url_href_str); ?>"><?php esc_html_e('Showcase URL', 'easy-affiliate'); ?></label>
              <?php
                AppHelper::info_tooltip(
                  'esaf-options-showcase-url-href',
                  esc_html__('Full URL of Showcase link', 'easy-affiliate')
                );
              ?>
            </th>
            <td>
              <div class="esaf-align-to-label">
                <input type="text" name="<?php echo esc_attr($options->showcase_url_href_str); ?>" id="<?php echo esc_attr($options->showcase_url_href_str); ?>" value="<?php echo esc_attr($options->showcase_url_href); ?>">
              </div>
            </td>
          </tr>
          <tr>
            <th scope="row">
              <label for="<?php echo esc_attr($options->showcase_url_title_str); ?>"><?php esc_html_e('Showcase URL Title', 'easy-affiliate'); ?></label>
              <?php
                AppHelper::info_tooltip(
                  'esaf-options-showcase-url-title',
                  esc_html__('Title for Showcase URL box.', 'easy-affiliate')
                );
              ?>
            </th>
            <td>
              <div class="esaf-align-to-label">
                <input type="text" name="<?php echo esc_attr($options->showcase_url_title_str); ?>" id="<?php echo esc_attr($options->showcase_url_title_str); ?>" value="<?php echo esc_attr($options->showcase_url_title); ?>">
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <table class="form-table">
      <tbody>
        <tr>
          <th scope="row">
            <label for="<?php echo esc_attr($options->custom_message_str); ?>"><?php esc_html_e('Welcome Message', 'easy-affiliate'); ?></label>
            <?php
              AppHelper::info_tooltip(
                'esaf-options-custom-message',
                esc_html__('This is the customized message your affiliates will see on their Affiliate Dashboard welcome page.', 'easy-affiliate')
              );
            ?>
          </th>
          <td>
            <?php wp_editor($options->custom_message, $options->custom_message_str, ['media_buttons' => false, 'teeny' => true, 'editor_height' => 250]); ?>
          </td>
        </tr>
      </tbody>
    </table>
    <?php do_action('esaf_affiliate_dashboard_settings_bottom'); ?>
  </div>
</div>
<?php do_action('esaf_affiliate_settings_bottom'); ?>
