<?php
/**
 * @license MIT
 *
 * Modified by gravityview on 23-February-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\GravityView\Foundation\ThirdParty\Illuminate\Contracts\Broadcasting;

interface ShouldBroadcast
{
    /**
     * Get the channels the event should broadcast on.
     *
     * @return array
     */
    public function broadcastOn();
}
