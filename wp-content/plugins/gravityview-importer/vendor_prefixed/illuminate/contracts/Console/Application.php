<?php
/**
 * @license MIT
 *
 * Modified by The GravityKit Team on 01-December-2022 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityImport\Foundation\ThirdParty\Illuminate\Contracts\Console;

interface Application
{
    /**
     * Call a console application command.
     *
     * @param  string  $command
     * @param  array  $parameters
     * @return int
     */
    public function call($command, array $parameters = []);

    /**
     * Get the output from the last command.
     *
     * @return string
     */
    public function output();
}
