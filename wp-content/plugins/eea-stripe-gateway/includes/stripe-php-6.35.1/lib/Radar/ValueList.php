<?php

namespace EE_Stripe_6_35_1\Radar;

/**
 * Class ValueList
 *
 * @property string $id
 * @property string $object
 * @property string $alias
 * @property int $created
 * @property string $created_by
 * @property string $item_type
 * @property Collection $list_items
 * @property bool $livemode
 * @property StripeObject $metadata
 * @property mixed $name
 * @property int $updated
 * @property string $updated_by
 *
 * @package Stripe\Radar
 */
class ValueList extends \EE_Stripe_6_35_1\ApiResource
{
    const OBJECT_NAME = "radar.value_list";

    use \EE_Stripe_6_35_1\ApiOperations\All;
    use \EE_Stripe_6_35_1\ApiOperations\Create;
    use \EE_Stripe_6_35_1\ApiOperations\Delete;
    use \EE_Stripe_6_35_1\ApiOperations\Retrieve;
    use \EE_Stripe_6_35_1\ApiOperations\Update;
}
