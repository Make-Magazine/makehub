<?php

namespace EasyAffiliate\Controllers;

use EasyAffiliate\Lib\BaseCtrl;
use EasyAffiliate\Lib\Utils;

class GoogleCaptchaCtrl extends BaseCtrl {
  public function load_hooks() {
    if(!function_exists('gglcptch_is_recaptcha_required')) {
      return;
    }

    add_filter('gglcptch_add_custom_form', [$this, 'add_options']);
    add_filter('esaf-validate-affiliate-application', [$this, 'remove_authenticate_action']);
    add_filter('esaf-validate-signup', [$this, 'remove_authenticate_action']);
    add_filter('esaf-validate-login', [$this, 'remove_authenticate_action']);
    add_filter('esaf-validate-forgot-password', [$this, 'remove_allow_password_reset_action']);
    add_filter('esaf-validate-reset-password', [$this, 'remove_authenticate_action']);
    add_action('esaf_pro_dashboard_footer', [$this, 'add_scripts']);

    if(gglcptch_is_recaptcha_required('easy_affiliate_application')) {
      add_action('esaf-affiliate-application-before-submit', [$this, 'add_recaptcha']);
      add_filter('esaf-validate-affiliate-application', [$this, 'verify_recaptcha']);
    }

    if(gglcptch_is_recaptcha_required('easy_affiliate_signup')) {
      add_action('esaf-user-signup-fields', [$this, 'add_recaptcha']);
      add_filter('esaf-validate-signup', [$this, 'verify_recaptcha']);
    }

    if(gglcptch_is_recaptcha_required('easy_affiliate_login')) {
      add_action('esaf-login-form-before-submit', [$this, 'add_recaptcha']);
      add_filter('esaf-validate-login', [$this, 'verify_recaptcha']);
    }

    if(gglcptch_is_recaptcha_required('easy_affiliate_forgot_password')) {
      add_action('esaf-forgot-password-form-before-submit', [$this, 'add_recaptcha']);
      add_filter('esaf-validate-forgot-password', [$this, 'verify_recaptcha']);
    }
  }

  public function add_options($forms) {
    $forms['easy_affiliate_application'] = ['form_name' => __('Easy Affiliate application form', 'easy-affiliate')];
    $forms['easy_affiliate_signup'] = ['form_name' => __('Easy Affiliate signup form', 'easy-affiliate')];
    $forms['easy_affiliate_login'] = ['form_name' => __('Easy Affiliate login form', 'easy-affiliate')];
    $forms['easy_affiliate_forgot_password'] = ['form_name' => __('Easy Affiliate forgot password form', 'easy-affiliate')];

    return $forms;
  }

  public function add_recaptcha() {
    ?>
    <div class="esaf-form-row esaf-google-captcha">
      <?php echo do_shortcode('[bws_google_captcha]'); ?>
    </div>
    <?php
  }

  public function verify_recaptcha($errors) {
    $is_valid = apply_filters('gglcptch_verify_recaptcha', true);

    if(!$is_valid) {
      $errors[] = __('Captcha verification failed', 'easy-affiliate');
    }

    return $errors;
  }

  public function remove_authenticate_action($errors) {
    // We need to remove this action or the reCAPTCHA is checked twice
    remove_action('authenticate', 'gglcptch_login_check', 21);

    return $errors;
  }

  public function remove_allow_password_reset_action($errors) {
    // We need to remove this action or the reCAPTCHA is checked twice
    remove_action('allow_password_reset', 'gglcptch_lostpassword_check');

    return $errors;
  }

  public function add_scripts() {
    if(Utils::is_pro_dashboard_page() && function_exists('gglcptch_add_scripts')) {
      gglcptch_add_scripts();
      wp_print_scripts('gglcptch_script');
    }
  }
}
