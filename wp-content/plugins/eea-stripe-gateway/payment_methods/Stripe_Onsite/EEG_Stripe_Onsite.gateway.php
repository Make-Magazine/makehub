<?php

use EE_Stripe_6_35_1\Charge;
use EE_Stripe_6_35_1\Error\Base;
use EE_Stripe_6_35_1\Error\Card;
use EE_Stripe_6_35_1\PaymentIntent;
use EE_Stripe_6_35_1\Stripe;
use EventEspresso\core\exceptions\InvalidDataTypeException;
use EventEspresso\core\exceptions\InvalidInterfaceException;

/**
 *
 * EEG_Stripe_Onsite
 *
 * Just approves payments where billing_info[ 'credit_card' ] == 1.
 * If $billing_info[ 'credit_card' ] == '2' then its pending.
 * All others get refused
 *
 * @package            Event Espresso
 * @subpackage        espresso-stripe-gateway
 * @author            Event Espresso
 *
 */
class EEG_Stripe_Onsite extends EE_Onsite_Gateway
{

    protected $_publishable_key = null;

    protected $_secret_key = null;

    /**
     * @var string indicating which integration method was chosen.
     */
    protected $_integration = 'checkout';

    /**
     * All the currencies supported by this gateway. Add any others you like,
     * as contained in the esp_currency table
     * @var array
     */
    protected $_currencies_supported = EE_Gateway::all_currencies_supported;

    /**
     *
     * @param EEI_Payment $payment
     * @param array $billing_info
     * @return EEI_Payment
     * @throws EE_Error
     */
    public function do_direct_payment($payment, $billing_info = null)
    {
        if (!$payment instanceof EEI_Payment) {
            $payment->set_gateway_response(__('Error. No associated payment was found.', 'event_espresso'));
            $payment->set_status($this->_pay_model->failed_status());
            return $payment;
        }
        $transaction = $payment->transaction();
        if (!$transaction instanceof EE_Transaction) {
            $payment->set_gateway_response(__('Could not process this payment because it has no associated transaction.', 'event_espresso'));
            $payment->set_status($this->_pay_model->failed_status());
            return $payment;
        }
        // If this merchant is using Stripe Connect we need a to use the connected account token.
        $payment_method = $transaction->payment_method();
        if (!$payment_method instanceof EE_Payment_Method) {
            $payment->set_gateway_response(
                esc_html__(
                    'Error. No payment method on this transaction, although we know its Stripe.',
                    'event_espresso'
                )
            );
            $payment->set_status($this->_pay_model->failed_status());
            return $payment;
        }
        $key = apply_filters(
            'FHEE__EEG_Stripe_Onsite__do_direct_payment__use_connected_account_token',
            $this->_secret_key,
            $transaction->payment_method()
        );
        $this->initializeStripeLibrary($key);
        if ($this->_integration === 'elements') {
            $payment = $this->doDirectPaymentWithPaymentIntents($payment, $transaction, $billing_info);
        } else {
            $payment = $this->doDirectPaymentWithCheckout($payment, $transaction, $billing_info);
        }
        return $payment;
    }

