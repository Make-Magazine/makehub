<?php

if (!defined('ABSPATH')) {
    die('You are not allowed to call this page directly.');
}

/**
 * @author kumaranup594
 * @since 3.3.1
 * @description This class will manage the Gravity forms PayPal checkout add-on support
 */
class GPPaypalCheckoutGateway extends GP_Payment_Gateways_Common {

    /**
     * Payment gateway name
     * @var public | string 
     */
    public $gateway_name = 'PayPal Express Checkout';
    public $time_delay = 20;

    /**
     * Default constructor.
     * 
     * @since 3.3.1
     */
    public function __construct() {
        $this->add_actions();
    }

    /**
     * @authore kumaranup594
     * @since 3.3.1
     * @description This function will add all the required filters and actions for Gravity Forms PayPal checkout
     */
    private function add_actions() {
        add_action("gform_user_registered", array($this, "gp_register_paypal_new_user"), 9, 4);

        add_action('mepr-txn-store', array($this, 'delete_transaction'), 10, 3);
    }

    /**
     * @since 3.3.1
     * 
     * @global type $paypal_checkout_customer
     * @param type $user_id
     * @param type $feed
     * @param type $entry
     * @param type $user_pass
     */
    public function gp_register_paypal_new_user($user_id, $feed, $entry, $user_pass) {

//        Checking the entries having PayPal checkout or not
        if (!empty($entry) && in_array('PayPal Checkout', $entry)) {
            global $paypal_checkout_customer;
            $paypal_checkout_customer = $this->gateway_name;
        }
    }

    /**
     * @since 3.3.1
     * 
     * @param type $txn
     * @param type $feed_settings
     * @param type $entry
     * @param type $signup_type
     */
    public function gp_process_subscription($txn, $feed_settings, $entry, $signup_type) {

        $sub = $this->process_subscription($entry, $txn, $signup_type);
        if ($sub == false || $signup_type == 'non-recurring') {
            return;
        }

        gform_update_meta($entry['id'], 'gp_memberpress_sub', $sub, $entry['form_id']);
        gform_update_meta($entry['id'], 'gp_memberpress_trans', $txn, $entry['form_id']);
    }

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

}

new GPPaypalCheckoutGateway();
