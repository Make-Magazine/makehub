<?php

namespace EE_Stripe_6_35_1\Sigma;

/**
 * Class Authorization
 *
 * @property string $id
 * @property string $object
 * @property int $created
 * @property int $data_load_time
 * @property string $error
 * @property \EE_Stripe_6_35_1\FileUpload $file
 * @property bool $livemode
 * @property int $result_available_until
 * @property string $sql
 * @property string $status
 * @property string $title
 *
 * @package Stripe\Sigma
 */
class ScheduledQueryRun extends \EE_Stripe_6_35_1\ApiResource
{
    const OBJECT_NAME = "scheduled_query_run";

    use \EE_Stripe_6_35_1\ApiOperations\All;
    use \EE_Stripe_6_35_1\ApiOperations\Retrieve;

    public static function classUrl()
    {
        return "/v1/sigma/scheduled_query_runs";
    }
}
