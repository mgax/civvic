<?php

require_once '../lib/Util.php';
Util::requireAdmin();

SmartyWrap::addCss('elfinder');
SmartyWrap::addJs('elfinder');
SmartyWrap::assign('pageTitle', 'Imagini');
SmartyWrap::display('imagini.tpl');

?>
