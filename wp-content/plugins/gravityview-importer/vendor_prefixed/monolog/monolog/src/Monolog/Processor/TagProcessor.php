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
 * Adds a tags array into record
 *
 * @author Martijn Riemers
 */
class TagProcessor implements ProcessorInterface
{
    private $tags;

    public function __construct(array $tags = array())
    {
        $this->setTags($tags);
    }

    public function addTags(array $tags = array())
    {
        $this->tags = array_merge($this->tags, $tags);
    }

    public function setTags(array $tags = array())
    {
        $this->tags = $tags;
    }

    public function __invoke(array $record)
    {
        $record['extra']['tags'] = $this->tags;

        return $record;
    }
}
