<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
use EasyAffiliate\Helpers\AppHelper;
use EasyAffiliate\Helpers\OptionsHelper;
use EasyAffiliate\Lib\Utils;
?>
<div class="esaf-settings-section">
  <div class="esaf-settings-section-title">
    <?php esc_html_e('Tracking', 'easy-affiliate'); ?>
  </div>
  <div class="esaf-settings-section-content">
    <table class="form-table">
      <tbody>
        <tr>
          <th scope="row">
            <label for="wafp_commission_type"><?php esc_html_e('Commission Type', 'easy-affiliate') ?></label>
            <?php
              AppHelper::info_tooltip(
                'esaf-options-commission-type',
                esc_html__('Base commissions on fixed amounts or on percentages of sales.', 'easy-affiliate')
              );
            ?>
          </th>
          <td>
            <select name="<?php echo esc_attr($options->commission_type_str); ?>" id="wafp_commission_type">
              <option value="percentage"<?php selected('percentage', $options->commission_type); ?>><?php esc_html_e('Percentage', 'easy-affiliate'); ?></option>
              <option value="fixed"<?php selected('fixed', $options->commission_type); ?>><?php esc_html_e('Fixed Amount', 'easy-affiliate'); ?></option>
            </select>
          </td>
        </tr>
        <tr>
          <th scope="row">
            <label><?php esc_html_e('Commission', 'easy-affiliate') ?></label>
            <?php
              AppHelper::info_tooltip(
                'esaf-options-commission-levels',
                esc_html__('Configure what percentage or fixed amount you want to pay your affiliates per sale.', 'easy-affiliate')
              );
            ?>
          </th>
          <td>
            <ul id="wafp_commission_levels"<?php echo count($options->commission) > 1 ? ' class="wafp-has-multiple-commission-levels"' : ''; ?>>
              <?php
                foreach($options->commission as $index => $commish) {
                  echo OptionsHelper::get_commission_level_html($index + 1, $commish);
                }
              ?>
            </ul>
            <?php echo AppHelper::get_commission_levels_upgrade_html(); ?>
          </td>
        </tr>
        <tr>
          <th scope="row">
            <label><?php esc_html_e('Subscription Commissions','easy-affiliate'); ?></label>
            <?php
              AppHelper::info_tooltip(
                'esaf-options-subscription-commissions',
                esc_html__('For subscriptions, choose which transactions to pay commissions on.', 'easy-affiliate')
              );
            ?>
          </th>
          <td>
            <div role="radiogroup" class="esaf-form-field-tiles esaf-subscription-commissions">
              <div class="esaf-form-field-tile">
                <input type="radio" id="<?php echo esc_attr($options->subscription_commissions_str); ?>-first-only" name="<?php echo esc_attr($options->subscription_commissions_str); ?>" value="first-only" <?php checked($options->subscription_commissions, 'first-only'); ?> class="esaf-toggle-radio">
                <label for="<?php echo esc_attr($options->subscription_commissions_str); ?>-first-only" class="button"><?php echo file_get_contents(ESAF_IMAGES_PATH . '/subscription-commissions-first-only.svg'); ?><?php esc_html_e('Pay Commission on First Sale Only', 'easy-affiliate'); ?></label>
              </div>
              <div class="esaf-form-field-tile">
                <input type="radio" id="<?php echo esc_attr($options->subscription_commissions_str); ?>-all" name="<?php echo esc_attr($options->subscription_commissions_str); ?>" value="all" <?php checked($options->subscription_commissions, 'all'); ?> class="esaf-toggle-radio">
                <label for="<?php echo esc_attr($options->subscription_commissions_str); ?>-all" class="button"><?php echo file_get_contents(ESAF_IMAGES_PATH . '/subscription-commissions-all.svg'); ?><?php esc_html_e('Pay Commission on All Sales', 'easy-affiliate'); ?></label>
              </div>
            </div>
          </td>
        </tr>
        <tr>
          <th scope="row">
            <label for="<?php echo esc_attr($options->expire_after_days_str); ?>"><?php esc_html_e('Expire Cookie', 'easy-affiliate'); ?></label>
            <?php
              AppHelper::info_tooltip(
                'esaf-options-expire-cookie',
                esc_html__('The length of time that you\'ll allow before the affiliate cookie expires', 'easy-affiliate')
              );
            ?>
          </th>
          <td>
            <input type="text" class="esaf-small" id="<?php echo esc_attr($options->expire_after_days_str); ?>" name="<?php echo esc_attr($options->expire_after_days_str); ?>" value="<?php echo esc_attr($options->expire_after_days); ?>" size="6" />&nbsp;<?php esc_html_e('Days', 'easy-affiliate'); ?>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
