<?php

namespace EasyAffiliate\Helpers;

use EasyAffiliate\Lib\Utils;
use EasyAffiliate\Models\Options;

class OptionsHelper
{
  public static function wp_pages_dropdown($field_name, $page_id = 0, $auto_page = true, $blank_page = false) {
    $output = '<table class="esaf-affiliate-pages-table"><tbody><tr><td>';

    $output .= sprintf(
      '<select name="%1$s" id="%1$s">',
       esc_attr($field_name)
    );

    if($blank_page || !$auto_page) {
      $output .= sprintf('<option value="">%s</option>', esc_html__('None', 'easy-affiliate'));
    }

    if($auto_page) {
      $output .= sprintf('<option value="auto">%s</option>', esc_html__('- Auto Create New Page -', 'easy-affiliate'));
    }

    foreach(Utils::get_pages() as $page) {
      $output .= sprintf(
        '<option value="%s"%s>%s</option>',
        esc_attr($page->ID),
        selected($page_id, $page->ID, false),
        esc_html($page->post_title)
      );
    }

    $output .= '</select></td><td>';

    if($page_id) {
      $permalink = get_permalink($page_id);

      if($permalink) {
        $output .= sprintf(
          '<a href="%s" title="%s" target="_blank">%s</a><a href="%s" title="%s" target="_blank">%s</a>',
          esc_url(admin_url("post.php?post={$page_id}&action=edit")),
          esc_attr__('Edit page', 'easy-affiliate'),
          '<i class="ea-icon ea-icon-pencil"></i>',
          esc_url($permalink),
          esc_attr__('View page', 'easy-affiliate'),
          '<i class="ea-icon ea-icon-eye"></i>'
        );
      }
    }

    $output .= '</td></tr></table>';

    return $output;
  }

  public static function payment_currencies_dropdown($field_name, $selected) {
    $payment_currencies = array(
      '$', '€', '£', 'د.إ', 'A$', 'Tk', '₿', 'R$', 'лв', 'C$', 'SFr.', '¥', 'Kč', 'kr', 'E£', 'GH₵', 'kn', 'Ft', 'Rp', '₪', '₹',
      '﷼', 'Ksh', '₩', '₸', '₭', 'Mex$', 'RM', '₦', '₨', '₱', 'zł', '₲', 'L', 'руб', 'SR', 'S$', '฿', 'DT', 'TL', 'NT$',
      '₴', '₫', 'R', ''
    );
    ?>
      <select name="<?php echo esc_attr($field_name); ?>" id="<?php echo esc_attr($field_name); ?>">
      <?php
        foreach($payment_currencies as $curr_currency)
        {
          ?>
          <option value="<?php echo esc_attr($curr_currency); ?>"<?php selected($selected, $curr_currency); ?>><?php echo esc_html($curr_currency); ?>&nbsp;</option>
          <?php
        }
      ?>
      </select>
    <?php
  }

  public static function payment_format_dropdown($field_name, $selected) {
    $payment_formats = array( '#,###.##', '#.###,##', '####' );
    ?>
      <select name="<?php echo esc_attr($field_name); ?>" id="<?php echo esc_attr($field_name); ?>">
      <?php
        foreach($payment_formats as $curr_format)
        {
          ?>
          <option value="<?php echo esc_attr($curr_format); ?>"<?php selected($selected, $curr_format); ?>><?php echo esc_html($curr_format); ?>&nbsp;</option>
          <?php
        }
      ?>
      </select>
    <?php
  }

  public static function payment_currency_code_dropdown($field_name, $selected) {
    $codes = array(
      'USD', 'EUR', 'GBP', 'AED', 'ARS', 'AUD', 'BDT', 'BTC', 'BRL', 'BGN', 'CAD', 'CHF', 'CLP', 'CNY', 'COP', 'CZK',
      'DKK', 'DOP', 'EGP', 'GHS', 'HKD', 'HRK', 'HUF', 'IDR', 'ILS', 'INR', 'IRR', 'ISK', 'JPY', 'KES', 'KRW', 'KZT', 'LAK',
      'MXN', 'MYR', 'NGN', 'NOK', 'NPR', 'NZD', 'PHP', 'PKR', 'PLN', 'PYG', 'RON', 'RUB', 'SAR', 'SGD', 'SEK', 'THB',
      'TND', 'TRY', 'TWD', 'UAH', 'VND', 'ZAR'
    );
    ?>
      <select name="<?php echo esc_attr($field_name); ?>" id="<?php echo esc_attr($field_name); ?>">
      <?php
        foreach($codes as $curr_code)
        {
          ?>
          <option value="<?php echo esc_attr($curr_code); ?>"<?php selected($selected, $curr_code); ?>><?php echo esc_html($curr_code); ?>&nbsp;</option>
          <?php
        }
      ?>
      </select>
    <?php
  }

