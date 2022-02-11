<?php

namespace EasyAffiliate\Controllers;

use EasyAffiliate\Helpers\AppHelper;
use EasyAffiliate\Helpers\CreativesHelper;
use EasyAffiliate\Lib\BaseCtrl;
use EasyAffiliate\Lib\CustomizeSetting;
use EasyAffiliate\Lib\Report;
use EasyAffiliate\Lib\Utils;
use EasyAffiliate\Models\Creative;
use EasyAffiliate\Models\CustomLink;
use EasyAffiliate\Models\Options;
use EasyAffiliate\Models\User;

class DashboardCtrl extends BaseCtrl {
  const CREATIVES_TEXT_LINKS_PER_PAGE = 10;
  const CREATIVES_BANNER_LINKS_PER_PAGE = 20;

  public function load_hooks() {
    // Get creatives via Ajax for infinite scroller
    add_action('wp_ajax_esaf_get_text_links', [$this, 'get_text_links_ajax']);
    add_action('wp_ajax_esaf_get_banner_links', [$this, 'get_banner_links_ajax']);
    add_action('wp_ajax_esaf_get_clicks_and_conversions_statistic', [$this, 'get_clicks_and_conversions_statistic_ajax']);
    add_action('wp_ajax_esaf_get_affiliate_payments', [$this, 'get_affiliate_payments_ajax']);
    add_action('wp_ajax_esaf_create_custom_link', [$this, 'create_custom_link_ajax']);
    add_action('wp_ajax_esaf_update_custom_link', [$this, 'update_custom_link_ajax']);
    add_action('customize_register', [$this, 'register_pro_dashboard_customizer_settings']);
    add_action('customize_preview_init', [$this, 'customizer_live_preview']);
  }

  public function route() {
    $options = Options::fetch();
    $user = Utils::get_currentuserinfo();
    $action = AppCtrl::get_param('action', 'home');

    if($user instanceof User) {
      // Make admin users affiliates automatically if they view the affiliate dashboard
      if(!$user->is_affiliate && (Utils::is_admin() || isset($_POST['become_affiliate_submit']))) {  // (see views/dashboard/become.php)
        $user->is_affiliate = true;
        $user->store();
      }

      if($user->is_affiliate) {
        if($user->is_blocked) {
          $this->display_blocked_affiliate();
        }
        else {
          $show_creatives = (bool) Creative::get_count();

          if($action == 'home' || empty($action)) {
            $this->display_dashboard();
          }
          elseif($action == 'creatives' && ($show_creatives || AppCtrl::get_param('view') == 'custom-links')) {
            $view = AppCtrl::get_param('view', 'text-links');

            if($view == 'text-links') {
              $this->display_creatives_text_links();
            }
            elseif($view == 'banners') {
              $this->display_creatives_banners();
            }
            elseif($view == 'coupons') {
              $this->display_coupons();
            }
            elseif($view == 'custom-links') {
              $this->display_creatives_custom_links();
            }
          }
          elseif($action == 'payments') {
            $this->display_payments();
          }
          elseif($action == 'account') {
            $this->display_account();
          }
          else {
            do_action('esaf_dashboard_page_route', $action);
          }
        }
      }
      else { // Not is_affiliate
        if($options->registration_type == 'public') {
          $this->display_become_affiliate(); //Added by Paul (shows if not affiliate)
        }
        elseif($options->registration_type == 'application') {
          ?>
          <script>
            window.location = '<?php echo esc_url_raw(Utils::signup_url()); ?>';
          </script>
          <?php
        }
        else {
          printf(
            '<p>%s</p>',
            esc_html__('Sorry, affiliate registration is private.', 'easy-affiliate')
          );
        }
      }
    }
    else {
      $loginURL = Utils::login_url();
      require ESAF_VIEWS_PATH . '/shared/unauthorized.php';
    }
  }

