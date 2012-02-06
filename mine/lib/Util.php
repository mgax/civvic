<?php

Util::init();

class Util {
  static $rootPath;
  static $wwwRoot;
  
  static function init() {
    ini_set('display_errors','On');
    spl_autoload_register('self::util_autoload');
    self::definePaths();
    require_once self::$rootPath . '/lib/idiorm/idiorm.php';
    require_once self::$rootPath . '/lib/idiorm/paris.php';
    Config::load(self::$rootPath . "/civvic.conf");
    SmartyWrap::init(Config::get('general.smartyClass'));
    Db::init(Config::get('general.database'));
  }
  
  private function definePaths() {
    self::$rootPath = realpath(__DIR__ . '/..');
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $pos = strrpos($scriptName, '/www/');
    self::$wwwRoot = ($pos === false) ? '' : substr($scriptName, 0, $pos + 4);
  }

  static function util_autoload($className) {
    $fileName = self::$rootPath . "/lib/{$className}.php";
    if (file_exists($fileName)) {
      require_once($fileName);
    }
    $fileName = self::$rootPath . "/lib/model/{$className}.php";
    if (file_exists($fileName)) {
      require_once($fileName);
    }
  }
}

?>
