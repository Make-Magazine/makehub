<?php
/**
 * @license MIT
 *
 * Modified by GravityKit on 30-October-2022 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

declare(strict_types=1);

namespace GravityKit\GravityRevisions\Doctrine\Inflector;

use GravityKit\GravityRevisions\Doctrine\Inflector\Rules\Ruleset;

interface LanguageInflectorFactory
{
    /**
     * Applies custom rules for singularisation
     *
     * @param bool $reset If true, will unset default inflections for all new rules
     *
     * @return $this
     */
    public function withSingularRules(?Ruleset $singularRules, bool $reset = false): self;

    /**
     * Applies custom rules for pluralisation
     *
     * @param bool $reset If true, will unset default inflections for all new rules
     *
     * @return $this
     */
    public function withPluralRules(?Ruleset $pluralRules, bool $reset = false): self;

    /**
     * Builds the inflector instance with all applicable rules
     */
    public function build(): Inflector;
}