  public function create_custom_link_ajax() {
    $user = Utils::get_currentuserinfo();

    if(!$user instanceof User) {
      wp_send_json_error(__('You are not currently logged in', 'easy-affiliate'));
    }

    if(!$user->is_affiliate) {
      wp_send_json_error(__('You are not currently an affiliate', 'easy-affiliate'));
    }

    $destination = isset($_POST['destination_url']) && is_string($_POST['destination_url']) ? sanitize_text_field($_POST['destination_url']) : '';

    if(empty($destination) || !wp_http_validate_url($destination)) {
      wp_send_json_error(__('Invalid URL', 'easy-affiliate'));
    }

    $custom_link = new CustomLink();
    $custom_link->affiliate_id = $user->ID;
    $custom_link->destination_link = esc_url_raw($destination);
    $custom_link->store();

    wp_send_json_success(CreativesHelper::custom_link_row($custom_link, $user));
  }

  public function update_custom_link_ajax() {
    $user = Utils::get_currentuserinfo();

    if(!$user instanceof User) {
      wp_send_json_error(__('You are not currently logged in', 'easy-affiliate'));
    }

    if(!$user->is_affiliate) {
      wp_send_json_error(__('You are not currently an affiliate', 'easy-affiliate'));
    }

    $id = isset($_POST['link_id']) && is_numeric($_POST['link_id']) ? (int) $_POST['link_id'] : 0;
    $destination = isset($_POST['destination_url']) && is_string($_POST['destination_url']) ? sanitize_text_field($_POST['destination_url']) : '';

    if(empty($destination) || !wp_http_validate_url($destination)) {
      wp_send_json_error(__('Invalid URL', 'easy-affiliate'));
    }

    $custom_link = CustomLink::get_one(['id' => $id, 'affiliate_id' => $user->ID]);

    if(!$custom_link instanceof CustomLink) {
      wp_send_json_error(__('Custom link not found', 'easy-affiliate'));
    }

    $custom_link->destination_link = $destination;
    $custom_link->store();

    wp_send_json_success(CreativesHelper::custom_link_row($custom_link, $user));
  }

  public function get_clicks_and_conversions_statistic_ajax() {
    $affiliate = Utils::get_currentuserinfo();

    if(!$affiliate instanceof User) {
      wp_send_json_error(__('Not logged in', 'easy-affiliate'));
    }

    if(!$affiliate->is_affiliate) {
      wp_send_json_error(__('Not an affiliate', 'easy-affiliate'));
    }

    if(empty($_POST['start']) || empty($_POST['end']) || !is_string($_POST['start']) || !is_string($_POST['end'])) {
      wp_send_json_error(__('Invalid dates', 'easy-affiliate'));
    }

    $start = sanitize_text_field(wp_unslash($_POST['start']));
    $end = sanitize_text_field(wp_unslash($_POST['end']));

    try {
      $utc = new \DateTimeZone('UTC');
      $start = new \DateTimeImmutable($start, $utc);
      $end = new \DateTimeImmutable($end, $utc);
      $now = new \DateTimeImmutable('now', $utc);

      if($end > $now) {
        $end = $now;
      }

      $clicks_in_period = Report::get_clicks_in_period($start, $end, $affiliate->ID);
      $commissions_in_period = Report::get_commissions_in_period($start, $end, $affiliate->ID);
      $interval = new \DateInterval('P1D');
      $period = new \DatePeriod($start, $interval, $end->modify('+1 day'));
      $clicks = [];
      $conversions = [];

      foreach($period as $date) {
        $date = $date->format('Y-m-d');

        $clicks[] = [
          'x' => $date,
          'y' => isset($clicks_in_period[$date]) ? (int) $clicks_in_period[$date] : 0
        ];

        $conversions[] = [
          'x' => $date,
          'y' => isset($commissions_in_period[$date]) ? (int) $commissions_in_period[$date] : 0
        ];
      }

      wp_send_json_success([
        'clicks' => $clicks,
        'conversions' => $conversions,
      ]);
    }
    catch (\Exception $e) {
      wp_send_json_error(__('Invalid dates', 'easy-affiliate'));
    }
  }

