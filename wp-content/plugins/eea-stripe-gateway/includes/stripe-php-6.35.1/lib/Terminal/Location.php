<?php

namespace EE_Stripe_6_35_1\Terminal;

/**
 * Class Location
 *
 * @property string $id
 * @property string $object
 * @property mixed $address
 * @property bool $deleted
 * @property string $display_name
 *
 * @package Stripe\Terminal
 */
class Location extends \EE_Stripe_6_35_1\ApiResource
{
    const OBJECT_NAME = "terminal.location";

    use \EE_Stripe_6_35_1\ApiOperations\All;
    use \EE_Stripe_6_35_1\ApiOperations\Create;
    use \EE_Stripe_6_35_1\ApiOperations\Delete;
    use \EE_Stripe_6_35_1\ApiOperations\Retrieve;
    use \EE_Stripe_6_35_1\ApiOperations\Update;
}
