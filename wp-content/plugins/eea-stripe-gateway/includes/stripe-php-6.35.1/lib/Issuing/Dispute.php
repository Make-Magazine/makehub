<?php

namespace EE_Stripe_6_35_1\Issuing;

/**
 * Class Dispute
 *
 * @property string $id
 * @property string $object
 * @property int $amount
 * @property int $created
 * @property string $currency
 * @property mixed $evidence
 * @property bool $livemode
 * @property \EE_Stripe_6_35_1\StripeObject $metadata
 * @property string $reason
 * @property string $status
 * @property Transaction $transaction
 *
 * @package Stripe\Issuing
 */
class Dispute extends \EE_Stripe_6_35_1\ApiResource
{
    const OBJECT_NAME = "issuing.dispute";

    use \EE_Stripe_6_35_1\ApiOperations\All;
    use \EE_Stripe_6_35_1\ApiOperations\Create;
    use \EE_Stripe_6_35_1\ApiOperations\Retrieve;
    use \EE_Stripe_6_35_1\ApiOperations\Update;
}
