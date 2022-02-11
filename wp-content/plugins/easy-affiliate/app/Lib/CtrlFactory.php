<?php

namespace EasyAffiliate\Lib;

/** Ctrls in Easy Affiliate are all singletons, so we can
  * use this factory to churn out objects for us.
  */
class CtrlFactory {
  public static function fetch($class, $args = []) {
    static $objs;

    if(!preg_match('#^EasyAffiliate\\\\Controllers#', $class)) {
      $class = 'EasyAffiliate\\Controllers\\' . Inflector::classify($class);
    }

    if(isset($objs[$class]) && ($objs[$class] instanceof BaseCtrl)) {
      return $objs[$class];
    }

    if(!class_exists($class)) {
      throw new \Exception(__('Ctrl wasn\'t found', 'easy-affiliate'));
    }

    // We'll let the autoloader handle including files containing these classes
    $r = new \ReflectionClass($class);
    $obj = $r->newInstanceArgs($args);

    $objs[$class] = $obj;

    return $obj;
  }

  public static function all($args = []) {
    $objs = [];

    $ctrls = @glob(ESAF_CTRLS_PATH . '/*Ctrl.php', GLOB_NOSORT);
    foreach($ctrls as $ctrl) {
      $class = preg_replace('#\.php#', '', basename($ctrl));
      $objs[$class] = self::fetch($class, $args);
    }

    return $objs;
  }
}
