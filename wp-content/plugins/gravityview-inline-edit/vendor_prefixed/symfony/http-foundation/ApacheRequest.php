<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Modified by __root__ on 02-November-2023 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityEdit\Symfony\Component\HttpFoundation;

/**
 * Request represents an HTTP request from an Apache server.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class ApacheRequest extends Request
{
    /**
     * {@inheritdoc}
     */
    protected function prepareRequestUri()
    {
        return $this->server->get('REQUEST_URI');
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareBaseUrl()
    {
        $baseUrl = $this->server->get('SCRIPT_NAME');

        if (false === strpos($this->server->get('REQUEST_URI'), $baseUrl)) {
            // assume mod_rewrite
            return rtrim(\dirname($baseUrl), '/\\');
        }

        return $baseUrl;
    }
}
