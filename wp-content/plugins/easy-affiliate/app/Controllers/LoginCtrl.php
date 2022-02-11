<?php

namespace EasyAffiliate\Controllers;

use EasyAffiliate\Lib\BaseCtrl;
use EasyAffiliate\Lib\Utils;
use EasyAffiliate\Models\Options;
use EasyAffiliate\Models\User;

class LoginCtrl extends BaseCtrl {
  public function load_hooks() {
    // Nothing yet
  }

  public function route() {
    $action = AppCtrl::get_param('action');

    if($action && $action == 'forgot_password') {
      $this->display_forgot_password_form();
    }
    else if($action && $action == 'wafp_process_forgot_password') {
      $this->process_forgot_password_form();
    }
    else if($action && $action == 'reset_password') {
      if(Utils::is_post_request() && AppCtrl::get_param('wafp_process_reset_password_form') == 'Y') {
        $this->process_reset_password_form();
      }
      else {
        $this->display_reset_password_form(AppCtrl::get_param('mkey'),urldecode(AppCtrl::get_param('u')));
      }
    }
    else {
      $this->display_login_form();
    }
  }

  public function display_forgot_password_form() {
    $process = AppCtrl::get_param('wafp_process_forgot_password_form');

    if(empty($process)) {
      require ESAF_VIEWS_PATH . '/login/forgot_password.php';
    }
    else {
      $this->process_forgot_password_form();
    }
  }

  public function process_forgot_password_form() {
    $errors = User::validate_forgot_password($_POST, []);
    $errors = apply_filters('esaf-validate-forgot-password', $errors);

    if(empty($errors)) {
      $wafp_user_or_email = isset($_POST['wafp_user_or_email']) && is_string($_POST['wafp_user_or_email']) ? sanitize_user(wp_unslash($_POST['wafp_user_or_email'])) : '';

      $is_email = (is_email($wafp_user_or_email) and email_exists($wafp_user_or_email));

      $is_username = username_exists($wafp_user_or_email);

      $user = new User();

      // If the username & email are identical then let's rely on it as a username first and foremost
      if($is_username) {
        $user->load_user_data_by_login( $wafp_user_or_email );
      }
      else if($is_email) {
        $user->load_user_data_by_email( $wafp_user_or_email );
      }

      if($user->ID) {
        $user->send_reset_password_requested_notification();

        require ESAF_VIEWS_PATH . '/login/forgot_password_requested.php';
      }
      else {
        require ESAF_VIEWS_PATH . '/shared/unknown_error.php';
      }
    }
    else {
      require ESAF_VIEWS_PATH . '/shared/frontend-errors.php';
      require ESAF_VIEWS_PATH . '/login/forgot_password.php';
    }
  }

  public function display_reset_password_form($wafp_key, $wafp_screenname) {
    $user = new User();
    $user->load_user_data_by_login($wafp_screenname);

    $loginURL = Utils::login_url();

    if($user->ID) {
      if($user->reset_form_key_is_valid($wafp_key)) {
        require ESAF_VIEWS_PATH . '/login/reset_password.php';
      }
      else {
        require ESAF_VIEWS_PATH . '/shared/unauthorized.php';
      }
    }
    else {
      require ESAF_VIEWS_PATH . '/shared/unauthorized.php';
    }
  }

  public function process_reset_password_form() {
    $values = $this->sanitize_reset_password_form_data(wp_unslash($_POST));
    $errors = User::validate_reset_password($values);
    $errors = apply_filters('esaf-validate-reset-password', $errors, $values);

    if(empty($errors)) {
      $user = new User();
      $user->load_user_data_by_login( $values['wafp_screenname'] );

      if($user->ID) {
        $user->set_password_and_send_notifications($values['wafp_key'], $values['wafp_user_password']);

        require ESAF_VIEWS_PATH . '/login/reset_password_thankyou.php';
      }
      else {
        require ESAF_VIEWS_PATH . '/shared/unknown_error.php';
      }
    }
    else {
      require ESAF_VIEWS_PATH . '/shared/frontend-errors.php';
      require ESAF_VIEWS_PATH . '/login/reset_password.php';
    }
  }

  protected function sanitize_reset_password_form_data($values) {
    $values['wafp_user_password'] = isset($values['wafp_user_password']) && is_string($values['wafp_user_password']) ? $values['wafp_user_password'] : '';
    $values['wafp_user_password_confirm'] = isset($values['wafp_user_password_confirm']) && is_string($values['wafp_user_password_confirm']) ? $values['wafp_user_password_confirm'] : '';
    $values['wafp_screenname'] = isset($values['wafp_screenname']) && is_string($values['wafp_screenname']) ? sanitize_user($values['wafp_screenname']) : '';
    $values['wafp_key'] = isset($values['wafp_key']) && is_string($values['wafp_key']) ? sanitize_key($values['wafp_key']) : '';

    return $values;
  }

  public function display_login_form() {
    $options = Options::fetch();

    $redirect_to = !empty($_REQUEST['redirect_to']) && is_string($_REQUEST['redirect_to']) ? sanitize_text_field(wp_unslash($_REQUEST['redirect_to'])) : Utils::dashboard_url();
    $redirect_to = apply_filters('esaf_login_redirect_url', $redirect_to);

    if($options->login_page_id > 0) {
      $login_url = Utils::login_url();
      $forgot_password_url = add_query_arg(['action' => 'forgot_password'], $login_url);
    }
    else {
      $login_url = wp_login_url();
      $forgot_password_url = add_query_arg(['action' => 'lostpassword'], $login_url);
    }

    $signup_url = Utils::signup_url();

    if(Utils::is_user_logged_in()) {
      require ESAF_VIEWS_PATH . '/shared/already_logged_in.php';
    }
    else {
      if(!empty($_POST['wafp_process_login_form']) && !empty($_POST['errors']) && is_array($_POST['errors'])) {
        $errors = array_map('sanitize_text_field', $_POST['errors']);
        require ESAF_VIEWS_PATH . '/shared/frontend-errors.php';
      }

      require ESAF_VIEWS_PATH . '/shared/login_form.php';
    }
  }

  public function process_login_form() {
    $errors = User::validate_login($_POST, []);
    $errors = apply_filters('esaf-validate-login', $errors);

    if(empty($errors)) {
      $creds = [];
      $creds['user_login'] = isset($_POST['log']) && is_string($_POST['log']) ? $_POST['log'] : '';     // Expected slashed/unsanitized
      $creds['user_password'] = isset($_POST['pwd']) && is_string($_POST['pwd']) ? $_POST['pwd'] : '';  // Expected slashed/unsanitized
      $creds['remember'] = isset($_POST['rememberme']);

      $user = wp_signon($creds);

      if(!$user instanceof \WP_User) {
        $errors[] = __('Login failed. Please double check your username and password.', 'easy-affiliate');
        $_POST['errors'] = $errors;
        return;
      }

      $redirect_to = !empty($_POST['redirect_to']) && is_string($_POST['redirect_to']) ? sanitize_text_field(wp_unslash($_POST['redirect_to'])) : Utils::dashboard_url();

      Utils::wp_redirect(esc_url_raw($redirect_to));
      exit;
    }
    else {
      $_POST['errors'] = $errors;
    }
  }
}
