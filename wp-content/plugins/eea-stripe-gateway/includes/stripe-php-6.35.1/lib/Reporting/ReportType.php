<?php

namespace EE_Stripe_6_35_1\Reporting;

/**
 * Class ReportType
 *
 * @property string $id
 * @property string $object
 * @property int $data_available_end
 * @property int $data_available_start
 * @property string $name
 * @property int $updated
 * @property string $version
 *
 * @package Stripe\Reporting
 */
class ReportType extends \EE_Stripe_6_35_1\ApiResource
{
    const OBJECT_NAME = "reporting.report_type";

    use \EE_Stripe_6_35_1\ApiOperations\All;
    use \EE_Stripe_6_35_1\ApiOperations\Retrieve;
}
