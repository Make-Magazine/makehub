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

namespace GravityKit\GravityRevisions\Symfony\Component\HttpFoundation\Session\Storage;

/**
 * Allows session to be started by PHP and managed by Symfony.
 *
 * @author Drak <drak@zikula.org>
 */
class PhpBridgeSessionStorage extends NativeSessionStorage
{
    /**
     * @param \SessionHandlerInterface|null $handler
     * @param MetadataBag                   $metaBag MetadataBag
     */
    public function __construct($handler = null, MetadataBag $metaBag = null)
    {
        if (!\extension_loaded('session')) {
            throw new \LogicException('PHP extension "session" is required.');
        }

        $this->setMetadataBag($metaBag);
        $this->setSaveHandler($handler);
    }

    /**
     * {@inheritdoc}
     */
    public function start()
    {
        if ($this->started) {
            return true;
        }

        $this->loadSession();

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        // clear out the bags and nothing else that may be set
        // since the purpose of this driver is to share a handler
        foreach ($this->bags as $bag) {
            $bag->clear();
        }

        // reconnect the bags to the session
        $this->loadSession();
    }
}
