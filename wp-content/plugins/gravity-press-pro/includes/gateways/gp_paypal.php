<?php

if (!defined('ABSPATH')) {
    die('You are not allowed to call this page directly.');
}

class GPPaypalStandardGateway extends GP_Payment_Gateways_Common
{

    public $gateway_name = 'PayPal Standard';

    // temp transaction time delay in minutes
    public $time_delay = 20;

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
        // this hook replace IPN URL
        // add_filter('gform_paypal_request', array($this, 'update_IPN_url'), 9, 3);

        // this User Registeration ad-on hook update User ID of subscrition/transaction
        add_action("gform_post_payment_status", array($this, "gp_register_paypal_new_user"), 50, 8);

        // this hook modify the query string, add transaction ID and gateway ID to parameters
        add_filter('gform_paypal_query', array($this, 'update_paypal_query'), 99, 5);

        // this MP hook fire after the new subscription created in IPN process
        add_action('mepr-new-sub', array($this, 'gp_mepr_final_process'), 10, 2);

        // this MP hook fire when subscription upgrade occur after processing IPN
        add_action('mepr-upgraded-sub', array($this, 'gp_mepr_final_process'), 10, 2);

        // this MP hook fire when subscription downgrade occur after processing IPN
        add_action('mepr-downgraded-sub', array($this, 'gp_mepr_final_process'), 10, 2);

