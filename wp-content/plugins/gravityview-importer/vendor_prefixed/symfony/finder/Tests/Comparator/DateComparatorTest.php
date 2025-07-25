<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Modified by The GravityKit Team on 25-January-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityImport\Symfony\Component\Finder\Tests\Comparator;

use PHPUnit\Framework\TestCase;
use GravityKit\GravityImport\Symfony\Component\Finder\Comparator\DateComparator;

class DateComparatorTest extends TestCase
{
    public function testConstructor()
    {
        try {
            new DateComparator('foobar');
            $this->fail('__construct() throws an \InvalidArgumentException if the test expression is not valid.');
        } catch (\Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e, '__construct() throws an \InvalidArgumentException if the test expression is not valid.');
        }

        try {
            new DateComparator('');
            $this->fail('__construct() throws an \InvalidArgumentException if the test expression is not valid.');
        } catch (\Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e, '__construct() throws an \InvalidArgumentException if the test expression is not valid.');
        }
    }

    /**
     * @dataProvider getTestData
     */
    public function testTest($test, $match, $noMatch)
    {
        $c = new DateComparator($test);

        foreach ($match as $m) {
            $this->assertTrue($c->test($m), '->test() tests a string against the expression');
        }

        foreach ($noMatch as $m) {
            $this->assertFalse($c->test($m), '->test() tests a string against the expression');
        }
    }

    public function getTestData()
    {
        return [
            ['< 2005-10-10', [strtotime('2005-10-09')], [strtotime('2005-10-15')]],
            ['until 2005-10-10', [strtotime('2005-10-09')], [strtotime('2005-10-15')]],
            ['before 2005-10-10', [strtotime('2005-10-09')], [strtotime('2005-10-15')]],
            ['> 2005-10-10', [strtotime('2005-10-15')], [strtotime('2005-10-09')]],
            ['after 2005-10-10', [strtotime('2005-10-15')], [strtotime('2005-10-09')]],
            ['since 2005-10-10', [strtotime('2005-10-15')], [strtotime('2005-10-09')]],
            ['!= 2005-10-10', [strtotime('2005-10-11')], [strtotime('2005-10-10')]],
        ];
    }
}
