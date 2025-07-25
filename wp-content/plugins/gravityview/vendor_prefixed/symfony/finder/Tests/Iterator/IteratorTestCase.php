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

use PHPUnit\Framework\TestCase;

abstract class IteratorTestCase extends TestCase
{
    protected function assertIterator($expected, \Traversable $iterator)
    {
        // set iterator_to_array $use_key to false to avoid values merge
        // this made FinderTest::testAppendWithAnArray() fail with GnuFinderAdapter
        $values = array_map(function (\SplFileInfo $fileinfo) { return str_replace('/', \DIRECTORY_SEPARATOR, $fileinfo->getPathname()); }, iterator_to_array($iterator, false));

        $expected = array_map(function ($path) { return str_replace('/', \DIRECTORY_SEPARATOR, $path); }, $expected);

        sort($values);
        sort($expected);

        $this->assertEquals($expected, array_values($values));
    }

    protected function assertOrderedIterator($expected, \Traversable $iterator)
    {
        $values = array_map(function (\SplFileInfo $fileinfo) { return $fileinfo->getPathname(); }, iterator_to_array($iterator));

        $this->assertEquals($expected, array_values($values));
    }

    /**
     *  Same as assertOrderedIterator, but checks the order of groups of
     *  array elements.
     *
     *  @param array $expected - an array of arrays. For any two subarrays
     *      $a and $b such that $a goes before $b in $expected, the method
     *      asserts that any element of $a goes before any element of $b
     *      in the sequence generated by $iterator
     */
    protected function assertOrderedIteratorForGroups(array $expected, \Traversable $iterator)
    {
        $values = array_values(array_map(function (\SplFileInfo $fileinfo) { return $fileinfo->getPathname(); }, iterator_to_array($iterator)));

        foreach ($expected as $subarray) {
            $temp = [];
            while (\count($values) && \count($temp) < \count($subarray)) {
                $temp[] = array_shift($values);
            }
            sort($temp);
            sort($subarray);
            $this->assertEquals($subarray, $temp);
        }
    }

    /**
     * Same as IteratorTestCase::assertIterator with foreach usage.
     */
    protected function assertIteratorInForeach(array $expected, \Traversable $iterator)
    {
        $values = [];
        foreach ($iterator as $file) {
            $this->assertInstanceOf('GravityKit\\GravityView\\Symfony\\Component\\Finder\\SplFileInfo', $file);
            $values[] = $file->getPathname();
        }

        sort($values);
        sort($expected);

        $this->assertEquals($expected, array_values($values));
    }

    /**
     * Same as IteratorTestCase::assertOrderedIterator with foreach usage.
     */
    protected function assertOrderedIteratorInForeach(array $expected, \Traversable $iterator)
    {
        $values = [];
        foreach ($iterator as $file) {
            $this->assertInstanceOf('GravityKit\\GravityView\\Symfony\\Component\\Finder\\SplFileInfo', $file);
            $values[] = $file->getPathname();
        }

        $this->assertEquals($expected, array_values($values));
    }
}
