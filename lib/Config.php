<?php

class Config {
  private static $config = array();

  static function load($fileName) {
    self::$config = parse_ini_file($fileName, true);
  }

  static function get($key) {
    list($section, $name) = explode('.', $key, 2);
    if (array_key_exists($section, self::$config) && array_key_exists($name, self::$config[$section])) {
      return self::$config[$section][$name];
    } else {
      return null;
    }
  }
}

?>
