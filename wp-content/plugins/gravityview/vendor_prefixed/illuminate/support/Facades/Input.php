<?php
/**
 * @license MIT
 *
 * Modified by gravityview on 23-February-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\GravityView\Foundation\ThirdParty\Illuminate\Support\Facades;

/**
 * @see \Illuminate\Http\Request
 */
class Input extends Facade
{
    /**
     * Get an item from the input data.
     *
     * This method is used for all request verbs (GET, POST, PUT, and DELETE)
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    public static function get($key = null, $default = null)
    {
        return static::$app['request']->input($key, $default);
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'request';
    }
}
