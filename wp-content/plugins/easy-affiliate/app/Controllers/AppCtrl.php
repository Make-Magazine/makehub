<?php

namespace EasyAffiliate\Controllers;

use EasyAffiliate\Helpers\AppHelper;
use EasyAffiliate\Lib\BaseCtrl;
use EasyAffiliate\Lib\Db;
use EasyAffiliate\Lib\Nonces;
use EasyAffiliate\Lib\Report;
use EasyAffiliate\Lib\Track;
use EasyAffiliate\Lib\Utils;
use EasyAffiliate\Models\Creative;
use EasyAffiliate\Models\Options;
use EasyAffiliate\Models\User;

class AppCtrl extends BaseCtrl {
  public function load_hooks() {
    add_action('plugins_loaded', [self::class, 'load_language'], 11);
    add_filter('the_content', [self::class, 'page_route']);
    add_action('admin_enqueue_scripts', [self::class, 'load_admin_scripts'], 5);
    add_action('wp_enqueue_scripts', [self::class, 'load_scripts'], 5000);
    add_action('in_admin_header', [self::class, 'admin_header'], 0);
    add_filter('parent_file', [self::class, 'highlight_menu']);
    add_filter('submenu_file', [self::class, 'highlight_menu_item']);
    add_action('admin_init', [self::class, 'install']); // DB upgrade is handled automatically here now
    add_action('init', [self::class, 'parse_standalone_request'], 5);
    //Because we're setting the nonce in a cookie -- this has to be here otherwise Headers already sent errors will occur
    add_action('template_redirect', [Nonces::class, 'setup_nonce'], 5);
    add_action('template_redirect', [self::class, 'record_generic_affiliate_link'], 5);
    add_action('menu_order', [self::class, 'admin_menu_order']);
    add_action('custom_menu_order', [self::class, 'admin_menu_order']);
    add_action('admin_notices', [self::class, 'configure_options_warning']);
    add_action('wp_dashboard_setup', [self::class, 'add_dashboard_widgets']);
    add_action('template_redirect', [self::class, 'handle_pro_dashboard_redirects'], 5);
    add_filter('template_include', [self::class, 'maybe_override_dashboard_template'], 999999); // High priority so we have the last say here
    add_filter('show_admin_bar', [self::class, 'maybe_hide_admin_bar'], 900);
    add_action('init', [self::class, 'maybe_flush_rewrite_rules'], 999998);
    add_action('init', [self::class, 'maybe_add_affiliatewp_rewrite_rules'], 999999);
    add_filter('redirect_canonical', [self::class, 'maybe_prevent_canonical_redirect'], 0, 2);
  }

  public static function activate() {
    if(!is_user_logged_in() || wp_doing_ajax() || !is_admin() || is_network_admin() || !Utils::is_admin()) {
      return;
    }

    if(Utils::is_post_request() && (isset($_POST['action']) || isset($_POST['action2']))) {
      return; // don't redirect on bulk activation
    }

    global $wpdb;

    wp_cache_flush();
    $wpdb->flush();

    $onboarded = $wpdb->get_var("SELECT option_value FROM {$wpdb->options} WHERE option_name = 'esaf_onboarded'");

    if($onboarded === null) {
      nocache_headers();
      wp_redirect(admin_url('admin.php?page=easy-affiliate-onboarding'), 307);
      exit;
    }
  }

  public static function load_admin_scripts() {
    wp_enqueue_style('esaf-fontello-animation', ESAF_URL . '/fonts/fontello/css/animation.css', [], ESAF_VERSION);
    wp_enqueue_style('esaf-fontello-easy-affiliate', ESAF_URL . '/fonts/fontello/css/easy-affiliate.css', ['esaf-fontello-animation'], ESAF_VERSION);

    $id = Utils::get_current_screen_id();

    if(self::on_easy_affiliate_page() || $id == 'dashboard' || $id == 'users' || $id == 'user-edit' || $id == 'profile' || $id = 'memberpressproduct') {
      $options = Options::fetch();

      if($id == 'dashboard' && apply_filters('esaf_enable_dashboard_widget', true)) {
        wp_enqueue_script('chart-min-js', ESAF_JS_URL . '/library/chart.min.js', ['jquery'], ESAF_VERSION, true);
        wp_enqueue_script('esaf-admin-dashboard-widget', ESAF_JS_URL . '/admin-dashboard-widget.js', ['jquery', 'chart-min-js'], ESAF_VERSION, true);

        wp_localize_script('esaf-admin-dashboard-widget', 'EsafAdminDashboardWidgetL10n', [
          'clicks' => __('Clicks', 'easy-affiliate'),
          'uniques' => __('Uniques', 'easy-affiliate'),
          'sales' => __('Sales', 'easy-affiliate')
        ]);
      }

      wp_enqueue_style('tippy.js',  ESAF_CSS_URL . '/tippy.min.css', [], '6.2.7');
      wp_enqueue_style('magnific-popup', ESAF_CSS_URL . '/magnific-popup.min.css', [], '1.1.0');
      wp_enqueue_style('thickbox');
      wp_enqueue_script('popper', ESAF_JS_URL . '/popper.min.js', [], '2.5.3');
      wp_enqueue_script('tippy.js', ESAF_JS_URL . '/tippy.umd.min.js', ['popper'], '6.2.7');
      wp_enqueue_script('magnific-popup', ESAF_JS_URL . '/jquery.magnific-popup.min.js', ['jquery'], '1.1.0', true);
      wp_enqueue_script('easy-affiliate-admin', ESAF_JS_URL . '/easy-affiliate-admin.js', ['jquery', 'tippy.js']);
      wp_enqueue_script('media-upload');
      wp_enqueue_script('thickbox');
      wp_enqueue_script('suggest');

      wp_localize_script('easy-affiliate-admin', 'EsafAdminL10n', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'install_addon_nonce' => wp_create_nonce('esaf_addons'),
        'install_addon_failed' => __('Could not install add-on. Please download from easyaffiliate.com and install manually.', 'easy-affiliate'),
        'install_addon_success' => __('Add-on installed successfully. Please refresh the page to enable the functionality.', 'easy-affiliate'),
        'activate_addon_success' => __('Add-on activated successfully. Please refresh the page to enable the functionality.', 'easy-affiliate'),
        'tax_id_field_html' => sprintf('<input type="text" id="%1$s" name="%1$s">', $options->business_tax_id_str)
      ]);
    }

