<?php

namespace EE_Stripe_6_35_1\Issuing;

/**
 * Class CardDetails
 *
 * @property string $id
 * @property string $object
 * @property Card $card
 * @property string $cvc
 * @property int $exp_month
 * @property int $exp_year
 * @property string $number
 *
 * @package Stripe\Issuing
 */
class CardDetails extends \EE_Stripe_6_35_1\ApiResource
{
    const OBJECT_NAME = "issuing.card_details";
}
