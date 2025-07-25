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

use GravityKit\GravityView\Symfony\Component\Finder\Comparator\DateComparator;

/**
 * DateRangeFilterIterator filters out files that are not in the given date range (last modified dates).
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class DateRangeFilterIterator extends FilterIterator
{
    private $comparators = [];

    /**
     * @param \Iterator        $iterator    The Iterator to filter
     * @param DateComparator[] $comparators An array of DateComparator instances
     */
    public function __construct(\Iterator $iterator, array $comparators)
    {
        $this->comparators = $comparators;

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

        if (!file_exists($fileinfo->getPathname())) {
            return false;
        }

        $filedate = $fileinfo->getMTime();
        foreach ($this->comparators as $compare) {
            if (!$compare->test($filedate)) {
                return false;
            }
        }

        return true;
    }
}
