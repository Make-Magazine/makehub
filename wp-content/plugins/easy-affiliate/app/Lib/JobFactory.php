<?php

namespace EasyAffiliate\Lib;

use EasyAffiliate\Lib\Exception\InvalidJob as InvalidJobException;

class JobFactory {
  public static function fetch($class, $db = false) {
    if(!class_exists($class)) {
      throw new InvalidJobException(sprintf(__('Job class wasn\'t found for %s', 'easy-affiliate'), $class));
    }

    // We'll let the autoloader in easyaffiliate.php
    // handle including files containing these classes
    $r = new \ReflectionClass($class);
    $job = $r->newInstanceArgs([$db]);

    if( !( $job instanceof BaseJob ) ) {
      throw new InvalidJobException(sprintf(__('%s is not a valid job object.', 'easy-affiliate'), $class));
    }

    return $job;
  }

  public static function paths() {
    $paths = apply_filters('esaf_job_paths', [ESAF_JOBS_PATH]);
    Utils::debug_log(sprintf(__('Job Paths %s', 'easy-affiliate'), Utils::object_to_string($paths)));
    return $paths;
  }
}
