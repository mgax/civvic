<?php

require_once Config::get('general.smartyClass');

class SmartyWrap {
  private static $theSmarty = null;

  static function init() {
    self::$theSmarty = new Smarty();
    self::$theSmarty->template_dir = Util::$rootPath . '/templates';
    self::$theSmarty->compile_dir = Util::$rootPath . '/templates_c';
  }

  static function display($templateName) {
    self::$theSmarty->assign('templateName', "templates/$templateName");
    self::$theSmarty->display('layout.tpl');
  }
}

?>
