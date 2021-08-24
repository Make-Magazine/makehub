<?php

namespace EE_Stripe_6_35_1\Error;

class InvalidRequest extends Base
{
    public function __construct(
        $message,
        $stripeParam,
        $httpStatus = null,
        $httpBody = null,
        $jsonBody = null,
        $httpHeaders = null
    ) {
        parent::__construct($message, $httpStatus, $httpBody, $jsonBody, $httpHeaders);
        $this->stripeParam = $stripeParam;
    }

    public function getStripeParam()
    {
        return $this->stripeParam;
    }
}
