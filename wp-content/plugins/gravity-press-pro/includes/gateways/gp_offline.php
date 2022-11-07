<?php

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class GPOfflineGateway extends GP_Payment_Gateways_Common {

    /**
     * Default constructor.
     *
     * Initialize Offline payment, Admin can manually completed the transactions.
     *
     * @since   1.0.0
     */
    function __construct() {
        $this->add_actions();
    }

    /**
     * Adds action hooks.
     *
     * @since   1.0.0
     */
    private function add_actions() {}

    /**
     * Process user after creating subscriptions and transactions
     * 
     * @param object $txn           The txn object
     * @param array $feed_settings  The GP feed settings
     * @param string $signup_type - recurring/non-recurring
     * @since 3.0
     */
     public function gp_process_subscription( $txn, $feed_settings, $entry, $signup_type, $transaction_id ){

        $this->process_subscription($entry, $txn, $signup_type);

        if( $signup_type == 'recurring'){
            $sub  = MeprSubscription::get_one_by_subscr_id($transaction_id);
            $sub->status = $feed_settings['sub_status'];
            $sub->subscr_id   = 'mp-sub-' . uniqid();
            $sub->store();
            // add note at GF entry
            GFFormsModel::add_note($entry['id'], 0, 'Gravity Press', 'MemberPress Subscription has been created. Subscription Id: ' . $sub->subscr_id . '.', 'success');
            gform_update_meta( $entry['id'], 'gp_memberpress_sub', $sub, $entry['form_id'] );
        }
     }
}
new GPOfflineGateway();