  /**
   * Get the HTML for a single commission level
   *
   * @param  int|null   $level  The commission level
   * @param  float|null $amount The commission amount
   * @return string
   */
  public static function get_commission_level_html($level = null, $amount = null) {
    $options = Options::fetch();
    $amount = $amount ? $amount : 0;

    $output = '<li>';
    $output .= '<span class="wafp_commission_level_label">' . ($level ? esc_html(sprintf(_x('Level %s:', 'commission level', 'easy-affiliate'), $level)) : '') . '</span>';
    $output .= '<span class="wafp_commission_currency_symbol">' . esc_html($options->currency_symbol) . '</span>';
    $output .= '<input type="text" class="esaf-small" size="6" value="' . esc_attr(Utils::format_float($amount)) . '" name="' . esc_attr($options->commission_str) . '[]">';
    $output .= '<span class="wafp_commission_percentage_symbol">%</span>';
    $output .= '</li>';

    return $output;
  }

  /**
   * Get the available email variables
   *
   * @param string $slug
   * @return string[]
   */
  public static function get_email_vars($slug) {
    $variables = [];

    switch($slug) {
      case 'admin_aff_applied':
        $variables = ['first_name', 'last_name', 'email', 'websites', 'strategy', 'social', 'edit_url'];
        break;
      case 'aff_approved':
        $variables = ['first_name', 'last_name', 'email', 'websites', 'strategy'];
        break;
      case 'welcome':
        $variables = [
          'affiliate_first_name', 'affiliate_last_name', 'affiliate_full_name', 'affiliate_login', 'affiliate_email',
          'affiliate_id'
        ];
        break;
      case 'affiliate':
        $variables = [
          'affiliate_first_name', 'affiliate_last_name', 'affiliate_full_name', 'affiliate_login', 'affiliate_email',
          'affiliate_id', 'transaction_type', 'item_name', 'trans_num', 'commission_amount', 'commission_percentage',
          'payment_amount', 'payment_level'
        ];
        break;
      case 'admin':
        $variables = [
          'affiliate_first_name', 'affiliate_last_name', 'affiliate_full_name', 'affiliate_login', 'affiliate_email',
          'affiliate_id', 'transaction_type', 'item_name', 'trans_num', 'commission_amount', 'commission_percentage',
          'payment_amount', 'customer_name', 'customer_email', 'commission_total'
        ];
        break;
    }

    $variables = array_merge($variables, array_keys(Utils::get_default_email_vars(false)));

    return $variables;
  }

  /**
   * Get the email variables populate with test data
   *
   * @return string[]
   */
  public static function get_test_email_vars() {
    return apply_filters('esaf_test_email_vars', [
      'first_name' => 'John',
      'last_name' => 'Doe',
      'email' => 'johndoe@example.com',
      'websites' => 'https://www.example.com/',
      'strategy' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas eget egestas libero. Aliquam faucibus est in massa semper, a semper dui auctor.',
      'social' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas eget egestas libero. Aliquam faucibus est in massa semper, a semper dui auctor.',
      'edit_url' => admin_url(),
      'affiliate_first_name' => 'John',
      'affiliate_last_name' => 'Doe',
      'affiliate_full_name' => 'John Doe',
      'affiliate_login' => 'johndoe',
      'affiliate_email' => 'johndoe@example.com',
      'affiliate_id' => 481,
      'transaction_type' => __('Payment', 'easy-affiliate'),
      'item_name' => __('Example Product', 'easy-affiliate'),
      'trans_num' => '9i8h7g6f5e',
      'commission_amount_num' => '5.00',
      'commission_amount' => AppHelper::format_currency(5.00),
      'commission_percentage' => '20%',
      'payment_amount_num' => '25.00',
      'payment_amount' => AppHelper::format_currency(25.00),
      'payment_level' => 1,
      'commission_total_num' => '6.50',
      'commission_total' => AppHelper::format_currency(6.50),
      'customer_name' => 'Jane Doe',
      'customer_email' => 'janedoe@example.com'
    ]);
  }

