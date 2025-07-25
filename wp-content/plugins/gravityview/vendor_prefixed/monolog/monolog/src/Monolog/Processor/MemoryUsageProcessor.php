<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Modified by gravityview on 23-February-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\GravityView\Foundation\ThirdParty\Monolog\Processor;

/**
 * Injects memory_get_usage in all records
 *
 * @see GravityKit\GravityView\Foundation\ThirdParty\Monolog\Processor\MemoryProcessor::__construct() for options
 * @author Rob Jensen
 */
class MemoryUsageProcessor extends MemoryProcessor
{
    /**
     * @param  array $record
     * @return array
     */
    public function __invoke(array $record)
    {
        $bytes = memory_get_usage($this->realUsage);
        $formatted = $this->formatBytes($bytes);

        $record['extra']['memory_usage'] = $formatted;

        return $record;
    }
}
