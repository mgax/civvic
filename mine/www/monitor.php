<?php

require_once '../lib/Util.php';

$id = Util::getRequestParameter('id');

$monitor = Monitor::get_by_id($id);

SmartyWrap::assign('monitor', $monitor);
SmartyWrap::assign('acts', Model::factory('Act')->where('monitorId', $monitor->id)->order_by_asc('name')->find_many());
SmartyWrap::assign('pageTitle', "Monitorul Oficial nr. {$monitor->number} / {$monitor->year}");
SmartyWrap::display('monitor.tpl');

?>
