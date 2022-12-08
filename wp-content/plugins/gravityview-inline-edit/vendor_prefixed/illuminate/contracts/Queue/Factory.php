<?php
/**
 * @license MIT
 *
 * Modified by __root__ on 01-December-2022 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityEdit\Foundation\ThirdParty\Illuminate\Contracts\Queue;

interface Factory
{
    /**
     * Resolve a queue connection instance.
     *
     * @param  string  $name
     * @return \GravityKit\GravityEdit\Foundation\ThirdParty\Illuminate\Contracts\Queue\Queue
     */
    public function connection($name = null);
}