  public function display_become_affiliate() {
    $options = Options::fetch();

    require ESAF_VIEWS_PATH . '/dashboard/become.php';
  }

  public function display_blocked_affiliate() {
    $user = Utils::get_currentuserinfo();

    require ESAF_VIEWS_PATH . '/dashboard/blocked.php';
  }

  public function display_dashboard() {
    global $current_user;

    $options = Options::fetch();
    Utils::get_currentuserinfo();
    $affiliate_id = $current_user->ID;
    $show_nav = !$options->pro_dashboard_enabled;
    $action = 'home';
    $pmt_totals = Report::affiliate_payment_totals($affiliate_id);
    extract($pmt_totals);
    $affiliate = new User($affiliate_id);
    $default_affiliate_url = $affiliate->default_affiliate_url();
    $showcase_url = $affiliate->showcase_url();
    $overall_stats = User::get_dashboard_stats($current_user->ID);
    $estimated_next_payout = $affiliate->get_estimated_next_payout();

    require ESAF_VIEWS_PATH . '/dashboard/ui.php';
  }

  public function display_account($show_nav = true) {
    global $current_user;
    $options = Options::fetch();
    Utils::get_currentuserinfo();
    $affiliate_id = $current_user->ID;

    if(Utils::is_post_request() && isset($_POST['esaf_process_account_form'])) {
      $values = $this->sanitize_account_form(wp_unslash($_POST));
      $errors = $this->validate_account_form($values);

      if(empty($errors)) {
        update_user_meta($affiliate_id, 'first_name', wp_slash($values['wafp_dashboard_first_name']));
        update_user_meta($affiliate_id, 'last_name', wp_slash($values['wafp_dashboard_last_name']));
        update_user_meta($affiliate_id, 'wafp_paypal_email', wp_slash($values['wafp_dashboard_paypal']));

        if($options->show_address_fields_account) {
          update_user_meta($affiliate_id, 'wafp_user_address_one', wp_slash($values['wafp_dashboard_address_one']));
          update_user_meta($affiliate_id, 'wafp_user_address_two', wp_slash($values['wafp_dashboard_address_two']));
          update_user_meta($affiliate_id, 'wafp_user_city', wp_slash($values['wafp_dashboard_city']));
          update_user_meta($affiliate_id, 'wafp_user_state', wp_slash($values['wafp_dashboard_state']));
          update_user_meta($affiliate_id, 'wafp_user_zip', wp_slash($values['wafp_dashboard_zip']));
          update_user_meta($affiliate_id, 'wafp_user_country', wp_slash($values['wafp_dashboard_country']));
        }

        if($options->show_tax_id_fields_account) {
          update_user_meta($affiliate_id, 'wafp_user_tax_id_us', wp_slash($values['wafp_dashboard_tax_id_us']));
          update_user_meta($affiliate_id, 'wafp_user_tax_id_int', wp_slash($values['wafp_dashboard_tax_id_int']));
        }

        update_user_meta($affiliate_id, 'wafp_affiliate_unsubscribed', $values['wafp_dashboard_unsubscribed']);

        $account_saved = true;
        do_action('esaf_dashboard_process_account');
      }
    }
    else {
      $values = [
        'wafp_dashboard_first_name' => $current_user->first_name,
        'wafp_dashboard_last_name' => $current_user->last_name
      ];

      if($options->is_payout_method_paypal()) {
        $values = array_merge($values, [
          'wafp_dashboard_paypal' => $current_user->wafp_paypal_email,
        ]);
      }

      if($options->show_address_fields_account) {
        $values = array_merge($values, [
          'wafp_dashboard_address_one' => $current_user->wafp_user_address_one,
          'wafp_dashboard_address_two' => $current_user->wafp_user_address_two,
          'wafp_dashboard_city' => $current_user->wafp_user_city,
          'wafp_dashboard_state' => $current_user->wafp_user_state,
          'wafp_dashboard_zip' => $current_user->wafp_user_zip,
          'wafp_dashboard_country' => $current_user->wafp_user_country,
        ]);
      }

      if($options->show_tax_id_fields_account) {
        $values = array_merge($values, [
          'wafp_dashboard_tax_id_us' => $current_user->wafp_user_tax_id_us,
          'wafp_dashboard_tax_id_int' => $current_user->wafp_user_tax_id_int,
        ]);
      }

      $values = array_merge($values, [
        'wafp_dashboard_unsubscribed' => $current_user->wafp_affiliate_unsubscribed,
      ]);

      $values = apply_filters('esaf_dashboard_account_values', $values);
    }

    $show_nav = !$options->pro_dashboard_enabled;
    $action = 'account';
    require ESAF_VIEWS_PATH . '/dashboard/ui.php';
  }

