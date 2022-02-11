<?php

namespace EasyAffiliate\Lib\Exception;

use EasyAffiliate\Lib\Utils;

class Log extends Exception {
  public function __construct($message, $code = 0, \Exception $previous = null) {
    $classname = get_class($this);
    Utils::error_log("{$classname}: {$message}");
    parent::__construct($message, $code, $previous);
  }
}
