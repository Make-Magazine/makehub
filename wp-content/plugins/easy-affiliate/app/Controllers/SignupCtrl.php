<?php

namespace EasyAffiliate\Controllers;

use EasyAffiliate\Helpers\AffiliateApplicationHelper;
use EasyAffiliate\Lib\BaseCtrl;
use EasyAffiliate\Lib\Cookie;
use EasyAffiliate\Lib\Utils;
use EasyAffiliate\Models\AffiliateApplication;
use EasyAffiliate\Models\Options;
use EasyAffiliate\Models\User;

class SignupCtrl extends BaseCtrl {
  public function load_hooks() {
    // Nothing yet
  }

  public function route() {
    $options = Options::fetch();
    $app = false;

    $current_user = Utils::get_currentuserinfo();

    // If the current user is already an affiliate then redirect to the dashboard
    if(!!$current_user && $current_user->is_affiliate) {
      ?>
        <script>
          window.location = "<?php echo esc_url_raw(Utils::dashboard_url()); ?>";
        </script>
      <?php
    }
    // If there's an application parameter, the user isn't logged in but the application
    // is already associated with an affiliate then redirect to the login with a
    // redirect_to back to the signup page after the user is logged in
    else if( $options->registration_type=='application' &&
             !!($app = AffiliateApplicationHelper::get_application_from_request()) &&
             !$current_user && $app->affiliate > 0 ) {
      ?>
        <script>
          window.location = "<?php echo esc_url_raw(Utils::login_url(['redirect_to' => urlencode($app->signup_url())])); ?>";
        </script>
      <?php
    }
    else if( $options->registration_type=='public' ||
             // Check to see if the user is logged in, has a 'ready'
             // application then we're going to show them the signup form
             ( $options->registration_type=='application' && !!$current_user &&
               !!($app = AffiliateApplication::get_one_by_affiliate($current_user->ID)) &&
               $app->ready() ) ||
             // Check to see that the application param is for
             // the correct applicant, if it is, show the signup form
             ( $options->registration_type=='application' &&
               !!($app = AffiliateApplicationHelper::get_application_from_request()) &&
               $app->ready() && (!$current_user || $current_user->ID == $app->affiliate) ) ) {
      if(Utils::is_post_request()) {
        $this->process_form($app);
      }
      else {
        $this->display_form($app);
      }
    }
    else if( $options->registration_type=='application' ) {
      if( !!$current_user &&
          !!($app = AffiliateApplication::get_one_by_affiliate($current_user->ID)) ) {
        if($app->status=='ignored' || $app->status=='pending') {
          esc_html_e('Your application is still pending', 'easy-affiliate');
        }
        else {
          esc_html_e('An unknown error occurred with your application', 'easy-affiliate');
        }
      }
      else {
        $app_ctrl = new AffiliateApplicationCtrl();
        $app_ctrl->route();
      }
    }
    else if( $options->registration_type=='private' ) {
      printf(
        '<p>%s</p>',
        esc_html__('Sorry, affiliate registration is private.', 'easy-affiliate')
      );
    }
  }

  public function display_form($app = false, $message = '', $errors = [], $values = []) {
    $wafp_blogurl = Utils::blogurl();
    $options = Options::fetch();

    $redirect_to = apply_filters('esaf_login_redirect_url', Utils::dashboard_url());

    $user = Utils::get_currentuserinfo();
    $logged_in = !!$user;

    if(!$user) {
      $user = new User();
    }

    if(!empty($app) && $app instanceof AffiliateApplication) {
      $user->first_name = $app->first_name;
      $user->last_name = $app->last_name;
      $user->email = $app->email;
    }

    if (Utils::is_post_request() && count($values)) {
      $user->load_from_sanitized_array($values);
    }

    $app = false;
    if( $options->registration_type=='application' ) {
      $app = AffiliateApplicationHelper::get_application_from_request();
    }

    if($logged_in && $user->is_affiliate) {
      require ESAF_VIEWS_PATH . '/shared/already_logged_in.php';
    }
    else {
      require ESAF_VIEWS_PATH . '/signup/form.php';
    }
  }

