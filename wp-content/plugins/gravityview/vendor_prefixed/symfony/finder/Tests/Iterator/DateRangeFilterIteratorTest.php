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

use GravityKit\GravityView\Symfony\Component\Finder\Comparator\DateComparator;
use GravityKit\GravityView\Symfony\Component\Finder\Iterator\DateRangeFilterIterator;

class DateRangeFilterIteratorTest extends RealIteratorTestCase
{
    /**
     * @dataProvider getAcceptData
     */
    public function testAccept($size, $expected)
    {
        $files = self::$files;
        $files[] = self::toAbsolute('doesnotexist');
        $inner = new Iterator($files);

        $iterator = new DateRangeFilterIterator($inner, $size);

        $this->assertIterator($expected, $iterator);
    }

    public function getAcceptData()
    {
        $since20YearsAgo = [
            '.git',
            'test.py',
            'foo',
            'foo/bar.tmp',
            'test.php',
            'toto',
            'toto/.git',
            '.bar',
            '.foo',
            '.foo/.bar',
            'foo bar',
            '.foo/bar',
        ];

        $since2MonthsAgo = [
            '.git',
            'test.py',
            'foo',
            'toto',
            'toto/.git',
            '.bar',
            '.foo',
            '.foo/.bar',
            'foo bar',
            '.foo/bar',
        ];

        $untilLastMonth = [
            'foo/bar.tmp',
            'test.php',
        ];

        return [
            [[new DateComparator('since 20 years ago')], $this->toAbsolute($since20YearsAgo)],
            [[new DateComparator('since 2 months ago')], $this->toAbsolute($since2MonthsAgo)],
            [[new DateComparator('until last month')], $this->toAbsolute($untilLastMonth)],
        ];
    }
}
