<?php

namespace EE_Stripe_6_35_1\Issuing;

/**
 * Class Card
 *
 * @property string $id
 * @property string $object
 * @property mixed $authorization_controls
 * @property mixed $billing
 * @property string $brand
 * @property Cardholder $cardholder
 * @property int $created
 * @property string $currency
 * @property int $exp_month
 * @property int $exp_year
 * @property string $last4
 * @property bool $livemode
 * @property \EE_Stripe_6_35_1\StripeObject $metadata
 * @property string $name
 * @property mixed $shipping
 * @property string $status
 * @property string $type
 *
 * @package Stripe\Issuing
 */
class Card extends \EE_Stripe_6_35_1\ApiResource
{
    const OBJECT_NAME = "issuing.card";

    use \EE_Stripe_6_35_1\ApiOperations\All;
    use \EE_Stripe_6_35_1\ApiOperations\Create;
    use \EE_Stripe_6_35_1\ApiOperations\Retrieve;
    use \EE_Stripe_6_35_1\ApiOperations\Update;

    /**
     * @param array|null $params
     * @param array|string|null $options
     *
     * @return CardDetails The card details associated with that issuing card.
     */
    public function details($params = null, $options = null)
    {
        $url = $this->instanceUrl() . '/details';
        list($response, $opts) = $this->_request('get', $url, $params, $options);
        $obj = \EE_Stripe_6_35_1\Util\Util::convertToStripeObject($response, $opts);
        $obj->setLastResponse($response);
        return $obj;
    }
}
