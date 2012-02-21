<?php

require_once '../lib/Util.php';

SmartyWrap::assign('monitors', Model::factory('Monitor')->order_by_asc('year')->order_by_asc('number')->find_many());
SmartyWrap::assign('pageTitle', 'Monitoare Oficiale');
SmartyWrap::display('monitoare.tpl');

?>
