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

namespace GravityKit\GravityRevisions\Symfony\Component\HttpFoundation\Tests\File;

use GravityKit\GravityRevisions\Symfony\Component\HttpFoundation\File\File as OrigFile;

class FakeFile extends OrigFile
{
    private $realpath;

    public function __construct($realpath, $path)
    {
        $this->realpath = $realpath;
        parent::__construct($path, false);
    }

    public function isReadable()
    {
        return true;
    }

    public function getRealpath()
    {
        return $this->realpath;
    }

    public function getSize()
    {
        return 42;
    }

    public function getMTime()
    {
        return time();
    }
}