  public function process_form($app=false) {
    $options = Options::fetch();
    $current_user = Utils::get_currentuserinfo();
    $logged_in = !!$current_user;

    // Yeah, sometimes this method get's loaded multiple times (depending on the theme).
    // So these are static to not get tripped up by this
    static $values, $errors, $user, $has_run; //$has_run is to prevent duplicate notifications

    if (!isset($values)) {
      $values = $this->sanitize_form_data(wp_unslash($_POST));
    }

    if(!isset($errors)) {
      $errors = User::validate_signup($values);
      $errors = apply_filters('esaf-validate-signup', $errors, $values);
    }

    if(empty($errors)) {
      if(!isset($user)) {
        $user = Utils::get_currentuserinfo();

        if(!$user) {
          $user = new User();
        }

        $user->load_from_sanitized_array($values);
        $user->is_affiliate = true;

        // Makin' it happen...
        $user->store();

        if( $options->registration_type=='application' &&
            !!($app = AffiliateApplicationHelper::get_application_from_request()) &&
            $app->ready() ) {
          // Update the application if it was set
          $app->affiliate = $user->ID;
          $app->store();
        }
      }

      if($user->ID) {
        // Yeah, we're going to record affiliate parent no matter what
        $affiliate_id = Cookie::get_affiliate_id();

        if($affiliate_id > 0) {
          $user->referrer = $affiliate_id;
          $user->store_meta();
        }

        if(!isset($has_run) || !$has_run) {
          do_action('esaf-process-signup', $user);

          $user->send_account_notifications(true, $options->welcome_email);
          $has_run = true;
        }

        require ESAF_VIEWS_PATH . '/signup/thankyou.php';
      }
      else {
        require ESAF_VIEWS_PATH . '/shared/unknown_error.php';
      }
    }
    else {
      $this->display_form($app,'',$errors,$values);
    }
  }

  /**
   * Sanitize the given form data
   *
   * @param   array  $values
   * @return  array
   */
  protected function sanitize_form_data($values) {
    $values['first_name'] = isset($values['first_name']) && is_string($values['first_name']) ? sanitize_text_field($values['first_name']) : '';
    $values['last_name'] = isset($values['last_name']) && is_string($values['last_name']) ? sanitize_text_field($values['last_name']) : '';
    $values['_wafp_user_user_login'] = isset($values['_wafp_user_user_login']) && is_string($values['_wafp_user_user_login']) ? sanitize_text_field($values['_wafp_user_user_login']) : '';
    $values['_wafp_user_user_email'] = isset($values['_wafp_user_user_email']) && is_string($values['_wafp_user_user_email']) ? sanitize_text_field($values['_wafp_user_user_email']) : '';
    $values['wafp_user_address_one'] = isset($values['wafp_user_address_one']) && is_string($values['wafp_user_address_one']) ? sanitize_text_field($values['wafp_user_address_one']) : '';
    $values['wafp_user_address_two'] = isset($values['wafp_user_address_two']) && is_string($values['wafp_user_address_two']) ? sanitize_text_field($values['wafp_user_address_two']) : '';
    $values['wafp_user_city'] = isset($values['wafp_user_city']) && is_string($values['wafp_user_city']) ? sanitize_text_field($values['wafp_user_city']) : '';
    $values['wafp_user_state'] = isset($values['wafp_user_state']) && is_string($values['wafp_user_state']) ? sanitize_text_field($values['wafp_user_state']) : '';
    $values['wafp_user_zip'] = isset($values['wafp_user_zip']) && is_string($values['wafp_user_zip']) ? sanitize_text_field($values['wafp_user_zip']) : '';
    $values['wafp_user_country'] = isset($values['wafp_user_country']) && is_string($values['wafp_user_country']) ? sanitize_text_field($values['wafp_user_country']) : '';
    $values['wafp_user_tax_id_us'] = isset($values['wafp_user_tax_id_us']) && is_string($values['wafp_user_tax_id_us']) ? sanitize_text_field($values['wafp_user_tax_id_us']) : '';
    $values['wafp_user_tax_id_int'] = isset($values['wafp_user_tax_id_int']) && is_string($values['wafp_user_tax_id_int']) ? sanitize_text_field($values['wafp_user_tax_id_int']) : '';
    $values['wafp_paypal_email'] = isset($values['wafp_paypal_email']) && is_string($values['wafp_paypal_email']) ? sanitize_text_field($values['wafp_paypal_email']) : '';
    $values['_wafp_user_user_pass'] = isset($values['_wafp_user_user_pass']) && is_string($values['_wafp_user_user_pass']) ? $values['_wafp_user_user_pass'] : ''; // No sanitization on password
    $values['wafp_user_password_confirm'] = isset($values['wafp_user_password_confirm']) && is_string($values['wafp_user_password_confirm']) ? $values['wafp_user_password_confirm'] : ''; // No sanitization on password
    $values['wafp_user_signup_agreement'] = isset($values['wafp_user_signup_agreement']);
    $values['wafp_honeypot'] = isset($values['wafp_honeypot']) && is_string($values['wafp_honeypot']) ? sanitize_text_field($values['wafp_honeypot']) : '';

    return $values;
  }
}
