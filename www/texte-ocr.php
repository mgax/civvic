<?php

require_once '../lib/Util.php';
Util::requireAdmin();

SmartyWrap::assign('pageTitle', "Texte OCR");
SmartyWrap::display('texte-ocr.tpl');

?>
