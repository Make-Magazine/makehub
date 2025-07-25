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

/**
 * @author Alex Bogomazov
 *
 * @group legacy
 */
class FilterIteratorTest extends RealIteratorTestCase
{
    public function testFilterFilesystemIterators()
    {
        $i = new \FilesystemIterator($this->toAbsolute());

        // it is expected that there are test.py test.php in the tmpDir
        $i = $this->getMockForAbstractClass('GravityKit\GravityView\Symfony\Component\Finder\Iterator\FilterIterator', [$i]);
        $i->expects($this->any())
            ->method('accept')
            ->willReturnCallback(function () use ($i) {
                return (bool) preg_match('/\.php/', (string) $i->current());
            }
        );

        $c = 0;
        foreach ($i as $item) {
            ++$c;
        }

        $this->assertEquals(1, $c);

        $i->rewind();

        $c = 0;
        foreach ($i as $item) {
            ++$c;
        }

        // This would fail in php older than 5.5.23/5.6.7 with \FilterIterator
        // but works with Symfony\Component\Finder\Iterator\FilterIterator
        // see https://bugs.php.net/68557
        $this->assertEquals(1, $c);
    }
}
