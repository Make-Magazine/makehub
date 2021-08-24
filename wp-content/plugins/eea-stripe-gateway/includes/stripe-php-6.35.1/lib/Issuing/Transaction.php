<?php

namespace EE_Stripe_6_35_1\Issuing;

/**
 * Class Transaction
 *
 * @property string $id
 * @property string $object
 * @property int $amount
 * @property string $authorization
 * @property string $balance_transaction
 * @property string $card
 * @property string $cardholder
 * @property int $created
 * @property string $currency
 * @property string $dispute
 * @property bool $livemode
 * @property mixed $merchant_data
 * @property \EE_Stripe_6_35_1\StripeObject $metadata
 * @property string $type
 *
 * @package Stripe\Issuing
 */
class Transaction extends \EE_Stripe_6_35_1\ApiResource
{
    const OBJECT_NAME = "issuing.transaction";

    use \EE_Stripe_6_35_1\ApiOperations\All;
    use \EE_Stripe_6_35_1\ApiOperations\Create;
    use \EE_Stripe_6_35_1\ApiOperations\Retrieve;
    use \EE_Stripe_6_35_1\ApiOperations\Update;
}
