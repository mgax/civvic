<?php

require_once '../lib/Util.php';

SmartyWrap::assign('acts', Model::factory('Act')->order_by_asc('name')->find_many());
SmartyWrap::assign('pageTitle', 'Acte');
SmartyWrap::display('acte.tpl');

?>
