<?php

require_once '../lib/Util.php';
Util::requireAdmin();

SmartyWrap::assign('actTypes', Model::factory('ActType')->order_by_asc('name')->find_many());
SmartyWrap::assign('pageTitle', 'Tipuri de acte');
SmartyWrap::display('tipuri-acte.tpl');

?>
