<?php

namespace EasyAffiliate\Controllers;

use EasyAffiliate\Lib\AffiliatesTable;
use EasyAffiliate\Lib\BaseCtrl;
use EasyAffiliate\Lib\Cookie;
use EasyAffiliate\Lib\Utils;
use EasyAffiliate\Models\Options;
use EasyAffiliate\Models\User;

class UsersCtrl extends BaseCtrl {
  public function load_hooks() {
    add_action('edit_user_profile', [self::class, 'display_user_fields']);
    add_action('show_user_profile', [self::class, 'display_user_fields']);
    add_action('edit_user_profile_update', [self::class, 'update_user_fields']);
    add_action('personal_options_update', [self::class, 'update_user_fields']);
    add_action('admin_enqueue_scripts', [self::class, 'enqueue_scripts']);
    add_filter('manage_users_columns', [self::class, 'add_affiliate_to_user_column']);
    add_filter('manage_users_custom_column', [self::class, 'modify_user_affiliate_row'], 10, 3);
    add_action('wp_ajax_esaf_resend_welcome_email', [self::class, 'resend_welcome_email_callback']);
    add_action('user_register', [self::class, 'affiliate_registration_actions']);
    add_action('wp_ajax_wafp_affiliate_search', [self::class, 'affiliate_search']);
    add_action('delete_user', [self::class, 'delete_user']);
    add_action('admin_init', [self::class, 'process_bulk_actions']);
    add_action('current_screen', [self::class, 'add_affiliate_screen_options']);
    add_filter('set-screen-option', [self::class, 'set_affiliate_screen_options'], 10, 3);
    add_action('wp_ajax_esaf_save_affiliate_notes', [self::class, 'save_affiliate_notes']);
    add_filter('esaf_commission_type', [self::class, 'maybe_override_commission_type'], 30, 2);
    add_filter('esaf_commission_percentages', [self::class, 'maybe_override_commission_percentages'], 30, 2);
    add_filter('esaf_commission_source', [self::class, 'maybe_override_commission_source'], 30, 2);
    add_filter('esaf_subscription_commissions', [self::class, 'maybe_override_subscription_commissions'], 30, 2);
  }

  public static function enqueue_scripts($hook) {
    if(preg_match('/_page_easy-affiliate-affiliates$/', $hook)) {
      wp_enqueue_style('magnific-popup', ESAF_CSS_URL . '/magnific-popup.min.css', [], '1.1.0');
      wp_enqueue_script('magnific-popup', ESAF_JS_URL . '/jquery.magnific-popup.min.js', ['jquery'], '1.1.0', true);
      wp_enqueue_script('esaf-affiliates', ESAF_JS_URL . '/admin-affiliates.js', ['jquery'], ESAF_VERSION, true);
      wp_localize_script('esaf-affiliates', 'EsafAffiliatesL10n', [
        'save_affiliate_notes_nonce' => wp_create_nonce('esaf_save_affiliate_notes'),
        'error_saving_notes' => __('An error occurred saving the notes', 'easy-affiliate'),
      ]);
    }
  }

  public static function display_user_fields($wpuser) {
    $options = Options::fetch();
    $user = new User($wpuser->ID);

    if(Utils::is_logged_in_and_an_admin()) {
      $affiliate = false;
      $affiliate_id = $user->referrer;

      if($affiliate_id) {
        $affiliate = new User($affiliate_id);
      }

      $commission_override_enabled = false;
      $commission_type = 'percentage';
      $commission_levels = ['0.00'];
      $subscription_commissions = 'all';

      $levels = get_user_meta($user->ID, 'wafp_override', true);

      if(is_array($levels) && $levels > 0) {
        $commission_override_enabled = true;
        $commission_type = get_user_meta($user->ID, 'wafp_commission_type', true);
        $commission_levels = $levels;
        $subscription_commissions = get_user_meta($user->ID, 'wafp_recurring', true) ? 'all' : 'first-only';
      }

      require ESAF_VIEWS_PATH . '/users/admin_profile.php';
    }
  }

