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
use GravityKit\GravityView\Symfony\Component\HttpFoundation\Session\Storage\Handler\MemcachedSessionHandler;

/**
 * @requires extension memcached
 * @group time-sensitive
 */
class MemcachedSessionHandlerTest extends TestCase
{
    const PREFIX = 'prefix_';
    const TTL = 1000;

    /**
     * @var MemcachedSessionHandler
     */
    protected $storage;

    protected $memcached;

    protected function setUp()
    {
        if (\defined('HHVM_VERSION')) {
            $this->markTestSkipped('PHPUnit_MockObject cannot mock the Memcached class on HHVM. See https://github.com/sebastianbergmann/phpunit-mock-objects/pull/289');
        }

        parent::setUp();

        if (version_compare(phpversion('memcached'), '2.2.0', '>=') && version_compare(phpversion('memcached'), '3.0.0b1', '<')) {
            $this->markTestSkipped('Tests can only be run with memcached extension 2.1.0 or lower, or 3.0.0b1 or higher');
        }

        $this->memcached = $this->getMockBuilder('Memcached')->getMock();
        $this->storage = new MemcachedSessionHandler(
            $this->memcached,
            ['prefix' => self::PREFIX, 'expiretime' => self::TTL]
        );
    }

    protected function tearDown()
    {
        $this->memcached = null;
        $this->storage = null;
        parent::tearDown();
    }

    public function testOpenSession()
    {
        $this->assertTrue($this->storage->open('', ''));
    }

    public function testCloseSession()
    {
        $this->memcached
            ->expects($this->once())
            ->method('quit')
            ->willReturn(true)
        ;

        $this->assertTrue($this->storage->close());
    }

    public function testReadSession()
    {
        $this->memcached
            ->expects($this->once())
            ->method('get')
            ->with(self::PREFIX.'id')
        ;

        $this->assertEquals('', $this->storage->read('id'));
    }

    public function testWriteSession()
    {
        $this->memcached
            ->expects($this->once())
            ->method('set')
            ->with(self::PREFIX.'id', 'data', $this->equalTo(time() + self::TTL, 2))
            ->willReturn(true)
        ;

        $this->assertTrue($this->storage->write('id', 'data'));
    }

    public function testDestroySession()
    {
        $this->memcached
            ->expects($this->once())
            ->method('delete')
            ->with(self::PREFIX.'id')
            ->willReturn(true)
        ;

        $this->assertTrue($this->storage->destroy('id'));
    }

    public function testGcSession()
    {
        $this->assertTrue($this->storage->gc(123));
    }

    /**
     * @dataProvider getOptionFixtures
     */
    public function testSupportedOptions($options, $supported)
    {
        try {
            new MemcachedSessionHandler($this->memcached, $options);
            $this->assertTrue($supported);
        } catch (\InvalidArgumentException $e) {
            $this->assertFalse($supported);
        }
    }

    public function getOptionFixtures()
    {
        return [
            [['prefix' => 'session'], true],
            [['expiretime' => 100], true],
            [['prefix' => 'session', 'expiretime' => 200], true],
            [['expiretime' => 100, 'foo' => 'bar'], false],
        ];
    }

    public function testGetConnection()
    {
        $method = new \ReflectionMethod($this->storage, 'getMemcached');
        $method->setAccessible(true);

        $this->assertInstanceOf('\Memcached', $method->invoke($this->storage));
    }
}
