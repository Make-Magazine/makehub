<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Modified by __root__ on 02-November-2023 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityEdit\Symfony\Component\HttpFoundation\Tests\Session\Storage;

use PHPUnit\Framework\TestCase;
use GravityKit\GravityEdit\Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;
use GravityKit\GravityEdit\Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use GravityKit\GravityEdit\Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;

/**
 * Test class for MockFileSessionStorage.
 *
 * @author Drak <drak@zikula.org>
 */
class MockFileSessionStorageTest extends TestCase
{
    /**
     * @var string
     */
    private $sessionDir;

    /**
     * @var MockFileSessionStorage
     */
    protected $storage;

    protected function setUp()
    {
        $this->sessionDir = sys_get_temp_dir().'/sf2test';
        $this->storage = $this->getStorage();
    }

    protected function tearDown()
    {
        array_map('unlink', glob($this->sessionDir.'/*'));
        if (is_dir($this->sessionDir)) {
            @rmdir($this->sessionDir);
        }
        $this->sessionDir = null;
        $this->storage = null;
    }

    public function testStart()
    {
        $this->assertEquals('', $this->storage->getId());
        $this->assertTrue($this->storage->start());
        $id = $this->storage->getId();
        $this->assertNotEquals('', $this->storage->getId());
        $this->assertTrue($this->storage->start());
        $this->assertEquals($id, $this->storage->getId());
    }

    public function testRegenerate()
    {
        $this->storage->start();
        $this->storage->getBag('attributes')->set('regenerate', 1234);
        $this->storage->regenerate();
        $this->assertEquals(1234, $this->storage->getBag('attributes')->get('regenerate'));
        $this->storage->regenerate(true);
        $this->assertEquals(1234, $this->storage->getBag('attributes')->get('regenerate'));
    }

    public function testGetId()
    {
        $this->assertEquals('', $this->storage->getId());
        $this->storage->start();
        $this->assertNotEquals('', $this->storage->getId());
    }

    public function testSave()
    {
        $this->storage->start();
        $id = $this->storage->getId();
        $this->assertNotEquals('108', $this->storage->getBag('attributes')->get('new'));
        $this->assertFalse($this->storage->getBag('flashes')->has('newkey'));
        $this->storage->getBag('attributes')->set('new', '108');
        $this->storage->getBag('flashes')->set('newkey', 'test');
        $this->storage->save();

        $storage = $this->getStorage();
        $storage->setId($id);
        $storage->start();
        $this->assertEquals('108', $storage->getBag('attributes')->get('new'));
        $this->assertTrue($storage->getBag('flashes')->has('newkey'));
        $this->assertEquals(['test'], $storage->getBag('flashes')->peek('newkey'));
    }

    public function testMultipleInstances()
    {
        $storage1 = $this->getStorage();
        $storage1->start();
        $storage1->getBag('attributes')->set('foo', 'bar');
        $storage1->save();

        $storage2 = $this->getStorage();
        $storage2->setId($storage1->getId());
        $storage2->start();
        $this->assertEquals('bar', $storage2->getBag('attributes')->get('foo'), 'values persist between instances');
    }

    public function testSaveWithoutStart()
    {
        $this->expectException('RuntimeException');
        $storage1 = $this->getStorage();
        $storage1->save();
    }

    private function getStorage()
    {
        $storage = new MockFileSessionStorage($this->sessionDir);
        $storage->registerBag(new FlashBag());
        $storage->registerBag(new AttributeBag());

        return $storage;
    }
}
