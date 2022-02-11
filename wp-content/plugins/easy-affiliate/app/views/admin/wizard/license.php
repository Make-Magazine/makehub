<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<div class="esaf-wizard-white-box">
  <div class="esaf-wizard-box-title">
    <h2><?php esc_html_e('Connect Easy Affiliate to Your Website', 'easy-affiliate'); ?></h2>
    <p><?php esc_html_e('Easy Affiliate connects to WordPress and helps you create an organic sales force.', 'easy-affiliate'); ?></p>
  </div>
  <div class="esaf-wizard-box-content esaf-wizard-box-content-license">
    <label for="esaf-wizard-license-key" class="esaf-wizard-license-key-label"><?php esc_html_e('License Key', 'easy-affiliate'); ?></label>
    <p>
      <?php
        printf(
          /* translators: %1$s: open link tag, %2$s: close link tag */
          esc_html__('Add your Easy Affiliate license key from the email receipt or account area. %1$sRetrieve your license key.%2$s', 'easy-affiliate'),
          '<a href="https://easyaffiliate.com/account/" target="_blank">',
          '</a>'
        );
      ?>
    </p>
    <div class="esaf-wizard-license-key-field">
      <input type="text" id="esaf-wizard-license-key" data-lpignore="true">
      <i id="esaf-wizard-license-key-loading" class="ea-icon ea-icon-spinner animate-spin" aria-hidden="true"></i>
      <i id="esaf-wizard-license-key-success" class="ea-icon ea-icon-ok" aria-hidden="true"></i>
    </div>
    <div id="esaf-wizard-license-key-verified" class="esaf-wizard-save-and-continue">
      <button type="button" class="button button-primary button-hero esaf-wizard-next-step"><?php esc_html_e('Save and Continue &rarr;', 'easy-affiliate'); ?></button>
    </div>
  </div>
</div>
