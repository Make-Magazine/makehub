<?php
namespace memberpress\gifting\lib;

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

use memberpress\gifting as base;
use memberpress\gifting\emails as emails;

/** Churns out our Emails on demand **/
class EmailFactory {
  public static function fetch( $class, $etype='BaseEmail', $args=array() ) {
    $class = str_replace('\\\\', '\\', $class);

    if(!\class_exists($class)) {
        throw new InvalidEmailException(__('Email wasn\'t found', 'memberpress-gifting'));
    }

    // We'll let the autoloader in memberpress.php
    // handle including files containing these classes
    $r = new \ReflectionClass($class);
    $obj = $r->newInstanceArgs($args);
    $etype = class_exists($etype) ? $etype : base\LIB_NAMESPACE.'\\'.$etype;

    if(!($obj instanceof $etype)) {
      throw new InvalidEmailException(sprintf(__('Not a valid email object: %1$s is not an instance of %2$s', 'memberpress-gifting'), $class, $etype));
    }

    return $obj;
  }

  public static function all($etype='BaseEmail', $args=array()) {
    static $objs;
    if( !isset($objs) ) { $objs = array(); }

    if( !isset($objs[$etype]) ) {
      $objs[$etype] = array();

      foreach( self::paths() as $path ) {
        $files = @glob( $path . '/*Email.php', GLOB_NOSORT );

        foreach( $files as $file ) {
          $class = base\EMAILS_NAMESPACE.'\\' . preg_replace( '#\.php#', '', basename($file) );

          try {
            $obj = self::fetch($class, $etype, $args);
            $objs[$etype][$class] = $obj;
          }
          catch (InvalidEmailException $e) {
            continue; // For now we do nothing if an exception is thrown
          }
        }
      }

      // order based on the ui_order
      uasort($objs[$etype], 'self::cmp_uasort');
    }

    return $objs[$etype];
  }

  // Purely used for sorting based on the ui_order
  public static function cmp_uasort($a, $b) {
    if($a->ui_order==$b->ui_order) { return 0; }
    return ($a->ui_order < $b->ui_order) ? -1 : 1;
  }

  public static function paths() {
    return \MeprHooks::apply_filters( 'mpgft-email-paths', array( base\EMAILS_PATH ) );
  }
}

