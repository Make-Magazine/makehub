<?php

namespace EE_Stripe_6_35_1\Issuing;

/**
 * Class Authorization
 *
 * @property string $id
 * @property string $object
 * @property bool $approved
 * @property string $authorization_method
 * @property int $authorized_amount
 * @property string $authorized_currency
 * @property \EE_Stripe_6_35_1\Collection $balance_transactions
 * @property Card $card
 * @property Cardholder $cardholder
 * @property int $created
 * @property int $held_amount
 * @property string $held_currency
 * @property bool $is_held_amount_controllable
 * @property bool $livemode
 * @property mixed $merchant_data
 * @property \EE_Stripe_6_35_1\StripeObject $metadata
 * @property int $pending_authorized_amount
 * @property int $pending_held_amount
 * @property mixed $request_history
 * @property string $status
 * @property \EE_Stripe_6_35_1\Collection $transactions
 * @property mixed $verification_data
 *
 * @package Stripe\Issuing
 */
class Authorization extends \EE_Stripe_6_35_1\ApiResource
{
    const OBJECT_NAME = "issuing.authorization";

    use \EE_Stripe_6_35_1\ApiOperations\All;
    use \EE_Stripe_6_35_1\ApiOperations\Retrieve;
    use \EE_Stripe_6_35_1\ApiOperations\Update;

    /**
     * @param array|null $params
     * @param array|string|null $options
     *
     * @return Authorization The approved authorization.
     */
    public function approve($params = null, $options = null)
    {
        $url = $this->instanceUrl() . '/approve';
        list($response, $opts) = $this->_request('post', $url, $params, $options);
        $this->refreshFrom($response, $opts);
        return $this;
    }

    /**
     * @param array|null $params
     * @param array|string|null $options
     *
     * @return Authorization The declined authorization.
     */
    public function decline($params = null, $options = null)
    {
        $url = $this->instanceUrl() . '/decline';
        list($response, $opts) = $this->_request('post', $url, $params, $options);
        $this->refreshFrom($response, $opts);
        return $this;
    }
}