  /**
   * Sanitize the account form values
   *
   * @param array $values
   * @return array
   */
  public function sanitize_account_form($values) {
    $options = Options::fetch();

    $values['wafp_dashboard_first_name'] = isset($values['wafp_dashboard_first_name']) && is_string($values['wafp_dashboard_first_name']) ? sanitize_text_field($values['wafp_dashboard_first_name']) : '';
    $values['wafp_dashboard_last_name'] = isset($values['wafp_dashboard_last_name']) && is_string($values['wafp_dashboard_last_name']) ? sanitize_text_field($values['wafp_dashboard_last_name']) : '';

    if($options->is_payout_method_paypal()) {
      $values['wafp_dashboard_paypal'] = isset($values['wafp_dashboard_paypal']) && is_string($values['wafp_dashboard_paypal']) ? sanitize_text_field($values['wafp_dashboard_paypal']) : '';
    }

    if($options->show_address_fields_account) {
      $values['wafp_dashboard_address_one'] = isset($values['wafp_dashboard_address_one']) && is_string($values['wafp_dashboard_address_one']) ? sanitize_text_field($values['wafp_dashboard_address_one']) : '';
      $values['wafp_dashboard_address_two'] = isset($values['wafp_dashboard_address_two']) && is_string($values['wafp_dashboard_address_two']) ? sanitize_text_field($values['wafp_dashboard_address_two']) : '';
      $values['wafp_dashboard_city'] = isset($values['wafp_dashboard_city']) && is_string($values['wafp_dashboard_city']) ? sanitize_text_field($values['wafp_dashboard_city']) : '';
      $values['wafp_dashboard_state'] = isset($values['wafp_dashboard_state']) && is_string($values['wafp_dashboard_state']) ? sanitize_text_field($values['wafp_dashboard_state']) : '';
      $values['wafp_dashboard_zip'] = isset($values['wafp_dashboard_zip']) && is_string($values['wafp_dashboard_zip']) ? sanitize_text_field($values['wafp_dashboard_zip']) : '';
      $values['wafp_dashboard_country'] = isset($values['wafp_dashboard_country']) && is_string($values['wafp_dashboard_country']) ? sanitize_text_field($values['wafp_dashboard_country']) : '';
    }

    if($options->show_tax_id_fields_account) {
      $values['wafp_dashboard_tax_id_us'] = isset($values['wafp_dashboard_tax_id_us']) && is_string($values['wafp_dashboard_tax_id_us']) ? sanitize_text_field($values['wafp_dashboard_tax_id_us']) : '';
      $values['wafp_dashboard_tax_id_int'] = isset($values['wafp_dashboard_tax_id_int']) && is_string($values['wafp_dashboard_tax_id_int']) ? sanitize_text_field($values['wafp_dashboard_tax_id_int']) : '';
    }

    $values['wafp_dashboard_unsubscribed'] = isset($values['wafp_dashboard_unsubscribed']);

    return $values;
  }

