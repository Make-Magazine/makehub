<?php
/**
 * @license MIT
 *
 * Modified by GravityKit on 30-October-2022 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

declare(strict_types=1);

namespace GravityKit\GravityRevisions\Doctrine\Inflector\Rules\Portuguese;

use GravityKit\GravityRevisions\Doctrine\Inflector\Rules\Pattern;

final class Uninflected
{
    /**
     * @return Pattern[]
     */
    public static function getSingular(): iterable
    {
        yield from self::getDefault();
    }

    /**
     * @return Pattern[]
     */
    public static function getPlural(): iterable
    {
        yield from self::getDefault();
    }

    /**
     * @return Pattern[]
     */
    private static function getDefault(): iterable
    {
        yield new Pattern('tórax');
        yield new Pattern('tênis');
        yield new Pattern('ônibus');
        yield new Pattern('lápis');
        yield new Pattern('fênix');
    }
}
