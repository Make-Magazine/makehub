<?php

namespace EasyAffiliate\Models;

use EasyAffiliate\Lib\Utils;

class Options {
  public static function fetch() {
    $options = get_option('wafp_options');

    if($options) {
      if(is_string($options)) {
        $options = unserialize($options);
      }

      if(is_object($options) && is_a($options, Options::class)) {
        $options->set_default_options();
        $options->store(); // store will convert this back into an array
      }
      else if(is_array($options)) {
        $options = new Options($options);
      }
      else {
        $options = new Options();
      }
    }
    else {
      $options = new Options();
    }

    return $options;
  }

  // License Key
  public $mothership_license;
  public $mothership_license_str;
  public $edge_updates;
  public $edge_updates_str;

  // Business Info
  public $business_name;
  public $business_name_str;
  public $business_address_one;
  public $business_address_one_str;
  public $business_address_two;
  public $business_address_two_str;
  public $business_address_city;
  public $business_address_city_str;
  public $business_address_state;
  public $business_address_state_str;
  public $business_address_zip;
  public $business_address_zip_str;
  public $business_address_country;
  public $business_address_country_str;
  public $business_tax_id;
  public $business_tax_id_str;

  // Page Setup Variables
  public $dashboard_page_id;
  public $dashboard_page_id_str;
  public $signup_page_id;
  public $signup_page_id_str;
  public $login_page_id;
  public $login_page_id_str;

  //Affiliate Settings
  public $make_new_users_affiliates;
  public $make_new_users_affiliates_str;
  public $show_address_fields;
  public $show_address_fields_str;
  public $show_address_fields_account;
  public $show_address_fields_account_str;
  public $require_address_fields;
  public $require_address_fields_str;
  public $show_tax_id_fields;
  public $show_tax_id_fields_str;
  public $show_tax_id_fields_account;
  public $show_tax_id_fields_account_str;
  public $require_tax_id_fields;
  public $require_tax_id_fields_str;
  public $pretty_affiliate_links;
  public $pretty_affiliate_links_str;
  public $utm_affiliate_links;
  public $utm_affiliate_links_str;
  public $registration_type;
  public $registration_type_str;
  public $application_thank_you;
  public $application_thank_you_str;
  public $pro_dashboard_enabled;
  public $pro_dashboard_enabled_str;
  public $pro_dashboard_brand_color;
  public $pro_dashboard_brand_color_str;
  public $pro_dashboard_accent_color;
  public $pro_dashboard_accent_color_str;
  public $pro_dashboard_menu_text_color;
  public $pro_dashboard_menu_text_color_str;
  public $pro_dashboard_menu_text_highlight_color;
  public $pro_dashboard_menu_text_highlight_color_str;
  public $pro_dashboard_logo_url;
  public $pro_dashboard_logo_url_str;
  public $showcase_url_enabled;
  public $showcase_url_enabled_str;
  public $showcase_url_href;
  public $showcase_url_href_str;
  public $showcase_url_title;
  public $showcase_url_title_str;

  // Commission Settings
  public $commission_type;
  public $commission_type_str;
  public $commission;
  public $commission_str;
  public $paypal_client_id;
  public $paypal_client_id_str;
  public $paypal_secret_id;
  public $paypal_secret_id_str;
  public $subscription_commissions;
  public $subscription_commissions_str;

  public $minimum;
  public $minimum_str;
  public $payout_waiting_period;
  public $payout_waiting_period_str;

  // Integration Settings
  public $integration;
  public $integration_str;

  // Paypal IPN Options
  public $paypal_sandbox;
  public $paypal_sandbox_str;
  public $paypal_emails;
  public $paypal_emails_str;
  public $paypal_src;
  public $paypal_src_str;
  public $paypal_dst;
  public $paypal_dst_str;

  // WooCommerce Settings
  public $woocommerce_integration_order_status;
  public $woocommerce_integration_order_status_str;

  // Payment Settings
  public $payment_type;
  public $payment_type_str;

  // Dashboard CSS Settings
  public $dash_css_width;
  public $dash_css_width_str;

  public $dash_nav;
  public $dash_nav_str;

  // Cookie Settings
  public $expire_after_days;
  public $expire_after_days_str;

  // International Settings
  public $currency_code;
  public $currency_code_str;
  public $currency_symbol;
  public $currency_symbol_str;
  public $currency_symbol_after_amount;
  public $currency_symbol_after_amount_str;
  public $number_format;
  public $number_format_str;

  // Notification Settings
  public $welcome_email;
  public $welcome_email_str;
  public $welcome_email_subject;
  public $welcome_email_subject_str;
  public $welcome_email_body;
  public $welcome_email_body_str;
  public $welcome_email_use_template;
  public $welcome_email_use_template_str;
  public $admin_email;
  public $admin_email_str;
  public $admin_email_subject;
  public $admin_email_subject_str;
  public $admin_email_body;
  public $admin_email_body_str;
  public $admin_email_use_template;
  public $admin_email_use_template_str;
  public $affiliate_email;
  public $affiliate_email_str;
  public $affiliate_email_subject;
  public $affiliate_email_subject_str;
  public $affiliate_email_body;
  public $affiliate_email_body_str;
  public $affiliate_email_use_template;
  public $affiliate_email_use_template_str;
  public $admin_email_addresses;
  public $admin_email_addresses_str;
  public $email_from_name;
  public $email_from_name_str;
  public $email_from_address;
  public $email_from_address_str;

  public $admin_aff_applied_email_enabled;
  public $admin_aff_applied_email_enabled_str;
  public $admin_aff_applied_email_subject;
  public $admin_aff_applied_email_subject_str;
  public $admin_aff_applied_email_body;
  public $admin_aff_applied_email_body_str;
  public $admin_aff_applied_email_use_template;
  public $admin_aff_applied_email_use_template_str;

  public $aff_approved_email_enabled;
  public $aff_approved_email_enabled_str;
  public $aff_approved_email_subject;
  public $aff_approved_email_subject_str;
  public $aff_approved_email_body;
  public $aff_approved_email_body_str;
  public $aff_approved_email_use_template;
  public $aff_approved_email_use_template_str;

  public $affiliate_agreement_enabled;
  public $affiliate_agreement_enabled_str;
  public $affiliate_agreement_text;
  public $affiliate_agreement_text_str;

