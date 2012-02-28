<?php

require_once '../lib/Util.php';

$id = Util::getRequestParameter('id');

$monitor = Monitor::get_by_id($id);
$acts = Model::factory('Act')->raw_query("select * from act where monitorId = {$id} order by actTypeId, cast(number as unsigned)", null)
  ->find_many();

SmartyWrap::assign('monitor', $monitor);
SmartyWrap::assign('acts', $acts);
SmartyWrap::assign('pageTitle', "Monitorul Oficial nr. {$monitor->number} / {$monitor->year}");
SmartyWrap::display('monitor.tpl');

?>
