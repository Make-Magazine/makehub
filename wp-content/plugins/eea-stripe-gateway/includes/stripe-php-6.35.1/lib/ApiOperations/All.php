<?php

namespace EE_Stripe_6_35_1\ApiOperations;

/**
 * Trait for listable resources. Adds a `all()` static method to the class.
 *
 * This trait should only be applied to classes that derive from StripeObject.
 */
trait All
{
    /**
     * @param array|null $params
     * @param array|string|null $opts
     *
     * @return \EE_Stripe_6_35_1\Collection of ApiResources
     */
    public static function all($params = null, $opts = null)
    {
        self::_validateParams($params);
        $url = static::classUrl();

        list($response, $opts) = static::_staticRequest('get', $url, $params, $opts);
        $obj = \EE_Stripe_6_35_1\Util\Util::convertToStripeObject($response->json, $opts);
        if (!is_a($obj, 'Stripe\\Collection')) {
            $class = get_class($obj);
            $message = "Expected type \"Stripe\\Collection\", got \"$class\" instead";
            throw new \EE_Stripe_6_35_1\Error\Api($message);
        }
        $obj->setLastResponse($response);
        $obj->setRequestParams($params);
        return $obj;
    }
}