        // this action remove tran/sub having temp user ID
        add_action('gp_remove_temp_transaction', array($this, 'gp_delete_temp_transactions'), 10, 3);

    }

    /**
     * This function used modify the IPN URL string that will be sent to PayPal.
     * This filter is fired immediately before the user is redirected to PayPal.
     *
     * @param $url string include return and IPN url etc
     * @param $form object
     * @param $entry object
     */
    public function update_IPN_url($url, $form, $entry)
    {

        // if no gp feed is active, stop processing the below
        $gp_feed_active = $this->are_GP_feeds_active($form['id']);
        if (!$gp_feed_active) return $url;

        // get Memberpress paypal standard IPN URL
        $mp_ipn_url = $this->get_notify_url('ipn');
//        $mp_ipn_url = 'https://wordpress-473433-1875914.cloudwaysapps.com/?callback=gravityformspaypal';

        //parse url into its individual pieces (host, path, querystring, etc.)
        $url_array = parse_url($url);
        //start rebuilding url
        $new_url = $url_array['scheme'] . '://' . $url_array['host'] . $url_array['path'] . '?';
        $query = $url_array['query']; //get querystring
        //parse querystring into pieces
        parse_str($query, $qs_param);

        if (empty($mp_ipn_url)) {
            $this->gp_debug_log(__METHOD__ . "(): no ipn url retrieved from memberpress, payment failed.");
            $notify_url = '';
            $qs_param['notify_url'] = '';
        } else {
            $this->gp_debug_log(__METHOD__ . "(): MP IPN URL set successfully");
            $notify_url = $mp_ipn_url; // IPN URL of Memberpress Paypal Standard Gateway
        }


        // in case gp feed is not active, set ipn url to default GF IPN
        global $paypal_customer;
        if (empty($paypal_customer)) {
            $notify_url = $qs_param['notify_url'];
        }

        // update notify_url querystring parameter to new value
        //$qs_param['notify_url'] = $notify_url;

        $new_qs = http_build_query($qs_param); //rebuild querystring
        $new_url .= $new_qs; //add querystring to url

        return $new_url;
    }

    /**
     * This filter used to modify the query string that will be sent to Paypal
     * Modifying few query string to make it complatible with Memberpress Paypal IPN Process
     *
     */
    public function update_paypal_query($query_string, $form, $entry, $feed)
    {

        // if no gp feed is active, stop processing the below
        $gp_feed_active = $this->are_GP_feeds_active($form['id']);
        if (!$gp_feed_active) return $query_string;

        parse_str($query_string, $query);

        // process initial subscription and transaction with status pending
        $process_form = $this->initialize_subscription($form, $entry, $feed);
        if (!$process_form) return $query_string;

        $txn_data = gform_get_meta($entry['id'], 'gp_memberpress_trans');

        // if txn id didn't save for some reason
        if (empty($txn_data->id)) {
            $this->delete_temp_user($entry);
            $this->gp_debug_log(__METHOD__ . "(): initial transaction ID missing.");
            return;
        }

        // save delay registration options, if the user registration is delayed or not.
        // using this option in Memberpress hooks
        gform_update_meta($entry['id'], 'gp_delay_registration', $feed['meta']['delay_gravityformsuserregistration'], $form['id']);

        $feed_transaction_type = $feed['meta']['transactionType'];
        $note_extra_description = ($feed_transaction_type == 'product') ? ' Non-recurring Subscription' : 'Subscription';

        // add note to GF entry, specific to subscription type
        GFFormsModel::add_note($entry['id'], 0, 'Gravity Press', 'MemberPress ' . $note_extra_description . ' has been created. Paypal payment is in process...');

        // set schedule, to see the temp ID is replace or not in sub/trans
        if ($txn_data->user_id == $this->temp_user_id) {
            $this->gp_set_temp_schedule_event($txn_data);
        }

        $product_data = $txn_data->product();


        // modify all these parameters to make it compatible with Memberpress IPN process
        $query['item_name'] = $product_data->post_title;
        $query['item_number'] = $txn_data->id;
        $paypal_mp_settings = $this->gp_memberpress_settings($this->gateway_name);
        $query['custom'] = $entry['id'] . '|' . wp_hash($entry['id']);//json_encode(array("gateway_id" => $paypal_mp_settings->id));

        if ($feed_transaction_type != 'subscription') {
            // default GF Paypal pass cmd parameter as "cart" to support multiple items,
            //but Memberpress doesn't support multiple so needed to override the "cart" with "_xclick"
            $query['cmd'] = '_xclick';
            $query['amount'] = $txn_data->total;
        }

        $this->gp_debug_log(__METHOD__ . "(): Paypal query strings modified");

        $query_string = http_build_query($query, '', '&');
        return '&' . $query_string;
    }

    /**
     * This method execute once a user has been registered through the User Registration Add-on.
     * This method replaces the temp user ID with newly registered user ID in subscription and transactio.
     *
     * @param $user_id int Registerd User ID
     * @param $feed object
     * @param @entry object
     * @param $user_id string
     *
     */
    public function gp_register_paypal_new_user($feed, $entry, $status, $transaction_id, $subscriber_id, $amount, $pending_reason, $reason)
    {
        $user_id = gform_get_meta($entry['id'], 'gp_new_user_id');


        global $paypal_customer;
        //if (empty($paypal_customer)) return;
        $txn = gform_get_meta($entry['id'], 'gp_memberpress_trans');

        if (empty($txn->id)) {
            $this->gp_debug_log(__METHOD__ . "(): no initial transaction has been created for the new user.");
            return;
        }

        $mepr_db = new MeprDb();
        $args = array(
            'trans_num' => $txn->trans_num
        );

        $txnRecords = $mepr_db->get_records($mepr_db->transactions, $args);

        $sub = $txn->subscription();

        // update user ID of subscription
        if ($sub) {

            $sub = new MeprSubscription($sub->id);

            $subscriber_id = $_POST['subscr_id'];

            if ($txn->user_id != 8888 and $txn->user_id != $this->temp_user_id) {

                $gateway_common = new GP_Payment_Gateways_Common();
                $corporate = gform_get_meta($txn->id, 'gp_memberpress_corporate');
                // save corporate sub account
                $gateway_common->gp_save_corporate_accounts($sub->id, $txn, $corporate, $txn->user_id);

            }
            if ($sub->user_id == $this->temp_user_id) {
                $sub->user_id = $user_id;
                $sub->status = 'active';
                $sub->subscr_id = $subscriber_id;
                $sub->store();

            }

            if ($sub->user_id == 8888) {

                $sub->user_id = $user_id;
                $sub->status = 'active';
                $sub->subscr_id = $subscriber_id;
                $sub->price = $_POST['amount3'];
                $sub->store();

                $gateway_common = new GP_Payment_Gateways_Common();
                $corporate = gform_get_meta($txn->id, 'gp_memberpress_corporate');

                // save corporate sub account
                $gateway_common->gp_save_corporate_accounts($sub->id, $txn, $corporate, $user_id);

            }

        }

        // update user ID in transaction
        foreach ($txnRecords as $txnRecord) {
            $txn1 = new MeprTransaction($txnRecord->id);

            if ($txn1->user_id == $this->temp_user_id || $txn1->user_id == 8888) {
                $txn1->status = MeprTransaction::$complete_str;
                $txn1->user_id = $user_id;
                $txn1->trans_num = $transaction_id;
                $txn1->store();
            }
        }

        if ($txn->user_id == 8888) {
            $txn->status = MeprTransaction::$complete_str;
            $txn->user_id = $user_id;
            $txn->trans_num = $transaction_id;
            if(isset( $_POST['payment_gross'])){
                $txn->total = $_POST['payment_gross'];
            }
            $txn->store();

            $this->gp_send_notices('signup_notice', $txn);
            $this->gp_send_notices('payment_receipt', $txn);
        }

    }


    /**
     * Process initial subscriptions/transactions just before user is redirecting to paypal
     *
     */
    public function initialize_subscription($form, $entry, $feed)
    {

        $form_id = $entry['form_id'];
        $user_id = $entry['created_by'];

        // creating temp user if delay registration is checked OR User Activation Options is enabled
        // doing this because IPN process in MP trigger error if it doesn't found the attach user in wordpress
        // so attach this temp user id to transaction and subscriptions
        $ur_feeds = GFAPI::get_feeds(null, $entry['form_id'], 'gravityformsuserregistration');
        if ((!is_wp_error($ur_feeds)) && ($feed['meta']['delay_gravityformsuserregistration'] == true)) {
            $user_id = $this->create_temp_user($entry);
        }

        // if subscription process is for new user, temporarilty assign the temp user ID to process transaction
        if (empty($user_id) || $user_id == 'NULL') {
            $user_id = $this->temp_user_id;
        }

        // check if user is loggedin, use that logged in user_id
        $has_registration_feed_type_create = gf_user_registration()->has_feed_type('create', $form);
        if (is_user_logged_in() and !$has_registration_feed_type_create) {
            $user_id = get_current_user_id();
        } else {
            $user_id = 8888;
        }

        // Ensure user id is not empty
        if (!empty($user_id)) {
            // if IPN URL is valid, set global parameter of paypal standard to use it in final process
            global $paypal_customer;
            $paypal_customer = $this->gateway_name;

            // set stripe transactiontype(subscription/product) as global
            global $gp_transaction_type;
            $gp_transaction_type = $feed['meta']['transactionType'];

            //Now grab remaining important variables
            $form = GFAPI::get_form($form_id);


            //Send data to gp_admin.php process_feed function to enroll user
            $enroll_user = new GravityPressFeedAddOn();
            $feed_process = $enroll_user->process_feed_enroll_user($feed, $entry, $form, $user_id);
            return $feed_process;
        }

        return false;

    }

    /**
     * Return url of given notifier for the current gateway
     *
     * @param $action string
     * @return url string
     */
    public function get_notify_url($action)
    {

        $paypal_obj = new MeprPayPalStandardGateway();
        $paypal_mp_settings = $this->gp_memberpress_settings($this->gateway_name);

        $url = '';
        if (!empty($paypal_mp_settings)) {
            $gateway_id = $paypal_mp_settings->id;
            // set gateway id of MP paypal object, to get correct notifier url
            $paypal_obj->id = $gateway_id;
            $url = $paypal_obj->notify_url($action);
            return $url;
        }

        return $url;
    }

    /**
     * Finalize the subscrition/traansaction process after creating new/upgrade/downgrade subscription
     * Add notes to the specific entry
     * change entry status to PAID if successful transaction
     * save form meta data
     *
     * @param string $type - recurring or single
     * @param array $sub - subscription object
     */
    public function gp_mepr_payment_complete($sub_type, $sub)
    {

        // log message that we are processing the MP subscription created successfully.
        $this->gp_debug_log(__METHOD__ . "(): processing the subscription/transaction created in Memberpress.");

        $gateway = $this->gp_get_gateway_detail($sub->gateway);

        // run below process only for paypal gateway
        if ($gateway->name != $this->gateway_name) return;

        if ($sub_type == 'recurring') {
            $first_txn = $sub->latest_txn();
            $txn_id = $first_txn->id;
        } else {
            $txn_id = $sub->id;
        }

        $txn = new MeprTransaction($txn_id);

        // get entry id from transaction id
        $entry_id = gform_get_meta($txn_id, 'gp_memberpress_trans_gf_entry');
        if (empty($entry_id)) {
            $this->gp_debug_log(__METHOD__ . "(): entry ID missing.");
            return;
        }

        // get entry detail
        $entry = GFAPI::get_entry($entry_id);

        // get user ID of temp user, if created
        $temp_user_id = gform_get_meta($entry['id'], 'gp_temp_user_id');

        // get user registration feed
        $ur_feeds = GFAPI::get_feeds(null, $entry['form_id'], 'gravityformsuserregistration');

        // get delay option
        $registration_delay = gform_get_meta($entry['id'], 'gp_delay_registration');

        // process user creation process if the following conditions fulfill
        // if Registration ad-on is activated, registration feed is enabled, delay checkbox is checked in payment feed and 
        // no user id is assigned to subscrition
        $is_user_activation = false;
        if (function_exists('gf_user_registration') && !is_wp_error($ur_feeds) && ($sub->user_id == $this->temp_user_id || $sub->user_id == 0 || true == $this->temp_user_exist($entry))) {

            $is_user_activation = rgars($ur_feeds[0], 'meta/userActivationEnable') == true;

            if (!$is_user_activation && $registration_delay == 1) {
                // process user createion using GF methods
                $user_id = $this->gp_process_create_user($entry, $ur_feeds[0]);
            }

            if ($is_user_activation) {
                $user_id = $this->gp_process_user_activation($entry, $ur_feeds[0], $sub, $txn);
                // Since User Activation is enabled, we need to destroy the subscription if created by setting the user id to 0
                $sub->user_id = $this->temp_user_id;
            }
            // user created successfully, update current subscription with new user id
            if ($user_id > 0) {
                // update subscription user id
                if ($sub_type == 'recurring') {
                    $sub = new MeprSubscription($sub->id);
                    $sub->user_id = $user_id;
                    $sub->store();
                }

                // update sub user id
                if ($sub_type != 'recurring') {
                    $sub->user_id = $user_id;
                }

                // update txn with newly created user id
                $txn->user_id = $user_id;
                $txn->store();

            }

        }

        // check if for any reason, user is still having temp user ID, just destroy the subscription/transaction
        // check if temp user still exist
        if ($sub->user_id == $this->temp_user_id || $sub->user_id == 0) {
            if ($sub_type != 'recurring') {
                $txn->destroy();
            } else {
                $sub = new MeprSubscription($sub->id);
                $sub->destroy();
            }

            // if Enable User Activation options is disabled, add following note
            if (!$is_user_activation) {
                // add log and debug
                $this->gp_debug_log(__METHOD__ . "(): transaction/subscription deleted due to failed registration.");
                GFFormsModel::add_note($entry_id, 0, 'Gravity Press', 'Transaction/Subscription deleted due to incomplete registration process ');
            }

            // If Activation option is enabled
            if ($is_user_activation) {
                GFFormsModel::add_note($entry_id, 0, 'Gravity Press', 'User set as pending, Please activate user to complete the Memberpress process.');
            }

            // delete meta data
            gform_delete_meta($entry_id, 'gp_memberpress_trans');
            gform_delete_meta($entry_id, 'gp_memberpress_sub');
            gform_delete_meta($entry_id, 'gp_delay_registration');

            // set cron to delete temp users
            $this->set_temp_user_cron($entry_id);
            return;
        }

        // process corporate feature
        $this->gp_process_corporate_memberships($txn, $sub_type);

        // save subscription detail with related entry / transaction detial for non-recurring and subscription detial for recurring
        gform_update_meta($entry_id, 'gp_memberpress_sub', $sub, $entry['form_id']);
        gform_update_meta($entry_id, 'gp_memberpress_trans', $txn, $entry['form_id']);

        // get transaction and add notes on entry detail page
        if ($txn->status == MeprTransaction::$complete_str || $txn->status == MeprTransaction::$confirmed_str) {

            // save notes
            if ($sub_type != 'single') {
                // send email to user for recurring subscriptions
                $this->gp_send_notices('signup_notice', $txn);
                GFFormsModel::add_note($entry_id, 0, 'Gravity Press', 'Subscription process complete. Subscription ID: ' . $sub->subscr_id, 'success');
            } else {
                GFFormsModel::add_note($entry_id, 0, 'Gravity Press', 'Memberpress transaction process completed. Transaction ID: ' . $txn->trans_num, 'success');
            }

            // update entry status
            GFAPI::update_entry_property($entry_id, 'payment_status', 'Paid');
            $this->gp_debug_log(__METHOD__ . "(): transaction completed successfully.");
        } else {
            GFFormsModel::add_note($entry_id, 0, 'Gravity Press', 'Transaction not complete. Transaction ID: ' . $txn->trans_num);
            $this->gp_debug_log(__METHOD__ . "(): transaction not completed due to some reasons.");
        }

        // delete meta data
        gform_delete_meta($entry['id'], 'gp_delay_registration');

        // reset all global vars
        $this->reset_global_vars();

        // set cron to delete temp users
        $this->set_temp_user_cron($entry_id);

    }

    /**
     * This method execute Memberpress hooks - new/upgrade/downgrade subscriptions
     *
     * @param string $type - recurring / single
     * @param array $sub - subscription object
     */
    public function gp_mepr_final_process($type, $sub)
    {
        $this->gp_mepr_payment_complete($type, $sub);
    }

    /**
     * set schedule after creating initial subscription which will delete subscription if it still having
     * temp user ID after specific time passed
     *
     * @param $txn array transaction object
     */
    public function gp_set_temp_schedule_event($txn)
    {

        // set schedule
        $sub_id = $txn->subscription_id;
        $type = ($sub_id > 0) ? 'recurring' : 'non-recurring';
        $txn_id = ($type == 'recurring') ? $sub_id : $txn->id;

        //$delay = 1 * MINUTE_IN_SECONDS;
        $time_delay = current_time('timestamp') + ($this->time_delay * MINUTE_IN_SECONDS);
        $time_delay = $time_delay - (get_option('gmt_offset') * 60 * 60);
        wp_schedule_single_event($time_delay, 'gp_remove_temp_transaction', array($type, $txn_id, $txn->user_id));

    }

    /**
     * Scheduled event action
     *
     * @param string $type - recurring/non-recurring
     * @param int $txn_id
     * @param int $user_id - to identify when user having temp id
     */
    public function gp_delete_temp_transactions($type, $txn_id, $user_id)
    {

        $entry_id = gform_get_meta($txn_id, 'gp_memberpress_trans_gf_entry');
        $temp_userid = gform_get_meta($entry_id, 'gp_temp_user_id');

        // delete transactions/subscription of temp user ID
        if ($type != 'recurring') {
            $txn1 = new MeprTransaction($txn_id);
            if ($txn1->user_id == $this->temp_user_id || $txn1->user_id == $temp_userid) {
                $txn1->destroy();
            }
        } else {
            $sub = new MeprSubscription($txn_id);
            if ($sub->user_id == $this->temp_user_id || $sub->user_id == $temp_userid) {
                $sub->destroy();
            }
        }

        // delete temp users of this entry if created.
        if (!empty($entry_id)) {
            // get entry detail
            $entry = GFAPI::get_entry($entry_id);
            $this->delete_temp_user($entry);
        }

        $this->gp_debug_log(__METHOD__ . "(): transaction/subscription with ID = " . $txn_id . " deleted having temp user ID becuase of incompelete payment.");
        return;
    }

    /**
     * Process after transaction creation and handle for corporate feature in recurring and non-recurring subscriptions
     *
     *
     * @param object $txn The txn object
     * @param array $feed_settings The GP feed settings
     * @param string $signup_type - recurring/non-recurring
     * @since 3.0
     */
    public function gp_process_subscription($txn, $feed_settings, $entry, $signup_type)
    {
        if ($txn->user_id != $this->temp_user_id && $signup_type == 'non-recurring') {
//        if ($txn->user_id != $this->temp_user_id) {
            $this->gp_process_corporate_memberships($txn, $signup_type);
        }
        return;
    }

    /**
     * Process corporate memberships
     */
    public function gp_process_corporate_memberships($txn, $signup_type)
    {
        if (!class_exists('MPCA_Corporate_Account')) return;

        $obj_id = ($signup_type == 'recurring') ? $txn->subscription_id : $txn->id;
        $corporate = gform_get_meta($txn->id, 'gp_memberpress_corporate');
        $this->gp_save_corporate_accounts($obj_id, $txn, $corporate);

        return;
    }

}

new GPPaypalStandardGateway();
?>
