<?php

namespace EasyAffiliate\Helpers;

use EasyAffiliate\Controllers\UpdateCtrl;
use EasyAffiliate\Models\Commission;
use EasyAffiliate\Models\Options;
use EasyAffiliate\Models\User;
use EasyAffiliate\Lib\Utils;

class AppHelper {
  public static function format_number($number, $show_decimals = false, $truncate_zeroes = false) {
    global $wp_locale;

    $decimal_point = $wp_locale->number_format['decimal_point'];
    $thousands_sep = $wp_locale->number_format['thousands_sep'];

    $rstr = 0;

    if((float)$number > 0.00) {
      if($show_decimals) {
        $rstr = (string)number_format((float)$number, 2, $decimal_point, $thousands_sep);
      }
      else {
        $rstr = (string)number_format((float)$number, 0, $decimal_point, $thousands_sep);
      }

      if($show_decimals && $truncate_zeroes) {
        $rstr = preg_replace('/' . preg_quote($decimal_point) . '00$/', '', $rstr);
      }
    }

    return $rstr;
  }

  public static function get_extension( $mimetype ) {
    switch( $mimetype ) {
      case "application/msword":
      case "application/rtf":
      case "text/richtext":
        return "doc";
      case "application/vnd.ms-excel":
        return "xls";
      case "application/vnd.ms-powerpoint":
        return "ppt";
      case "application/pdf":
        return "pdf";
      case "application/zip":
        return "zip";
      case "image/jpeg":
        return "jpg";
      case "image/gif":
        return "gif";
      case "image/png":
        return "png";
      case "image/tiff":
        return "tif";
      case "text/plain":
        return "txt";
      case "text/html":
        return "html";
      case "video/quicktime":
        return "mov";
      case "video/x-msvideo":
        return "avi";
      case "video/x-ms-wmv":
        return "wmv";
      case "video/ms-wmv":
        return "wmv";
      case "video/mpeg":
        return "mpg";
      case "audio/mpg":
        return "mp3";
      case "audio/x-m4a":
        return "aac";
      case "audio/m4a":
        return "aac";
      case "audio/x-wav":
        return "wav";
      case "audio/wav":
        return "wav";
      case "application/x-zip-compressed":
        return "zip";
      default:
        return "bin";
    }
  }

  public static function format_currency($number, $show_symbol = true) {
    $options = Options::fetch();

    //TODO: We may want to use $wp_locale in the future for this ... then we could just eliminate it from the options page.
    /* Example:

    global $wp_locale;

    if(is_numeric($number))
      return number_format($number, $num_decimals, $wp_locale->number_format['decimal_point'], '');
  */

    if($options->number_format == "#.###,##") {
      $dec = ',';
      $tho = '.';
    }
    else if($options->number_format == '####') {
      $dec = '';
      $tho = '';
    }
    else {
      $dec = '.';
      $tho = ',';
    }

    if($options->number_format == '####') {
      $formatted = (string) (int) $number;
    }
    else {
      $formatted = number_format($number, 2, $dec, $tho);
    }

    if($show_symbol) {
      if($options->currency_symbol_after_amount) {
        $formatted = $formatted . $options->currency_symbol;
      }
      else {
        $formatted = $options->currency_symbol . $formatted;
      }
    }

    return apply_filters('esaf_format_currency', $formatted, $number, $show_symbol);
  }

  public static function admin_header() {
    ob_start();
    ?>
    <div id="esaf-admin-header">
      <div class="esaf-admin-header-logo">
        <?php echo file_get_contents(ESAF_IMAGES_PATH . '/header-logo.svg'); ?>
      </div>
      <a href="https://easyaffiliate.com/support/"><?php esc_html_e('Support', 'easy-affiliate'); ?></a>
    </div>
    <?php
    return ob_get_clean();
  }

  /**
   * Render the page title HTML
   *
   * @param  string $title The page title
   * @param  bool   $new   The HTML for the Add New button
   * @return void
   */
  public static function plugin_title($title, $new = false) {
    require ESAF_VIEWS_PATH . '/shared/title.php';
  }