  public $custom_message;
  public $custom_message_str;

  // Is the setup sufficiently completed for affiliate program to function?
  public $setup_complete;

  public function __construct($options_array = []) {
    // Set values from array
    foreach($options_array as $key => $value) {
      $this->{$key} = $value;
    }

    $this->set_default_options();
  }

  public function set_default_options() {
    if(!isset($this->mothership_license))
      $this->mothership_license = '';

    if(!isset($this->edge_updates))
      $this->edge_updates = false;

    if(!isset($this->business_name))
      $this->business_name = '';

    if(!isset($this->business_name_str))
      $this->business_name_str = 'wafp-business-name';

    if(!isset($this->business_address_one))
      $this->business_address_one = '';

    if(!isset($this->business_address_one_str))
      $this->business_address_one_str = 'wafp-business-address-one';

    if(!isset($this->business_address_two))
      $this->business_address_two = '';

    if(!isset($this->business_address_two_str))
      $this->business_address_two_str = 'wafp-business-address-two';

    if(!isset($this->business_address_city))
      $this->business_address_city = '';

    if(!isset($this->business_address_city_str))
      $this->business_address_city_str = 'wafp-business-address-city';

    if(!isset($this->business_address_state))
      $this->business_address_state = '';

    if(!isset($this->business_address_state_str))
      $this->business_address_state_str = 'wafp-business-address-state';

    if(!isset($this->business_address_zip))
      $this->business_address_zip = '';

    if(!isset($this->business_address_zip_str))
      $this->business_address_zip_str = 'wafp-business-address-zip';

    if(!isset($this->business_address_country))
      $this->business_address_country = '';

    if(!isset($this->business_address_country_str))
      $this->business_address_country_str = 'wafp-business-address-country';

    if(!isset($this->business_tax_id))
      $this->business_tax_id = '';

    if(!isset($this->business_tax_id_str))
      $this->business_tax_id_str = 'wafp-business-address-tax-id';

    if(!isset($this->dashboard_page_id))
      $this->dashboard_page_id = 0;

    if(!isset($this->signup_page_id) or empty($this->signup_page_id))
      $this->signup_page_id = 0;

    if(!isset($this->login_page_id) or empty($this->login_page_id))
      $this->login_page_id = 0;

    if(!isset($this->welcome_email))
      $this->welcome_email = 1;

    if(!isset($this->welcome_email_subject))
      $this->welcome_email_subject = $this->get_default_email_subject('welcome');

    if(!isset($this->welcome_email_body))
      $this->welcome_email_body = $this->get_default_email_body('welcome');

    if(!isset($this->welcome_email_use_template))
      $this->welcome_email_use_template = 1;

    if(!isset($this->admin_email))
      $this->admin_email = 1;

    if(!isset($this->admin_email_subject))
      $this->admin_email_subject = $this->get_default_email_subject('admin');

    if(!isset($this->admin_email_body))
      $this->admin_email_body = $this->get_default_email_body('admin');

    if(!isset($this->admin_email_use_template))
      $this->admin_email_use_template = 1;

    if(!isset($this->affiliate_email))
      $this->affiliate_email = 1;

    if(!isset($this->affiliate_email_subject))
      $this->affiliate_email_subject = $this->get_default_email_subject('affiliate');

    if(!isset($this->affiliate_email_body))
      $this->affiliate_email_body = $this->get_default_email_body('affiliate');

    if(!isset($this->affiliate_email_use_template))
      $this->affiliate_email_use_template = 1;

    if(!isset($this->admin_aff_applied_email_enabled))
      $this->admin_aff_applied_email_enabled = true;

    if(!isset($this->admin_aff_applied_email_subject))
      $this->admin_aff_applied_email_subject = $this->get_default_email_subject('admin_aff_applied');

    if(!isset($this->admin_aff_applied_email_body))
      $this->admin_aff_applied_email_body = $this->get_default_email_body('admin_aff_applied');

    if(!isset($this->admin_aff_applied_email_use_template))
      $this->admin_aff_applied_email_use_template = 1;

    if(!isset($this->aff_approved_email_enabled))
      $this->aff_approved_email_enabled = true;

    if(!isset($this->aff_approved_email_subject))
      $this->aff_approved_email_subject = $this->get_default_email_subject('aff_approved');

    if(!isset($this->aff_approved_email_body))
      $this->aff_approved_email_body = $this->get_default_email_body('aff_approved');

    if(!isset($this->aff_approved_email_use_template))
      $this->aff_approved_email_use_template = 1;

    if(!isset($this->affiliate_agreement_enabled))
      $this->affiliate_agreement_enabled = 0;

    if(!isset($this->affiliate_agreement_text))
      $this->affiliate_agreement_text = '';

    // Affiliate Settings
    if(!isset($this->make_new_users_affiliates))
      $this->make_new_users_affiliates = 0;

    $this->make_new_users_affiliates_str     = 'wafp-make-new-users-affiliates';

    if(!isset($this->show_address_fields))
      $this->show_address_fields = 0;

    $this->show_address_fields_str     = 'wafp-show-address-fields';

    if(!isset($this->show_address_fields_account)) {
      if(isset($this->show_address_fields)) {
        $this->show_address_fields_account = $this->show_address_fields;
      }
      else {
        $this->show_address_fields_account = 0;
      }
    }

    $this->show_address_fields_account_str = 'wafp-show-address-fields-account';

    if(!isset($this->require_address_fields)) {
      if(isset($this->force_account_info)) {
        $this->require_address_fields = $this->force_account_info;
      }
      else {
        $this->require_address_fields = 0;
      }
    }

    $this->require_address_fields_str = 'wafp-require-address-fields';

    if(!isset($this->show_tax_id_fields))
      $this->show_tax_id_fields = 0;

    $this->show_tax_id_fields_str    = 'wafp-show-tax-id-fields';

    if(!isset($this->show_tax_id_fields_account)) {
      if(isset($this->show_tax_id_fields)) {
        $this->show_tax_id_fields_account = $this->show_tax_id_fields;
      }
      else {
        $this->show_tax_id_fields_account = 0;
      }
    }

    $this->show_tax_id_fields_account_str = 'wafp-show-tax-id-fields-account';

    if(!isset($this->require_tax_id_fields)) {
      if(isset($this->force_account_info)) {
        $this->require_tax_id_fields = $this->force_account_info;
      }
      else {
        $this->require_tax_id_fields = 0;
      }
    }

    $this->require_tax_id_fields_str = 'wafp-require-tax-id-fields';

    if(!isset($this->pretty_affiliate_links))
      $this->pretty_affiliate_links = 0;

    $this->pretty_affiliate_links_str = 'wafp-pretty-affiliate-links';

    if(!isset($this->utm_affiliate_links))
      $this->utm_affiliate_links = 0;

    $this->utm_affiliate_links_str = 'wafp-utm-affiliate-links';

    $this->registration_type_str = 'wafp-registration-type';
    if(!isset($this->registration_type)) {
      $this->registration_type = 'public';
    }

    $this->application_thank_you_str = 'wafp-application-thank-you';
    if(!isset($this->application_thank_you)) {
      ob_start();

      ?><h2><?php esc_html_e('Thank you for your application', 'easy-affiliate'); ?></h2>
<?php
      ?><p><?php esc_html_e('We\'ll review your affiliate application and get back to you shortly if your application is approved.', 'easy-affiliate'); ?></p>
<?php

      $this->application_thank_you = ob_get_clean();
    }

    $this->pro_dashboard_enabled_str = 'wafp-pro-dashboard-enabled';
    if(!isset($this->pro_dashboard_enabled)) {
      $this->pro_dashboard_enabled = true;
    }

    $this->pro_dashboard_brand_color_str = 'wafp-pro-dashboard-brand-color';
    if(!isset($this->pro_dashboard_brand_color)) {
      $this->pro_dashboard_brand_color = '#32373b';
    }

    $this->pro_dashboard_accent_color_str = 'wafp-pro-dashboard-accent-color';
    if(!isset($this->pro_dashboard_accent_color)) {
      $this->pro_dashboard_accent_color = '#222629';
    }

    $this->pro_dashboard_menu_text_color_str = 'wafp-pro-dashboard-menu-text-color';
    if(!isset($this->pro_dashboard_menu_text_color)) {
      $this->pro_dashboard_menu_text_color = '#b7bcc0';
    }

    $this->pro_dashboard_menu_text_highlight_color_str = 'wafp-pro-dashboard-menu-text-highlight-color';
    if(!isset($this->pro_dashboard_menu_text_highlight_color)) {
      $this->pro_dashboard_menu_text_highlight_color = '#ffffff';
    }

    $this->pro_dashboard_logo_url_str = 'wafp-pro-dashboard-logo-url';
    if(!isset($this->pro_dashboard_logo_url)) {
      $this->pro_dashboard_logo_url = '';
    }

    $this->showcase_url_enabled_str = 'wafp-showcase-url-enabled';
    $this->showcase_url_href_str = 'wafp-showcase-url-href';
    $this->showcase_url_title_str = 'wafp-showcase-url-title';

    if(!isset($this->showcase_url_enabled)) {
      $this->showcase_url_enabled = false;
    }

    if(!isset($this->showcase_url_href))
      $this->showcase_url_href = '';
    if(!isset($this->showcase_url_title))
      $this->showcase_url_title = '';


    if(!isset($this->commission_type))
      $this->commission_type = 'percentage';

    if(!isset($this->commission))
      $this->commission = [0];
    else if(is_numeric($this->commission))
      $this->commission = [$this->commission];

    if(!isset($this->subscription_commissions)) {
      if(isset($this->recurring)) {
        $this->subscription_commissions = $this->recurring ? 'all' : 'first-only';
      }
      else {
        $this->subscription_commissions = 'first-only';
      }
    }

    $this->subscription_commissions_str = 'wafp-subscription-commissions';

    if(!isset($this->minimum))
      $this->minimum = '0.00';

    if(!isset($this->payout_waiting_period)) {
      $this->payout_waiting_period = 1;
    }

    $this->payout_waiting_period_str = 'wafp_payout_waiting_period';

    if(!isset($this->paypal_client_id))
      $this->paypal_client_id = '';

    if(!isset($this->paypal_secret_id))
      $this->paypal_secret_id = '';

    $this->mothership_license_str= 'wafp-mothership-license';
    $this->edge_updates_str      = 'wafp-edge-updates';

    $this->dashboard_page_id_str = 'wafp-dashboard-page-id';
    $this->signup_page_id_str    = 'wafp-signup-page-id';
    $this->login_page_id_str     = 'wafp-login-page-id';

    $this->commission_type_str   = 'wafp-commission-type';
    $this->commission_str        = 'wafp-commission';
    $this->paypal_client_id_str  = 'wafp-paypal-client-id';
    $this->paypal_secret_id_str  = 'wafp-paypal-secret-id';
    $this->minimum_str           = 'wafp_minimum';

    // Payment Settings
    if(!isset($this->payment_type))
      $this->payment_type = 'paypal';

    $this->payment_type_str = 'wafp-payment-type';

    //Dash CSS Settings
    if(!isset($this->dash_css_width))
      $this->dash_css_width = 500;
    $this->dash_css_width_str = 'wafp-dash-css-width';

    if(!isset($this->dash_nav))
      $this->dash_nav = [];
    $this->dash_nav_str = 'wafp-dash-nav';

    // Cookie Settings
    if(!isset($this->expire_after_days))
      $this->expire_after_days = 60;

    $this->expire_after_days_str = 'wafp-expire-after-days';

    // Notification Settings
    $this->welcome_email_str = 'wafp-welcome-email';
    $this->welcome_email_subject_str = 'wafp-welcome-email-subject';
    $this->welcome_email_body_str = 'wafp-welcome-email-body';
    $this->welcome_email_use_template_str = 'wafp-welcome-email-use-template';
    $this->admin_email_str = 'wafp-admin-email';
    $this->admin_email_subject_str = 'wafp-admin-email-subject';
    $this->admin_email_body_str = 'wafp-admin-email-body';
    $this->admin_email_use_template_str = 'wafp-admin-email-use-template';
    $this->affiliate_email_str = 'wafp-affiliate-email';
    $this->affiliate_email_subject_str = 'wafp-affiliate-email-subject';
    $this->affiliate_email_body_str = 'wafp-affiliate-email-body';
    $this->affiliate_email_use_template_str = 'wafp-affiliate-email-use-template';
    $this->admin_aff_applied_email_enabled_str = 'wafp-admin-aff-applied-email';
    $this->admin_aff_applied_email_subject_str = 'wafp-admin-aff-applied-email-subject';
    $this->admin_aff_applied_email_body_str = 'wafp-admin-aff-applied-email-body';
    $this->admin_aff_applied_email_use_template_str = 'wafp-admin-aff-applied-email-use-template';
    $this->aff_approved_email_enabled_str = 'wafp-aff-approved-email';
    $this->aff_approved_email_subject_str = 'wafp-aff-approved-email-subject';
    $this->aff_approved_email_body_str = 'wafp-aff-approved-email-body';
    $this->aff_approved_email_use_template_str = 'wafp-aff-approved-email-use-template';

    $this->admin_email_addresses_str = 'wafp-admin-email-addresses';
    $this->email_from_name_str = 'wafp-email-from-name';
    $this->email_from_address_str = 'wafp-email-from-address';

    if(!isset($this->admin_email_addresses)) {
      $this->admin_email_addresses = get_option('admin_email');
    }

    if(!isset($this->email_from_name)) {
      $this->email_from_name = Utils::blogname();
    }

    if(!isset($this->email_from_address)) {
      $this->email_from_address = get_option('admin_email');
    }

    $this->affiliate_agreement_enabled_str = 'wafp-affiliate-agreement-enabled';
    $this->affiliate_agreement_text_str = 'wafp-affiliate-agreement-text';

    if(!isset($this->custom_message))
      $this->custom_message = sprintf(__('Welcome to %s\'s Affiliate Program.', 'easy-affiliate'), Utils::blogname());
    $this->custom_message_str = 'wafp-custom-message';

    if(!isset($this->setup_complete))
      $this->setup_complete = ($this->dashboard_page_id)?0:1;

    $this->currency_code_str   = 'wafp_currency_code';
    $this->currency_symbol_str = 'wafp_currency_symbol';
    $this->currency_symbol_after_amount_str = 'wafp_currency_symbol_after_amount';
    $this->number_format_str   = 'wafp_number_format';

    if( !isset($this->currency_code))
      $this->currency_code = 'USD';
    if( !isset($this->currency_symbol))
      $this->currency_symbol = '$';

    if(!isset($this->currency_symbol_after_amount)) {
      $this->currency_symbol_after_amount = false;
    }

    if( !isset($this->number_format))
      $this->number_format = '#,###.##';

    if(!isset($this->integration))
      $this->integration = [];
    else
      $this->integration = is_array($this->integration) ? $this->integration : [$this->integration];

    $this->integration_str = 'wafp-integration-type';

    $this->paypal_src_str = 'wafp-paypal-ipn-source';
    if(!isset($this->paypal_src))
      $this->paypal_src = '';

    $this->paypal_dst_str = 'wafp-paypal-ipn-destination';
    if(!isset($this->paypal_dst))
      $this->paypal_dst = '';

    $this->paypal_sandbox_str = 'wafp-paypal-sandbox';
    if(!isset($this->paypal_sandbox))
      $this->paypal_sandbox = false;

    $this->paypal_emails_str = 'wafp-paypal-emails';
    if(!isset($this->paypal_emails))
      $this->paypal_emails = '';

    $this->woocommerce_integration_order_status_str = 'wafp-woocommerce-order-status';
    if(!isset($this->woocommerce_integration_order_status))
      $this->woocommerce_integration_order_status = 'completed';

    if(!isset($this->default_link_id))
      $this->default_link_id = 0;

    if(!isset($this->custom_default_redirect))
      $this->custom_default_redirect = false;

    if(!isset($this->custom_default_redirect_url))
      $this->custom_default_redirect_url = '';
  }