  /**
   * Get the HTML for the email editor and associated options
   *
   * @param string    $slug
   * @param \stdClass $email
   * @return string
   */
  public static function get_email_editor_html($slug, $email) {
    ob_start();
    ?>
    <table class="form-table">
      <tbody>
        <tr>
          <th>
            <input type="checkbox" name="<?php echo esc_attr($email->id); ?>" id="<?php echo esc_attr($email->id); ?>" class="esaf-toggle-email-checkbox esaf-toggle-switch esaf-toggle-switch-before-label" <?php checked($email->send); ?> />
            <label for="<?php echo esc_attr($email->id); ?>"></label>
            <label for="<?php echo esc_attr($email->id); ?>"><?php echo esc_html($email->send_label); ?></label>
            <?php AppHelper::info_tooltip("esaf-options-email-{$slug}", esc_html($email->tooltip_body)); ?>
            <button type="button"
                    class="esaf-edit-email-toggle button"
                    data-box="esaf-options-email-<?php echo esc_attr($slug); ?>-box"
                    data-edit-text="<?php esc_attr_e('Edit', 'easy-affiliate'); ?>"
                    data-cancel-text="<?php esc_attr_e('Hide Editor', 'easy-affiliate'); ?>"><?php esc_html_e('Edit', 'easy-affiliate'); ?></button>
            <button type="button"
                    class="esaf-send-test-email button"
                    data-option-id="<?php echo esc_attr($slug); ?>"
                    data-subject-id="<?php echo esc_attr($email->subject_id); ?>"
                    data-body-id="<?php echo esc_attr($email->body_id); ?>"
                    data-use-template-id="<?php echo esc_attr($email->use_template_id); ?>"
            ><?php esc_html_e('Send Test', 'easy-affiliate'); ?></button>
            <button type="button"
                    class="esaf-reset-email button"
                    data-option-id="<?php echo esc_attr($slug); ?>"
                    data-subject-id="<?php echo esc_attr($email->subject_id); ?>"
                    data-body-id="<?php echo esc_attr($email->body_id); ?>"
                    data-use-template-id="<?php echo esc_attr($email->use_template_id); ?>"
            ><?php esc_html_e('Reset to Default', 'easy-affiliate'); ?></button>
          </th>
        </tr>
      </tbody>
    </table>
    <div class="esaf-sub-box-white esaf-options-email-<?php echo esc_attr($slug); ?>-box esaf-hidden esaf-email-editor">
      <table class="form-table">
        <tbody>
        <tr>
          <th scope="row">
            <label for="<?php echo esc_attr($email->subject_id); ?>"><?php esc_html_e('Subject', 'easy-affiliate'); ?></label>
          </th>
          <td>
            <input class="form-field regular-text" type="text" id="<?php echo esc_attr($email->subject_id); ?>" name="<?php echo esc_attr($email->subject_id); ?>" value="<?php echo esc_attr($email->subject); ?>" />
          </td>
        </tr>
        <tr>
          <th scope="row">
            <label for="<?php echo esc_attr($email->body_id); ?>"><?php esc_html_e('Body', 'easy-affiliate'); ?></label>
          </th>
          <td>
            <?php wp_editor($email->body, $email->body_id, ['editor_height' => 300, 'wpautop' => !Utils::has_html_tag($email->body)]); ?>
          </td>
        </tr>
        <tr>
          <th scope="row">
          </th>
          <td>
            <select id="var-<?php echo esc_attr($email->body_id); ?>" class="esaf-insert-email-var-menu">
              <?php foreach(OptionsHelper::get_email_vars($slug) as $var): ?>
                <option value="{$<?php echo esc_attr($var); ?>}">{$<?php echo esc_html($var); ?>}</option>
              <?php endforeach; ?>
            </select>
            <button type="button" class="button esaf-insert-email-var" data-variable-id="var-<?php echo esc_attr($email->body_id); ?>"
                    data-textarea-id="<?php echo esc_attr($email->body_id); ?>"><?php esc_html_e('Insert &uarr;', 'easy-affiliate'); ?></button>
            <br/><br/>
            <input type="checkbox" class="esaf-toggle-switch esaf-toggle-switch-before-label" name="<?php echo esc_attr($email->use_template_id); ?>" id="<?php echo esc_attr($email->use_template_id); ?>" <?php checked($email->use_template); ?> />
            <label for="<?php echo esc_attr($email->use_template_id); ?>"></label>
            <label for="<?php echo esc_attr($email->use_template_id); ?>"><?php esc_html_e('Use default template','easy-affiliate'); ?></label>
            <?php
              AppHelper::info_tooltip(
                $email->use_template_id,
                esc_html__('When this is checked the body of this email will be wrapped in the default email template.', 'easy-affiliate')
              );
            ?>
          </td>
        </tr>
        </tbody>
      </table>
    </div>
    <?php
    return ob_get_clean();
  }

