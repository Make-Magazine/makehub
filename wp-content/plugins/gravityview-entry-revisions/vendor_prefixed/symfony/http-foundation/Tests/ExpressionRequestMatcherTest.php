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
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use GravityKit\GravityRevisions\Symfony\Component\HttpFoundation\ExpressionRequestMatcher;
use GravityKit\GravityRevisions\Symfony\Component\HttpFoundation\Request;

class ExpressionRequestMatcherTest extends TestCase
{
    public function testWhenNoExpressionIsSet()
    {
        $this->expectException('LogicException');
        $expressionRequestMatcher = new ExpressionRequestMatcher();
        $expressionRequestMatcher->matches(new Request());
    }

    /**
     * @dataProvider provideExpressions
     */
    public function testMatchesWhenParentMatchesIsTrue($expression, $expected)
    {
        $request = Request::create('/foo');
        $expressionRequestMatcher = new ExpressionRequestMatcher();

        $expressionRequestMatcher->setExpression(new ExpressionLanguage(), $expression);
        $this->assertSame($expected, $expressionRequestMatcher->matches($request));
    }

    /**
     * @dataProvider provideExpressions
     */
    public function testMatchesWhenParentMatchesIsFalse($expression)
    {
        $request = Request::create('/foo');
        $request->attributes->set('foo', 'foo');
        $expressionRequestMatcher = new ExpressionRequestMatcher();
        $expressionRequestMatcher->matchAttribute('foo', 'bar');

        $expressionRequestMatcher->setExpression(new ExpressionLanguage(), $expression);
        $this->assertFalse($expressionRequestMatcher->matches($request));
    }

    public function provideExpressions()
    {
        return [
            ['request.getMethod() == method', true],
            ['request.getPathInfo() == path', true],
            ['request.getHost() == host', true],
            ['request.getClientIp() == ip', true],
            ['request.attributes.all() == attributes', true],
            ['request.getMethod() == method && request.getPathInfo() == path && request.getHost() == host && request.getClientIp() == ip &&  request.attributes.all() == attributes', true],
            ['request.getMethod() != method', false],
            ['request.getMethod() != method && request.getPathInfo() == path && request.getHost() == host && request.getClientIp() == ip &&  request.attributes.all() == attributes', false],
        ];
    }
}
