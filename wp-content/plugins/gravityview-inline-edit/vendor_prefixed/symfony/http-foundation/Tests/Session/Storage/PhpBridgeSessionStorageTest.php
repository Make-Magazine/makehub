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
use GravityKit\GravityEdit\Symfony\Component\HttpFoundation\Session\Storage\PhpBridgeSessionStorage;

/**
 * Test class for PhpSessionStorage.
 *
 * @author Drak <drak@zikula.org>
 *
 * These tests require separate processes.
 *
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class PhpBridgeSessionStorageTest extends TestCase
{
    private $savePath;

    protected function setUp()
    {
        $this->iniSet('session.save_handler', 'files');
        $this->iniSet('session.save_path', $this->savePath = sys_get_temp_dir().'/sf2test');
        if (!is_dir($this->savePath)) {
            mkdir($this->savePath);
        }
    }

    protected function tearDown()
    {
        session_write_close();
        array_map('unlink', glob($this->savePath.'/*'));
        if (is_dir($this->savePath)) {
            @rmdir($this->savePath);
        }

        $this->savePath = null;
    }

    /**
     * @return PhpBridgeSessionStorage
     */
    protected function getStorage()
    {
        $storage = new PhpBridgeSessionStorage();
        $storage->registerBag(new AttributeBag());

        return $storage;
    }

    public function testPhpSession()
    {
        $storage = $this->getStorage();

        $this->assertFalse($storage->getSaveHandler()->isActive());
        $this->assertFalse($storage->isStarted());

        session_start();
        $this->assertTrue(isset($_SESSION));
        // in PHP 5.4 we can reliably detect a session started
        $this->assertTrue($storage->getSaveHandler()->isActive());
        // PHP session might have started, but the storage driver has not, so false is correct here
        $this->assertFalse($storage->isStarted());

        $key = $storage->getMetadataBag()->getStorageKey();
        $this->assertArrayNotHasKey($key, $_SESSION);
        $storage->start();
        $this->assertArrayHasKey($key, $_SESSION);
    }

    public function testClear()
    {
        $storage = $this->getStorage();
        session_start();
        $_SESSION['drak'] = 'loves symfony';
        $storage->getBag('attributes')->set('symfony', 'greatness');
        $key = $storage->getBag('attributes')->getStorageKey();
        $this->assertEquals(['symfony' => 'greatness'], $_SESSION[$key]);
        $this->assertEquals('loves symfony', $_SESSION['drak']);
        $storage->clear();
        $this->assertEquals([], $_SESSION[$key]);
        $this->assertEquals('loves symfony', $_SESSION['drak']);
    }
}
