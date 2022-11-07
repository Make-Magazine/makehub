<?php

if (!defined('ABSPATH')) {
    die('You are not allowed to call this page directly.');
}


/**
 * Add Transaction to Subscription that is already created in GravityForms and in MemberPress.
 *
 * @param $entry
 * @param $action
 * @uses gform_get_meta() To get Transaction Information that was stored in first transaction while creating Subscription.
 * @uses MeprTransaction() To create a new Transaction.
 */
function gpPro_add_transaction_to_memberpress($entry, $action) {

    //Obtain Variables
    $subscr_info = gform_get_meta($entry['id'], 'gf_memberpress_subscr');
    $MP_level = new MeprProduct($subscr_info->product_id); //MP Level
    $subscription_created = new DateTime($subscr_info->created_at);

    //Check first to make sure subscription was created over 1 day ago before continuing to add this transaction.
    //If less than 1 day ago, then end here. The core GP plugin will have created an initial transaction simultaneously the subscription.
    //Only subsequent transactions upon payment success should run with this function
    if ( ($subscription_created->getTimestamp()) >= strtotime('23 hours ago') ) {
      return;
    }


    ///Ensure Billing Type is Recurring (not One-Time)
    if ( ($MP_level->ID != null) && ($subscr_info != null) && ($MP_level->period_type != 'lifetime') ) {
        $txn = new MeprTransaction();
        $txn->user_id = $subscr_info->user_id;
        $txn->product_id = $subscr_info->product_id;
        $txn->trans_num = 'gf-txn-' . uniqid();
        $txn->amount = $entry['payment_amount'];
        $txn->status = MeprTransaction::$complete_str;
        $txn->expires_at = null; // 0 = Lifetime, null = product default expiration
        $txn->subscription_id = $subscr_info->id;
        $txn->gateway = MeprTransaction::$manual_gateway_str;
        $txn->store();
        gform_update_meta($entry['id'], 'gf_memberpress_txn', $txn);
    }
}
add_action('gform_post_add_subscription_payment', 'gpPro_add_transaction_to_memberpress', 10, 2);

/**
 * Stop MemberPress Transactions and Subscription on Failed GravityForm Subscription Payment.
 *
 * @param $entry
 * @param $action
 * @uses gform_get_meta() To get Transaction Information that was stored in first transaction while creating Subscription.
 * @uses MeprTransaction() To create a new Transaction.
 * @uses GFAPI::update_entry() To cancel GravityForm Entry.
 * @uses MeprSubscription() To cancel MemberPress Subscription.
 * @uses GFFormsModel::add_note() To add a note and save the time of cancellation of Subscription Entry.
 */
function gpPro_add_transaction_and_stop_subscription_to_memberpress($entry, $action)
{
    $txnInfo = gform_get_meta($entry['id'], 'gf_memberpress_txn');
    $MP_level = new MeprProduct($txnInfo->product_id);

    if ($MP_level->period_type != 'lifetime') {
        $txn = new MeprTransaction();
        $txn->user_id = $txnInfo->user_id;
        $txn->product_id = $txnInfo->product_id;
        $txn->trans_num = 'gf-txn-' . uniqid();
        $txn->amount = $entry['payment_amount'];
        $txn->status = MeprTransaction::$failed_str;
        $txn->expires_at = null; // 0 = Lifetime, null = product default expiration
        $txn->subscription_id = $txnInfo->subscription_id;
        $txn->store();

        $entry['payment_status'] = 'Cancelled';
        GFAPI::update_entry($entry);
        $sub = new MeprSubscription($txnInfo->subscription_id);
        GFFormsModel::add_note($entry['id'], 0, 'Stripe', 'Subscription has been cancelled. Subscription Id: ' . $entry->transaction_id . ',Memberpress:' . $sub->subscr_id . '.');
        $sub->status = MeprSubscription::$cancelled_str;;
        MeprSubscription::update($sub);
    }
}
add_action('gform_post_fail_subscription_payment', 'gpPro_add_transaction_and_stop_subscription_to_memberpress', 10, 2);