  /**
   * Validate the account form values
   *
   * @param array $values
   * @return array
   */
  public function validate_account_form($values) {
    $errors = [];
    $options = Options::fetch();

    if(empty($values['wafp_dashboard_first_name'])) {
      $errors[] = __('You must enter a First Name','easy-affiliate');
    }

    if(empty($values['wafp_dashboard_last_name'])) {
      $errors[] = __('You must enter a Last Name','easy-affiliate');
    }

    if($options->is_payout_method_paypal()) {
      if(empty($values['wafp_dashboard_paypal'])) {
        $errors[] = __('PayPal email address is required','easy-affiliate');
      }
      elseif(!is_email($values['wafp_dashboard_paypal'])) {
        $errors[] = __('PayPal email address must be a real and properly formatted email address','easy-affiliate');
      }
    }

    if($options->show_address_fields_account && $options->require_address_fields) {
      if(empty($values['wafp_dashboard_address_one'])) {
        $errors[] = __('You must enter an Address', 'easy-affiliate');
      }

      if(empty($values['wafp_dashboard_city'])) {
        $errors[] = __('You must enter a City', 'easy-affiliate');
      }

      if(empty($values['wafp_dashboard_state'])) {
        $errors[] = __('You must enter a State/Province', 'easy-affiliate');
      }

      if(empty($values['wafp_dashboard_zip'])) {
        $errors[] = __('You must enter a Zip/Postal Code', 'easy-affiliate');
      }

      if(empty($values['wafp_dashboard_country'])) {
        $errors[] = __('You must enter a Country', 'easy-affiliate');
      }
    }

    if($options->show_tax_id_fields_account && $options->require_tax_id_fields) {
      if(empty($values['wafp_dashboard_tax_id_us']) && empty($values['wafp_dashboard_tax_id_int'])) {
        $errors[] = __('You must enter an SSN / Tax ID or International Tax ID', 'easy-affiliate');
      }
    }

    return $errors;
  }

  public function display_creatives_text_links() {
    global $current_user;
    $options = Options::fetch();
    Utils::get_currentuserinfo();
    $affiliate_id = $current_user->ID;
    $affiliate = new User($affiliate_id);
    $default_affiliate_url = $affiliate->default_affiliate_url();
    $show_nav = !$options->pro_dashboard_enabled;
    $action = 'creatives';
    $view = 'text-links';
    $per_page = apply_filters('esaf_dashboard_creatives_text_links_per_page', self::CREATIVES_TEXT_LINKS_PER_PAGE);

    $text_links =  Creative::get_all_visible(['text', 'url'], false, false, 1, $per_page);

    require ESAF_VIEWS_PATH . '/dashboard/ui.php';
  }

  public function display_creatives_banners() {
    global $current_user;
    $options = Options::fetch();
    Utils::get_currentuserinfo();
    $affiliate_id = $current_user->ID;
    $show_nav = !$options->pro_dashboard_enabled;
    $action = 'creatives';
    $view = 'banners';
    $banners = Creative::get_all_visible('banner', false, false, 1, 20);

    require ESAF_VIEWS_PATH . '/dashboard/ui.php';
  }

  public function display_coupons() {
    global $current_user;
    $options = Options::fetch();
    Utils::get_currentuserinfo();
    $affiliate_id = $current_user->ID;
    $show_nav = !$options->pro_dashboard_enabled;
    $action = 'creatives';
    $view = 'coupons';
    $coupons = apply_filters('esaf_dashboard_coupons', [], $affiliate_id);

    require ESAF_VIEWS_PATH . '/dashboard/ui.php';
  }

  public function display_creatives_custom_links() {
    global $current_user;
    $options = Options::fetch();
    $user = Utils::get_currentuserinfo();
    $affiliate_id = $current_user->ID;
    $show_nav = !$options->pro_dashboard_enabled;
    $action = 'creatives';
    $view = 'custom-links';
    $custom_links = CustomLink::get_all('created_at DESC', '', ['affiliate_id' => $affiliate_id]);

    require ESAF_VIEWS_PATH . '/dashboard/ui.php';
  }