  /**
   * Get the integrations that are available on higher plans
   *
   * @return array
   */
  public static function get_upgrade_integrations() {
    $upgrade_integrations = [];

    if(ESAF_EDITION == 'easy-affiliate-pro') {
      return $upgrade_integrations;
    }

    $integrations = [
      [
        'name' => __('ActiveCampaign Add-on', 'easy-affiliate'),
        'title' => __('ActiveCampaign Signup Integration', 'easy-affiliate'),
        'description' => __('Integrate ActiveCampaign with Easy Affiliate', 'easy-affiliate'),
        'logo' => ESAF_IMAGES_URL . '/integrations/activecampaign.png',
        'plan' => 'Pro',
        'show_on_editions' => ['easy-affiliate-basic', 'easy-affiliate-plus'],
        'addon' => 'activecampaign',
        'upgrade' => true
      ],
      [
        'name' => __('ConvertKit Add-on', 'easy-affiliate'),
        'title' => __('ConvertKit Signup Integration', 'easy-affiliate'),
        'description' => __('Integrate ConvertKit with Easy Affiliate', 'easy-affiliate'),
        'logo' => ESAF_IMAGES_URL . '/integrations/convertkit.png',
        'plan' => 'Plus',
        'show_on_editions' => ['easy-affiliate-basic'],
        'addon' => 'convertkit',
        'upgrade' => true
      ],
      [
        'name' => __('Mailchimp Add-on', 'easy-affiliate'),
        'title' => __('Mailchimp Signup Integration', 'easy-affiliate'),
        'description' => __('Integrate Mailchimp with Easy Affiliate', 'easy-affiliate'),
        'logo' => ESAF_IMAGES_URL . '/integrations/mailchimp.png',
        'plan' => 'Plus',
        'show_on_editions' => ['easy-affiliate-basic'],
        'addon' => 'mailchimp',
        'upgrade' => true
      ]
    ];

    foreach($integrations as $integration) {
      if(!Utils::is_addon_active($integration['addon']) && in_array(ESAF_EDITION, $integration['show_on_editions'])) {
        $upgrade_integrations[] = $integration;
      }
    }

    return $upgrade_integrations;
  }

  /**
   * Get the HTML for the given eCommerce integration
   *
   * @param string $slug
   * @param array  $integration
   * @return string
   */
  public static function get_ecommerce_integration_html($slug, array $integration) {
    $options = Options::fetch();
    $integration['checked'] = in_array($slug, $options->integration);
    $curr_name = "{$options->integration_str}[]";
    $curr_id = "esaf-payment-integration-{$slug}";
    $curr_show_config_box = isset($integration['config']) && file_exists($integration['config']);
    $curr_config_box = "esaf-config-{$slug}-box";
    $disabled = false;

    if(isset($integration['detectable']) && $integration['detectable']) {
      if(isset($integration['controller']) && class_exists($integration['controller']) && method_exists($integration['controller'], 'is_plugin_active') && call_user_func([$integration['controller'], 'is_plugin_active'])) {
        if($integration['checked']) {
          $label = __('%s has been detected and payment integration is active.', 'easy-affiliate');
        }
        else {
          $label = __('%s has been detected and payment integration is not active.', 'easy-affiliate');
        }
      }
      else {
        $label = __('%s has not been detected and payment integration is not active.', 'easy-affiliate');
        $disabled = true;
      }
    }
    else {
      if($integration['checked']) {
        $label = __('%s payment integration is active.', 'easy-affiliate');
      }
      else {
        $label = __('%s payment integration is not active.', 'easy-affiliate');
      }
    }

    $curr_label = sprintf(
      esc_html($label),
      sprintf('<strong>%s</strong>', $integration['label'])
    );
    ?>
    <div class="esaf-payment-integration esaf-clearfix<?php echo $disabled ? ' esaf-payment-integration-disabled' : ''; ?>">
      <?php if(!$disabled) : ?>
        <input type="checkbox" class="esaf-toggle-switch" name="<?php echo esc_attr($curr_name); ?>" id="<?php echo esc_attr($curr_id); ?>" value="<?php echo esc_attr($slug); ?>"<?php checked($integration['checked']); ?> />
        <label for="<?php echo esc_attr($curr_id); ?>"></label>
        <label for="<?php echo esc_attr($curr_id); ?>"><?php echo $curr_label; ?></label>
        <?php if($curr_show_config_box): ?>
          <a href="" class="esaf-toggle-link esaf-expand-payment-integration" data-box="<?php echo esc_attr($curr_config_box); ?>"><i class="ea-icon ea-icon-angle-down"></i></a>
          <div class="esaf-sub-box-white <?php echo esc_attr($curr_config_box); ?>">
            <?php require $integration['config']; ?>
          </div>
        <?php endif; ?>
      <?php else : ?>
        <input type="checkbox" class="esaf-hidden" name="<?php echo esc_attr($curr_name); ?>" id="<?php echo esc_attr($curr_id); ?>" value="<?php echo esc_attr($slug); ?>"<?php checked($integration['checked']); ?> />
        <input type="checkbox" class="esaf-toggle-switch" id="<?php echo esc_attr($curr_id); ?>-disabled" disabled />
        <label for="<?php echo esc_attr($curr_id); ?>-disabled"></label>
        <label for="<?php echo esc_attr($curr_id); ?>-disabled"><?php echo $curr_label; ?></label>
      <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
  }

