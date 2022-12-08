<?php
/**
 * @license MIT
 *
 * Modified by GravityKit on 01-December-2022 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityRevisions\Foundation\ThirdParty\Illuminate\Contracts\Filesystem;

interface Factory
{
    /**
     * Get a filesystem implementation.
     *
     * @param  string  $name
     * @return \GravityKit\GravityRevisions\Foundation\ThirdParty\Illuminate\Contracts\Filesystem\Filesystem
     */
    public function disk($name = null);
}
