<?php

namespace EE_Stripe_6_35_1\Terminal;

/**
 * Class ConnectionToken
 *
 * @property string $secret
 *
 * @package Stripe\Terminal
 */
class ConnectionToken extends \EE_Stripe_6_35_1\ApiResource
{
    const OBJECT_NAME = "terminal.connection_token";

    use \EE_Stripe_6_35_1\ApiOperations\Create;
}