  /**
   * Sanitize the given options and return them
   *
   * @param  array $params
   * @return array
   */
  public function sanitize($params) {
    // General
    $params[$this->business_name_str] = isset($params[$this->business_name_str]) && is_string($params[$this->business_name_str]) ? sanitize_text_field($params[$this->business_name_str]) : '';
    $params[$this->business_address_one_str] = isset($params[$this->business_address_one_str]) && is_string($params[$this->business_address_one_str]) ? sanitize_text_field($params[$this->business_address_one_str]) : '';
    $params[$this->business_address_two_str] = isset($params[$this->business_address_two_str]) && is_string($params[$this->business_address_two_str]) ? sanitize_text_field($params[$this->business_address_two_str]) : '';
    $params[$this->business_address_city_str] = isset($params[$this->business_address_city_str]) && is_string($params[$this->business_address_city_str]) ? sanitize_text_field($params[$this->business_address_city_str]) : '';
    $params[$this->business_address_state_str] = isset($params[$this->business_address_state_str]) && is_string($params[$this->business_address_state_str]) ? sanitize_text_field($params[$this->business_address_state_str]) : '';
    $params[$this->business_address_zip_str] = isset($params[$this->business_address_zip_str]) && is_string($params[$this->business_address_zip_str]) ? sanitize_text_field($params[$this->business_address_zip_str]) : '';
    $params[$this->business_address_country_str] = isset($params[$this->business_address_country_str]) && is_string($params[$this->business_address_country_str]) ? sanitize_text_field($params[$this->business_address_country_str]) : '';

    if(isset($params[$this->business_tax_id_str])) {
      $params[$this->business_tax_id_str] = is_string($params[$this->business_tax_id_str]) ? sanitize_text_field($params[$this->business_tax_id_str]) : '';
    }
    else {
      $params[$this->business_tax_id_str] = $this->business_tax_id;
    }

    $params[$this->dashboard_page_id_str] = isset($params[$this->dashboard_page_id_str]) && is_string($params[$this->dashboard_page_id_str]) ? $this->sanitize_page_option($params[$this->dashboard_page_id_str]) : 0;
    $params[$this->signup_page_id_str] = isset($params[$this->signup_page_id_str]) && is_string($params[$this->signup_page_id_str]) ? $this->sanitize_page_option($params[$this->signup_page_id_str]) : 0;
    $params[$this->login_page_id_str] = isset($params[$this->login_page_id_str]) && is_string($params[$this->login_page_id_str]) ? $this->sanitize_page_option($params[$this->login_page_id_str]) : 0;

    // Commission
    $params[$this->commission_type_str] = isset($params[$this->commission_type_str]) && is_string($params[$this->commission_type_str]) && in_array($params[$this->commission_type_str], ['percentage', 'fixed']) ? $params[$this->commission_type_str] : 'percentage';
    $params[$this->commission_str] = isset($params[$this->commission_str]) && is_array($params[$this->commission_str]) ? $this->sanitize_commissions($params[$this->commission_str], $params[$this->commission_type_str]) : [];
    $params[$this->subscription_commissions_str] = isset($params[$this->subscription_commissions_str]) && is_string($params[$this->subscription_commissions_str]) && in_array($params[$this->subscription_commissions_str], ['first-only', 'all']) ? $params[$this->subscription_commissions_str] : 'first-only';
    $params[$this->minimum_str] = isset($params[$this->minimum_str . '-checkbox'], $params[$this->minimum_str]) && is_numeric($params[$this->minimum_str]) ? Utils::format_float($params[$this->minimum_str]) : '0.00';
    $params[$this->payout_waiting_period_str] = isset($params[$this->payout_waiting_period_str]) && is_numeric($params[$this->payout_waiting_period_str]) && $params[$this->payout_waiting_period_str] >= 0 ? (int) ($params[$this->payout_waiting_period_str]) : 1;
    $params[$this->paypal_client_id_str] = isset($params[$this->paypal_client_id_str]) && is_string($params[$this->paypal_client_id_str]) ? trim($params[$this->paypal_client_id_str]) : '';
    $params[$this->paypal_secret_id_str] = isset($params[$this->paypal_secret_id_str]) && is_string($params[$this->paypal_secret_id_str]) ? trim($params[$this->paypal_secret_id_str]) : '';

    // Dashboard
    $params[$this->custom_message_str] = isset($params[$this->custom_message_str]) && is_string($params[$this->custom_message_str]) ? wp_kses_post($params[$this->custom_message_str]) : '';
    $params[$this->dash_nav_str] = isset($params[$this->dash_nav_str]) && is_array($params[$this->dash_nav_str]) ? array_map('intval', $params[$this->dash_nav_str]) : [];

    // Affiliates
    $params[$this->payment_type_str] = isset($params[$this->payment_type_str]) && is_string($params[$this->payment_type_str]) && in_array($params[$this->payment_type_str], ['paypal', 'paypal-1-click', 'manual']) ? $params[$this->payment_type_str] : 'paypal';
    $params[$this->registration_type_str] = isset($params[$this->registration_type_str]) && is_string($params[$this->registration_type_str]) && in_array($params[$this->registration_type_str], ['public', 'application', 'private']) ? $params[$this->registration_type_str] : 'public';
    $params[$this->admin_aff_applied_email_enabled_str] = isset($params[$this->admin_aff_applied_email_enabled_str]);
    $params[$this->admin_aff_applied_email_subject_str] = isset($params[$this->admin_aff_applied_email_subject_str]) && is_string($params[$this->admin_aff_applied_email_subject_str]) ? sanitize_text_field($params[$this->admin_aff_applied_email_subject_str]) : '';
    $params[$this->admin_aff_applied_email_body_str] = isset($params[$this->admin_aff_applied_email_body_str]) && is_string($params[$this->admin_aff_applied_email_body_str]) ? $params[$this->admin_aff_applied_email_body_str] : '';
    $params[$this->admin_aff_applied_email_use_template_str] = isset($params[$this->admin_aff_applied_email_use_template_str]);
    $params[$this->aff_approved_email_enabled_str] = isset($params[$this->aff_approved_email_enabled_str]);
    $params[$this->aff_approved_email_subject_str] = isset($params[$this->aff_approved_email_subject_str]) && is_string($params[$this->aff_approved_email_subject_str]) ? sanitize_text_field($params[$this->aff_approved_email_subject_str]) : '';
    $params[$this->aff_approved_email_body_str] = isset($params[$this->aff_approved_email_body_str]) && is_string($params[$this->aff_approved_email_body_str]) ? $params[$this->aff_approved_email_body_str] : '';
    $params[$this->aff_approved_email_use_template_str] = isset($params[$this->aff_approved_email_use_template_str]);
    $params[$this->application_thank_you_str] = isset($params[$this->application_thank_you_str]) && is_string($params[$this->application_thank_you_str]) ? wp_kses_post($params[$this->application_thank_you_str]) : '';
    $params[$this->show_address_fields_str] = isset($params[$this->show_address_fields_str]);
    $params[$this->show_address_fields_account_str] = isset($params[$this->show_address_fields_account_str]);
    $params[$this->require_address_fields_str] = isset($params[$this->require_address_fields_str]);
    $params[$this->show_tax_id_fields_str] = isset($params[$this->show_tax_id_fields_str]);
    $params[$this->show_tax_id_fields_account_str] = isset($params[$this->show_tax_id_fields_account_str]);
    $params[$this->require_tax_id_fields_str] = isset($params[$this->require_tax_id_fields_str]);
    $params[$this->make_new_users_affiliates_str] = isset($params[$this->make_new_users_affiliates_str]);
    $params[$this->affiliate_agreement_enabled_str] = isset($params[$this->affiliate_agreement_enabled_str]);
    $params[$this->affiliate_agreement_text_str] = isset($params[$this->affiliate_agreement_text_str]) && is_string($params[$this->affiliate_agreement_text_str]) ? wp_kses_post($params[$this->affiliate_agreement_text_str]) : '';
    $params[$this->expire_after_days_str] = isset($params[$this->expire_after_days_str]) && is_numeric($params[$this->expire_after_days_str]) ? (int) $params[$this->expire_after_days_str] : 60;
    $params[$this->pretty_affiliate_links_str] = isset($params[$this->pretty_affiliate_links_str]);
    $params[$this->utm_affiliate_links_str] = isset($params[$this->utm_affiliate_links_str]);
    $params[$this->pro_dashboard_enabled_str] = isset($params[$this->pro_dashboard_enabled_str]);
    $params[$this->pro_dashboard_brand_color_str] = isset($params[$this->pro_dashboard_brand_color_str]) && is_string($params[$this->pro_dashboard_brand_color_str]) ? sanitize_hex_color($params[$this->pro_dashboard_brand_color_str]) : '#32373b';
    $params[$this->pro_dashboard_accent_color_str] = isset($params[$this->pro_dashboard_accent_color_str]) && is_string($params[$this->pro_dashboard_accent_color_str]) ? sanitize_hex_color($params[$this->pro_dashboard_accent_color_str]) : '#222629';
    $params[$this->pro_dashboard_menu_text_color_str] = isset($params[$this->pro_dashboard_menu_text_color_str]) && is_string($params[$this->pro_dashboard_menu_text_color_str]) ? sanitize_hex_color($params[$this->pro_dashboard_menu_text_color_str]) : '#b7bcc0';
    $params[$this->pro_dashboard_menu_text_highlight_color_str] = isset($params[$this->pro_dashboard_menu_text_highlight_color_str]) && is_string($params[$this->pro_dashboard_menu_text_highlight_color_str]) ? sanitize_hex_color($params[$this->pro_dashboard_menu_text_highlight_color_str]) : '#ffffff';
    $params[$this->pro_dashboard_logo_url_str] = isset($params[$this->pro_dashboard_logo_url_str]) && is_string($params[$this->pro_dashboard_logo_url_str]) ? esc_url_raw($params[$this->pro_dashboard_logo_url_str]) : '';
    $params[$this->showcase_url_enabled_str] = isset($params[$this->showcase_url_enabled_str]);
    $params[$this->showcase_url_href_str] = isset($params[$this->showcase_url_href_str]) && is_string($params[$this->showcase_url_href_str]) ? esc_url_raw($params[$this->showcase_url_href_str]) : '';
    $params[$this->showcase_url_title_str] = isset($params[$this->showcase_url_title_str]) && is_string($params[$this->showcase_url_title_str]) ? sanitize_text_field($params[$this->showcase_url_title_str]) : '';

    // Integrations
    $params[$this->integration_str] = isset($params[$this->integration_str]) && is_array($params[$this->integration_str]) ? array_map('sanitize_key', $params[$this->integration_str]) : [];
    $params[$this->paypal_emails_str] = isset($params[$this->paypal_emails_str]) && is_string($params[$this->paypal_emails_str]) ? sanitize_text_field($params[$this->paypal_emails_str]) : '';
    $params[$this->paypal_sandbox_str] = isset($params[$this->paypal_sandbox_str]);
    $params[$this->paypal_src_str] = isset($params[$this->paypal_src_str]) && is_string($params[$this->paypal_src_str]) ? sanitize_text_field($params[$this->paypal_src_str]) : '';
    $params[$this->paypal_dst_str] = isset($params[$this->paypal_dst_str]) && is_string($params[$this->paypal_dst_str]) ? Utils::sanitize_textarea_field($params[$this->paypal_dst_str]) : '';
    $params[$this->woocommerce_integration_order_status_str] = isset($params[$this->woocommerce_integration_order_status_str])
    && in_array($params[$this->woocommerce_integration_order_status_str], ['processing', 'completed']) ? $params[$this->woocommerce_integration_order_status_str] : 'completed';

    // Emails
    $params[$this->welcome_email_str] = isset($params[$this->welcome_email_str]);
    $params[$this->welcome_email_subject_str] = isset($params[$this->welcome_email_subject_str]) && is_string($params[$this->welcome_email_subject_str]) ? sanitize_text_field($params[$this->welcome_email_subject_str]) : '';
    $params[$this->welcome_email_body_str] = isset($params[$this->welcome_email_body_str]) && is_string($params[$this->welcome_email_body_str]) ? $params[$this->welcome_email_body_str] : '';
    $params[$this->welcome_email_use_template_str] = isset($params[$this->welcome_email_use_template_str]);
    $params[$this->affiliate_email_str] = isset($params[$this->affiliate_email_str]);
    $params[$this->affiliate_email_subject_str] = isset($params[$this->affiliate_email_subject_str]) && is_string($params[$this->affiliate_email_subject_str]) ? sanitize_text_field($params[$this->affiliate_email_subject_str]) : '';
    $params[$this->affiliate_email_body_str] = isset($params[$this->affiliate_email_body_str]) && is_string($params[$this->affiliate_email_body_str]) ? $params[$this->affiliate_email_body_str] : '';
    $params[$this->affiliate_email_use_template_str] = isset($params[$this->affiliate_email_use_template_str]);
    $params[$this->admin_email_str] = isset($params[$this->admin_email_str]);
    $params[$this->admin_email_subject_str] = isset($params[$this->admin_email_subject_str]) && is_string($params[$this->admin_email_subject_str]) ? sanitize_text_field($params[$this->admin_email_subject_str]) : '';
    $params[$this->admin_email_body_str] = isset($params[$this->admin_email_body_str]) && is_string($params[$this->admin_email_body_str]) ? $params[$this->admin_email_body_str] : '';
    $params[$this->admin_email_use_template_str] = isset($params[$this->admin_email_use_template_str]);
    $params[$this->admin_email_addresses_str] = isset($params[$this->admin_email_addresses_str]) && is_string($params[$this->admin_email_addresses_str]) ? $this->sanitize_emails($params[$this->admin_email_addresses_str]) : '';
    $params[$this->email_from_name_str] = isset($params[$this->email_from_name_str]) && is_string($params[$this->email_from_name_str]) ? sanitize_text_field($params[$this->email_from_name_str]) : '';
    $params[$this->email_from_address_str] = isset($params[$this->email_from_address_str]) && is_string($params[$this->email_from_address_str]) ? sanitize_email($params[$this->email_from_address_str]) : '';

    // I18n
    $params[$this->currency_code_str] = isset($params[$this->currency_code_str]) && is_string($params[$this->currency_code_str]) ? sanitize_text_field($params[$this->currency_code_str]) : '';
    $params[$this->currency_symbol_str] = isset($params[$this->currency_symbol_str]) && is_string($params[$this->currency_symbol_str]) ? sanitize_text_field($params[$this->currency_symbol_str]) : '';
    $params[$this->currency_symbol_after_amount_str] = isset($params[$this->currency_symbol_after_amount_str]);
    $params[$this->number_format_str] = isset($params[$this->number_format_str]) && is_string($params[$this->number_format_str]) ? sanitize_text_field($params[$this->number_format_str]) : '';

    // Misc
    $params[$this->edge_updates_str] = isset($params[$this->edge_updates_str]);

    return $params;
  }

