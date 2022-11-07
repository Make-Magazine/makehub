<?php

if (!defined('ABSPATH')) {
    die('You are not allowed to call this page directly.');
}

class GPStripeGateway extends GP_Payment_Gateways_Common
{

    public $customer_detail;

    public $secret_key;

    /**
     *
     * Added by @kumaranup594
     * 
     * @since 3.4.0
     * @var type 
     */
    public $gateway_name =  'Stripe';

    /**
     * Default constructor.
     *
     * Initialize stripe payment
     *
     * @since   1.0.0
     */
    function __construct()
    {
        $this->add_actions();
    }

    /**
     * Adds action hooks.
     *
     * @since   1.0.0
     */
    private function add_actions()
    {

        // hook only applicable to Product & Service feed for stripe credit method
        add_filter("gform_stripe_charge_pre_create", array($this, 'gp_stripe_charge_pre_create'), 10, 5);

        // this hook set customer object as global and save cusotmer ID / Only for subscription feeds
        add_action("gform_stripe_customer_after_create", array($this, 'get_stripe_customer_after_create'), 10, 4);

        // hooks run for Stripe Payment Form method only
        add_action("gform_stripe_fulfillment", array($this, "gp_stripe_fulfillment"), 9, 4);

        add_filter("gform_stripe_session_data", array($this, "stripe_session_data"), 10, 5);
        // hooks for new user registration
        add_action("gform_user_registered", array($this, "enroll_delay_registration"), 10, 4);

        // hook run after processing the stripe feed 
        add_action("gform_gravityformsstripe_post_process_feed", array($this, "process_stripe_feed"), 10, 4);

        // this action remove manual transaction for the subscription which are created using Stripe Payment Form method
        add_action('gp_stripe_remove_manual_trans', array($this, 'gp_stripe_destroy_transaction'), 10, 3);

    /**
     *
     * Added by @kumaranup594
     * 
     * @since 3.4.0
     * @var type
     * @description This action is called when the memberpress transaction saved and our callback function is checking all transaction and deleted all except latest one. 
     */
        add_action('mepr-txn-store', array($this, 'delete_transaction'), 10, 3);
    }


    /**
     *
     * Added by @kumaranup594
     * 
     * @since 3.4.0
     * @var type 
     * @description This function is checking all transaction's and deleted all except latest one. 
     */
     public function delete_transaction($txn, $old_txn) {
        if (!isset($txn->subscription_id) || empty($txn->subscription_id))
            return false;

        $sub = new MeprSubscription($txn->subscription_id);
        
        if (empty($sub))
            return false;

        $txns = $sub->transactions();
        
        if (count($txns) >= 2) {
            $paymentGatewayExist = false;
            $txnsIds = [];

            $txns = array_reverse($txns);
            
            foreach ($txns as $eachTxn) {
                $paymentGateway = $eachTxn->payment_method();
                if ($paymentGateway->name == $this->gateway_name && !$paymentGatewayExist) {
                    $paymentGatewayExist = true;
                } else {
                    $txnsIds[] = $eachTxn->id;
                }
            }
            
            if ($paymentGatewayExist) {
                foreach ($txnsIds as $txnId) {
                    $txn = new MeprTransaction($txnId);
                    $txn ? $txn->destroy() : '';
                }
            }
        }
    }

    /**
     * Stripe charge pre create, setting stripe global variable for Product & Service feeds
     */
    public function gp_stripe_charge_pre_create($charge_meta, $feed, $submission_data, $form, $entry)
    {

        // set stripe customer to anything to avoid error
        global $stripe_customer;
        $stripe_customer = 'Stripe';

        // set transactiontype product
        global $gp_transaction_type;
        $gp_transaction_type = $feed['meta']['transactionType'];

        return $charge_meta;
    }