  public function display_payments() {
    global $current_user;
    $options = Options::fetch();
    Utils::get_currentuserinfo();
    $affiliate_id = $current_user->ID;

    $payments = Report::affiliate_frontend_payments($affiliate_id);
    $pmt_totals = Report::affiliate_payment_totals($affiliate_id);

    extract($pmt_totals);

    $show_nav = !$options->pro_dashboard_enabled;
    $action = 'payments';
    require ESAF_VIEWS_PATH . '/dashboard/ui.php';
  }

  public function get_affiliate_payments_ajax() {
    if(!isset($_GET['offset']) || !is_numeric($_GET['offset'])) {
      wp_send_json_error(__('Bad request', 'easy-affiliate'));
    }

    $page = max((int) $_GET['offset'], 2);

    $affiliate = Utils::get_currentuserinfo();

    if(!$affiliate instanceof User) {
      wp_send_json_error(__('Not logged in', 'easy-affiliate'));
    }

    if(!$affiliate->is_affiliate) {
      wp_send_json_error(__('Not an affiliate', 'easy-affiliate'));
    }

    $payments = Report::affiliate_frontend_payments($affiliate->ID, $page);
    $payments_row = [];

    if (is_array($payments) && count($payments) > 0) {
      foreach ($payments as $payment) {
        $date           = $payment->year . '-' . $payment->month . '-01';
        $date           = strtotime($date);
        $payments_row[] =
          "<tr>
            <td>" . date('M', $date) . ' ' . date('Y', $date) . "</td>
            <td>" .esc_html(AppHelper::format_currency( (float)$payment->paid )) . "</td>
          </tr>";
      }
    }

    wp_send_json_success($payments_row);
  }

  public function get_text_links_ajax() {
    if(!isset($_GET['offset']) || !is_numeric($_GET['offset'])) {
      wp_send_json_error(__('Bad request', 'easy-affiliate'));
    }

    $offset = max((int) $_GET['offset'], 2);

    $affiliate = Utils::get_currentuserinfo();

    if(!$affiliate instanceof User) {
      wp_send_json_error(__('Not logged in', 'easy-affiliate'));
    }

    if(!$affiliate->is_affiliate) {
      wp_send_json_error(__('Not an affiliate', 'easy-affiliate'));
    }

    $per_page = apply_filters('esaf_dashboard_creatives_text_links_per_page', self::CREATIVES_TEXT_LINKS_PER_PAGE);
    $text_links = Creative::get_all_visible(['text', 'url'], false, false, $offset, $per_page);
    $data = [];

    if(is_array($text_links)) {
      foreach ($text_links as $text_link) {
        $data[] = CreativesHelper::text_link_row($text_link, $affiliate->ID);
      }
    }

    wp_send_json_success($data);
  }

  public function get_banner_links_ajax() {
    if(!isset($_GET['offset']) || !is_numeric($_GET['offset'])) {
      wp_send_json_error(__('Bad request', 'easy-affiliate'));
    }

    $offset = max((int) $_GET['offset'], 2);

    $affiliate = Utils::get_currentuserinfo();

    if(!$affiliate instanceof User) {
      wp_send_json_error(__('Not logged in', 'easy-affiliate'));
    }

    if(!$affiliate->is_affiliate) {
      wp_send_json_error(__('Not an affiliate', 'easy-affiliate'));
    }

    $per_page = apply_filters('esaf_dashboard_creatives_text_links_per_page', self::CREATIVES_BANNER_LINKS_PER_PAGE);
    $links = Creative::get_all_visible('banner', false, false, $offset, $per_page);
    $data = [];

    if(is_array($links)) {
      foreach($links as $link) {
        $data[] = CreativesHelper::banner_grid_item($link, $affiliate->ID);
      }
    }

    wp_send_json_success($data);
  }

