<?php

class Db {
  static function init($dsn) {
    $parts = self::parseDsn($dsn);
    ORM::configure(sprintf("mysql:host=%s;dbname=%s", $parts['host'], $parts['database']));
    ORM::configure('username', $parts['user']);
    ORM::configure('password', $parts['password']);
    // If you enable query logging, you can then run var_dump(ORM::get_query_log());
    // ORM::configure('logging', true);
    ORM::configure('driver_options', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
  }

  /**
   * Returns an array mapping user, password, host and database to their respective values.
   **/
  private static function parseDsn($dsn = null) {
    $matches = array();
    $numMatches = preg_match('/^([^:]+):\/\/([^:@]+)(:([^@]+))?@([^\/]+)\/(.+)$/', $dsn, $matches);
    return array('driver' => $matches[1],
                 'user' => $matches[2],
                 'password' => $matches[4],
                 'host' => $matches[5],
                 'database' => $matches[6]);
  }

}

?>