    /**
     * Performs the payment with Stripe Payment Intent, and updates the payment object accordingly.
     * @since 1.1.4.p
     * @param EE_Payment $payment
     * @param EE_Transaction $transaction
     * @param null $billing_info
     * @reutn EE_Payment
     * @throws EE_Error
     * @throws InvalidArgumentException
     * @throws ReflectionException
     * @throws InvalidDataTypeException
     * @throws InvalidInterfaceException
     */
    protected function doDirectPaymentWithPaymentIntents(
        EE_Payment $payment,
        EE_Transaction $transaction,
        $billing_info = null
    ) {
        // The JS got the user's card ("payment method") now let's tell Stripe what they are to pay...
        $card_payment_method_id = isset($billing_info['ee_stripe_payment_method_id']) ? $billing_info['ee_stripe_payment_method_id'] : '';
        $payment_intent_id = isset($billing_info['ee_stripe_payment_intent_id']) ? $billing_info['ee_stripe_payment_intent_id'] : '';
        $payment_intent_data = $stripe_data = apply_filters(
            'FHEE__EEG_Stripe_Onsite__doDirectPaymentWithPaymentIntents__payment_intent_data',
            [
                'payment_method' => $card_payment_method_id,
                'amount' => $this->prepare_amount_for_stripe($payment->amount()),
                'currency' => strtolower($payment->currency_code()),
                'confirmation_method' => 'manual',
                'confirm' => true,
                'description' => $this->_get_gateway_formatter()->formatOrderDescription($payment),
            ],
            $payment,
            $transaction,
            $billing_info
        );
        $intent = null;
        try {
            if ($card_payment_method_id) {
                # Create the PaymentIntent
                $intent = PaymentIntent::create($payment_intent_data);
            }
            if ($payment_intent_id) {
                $in_progress_payment = $this->_pay_model->get_payment_by_txn_id_chq_nmbr($payment_intent_id);
                if ($in_progress_payment instanceof EE_Payment) {
                    // Update the payment that's already in progress, instead of making a new one.
                    $payment->delete();
                    $payment = $in_progress_payment;
                }
                $intent = PaymentIntent::retrieve(
                    $payment_intent_id
                );
                $intent->confirm();
                $this->_pay_log->gateway_log(
                    [
                        'payment intent' => $intent->jsonSerialize(),
                    ],
                    $payment->ID(),
                    'Payment'
                );
            } else {
                $this->_pay_log->gateway_log(
                    [
                        'payment_intent' => esc_html__('No payment intent ID present, so no request to create a payment intent created.', 'event_espresso')
                    ],
                    $payment->ID(),
                    'Payment'
                );
            }

            if (! $intent) {
                $payment->set_gateway_response(esc_html__('No payment information found.', 'event_espresso'));
                $payment->set_status($this->_pay_model->failed_status());
                return $payment;
            }
            if ($intent->status === 'requires_action'
                && is_object($intent->next_action)
                && $intent->next_action->type === 'use_stripe_sdk') {
                # Tell the client to handle the action
                $payment->set_gateway_response(esc_html__('Payment requires further verification.', 'event_espresso'));
                $payment->set_status($this->_pay_model->declined_status());
                $payment->set_txn_id_chq_nmbr($intent->id);

                // Don't send messsages on this request. We don't want to send a payment failed message in this case.
                remove_all_filters('AHEE__EE_Payment_Processor__update_txn_based_on_payment');

                //   Add to SPCO's JSON response so JS code knows more verification is needed.
                add_filter(
                    'FHEE__EE_SPCO_JSON_Response___toString__JSON_response',
                    function ($json_ready_array) use ($intent) {
                        $json_ready_array['stripe'] = [
                            'requires_action' => true,
                            'payment_intent_client_secret' => $intent->client_secret
                        ];
                        return $json_ready_array;
                    }
                );
            } elseif ($intent->status == 'succeeded') {
                # The payment didn’t need any additional actions and completed!
                # Handle post-payment fulfillment
                $payment->set_status($this->_pay_model->approved_status());
                $payment->set_gateway_response(esc_html__('Success', 'event_espresso'));
                $payment->set_txn_id_chq_nmbr($intent->id);
            } else {
                # Invalid status
                $payment->set_status($this->_pay_model->failed_status());
                $payment->set_gateway_response(esc_html__('Invalid PaymentIntent status', 'event_espresso'));
            }
        } catch (Base $e) {
            $payment->set_status($this->_pay_model->declined_status());
            $payment->set_gateway_response($e->getMessage());
        }
        return $payment;
    }