  /**
   * Sanitize the value for the affiliate page options
   *
   * @param   string      $value
   * @return  int|string
   */
  protected function sanitize_page_option($value) {
    if (is_numeric($value)) {
      return (int) $value;
    }

    return $value == 'auto' ? 'auto' : 0;
  }

  /**
   * Sanitize the commission levels based on the given commission type
   *
   * @param array $commissions
   * @param string $type
   * @return array
   */
  public function sanitize_commissions(array $commissions, $type) {
    foreach($commissions as $key => $commission) {
      $commissions[$key] = is_numeric($commission) ? Utils::format_float($commission) : '0.00';

      if($commission < 0) {
        $commissions[$key] = '0.00';
      }
      elseif($type == 'percentage' && $commission > 100) {
        $commissions[$key] = '100.00';
      }
    }

    return $commissions;
  }

  /**
   * Sanitize a comma separated list of email addresses
   *
   * @param string $emails
   * @return string
   */
  protected function sanitize_emails($emails) {
    $emails = explode(',', $emails);
    $sanitized = [];

    foreach($emails as $email) {
      $email = trim(sanitize_email($email));

      if(is_email($email)) {
        $sanitized[] = $email;
      }
    }

    return join(', ', $sanitized);
  }

  public function validate($params, $errors) {
    if(empty($params[$this->business_name_str])) {
      $errors[] = __('Business Name is required.', 'easy-affiliate');
    }

    if(empty($params[$this->business_address_one_str])) {
      $errors[] = __('Business Address Line 1 is required.', 'easy-affiliate');
    }

    if(empty($params[$this->business_address_city_str])) {
      $errors[] = __('Business City is required.', 'easy-affiliate');
    }

    if(empty($params[$this->business_address_state_str])) {
      $errors[] = __('Business State is required.', 'easy-affiliate');
    }

    if(empty($params[$this->business_address_zip_str])) {
      $errors[] = __('Business Postcode is required.', 'easy-affiliate');
    }

    if(empty($params[$this->business_address_country_str])) {
      $errors[] = __('Business Country is required.', 'easy-affiliate');
    }

    if(empty($params[ $this->integration_str ])) {
      $errors[] = __('You must enable at least one eCommerce Payment Integration.', 'easy-affiliate');
    }

    if(empty($params[$this->commission_str])) {
      $errors[] = __('The Commission Amount must not be empty.', 'easy-affiliate');
    }

    foreach($params[$this->commission_str] as $index => $commish) {
      $level = $index + 1;
      if(!is_numeric($commish)) {
        $errors[] = sprintf(__('The commission amount in level %d must be a number.', 'easy-affiliate'), $level);
      }
      elseif(((int) $commish > 100 || (int) $commish < 0) && $params[$this->commission_type_str] == 'percentage') {
        $errors[] = sprintf(__('The commission amount in level %d is a percentage so it must be a number from 0 to 100.', 'easy-affiliate'), $level);
      }
    }

    if(!empty($params[$this->paypal_emails_str])) {
      $paypal_emails = explode(',', $params[$this->paypal_emails_str]);

      foreach($paypal_emails as $paypal_email) {
        if(!is_email(trim($paypal_email))) {
          $errors[] = __('One or more of your PayPal email addresses is not a valid email.', 'easy-affiliate');
          break;
        }
      }
    }

    // Validate urls in PayPal IPN urls
    if(!empty( $params[$this->paypal_src_str])) {
      $ip_pattern = "/^(([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]).){3}([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/";
      $ips = explode(',', $params[$this->paypal_src_str]);

      foreach($ips as $ip) {
        if(!preg_match($ip_pattern, trim($ip))) {
          $errors[] = __('One or more of the PayPal IPN source hosts is not a valid IP address.', 'easy-affiliate');
          break;
        }
      }
    }

    if(!empty( $params[$this->paypal_dst_str])) {
      $url_pattern = "/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i";
      $urls = explode("\n", $params[$this->paypal_dst_str]);

      foreach($urls as $url) {
        if(!preg_match($url_pattern, trim($url))) {
          $errors[] = __('One or more of the PayPal destination IPN URLs is not a valid URL.', 'easy-affiliate');
          break;
        }
      }
    }

    return $errors;
  }