  public static function update_user_fields( $user_id ) {
    if( Utils::is_logged_in_and_an_admin() ) {
      $user = new User($user_id);
      $values = self::sanitize(wp_unslash($_POST), $user);
      $user->load_from_sanitized_array($values);

      if(!empty($values[$user->referrer_str])) {
        $referrer = Utils::get_userdatabylogin($values[$user->referrer_str]);

        if ($referrer) {
          $user->referrer = $referrer->ID;
        }
      }

      $user->store_meta();

      if(isset($_POST['wafp_override_enabled'])) {
        $options = Options::fetch();

        $commission_type = isset($_POST['wafp-commission-type']) && is_string($_POST['wafp-commission-type']) && $_POST['wafp-commission-type'] == 'fixed' ? 'fixed' : 'percentage';
        $commission_levels = isset($_POST['wafp-commission']) && is_array($_POST['wafp-commission']) ? $options->sanitize_commissions($_POST['wafp-commission'], $commission_type) : [];
        $recurring = isset($_POST['wafp-subscription-commissions']) && is_string($_POST['wafp-subscription-commissions']) && $_POST['wafp-subscription-commissions'] == 'all';

        update_user_meta($user_id, 'wafp_override', $commission_levels);
        update_user_meta($user_id, 'wafp_commission_type', $commission_type);
        update_user_meta($user_id, 'wafp_recurring', $recurring);
      }
      else {
        delete_user_meta($user_id, 'wafp_override');
        delete_user_meta($user_id, 'wafp_commission_type');
        delete_user_meta($user_id, 'wafp_recurring');
      }
    }
  }

  /**
   * Sanitize the given user values
   *
   * @param  array $values
   * @param  User  $user
   * @return array
   */
  private static function sanitize($values, $user) {
    $options = Options::fetch();

    if($options->is_payout_method_paypal() && $user->is_affiliate) {
      $values['wafp_paypal_email'] = isset($values['wafp_paypal_email']) && is_string($values['wafp_paypal_email']) ? sanitize_text_field($values['wafp_paypal_email']) : '';
    }

    if(($options->show_address_fields || $options->show_address_fields_account) && $user->is_affiliate) {
      $values['wafp_user_address_one'] = isset($values['wafp_user_address_one']) && is_string($values['wafp_user_address_one']) ? sanitize_text_field($values['wafp_user_address_one']) : '';
      $values['wafp_user_address_two'] = isset($values['wafp_user_address_two']) && is_string($values['wafp_user_address_two']) ? sanitize_text_field($values['wafp_user_address_two']) : '';
      $values['wafp_user_city'] = isset($values['wafp_user_city']) && is_string($values['wafp_user_city']) ? sanitize_text_field($values['wafp_user_city']) : '';
      $values['wafp_user_state'] = isset($values['wafp_user_state']) && is_string($values['wafp_user_state']) ? sanitize_text_field($values['wafp_user_state']) : '';
      $values['wafp_user_zip'] = isset($values['wafp_user_zip']) && is_string($values['wafp_user_zip']) ? sanitize_text_field($values['wafp_user_zip']) : '';
      $values['wafp_user_country'] = isset($values['wafp_user_country']) && is_string($values['wafp_user_country']) ? sanitize_text_field($values['wafp_user_country']) : '';
    }

    if(($options->show_tax_id_fields || $options->show_tax_id_fields_account) && $user->is_affiliate) {
      $values['wafp_user_tax_id_us'] = isset($values['wafp_user_tax_id_us']) && is_string($values['wafp_user_tax_id_us']) ? sanitize_text_field($values['wafp_user_tax_id_us']) : '';
      $values['wafp_user_tax_id_int'] = isset($values['wafp_user_tax_id_int']) && is_string($values['wafp_user_tax_id_int']) ? sanitize_text_field($values['wafp_user_tax_id_int']) : '';
    }

    $values['wafp-affiliate-referrer'] = isset($values['wafp-affiliate-referrer']) && is_string($values['wafp-affiliate-referrer']) ? sanitize_user($values['wafp-affiliate-referrer']) : '';
    $values['wafp_is_affiliate'] = isset($values['wafp_is_affiliate']);
    $values['wafp_is_blocked'] = isset($values['wafp_is_blocked']);
    $values['wafp_affiliate_unsubscribed'] = isset($values['wafp_affiliate_unsubscribed']);
    $values['wafp_blocked_message'] = isset($values['wafp_blocked_message']) && is_string($values['wafp_blocked_message']) ? wp_kses_post($values['wafp_blocked_message']) : '';


    return $values;
  }

