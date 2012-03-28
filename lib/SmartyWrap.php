<?php

class SmartyWrap {
  private static $theSmarty = null;
  private static $cssFiles = array();
  private static $jsFiles = array();

  static function init($smartyClass) {
    require_once $smartyClass;
    self::$theSmarty = new Smarty();
    self::$theSmarty->template_dir = Util::$rootPath . '/templates';
    self::$theSmarty->compile_dir = Util::$rootPath . '/templates_c';
    self::assign('wwwRoot', Util::$wwwRoot);
    self::assign('user', Session::getUser());
    self::assign('moArchiveUrl', Config::get('general.moArchiveUrl'));
    self::addCss('main');
    self::addCss('jqueryui-smoothness');
    self::addJs('jquery');
    self::addJs('jqueryui');
    self::addJs('datepicker');
    self::addJs('main');
  }

  static function assign($name, $value) {
    self::$theSmarty->assign($name, $value);
  }

  static function display($templateName) {
    self::$theSmarty->assign('cssFiles', self::$cssFiles);
    self::$theSmarty->assign('jsFiles', self::$jsFiles);
    self::$theSmarty->assign('templateName', $templateName);
    self::$theSmarty->display('layout.tpl');
  }

  static function addCss($id) {
    switch ($id) {
    case 'jcrop':
      self::$cssFiles[] = 'jcrop/jquery.Jcrop.min.css';
      break;
    case 'jqgrid':
      self::$cssFiles[] = 'ui.jqgrid.css';
      break;
    case 'jqueryui-smoothness':
      self::$cssFiles[] = 'smoothness/jquery-ui-1.8.18.custom.css';
      break;
    case 'main':
      self::$cssFiles[] = 'main.css?v=11';
      break;
    default:
      FlashMessage::add("Cannot load CSS file {$id}");
      Util::redirect(Util::$wwwRoot);
    }
  }

  static function addJs($id) {
    switch ($id) {
    case 'datepicker':
      self::$jsFiles[] = 'jquery.ui.datepicker-ro.js';
      break;
    case 'jcrop':
      self::$jsFiles[] = 'jquery.Jcrop.min.js';
      break;
    case 'jquery':
      self::$jsFiles[] = 'jquery-1.7.1.min.js';
      break;
    case 'jqueryui':
      self::$jsFiles[] = 'jquery-ui-1.8.18.custom.min.js';
      break;
    case 'jqgrid':
      self::$jsFiles[] = 'grid.locale-ro.js';
      self::$jsFiles[] = 'jquery.jqGrid.min.js';
      break;
    case 'main':
      self::$jsFiles[] = 'main.js';
      break;
    default:
      FlashMessage::add("Cannot load JS script {$id}");
      Util::redirect(Util::$wwwRoot);
    }
  }
}

?>
