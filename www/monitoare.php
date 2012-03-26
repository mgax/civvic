<?php

require_once '../lib/Util.php';

$yearMap = array();
$monitors = Model::factory('Monitor')->raw_query('select * from monitor order by cast(number as unsigned)', null)->find_many();

foreach ($monitors as $m) {
  if (!array_key_exists($m->year, $yearMap)) {
    $yearMap[$m->year] = array();
  }
  $yearMap[$m->year][] = $m;
}
krsort($yearMap);

SmartyWrap::assign('yearMap', $yearMap);
SmartyWrap::assign('pageTitle', 'Monitoare Oficiale');
SmartyWrap::display('monitoare.tpl');

?>