  public static function display_affiliate_commissions($aff_id) {
    $aff = new User($aff_id);

    $commission_source = Commission::get_source($aff);
    $commission_type   = Commission::get_type($aff);
    $commissions       = Commission::get_levels($aff);
    $commissions_count = count($commissions);
    ?>
    <div class="esaf-affiliate-commissions">
      <?php if($commission_source['slug'] != 'global' && Utils::is_admin()) : ?>
        <h3><?php echo esc_html(sprintf(__('Commissions (%s)', 'easy-affiliate'), $commission_source['label'])); ?></h3>
      <?php else : ?>
        <h3><?php esc_html_e('Commissions', 'easy-affiliate'); ?></h3>
      <?php endif; ?>
      <?php for($i = 0; $i < $commissions_count; $i++) : ?>
        <?php $commission_level = $commissions[$i]; ?>
        <div class="esaf-affiliate-commissions-level-row">
          <?php if($commissions_count > 1) : ?>
            <span class="esaf-affiliate-commissions-level"><?php echo esc_html(sprintf(_x('Level %s:', 'commission level', 'easy-affiliate'), ($i + 1))); ?></span>
          <?php endif; ?>
          <span class="esaf-affiliate-commissions-percentage"><?php echo esc_html($commission_type == 'fixed' ? AppHelper::format_currency($commission_level) : Utils::format_float($commission_level) . '%'); ?></span>
        </div>
      <?php endfor; ?>
    </div>
    <?php
  }

  public static function display_commission_override($type, $levels, $subscription_commissions) {
    ?>
    <div id="wafp-commission-override" class="esaf-hidden">
      <div class="esaf-commission-override-field">
        <div class="esaf-commission-override-field-label">
          <label for="wafp_commission_type"><?php esc_html_e('Commission Type', 'easy-affiliate'); ?></label>
          <?php
            AppHelper::info_tooltip(
              'esaf-commission-override-commission-type',
              esc_html__('Base commissions on fixed amounts or on percentages of sales.', 'easy-affiliate')
            );
          ?>
        </div>
        <select id="wafp_commission_type" name="wafp-commission-type">
          <option value="percentage" <?php selected($type, 'percentage'); ?>><?php esc_html_e('Percentage', 'easy-affiliate'); ?></option>
          <option value="fixed" <?php selected($type, 'fixed'); ?>><?php esc_html_e('Fixed Amount', 'easy-affiliate'); ?></option>
        </select>
      </div>
      <div class="esaf-commission-override-field">
        <div class="esaf-commission-override-field-label">
          <label><?php esc_html_e('Commission', 'easy-affiliate'); ?></label>
          <?php
            AppHelper::info_tooltip(
              'esaf-commission-override-commission-levels',
              esc_html__('Configure what percentage or fixed amount you want to pay your affiliates per sale.', 'easy-affiliate')
            );
          ?>
        </div>
        <ul id="wafp_commission_levels"<?php echo count($levels) > 1 ? ' class="wafp-has-multiple-commission-levels"' : ''; ?>>
          <?php
            foreach($levels as $index => $commish) {
              echo OptionsHelper::get_commission_level_html($index + 1, $commish);
            }
          ?>
        </ul>
        <?php echo AppHelper::get_commission_levels_upgrade_html(); ?>
      </div>
      <div class="esaf-commission-override-field">
        <div class="esaf-commission-override-field-label">
          <label for="wafp_subscription_commissions"><?php esc_html_e('Subscription Commissions', 'easy-affiliate'); ?></label>
          <?php
            AppHelper::info_tooltip(
              'esaf-commission-override-subscription-commissions',
              esc_html__('For subscriptions, choose which transactions to pay commissions on.', 'easy-affiliate')
            );
          ?>
        </div>
        <select id="wafp_subscription_commissions" name="wafp-subscription-commissions">
          <option value="first-only" <?php selected($subscription_commissions, 'first-only'); ?>><?php esc_html_e('Pay commission on first sale only', 'easy-affiliate'); ?></option>
          <option value="all" <?php selected($subscription_commissions, 'all'); ?>><?php esc_html_e('Pay commission on all sales', 'easy-affiliate'); ?></option>
        </select>
      </div>
    </div>
    <?php
  }

  public static function info_tooltip($id, $content) {
    ?>
    <span id="esaf-tooltip-<?php echo esc_attr($id); ?>" class="esaf-tooltip">
      <span><i class="ea-icon ea-icon-info-circled ea-16"></i></span>
      <span class="esaf-data-info esaf-hidden"><?php echo $content; ?></span>
    </span>
    <?php
  }