  /**
   * Get the custom CSS for the pro dashboard
   *
   * @return string
   */
  public static function get_pro_dashboard_custom_css() {
    global $wp_customize;
    $options = Options::fetch();
    $css = '';

    if($wp_customize instanceof \WP_Customize_Manager && $wp_customize->is_preview()) {
      $brand_color_setting = $wp_customize->get_setting('esaf_pro_dashboard_brand_color');
      $brand_color = $brand_color_setting instanceof \WP_Customize_Setting ? $brand_color_setting->value() : '#32373b';
      $accent_color_setting = $wp_customize->get_setting('esaf_pro_dashboard_accent_color');
      $accent_color = $accent_color_setting instanceof \WP_Customize_Setting ? $accent_color_setting->value() : '#222629';
      $menu_text_color_setting = $wp_customize->get_setting('esaf_pro_dashboard_menu_text_color');
      $menu_text_color = $menu_text_color_setting instanceof \WP_Customize_Setting ? $menu_text_color_setting->value() : '#b7bcc0';
      $menu_text_highlight_color_setting = $wp_customize->get_setting('esaf_pro_dashboard_menu_text_highlight_color');
      $menu_text_highlight_color = $menu_text_highlight_color_setting instanceof \WP_Customize_Setting ? $menu_text_highlight_color_setting->value() : '#ffffff';
    }
    else {
      $brand_color = $options->pro_dashboard_brand_color;
      $accent_color = $options->pro_dashboard_accent_color;
      $menu_text_color = $options->pro_dashboard_menu_text_color;
      $menu_text_highlight_color = $options->pro_dashboard_menu_text_highlight_color;
    }

    if(!empty($brand_color) && $brand_color != '#32373b') {
      $css .= sprintf(self::get_brand_color_custom_css(),$brand_color);
    }

    if(!empty($accent_color) && $accent_color != '#222629') {
      $css .= sprintf(self::get_accent_color_custom_css(), $accent_color);
    }

    if(!empty($menu_text_color) && $menu_text_color != '#b7bcc0') {
      $css .= sprintf(self::get_menu_text_color_custom_css(), $menu_text_color);
    }

    if(!empty($menu_text_highlight_color) && ($menu_text_highlight_color != '#ffffff') || (!empty($menu_text_color) && $menu_text_color != '#b7bcc0')) {
      $css .= sprintf(self::get_menu_text_highlight_color_custom_css(), $menu_text_highlight_color);
    }

    return $css;
  }

  public static function get_brand_color_custom_css() {
    return '.esaf-pro-dashboard-header,
      .esaf-pro-dashboard-header-menu-content,
      .esaf-pro-dashboard-menu,
      .esaf-pro-dashboard button,
      .esaf-pro-dashboard button:hover,
      .esaf-pro-dashboard button:focus {
        background-color: %1$s;
      }';
  }