  /**
   * Get the HTML for the active license details
   *
   * @param array $license
   * @return string
   */
  public static function get_active_license_html($license) {
    ob_start();
    ?>
    <div class="esaf-license-active">
      <table class="form-table">
        <tbody>
          <tr>
            <th scope="row"><?php esc_html_e('Status', 'easy-affiliate'); ?></th>
            <td><strong><?php echo esc_html(sprintf(__('Active on %s', 'easy-affiliate'), Utils::site_domain())); ?></strong></td>
          </tr>
          <tr>
            <th scope="row"><?php esc_html_e('License Key', 'easy-affiliate'); ?></th>
            <td>********-****-****-****-<?php echo esc_html(substr($license['license_key']['license'], -12)); ?></td>
          </tr>
          <tr>
            <th scope="row"><?php esc_html_e('Product', 'easy-affiliate'); ?></th>
            <td><?php echo esc_html($license['product_name']); ?></td>
          </tr>
          <tr>
            <th scope="row"><?php esc_html_e('Activations', 'easy-affiliate'); ?></th>
            <td>
              <?php
                printf(
                  // translators: %1$s: open strong tag, %2$d: activation count, %3$s: max activations, %4$s: close strong tag
                  esc_html__('%1$s%2$d of %3$s%4$s sites have been activated with this license key', 'easy-affiliate'),
                  '<strong>',
                  esc_html($license['activation_count']),
                  esc_html(ucwords($license['max_activations'])),
                  '</strong>'
                );
              ?>
            </td>
          </tr>
        </tbody>
      </table>
      <div id="esaf-license-deactivate"><button type="button" class="button button-primary"><?php echo esc_html(sprintf(__('Deactivate License Key on %s', 'easy-affiliate'), Utils::site_domain())); ?></button></div>
    </div>
    <?php
    return ob_get_clean();
  }

  /**
   * Get the HTML for the license key field
   *
   * @return string
   */
  public static function get_license_key_field_html() {
    $options = Options::fetch();
    ob_start();
    ?>
    <table class="form-table">
      <tbody>
        <tr>
          <th scope="row">
            <label for="<?php echo esc_attr($options->mothership_license_str); ?>"><?php esc_html_e('License Key', 'easy-affiliate') ?></label>
            <?php
              AppHelper::info_tooltip(
                'esaf-options-license',
                sprintf(
                  /* translators: %1$s: open link tag, %2$s: close link tag */
                  esc_html__('Add your Easy Affiliate license key from the email receipt or account area. %1$sRetrieve your license key.%2$s', 'easy-affiliate'),
                  '<a href="https://easyaffiliate.com/account/" target="_blank">',
                  '</a>'
                )
              );
            ?>
          </th>
          <td>
            <div class="esaf-options-license-key-field">
              <input type="text" id="<?php echo esc_attr($options->mothership_license_str); ?>"  data-lpignore="true">
              <i id="esaf-options-license-key-loading" class="ea-icon ea-icon-spinner animate-spin" aria-hidden="true"></i>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
    <?php
    return ob_get_clean();
  }
}
