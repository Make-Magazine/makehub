<?php

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class GPAuthorizeGateway extends GP_Payment_Gateways_Common {

    public $gateway_name = 'Authorize.net';
    public $gf_authorize_feed_slug = 'gravityformsauthorizenet';

    /**
     * Default constructor.
     *
     * Initialize authorize payment
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
    private function add_actions() {
        add_filter( 'gform_submission_data_pre_process_payment', array($this, 'gp_form_data_pre_process_payment'), 10, 4 );
    }

    /**
     * This function detect if authorize feed is in process, if enabled set global variables
     * 
     * @param array $submission_data
     * @param object $feed
     * @param object $form
     * @param array $entry
     * 
     * @return array $submission_data - modified data
     */
    public function gp_form_data_pre_process_payment( $submission_data, $feed, $form, $entry ){

        
        if( $feed['addon_slug'] == $this->gf_authorize_feed_slug ){
            $gp_membership_obj = new GP_Payment_Gateways_Common();
            $gp_feed_active = $gp_membership_obj->are_GP_feeds_active($form['id']);

            if( $gp_feed_active == 1 ){
                global $authorize_customer;
                $authorize_customer = $this->gateway_name;
    
                global $gp_transaction_type;
                $gp_transaction_type = $feed['meta']['transactionType'];
            }
        }


        return $submission_data;
    }

    
    /**
     * Process user subscription
     * create subscription in memberpress
     * 
     * @param object $txn           The txn object
     * @param array $feed_settings  The GP feed settings
     * @param string $signup_type - recurring/non-recurring
     * @since 3.0
     */
    public function gp_process_subscription( $txn, $feed_settings, $entry, $signup_type, $subscr_id ){

        $sub_process = $this->process_subscription($entry, $txn, $signup_type);
        if( $sub_process == false || $signup_type == 'non-recurring' ) {
            //GFFormsModel::add_note($entry['id'], 0, 'Gravity Press', 'MemberPress Subscription has been created. Subscription Id: ' . $sub->subscr_id . '.', 'success');
            return;
        }

        /** Used to record a successful recurring subscription by the given gateway. It should have
        * the ability to record a successful subscription or a failure. 
        */
        $this->record_create_subscription( $txn, $feed_settings, $entry, $subscr_id );
    }

    /** Used to record a successful subscription by the given gateway. It should have
    * the ability to record a successful subscription or a failure.
    * 
    * @param object $customer       The customer object
    * @param string $feed_settings     Set subscription status based on the feeds settings
    *
    * @since 3.0
    */
    public function record_create_subscription( $txn, $feed_settings, $entry, $subscr_id ){

        $this->gp_debug_log(__METHOD__ . "(): recording successful subscription");

        $mepr_options = MeprOptions::fetch();
        
        $authorize_obj = new MeprAuthorizeGateway();

        $sub           = MeprSubscription::get_one_by_subscr_id($subscr_id);
        $sub->status   = $feed_settings['sub_status'];
        $sub->store();
        
        // determin if the selected level is upgrade or downgrade
        $upgrade = $sub->is_upgrade();
        $downgrade = $sub->is_downgrade();

        // cancel old subscription if upgraded membership
        $event_txn = $sub->maybe_cancel_old_sub();

        // If no trial or trial amount is zero then we've got to make
        // sure the confirmation txn lasts through the trial
        if(!$sub->trial || ($sub->trial and $sub->trial_amount <= 0.00)) {

            $trial_days = ($sub->trial)?$sub->trial_days:$mepr_options->grace_init_days;
            $txn->trans_num  = $sub->subscr_id;
            $txn->status     = MeprTransaction::$confirmed_str;
            $txn->txn_type   = MeprTransaction::$subscription_confirmation_str;
            $txn->response   = (string)$sub;

            if($sub->trial) {
                $expires_at = MeprUtils::ts_to_mysql_date(time() + MeprUtils::days($sub->trial_days), 'Y-m-d 23:59:59');
            } elseif(!$mepr_options->disable_grace_init_days && $mepr_options->grace_init_days > 0) {
                $expires_at = MeprUtils::ts_to_mysql_date(time() + MeprUtils::days($mepr_options->grace_init_days), 'Y-m-d 23:59:59');
            } else {
                $expires_at = $txn->created_at; // Expire immediately
            }

            // expires at defaut to membership regular expiration rules
           $txn->expires_at = $expires_at;
           $txn->set_subtotal(0.00); // This txn is just a confirmation txn ... it shouldn't have a cost
           $txn->store();
            // add note at GF entry
            GFFormsModel::add_note($entry['id'], 0, 'Gravity Press', 'MemberPress Subscription has been created. Subscription Id: ' . $sub->subscr_id . '.', 'success');
        }

        if($upgrade) {
            $authorize_obj->upgraded_sub($sub, $event_txn);
        }
        elseif($downgrade) {
            $authorize_obj->downgraded_sub($sub, $event_txn);
        }
        else {
            $authorize_obj->new_sub($sub, true);
        }

        return array('subscription' => $sub, 'transaction' => $txn); 
    }
}
new GPAuthorizeGateway();