<?php
/**
 * @license MIT
 *
 * Modified by gravityview on 23-February-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\GravityView\Foundation\ThirdParty\Illuminate\Contracts\Routing;

interface UrlRoutable
{
    /**
     * Get the value of the model's route key.
     *
     * @return mixed
     */
    public function getRouteKey();

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName();
}
