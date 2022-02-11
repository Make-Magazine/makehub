<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
/** @var array $integrations */
use EasyAffiliate\Helpers\OptionsHelper;
use EasyAffiliate\Helpers\WizardHelper;
?>
<div class="esaf-wizard-white-box">
  <div class="esaf-wizard-box-title">
    <h2><?php esc_html_e('eCommerce Setup', 'easy-affiliate'); ?></h2>
    <p><?php esc_html_e('What eCommerce Plugins do you want Easy Affiliate to track Affiliate Sales with?', 'easy-affiliate'); ?></p>
    <?php echo WizardHelper::get_no_ecommerce_plugins_message_html(); ?>
  </div>
  <div class="esaf-wizard-box-content esaf-wizard-box-content-ecommerce">
    <div class="esaf-wizard-ecommerce-integrations">
      <?php
        foreach($integrations as $slug => $integration) {
          if(!in_array($slug, ['memberpress', 'woocommerce', 'easy_digital_downloads', 'wpforms', 'formidable'])) {
            continue;
          }

          echo OptionsHelper::get_ecommerce_integration_html($slug, $integration);
        }
      ?>
    </div>
    <div class="esaf-wizard-save-and-continue">
      <button type="button" id="esaf-wizard-ecommerce-save-and-continue" class="button button-primary button-hero"><?php esc_html_e('Save and Continue &rarr;', 'easy-affiliate'); ?></button>
    </div>
  </div>
</div>