    /**
     * Set customer as global variable for Subscription Feed to use it in Memberpress subscription
     *
     * @since  3.0
     * @access public
     *
     * @param string $customer_id The identifier of the customer to be retrieved.
     * @param array $feed The feed currently being processed.
     * @param array $entry The entry currently being processed.
     * @param array $form The which created the current entry.
     *
     * @return string $customer_id
     */
    public function get_stripe_customer_after_create($customer, $feed, $entry, $form)
    {

        // stop executing the below process if no GravityPress feed is activated.
        $gp_feed_active = $this->are_GP_feeds_active($form['id']);
        if (!$gp_feed_active) return;

        // converting GF stripe customer object to stdclass object to make it compatible with Memberpress
        $customer_str = json_encode($customer, true);
        $customer_arr = (object)json_decode($customer_str, true);
        $customer = $customer_arr;

        if (isset($customer->error)) {
            $this->gp_debug_log(__METHOD__ . "(): customer creation failed in stripe webhook");
            return;
        }

        // set stripe customer as global so we are able to use this customer while creating subscription
        global $stripe_customer;
        $stripe_customer = $customer;

        // set common transactiontype(subscription/product) as global
        global $gp_transaction_type;
        $gp_transaction_type = $feed['meta']['transactionType'];


        $this->gp_debug_log(__METHOD__ . "(): declare customer object as global");

    }

    /**
     * Process user subscription
     * create subscription in memberpress
     *
     * @param object $txn The txn object
     * @param array $feed_settings The GP feed settings
     * @param string $signup_type - recurring/non-recurring
     * @since 3.0
     */
    public function gp_process_subscription($txn, $feed_settings, $entry, $signup_type)
    {

        $sub_process = $this->process_subscription($entry, $txn, $signup_type);
        if ($sub_process == false || $signup_type == 'non-recurring') {
            return;
        }

        /**
         * For recurring subscription, transaction email sending via webhook in memberpress
         * also new transaction creation with charge ID has been handle through webhooks in MP
         */

        // below process is only for recurring subscriptions
        global $stripe_customer;
        $customer = $stripe_customer;

        $txn_gateway = $txn->gateway;

        $user_id = $entry['created_by'];
        $usr = new MeprUser($user_id);

        // Add customer id meta data for live transactions
        $usr->set_stripe_customer_id($txn_gateway, $customer->id);
        // Add customer id meta data for test transactions
        $usr->set_stripe_customer_id($txn_gateway . '_test', $customer->id);

        // for some reason if customer object is missing
        if (empty($customer)) {
            $this->gp_debug_log(__METHOD__ . "(): customer object is empty");
            return;
        }


        /** Used to record a successful recurring subscription by the given gateway. It should have
         * the ability to record a successful subscription or a failure.
         */
        $this->record_create_subscription($txn, $customer, $feed_settings, $entry);
        return;
    }

