<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
use EasyAffiliate\Helpers\OptionsHelper;
use EasyAffiliate\Lib\Config;
/** @var \EasyAffiliate\Models\Options $options */
$integrations = Config::get('integrations');
?>
<div class="esaf-settings-section">
  <div class="esaf-settings-section-title">
    <?php esc_html_e('eCommerce Payment Integration', 'easy-affiliate'); ?>
  </div>
  <div class="esaf-settings-section-content">
    <?php if(is_wp_error($integrations)) : ?>
      <p><?php esc_html_e('No payment integrations were found.', 'easy-affiliate'); ?></p>
    <?php else : ?>
      <div class="esaf-payment-integrations">
        <?php
          foreach($integrations as $slug => $integration) {
            $integration['checked'] = in_array($slug, $options->integration);

            if($integration['deprecated'] && !$integration['checked']) {
              continue; // Skip this integration if it's deprecated and not being used
            }

            echo OptionsHelper::get_ecommerce_integration_html($slug, $integration);
          }
        ?>
      </div>
    <?php endif; ?>
  </div>
</div>
