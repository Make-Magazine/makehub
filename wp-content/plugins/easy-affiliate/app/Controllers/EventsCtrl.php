<?php

namespace EasyAffiliate\Controllers;

use EasyAffiliate\Lib\BaseCtrl;
use EasyAffiliate\Models\Campaign;
use EasyAffiliate\Models\Event;
use EasyAffiliate\Models\Creative;
use EasyAffiliate\Models\User;

/** This will pick up some of the hook based events, more in-depth
  * processing of certain events and event cleanup maintenance tasks.
  */
class EventsCtrl extends BaseCtrl {
  public function load_hooks() {
    // User based events
    add_action('wp_login', [$this, 'affiliate_login'], 10, 2);
    add_action('profile_update', [$this, 'affiliate_updated']);
    add_action('delete_user', [$this, 'affiliate_deleted']);

    // CPT based events
    add_action('wp_insert_post', [$this, 'post_added'], 10, 3);
    add_action('post_updated', [$this, 'post_updated']);
    add_action('delete_post', [$this, 'post_deleted']);

    // Ctax based events
    add_action('created_term', [$this, 'term_added'], 10, 3);
    add_action('edited_term', [$this, 'term_updated'], 10, 3);
    add_action('pre_delete_term', [$this, 'term_deleted'], 10, 2);

    // Affiliate application events
    add_action('esaf_cpt_create-esaf-application', [$this, 'application_submitted']);
    add_action('esaf_cpt_store_meta-esaf-application-status', [$this, 'application_status_changed'], 10, 3);

    // User events
    add_action('esaf_user_store_meta-is_affiliate', [$this, 'user_affiliate_status_changed'], 10, 3);
    add_action('esaf_user_store_meta-is_blocked', [$this, 'user_affiliate_blocked_changed'], 10, 3);

    // Transaction/Commission based events
    add_action('esaf_transaction_pre_update', [$this, 'transaction_changed'], 10, 2);
    add_action('esaf_commission_pre_update', [$this, 'commission_changed'], 10, 2);
  }

  public function affiliate_login($user_login, $user) {
    $user = new User($user->ID);

    if($user->is_affiliate) {
      Event::record('affiliate-login', $user);
    }
  }

  public function affiliate_updated($affiliate_id) {
    if(!empty($affiliate_id)) {
      $affiliate = new User($affiliate_id);
      if($affiliate->is_affiliate) {
        Event::record('affiliate-updated', $affiliate);
      }
    }
  }

  public function affiliate_deleted($affiliate_id) {
    if(!empty($affiliate_id)) {
      // Since the 'delete_user' action fires just before the affiliate is deleted
      // we should still have access to the full User object for them
      $affiliate = new User($affiliate_id);
      if($affiliate->is_affiliate) {
        Event::record('affiliate-deleted', $affiliate);
      }
    }
  }

  public function post_added($post_id, $post, $update) {
    $post = get_post($post_id);
    if($post->post_type==Creative::$cpt && !$update) {
      Event::record('creative-added', (new Creative($post_id)));
    }
  }

  public function post_updated($post_id) {
    $post = get_post($post_id);
    if($post->post_type==Creative::$cpt) {
      Event::record('creative-updated', (new Creative($post_id)));
    }
  }

  public function post_deleted($post_id) {
    $post = get_post($post_id);
    if($post->post_type==Creative::$cpt) {
      Event::record('creative-deleted', (new Creative($post_id)));
    }
  }

  public function term_added($term_id, $tt_id, $taxonomy) {
    if($taxonomy==Campaign::$ctax) {
      Event::record('campaign-added', (new Campaign($term_id)));
    }
  }

  public function term_updated($term_id, $tt_id, $taxonomy) {
    if($taxonomy==Campaign::$ctax) {
      Event::record('campaign-updated', (new Campaign($term_id)));
    }
  }

  public function term_deleted($term_id, $taxonomy) {
    if($taxonomy==Campaign::$ctax) {
      Event::record('campaign-deleted', (new Campaign($term_id)));
    }
  }

  public function application_submitted($app) {
    Event::record('affiliate-application-submitted', $app);
  }

  public function application_status_changed($status, $old_status, $app) {
    if($status!=$old_status) {
      if( $status=='approved' &&
          ( 0 >= Event::get_count_by_obj(
              'affiliate-application-approved',
              'affiliate_application',
              $app->ID
            ) ) ) {
        Event::record('affiliate-application-approved', $app);
      }
      else if( $status=='ignored' &&
               ( 0 >= Event::get_count_by_obj(
                   'affiliate-application-ignored',
                   'affiliate_application',
                   $app->ID
                 ) ) ) {
        Event::record('affiliate-application-ignored', $app);
      }
    }
  }

  public function user_affiliate_status_changed($is_affiliate, $old_is_affiliate, $user) {
    if($is_affiliate!=$old_is_affiliate && $is_affiliate) {
      Event::record('affiliate-added', $user);
    }
  }

  public function user_affiliate_blocked_changed($is_blocked, $old_is_blocked, $user) {
    if($is_blocked!=$old_is_blocked && $is_blocked) {
      Event::record('affiliate-blocked', $user);
    }
  }

  public function transaction_changed($transaction, $old_transaction) {
    if($old_transaction->refund_amount != $transaction->refund_amount && $transaction->refund_amount > 0.00) {
      Event::record('transaction-refunded', $transaction, ['refund_amount'=>$transaction->refund_amount]);
    }
  }

  public function commission_changed($commission, $old_commission) {
    if($old_commission->correction_amount != $commission->correction_amount && $commission->correction_amount > 0.00) {
      Event::record('commission-corrected', $commission, ['correction_amount'=>$commission->correction_amount]);
    }
  }

} //End class
