<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Modified by gravityview on 23-February-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\GravityView\Symfony\Component\Finder\Tests\Iterator;

use GravityKit\GravityView\Symfony\Component\Finder\Comparator\NumberComparator;
use GravityKit\GravityView\Symfony\Component\Finder\Iterator\SizeRangeFilterIterator;

class SizeRangeFilterIteratorTest extends RealIteratorTestCase
{
    /**
     * @dataProvider getAcceptData
     */
    public function testAccept($size, $expected)
    {
        $inner = new InnerSizeIterator(self::$files);

        $iterator = new SizeRangeFilterIterator($inner, $size);

        $this->assertIterator($expected, $iterator);
    }

    public function getAcceptData()
    {
        $lessThan1KGreaterThan05K = [
            '.foo',
            '.git',
            'foo',
            'test.php',
            'toto',
            'toto/.git',
        ];

        return [
            [[new NumberComparator('< 1K'), new NumberComparator('> 0.5K')], $this->toAbsolute($lessThan1KGreaterThan05K)],
        ];
    }
}

class InnerSizeIterator extends \ArrayIterator
{
    public function current()
    {
        return new \SplFileInfo(parent::current());
    }

    public function getFilename()
    {
        return parent::current();
    }

    public function isFile()
    {
        return $this->current()->isFile();
    }

    public function getSize()
    {
        return $this->current()->getSize();
    }
}
