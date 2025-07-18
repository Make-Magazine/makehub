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

namespace GravityKit\GravityRevisions\Symfony\Component\HttpFoundation\Tests;

use PHPUnit\Framework\TestCase;
use GravityKit\GravityRevisions\Symfony\Component\HttpFoundation\Request;
use GravityKit\GravityRevisions\Symfony\Component\HttpFoundation\RequestStack;

class RequestStackTest extends TestCase
{
    public function testGetCurrentRequest()
    {
        $requestStack = new RequestStack();
        $this->assertNull($requestStack->getCurrentRequest());

        $request = Request::create('/foo');

        $requestStack->push($request);
        $this->assertSame($request, $requestStack->getCurrentRequest());

        $this->assertSame($request, $requestStack->pop());
        $this->assertNull($requestStack->getCurrentRequest());

        $this->assertNull($requestStack->pop());
    }

    public function testGetMasterRequest()
    {
        $requestStack = new RequestStack();
        $this->assertNull($requestStack->getMasterRequest());

        $masterRequest = Request::create('/foo');
        $subRequest = Request::create('/bar');

        $requestStack->push($masterRequest);
        $requestStack->push($subRequest);

        $this->assertSame($masterRequest, $requestStack->getMasterRequest());
    }

    public function testGetParentRequest()
    {
        $requestStack = new RequestStack();
        $this->assertNull($requestStack->getParentRequest());

        $masterRequest = Request::create('/foo');

        $requestStack->push($masterRequest);
        $this->assertNull($requestStack->getParentRequest());

        $firstSubRequest = Request::create('/bar');

        $requestStack->push($firstSubRequest);
        $this->assertSame($masterRequest, $requestStack->getParentRequest());

        $secondSubRequest = Request::create('/baz');

        $requestStack->push($secondSubRequest);
        $this->assertSame($firstSubRequest, $requestStack->getParentRequest());
    }
}
