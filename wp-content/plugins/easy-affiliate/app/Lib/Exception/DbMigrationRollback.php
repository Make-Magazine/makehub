<?php

namespace EasyAffiliate\Lib\Exception;

class DbMigrationRollback extends DbMigration {
  public function __construct($message, $code = 0, \Exception $previous = null) {
    global $wpdb;
    $wpdb->query('ROLLBACK'); // Attempt a rollback
    parent::__construct($message, $code, $previous);
  }
}
