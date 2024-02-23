<?php

// File generated from our OpenAPI spec

namespace StellarWP\Learndash\Stripe\Service\FinancialConnections;

/**
 * Service factory class for API resources in the FinancialConnections namespace.
 *
 * @property AccountService $accounts
 * @property SessionService $sessions
 *
 * @license MIT
 * Modified by learndash on 14-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */
class FinancialConnectionsServiceFactory extends \StellarWP\Learndash\Stripe\Service\AbstractServiceFactory
{
    /**
     * @var array<string, string>
     */
    private static $classMap = [
        'accounts' => AccountService::class,
        'sessions' => SessionService::class,
    ];

    protected function getServiceClass($name)
    {
        return \array_key_exists($name, self::$classMap) ? self::$classMap[$name] : null;
    }
}