    /** Used to record a successful subscription by the given gateway. It should have
     * the ability to record a successful subscription or a failure.
     *
     * @param object $customer The customer object
     * @param string $feed_settings Set subscription status based on the feeds settings
     *
     * @since 3.0
     */
    public function record_create_subscription($txn, $customer, $feed_settings, $entry)
    {

        $this->gp_debug_log(__METHOD__ . "(): recording successful subscription");

        $mepr_options = MeprOptions::fetch();

        $stripe_obj = new MeprStripeGateway();

        $subscr_id = (!empty($entry['transaction_id'])) ? $entry['transaction_id'] : $customer->id;
        $sub = MeprSubscription::get_one_by_subscr_id($subscr_id);
        if (!$sub) {
            $sub = $txn->subscription();
        }

        $sub->response = $customer;
        $sub->status = $feed_settings['sub_status'];

        if ($card = $stripe_obj->get_default_card($customer)) {
            $sub->cc_last4 = $card['last4'];
            $sub->cc_exp_month = $card['exp_month'];
            $sub->cc_exp_year = $card['exp_year'];
        }

        $sub->created_at = gmdate('c');
        $sub->store();

        // determin if the selected level is upgrade or downgrade
        $upgrade = $sub->is_upgrade();
        $downgrade = $sub->is_downgrade();

        // cancel old subscription if upgraded membership
        $event_txn = $sub->maybe_cancel_old_sub();

        // check if its stripe payment form method and delay checkbox is enabled or not
        global $stripe_checkout;
        $stripe_delay_register = gform_get_meta($entry['id'], 'gp_stripe_delay_register', true);
        $stripe_delay = (!empty($stripe_checkout)) ? true : false;

        // If no trial or trial amount is zero then we've got to make
        // sure the confirmation txn lasts through the trial

        if (!$sub->trial || ($sub->trial and $sub->trial_amount <= 0.00)) {

            $trial_days = ($sub->trial) ? $sub->trial_days : $mepr_options->grace_init_days;
            $txn->response = (string)$sub;

            if ($sub->trial) {
                $expires_at = MeprUtils::ts_to_mysql_date(time() + MeprUtils::days($sub->trial_days), 'Y-m-d 23:59:59');
            } elseif (!$mepr_options->disable_grace_init_days && $mepr_options->grace_init_days > 0) {
                $expires_at = MeprUtils::ts_to_mysql_date(time() + MeprUtils::days($mepr_options->grace_init_days), 'Y-m-d 23:59:59');
            } else {
                $expires_at = $txn->expires_at;              
            }

            if ($stripe_delay) {
                // For Stripe Payment Form method, set transaction status to complete because we need to show this transaction under this subscription
                // auto transaction sometime get failed to add
                $txn->status = MeprTransaction::$complete_str;
                $txn->trans_num = 'gp-mp-txn-' . uniqid();
            }
            else {
                $txn->trans_num = $sub->subscr_id;
                $txn->txn_type = MeprTransaction::$subscription_confirmation_str;
//                $txn->txn_type = MeprTransaction::$payment_str;
                $txn->status = MeprTransaction::$confirmed_str;
//                  $txn->status = MeprTransaction::$complete_str;
                $txn->expires_at = $expires_at;
                // expires at defaut to membership regular expiration rules
                 $txn->set_subtotal(0.00); // This txn is just a confirmation txn ... it shouldn't have a cost
            }

            // save txn
            $txn->store();

            // if stripe checkout is enable, set cron that will delete the manual transaction if auto transaction has been created for this subscription
            // this will run after 2 hrs = 120 minutes
            if ($stripe_delay) {
                $time_delay = current_time('timestamp') + (120 * MINUTE_IN_SECONDS);
                $time_delay = $time_delay - (get_option('gmt_offset') * 60 * 60);
                wp_schedule_single_event($time_delay, 'gp_stripe_remove_manual_trans', array($sub->id, $txn->id, $subscr_id));
            }

            // add note at GF entry
            GFFormsModel::add_note($entry['id'], 0, 'Gravity Press', 'MemberPress Subscription has been created. Subscription Id: ' . $sub->subscr_id . '.', 'success');
        }

        if ($upgrade) {
            $stripe_obj->upgraded_sub($sub, $event_txn);
        } else if ($downgrade) {
            $stripe_obj->downgraded_sub($sub, $event_txn);
        } else {
            $stripe_obj->new_sub($sub, true);
        }

        gform_update_meta($entry['id'], 'gp_memberpress_sub', $sub, $entry['form_id']);
        gform_update_meta($entry['id'], 'gp_memberpress_trans', $txn, $entry['form_id']);

        $this->gp_send_notices('signup_notice', $txn);

        // delete meta data
        gform_delete_meta($entry['id'], 'gp_new_user_id');
        gform_delete_meta($entry['id'], 'gp_stripe_delay_register');

        return array('subscription' => $sub, 'transaction' => $txn);
    }

    public function get_stripe_settings($gateway_id)
    {
        $mepr_options = MeprOptions::fetch();
        $gateway = $mepr_options->payment_method($gateway_id);
        return $gateway->settings;
    }

