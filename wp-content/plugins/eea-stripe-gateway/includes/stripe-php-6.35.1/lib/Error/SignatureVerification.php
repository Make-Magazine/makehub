<?php

namespace EE_Stripe_6_35_1\Error;

class SignatureVerification extends Base
{
    public function __construct(
        $message,
        $sigHeader,
        $httpBody = null
    ) {
        parent::__construct($message, null, $httpBody, null, null);
        $this->sigHeader = $sigHeader;
    }

    public function getSigHeader()
    {
        return $this->sigHeader;
    }
}
