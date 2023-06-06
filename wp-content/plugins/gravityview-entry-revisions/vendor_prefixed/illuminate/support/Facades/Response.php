<?php
/**
 * @license MIT
 *
 * Modified by GravityKit on 20-February-2023 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityRevisions\Foundation\ThirdParty\Illuminate\Support\Facades;

use GravityKit\GravityRevisions\Foundation\ThirdParty\Illuminate\Contracts\Routing\ResponseFactory as ResponseFactoryContract;

/**
 * @see \GravityKit\GravityRevisions\Foundation\ThirdParty\Illuminate\Contracts\Routing\ResponseFactory
 */
class Response extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return ResponseFactoryContract::class;
    }
}
