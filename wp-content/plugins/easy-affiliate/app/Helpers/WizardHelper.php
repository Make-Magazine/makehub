<?php

namespace EasyAffiliate\Helpers;

use EasyAffiliate\Controllers\EasyDigitalDownloadsCtrl;
use EasyAffiliate\Controllers\MemberPressCtrl;
use EasyAffiliate\Controllers\WooCommerceCtrl;

class WizardHelper {
  /**
   * Get the HTML for the Wizard progress steps
   *
   * @param int $current_step
   * @param int $number_of_steps
   * @return string
   */
  public static function get_progress($current_step, $number_of_steps) {
    $output = '<div class="esaf-wizard-progress-steps esaf-clearfix">';

    foreach(range(1, $number_of_steps) as $step) {
      if($step == $current_step) {
        $output .= '<div class="esaf-wizard-progress-step esaf-current"></div>';
      }
      else {
        $output .= '<div class="esaf-wizard-progress-step"></div>';
      }
    }

    $output .= '</div>';

    return $output;
  }

  /**
   * Returns true if AffiliateWP is detected
   *
   * @return bool
   */
  public static function is_affiliatewp_detected() {
    return get_option('affwp_is_installed') === '1';
  }

  /**
   * Returns true if Affiliate Royale is detected
   *
   * @return bool
   */
  public static function is_affiliate_royale_detected() {
    return file_exists(WP_PLUGIN_DIR . '/affiliate-royale/affiliate-royale.php');
  }

  /**
   * Returns true if any plugin with a migration is detected
   *
   * @return bool
   */
  public static function is_migration_available() {
    return self:: is_affiliatewp_detected() || self::is_affiliate_royale_detected();
  }

  /**
   * Get the HTML for the message to display if there are no eCommerce plugins installed
   *
   * @return string
   */
  public static function get_no_ecommerce_plugins_message_html() {
    if(
      MemberPressCtrl::is_plugin_active() ||
      WooCommerceCtrl::is_plugin_active() ||
      EasyDigitalDownloadsCtrl::is_plugin_active()
    ) {
      return '';
    }

    return sprintf(
      '<p class="esaf-wizard-ecommerce-no-plugins"><i class="ea-icon ea-icon-attention"></i>%s</p>',
      esc_html__('No eCommerce Plugins Were Detected. We recommend using Easy Affiliate with one of our supported eCommerce plugins', 'easy-affiliate')
    );
  }
}
