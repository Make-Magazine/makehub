<?php
/**
 * @license MIT
 *
 * Modified by The GravityKit Team on 01-December-2022 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityImport\Foundation\ThirdParty\Illuminate\Contracts\Support;

interface MessageProvider
{
    /**
     * Get the messages for the instance.
     *
     * @return \GravityKit\GravityImport\Foundation\ThirdParty\Illuminate\Contracts\Support\MessageBag
     */
    public function getMessageBag();
}
