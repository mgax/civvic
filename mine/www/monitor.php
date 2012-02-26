<?php

require_once '../lib/Util.php';

$id = Util::getRequestParameter('id');

$monitor = Monitor::get_by_id($id);
$acts = Model::factory('Act')->where('monitorId', $monitor->id)->order_by_asc('actTypeId')->order_by_asc('number')->find_many();

SmartyWrap::assign('monitor', $monitor);
SmartyWrap::assign('acts', $acts);
SmartyWrap::assign('pageTitle', "Monitorul Oficial nr. {$monitor->number} / {$monitor->year}");
SmartyWrap::display('monitor.tpl');

?>