<div class="esaf-settings-section">
  <div class="esaf-settings-section-title">
    <?php esc_html_e('Payouts', 'easy-affiliate'); ?>
  </div>
  <div class="esaf-settings-section-content">
    <table class="form-table">
      <tbody>
        <tr>
          <th scope="row">
            <label><?php esc_html_e('Payout Method', 'easy-affiliate'); ?></label>
            <?php
              AppHelper::info_tooltip(
                'esaf-options-payment-method',
                esc_html__('What method will you use to pay your affiliates?', 'easy-affiliate')
              );
            ?>
          </th>
          <td>
            <div role="radiogroup" class="esaf-form-field-tiles esaf-payout-method">
              <div class="esaf-form-field-tile">
                <input type="radio" id="<?php echo esc_attr($options->payment_type_str); ?>-paypal" name="<?php echo esc_attr($options->payment_type_str); ?>" value="paypal" <?php checked($options->payment_type, 'paypal'); ?> class="esaf-toggle-radio">
                <label for="<?php echo esc_attr($options->payment_type_str); ?>-paypal" class="button"><?php echo file_get_contents(ESAF_IMAGES_PATH . '/payout-method-paypal-mass.svg'); ?><?php esc_html_e('PayPal Mass Payment File', 'easy-affiliate'); ?></label>
              </div>
              <div class="esaf-form-field-tile">
                <input type="radio" id="<?php echo esc_attr($options->payment_type_str); ?>-paypal-1-click" name="<?php echo esc_attr($options->payment_type_str); ?>" value="paypal-1-click" <?php checked($options->payment_type, 'paypal-1-click'); ?> class="esaf-toggle-radio" data-box="esaf-options-paypal-api-keys">
                <label for="<?php echo esc_attr($options->payment_type_str); ?>-paypal-1-click" class="button"><?php echo file_get_contents(ESAF_IMAGES_PATH . '/payout-method-paypal-1-click.svg'); ?><?php esc_html_e('PayPal 1-Click', 'easy-affiliate'); ?></label>
              </div>
              <div class="esaf-form-field-tile">
                <input type="radio" id="<?php echo esc_attr($options->payment_type_str); ?>-manual" name="<?php echo esc_attr($options->payment_type_str); ?>" value="manual" <?php checked($options->payment_type, 'manual'); ?> class="esaf-toggle-radio">
                <label for="<?php echo esc_attr($options->payment_type_str); ?>-manual" class="button"><?php echo file_get_contents(ESAF_IMAGES_PATH . '/payout-method-offline.svg'); ?><?php esc_html_e('Offline', 'easy-affiliate'); ?></label>
              </div>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
    <div class="esaf-sub-box-white esaf-options-paypal-api-keys">
      <table class="form-table">
        <tbody>
          <tr>
            <th scope="row">
              <label for="<?php echo esc_attr($options->paypal_client_id_str); ?>"><?php esc_html_e('PayPal App Client ID', 'easy-affiliate'); ?></label>
              <?php
                AppHelper::info_tooltip(
                  'esaf-options-paypal-client-id',
                  esc_html__('PayPal App Client ID that you can get when creating your PayPal app', 'easy-affiliate')
                );
              ?>
            </th>
            <td>
              <input type="text" id="<?php echo esc_attr($options->paypal_client_id_str); ?>" name="<?php echo esc_attr($options->paypal_client_id_str); ?>" value="<?php echo $options->paypal_client_id; ?>"></td>
          </tr>
          <tr>
            <th scope="row">
              <label for="<?php echo esc_attr($options->paypal_secret_id_str); ?>"><?php esc_html_e('PayPal App Secret Key', 'easy-affiliate'); ?></label>
              <?php
                AppHelper::info_tooltip(
                  'esaf-options-paypal-secret-id',
                  esc_html__('PayPal App Secret Key that you can get when creating your PayPal app', 'easy-affiliate')
                );
              ?>
            </th>
            <td>
              <input type="text" id="<?php echo esc_attr($options->paypal_secret_id_str); ?>" name="<?php echo esc_attr($options->paypal_secret_id_str); ?>" value="<?php echo $options->paypal_secret_id; ?>"></td>
          </tr>
        </tbody>
      </table>
    </div>
    <table class="form-table">
      <tbody>
        <tr>
          <th scope="row">
            <input type="checkbox" class="esaf-toggle-checkbox esaf-toggle-switch esaf-toggle-switch-before-label" name="<?php echo esc_attr($options->minimum_str); ?>-checkbox" id="<?php echo esc_attr($options->minimum_str); ?>-checkbox" data-box="esaf-options-minimum-payout-box" <?php checked(($options->minimum > 0.00)); ?> />
            <label for="<?php echo esc_attr($options->minimum_str); ?>-checkbox"></label>
            <label for="<?php echo esc_attr($options->minimum_str); ?>-checkbox"><?php esc_html_e('Require Minimum Payout','easy-affiliate'); ?></label>
            <?php
              AppHelper::info_tooltip(
                'esaf-options-minimum-payout',
                esc_html__('If this box is checked, an affiliate won\'t be eligible to get commissions until they\'ve acquired a minimum amount of commissions', 'easy-affiliate')
              );
            ?>
          </th>
        </tr>
      </tbody>
    </table>
    <div class="esaf-sub-box-white esaf-options-minimum-payout-box">
      <table class="form-table">
        <tbody>
          <tr>
            <th scope="row">
              <label for="<?php echo esc_attr($options->minimum_str); ?>"><?php esc_html_e('Minimum Payout', 'easy-affiliate'); ?></label>
            </th>
            <td>
              <input class="form-field regular-text" type="text" id="<?php echo esc_attr($options->minimum_str); ?>" name="<?php echo esc_attr($options->minimum_str); ?>" value="<?php echo esc_attr(Utils::format_float($options->minimum)); ?>" />
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <table class="form-table">
      <tbody>
        <tr>
          <th scope="row">
            <label for="<?php echo esc_attr($options->payout_waiting_period_str); ?>"><?php esc_html_e('Payout Waiting Period', 'easy-affiliate'); ?></label>
            <?php
              AppHelper::info_tooltip(
                'esaf-options-payout-waiting-period',
                esc_html__('Choose the length of time that affiliates have to wait for a payout after a commission is earned. The value selected here is used to calculate the "Estimated Next Payout" shown on the Affiliate Dashboard.', 'easy-affiliate')
              );
            ?>
          </th>
          <td>
            <select id="<?php echo esc_attr($options->payout_waiting_period_str); ?>" name="<?php echo esc_attr($options->payout_waiting_period_str); ?>">
              <option value="0"><?php esc_html_e('None', 'easy-affiliate'); ?></option>
              <?php foreach(range(1, 12) as $month) : ?>
                <option value="<?php echo esc_attr($month); ?>"<?php selected($options->payout_waiting_period, $month); ?>><?php echo esc_html(sprintf(_n('%s month', '%s months', $month, 'easy-affiliate'), $month)); ?></option>
              <?php endforeach; ?>
            </select>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
