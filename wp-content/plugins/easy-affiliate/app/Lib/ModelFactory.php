<?php

namespace EasyAffiliate\Lib;

/** Get a specific model from a name string. */
class ModelFactory {
  public static function fetch($model, $id) {
    $class = 'EasyAffiliate\\Models\\' . Inflector::classify($model);

    if(!class_exists($class)) {
      return new \WP_Error(sprintf(__('A model for %s wasn\'t found', 'easy-affiliate'), $model));
    }

    // We'll let the autoloader handle including files containing these classes
    $r = new \ReflectionClass($class);
    $obj = $r->newInstanceArgs([$id]);

    if(isset($obj->ID) && $obj->ID <= 0) {
      return new \WP_Error(sprintf(__('There was no %s with an id of %d found', 'easy-affiliate'), $model, $obj->ID));
    }
    else if(isset($obj->id) && $obj->id <= 0) {
      return new \WP_Error(sprintf(__('There was no %s with an id of %d found', 'easy-affiliate'), $model, $obj->id));
    }
    else if(isset($obj->term_id) && $obj->term_id <= 0) {
      return new \WP_Error(sprintf(__('There was no %s with an id of %d found', 'easy-affiliate'), $model, $obj->term_id));
    }

    $objs[$class] = $obj;

    return $obj;
  }
}
