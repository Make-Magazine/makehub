<?php

namespace EE_Stripe_6_35_1\Checkout;

/**
 * Class Session
 *
 * @property string $id
 * @property string $object
 * @property string $cancel_url
 * @property string $client_reference_id
 * @property string $customer
 * @property string $customer_email
 * @property mixed $display_items
 * @property bool $livemode
 * @property string $payment_intent
 * @property string[] $payment_method_types
 * @property string $subscription
 * @property string $success_url
 *
 * @package Stripe
 */
class Session extends \EE_Stripe_6_35_1\ApiResource
{

    const OBJECT_NAME = "checkout.session";

    use \EE_Stripe_6_35_1\ApiOperations\Create;
    use \EE_Stripe_6_35_1\ApiOperations\Retrieve;
}
