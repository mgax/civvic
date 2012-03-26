<?php

require_once '../lib/Util.php';

// Load all the acts and map them by year
$acts = Model::factory('Act')->raw_query('select * from act order by actTypeId, year desc, cast(number as unsigned)', null)->find_many();
$actMap = array();
foreach ($acts as $act) {
  $actMap[$act->year][] = $act;
}
krsort($actMap);

SmartyWrap::assign('actMap', $actMap);
SmartyWrap::assign('pageTitle', 'Acte');
SmartyWrap::display('acte.tpl');

?>
