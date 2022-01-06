<?php

namespace EasyAffiliate\Controllers;

use EasyAffiliate\Helpers\OptionsHelper;
use EasyAffiliate\Lib\BaseCtrl;
use EasyAffiliate\Lib\Utils;
use EasyAffiliate\Models\Options;

class OptionsCtrl extends BaseCtrl {
  public function load_hooks() {
    add_action('wp_ajax_esaf_set_email_defaults', [self::class, 'set_email_defaults']);
    add_action('wp_ajax_esaf_send_test_email', [self::class, 'send_test_email']);
    add_action('wp_ajax_esaf_options_activate_license', [self::class, 'activate_license']);
    add_action('wp_ajax_esaf_options_deactivate_license', [self::class, 'deactivate_license']);

    if(isset($_REQUEST['page']) && $_REQUEST['page'] == 'easy-affiliate-settings') {
      add_action('admin_enqueue_scripts', [self::class, 'admin_enqueue_scripts']);
      add_action('admin_notices', [self::class, 'remove_all_admin_notices'], 0);
    }
  }

  public static function set_email_defaults() {
    if(!isset($_POST['e']) || !is_string($_POST['e'])) {
      wp_send_json_error(__('Bad request.', 'easy-affiliate'));
    }

    if(!check_ajax_referer('esaf_set_email_defaults', false, false)) {
      wp_send_json_error(__('Security check failed.', 'easy-affiliate'));
    }

    if(!Utils::is_wafp_admin()) {
      wp_send_json_error(__('Sorry, you don\'t have permission to do this.', 'easy-affiliate'));
    }

    $options = Options::fetch();
    $email = sanitize_key($_POST['e']);

    wp_send_json_success([
      'subject' => $options->get_default_email_subject($email),
      'body' => $options->get_default_email_body($email)
    ]);
  }

  public static function send_test_email() {
    if(!isset($_POST['s'], $_POST['b']) || !is_string($_POST['s']) || !is_string($_POST['b'])) {
      wp_send_json_error(__('Bad request.', 'easy-affiliate'));
    }

    if(!check_ajax_referer('esaf_send_test_email', false, false)) {
      wp_send_json_error(__('Security check failed.', 'easy-affiliate'));
    }

    if(!Utils::is_wafp_admin()) {
      wp_send_json_error(__('Sorry, you don\'t have permission to do this.', 'easy-affiliate'));
    }

    $subject = sanitize_text_field(wp_unslash($_POST['s']));
    $body = wp_unslash($_POST['b']);
    $variables = OptionsHelper::get_test_email_vars();
    $use_template = isset($_POST['t']) && $_POST['t'] == 'true';

    Utils::send_admin_email_notification($subject, $body, $variables, $use_template);

    wp_send_json_success(__('Your test email was successfully sent.', 'easy-affiliate'));
  }

  public static function activate_license() {
    if(!Utils::is_post_request() || empty($_POST['license_key']) || !is_string($_POST['license_key'])) {
      wp_send_json_error(__('Bad request', 'easy-affiliate'));
    }

    if(!Utils::is_logged_in_and_an_admin()) {
      wp_send_json_error(__('Sorry, you don\'t have permission to do this.', 'easy-affiliate'));
    }

    if(!check_ajax_referer('esaf_activate_license', false, false)) {
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

      // Clear the add-ons cache
      delete_site_transient('esaf_addons');
      delete_site_transient('esaf_all_addons');

      $output = sprintf('<div class="notice notice-success"><p>%s</p></div>', esc_html__('The license was successfully activated.', 'easy-affiliate'));
      $license = get_site_transient('wafp_license_info');

      if(is_array($license)) {
        $output .= OptionsHelper::get_active_license_html($license);
      }
      else {
        $output .= sprintf('<div class="notice notice-warning"><p>%s</p></div>', esc_html__('The license information is not available, try refreshing the page.', 'easy-affiliate'));
      }

      wp_send_json_success($output);
    }
    catch(\Exception $e) {
      wp_send_json_error($e->getMessage());
    }
  }

