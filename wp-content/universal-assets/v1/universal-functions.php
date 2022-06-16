<?php

if (!defined('ABSPATH'))
    define('ABSPATH', dirname(__FILE__) . '/');
require_once(ABSPATH . 'wp-load.php');

// Add that navwalker for the custom menus
require_once('lib/wp_bootstrap_navwalker.php');

// Include all function files in the make-experiences/functions directory:
$function_files = glob(dirname(__FILE__) .'/functions/*.php');

$count=0;
foreach ($function_files as $file) {
  include_once $file;
}
