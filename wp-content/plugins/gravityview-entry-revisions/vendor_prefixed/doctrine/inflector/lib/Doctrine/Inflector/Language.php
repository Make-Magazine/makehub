<?php
/**
 * @license MIT
 *
 * Modified by GravityKit on 30-October-2022 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

declare(strict_types=1);

namespace GravityKit\GravityRevisions\Doctrine\Inflector;

final class Language
{
    public const ENGLISH          = 'english';
    public const FRENCH           = 'french';
    public const NORWEGIAN_BOKMAL = 'norwegian-bokmal';
    public const PORTUGUESE       = 'portuguese';
    public const SPANISH          = 'spanish';
    public const TURKISH          = 'turkish';

    private function __construct()
    {
    }
}