  public static function get_accent_color_custom_css() {
    return '.esaf-pro-dashboard-menu a.esaf-nav-active {
      background-color: %1$s;
    }';
  }

  public static function get_menu_text_color_custom_css() {
    return '.esaf-pro-dashboard-header .esaf-pro-dashboard-back-link svg {
      fill: %1$s;
    }
    .esaf-pro-dashboard-header h1,
    .esaf-pro-dashboard-header .ea-icon,
    .esaf-pro-dashboard-user-name,
    .esaf-pro-dashboard-user-username-id,
    .esaf-pro-dashboard-header-menu-content a,
    .esaf-pro-dashboard-header-menu-content a:link,
    .esaf-pro-dashboard-header-menu-content a:visited,
    .esaf-pro-dashboard-menu a,
    .esaf-pro-dashboard-menu a:link,
    .esaf-pro-dashboard-menu a:visited,
    .esaf-pro-dashboard button,
    .esaf-pro-dashboard button:hover,
    .esaf-pro-dashboard button:focus {
      color: %1$s;
    }
    .esaf-pro-dashboard-header .avatar,
    .esaf-pro-dashboard-user-box {
      border-color: %1$s;
    }';
  }

  public static function get_menu_text_highlight_color_custom_css() {
    return '.esaf-pro-dashboard-header .esaf-pro-dashboard-back-link:hover svg {
      fill: %1$s;
    }
    .esaf-pro-dashboard-header-menu-content a:hover,
    .esaf-pro-dashboard-header-menu-content a:active,
    .esaf-pro-dashboard-header-menu-content a:focus,
    .esaf-pro-dashboard-menu a.esaf-nav-active,
    .esaf-pro-dashboard-menu a:hover,
    .esaf-pro-dashboard-menu a:active,
    .esaf-pro-dashboard-menu a:focus {
      color: %1$s;
    }';
  }

  public static function get_pro_dashboard_logo_url() {
    global $wp_customize;
    $options = Options::fetch();

    if($wp_customize instanceof \WP_Customize_Manager && $wp_customize->is_preview()) {
      $logo_url_setting = $wp_customize->get_setting('esaf_pro_dashboard_logo_url');

      if($logo_url_setting instanceof \WP_Customize_Setting) {
        return $logo_url_setting->value();
      }
    }

    return $options->pro_dashboard_logo_url;
  }

  /**
   * Register the customizer controls for the Pro Dashboard
   *
   * @param \WP_Customize_Manager $customizer
   */
  public function register_pro_dashboard_customizer_settings($customizer) {
    $options = Options::fetch();

    if(!$options->pro_dashboard_enabled) {
      return;
    }

    $customizer->add_section('esaf_pro_dashboard', [
      'title' => __('Easy Affiliate Pro Dashboard', 'easy-affiliate'),
      'capability' => 'administrator',
    ]);

    $colors = [
      'brand_color' => [
        'label' => __('Brand Color', 'easy-affiliate'),
        'default' => '#32373b'
      ],
      'accent_color' => [
        'label' => __('Accent Color', 'easy-affiliate'),
        'default' => '#222629'
      ],
      'menu_text_color' => [
        'label' => __('Menu Text Color', 'easy-affiliate'),
        'default' => '#b7bcc0'
      ],
      'menu_text_highlight_color' => [
        'label' => __('Menu Text Highlight Color', 'easy-affiliate'),
        'default' => '#ffffff'
      ]
    ];

    foreach($colors as $key => $data) {
      $customizer->add_setting(new CustomizeSetting(
        $customizer,
        'esaf_pro_dashboard_' . $key,
        [
          'type' => 'esaf_pro_dashboard_' . $key, // Value unimportant but must be something other than 'option' or 'theme_mod'
          'default' => $data['default'],
          'transport' => 'postMessage',
          'sanitize_callback' => 'sanitize_hex_color'
        ]
      ));

      $customizer->add_control(new \WP_Customize_Color_Control($customizer, 'esaf_pro_dashboard_' . $key, [
        'label' => $data['label'],
        'section' => 'esaf_pro_dashboard'
      ]));
    }

    $customizer->add_setting(new CustomizeSetting(
      $customizer,
      'esaf_pro_dashboard_logo_url',
      [
        'type' => 'esaf_pro_dashboard_logo_url',
        'default' => '',
        'transport' => 'refresh',
        'sanitize_callback' => 'esc_url_raw'
      ]
    ));

    $customizer->add_control(new \WP_Customize_Image_Control($customizer, 'esaf_pro_dashboard_logo_url', [
      'label' => __('Logo', 'easy-affiliate'),
      'section' => 'esaf_pro_dashboard'
    ]));
  }

  public function customizer_live_preview() {
    $options = Options::fetch();

    if(!$options->pro_dashboard_enabled) {
      return;
    }

    wp_enqueue_script('esaf-admin-customizer', ESAF_JS_URL . '/admin-customizer.js', ['jquery'], ESAF_VERSION, true);

    wp_localize_script('esaf-admin-customizer', 'EsafAdminCustomizerL10n', [
      'brand_color_css' => self::get_brand_color_custom_css(),
      'accent_color_css' => self::get_accent_color_custom_css(),
      'menu_text_color_css' => self::get_menu_text_color_custom_css(),
      'menu_text_highlight_color_css' => self::get_menu_text_highlight_color_custom_css()
    ]);
  }
}
