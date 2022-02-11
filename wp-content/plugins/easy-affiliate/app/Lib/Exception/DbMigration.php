<?php

namespace EasyAffiliate\Lib\Exception;

use EasyAffiliate\Lib\Utils;

class DbMigration extends Log {
  public function __construct($message, $code = 0, \Exception $previous = null) {
    delete_transient('wafp_migrating');
    delete_transient('wafp_current_migration');
    set_transient('wafp_migration_error',$message,Utils::hours(4));
    parent::__construct($message, $code, $previous);
  }
}
