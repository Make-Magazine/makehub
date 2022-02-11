<?php
namespace memberpress\gifting\controllers;

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

use memberpress\gifting as base;
use memberpress\gifting\lib as lib;
use memberpress\gifting\models as models;
use memberpress\gifting\helpers as helpers;
use memberpress\gifting\emails as emails;

class Account extends lib\BaseCtrl {
  public function load_hooks() {
    add_action('mepr_account_nav', array($this, 'gifts_nav'));
    add_action('mepr_account_nav_content', array($this, 'gifts_list'));
    add_action('mepr-txn-status-refunded', array($this, 'handle_txn_refund'));
    add_action('wp_ajax_mpgft_send_gift_email', array($this, 'send_gift_email'));

    add_action('mepr_recurring_subscriptions_table_joins', array($this, 'recurring_subscriptions_table_joins'));
    add_action('mepr_recurring_subscriptions_table_args', array($this, 'recurring_subscriptions_table_args'));
    add_action('mepr_nonrecurring_subscriptions_table_joins', array($this, 'nonrecurring_subscriptions_table_joins'));
    add_action('mepr_nonrecurring_subscriptions_table_args', array($this, 'nonrecurring_subscriptions_table_args'));
  }

  /**
   * Join Subscription Meta table to remove Gift subscriptions from Account->Subscriptions tab
   *
   * @param mixed $joins
   *
   * @return array
   */
  public function recurring_subscriptions_table_joins($joins){

    if(is_admin()) return $joins;

    $mepr_db = new \MeprDb();
    $joins[] = "/* IMPORTANT */ LEFT JOIN {$mepr_db->subscription_meta} AS meta
    ON meta.subscription_id = sub.id";

    return $joins;
  }


  /**
   * Join Subscription Meta table to remove Gift subscriptions from Account->Subscriptions tab
   * @param mixed $args
   *
   * @return array
   */
  public function recurring_subscriptions_table_args($args){
    if(is_admin()) return $args;

    $mepr_db = new \MeprDb();
    global $wpdb;

    $args[] = $wpdb->prepare("(
      ISNULL(
        (
          SELECT m.meta_value
          FROM {$mepr_db->subscription_meta} AS m
          WHERE m.subscription_id = sub.id
          AND m.meta_key = %s
          AND m.meta_value = 1
          LIMIT 1
        )
      )
      )",
      models\Gift::$is_gift_complete_str
    );

