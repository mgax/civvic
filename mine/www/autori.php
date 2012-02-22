<?php

require_once '../lib/Util.php';
Util::requireAdmin();

SmartyWrap::assign('authors', Author::loadAllOrderByDisplayName());
SmartyWrap::assign('pageTitle', 'Autori');
SmartyWrap::display('autori.tpl');

?>
