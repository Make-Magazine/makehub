<?php

namespace EE_Stripe_6_35_1;

/**
 * Class ExchangeRate
 *
 * @package Stripe
 */
class ExchangeRate extends ApiResource
{

    const OBJECT_NAME = "exchange_rate";

    use ApiOperations\All;
    use ApiOperations\Retrieve;
}