    /**
     * Process Memberpress subscriptions after stripe checkout session is completed
     * also set global variable for further use.
     */
    public function gp_stripe_fulfillment($session, $entry, $feed, $form)
    {
        $this->gp_debug_log(__METHOD__ . "(): stripe fulfillment started for entry ID: " . $entry['id']);

        $form_id = $form['id'];
        $user_id = $entry['created_by'];

        // stop executing the below process if no GravityPress feed is activated.
        $gp_feed_active = $this->are_GP_feeds_active($form_id);
        if (!$gp_feed_active) return;

        // prepare global variable array from session data
        $this->set_stripe_checkout_globals($session, $feed);

        // update entry detail, set transaction id from session data
        if (isset($session['subscription']) && $session['subscription'] != '') {
            $entry['transaction_id'] = $session['subscription'];
        } else {
            $entry['transaction_id'] = $session['payment_intent'];
        }

        // if the payment feed option Delay Register is enabled, get user id (saved in gfrom_registered hook) from meta data
        $delay_register = (isset($feed['meta']['delay_gravityformsuserregistration'])) ? $feed['meta']['delay_gravityformsuserregistration'] : false;
        if ($delay_register == true && empty($user_id)) {
            $user_id = gform_get_meta($entry['id'], 'gp_new_user_id', true);
        }

        // if user is already loggedin, get current user id
        if (is_user_logged_in() && empty($user_id)) {
            $user_id = get_current_user_id();
        }

        // Ensure GP feed is active and user_id is not null
        if (!empty($user_id)) {
            $feed = GFAPI::get_feeds($feed_ids = '', $form_id = '', $addon_slug = 'gravitypress', $is_active = true);
            //Send data to gp_admin.php process_feed function to enroll user
            $enroll_user = new GravityPressFeedAddOn();

            $enroll_user->process_feed_enroll_user($feed, $entry, $form, $user_id);
        }
    }

    /**
     * Set customer data and other info as global variables before creating the session in stripe
     * we use these global variables in other hooks(gform_confirmation) to stop process for this method
     */
    public function stripe_session_data($session_data, $feed, $submission_data, $form, $entry)
    {
        // set checkout global vars
        $this->set_stripe_checkout_globals($session_data, $feed);
        return $session_data;
    }

    /**
     * Prepared Stripe Checkout global variables
     */
    public function set_stripe_checkout_globals($session_data, $feed)
    {

        global $stripe_checkout;
        $stripe_checkout = [
            'customer' => (isset($session_data['customer'])) ? $session_data['customer'] : '',
            'transaction_id' => (isset($session_data['payment_intent'])) ? $session_data['payment_intent'] : '',
            'subscription' => (isset($session_data['subscription'])) ? $session_data['subscription'] : '',
            'delay_register' => (isset($feed['meta']['delay_gravityformsuserregistration'])) ? $feed['meta']['delay_gravityformsuserregistration'] : ''
        ];
        return true;
    }


    /**
     * This method temporarily save newly registered user id in meta data,
     * we use this user ID for Stripe Payment Form method in gform_stripe_fulfillment hook
     */
    public function enroll_delay_registration($user_id, $feed, $entry, $user_pass)
    {

        gform_update_meta($entry['id'], 'gp_new_user_id', $user_id, $entry['form_id']);

    }

    /**
     * save feed Delay Registration option temporarily for later use in other hooks
     * we do this for Stripe Payment Form method
     */
    public function process_stripe_feed($feed, $entry, $form, $addon)
    {
        if (isset($feed['meta']['delay_gravityformsuserregistration'])) {
            gform_update_meta($entry['id'], 'gp_stripe_delay_register', $feed['meta']['delay_gravityformsuserregistration'], $entry['form_id']);
        }
    }

    /**
     * This method will delete manual transaction
     * check if auto transaction with id "ch_" is created
     */
    public function gp_stripe_destroy_transaction($sub_id, $txn_id, $subscr_id)
    {
        if (empty($txn_id) || !$txn_id) {
            return false;
        }

        $manual_trans = new MeprTransaction($txn_id);

        if ($manual_trans) {
            $manual_trans_id = $manual_trans->trans_num;

            $txns = MeprTransaction::get_all_by_subscription_id($sub_id);
            // loop through all transaction and see if original tranaction is created, destroy manual trans
            if (!empty($txns) && count($txns) > 1) {
                foreach ($txns as $txn) {
                    $transaction_id = $txn->trans_num;
                    if ((strpos($transaction_id, 'ch_') !== false || strpos($transaction_id, 'pi_') !== false)
                        && (date('Y-m-d', strtotime($txn->expires_at)) == date('Y-m-d', strtotime($manual_trans->expires_at)))) {
                        $manual_trans->destroy();
                    }
                }
            }
        }
    }

}

new GPStripeGateway();
?>
