<?php

// File generated from our OpenAPI spec

namespace StellarWP\Learndash\Stripe\Service\Tax;

class CalculationService extends \StellarWP\Learndash\Stripe\Service\AbstractService
{
    /**
     * Retrieves the line items of a persisted tax calculation as a collection.
     *
     * @param string $id
     * @param null|array $params
     * @param null|array|\StellarWP\Learndash\Stripe\Util\RequestOptions $opts
     *
     * @throws \StellarWP\Learndash\Stripe\Exception\ApiErrorException if the request fails
     *
     * @return \StellarWP\Learndash\Stripe\Collection<\Stripe\Tax\CalculationLineItem>
     *
     * @license MIT
     * Modified by learndash on 14-February-2024 using Strauss.
     * @see https://github.com/BrianHenryIE/strauss
     */
    public function allLineItems($id, $params = null, $opts = null)
    {
        return $this->requestCollection('get', $this->buildPath('/v1/tax/calculations/%s/line_items', $id), $params, $opts);
    }

    /**
     * Calculates tax based on input and returns a Tax <code>Calculation</code> object.
     *
     * @param null|array $params
     * @param null|array|\StellarWP\Learndash\Stripe\Util\RequestOptions $opts
     *
     * @throws \StellarWP\Learndash\Stripe\Exception\ApiErrorException if the request fails
     *
     * @return \StellarWP\Learndash\Stripe\Tax\Calculation
     */
    public function create($params = null, $opts = null)
    {
        return $this->request('post', '/v1/tax/calculations', $params, $opts);
    }
}