    return $args;
  }

  /**
   * Join Transaction Meta table to remove gifter/giftee transactions from Account->Subscriptions on the frontend and backend
   * @param mixed $joins
   *
   * @return array
   */
  public function nonrecurring_subscriptions_table_joins($joins){
    $mepr_db = new \MeprDb();
    $joins[] = "LEFT JOIN {$mepr_db->transaction_meta} AS meta
    ON meta.transaction_id = txn.id";

    return $joins;
  }

  /**
   * Join Transaction Meta table to remove gifter/giftee transactions from Account->Subscriptions on the frontend and backend
   * @param mixed $args
   *
   * @return array
   */
  public function nonrecurring_subscriptions_table_args($args){
    $mepr_db = new \MeprDb();
    global $wpdb;

    $args[] = $wpdb->prepare("(
      ISNULL(
        (
          SELECT m.meta_value
          FROM {$mepr_db->transaction_meta} AS m
          WHERE m.transaction_id = txn.id
          AND m.meta_key IN (%s, %s)
          AND m.meta_value >= '1'
          LIMIT 1
        )
      )
      )",
      models\Gift::$is_gift_complete_str,
      models\Gift::$gifter_txn_str
    );

    return $args;
  }


  /**
  * Render gift nav
  *
  * @see load_hooks(), add_action('mepr_account_nav')
  * @param \MeprUser $current_user logged in MeprUser object
  */
  public static function gifts_nav($current_user) {
    global $post;
    $current_user = \MeprUtils::get_currentuserinfo();
    $gift_txn_ids = (array) models\Gift::find_gifts_by_user_id($current_user->ID);
    if(empty($gift_txn_ids)) return;

    $account_url = \MeprUtils::get_permalink($post->ID);
    $delim = preg_match('#\?#', $account_url) ? '&' : '?';
    ?>
    <span class="mepr-nav-item <?php \MeprAccountHelper::active_nav('gifts'); ?>">
      <a href="<?php echo \apply_filters('mpgft-account-nav-gifts-link', $account_url . $delim . 'action=gifts'); ?>" id="mepr-account-gifts">
        <?php echo \apply_filters('mpgft-account-nav-gifts-label', esc_html__('Gifts', 'memberpress-gifting')); ?></a>
    </span>
    <?php
  }


  /**
  * Render gift list
  *
  * @see load_hooks(), add_action('mepr_account_nav_content')
  * @param string $action Account page current action
  * @param boolean $show_bookmark Show progress bar
  */
  public static function gifts_list($action, $show_bookmark = true) {
    global $post;
    $mepr_options = \MeprOptions::fetch();

    if(is_user_logged_in() && $action === 'gifts') {
      $my_gifts = array();
      $current_user = \MeprUtils::get_currentuserinfo();
      $gift_txn_ids = (array) models\Gift::find_gifts_by_user_id($current_user->ID);
      foreach ($gift_txn_ids as $txn_id) {
        // $gift = new models\Gift($transaction_id);
        $my_gifts[] = new models\Gift($txn_id);
      }

      if(!empty($my_gifts)){
        require_once(base\VIEWS_PATH . '/account/gift-list.php');
      }

    }
  }


  /**
   * @return [type]
   */
  public function send_gift_email() {
    check_ajax_referer('mpgft_send_gift_email', 'security'); // Security check

    $this->validate_email();

    $mepr_options = \MeprOptions::fetch();

    if(!isset($_POST['txn_id']) || empty($_POST['txn_id']) || !is_numeric($_POST['txn_id'])) {
      wp_send_json_error(__('Could not send email. Please try again later.', 'memberpress-gifting'));
    }

    $txn = new \MeprTransaction($_POST['txn_id']);
    lib\Utils::send_notices(
      $txn,
      base\EMAILS_NAMESPACE.'\\'.'GiftClaimEmail',
      NULL,
      true
    );

    wp_send_json_success(__('Email sent', 'memberpress-gifting'));
  }

  /**
   * Validates Email fieldsE
   * @return string
   */
  public function validate_email(){
    $errors = array();

    if(!isset($_POST['gifter_name']) || empty($_POST['gifter_name'])) {
      $errors[] = 'gifter_name';
    }

    if(!isset($_POST['giftee_name']) || empty($_POST['giftee_name'])) {
      $errors[] = 'giftee_name';
    }

    if(!isset($_POST['giftee_email']) || empty($_POST['giftee_email']) || false == is_email($_POST['giftee_email'])) {
      $errors[] = 'giftee_email';
    }

    if(!empty($errors)){
      wp_send_json_error($errors);
    }
  }

  /**
   * Expire gift coupon or refund giftee's transaction when gift transaction is refunded.
   * @param object $txn Gift transaction
   * @return void
   */
  public function handle_txn_refund($txn) {
    // Make sure we're only running on transactions that are gifts.
    if(!$txn->get_meta(models\Gift::$status_str, true)) {
      return;
    }

    $gift = new models\Gift($txn);
    $coupon = new \MeprCoupon($gift->coupon_id);

    // If the gift is unclaimed and the transaction is refunded, expire the gift coupon.
    if($gift->status == models\Gift::$unclaimed_str) {
      $coupon->mark_as_expired();
    } else {
      // Find the giftee's transaction and refund it.
      global $wpdb;
      $mepr_db = new \MeprDb();

      $giftee_query = $wpdb->prepare("SELECT id FROM {$mepr_db->transactions} WHERE coupon_id = %s", $coupon->ID);

      if(($giftee_txn_id = $wpdb->get_var($giftee_query))) {
        $giftee_txn = new \MeprTransaction($giftee_txn_id);
        $giftee_txn->status = \MeprTransaction::$refunded_str;
        $giftee_txn->store();
      }
    }
  }
}