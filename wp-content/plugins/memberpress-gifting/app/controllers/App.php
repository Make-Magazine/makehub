<?php
namespace memberpress\gifting\controllers;

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

use memberpress\gifting as base;
use memberpress\gifting\lib as lib;
use memberpress\gifting\helpers as helpers;
use memberpress\gifting\controllers\admin as ctrl;
use memberpress\gifting\models as models;

class App extends lib\BaseCtrl {
  public function load_hooks() {
    add_action('mepr_enqueue_scripts', array($this, 'enqueue_scripts'), 10, 3);
    add_action('admin_enqueue_scripts', array($this, 'load_admin_scripts'), 10);
    add_filter('mepr_view_get_string', array($this, 'add_transaction_filter_type'), 10, 3);
    add_filter('mepr-list-table-joins', array($this, 'transaction_filter_type_joins'));
    add_filter('mepr-list-table-args', array($this, 'transaction_filter_type_args'));
    add_filter('mepr-bypass-user-roles-setup', array($this, 'is_gifted_transaction'), 10, 4);
    add_action( 'plugins_loaded', array($this, 'load_language') ); // Must load here or it won't work with PolyLang etc
  }

  /**
   * Joins for Admin Transactions page filter Type
   * @param mixed $joins
   *
   * @return array
   */
  public function transaction_filter_type_joins($joins){
    $mepr_db = new \MeprDb();
    $params=$_GET;

    if(isset($params['type']) && $params['type'] != 'all') {
      $joins[] = "/* IMPORTANT */ LEFT JOIN {$mepr_db->transaction_meta} AS tr_meta ON tr.id=tr_meta.transaction_id";
    }
    return $joins;
  }


  /**
   * Args for Admin Transactions page filter Type
   * @param mixed $joins
   *
   * @return array
   */
  public function transaction_filter_type_args($args){
    global $wpdb;
    $mepr_db = new \MeprDb();
    $params=$_GET;

    if(isset($params['type']) && $params['type'] != 'all') {
      if('purchased' == $params['type']){
        $args[] = $wpdb->prepare("tr_meta.meta_key=%s AND tr_meta.meta_value IN (%s, %s)", models\Gift::$status_str, models\Gift::$unclaimed_str, models\Gift::$claimed_str);
      }
      if('claimed' == $params['type']){
        $args[] = $wpdb->prepare("tr_meta.meta_key=%s AND tr_meta.meta_value >= 1", models\Gift::$gifter_txn_str);
      }

    }
    return $args;
  }


  /**
   * @param mixed $view
   * @param mixed $slug
   * @param mixed $vars
   *
   * @return [type]
   */
  public function add_transaction_filter_type($view, $slug, $vars){

    if('/admin/transactions/search_box' == $slug){
      $search = '<select class="mepr_filter_field" id="gateway">';
      $types = (isset($_REQUEST['type'])?$_REQUEST['type']:'all');

      \ob_start();
        require_once(base\VIEWS_PATH . '/admin/transactions/search_box.php');
      $html = \ob_get_clean();

      $view = \str_replace($search, $html . $search, $view);
    }
    return $view;
  }

  /**
  * Enqueue scripts for plugin
  * @see load_hooks(), add_action('admin_enqueue_scripts')
  * @param string $hook Current admin page
  */
  public static function enqueue_scripts($is_product_page, $is_group_page, $is_account_page) {
    // global $post;
    $mepr_options = \MeprOptions::fetch();

    if($is_group_page || $is_product_page || $is_account_page) {
      wp_enqueue_style('mpgft-signup', base\CSS_URL . '/signup.css', array(), base\VERSION );
      wp_enqueue_script( 'mpgft-signup', base\JS_URL . '/signup.js', array('mp-signup'), base\VERSION );
    }

    if($is_account_page) {
      $popup_ctrl = new \MeprPopupCtrl();

      wp_register_style('jquery-magnific-popup', $popup_ctrl->popup_css);
      wp_register_style('mpgft-clipboardtip', base\CSS_URL . '/tooltipster.bundle.min.css', array(), base\VERSION );
      wp_register_style('mpgft-clipboardtip-borderless', base\CSS_URL . '/tooltipster-sideTip-borderless.min.css', array('mpgft-clipboardtip'), base\VERSION );
      wp_enqueue_style('mpgft-account', base\CSS_URL . '/account.css', array('jquery-magnific-popup', 'mpgft-clipboardtip-borderless', 'jquery-magnific-popup'), base\VERSION );

      wp_register_script('jquery-magnific-popup', $popup_ctrl->popup_js, array('jquery'));
      wp_register_script( 'mpgft-clipboard-js', base\JS_URL . '/clipboard.min.js', array(), base\VERSION );
      wp_register_script( 'mpgft-tooltipster', base\JS_URL . '/tooltipster.bundle.min.js', array('jquery'), base\VERSION );
      wp_enqueue_script( 'mpgft-copy-to-clipboard', base\JS_URL . '/copy_to_clipboard.js', array('mpgft-clipboard-js','mpgft-tooltipster'), base\VERSION );
      wp_localize_script( 'mpgft-copy-to-clipboard', 'MeprClipboard', array(
        'copy_text' => __('Copy to Clipboard', 'memberpress-gifting'),
        'copied_text' => __('Copied!', 'memberpress-gifting'),
        'copy_error_text' => __('Oops, Copy Failed!', 'memberpress-gifting'),
      ));
      wp_enqueue_script( 'mpgft-account', base\JS_URL . '/account.js', array('jquery-magnific-popup', 'mpgft-copy-to-clipboard'), base\VERSION );
    }
  }


  public function load_admin_scripts( $hook ) {
    global $post;
    if ( $hook == 'post-new.php' || $hook == 'post.php' ) {
      if ( \MeprProduct::$cpt === $post->post_type ) {
        wp_enqueue_script( 'mpgft-product', base\JS_URL . '/product.js', array(), base\VERSION );
      }
    }
  }


  /**
   * Language files
   * @return [type]
   */
  public function load_language() {
    $path_from_plugins_folder = \memberpress\gifting\PLUGIN_NAME . '/i18n/';
    load_plugin_textdomain( \memberpress\gifting\PLUGIN_NAME, false, $path_from_plugins_folder );
    load_plugin_textdomain( \memberpress\gifting\PLUGIN_NAME, false, '/mepr-i18n' );
  }

  public function is_gifted_transaction( $bool, $obj, $sub_status, $wp_user ) {
    if($obj instanceof \MeprTransaction){
      if( isset($_POST['mpgft-signup-gift-checkbox']) && "on" == $_POST['mpgft-signup-gift-checkbox'] ) {
        return true; // Transaction is a gift.
      }

      $gifter_id = (int) $obj->get_meta( models\Gift::$gifter_id_str, true);
      if($gifter_id > 0 || $obj->get_meta( models\Gift::$is_gift_complete_str, true )  || $obj->get_meta( models\Gift::$is_gift_pending_str, true ) ){
        return true; // Transaction is a gift.
      }
    }

    return $bool;
  }

}
