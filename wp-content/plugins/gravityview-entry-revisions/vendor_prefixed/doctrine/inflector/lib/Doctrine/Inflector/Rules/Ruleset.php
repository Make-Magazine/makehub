<?php
/**
 * @license MIT
 *
 * Modified by GravityKit on 30-October-2022 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

declare(strict_types=1);

namespace GravityKit\GravityRevisions\Doctrine\Inflector\Rules;

class Ruleset
{
    /** @var Transformations */
    private $regular;

    /** @var Patterns */
    private $uninflected;

    /** @var Substitutions */
    private $irregular;

    public function __construct(Transformations $regular, Patterns $uninflected, Substitutions $irregular)
    {
        $this->regular     = $regular;
        $this->uninflected = $uninflected;
        $this->irregular   = $irregular;
    }

    public function getRegular(): Transformations
    {
        return $this->regular;
    }

    public function getUninflected(): Patterns
    {
        return $this->uninflected;
    }

    public function getIrregular(): Substitutions
    {
        return $this->irregular;
    }
}
