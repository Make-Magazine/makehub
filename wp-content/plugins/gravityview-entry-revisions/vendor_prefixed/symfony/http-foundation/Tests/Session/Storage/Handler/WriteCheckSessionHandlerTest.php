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

namespace GravityKit\GravityRevisions\Symfony\Component\HttpFoundation\Tests\Session\Storage\Handler;

use PHPUnit\Framework\TestCase;
use GravityKit\GravityRevisions\Symfony\Component\HttpFoundation\Session\Storage\Handler\WriteCheckSessionHandler;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 *
 * @group legacy
 */
class WriteCheckSessionHandlerTest extends TestCase
{
    public function test()
    {
        $wrappedSessionHandlerMock = $this->getMockBuilder('SessionHandlerInterface')->getMock();
        $writeCheckSessionHandler = new WriteCheckSessionHandler($wrappedSessionHandlerMock);

        $wrappedSessionHandlerMock
            ->expects($this->once())
            ->method('close')
            ->with()
            ->willReturn(true)
        ;

        $this->assertTrue($writeCheckSessionHandler->close());
    }

    public function testWrite()
    {
        $wrappedSessionHandlerMock = $this->getMockBuilder('SessionHandlerInterface')->getMock();
        $writeCheckSessionHandler = new WriteCheckSessionHandler($wrappedSessionHandlerMock);

        $wrappedSessionHandlerMock
            ->expects($this->once())
            ->method('write')
            ->with('foo', 'bar')
            ->willReturn(true)
        ;

        $this->assertTrue($writeCheckSessionHandler->write('foo', 'bar'));
    }

    public function testSkippedWrite()
    {
        $wrappedSessionHandlerMock = $this->getMockBuilder('SessionHandlerInterface')->getMock();
        $writeCheckSessionHandler = new WriteCheckSessionHandler($wrappedSessionHandlerMock);

        $wrappedSessionHandlerMock
            ->expects($this->once())
            ->method('read')
            ->with('foo')
            ->willReturn('bar')
        ;

        $wrappedSessionHandlerMock
            ->expects($this->never())
            ->method('write')
        ;

        $this->assertEquals('bar', $writeCheckSessionHandler->read('foo'));
        $this->assertTrue($writeCheckSessionHandler->write('foo', 'bar'));
    }

    public function testNonSkippedWrite()
    {
        $wrappedSessionHandlerMock = $this->getMockBuilder('SessionHandlerInterface')->getMock();
        $writeCheckSessionHandler = new WriteCheckSessionHandler($wrappedSessionHandlerMock);

        $wrappedSessionHandlerMock
            ->expects($this->once())
            ->method('read')
            ->with('foo')
            ->willReturn('bar')
        ;

        $wrappedSessionHandlerMock
            ->expects($this->once())
            ->method('write')
            ->with('foo', 'baZZZ')
            ->willReturn(true)
        ;

        $this->assertEquals('bar', $writeCheckSessionHandler->read('foo'));
        $this->assertTrue($writeCheckSessionHandler->write('foo', 'baZZZ'));
    }
}
