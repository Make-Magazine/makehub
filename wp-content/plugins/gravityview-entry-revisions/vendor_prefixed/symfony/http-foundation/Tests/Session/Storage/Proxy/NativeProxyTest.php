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

namespace GravityKit\GravityRevisions\Symfony\Component\HttpFoundation\Tests\Session\Storage\Proxy;

use PHPUnit\Framework\TestCase;
use GravityKit\GravityRevisions\Symfony\Component\HttpFoundation\Session\Storage\Proxy\NativeProxy;

/**
 * Test class for NativeProxy.
 *
 * @group legacy
 *
 * @author Drak <drak@zikula.org>
 */
class NativeProxyTest extends TestCase
{
    public function testIsWrapper()
    {
        $proxy = new NativeProxy();
        $this->assertFalse($proxy->isWrapper());
    }

    public function testGetSaveHandlerName()
    {
        $name = ini_get('session.save_handler');
        $proxy = new NativeProxy();
        $this->assertEquals($name, $proxy->getSaveHandlerName());
    }
}
