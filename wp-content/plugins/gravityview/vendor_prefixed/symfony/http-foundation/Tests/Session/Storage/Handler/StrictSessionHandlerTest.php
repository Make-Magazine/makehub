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

namespace GravityKit\GravityView\Symfony\Component\HttpFoundation\Tests\Session\Storage\Handler;

use PHPUnit\Framework\TestCase;
use GravityKit\GravityView\Symfony\Component\HttpFoundation\Session\Storage\Handler\AbstractSessionHandler;
use GravityKit\GravityView\Symfony\Component\HttpFoundation\Session\Storage\Handler\StrictSessionHandler;

class StrictSessionHandlerTest extends TestCase
{
    public function testOpen()
    {
        $handler = $this->getMockBuilder('SessionHandlerInterface')->getMock();
        $handler->expects($this->once())->method('open')
            ->with('path', 'name')->willReturn(true);
        $proxy = new StrictSessionHandler($handler);

        $this->assertInstanceOf('SessionUpdateTimestampHandlerInterface', $proxy);
        $this->assertInstanceOf(AbstractSessionHandler::class, $proxy);
        $this->assertTrue($proxy->open('path', 'name'));
    }

    public function testCloseSession()
    {
        $handler = $this->getMockBuilder('SessionHandlerInterface')->getMock();
        $handler->expects($this->once())->method('close')
            ->willReturn(true);
        $proxy = new StrictSessionHandler($handler);

        $this->assertTrue($proxy->close());
    }

    public function testValidateIdOK()
    {
        $handler = $this->getMockBuilder('SessionHandlerInterface')->getMock();
        $handler->expects($this->once())->method('read')
            ->with('id')->willReturn('data');
        $proxy = new StrictSessionHandler($handler);

        $this->assertTrue($proxy->validateId('id'));
    }

    public function testValidateIdKO()
    {
        $handler = $this->getMockBuilder('SessionHandlerInterface')->getMock();
        $handler->expects($this->once())->method('read')
            ->with('id')->willReturn('');
        $proxy = new StrictSessionHandler($handler);

        $this->assertFalse($proxy->validateId('id'));
    }

    public function testRead()
    {
        $handler = $this->getMockBuilder('SessionHandlerInterface')->getMock();
        $handler->expects($this->once())->method('read')
            ->with('id')->willReturn('data');
        $proxy = new StrictSessionHandler($handler);

        $this->assertSame('data', $proxy->read('id'));
    }

    public function testReadWithValidateIdOK()
    {
        $handler = $this->getMockBuilder('SessionHandlerInterface')->getMock();
        $handler->expects($this->once())->method('read')
            ->with('id')->willReturn('data');
        $proxy = new StrictSessionHandler($handler);

        $this->assertTrue($proxy->validateId('id'));
        $this->assertSame('data', $proxy->read('id'));
    }

    public function testReadWithValidateIdMismatch()
    {
        $handler = $this->getMockBuilder('SessionHandlerInterface')->getMock();
        $handler->expects($this->exactly(2))->method('read')
            ->withConsecutive(['id1'], ['id2'])
            ->will($this->onConsecutiveCalls('data1', 'data2'));
        $proxy = new StrictSessionHandler($handler);

        $this->assertTrue($proxy->validateId('id1'));
        $this->assertSame('data2', $proxy->read('id2'));
    }

    public function testUpdateTimestamp()
    {
        $handler = $this->getMockBuilder('SessionHandlerInterface')->getMock();
        $handler->expects($this->once())->method('write')
            ->with('id', 'data')->willReturn(true);
        $proxy = new StrictSessionHandler($handler);

        $this->assertTrue($proxy->updateTimestamp('id', 'data'));
    }

    public function testWrite()
    {
        $handler = $this->getMockBuilder('SessionHandlerInterface')->getMock();
        $handler->expects($this->once())->method('write')
            ->with('id', 'data')->willReturn(true);
        $proxy = new StrictSessionHandler($handler);

        $this->assertTrue($proxy->write('id', 'data'));
    }

    public function testWriteEmptyNewSession()
    {
        $handler = $this->getMockBuilder('SessionHandlerInterface')->getMock();
        $handler->expects($this->once())->method('read')
            ->with('id')->willReturn('');
        $handler->expects($this->never())->method('write');
        $handler->expects($this->once())->method('destroy')->willReturn(true);
        $proxy = new StrictSessionHandler($handler);

        $this->assertFalse($proxy->validateId('id'));
        $this->assertSame('', $proxy->read('id'));
        $this->assertTrue($proxy->write('id', ''));
    }

    public function testWriteEmptyExistingSession()
    {
        $handler = $this->getMockBuilder('SessionHandlerInterface')->getMock();
        $handler->expects($this->once())->method('read')
            ->with('id')->willReturn('data');
        $handler->expects($this->never())->method('write');
        $handler->expects($this->once())->method('destroy')->willReturn(true);
        $proxy = new StrictSessionHandler($handler);

        $this->assertSame('data', $proxy->read('id'));
        $this->assertTrue($proxy->write('id', ''));
    }

    public function testDestroy()
    {
        $handler = $this->getMockBuilder('SessionHandlerInterface')->getMock();
        $handler->expects($this->once())->method('destroy')
            ->with('id')->willReturn(true);
        $proxy = new StrictSessionHandler($handler);

        $this->assertTrue($proxy->destroy('id'));
    }

    public function testDestroyNewSession()
    {
        $handler = $this->getMockBuilder('SessionHandlerInterface')->getMock();
        $handler->expects($this->once())->method('read')
            ->with('id')->willReturn('');
        $handler->expects($this->once())->method('destroy')->willReturn(true);
        $proxy = new StrictSessionHandler($handler);

        $this->assertSame('', $proxy->read('id'));
        $this->assertTrue($proxy->destroy('id'));
    }

    public function testDestroyNonEmptyNewSession()
    {
        $handler = $this->getMockBuilder('SessionHandlerInterface')->getMock();
        $handler->expects($this->once())->method('read')
            ->with('id')->willReturn('');
        $handler->expects($this->once())->method('write')
            ->with('id', 'data')->willReturn(true);
        $handler->expects($this->once())->method('destroy')
            ->with('id')->willReturn(true);
        $proxy = new StrictSessionHandler($handler);

        $this->assertSame('', $proxy->read('id'));
        $this->assertTrue($proxy->write('id', 'data'));
        $this->assertTrue($proxy->destroy('id'));
    }

    public function testGc()
    {
        $handler = $this->getMockBuilder('SessionHandlerInterface')->getMock();
        $handler->expects($this->once())->method('gc')
            ->with(123)->willReturn(true);
        $proxy = new StrictSessionHandler($handler);

        $this->assertTrue($proxy->gc(123));
    }
}
