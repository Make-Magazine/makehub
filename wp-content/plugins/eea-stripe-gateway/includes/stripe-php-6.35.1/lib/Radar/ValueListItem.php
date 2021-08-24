<?php

namespace EE_Stripe_6_35_1\Radar;

/**
 * Class ValueListItem
 *
 * @property string $id
 * @property string $object
 * @property int $created
 * @property string $created_by
 * @property string $list
 * @property bool $livemode
 * @property string $value
 *
 * @package Stripe\Radar
 */
class ValueListItem extends \EE_Stripe_6_35_1\ApiResource
{
    const OBJECT_NAME = "radar.value_list_item";

    use \EE_Stripe_6_35_1\ApiOperations\All;
    use \EE_Stripe_6_35_1\ApiOperations\Create;
    use \EE_Stripe_6_35_1\ApiOperations\Delete;
    use \EE_Stripe_6_35_1\ApiOperations\Retrieve;
}
