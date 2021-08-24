<?php

namespace EE_Stripe_6_35_1\Terminal;

/**
 * Class Reader
 *
 * @property string $id
 * @property string $object
 * @property bool $deleted
 * @property string $device_sw_version
 * @property string $device_type
 * @property string $ip_address
 * @property string $label
 * @property string $location
 * @property string $serial_number
 * @property string $status
 *
 * @package Stripe\Terminal
 */
class Reader extends \EE_Stripe_6_35_1\ApiResource
{
    const OBJECT_NAME = "terminal.reader";

    use \EE_Stripe_6_35_1\ApiOperations\All;
    use \EE_Stripe_6_35_1\ApiOperations\Create;
    use \EE_Stripe_6_35_1\ApiOperations\Delete;
    use \EE_Stripe_6_35_1\ApiOperations\Retrieve;
    use \EE_Stripe_6_35_1\ApiOperations\Update;
}
