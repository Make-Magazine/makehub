<?php

namespace EE_Stripe_6_35_1;

/**
 * Class UsageRecord
 *
 * @package Stripe
 *
 * @property string $id
 * @property string $object
 * @property string $invoice
 * @property bool $livemode
 * @property mixed $period
 * @property string $subscription_item
 * @property int $total_usage
 */
class UsageRecordSummary extends ApiResource
{

    const OBJECT_NAME = "usage_record_summary";
}
