<?php

namespace EasyAffiliate\Lib;

/** Class used for getting config data **/
class Config {
  // Attempts to retrieve data from a config file
  public static function get($name) {
    $filename = ESAF_CONFIG_PATH . "/{$name}.php";

    if(!file_exists($filename)) {
      return new \WP_Error(sprintf(__("A config file for %s wasn\'t found", 'easy-affiliate'), $name));
    }

    return require($filename);
  }
}