  public static function add_affiliate_to_user_column($column) {
    $column['wafp_is_affiliate'] = esc_html__('Is Affiliate', 'easy-affiliate');
    $column['wafp_affiliate'] = esc_html__('Affiliate Referrer', 'easy-affiliate');

    return $column;
  }

  public static function modify_user_affiliate_row($val, $column_name, $user_id) {
    if($column_name == 'wafp_affiliate') {
      $wuser = new User($user_id);
      $affiliate_id = $wuser->referrer;

      if($affiliate_id) {
        $affiliate = new User($affiliate_id);

        if($affiliate != false) {
          return "<a href=\"" . esc_url(admin_url("user-edit.php?user_id={$affiliate_id}&wp_http_referer=%2Fwp-admin%2Fusers.php")) . "\">" . esc_html($affiliate->full_name()) . "</a>";
        }
      }

      return esc_html__('None', 'easy-affiliate');
    }
    else if($column_name == 'wafp_is_affiliate') {
      $user = new User($user_id);

      return ($user->is_affiliate?esc_html__('Yes', 'easy-affiliate'):esc_html__('No', 'easy-affiliate'));
    }

    return $val;
  }

  public static function resend_welcome_email_callback() {
    if(!Utils::is_post_request() || !isset($_POST['uid']) || !is_numeric($_POST['uid'])) {
      wp_send_json_error(__('Bad request', 'easy-affiliate'));
    }

    if(!Utils::is_logged_in_and_an_admin()) {
      wp_send_json_error(__('Sorry, you don\'t have permission to do this.', 'easy-affiliate'));
    }

    if(!check_ajax_referer('esaf_resend_welcome_email', false, false)) {
      wp_send_json_error(__('Security check failed.', 'easy-affiliate'));
    }

    $user = new User((int) $_POST['uid']);
    $user->send_account_notifications(false, true);

    wp_send_json_success(__('Message sent', 'easy-affiliate'));
  }

  public static function display_affiliates_list() {
    $aff_table = new AffiliatesTable();
    $aff_table->prepare_items();

    require ESAF_VIEWS_PATH . '/affiliates/list.php';
  }

  public static function affiliate_registration_actions($user_id) {
    if(isset($_REQUEST['_wafp_user_user_email'])) { return; } // User registered via EA, no need to run this again

    $options = Options::fetch();
    $user = new User($user_id);

    //Set this user's referring affiliate if any
    if(!current_user_can('remove_users') && Cookie::get_affiliate_id() > 0) {
      $user->referrer = Cookie::get_affiliate_id();
      $user->store();
    }

    // Let's set user to be an affiliate automatically
    //Adding no_wafp_aff as a way for other plugins to omit the automatic affiliate creation if they want
    if($options->make_new_users_affiliates && !$user->is_affiliate && !isset($_REQUEST['no_wafp_aff'])) {
      $user->is_affiliate = true;
      $user->store();
      $user->send_account_notifications($options->welcome_email, $options->welcome_email);
    }
  }

  public static function affiliate_search() {
    if (!current_user_can('list_users')) {
      die('-1');
    }

    $s = $_GET['q']; // is this slashed already?

    $s = trim($s);
    if (strlen($s) < 2) {
      die; // require 2 chars for matching
    }

    $users = get_users(['search' => "*$s*", 'meta_key' => 'wafp_is_affiliate', 'meta_value' => 1]);

    require ESAF_VIEWS_PATH . '/users/affiliate_search.php';

    die;
  }

  /**
   * reassign referrers to parent if exist, other blank out child referrers
   *
   * @return void
   * @author Brad Van Skyhawk
   **/
  public static function delete_user($user_id) {
    $user = new User();
    $key = $user->referrer_str;

    // Get the children
    $children = get_users(['fields' => 'ID', 'meta_key' => $key, 'meta_value' => $user_id]);

    if($children) {
      // Get the parent
      $parent_id = get_user_meta($user_id, $key, true);

      // Reassign children to parent
      foreach($children as $child_id) {
        update_user_meta($child_id, $key, $parent_id);
      }
    }
  }

  public static function process_bulk_actions() {
    if(empty($_GET['page']) || $_GET['page'] != 'easy-affiliate-affiliates') {
      return;
    }

    if(!empty($_GET['_wp_http_referer'])) {
      wp_redirect(remove_query_arg(['_wp_http_referer', '_wpnonce'], wp_unslash($_SERVER['REQUEST_URI'])));
      exit;
    }
  }

