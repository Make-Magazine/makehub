<?php
/**
 * @license MIT
 *
 * Modified by GravityKit on 30-October-2022 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

declare(strict_types=1);

namespace GravityKit\GravityRevisions\Doctrine\Inflector\Rules;

final class Substitution
{
    /** @var Word */
    private $from;

    /** @var Word */
    private $to;

    public function __construct(Word $from, Word $to)
    {
        $this->from = $from;
        $this->to   = $to;
    }

    public function getFrom(): Word
    {
        return $this->from;
    }

    public function getTo(): Word
    {
        return $this->to;
    }
}