  /**
   * Get the HTML for a dropdown menu of countries
   *
   * @param string $field_name
   * @param string $field_id
   * @param string $value
   * @param bool   $required
   * @return string
   */
  public static function countries_dropdown($field_name, $field_id, $value = '', $required = false) {
    ob_start();
    ?>
    <select id="<?php echo esc_attr($field_id); ?>" name="<?php echo esc_attr($field_name); ?>"<?php echo $required ? ' required' : ''; ?>>
      <option value=""><?php esc_html_e('Please select', 'easy-affiliate'); ?></option>
      <?php foreach(Utils::get_countries() as $country_key => $country) : ?>
        <option value="<?php echo esc_attr($country_key); ?>" <?php selected($value, $country_key); ?>><?php echo esc_html($country); ?></option>
      <?php endforeach; ?>
    </select>
    <?php
    return ob_get_clean();
  }

  /**
   * Get the HTML for the commission levels upgrade or install popups
   *
   * @return string
   */
  public static function get_commission_levels_upgrade_html() {
    if(Utils::is_addon_active('commission-levels')) {
      return '';
    }

    $addons = UpdateCtrl::addons(true, false, true);
    $slug = 'easy-affiliate-commission-levels';

    if(!is_object($addons) || !property_exists($addons, $slug) || !is_object($addons->$slug)) {
      return '';
    }

    $addon = $addons->$slug;
    $installed = isset($addon->extra_info->directory) && is_dir(WP_PLUGIN_DIR . '/' . $addon->extra_info->directory);

    ob_start();
    ?>
    <?php if(ESAF_EDITION == 'easy-affiliate-basic') : ?>
      <button id="esaf-commission-levels-upgrade" type="button" class="button button-primary"><?php esc_html_e('Add Level', 'easy-affiliate'); ?></button>
      <div id="esaf-commission-levels-upgrade-popup" class="esaf-popup esaf-upgrade-popup mfp-hide">
        <div class="esaf-popup-content">
          <i class="ea-icon ea-icon-lock"></i>
          <h3><?php esc_html_e('Commission Levels are a Plus Feature', 'easy-affiliate'); ?></h3>
          <p><?php esc_html_e('We\'re sorry, Commission Levels are not available on your plan. Please upgrade to the Plus plan to unlock all these awesome features.', 'easy-affiliate'); ?></p>
          <a href="https://easyaffiliate.com/pricing/" class="button button-primary button-hero"><?php esc_html_e('Upgrade to Plus', 'easy-affiliate'); ?></a>
        </div>
      </div>
    <?php else : ?>
      <button id="esaf-commission-levels-install" type="button" class="button button-primary"><?php esc_html_e('Add Level', 'easy-affiliate'); ?></button>
      <div id="esaf-commission-levels-install-popup" class="esaf-popup esaf-install-popup mfp-hide">
        <div class="esaf-popup-content">
          <?php if ($installed) : ?>
            <i class="ea-icon ea-icon-toggle-on"></i>
            <h3><?php esc_html_e('Activate Commission Levels Add-on', 'easy-affiliate'); ?></h3>
            <p><?php esc_html_e('The Commission Levels add-on is installed but not currently active. Do you want to activate this add-on to enable Commission Levels?', 'easy-affiliate'); ?></p>
            <div class="esaf-columns esaf-2-columns esaf-clearfix">
              <div><button type="button" id="esaf-commission-levels-install-cancel" class="button button-secondary button-hero"><?php esc_html_e('Cancel', 'easy-affiliate'); ?></button></div>
              <div><button type="button" id="esaf-commission-levels-install-action" data-plugin="<?php echo esc_attr($addon->extra_info->main_file); ?>" class="button button-primary button-hero" data-action="activate"><?php esc_html_e('Activate', 'easy-affiliate'); ?></button></div>
            </div>
          <?php else : ?>
            <i class="ea-icon ea-icon-download-cloud"></i>
            <h3><?php esc_html_e('Install Commission Levels Add-on', 'easy-affiliate'); ?></h3>
            <p><?php esc_html_e('The Commission Levels add-on is available on your plan but not currently installed. Do you want to install and activate this add-on to enable Commission Levels?', 'easy-affiliate'); ?></p>
            <div class="esaf-columns esaf-2-columns esaf-clearfix">
              <div><button type="button" id="esaf-commission-levels-install-cancel" class="button button-secondary button-hero"><?php esc_html_e('Cancel', 'easy-affiliate'); ?></button></div>
              <div><button type="button" id="esaf-commission-levels-install-action" data-plugin="<?php echo esc_attr($addon->url); ?>" class="button button-primary button-hero" data-action="install"><?php esc_html_e('Install', 'easy-affiliate'); ?></button></div>
            </div>
          <?php endif; ?>
        </div>
      </div>
    <?php endif; ?>
    <?php
    return ob_get_clean();
  }
}