  /**
   * Add Screen Options to the Affiliates list page
   *
   * @param \WP_Screen $screen
   */
  public static function add_affiliate_screen_options($screen) {
    if($screen instanceof \WP_Screen && preg_match('/_page_easy-affiliate-affiliates$/', $screen->id)) {
      add_screen_option('per_page', [
        'label' => esc_html__('Affiliates per page', 'easy-affiliate'),
        'default' => 10,
        'option' => 'esaf_affiliates_per_page'
      ]);
    }
  }

  /**
   * Save the Screen Options on the Affiliates list page
   *
   * @param  bool     $keep
   * @param  string   $option
   * @param  string   $value
   * @return int|bool
   */
  public static function set_affiliate_screen_options($keep, $option, $value) {
    if($option == 'esaf_affiliates_per_page' && is_numeric($value)) {
      return Utils::clamp((int) $value, 1, 999);
    }

    return $keep;
  }

  public static function save_affiliate_notes() {
    if(!Utils::is_post_request() || !isset($_POST['data']) || !is_string($_POST['data'])) {
      wp_send_json_error(__('Bad request.', 'easy-affiliate'));
    }

    if(!Utils::is_logged_in_and_an_admin()) {
      wp_send_json_error(__('Sorry, you don\'t have permission to do this.', 'easy-affiliate'));
    }

    if(!check_ajax_referer('esaf_save_affiliate_notes', false, false)) {
      wp_send_json_error(__('Security check failed.', 'easy-affiliate'));
    }

    $data = json_decode(wp_unslash($_POST['data']), true);

    if(!is_array($data) || !isset($data['affiliate_id'], $data['notes']) || !is_numeric($data['affiliate_id']) || !is_string($data['notes'])) {
      wp_send_json_error(__('Bad request', 'easy-affiliate'));
    }

    $affiliate = new User((int) $data['affiliate_id']);

    if($affiliate->ID > 0) {
      $affiliate->notes = sanitize_textarea_field($data['notes']);
      $affiliate->store();
    }

    wp_send_json_success();
  }

  /**
   * Override the commission type if the affiliate has a User Profile Commission
   *
   * @param string $commission_type
   * @param \EasyAffiliate\Models\User $affiliate
   * @return string
   */
  public static function maybe_override_commission_type($commission_type, $affiliate) {
    $levels = get_user_meta($affiliate->ID, 'wafp_override', true);

    if(is_array($levels) && count($levels) > 0) {
      $commission_type = get_user_meta($affiliate->ID, 'wafp_commission_type', true);
    }

    return $commission_type;
  }

  /**
   * Override the commission levels if the affiliate has a User Profile Commission
   *
   * @param array $commissions
   * @param \EasyAffiliate\Models\User $affiliate
   * @return array
   */
  public static function maybe_override_commission_percentages($commissions, $affiliate) {
    $levels = get_user_meta($affiliate->ID, 'wafp_override', true);

    if(is_array($levels) && count($levels) > 0) {
      $commissions = $levels;
    }

    return $commissions;
  }

  /**
   * Override the commission source if the affiliate has a User Profile Commission
   *
   * @param array $source
   * @param \EasyAffiliate\Models\User $affiliate
   * @return array
   */
  public static function maybe_override_commission_source($source, $affiliate) {
    $levels = get_user_meta($affiliate->ID, 'wafp_override', true);

    if(is_array($levels) && count($levels) > 0) {
      $source = [
        'slug' => 'user',
        'label' => __('User Override','easy-affiliate'),
        'object' => $affiliate
      ];
    }

    return $source;
  }

  /**
   * Override the subscription commissions if the affiliate has a User Profile Commission
   *
   * @param string $subscription_commissions
   * @param \EasyAffiliate\Models\User $affiliate
   * @return string
   */
  public static function maybe_override_subscription_commissions($subscription_commissions, $affiliate) {
    $levels = get_user_meta($affiliate->ID, 'wafp_override', true);

    if(is_array($levels) && count($levels) > 0) {
      $subscription_commissions = get_user_meta($affiliate->ID, 'wafp_recurring', true) ? 'all' : 'first-only';
    }

    return $subscription_commissions;
  }
}
