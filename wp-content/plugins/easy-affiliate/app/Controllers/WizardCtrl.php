<?php

namespace EasyAffiliate\Controllers;

use EasyAffiliate\Helpers\WizardHelper;
use EasyAffiliate\Lib\BaseCtrl;
use EasyAffiliate\Lib\Config;
use EasyAffiliate\Lib\Migrator\AffiliateWP;
use EasyAffiliate\Lib\Utils;
use EasyAffiliate\Models\Creative;
use EasyAffiliate\Models\Options;

class WizardCtrl extends BaseCtrl {
  public function load_hooks() {
    add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);
    add_action('admin_notices', [$this, 'remove_all_admin_notices'], 0);
    add_action('wp_ajax_esaf_verify_license_key', [$this, 'verify_license_key']);
    add_action('wp_ajax_esaf_migrate_affiliatewp', [$this, 'migrate_affiliatewp']);
    add_action('wp_ajax_esaf_migrate_affiliate_royale', [$this, 'migrate_affiliate_royale']);
    add_action('wp_ajax_esaf_wizard_save_ecommerce_setup', [$this, 'save_ecommerce_setup']);
    add_action('wp_ajax_esaf_wizard_save_business_information', [$this, 'save_business_information']);
    add_action('wp_ajax_esaf_wizard_save_affiliate_registration_information', [$this, 'save_affiliate_registration_information']);
    add_action('wp_ajax_esaf_wizard_save_commissions_payouts', [$this, 'save_commissions_payouts']);
    add_action('wp_ajax_esaf_wizard_add_creative', [$this, 'add_creative']);
  }

  public function admin_enqueue_scripts() {
    if(preg_match('/_page_easy-affiliate-wizard$/', Utils::get_current_screen_id())) {
      wp_enqueue_media();
      wp_enqueue_style('magnific-popup', ESAF_CSS_URL . '/magnific-popup.min.css', [], '1.1.0');
      wp_enqueue_style('esaf-wizard', ESAF_CSS_URL . '/admin-wizard.css', [], ESAF_VERSION);
      wp_enqueue_script('magnific-popup', ESAF_JS_URL . '/jquery.magnific-popup.min.js', ['jquery'], '1.1.0', true);
      wp_enqueue_script('esaf-wizard', ESAF_JS_URL . '/admin-wizard.js', ['jquery'], ESAF_VERSION);
      wp_localize_script('esaf-wizard', 'EsafWizardL10n', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'verify_license_key_nonce' => wp_create_nonce('esaf_verify_license_key'),
        'error_verifying_license_key' => __('An error occurred verifying the license key.', 'easy-affiliate'),
        'migrate_affiliatewp_nonce' => wp_create_nonce('esaf_migrate_affiliatewp'),
        'error_migrating_affiliatewp' => __('An error occurred during the AffiliateWP migration.', 'easy-affiliate'),
        'migrating_settings' => __('Migrating Settings', 'easy-affiliate'),
        'migration_complete' => __('Migration Complete', 'easy-affiliate'),
        'migration_leave_are_you_sure' => __('The migration has not yet completed, are you sure you want to leave this page?', 'easy-affiliate'),
        'migrate_affiliate_royale_nonce' => wp_create_nonce('esaf_migrate_affiliate_royale'),
        'error_migrating_affiliate_royale' => __('An error occurred during the Affiliate Royale migration.', 'easy-affiliate'),
        'save_ecommerce_setup_nonce' => wp_create_nonce('esaf_wizard_save_ecommerce_setup'),
        'error_saving_ecommerce_setup' => __('An error occurred saving the eCommerce Setup.', 'easy-affiliate'),
        'save_business_information_nonce' => wp_create_nonce('esaf_wizard_save_business_information'),
        'error_saving_business_information' => __('An error occurred saving the Business Information.', 'easy-affiliate'),
        'save_affiliate_registration_information_nonce' => wp_create_nonce('esaf_wizard_save_affiliate_registration_information'),
        'error_saving_affiliate_registration_information' => __('An error occurred saving the Affiliate Registration Information.', 'easy-affiliate'),
        'commission_must_be_number' => __('The commission amount in level %d must be a number.', 'easy-affiliate'),
        'commission_percentage_range' => __('The commission amount in level %d is a percentage so it must be a number from 0 to 100.', 'easy-affiliate'),
        'paypal_client_id_required' => __('The PayPal Client ID is required.', 'easy-affiliate'),
        'paypal_secret_key_required' => __('The PayPal Secret Key is required.', 'easy-affiliate'),
        'save_commissions_payouts_nonce' => wp_create_nonce('esaf_wizard_save_commissions_payouts'),
        'error_saving_commissions_payouts' => __('An error occurred saving the Commissions & Payouts.', 'easy-affiliate'),
        'choose_or_upload_banner' => __('Choose or Upload a Banner', 'easy-affiliate'),
        'use_this_image' => __('Use this image', 'easy-affiliate'),
        'a_banner_image_is_required' => __('A banner image is required', 'easy-affiliate'),
        'this_field_is_required' => __('This field is required', 'easy-affiliate'),
        'add_creative_nonce' => wp_create_nonce('esaf_wizard_add_creative'),
        'save_and_continue' => __('Save and Continue &rarr;', 'easy-affiliate')
      ]);
    }
  }

  public function remove_all_admin_notices() {
    if(preg_match('/_page_easy-affiliate-wizard$/', Utils::get_current_screen_id())) {
      remove_all_actions('admin_notices');
    }
  }

  public static function route() {
    $options = Options::fetch();
    $license = !empty($options->mothership_license) ? get_site_transient('wafp_license_info') : false;
    $steps = self::get_steps();
    $integrations = Config::get('integrations');

    if(!empty($license)) {
      // Skip the first step if the license is valid
      array_shift($steps);
    }

    require ESAF_VIEWS_PATH . '/admin/wizard.php';
  }

  private static function get_steps() {
    return apply_filters('esaf_wizard_steps', [
      ESAF_VIEWS_PATH . '/admin/wizard/license.php',
      ESAF_VIEWS_PATH . '/admin/wizard/migrate.php',
      ESAF_VIEWS_PATH . '/admin/wizard/ecommerce.php',
      ESAF_VIEWS_PATH . '/admin/wizard/business.php',
      ESAF_VIEWS_PATH . '/admin/wizard/affiliate-registration.php',
      ESAF_VIEWS_PATH . '/admin/wizard/commissions-payouts.php',
      ESAF_VIEWS_PATH . '/admin/wizard/creatives.php',
      ESAF_VIEWS_PATH . '/admin/wizard/finish.php'
    ]);
  }

  private function validate_request($nonce_action) {
    if(!Utils::is_post_request()) {
      wp_send_json_error(__('Bad request', 'easy-affiliate'));
    }

    if(!Utils::is_logged_in_and_an_admin()) {
      wp_send_json_error(__('Sorry, you don\'t have permission to do this.', 'easy-affiliate'));
    }

    if(!check_ajax_referer($nonce_action, false, false)) {
      wp_send_json_error(__('Security check failed.', 'easy-affiliate'));
    }
  }

  public function verify_license_key() {
    if(!Utils::is_post_request() || empty($_POST['license_key']) || !is_string($_POST['license_key'])) {
      wp_send_json_error(__('Bad request', 'easy-affiliate'));
    }

    if(!Utils::is_logged_in_and_an_admin()) {
      wp_send_json_error(__('Sorry, you don\'t have permission to do this.', 'easy-affiliate'));
    }

    if(!check_ajax_referer('esaf_verify_license_key', false, false)) {
      wp_send_json_error(__('Security check failed.', 'easy-affiliate'));
    }

    $options = Options::fetch();
    $options->mothership_license = sanitize_text_field(wp_unslash($_POST['license_key']));
    $domain = urlencode(Utils::site_domain());

    try {
      $args = compact('domain');
      UpdateCtrl::send_mothership_request("/license_keys/activate/{$options->mothership_license}", $args, 'post');
      $options->store();
      UpdateCtrl::manually_queue_update();
    }
    catch(\Exception $e) {
      wp_send_json_error($e->getMessage());
    }

    wp_send_json_success();
  }

  public function migrate_affiliatewp() {
    self::validate_request('esaf_migrate_affiliatewp');

    if(!WizardHelper::is_affiliatewp_detected()) {
      wp_send_json_error(__('AffiliateWP was not detected.', 'easy-affiliate'));
    }

    if(!isset($_POST['data']) || !is_string($_POST['data'])) {
      wp_send_json_error(__('Bad request', 'easy-affiliate'));
    }

    $data = json_decode(wp_unslash($_POST['data']), true);

    if(!is_array($data)) {
      wp_send_json_error(__('Bad request', 'easy-affiliate'));
    }

    AffiliateWP::migrate($data);
  }

  public function migrate_affiliate_royale() {
    self::validate_request('esaf_migrate_affiliate_royale');

    if(!WizardHelper::is_affiliate_royale_detected()) {
      wp_send_json_error(__('Affiliate Royale was not detected.', 'easy-affiliate'));
    }

    sleep(6);

    wp_send_json_success();
  }

  /**
   * Validates the request and returns the data array
   *
   * @param  string $nonce_action
   * @return array
   */
  private function get_request_data($nonce_action) {
    self::validate_request($nonce_action);

    if(!isset($_POST['data']) || !is_string($_POST['data'])) {
      wp_send_json_error(__('Bad request', 'easy-affiliate'));
    }

    $data = json_decode(wp_unslash($_POST['data']), true);

    if(!is_array($data)) {
      wp_send_json_error(__('Bad request', 'easy-affiliate'));
    }

    return $data;
  }

  public function save_ecommerce_setup() {
    $options = Options::fetch();
    $integrations = $options->integration;
    $data = self::get_request_data('esaf_wizard_save_ecommerce_setup');

    if(isset($data['integrations']) && is_array($data['integrations'])) {
      $posted_integrations = array_map('sanitize_key', $data['integrations']);

      foreach(['memberpress', 'woocommerce', 'easy_digital_downloads', 'wpforms', 'formidable'] as $integration) {
        $key = array_search($integration, $integrations);
        $enabled = in_array($integration, $posted_integrations);

        if($key === false && $enabled) {
          $integrations[] = $integration;
        }
        elseif($key !== false && !$enabled) {
          unset($integrations[$key]);
        }
      }

      $options->integration = array_values($integrations);
    }

    if(isset($data[$options->woocommerce_integration_order_status_str]) && is_string($data[$options->woocommerce_integration_order_status_str]) && in_array($data[$options->woocommerce_integration_order_status_str], ['processing', 'completed'])) {
      $options->woocommerce_integration_order_status = $data[$options->woocommerce_integration_order_status_str];
    }

    $options->store();

    update_option('esaf_flush_rewrite_rules', '1');

    wp_send_json_success();
  }

  public function save_business_information() {
    $options = Options::fetch();
    $data = self::get_request_data('esaf_wizard_save_business_information');

    $fields = [
      $options->business_name_str => 'business_name',
      $options->business_address_one_str => 'business_address_one',
      $options->business_address_two_str => 'business_address_two',
      $options->business_address_city_str => 'business_address_city',
      $options->business_address_state_str => 'business_address_state',
      $options->business_address_zip_str => 'business_address_zip',
      $options->business_address_country_str => 'business_address_country',
      $options->business_tax_id_str => 'business_tax_id'
    ];

    foreach($fields as $key => $option) {
      if(isset($data[$key]) && is_string($data[$key]) && $data[$key] !== '') {
        $options->$option = sanitize_text_field($data[$key]);
      }
    }

    $options->store();

    wp_send_json_success();
  }

  public function save_affiliate_registration_information() {
    $options = Options::fetch();
    $data = self::get_request_data('esaf_wizard_save_affiliate_registration_information');

    if(isset($data[$options->registration_type_str]) && is_string($data[$options->registration_type_str]) && in_array($data[$options->registration_type_str], ['application', 'public', 'private'])) {
      $options->registration_type = $data[$options->registration_type_str];
    }

    $toggles = [
      $options->show_address_fields_str => 'show_address_fields',
      $options->show_address_fields_account_str => 'show_address_fields_account',
      $options->require_address_fields_str => 'require_address_fields',
      $options->show_tax_id_fields_str => 'show_tax_id_fields',
      $options->show_tax_id_fields_account_str => 'show_tax_id_fields_account',
      $options->require_tax_id_fields_str => 'require_tax_id_fields',
      $options->affiliate_agreement_enabled_str => 'affiliate_agreement_enabled'
    ];

    foreach($toggles as $key => $option) {
      if(isset($data[$key]) && is_bool($data[$key])) {
        $options->{$option} = $data[$key];
      }
    }

    if(!empty($data[$options->affiliate_agreement_text_str]) && is_string($data[$options->affiliate_agreement_text_str])) {
      $options->affiliate_agreement_text = wp_kses_post($data[$options->affiliate_agreement_text_str]);
    }

    if(!$options->dashboard_page_id) {
      $options->dashboard_page_id = $options->auto_add_page(__('Affiliate Dashboard', 'easy-affiliate'));
    }

    if(!$options->signup_page_id) {
      $options->signup_page_id = $options->auto_add_page(__('Affiliate Signup', 'easy-affiliate'));
    }

    if(!$options->login_page_id) {
      $options->login_page_id = $options->auto_add_page(__('Affiliate Login', 'easy-affiliate'));
    }

    $options->store();

    wp_send_json_success();
  }

  public function save_commissions_payouts() {
    $options = Options::fetch();
    $data = self::get_request_data('esaf_wizard_save_commissions_payouts');

    if(isset($data[$options->commission_type_str]) && is_string($data[$options->commission_type_str]) && in_array($data[$options->commission_type_str], ['percentage', 'fixed'])) {
      $options->commission_type = $data[$options->commission_type_str];
    }

    if(isset($data[$options->commission_str]) && is_array($data[$options->commission_str]) && count($data[$options->commission_str])) {
      $options->commission = $options->sanitize_commissions($data[$options->commission_str], $options->commission_type);
    }

    if(isset($data[$options->subscription_commissions_str]) && is_string($data[$options->subscription_commissions_str]) && in_array($data[$options->subscription_commissions_str], ['first-only', 'all'])) {
      $options->subscription_commissions = $data[$options->subscription_commissions_str];
    }

    if(isset($data[$options->payment_type_str]) && is_string($data[$options->payment_type_str]) && in_array($data[$options->payment_type_str], ['paypal', 'paypal-1-click', 'manual'])) {
      $options->payment_type = $data[$options->payment_type_str];
    }

    if(!empty($data[$options->paypal_client_id_str]) && is_string($data[$options->paypal_client_id_str])) {
      $options->paypal_client_id = trim($data[$options->paypal_client_id_str]);
    }

    if(!empty($data[$options->paypal_secret_id_str]) && is_string($data[$options->paypal_secret_id_str])) {
      $options->paypal_secret_id = trim($data[$options->paypal_secret_id_str]);
    }

    $options->store();

    wp_send_json_success();
  }

  public function add_creative() {
    $data = self::get_request_data('esaf_wizard_add_creative');

    if(empty($data['name']) || empty($data['url']) || empty($data['type'])) {
      wp_send_json_error(__('Bad request', 'easy-affiliate'));
    }

    $values = [
      '_wafp_creative_url' => isset($data['url']) && is_string($data['url']) ? sanitize_text_field($data['url']) : '',
      '_wafp_creative_link_type' => isset($data['type']) && is_string($data['type']) && in_array($data['type'], ['banner', 'text']) ? $data['type'] : 'banner'
    ];

    if($values['_wafp_creative_link_type'] == 'banner') {
      if(isset($data['image']) && is_array($data['image'])) {
        $values = array_merge($values, [
          '_wafp_creative_image' => isset($data['image']['url']) && is_string($data['image']['url']) ? sanitize_text_field($data['image']['url']) : '',
          '_wafp_creative_image_alt' => isset($data['image']['alt']) && is_string($data['image']['alt']) ? sanitize_text_field($data['image']['alt']) : '',
          '_wafp_creative_image_title' => isset($data['image']['title']) && is_string($data['image']['title']) ? sanitize_text_field($data['image']['title']) : '',
          '_wafp_creative_image_width' => isset($data['image']['width']) && is_numeric($data['image']['width']) ? max(0, (int) $data['image']['width']) : 0,
          '_wafp_creative_image_height' => isset($data['image']['height']) && is_numeric($data['image']['height']) ? max(0, (int) $data['image']['height']) : 0
        ]);
      }
    }
    else {
      $values = array_merge($values, [
        '_wafp_creative_link_text' => isset($data['text']) && is_string($data['text']) ? sanitize_text_field($data['text']) : ''
      ]);
    }

    $creative = new Creative();
    $creative->post_title = isset($data['name']) && is_string($data['name']) ? sanitize_text_field($data['name']) : '';
    $creative->load_from_sanitized_array($values);
    $result = $creative->store();

    if(is_wp_error($result)) {
      wp_send_json_error($result->get_error_message());
    }

    $row = '<tr>';
    $row .= sprintf('<td>%s</td>', esc_html($creative->post_title));
    $row .= sprintf('<td>%s</td>', esc_url($creative->url));
    $row .= sprintf('<td>%s</td>', $creative->link_code(get_current_user_id(), '_blank'));
    $row .= '</tr>';

    wp_send_json_success(compact('row'));
  }
}
