<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
/** @var \EasyAffiliate\Models\Options $options */
?>
<div class="esaf-wizard-white-box">
  <div class="esaf-wizard-box-title">
    <h2><?php esc_html_e('Affiliate Registration Information', 'easy-affiliate'); ?></h2>
    <p><?php esc_html_e('Set up your initial Affiliate Registration settings.', 'easy-affiliate'); ?></p>
  </div>
  <div class="esaf-wizard-box-content esaf-wizard-box-content-affiliate-registration">
    <div class="esaf-wizard-tiles">
      <div>
        <input type="radio" name="<?php echo esc_attr($options->registration_type_str); ?>" value="application" id="<?php echo esc_attr($options->registration_type_str); ?>-application" <?php checked('application', $options->registration_type); ?>>
        <label for="<?php echo esc_attr($options->registration_type_str); ?>-application">
          <?php echo file_get_contents(ESAF_IMAGES_PATH . '/affiliate-registration-application.svg'); ?>
          <span class="esaf-wizard-tile-title"><?php esc_html_e('Application', 'easy-affiliate'); ?></span>
          <span class="esaf-wizard-tile-description"><?php esc_html_e('Affiliates must apply and be approved by you', 'easy-affiliate'); ?></span>
        </label>
      </div>
      <div>
        <input type="radio" name="<?php echo esc_attr($options->registration_type_str); ?>" value="public" id="<?php echo esc_attr($options->registration_type_str); ?>-public" <?php checked('public', $options->registration_type); ?>>
        <label for="<?php echo esc_attr($options->registration_type_str); ?>-public">
          <?php echo file_get_contents(ESAF_IMAGES_PATH . '/affiliate-registration-public.svg'); ?>
          <span class="esaf-wizard-tile-title"><?php esc_html_e('Public', 'easy-affiliate'); ?></span>
          <span class="esaf-wizard-tile-description"><?php esc_html_e('Anyone can instantly become an affiliate without approval', 'easy-affiliate'); ?></span>
        </label>
      </div>
      <div>
        <input type="radio" name="<?php echo esc_attr($options->registration_type_str); ?>" value="private" id="<?php echo esc_attr($options->registration_type_str); ?>-private" <?php checked('private', $options->registration_type); ?>>
        <label for="<?php echo esc_attr($options->registration_type_str); ?>-private">
          <?php echo file_get_contents(ESAF_IMAGES_PATH . '/affiliate-registration-private.svg'); ?>
          <span class="esaf-wizard-tile-title"><?php esc_html_e('Private', 'easy-affiliate'); ?></span>
          <span class="esaf-wizard-tile-description"><?php esc_html_e('Affiliates can only be manually added by you', 'easy-affiliate'); ?></span>
        </label>
      </div>
    </div>
    <table class="form-table">
      <tbody>
        <tr>
          <th scope="row">
            <label for="<?php echo esc_attr($options->show_address_fields_str); ?>"><?php esc_html_e('Collect Affiliate Addresses', 'easy-affiliate'); ?></label>
          </th>
          <td>
            <div class="esaf-flex-columns esaf-align-to-label">
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
            <label for="<?php echo esc_attr($options->show_tax_id_fields_str); ?>"><?php esc_html_e('Collect Tax Info', 'easy-affiliate'); ?></label>
          </th>
          <td>
            <div class="esaf-flex-columns esaf-align-to-label">
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
    <div class="esaf-wizard-save-and-continue">
      <button type="button" id="esaf-wizard-affiliate-registration-save-and-continue" class="button button-primary button-hero"><?php esc_html_e('Save and Continue &rarr;', 'easy-affiliate'); ?></button>
    </div>
  </div>
</div>
