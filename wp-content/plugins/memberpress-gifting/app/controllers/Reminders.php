<?php
namespace memberpress\gifting\controllers;

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

use memberpress\gifting as base;
use memberpress\gifting\lib as lib;
use memberpress\gifting\models as models;
use memberpress\gifting\helpers as helpers;
use memberpress\gifting\emails as emails;

class Reminders extends lib\BaseCtrl {
  public function load_hooks() {
    $disable_reminder_crons = get_option('mepr_disable_reminder_crons');
    if(!$disable_reminder_crons) {
      $r = new models\Reminder();
      foreach($r->event_actions as $e) {
        add_action($e, array($this, 'send_reminders'));
      }
    }
    add_action('mepr-reminder-trigger-option', array($this, 'add_reminder_option_html'));
    add_action('mepr_reminders_worker', array($this, 'worker'));
    add_filter("mepr-sub-expires-reminder-disable", array($this, 'disable_reminder_email'), 10, 5);
    add_filter("mepr_reminder_lookup", array($this, 'add_reminder_lookup'), 10, 2);
  }


  public function get_valid_reminder($id) {
    // if the remider_id is empty then forget it
    if(empty($id)) { return false; }

    $post = get_post($id);

    // Post not found? fail
    if(empty($post)) { return false; }

    // not the right post type? fail
    if($post->post_type!==models\Reminder::$cpt) { return false; }

    // not a published post? fail
    if($post->post_status!=='publish') { return false; }

    $reminder = new models\Reminder($id);

    // ID is empty? fail
    if(empty($reminder->ID)) { return false; }

    return $reminder;
  }

  public function worker($reminder_id) {
    $reminder = $this->get_valid_reminder($reminder_id);
    if($reminder !== false) {
      @set_time_limit(0); // unlimited run time
      $run_limit = \MeprUtils::minutes(10); // limit to 10 minutes

      // Event name will be the same no matter what we're doing here
      $event = "{$reminder->trigger_timing}-{$reminder->trigger_event}-reminder";
      while($this->run_time() < $run_limit) {
        $args = $reminder_id;
        $obj = null;

        switch($reminder->trigger_event) {
          case 'gift-expires':

            if(($txn = $reminder->get_next_expiring_gift_txn())) {
              $obj = new \MeprTransaction($txn->id); // we need the actual model
            }
            break;
          default:
            // $this->unschedule_reminder($reminder_id);
            break;
        }

        if(isset($obj)) {
          // We just catch the hooks from these events
          \MeprEvent::record($event, $obj, $args);
        }
        else {
          break; //break out of the while loop
        }
      } //End while
    }
  }

  private function run_time() {
    static $start_time;

    if(!isset($start_time)) {
      $start_time = time();
    }

    return ( time() - $start_time );
  }

  private function send_emails($usr, $uclass, $aclass, $params, $args) {
    try {
      $uemail = lib\EmailFactory::fetch( $uclass, 'BaseReminderEmail', $args );
      $uemail->to = $usr->formatted_email();
      $uemail->send_if_enabled($params);
    }
    catch( \Exception $e ) {
      // Fail silently for now
    }
  }

  public function send_reminders($event) {
    $disable_email = false; //Do not send the emails if this gets set to true

    if($event->evt_id_type == 'transactions' && ($txn = new \MeprTransaction($event->evt_id))) {
      //Do not send reminders to sub-accounts
      if(isset($txn->parent_transaction_id) && $txn->parent_transaction_id > 0) { $disable_email = true; }

      //Do not send reminders to membership gifter accounts
      // if(isset($txn->parent_transaction_id) && $txn->parent_transaction_id > 0) { $disable_email = true; }

      $usr      = $txn->user();
      $prd      = new \MeprProduct($txn->product_id);
      $reminder = $this->get_valid_reminder($event->args);

      if($reminder === false) { return; } // fail silently if reminder is invalid

      $params = array_merge(\MeprRemindersHelper::get_email_params($reminder), \MeprTransactionsHelper::get_email_params($txn));

      switch($reminder->trigger_event) {
        case 'gift-expires':
          //Don't send a reminder if the user has already renewed either a one-time or an offline subscription
          if($reminder->trigger_timing == 'before') { //Handle when the reminder should go out before
            $txn_count = count($usr->transactions_for_product($txn->product_id, false, true));

            //txn_count > 1 works well for both renewals and offline subs actually because transactions_for_product
            //should only ever return a count of currently active (payment type) transactions and no expired transactions
            if($txn_count > 1) {
              $disable_email = true;
            }
          }
          else { //Handle when the reminder should go out after
            //Don't send to folks if they have an active txn on this subscription already yo
            if(in_array($txn->product_id, $usr->active_product_subscriptions('ids'), false)) {

              $disable_email = true;
            }
          }
          $uclass = base\EMAILS_NAMESPACE.'\\'.'UserGiftExpiresReminderEmail';
          $aclass = '';
          break;
        default:
          $uclass=$aclass='';
      }

      $args = array(array('reminder_id'=>$event->args));

      $disable_email = apply_filters(base\SLUG_KEY."-{$reminder->trigger_event}-reminder-disable", $disable_email, $reminder, $usr, $prd, $event);

      if(!$disable_email) {
        $this->send_emails($usr, $uclass, $aclass, $params, $args);
      }
    }
  }

  public function delete( $id ) {
    global $post_type;
    if ( $post_type != models\Reminder::$cpt ) return;
    //$this->unschedule_reminder($id);
  }

 /**
  * No reminders get triggered for purchased gift transactions
  *
  * @param bool $disable_email
  * @param \MeprReminder $reminder
  * @param \MeprUser $usr
  * @param \MeprProduct $prd
  * @param \MeprEvent $event
  * @return bool
  */
  public function disable_reminder_email($disable_email, $reminder, $usr, $prd, $event = null) {
    if($event instanceof \MeprEvent && $event->evt_id_type == 'transactions') {
      $txn = new \MeprTransaction($event->evt_id);

      if($txn->get_meta(models\Gift::$is_gift_complete_str, true)) {
        $disable_email = true;
      }
    }

    return $disable_email;
  }

  public function add_reminder_lookup(array $lookup, $reminder){
    $lookup['gift-expires'] = array(
      'after' => array(
        'name' => __('Gift Membership Expired', 'memberpress-gifting'),
        'description' => sprintf( __( 'Gift Membership expired %d %s ago' , 'memberpress-gifting'),
        $reminder->trigger_length,
        $reminder->get_trigger_interval_str() )
      )
    );
    return $lookup;
  }

  public function add_reminder_option_html($trigger){
    echo sprintf('<option value="after_gift-expires" %s> %s</option>', selected($trigger,'after_gift-expires'), _x("after Gifted Membership Expires", "ui", 'memberpress-gifting'));
    //return $option;
  }

} //End class
