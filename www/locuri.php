<?php

require_once '../lib/Util.php';
Util::requireAdmin();

SmartyWrap::assign('places', Model::factory('Place')->order_by_asc('name')->find_many());
SmartyWrap::assign('pageTitle', 'Locuri');
SmartyWrap::display('locuri.tpl');

?>
