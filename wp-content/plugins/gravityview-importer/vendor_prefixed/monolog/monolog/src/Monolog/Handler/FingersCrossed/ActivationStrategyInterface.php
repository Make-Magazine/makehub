<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Modified by The GravityKit Team on 25-January-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityImport\Foundation\ThirdParty\Monolog\Handler\FingersCrossed;

/**
 * Interface for activation strategies for the FingersCrossedHandler.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
interface ActivationStrategyInterface
{
    /**
     * Returns whether the given record activates the handler.
     *
     * @param  array   $record
     * @return bool
     */
    public function isHandlerActivated(array $record);
}
