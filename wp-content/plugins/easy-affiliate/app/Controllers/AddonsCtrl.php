<?php

namespace EasyAffiliate\Controllers;

use EasyAffiliate\Lib\AddonInstallSkin;
use EasyAffiliate\Lib\BaseCtrl;

class AddonsCtrl extends BaseCtrl {
  public function load_hooks() {
    add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
    add_action('wp_ajax_esaf_addon_activate', [$this, 'ajax_addon_activate']);
    add_action('wp_ajax_esaf_addon_deactivate', [$this, 'ajax_addon_deactivate']);
    add_action('wp_ajax_esaf_addon_install', [$this, 'ajax_addon_install']);
  }

  public function enqueue_scripts($hook) {
    if(preg_match('/_page_easy-affiliate-addons$/', $hook)) {
      wp_enqueue_style('esaf-addons-css', ESAF_CSS_URL . '/admin-addons.css', [], ESAF_VERSION);
      wp_enqueue_script('list-js', ESAF_JS_URL . '/list.min.js', [], '1.5.0');
      wp_enqueue_script('jquery-match-height', ESAF_JS_URL . '/jquery.matchHeight-min.js', [], '0.7.2');
      wp_enqueue_script('esaf-addons-js', ESAF_JS_URL . '/admin_addons.js', ['list-js', 'jquery-match-height'], ESAF_VERSION);

      wp_localize_script('esaf-addons-js', 'EsafAddons', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('esaf_addons'),
        'active' => __('Active', 'easy-affiliate'),
        'inactive' => __('Inactive', 'easy-affiliate'),
        'activate' => __('Activate', 'easy-affiliate'),
        'deactivate' => __('Deactivate', 'easy-affiliate'),
        'install_failed' => __('Could not install add-on. Please download from easyaffiliate.com and install manually.', 'easy-affiliate'),
        'plugin_install_failed' => __('Could not install plugin. Please download and install manually.', 'easy-affiliate'),
      ]);
    }
  }

  public static function route() {
    $force = isset($_GET['refresh']) && $_GET['refresh'] == 'true';
    $addons = UpdateCtrl::addons(true, $force, true);
    $plugins = get_plugins();

    require ESAF_VIEWS_PATH . '/admin/addons/addons.php';
  }

  public function ajax_addon_activate() {
    if(!isset($_POST['plugin'])) {
      wp_send_json_error(__('Bad request.', 'easy-affiliate'));
    }

    if(!current_user_can('activate_plugins')) {
      wp_send_json_error(__('Sorry, you don\'t have permission to do this.', 'easy-affiliate'));
    }

    if(!check_ajax_referer('esaf_addons', false, false)) {
      wp_send_json_error(__('Security check failed.', 'easy-affiliate'));
    }

    $result = activate_plugins(wp_unslash($_POST['plugin']));
    $type = isset($_POST['type']) ? sanitize_key($_POST['type']) : 'add-on';

    if(is_wp_error($result)) {
      if($type == 'plugin') {
        wp_send_json_error(__('Could not activate plugin. Please activate from the Plugins page manually.', 'easy-affiliate'));
      } else {
        wp_send_json_error(__('Could not activate add-on. Please activate from the Plugins page manually.', 'easy-affiliate'));
      }
    }

    if($type == 'plugin') {
      wp_send_json_success(__('Plugin activated.', 'easy-affiliate'));
    } else {
      wp_send_json_success(__('Add-on activated.', 'easy-affiliate'));
    }
  }

  public function ajax_addon_deactivate() {
    if(!isset($_POST['plugin'])) {
      wp_send_json_error(__('Bad request.', 'easy-affiliate'));
    }

    if(!current_user_can('deactivate_plugins')) {
      wp_send_json_error(__('Sorry, you don\'t have permission to do this.', 'easy-affiliate'));
    }

    if(!check_ajax_referer('esaf_addons', false, false)) {
      wp_send_json_error(__('Security check failed.', 'easy-affiliate'));
    }

    deactivate_plugins(wp_unslash($_POST['plugin']));
    $type = isset($_POST['type']) ? sanitize_key($_POST['type']) : 'add-on';

    if($type == 'plugin') {
      wp_send_json_success(__('Plugin deactivated.', 'easy-affiliate'));
    } else {
      wp_send_json_success(__('Add-on deactivated.', 'easy-affiliate'));
    }
  }

  public function ajax_addon_install() {
    if(!isset($_POST['plugin'])) {
      wp_send_json_error(__('Bad request.', 'easy-affiliate'));
    }

    if(!current_user_can('install_plugins') || !current_user_can('activate_plugins')) {
      wp_send_json_error(__('Sorry, you don\'t have permission to do this.', 'easy-affiliate'));
    }

    if(!check_ajax_referer('esaf_addons', false, false)) {
      wp_send_json_error(__('Security check failed.', 'easy-affiliate'));
    }

    $type = isset($_POST['type']) ? sanitize_key($_POST['type']) : 'add-on';

    if($type == 'plugin') {
      $error = esc_html__('Could not install plugin. Please download and install manually.', 'easy-affiliate');
    } else {
      $error = esc_html__('Could not install add-on. Please download from easyaffiliate.com and install manually.', 'easy-affiliate');
    }

    // Set the current screen to avoid undefined notices
    set_current_screen('easy-affiliate_page_easy-affiliate-addons');

    // Prepare variables
    $url = esc_url_raw(
      add_query_arg(
        [
          'page' => 'easy-affiliate-addons',
        ],
        admin_url('admin.php')
      )
    );

    $creds = request_filesystem_credentials($url, '', false, false, null);

    // Check for file system permissions
    if(false === $creds) {
      wp_send_json_error($error);
    }

    if(!WP_Filesystem($creds)) {
      wp_send_json_error($error);
    }

    // We do not need any extra credentials if we have gotten this far, so let's install the plugin
    require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

    // Do not allow WordPress to search/download translations, as this will break JS output
    remove_action('upgrader_process_complete', ['Language_Pack_Upgrader', 'async_upgrade'], 20);

    // Create the plugin upgrader with our custom skin
    $installer = new \Plugin_Upgrader(new AddonInstallSkin());

    $plugin = wp_unslash($_POST['plugin']);
    $installer->install($plugin);

    // Flush the cache and return the newly installed plugin basename
    wp_cache_flush();

    if($installer->plugin_info()) {
      $plugin_basename = $installer->plugin_info();

      // Activate the plugin silently
      $activated = activate_plugin($plugin_basename);

      if(!is_wp_error($activated)) {
        wp_send_json_success(
          [
            'message'   => $type == 'plugin' ? __('Plugin installed & activated.', 'easy-affiliate') : __('Add-on installed & activated.', 'easy-affiliate'),
            'activated' => true,
            'basename'  => $plugin_basename
          ]
        );
      } else {
        wp_send_json_success(
          [
            'message'   => $type == 'plugin' ? __('Plugin installed.', 'easy-affiliate') : __('Add-on installed.', 'easy-affiliate'),
            'activated' => false,
            'basename'  => $plugin_basename
          ]
        );
      }
    }

    wp_send_json_error($error);
  }
}
