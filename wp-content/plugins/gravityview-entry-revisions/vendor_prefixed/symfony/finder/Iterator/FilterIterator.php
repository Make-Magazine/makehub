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

namespace GravityKit\GravityRevisions\Symfony\Component\Finder\Iterator;

/**
 * This iterator just overrides the rewind method in order to correct a PHP bug,
 * which existed before version 5.5.23/5.6.7.
 *
 * @see https://bugs.php.net/68557
 *
 * @author Alex Bogomazov
 *
 * @deprecated since 3.4, to be removed in 4.0.
 */
abstract class FilterIterator extends \FilterIterator
{
    /**
     * This is a workaround for the problem with \FilterIterator leaving inner \FilesystemIterator in wrong state after
     * rewind in some cases.
     *
     * @see FilterIterator::rewind()
     */
    public function rewind()
    {
        if (\PHP_VERSION_ID > 50607 || (\PHP_VERSION_ID > 50523 && \PHP_VERSION_ID < 50600)) {
            parent::rewind();

            return;
        }

        $iterator = $this;
        while ($iterator instanceof \OuterIterator) {
            $innerIterator = $iterator->getInnerIterator();

            if ($innerIterator instanceof RecursiveDirectoryIterator) {
                // this condition is necessary for iterators to work properly with non-local filesystems like ftp
                if ($innerIterator->isRewindable()) {
                    $innerIterator->next();
                    $innerIterator->rewind();
                }
            } elseif ($innerIterator instanceof \FilesystemIterator) {
                $innerIterator->next();
                $innerIterator->rewind();
            }

            $iterator = $innerIterator;
        }

        parent::rewind();
    }
}