  public static function deactivate_license() {
    if(!Utils::is_post_request()) {
      wp_send_json_error(__('Bad request', 'easy-affiliate'));
    }

    if(!Utils::is_logged_in_and_an_admin()) {
      wp_send_json_error(__('Sorry, you don\'t have permission to do this.', 'easy-affiliate'));
    }

    if(!check_ajax_referer('esaf_deactivate_license', false, false)) {
      wp_send_json_error(__('Security check failed.', 'easy-affiliate'));
    }

    $options = Options::fetch();

    try {
      $domain = urlencode(Utils::site_domain());
      $args = compact('domain');
      UpdateCtrl::send_mothership_request("/license_keys/deactivate/{$options->mothership_license}", $args, 'post');

      $options->deactivate_license();
      UpdateCtrl::manually_queue_update();

      $output = sprintf('<div class="notice notice-success"><p>%s</p></div>', esc_html__('The license was successfully deactivated.', 'easy-affiliate'));
      $output .= OptionsHelper::get_license_key_field_html();

      wp_send_json_success($output);
    }
    catch(\Exception $e) {
      $options->deactivate_license();

      wp_send_json_error($e->getMessage());
    }
  }

  /**
   * Remove admin notices from the settings page, so they don't interfere with the layout
   */
  public static function remove_all_admin_notices() {
    remove_all_actions('admin_notices');
  }

  public static function admin_enqueue_scripts() {
    wp_enqueue_media();
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_style('esaf-options', ESAF_CSS_URL . '/admin-options.css', [], ESAF_VERSION);

    wp_enqueue_script('wp-color-picker');
    wp_enqueue_script('esaf-options', ESAF_JS_URL . '/admin-options.js', ['jquery', 'magnific-popup'], ESAF_VERSION);

    wp_localize_script('esaf-options', 'EsafOptionsL10n', [
      'ajax_url' => admin_url('admin-ajax.php'),
      'pages' => self::get_pages(),
      'set_email_defaults_nonce' => wp_create_nonce('esaf_set_email_defaults'),
      'send_test_email_nonce' => wp_create_nonce('esaf_send_test_email'),
      'activate_license_nonce' => wp_create_nonce('esaf_activate_license'),
      'error_activating_license' => __('An error occurred activating the license.', 'easy-affiliate'),
      'deactivate_license_are_you_sure' => sprintf(__('Are you sure? Automatic updates of Easy Affiliate will not be available on %s if this License Key is deactivated.', 'easy-affiliate'), Utils::site_domain()),
      'deactivate_license_nonce' => wp_create_nonce('esaf_deactivate_license'),
      'error_deactivating_license' => __('An error occurred deactivating license.', 'easy-affiliate'),
      'choose_or_upload_an_image' => __('Choose or Upload an Image', 'easy-affiliate'),
      'use_this_image' => __('Use this image', 'easy-affiliate')
    ]);
  }

  private static function get_pages() {
    $pages = [];

    foreach (Utils::get_pages() as $page) {
      $pages[] = ['ID' => $page->ID, 'title' => $page->post_title];
    }

    return $pages;
  }

  public static function route()  {
    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : null;

    if($action == 'process-form') {
      return self::process_form();
    }
    else {
      return self::display_form();
    }
  }

  public static function display_form() {
    $options = Options::fetch();

    if(Utils::is_logged_in_and_an_admin()) {
      if(!empty($options->mothership_license)) {
        $license = get_site_transient('wafp_license_info');

        if($license === false) {
          UpdateCtrl::manually_queue_update();
          $license = get_site_transient('wafp_license_info');
        }
      }

      require ESAF_VIEWS_PATH . '/options/form.php';
    }
  }

  public static function process_form() {
    $options = Options::fetch();

    if(Utils::is_logged_in_and_an_admin()) {
      $params = $options->sanitize(wp_unslash($_POST));
      $errors = apply_filters('esaf_validate_options', $options->validate($params, []));
      $old_edge_updates = $options->edge_updates;
      $options->update($params);

      if(!count($errors)) {
        if($options->dashboard_page_id == 'auto') {
          $options->dashboard_page_id = $options->auto_add_page(__('Affiliate Dashboard', 'easy-affiliate'));
        }

        if($options->signup_page_id == 'auto') {
          $options->signup_page_id = $options->auto_add_page(__('Affiliate Signup', 'easy-affiliate'));
        }

        if($options->login_page_id == 'auto') {
          $options->login_page_id = $options->auto_add_page(__('Affiliate Login', 'easy-affiliate'));
        }

        do_action('esaf_process_options');
        $options->store();
        $settings_saved = true;
        update_option('esaf_flush_rewrite_rules', '1');

        if($old_edge_updates !== $options->edge_updates) {
          UpdateCtrl::manually_queue_update();
        }
      }

      if(!empty($options->mothership_license)) {
        $license = get_site_transient('wafp_license_info');

        if($license === false) {
          UpdateCtrl::manually_queue_update();
          $license = get_site_transient('wafp_license_info');
        }
      }

      require ESAF_VIEWS_PATH . '/options/form.php';
    }
  }
}
