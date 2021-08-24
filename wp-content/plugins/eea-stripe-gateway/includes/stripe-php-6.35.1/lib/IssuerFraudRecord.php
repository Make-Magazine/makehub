<?php

namespace EE_Stripe_6_35_1;

/**
 * Class IssuerFraudRecord
 *
 * @property string $id
 * @property string $object
 * @property string $charge
 * @property int $created
 * @property int $post_date
 * @property string $fraud_type
 * @property bool $livemode
 *
 * @package Stripe
 */
class IssuerFraudRecord extends ApiResource
{

    const OBJECT_NAME = "issuer_fraud_record";

    use ApiOperations\All;
    use ApiOperations\Retrieve;
}
