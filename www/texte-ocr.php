<?php

require_once '../lib/Util.php';
Util::requireAdmin();

SmartyWrap::addCss('jqgrid');
SmartyWrap::addJs('jqgrid');
SmartyWrap::assign('pageTitle', "Texte OCR");
SmartyWrap::display('texte-ocr.tpl');

?>