    wp_enqueue_style('esaf-admin-shared', ESAF_CSS_URL . '/admin-shared.css', [], ESAF_VERSION);
  }

  public static function admin_header() {
    $screen_id = Utils::get_current_screen_id();

    if(preg_match('/_page_easy-affiliate-settings$/', $screen_id)) {
      return; // We have a custom header on the settings page
    }
    elseif(preg_match('/_page_easy-affiliate-onboarding$/', $screen_id) || preg_match('/_page_easy-affiliate-wizard$/', $screen_id)) {
      return; // We don't need a header on these pages
    }

    $options = Options::fetch();

    if(self::on_easy_affiliate_page()) {
      echo AppHelper::admin_header();

      $nav_items = [];
      $back_link_text = '';
      $back_link_url = '';

      if(apply_filters('esaf_show_reports_nav_items', preg_match('/_page_easy-affiliate$/', $screen_id) || preg_match('/_page_easy-affiliate-clicks$/', $screen_id), $screen_id)) {
        $nav_items[] = [
          'text' => __('Reports', 'easy-affiliate'),
          'url' => admin_url('admin.php?page=easy-affiliate'),
          'active' => preg_match('/_page_easy-affiliate$/', $screen_id)
        ];

        $nav_items[] = [
          'text' => __('Clicks', 'easy-affiliate'),
          'url' => admin_url('admin.php?page=easy-affiliate-clicks'),
          'active' => preg_match('/_page_easy-affiliate-clicks$/', $screen_id)
        ];
      }
      elseif(preg_match('/_page_easy-affiliate-affiliates$/', $screen_id) || in_array($screen_id, ['edit-esaf-application', 'esaf-application'])) {
        if($options->registration_type == 'application') {
          $nav_items[] = [
            'text' => __('Affiliates', 'easy-affiliate'),
            'url' => admin_url('admin.php?page=easy-affiliate-affiliates'),
            'active' => preg_match('/_page_easy-affiliate-affiliates$/', $screen_id)
          ];

          $nav_items[] = [
            'text' => __('Applications', 'easy-affiliate'),
            'url' => admin_url('edit.php?post_type=esaf-application'),
            'active' => in_array($screen_id, ['edit-esaf-application', 'esaf-application'])
          ];

          if($screen_id == 'esaf-application') {
            $back_link_text = __('&larr; Back to Applications', 'easy-affiliate');
            $back_link_url = admin_url('edit.php?post_type=esaf-application');
          }
        }
      }
      elseif(in_array($screen_id, ['edit-esaf-creative', 'esaf-creative', 'edit-esaf-campaign'])) {
        $nav_items[] = [
          'text' => __('Creatives (Links & Banners)', 'easy-affiliate'),
          'url' => admin_url('edit.php?post_type=esaf-creative'),
          'active' => in_array($screen_id, ['edit-esaf-creative', 'esaf-creative'])
        ];

        $nav_items[] = [
          'text' => __('Campaigns', 'easy-affiliate'),
          'url' => admin_url('edit-tags.php?taxonomy=esaf-campaign&post_type=esaf-creative'),
          'active' => in_array($screen_id, ['edit-esaf-campaign'])
        ];

        if($screen_id == 'esaf-creative') {
          $back_link_text = __('&larr; Back to Creatives Listing', 'easy-affiliate');
          $back_link_url = admin_url('edit.php?post_type=esaf-creative');
        }
      }
      elseif(preg_match('/_page_easy-affiliate-transactions$/', $screen_id)) {
        if(isset($_GET['action'])) {
          $back_link_text = __('&larr; Back to Transactions', 'easy-affiliate');
          $back_link_url = admin_url('admin.php?page=easy-affiliate-transactions');
        }
      }
      elseif(preg_match('/_page_easy-affiliate-pay-affiliates$/', $screen_id) || preg_match('/_page_easy-affiliate-payments$/', $screen_id)) {
        $nav_items[] = [
          'text' => __('Pay Affiliates', 'easy-affiliate'),
          'url' => admin_url('admin.php?page=easy-affiliate-pay-affiliates'),
          'active' => preg_match('/_page_easy-affiliate-pay-affiliates$/', $screen_id)
        ];

        $nav_items[] = [
          'text' => __('Payout History', 'easy-affiliate'),
          'url' => admin_url('admin.php?page=easy-affiliate-payments'),
          'active' => preg_match('/_page_easy-affiliate-payments$/', $screen_id)
        ];
      }

      $nav_items = apply_filters('esaf_admin_header_nav_items', $nav_items, $screen_id);

      if(count($nav_items)) {
        echo '<div class="esaf-admin-header-nav">';

        foreach($nav_items as $nav_item) {
          printf(
            '<a href="%1$s"%2$s>%3$s</a>',
            $nav_item['url'],
            $nav_item['active'] ? ' class="esaf-active"' : '',
            $nav_item['text']
          );
        }

        echo '</div>';
      }

      if(!empty($back_link_text) && !empty($back_link_url)) {
        printf(
          '<div class="esaf-admin-header-back"><a href="%1$s">%2$s</a></div>',
          esc_url($back_link_url),
          esc_html($back_link_text)
        );
      }
    }
  }

  public static function on_easy_affiliate_page() {
    static $result;

    if (is_bool($result)) {
      return $result;
    }

    $id = Utils::get_current_screen_id();

    $result = apply_filters(
      'esaf_on_easy_affiliate_page',
      $id == 'toplevel_page_easy-affiliate' ||
      $id == 'esaf-creative' ||
      $id == 'edit-esaf-creative' ||
      $id == 'edit-esaf-campaign' ||
      $id == 'esaf-application' ||
      $id == 'edit-esaf-application' ||
      preg_match('/_page_easy-affiliate-affiliates$/', $id) ||
      preg_match('/_page_easy-affiliate-clicks$/', $id) ||
      preg_match('/_page_easy-affiliate-transactions$/', $id) ||
      preg_match('/_page_easy-affiliate-payments$/', $id) ||
      preg_match('/_page_easy-affiliate-pay-affiliates$/', $id) ||
      preg_match('/_page_easy-affiliate-settings$/', $id) ||
      preg_match('/_page_easy-affiliate-onboarding$/', $id) ||
      preg_match('/_page_easy-affiliate-wizard$/', $id)
    );

    return $result;
  }

  public static function highlight_menu($parent_file) {
    if(self::on_easy_affiliate_page()) {
      $parent_file = 'easy-affiliate';
    }

    return $parent_file;
  }

  public static function highlight_menu_item($submenu_file) {
    // Remove the "Clicks", "Payments", "Onboarding" and "Wizard" menu items
    remove_submenu_page('easy-affiliate', 'easy-affiliate-clicks');
    remove_submenu_page('easy-affiliate', 'easy-affiliate-payments');
    remove_submenu_page('easy-affiliate', 'easy-affiliate-onboarding');
    remove_submenu_page('easy-affiliate', 'easy-affiliate-wizard');

    $screen_id = Utils::get_current_screen_id();

    if(!empty($screen_id) && is_string($screen_id)) {
      if(in_array($screen_id, ['esaf-creative', 'edit-esaf-campaign'])) {
        $submenu_file = 'edit.php?post_type=esaf-creative';
      }
      elseif(in_array($screen_id, ['edit-esaf-application', 'esaf-application'])) {
        $submenu_file = 'easy-affiliate-affiliates';
      }
      elseif(preg_match('/_page_easy-affiliate-payments$/', $screen_id)) {
        $submenu_file = 'easy-affiliate-pay-affiliates';
      }
      elseif(preg_match('/_page_easy-affiliate-clicks$/', $screen_id)) {
        $submenu_file = 'easy-affiliate';
      }
      elseif(preg_match('/_page_easy-affiliate-onboarding$/', $screen_id) || preg_match('/_page_easy-affiliate-wizard$/', $screen_id)) {
        $submenu_file = 'easy-affiliate-settings';
      }
    }

    return $submenu_file;
  }

  public static function setup_menus() {
    add_action('admin_menu', [self::class, 'menu']);
  }

  /********* INSTALL PLUGIN ***********/
  public static function install() {
    $db = Db::fetch();

    $db->upgrade();
  }

  public static function menu() {
    self::admin_separator();

    add_menu_page(
      __('Easy Affiliate', 'easy-affiliate'),
      esc_html__('Easy Affiliate', 'easy-affiliate'),
      'administrator',
      'easy-affiliate',
      [ReportsCtrl::class, 'route'],
      'data:image/svg+xml;base64,' . base64_encode(file_get_contents(ESAF_IMAGES_PATH . '/menu-logo.svg')),
      775877
    );

    add_submenu_page(
      'easy-affiliate',
      __('Reports', 'easy-affiliate'),
      esc_html__('Reports', 'easy-affiliate'),
      'administrator',
      'easy-affiliate',
      [ReportsCtrl::class, 'route']
    );

    add_submenu_page(
      'easy-affiliate',
      __('Clicks', 'easy-affiliate'),
      esc_html__('Clicks', 'easy-affiliate'),
      'administrator',
      'easy-affiliate-clicks',
      [ClicksCtrl::class, 'route']
    ); // This page is removed from the admin menu on a later hook

    do_action('esaf_menu_after_item_1');

    add_submenu_page(
      'easy-affiliate',
      __('Affiliates', 'easy-affiliate'),
      esc_html__('Affiliates', 'easy-affiliate'),
      'administrator',
      'easy-affiliate-affiliates',
      [UsersCtrl::class, 'display_affiliates_list']
    );

    do_action('esaf_menu_after_item_2');

    add_submenu_page(
      'easy-affiliate',
      __('Creatives', 'easy-affiliate'),
      esc_html__('Creatives', 'easy-affiliate'),
      'administrator',
      'edit.php?post_type=esaf-creative',
      false
    );

    do_action('esaf_menu_after_item_3');

    add_submenu_page(
      'easy-affiliate',
      __('Transactions', 'easy-affiliate'),
      esc_html__('Transactions', 'easy-affiliate'),
      'administrator',
      'easy-affiliate-transactions',
      [TransactionsCtrl::class, 'route']
    );

    do_action('esaf_menu_after_item_4');

    add_submenu_page(
      'easy-affiliate',
      __('Pay Affiliates', 'easy-affiliate'),
      esc_html__('Pay Affiliates', 'easy-affiliate'),
      'administrator',
      'easy-affiliate-pay-affiliates',
      [PaymentsCtrl::class, 'route']
    );

    add_submenu_page(
      'easy-affiliate',
      __('Payments', 'easy-affiliate'),
      esc_html__('Payments', 'easy-affiliate'),
      'administrator',
      'easy-affiliate-payments',
      [PaymentsCtrl::class, 'route']
    ); // This page is removed from the admin menu on a later hook

    do_action('esaf_menu_after_item_5');

    add_submenu_page(
      'easy-affiliate',
      __('Settings', 'easy-affiliate'),
      esc_html__('Settings', 'easy-affiliate'),
      'administrator',
      'easy-affiliate-settings',
      [OptionsCtrl::class, 'route']
    );

    do_action('esaf_menu_after_item_6');

    add_submenu_page(
      'easy-affiliate',
      __('Add-ons', 'easy-affiliate'),
      '<span style="color:#8CBD5A;">' . esc_html__('Add-ons', 'easy-affiliate') . '</span>',
      'administrator',
      'easy-affiliate-addons',
      [AddonsCtrl::class, 'route']
    );

    do_action('esaf_menu_after_item_7');

    add_submenu_page(
      'easy-affiliate',
      __('Onboarding', 'easy-affiliate'),
      esc_html__('Onboarding', 'easy-affiliate'),
      'administrator',
      'easy-affiliate-onboarding',
      [OnboardingCtrl::class, 'route']
    ); // This page is removed from the admin menu on a later hook

    add_submenu_page(
      'easy-affiliate',
      __('Wizard', 'easy-affiliate'),
      esc_html__('Wizard', 'easy-affiliate'),
      'administrator',
      'easy-affiliate-wizard',
      [WizardCtrl::class, 'route']
    ); // This page is removed from the admin menu on a later hook

    do_action('esaf_menu');
  }

  /**
   * Add a separator to the WordPress admin menus
   */
  public static function admin_separator() {
    // Prevent duplicate separators when no core menu items exist
    if ( !current_user_can( 'manage_options' ) )
      return;

    global $menu;
    $menu[] = ['', 'read', 'separator-easy-affiliate', '', 'wp-menu-separator easy-affiliate'];
  }

  /**
   * Move our custom separator above our admin menu
   *
   * @param array $menu_order Menu Order
   * @return array Modified menu order
   */
  public static function admin_menu_order( $menu_order ) {
    if( !$menu_order )
      return true;

    if( !is_array( $menu_order ) )
      return $menu_order;

    // Initialize our custom order array
    $new_menu_order = [];

    // Menu values
    $second_sep   = 'separator2';
    $custom_menus = ['separator-easy-affiliate', 'easy-affiliate'];

    // Loop through menu order and do some rearranging
    foreach( $menu_order as $item ) {

      // Position Easy Affiliate menus above appearance
      if( $second_sep == $item ) {

        // Add our custom menus
        foreach( $custom_menus as $custom_menu ) {
          if( array_search( $custom_menu, $menu_order ) ) {
            $new_menu_order[] = $custom_menu;
          }
        }

        // Add the appearance separator
        $new_menu_order[] = $second_sep;

      // Skip our menu items down below
      }
      elseif( !in_array( $item, $custom_menus ) ) {
        $new_menu_order[] = $item;
      }
    }

    // Return our custom order
    return $new_menu_order;
  }

  // Routes for wordpress pages -- we're just replacing content here folks.
  public static function page_route($content) {
    global $post;
    $options = Options::fetch();

    //Setup the $current_post and account for non-singular views
    if(in_the_loop())
      $current_post = get_post(get_the_ID());
    else
      $current_post = $post;

    //Fix for lots of things probably, but mostly the lack of this check was causing issues in OptimizePress
    if(!isset($current_post->ID) || !$current_post->ID) { return $content; }

    //WARNING the_content CAN be run more than once per page load
    //so this static var prevents stuff from happening twice
    //like cancelling a subscr or resuming etc...
    static $already_run = [];
    static $new_content = [];
    static $content_length = [];
    //Init this posts static values
    if(!isset($new_content[$current_post->ID]) || empty($new_content[$current_post->ID])) {
      $already_run[$current_post->ID] = false;
      $new_content[$current_post->ID] = '';
      $content_length[$current_post->ID] = -1;
    }

    if($already_run[$current_post->ID] && strlen(trim($content)) == $content_length[$current_post->ID]) {
      return $new_content[$current_post->ID];
    }

    $content_length[$current_post->ID] = strlen(trim($content));
    $already_run[$current_post->ID] = true;

    if(apply_filters('esaf-stop-page-route', false)) {
      $new_content[$current_post->ID] = $content;
      return $new_content[$current_post->ID];
    }

    switch($current_post->ID) {
      case $options->dashboard_page_id:
        if(post_password_required($current_post)) {
          $new_content[$current_post->ID] = $content;
          return $new_content[$current_post->ID];
        }

        // If the dashboard shortcode is in the content, just return the content
        if(preg_match('/\[esaf_dashboard/', $content)) {
          $new_content[$current_post->ID] = $content;
          return $new_content[$current_post->ID];
        }

        ob_start();

        $dashboard_ctrl = new DashboardCtrl();
        $dashboard_ctrl->route();

        $content .= ob_get_clean();
        break;
      case $options->login_page_id:
        if( post_password_required($current_post) ) {
          $new_content[$current_post->ID] = $content;
          return $new_content[$current_post->ID];
        }

        // If the login shortcode is in the content, just return the content
        if(preg_match('/\[esaf_login/', $content)) {
          $new_content[$current_post->ID] = $content;
          return $new_content[$current_post->ID];
        }

        ob_start();

        $login_ctrl = new LoginCtrl();
        $login_ctrl->route();

        $content .= ob_get_clean();
        break;
      case $options->signup_page_id:
        if( post_password_required($current_post) ) {
          $new_content[$current_post->ID] = $content;
          return $new_content[$current_post->ID];
        }

        // If the signup shortcode is in the content, just return the content
        if(preg_match('/\[esaf_signup/', $content)) {
          $new_content[$current_post->ID] = $content;
          return $new_content[$current_post->ID];
        }

        ob_start();

        $signup_ctrl = new SignupCtrl();
        $signup_ctrl->route();

        $content .= ob_get_clean();
        break;
    }

    $new_content[$current_post->ID] = $content;
    return $new_content[$current_post->ID];
  }

  public static function load_scripts() {
    global $post;
    $options = Options::fetch();

    if(!$post instanceof \WP_Post) {
      return;
    }

    if($post->ID == $options->dashboard_page_id ||
       $post->ID == $options->signup_page_id ||
       $post->ID == $options->login_page_id ||
       preg_match('~\[(wafp|esaf)[-_]~',$post->post_content)
    ) {
      if(Utils::is_pro_dashboard_page()) {
        global $wp_styles;

        foreach($wp_styles->queue as $style) {
          if($style != 'wp-block-library') {
            $handle = $wp_styles->registered[$style]->handle;
            wp_deregister_style($handle);
            wp_dequeue_style($handle);
          }
        }

        wp_enqueue_style('modern-normalize', ESAF_CSS_URL . '/modern-normalize.min.css', [], '1.0.0');
      }

      $magnific_popup_handle = Utils::is_pro_dashboard_page() ? 'esaf-magnific-popup' : 'magnific-popup';

      wp_register_style('easy-affiliate-font-animation', ESAF_URL . '/fonts/fontello/css/animation.css', [], ESAF_VERSION);
      wp_register_style('easy-affiliate-fonts', ESAF_URL . '/fonts/fontello/css/easy-affiliate.css', ['easy-affiliate-font-animation'], ESAF_VERSION);
      wp_register_style($magnific_popup_handle, ESAF_CSS_URL . '/magnific-popup.min.css', [], '1.1.0');
      wp_register_style('primer-tooltips', ESAF_CSS_URL . '/tooltips.min.css', [], '15.1.0');
      wp_enqueue_style('esaf-affiliate-grid-css', ESAF_CSS_URL . '/library/easy-affiliate-grid.css', [], ESAF_VERSION);
      wp_enqueue_style('easy-affiliate',  ESAF_CSS_URL . '/easy-affiliate.css', ['easy-affiliate-fonts', $magnific_popup_handle, 'primer-tooltips'], ESAF_VERSION);

      if(Utils::is_pro_dashboard_page()) {
        wp_enqueue_style('esaf-pro-dashboard',  ESAF_CSS_URL . '/pro-dashboard.css', [], ESAF_VERSION);
        $custom_css = DashboardCtrl::get_pro_dashboard_custom_css();

        if($custom_css) {
          wp_add_inline_style('esaf-pro-dashboard', $custom_css);
        }
      }

      wp_register_script('clipboard-js', ESAF_JS_URL . '/clipboard.min.js', [], '2.0.6');
      wp_register_script($magnific_popup_handle, ESAF_JS_URL . '/jquery.magnific-popup.min.js', ['jquery'], '1.1.0');
      wp_enqueue_script('momentjs', ESAF_JS_URL . '/library/moment.min.js', ['jquery'], ESAF_VERSION);
      wp_enqueue_script('chart-min-js', ESAF_JS_URL . '/library/chart.min.js', ['jquery', 'momentjs'], ESAF_VERSION);
      wp_enqueue_script('date-range-picker', ESAF_JS_URL . '/library/daterangepicker.min.js', ['jquery'], ESAF_VERSION);
      wp_enqueue_style('esaf-affiliate-grid-css', ESAF_CSS_URL . '/library/easy-affiliate-grid.css', [], ESAF_VERSION);
      wp_enqueue_style('date-range-picker', ESAF_CSS_URL . '/library/daterangepicker.css', [], ESAF_VERSION);
      wp_enqueue_script('easy-affiliate', ESAF_JS_URL . '/easy-affiliate.js', ['jquery', 'clipboard-js', $magnific_popup_handle], ESAF_VERSION);

      wp_localize_script('easy-affiliate', 'EsafL10n', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'copied' => __('Copied!', 'easy-affiliate'),
        // translators: %s: copy button combo e.g. Ctrl-C
        'press_to_copy' => __('Press %s to copy', 'easy-affiliate'),
        'no_support' => __('No support :(', 'easy-affiliate'),
        'error_creating_custom_link' => __('An error occurred creating the custom link', 'easy-affiliate'),
        'error_updating_custom_link' => __('An error occurred updating the custom link', 'easy-affiliate')
      ]);
    }

    if($post->ID == $options->signup_page_id
       || preg_match('~\[\s*esaf_affiliate_application~',$post->post_content)
       || preg_match('~\[\s*esaf_signup~',$post->post_content)
    ) {
      wp_register_script('easy-affiliate-validate', ESAF_JS_URL . '/validate.js', [], ESAF_VERSION);
      wp_enqueue_script('easy-affiliate-signup', ESAF_JS_URL . '/signup.js', ['jquery', 'easy-affiliate-validate'], ESAF_VERSION);
    }
  }

  // The tight way to process standalone requests dogg...
  public static function parse_standalone_request() {
    $options = Options::fetch();

    $plugin     = isset($_REQUEST['plugin'])?$_REQUEST['plugin']:'';
    $action     = isset($_REQUEST['action'])?$_REQUEST['action']:'';
    $controller = isset($_REQUEST['controller'])?$_REQUEST['controller']:'';

    $request_uri = $_SERVER['REQUEST_URI'];

    // TRIM PARAMS FROM REQUEST_URI
    $request_uri = preg_replace('#\?.*#','',$_SERVER['REQUEST_URI']);
    preg_match('#^https?://[^/]+(/.*)?#', home_url(), $matches);
    $pre_slug = isset($matches[1])?$matches[1]:'';

    if(!empty($plugin) && $plugin == 'wafp' && !empty($controller) && !empty($action)) {
      self::standalone_route($controller, $action);
      exit;
    }
    else if(isset($_POST) && isset($_POST['wafp_process_login_form'])) {
      $login_ctrl = new LoginCtrl();
      $login_ctrl->process_login_form();
    }
    else if($options->pretty_affiliate_links &&
            preg_match('#^'.$pre_slug.'/([^/]*)/([^/]*)/?$#', $request_uri, $matches) &&
            isset($matches[1]) && isset($matches[2]) && !empty($matches[2]) &&
            ($affiliate_id = User::get_aff_id_from_string($matches[1])) &&
            ($creative = Creative::get_one_by_slug($matches[2]))) {
      Track::redirect($creative->ID, $affiliate_id);
    }
  }

  // Routes for standalone / ajax requests
  public static function standalone_route($controller, $action) {
    if($controller == 'links') {
      if($action == 'redirect') { //Deprecated
        // accept an id or slug for the creative
        if($creative = Creative::get_one_by_slug(AppCtrl::get_param('l'))) {
          // accept an id or username for the affiliate
          $affiliate_id = username_exists(urldecode(AppCtrl::get_param('a')));

          if(!$affiliate_id) {
            $affiliate_id = urldecode(AppCtrl::get_param('a'));
          }

          Track::redirect($creative->ID, $affiliate_id);
        }
      }
    }
    else if($controller == 'reports') {
      if(current_user_can('administrator')) {
        if($action == 'admin_affiliate_payments') {
          PaymentsCtrl::admin_affiliates_owed(AppCtrl::get_param('period'));
        }
      }
    }
    else if($controller == 'payments') {
      if(!current_user_can('administrator')) {
        return;
      }

      if($action == 'paypal_bulk_file') {
        PaymentsCtrl::admin_paypal_bulk_file(AppCtrl::get_param('id'));
      }
      elseif($action == 'manual_bulk_csv_download') {
        PaymentsCtrl::admin_manual_bulk_csv_download(AppCtrl::get_param('id'));
      }
    }
    else {
      do_action('esaf_process_route');
    }
  }

  public static function load_language() {
    $paths = array();
    $paths[] = str_replace(wp_normalize_path(WP_PLUGIN_DIR), '', wp_normalize_path(ESAF_I18N_PATH));

    //Have to use WP_PLUGIN_DIR because load_plugin_textdomain doesn't accept abs paths
    if(!file_exists(WP_PLUGIN_DIR . '/' . 'esaf-i18n')) {
      @mkdir(WP_PLUGIN_DIR . '/' . 'esaf-i18n');

      if(file_exists(WP_PLUGIN_DIR . '/' . 'esaf-i18n'))
        $paths[] = '/esaf-i18n';
    }
    else {
      $paths[] = '/esaf-i18n';
    }

    $paths = apply_filters('esaf-textdomain-paths', $paths);

    foreach($paths as $path) {
      load_plugin_textdomain('easy-affiliate', false, $path);
    }
  }

  // Utility function to grab the parameter whether it's a get or post
  public static function get_param($param, $default='') {
    if(!isset($_REQUEST) or empty($_REQUEST) or !isset($_REQUEST[$param]))
      return $default;

    return $_REQUEST[$param];
  }

  public static function get_param_delimiter_char($link) {
    return ((preg_match("#\?#",$link))?'&':'?');
  }

  public static function configure_options_warning() {
    $options = Options::fetch();

    if(!$options->setup_complete) {
      require ESAF_VIEWS_PATH . '/shared/must_configure.php';
    }
  }

  public static function add_dashboard_widgets() {
    if(!Utils::is_admin() || !apply_filters('esaf_enable_dashboard_widget', true)) {
      return;
    }

    try {
      $start = new \DateTimeImmutable('-6 days', new \DateTimeZone('UTC'));
      $end = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
      $stats = Report::get_report_stats_in_period($start, $end);

      wp_add_dashboard_widget(
        'esaf_weekly_stats_widget',
        esc_html__('Easy Affiliate Weekly Stats', 'easy-affiliate'),
        function () use ($stats) {
          require ESAF_VIEWS_PATH . '/reports/weekly_stats.php';
        }
      );
    } catch (\Exception $e) {
      // Skip displaying the widget if there was an exception
    }

    // Globalize the metaboxes array, this holds all the widgets for wp-admin

    global $wp_meta_boxes;

    // Get the regular dashboard widgets array
    // (which has our new widget already but at the end)

    $normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];

    // Backup and delete our new dashboard widget from the end of the array

    $esaf_weekly_stats_widget_backup = ['esaf_weekly_stats_widget' => $normal_dashboard['esaf_weekly_stats_widget']];
    unset($normal_dashboard['esaf_weekly_stats_widget']);

    // Merge the two arrays together so our widget is at the beginning

    $sorted_dashboard = array_merge($esaf_weekly_stats_widget_backup, $normal_dashboard);

    // Save the sorted array back into the original metaboxes

    $wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;
  }

  public static function record_generic_affiliate_link() {
    $options = Options::fetch();
    $affiliate_id = Track::get_affiliate_id();

    if(!empty($affiliate_id)) {
      if(isset($_REQUEST['p'])) {
        $creative_id_or_slug = sanitize_text_field(wp_unslash($_REQUEST['p']));

        if(is_numeric($creative_id_or_slug)) {
          $creative = new Creative($creative_id_or_slug);
        }
        else {
          $creative = Creative::get_one_by_slug($creative_id_or_slug);
        }

        if($creative instanceof Creative && !empty($creative->ID)) {
          Track::redirect($creative->ID, $affiliate_id); // this exits too
        }
      }

      Track::click($affiliate_id);

      if(isset($_GET['aff'])) {
        $target_url = remove_query_arg('aff', Utils::current_url());

        if($options->custom_default_redirect) {
          $target_url = $options->custom_default_redirect_url;
        }

        if(!apply_filters('esaf_disable_default_redirect', false)) {
          Utils::wp_redirect(esc_url_raw(apply_filters('esaf_affiliate_target_url', $target_url, $affiliate_id, false)));
          exit;
        }
      }
    }
  }

  /**
   * Handle the redirects between the different Pro Dashboard pages depending on the affiliate status
   *
   * @return void
   */
  public static function handle_pro_dashboard_redirects() {
    $options = Options::fetch();
    $user = Utils::get_currentuserinfo();
    $logged_in = $user instanceof User;

    if($options->pro_dashboard_enabled) {
      if(Utils::is_dashboard_page()) {
        if(!$logged_in) {
          $args = [];

          if(isset($_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI'])) {
            $redirect_to = set_url_scheme('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);

            if($redirect_to != Utils::dashboard_url()) {
              $args['redirect_to'] = urlencode($redirect_to);
            }
          }

          wp_redirect(Utils::login_url($args));
          exit;
        }
        elseif(!$user->is_affiliate && !Utils::is_admin()) {
          wp_redirect(Utils::signup_url());
          exit;
        }
      }
      elseif(Utils::is_login_page()) {
        if($logged_in && $user->is_affiliate) {
          wp_redirect(Utils::dashboard_url());
          exit;
        }
        elseif($logged_in && !$user->is_affiliate) {
          wp_redirect(Utils::signup_url());
          exit;
        }
      }
      elseif(Utils::is_signup_page() && $logged_in && $user->is_affiliate) {
        wp_redirect(Utils::dashboard_url());
        exit;
      }
    }
  }

  /**
   * Override the template if we are on the Pro Dashboard page
   *
   * @param string $template
   * @return string
   */
  public static function maybe_override_dashboard_template($template) {
    $options = Options::fetch();

    if($options->pro_dashboard_enabled) {
      if(Utils::is_dashboard_page()) {
        $template = ESAF_VIEWS_PATH . '/pro-dashboard/dashboard.php';
      }
      elseif(Utils::is_signup_page()) {
        $template = ESAF_VIEWS_PATH . '/pro-dashboard/signup.php';
      }
      elseif(Utils::is_login_page()) {
        $template = ESAF_VIEWS_PATH . '/pro-dashboard/login.php';
      }
    }

    return $template;
  }

  /**
   * Hide the admin bar if we are on the Pro Dashboard page
   *
   * @param bool $show_admin_bar
   * @return bool
   */
  public static function maybe_hide_admin_bar($show_admin_bar) {
    if(Utils::is_pro_dashboard_page()) {
      $show_admin_bar = false;
    }

    return $show_admin_bar;
  }

  /**
   * Maybe flush rewrite rules
   */
  public static function maybe_flush_rewrite_rules() {
    if(get_option('esaf_flush_rewrite_rules')) {
      flush_rewrite_rules();
      delete_option('esaf_flush_rewrite_rules');
    }
  }

  /**
   * Add the rewrite rules necessary to support pretty affiliate links if we've migrated from AffiliateWP
   */
  public static function maybe_add_affiliatewp_rewrite_rules() {
    $ref = get_option('esaf_affiliate_wp_referral_var');

    if(empty($ref) || apply_filters('esaf_disable_affiliatewp_rewrites', false)) {
      return;
    }

    $taxonomies = get_taxonomies(['public' => true, '_builtin' => false], 'objects');

    foreach($taxonomies as $tax_id => $tax) {
      if(is_array($tax->rewrite) && isset($tax->rewrite['slug'])) {
        add_rewrite_rule($tax->rewrite['slug'] . '/(.+?)/' . $ref . '(/(.*))?/?$', 'index.php?' . $tax_id . '=$matches[1]&' . $ref . '=$matches[3]', 'top');
      }
    }

    add_rewrite_endpoint($ref, EP_PERMALINK | EP_ROOT | EP_COMMENTS | EP_SEARCH | EP_PAGES | EP_ALL_ARCHIVES, false);

    $options = Options::fetch();

    if(in_array('woocommerce', $options->integration) && function_exists('wc_get_page_id')) {
      if($shop_page_id = wc_get_page_id('shop')) {
        $uri = get_page_uri($shop_page_id);

        add_rewrite_rule($uri . '/' . $ref . '(/(.*))?/?$', 'index.php?post_type=product&' . $ref . '=$matches[2]', 'top');
      }
    }

    if(in_array('easy_digital_downloads', $options->integration)) {
      $download_pt = get_post_type_object('download');

      if($download_pt instanceof \WP_Post_Type) {
        if(!empty($download_pt->rewrite['slug'])) {
          $slug = $download_pt->rewrite['slug'];
        }
        else {
          $slug = 'downloads';
        }

        add_rewrite_rule($slug . '/' . $ref . '(/(.*))?/?$', 'index.php?post_type=download&' . $ref . '=$matches[2]', 'top');
      }
    }
  }

  /**
   * Prevent a canonical redirect on the home page to support pretty affiliate links if we've migrated from AffiliateWP
   *
   * @param string $redirect_url
   * @param string $requested_url
   * @return string
   */
  public static function maybe_prevent_canonical_redirect($redirect_url, $requested_url) {
    if(!is_front_page()) {
      return $redirect_url;
    }

    $ref = get_option('esaf_affiliate_wp_referral_var');

    if(empty($ref) || apply_filters('esaf_disable_affiliatewp_rewrites', false)) {
      return $redirect_url;
    }

    $var = get_query_var($ref);

    if(!empty($var) || false !== strpos($requested_url, $ref)) {
      $redirect_url = $requested_url;
    }

    return $redirect_url;
  }
}