  public function update($params) {
    //mothership_license and edge_updates are set in the update controller, don't do it here

    // General
    $this->business_name = $params[$this->business_name_str];
    $this->business_address_one = $params[$this->business_address_one_str];
    $this->business_address_two = $params[$this->business_address_two_str];
    $this->business_address_city = $params[$this->business_address_city_str];
    $this->business_address_state = $params[$this->business_address_state_str];
    $this->business_address_zip = $params[$this->business_address_zip_str];
    $this->business_address_country = $params[$this->business_address_country_str];
    $this->business_tax_id = $params[$this->business_tax_id_str];
    $this->dashboard_page_id = $params[$this->dashboard_page_id_str];
    $this->signup_page_id    = $params[$this->signup_page_id_str];
    $this->login_page_id     = $params[$this->login_page_id_str];

    // Commission
    $this->commission_type  = $params[$this->commission_type_str];
    $this->commission       = $params[$this->commission_str];
    $this->subscription_commissions = $params[$this->subscription_commissions_str];

    $this->minimum          = $params[$this->minimum_str];
    $this->paypal_client_id = $params[$this->paypal_client_id_str];
    $this->paypal_secret_id = $params[$this->paypal_secret_id_str];
    $this->payout_waiting_period = $params[$this->payout_waiting_period_str];

    // Dashboard
    $this->custom_message      = $params[$this->custom_message_str];
    $this->dash_nav            = $params[$this->dash_nav_str];

    // Affiliates
    $this->payment_type                      = $params[$this->payment_type_str];
    $this->registration_type                 = $params[$this->registration_type_str];
    $this->admin_aff_applied_email_enabled   = $params[$this->admin_aff_applied_email_enabled_str];
    $this->admin_aff_applied_email_subject   = $params[$this->admin_aff_applied_email_subject_str];
    $this->admin_aff_applied_email_body      = $params[$this->admin_aff_applied_email_body_str];
    $this->aff_approved_email_enabled        = $params[$this->aff_approved_email_enabled_str];
    $this->aff_approved_email_subject        = $params[$this->aff_approved_email_subject_str];
    $this->aff_approved_email_body           = $params[$this->aff_approved_email_body_str];
    $this->application_thank_you             = $params[$this->application_thank_you_str];
    $this->show_address_fields               = $params[$this->show_address_fields_str];
    $this->show_address_fields_account       = $params[$this->show_address_fields_account_str];
    $this->require_address_fields            = $params[$this->require_address_fields_str];
    $this->show_tax_id_fields                = $params[$this->show_tax_id_fields_str];
    $this->show_tax_id_fields_account        = $params[$this->show_tax_id_fields_account_str];
    $this->require_tax_id_fields             = $params[$this->require_tax_id_fields_str];
    $this->make_new_users_affiliates         = $params[$this->make_new_users_affiliates_str];
    $this->affiliate_agreement_enabled       = $params[$this->affiliate_agreement_enabled_str];
    $this->affiliate_agreement_text          = $params[$this->affiliate_agreement_text_str];
    $this->expire_after_days                 = $params[$this->expire_after_days_str];
    $this->pretty_affiliate_links            = $params[$this->pretty_affiliate_links_str];
    $this->utm_affiliate_links               = $params[$this->utm_affiliate_links_str];
    $this->pro_dashboard_enabled             = $params[$this->pro_dashboard_enabled_str];
    $this->pro_dashboard_brand_color         = $params[$this->pro_dashboard_brand_color_str];
    $this->pro_dashboard_accent_color        = $params[$this->pro_dashboard_accent_color_str];
    $this->pro_dashboard_menu_text_color     = $params[$this->pro_dashboard_menu_text_color_str];
    $this->pro_dashboard_menu_text_highlight_color = $params[$this->pro_dashboard_menu_text_highlight_color_str];
    $this->pro_dashboard_logo_url = $params[$this->pro_dashboard_logo_url_str];
    $this->showcase_url_enabled             = $params[$this->showcase_url_enabled_str];
    $this->showcase_url_href                = $params[$this->showcase_url_href_str];
    $this->showcase_url_title               = $params[$this->showcase_url_title_str];

    // Integrations
    $this->integration    = $params[$this->integration_str];
    $this->paypal_emails  = $params[$this->paypal_emails_str];
    $this->paypal_sandbox = $params[$this->paypal_sandbox_str];
    $this->paypal_src     = $params[$this->paypal_src_str];
    $this->paypal_dst     = $params[$this->paypal_dst_str];
    $this->woocommerce_integration_order_status     = $params[$this->woocommerce_integration_order_status_str];

    // Emails
    $this->welcome_email                = $params[$this->welcome_email_str];
    $this->welcome_email_use_template   = $params[$this->welcome_email_use_template_str];
    $this->welcome_email_subject        = $params[$this->welcome_email_subject_str];
    $this->welcome_email_body           = $params[$this->welcome_email_body_str];
    $this->affiliate_email              = $params[$this->affiliate_email_str];
    $this->affiliate_email_use_template = $params[$this->affiliate_email_use_template_str];
    $this->affiliate_email_subject      = $params[$this->affiliate_email_subject_str];
    $this->affiliate_email_body         = $params[$this->affiliate_email_body_str];
    $this->admin_email                  = $params[$this->admin_email_str];
    $this->admin_email_use_template     = $params[$this->admin_email_use_template_str];
    $this->admin_email_subject          = $params[$this->admin_email_subject_str];
    $this->admin_email_body             = $params[$this->admin_email_body_str];
    $this->admin_email_addresses        = $params[$this->admin_email_addresses_str];
    $this->email_from_name              = $params[$this->email_from_name_str];
    $this->email_from_address           = $params[$this->email_from_address_str];

    // I18n
    $this->currency_code   = $params[$this->currency_code_str];
    $this->currency_symbol = $params[$this->currency_symbol_str];
    $this->currency_symbol_after_amount = $params[$this->currency_symbol_after_amount_str];
    $this->number_format   = $params[$this->number_format_str];

    // Misc
    $this->edge_updates     = $params[$this->edge_updates_str];

    if($this->dashboard_page_id) {
      $this->setup_complete = true;
    }
  }

