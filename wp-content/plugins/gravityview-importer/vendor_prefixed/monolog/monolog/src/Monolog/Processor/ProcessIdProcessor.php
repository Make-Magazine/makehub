<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Modified by The GravityKit Team on 25-January-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityImport\Foundation\ThirdParty\Monolog\Processor;

/**
 * Adds value of getmypid into records
 *
 * @author Andreas Hörnicke
 */
class ProcessIdProcessor implements ProcessorInterface
{
    /**
     * @param  array $record
     * @return array
     */
    public function __invoke(array $record)
    {
        $record['extra']['process_id'] = getmypid();

        return $record;
    }
}