    /**
     * Performs the payment with Stripe Checkout and updates the payment object accordingly.
     * @since 1.1.4.p
     * @param EE_Payment $payment
     * @param EE_Transaction $transaction
     * @param null $billing_info
     * @return EE_Payment
     * @throws EE_Error
     */
    protected function doDirectPaymentWithCheckout(EE_Payment $payment, EE_Transaction $transaction, $billing_info = null)
    {
        $stripe_data = array(
            'amount' => $this->prepare_amount_for_stripe($payment->amount()),

            'currency' => $payment->currency_code(),
            'card' => $billing_info['ee_stripe_token'],
            'description' => $billing_info['ee_stripe_prod_description']
        );
        $stripe_data = apply_filters('FHEE__EEG_Stripe_Onsite__do_direct_payment__stripe_data_array', $stripe_data, $payment, $transaction, $billing_info);

        // Create the charge on Stripe's servers - this will charge the user's card.
        try {
            $this->log(array('Stripe Request data:' => $stripe_data), $payment);
            $charge = Charge::create($stripe_data);
        } catch (Card $error) {
            $payment->set_status($this->_pay_model->declined_status());
            $payment->set_gateway_response($error->getMessage());
            $this->log(array('Stripe Error occurred:' => $error), $payment);
            return $payment;
        } catch (Exception $exception) {
            $payment->set_status($this->_pay_model->failed_status());
            $payment->set_gateway_response($exception->getMessage());
            $this->log(array('Stripe Error occurred:' => $exception), $payment);
            return $payment;
        }

        $charge_array = $charge->__toArray(true);
        $this->log(array('Stripe charge:' => $charge_array), $payment);
        $payment->set_gateway_response($charge_array['status']);
        $payment->set_txn_id_chq_nmbr($charge_array['id']);
        $payment->set_details($charge_array);
        $payment->set_amount(floatval($this->prepare_amount_from_stripe($charge_array['amount'])));
        $payment->set_status($this->_pay_model->approved_status());
        return $payment;
    }

    /**
     * Includes the Stripe PHP Library, and sets the API key and App Info.
     * @since 1.1.6.p
     * @param string $key
     */
    protected function initializeStripeLibrary($key)
    {
        require_once(EE_STRIPE_PATH . 'includes/stripe-php-6.35.1/init.php');

        Stripe::setApiKey($key);
        Stripe::setAppInfo(
            'WordPress EventEspresso Stripe Elements',
            EE_STRIPE_VERSION,
            'https://eventespresso.com',
            'pp_partner_G8WTFFUgavic4D'// Event Espresso app partner ID
        );
        // See https://stripe.com/docs/upgrades#2019-12-03
        Stripe::setApiVersion('2019-12-03');
    }

    /**
     * Gets the number of decimal places Stripe expects a currency to have.
     * See https://stripe.com/docs/currencies#charge-currencies for the list.
     *
     * @param string $currency Accepted currency.
     * @return int
     */
    public function get_stripe_decimal_places($currency = '')
    {
        if (!$currency) {
            $currency = EE_Registry::instance()->CFG->currency->code;
        }
        switch (strtoupper($currency)) {
            // Zero decimal currencies.
            case 'BIF':
            case 'CLP':
            case 'DJF':
            case 'GNF':
            case 'JPY':
            case 'KMF':
            case 'KRW':
            case 'MGA':
            case 'PYG':
            case 'RWF':
            case 'VND':
            case 'VUV':
            case 'XAF':
            case 'XOF':
            case 'XPF':
                return 0;
            default:
                return 2;
        }
    }


    /**
     * Converts an amount into the currency's subunits as expected by Stripe.
     * (Some currencies have no subunits, so leaves them in the currency's main units).
     * @param float $amount
     * @return int in the currency's smallest unit (e.g., pennies)
     */
    public function prepare_amount_for_stripe($amount)
    {
        return $amount * pow(10, $this->get_stripe_decimal_places());
    }


    /**
     * Converts an amount from Stripe (in the currency's subunits) to a
     * float as used by EE
     * @param $amount
     * @return float
     */
    public function prepare_amount_from_stripe($amount)
    {
        return $amount / pow(10, $this->get_stripe_decimal_places());
    }
}
