<?php
/**
 * @license MIT
 *
 * Modified by GravityKit on 12-December-2023 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

spl_autoload_register(function ($class) {
    if (strpos($class, 'GravityKit\\GravityRevisions\\Foundation\\ThirdParty\\Gettext\\') !== 0) {
        return;
    }

    $file = __DIR__.str_replace('\\', DIRECTORY_SEPARATOR, substr($class, strlen('GravityKit\GravityRevisions\Foundation\ThirdParty\Gettext'))).'.php';

    if (is_file($file)) {
        require_once $file;
    }
});
