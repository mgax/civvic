<?php

Util::init();

class Util {
  static $rootPath;
  
  static function init() {
    ini_set('display_errors','On');
    spl_autoload_register('self::util_autoload');
    self::$rootPath = realpath(__DIR__ . '/..');
    Config::load(self::$rootPath . "/civvic.conf");
    SmartyWrap::init();
  }
  
  static function util_autoload($className) {
    $fileName = self::$rootPath . "/lib/{$className}.php";
    if (file_exists($fileName)) {
      require_once($fileName);
    }
  }
}

?>
