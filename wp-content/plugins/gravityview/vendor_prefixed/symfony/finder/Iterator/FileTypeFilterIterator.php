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

namespace GravityKit\GravityView\Symfony\Component\Finder\Iterator;

/**
 * FileTypeFilterIterator only keeps files, directories, or both.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class FileTypeFilterIterator extends FilterIterator
{
    const ONLY_FILES = 1;
    const ONLY_DIRECTORIES = 2;

    private $mode;

    /**
     * @param \Iterator $iterator The Iterator to filter
     * @param int       $mode     The mode (self::ONLY_FILES or self::ONLY_DIRECTORIES)
     */
    public function __construct(\Iterator $iterator, $mode)
    {
        $this->mode = $mode;

        parent::__construct($iterator);
    }

    /**
     * Filters the iterator values.
     *
     * @return bool true if the value should be kept, false otherwise
     */
    public function accept()
    {
        $fileinfo = $this->current();
        if (self::ONLY_DIRECTORIES === (self::ONLY_DIRECTORIES & $this->mode) && $fileinfo->isFile()) {
            return false;
        } elseif (self::ONLY_FILES === (self::ONLY_FILES & $this->mode) && $fileinfo->isDir()) {
            return false;
        }

        return true;
    }
}
