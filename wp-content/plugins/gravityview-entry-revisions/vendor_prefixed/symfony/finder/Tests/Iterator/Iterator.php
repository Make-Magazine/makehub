<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Modified by GravityKit on 12-December-2023 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityRevisions\Symfony\Component\Finder\Tests\Iterator;

class Iterator implements \Iterator
{
    protected $values = [];

    public function __construct(array $values = [])
    {
        foreach ($values as $value) {
            $this->attach(new \SplFileInfo($value));
        }
        $this->rewind();
    }

    public function attach(\SplFileInfo $fileinfo)
    {
        $this->values[] = $fileinfo;
    }

    public function rewind()
    {
        reset($this->values);
    }

    public function valid()
    {
        return false !== $this->current();
    }

    public function next()
    {
        next($this->values);
    }

    public function current()
    {
        return current($this->values);
    }

    public function key()
    {
        return key($this->values);
    }
}
