<?php
/**
 * @license MIT
 *
 * Modified by The GravityKit Team on 25-January-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

spl_autoload_register(function ($class) {
    if (strpos($class, 'GravityKit\\GravityImport\\Foundation\\ThirdParty\\Gettext\\') !== 0) {
        return;
    }

    $file = __DIR__.str_replace('\\', DIRECTORY_SEPARATOR, substr($class, strlen('GravityKit\GravityImport\Foundation\ThirdParty\Gettext'))).'.php';

    if (is_file($file)) {
        require_once $file;
    }
});