  public function store() {
    $storage_array = (array) $this;
    update_option( 'wafp_options', $storage_array );
  }

  public function auto_add_page($page_name) {
    $post_id = wp_insert_post(['post_title' => $page_name, 'post_type' => 'page', 'post_status' => 'publish', 'comment_status' => 'closed']);

    if(is_numeric($post_id) && $post_id > 0) {
      return $post_id;
    }

    return 0;
  }

  public function integration_lookup() {
    $config = require ESAF_CONFIG_PATH . '/integrations.php';

    $lookup = [];
    foreach($this->integration as $integration) {
      if(!isset($config[$integration])) { continue; }
      $lookup[$integration] = $config[$integration]['label'];
    }

    return $lookup;
  }

  /**
   * Get the default subject for the given email
   *
   * @param string $email
   * @return string
   */
  public function get_default_email_subject($email) {
    $subject = '';

    switch($email) {
      case 'welcome':
        $subject = __('Welcome to the Affiliate Program on {$site_name}!', 'easy-affiliate');
        break;
      case 'admin':
        $subject = __('** Affiliate Sale', 'easy-affiliate');
        break;
      case 'affiliate':
        $subject = __('** Affiliate Commission', 'easy-affiliate');
        break;
      case 'admin_aff_applied':
        $subject = __('** Someone Applied to Become an Affiliate', 'easy-affiliate');
        break;
      case 'aff_approved':
        $subject = __("** You've Been Approved!", 'easy-affiliate');
        break;
    }

    return $subject;
  }

  /**
   * Get the default body for the given email
   *
   * @param string $email
   * @return string
   */
  public function get_default_email_body($email) {
    $body = '';
    $path = ESAF_VIEWS_PATH . "/emails/$email.php";

    if(file_exists($path)) {
      ob_start();
      include $path;
      $body = ob_get_clean();
    }

    return $body;
  }

  /**
   * Get the integrations to display on the Integrations settings tab
   *
   * @return array
   */
  public function get_integrations() {
    return apply_filters('esaf_integrations', []);
  }

  /**
   * Returns true if the payout method is PayPal or PayPal 1 Click
   *
   * @return bool
   */
  public function is_payout_method_paypal() {
    return $this->payment_type == 'paypal' || $this->payment_type == 'paypal-1-click';
  }

  public function deactivate_license() {
    $this->mothership_license = '';
    $this->store();

    // Clear the license & add-ons cache
    delete_site_transient('esaf_addons');
    delete_site_transient('esaf_all_addons');
    delete_site_transient('wafp_license_info');
  }
}
