<?php

require_once '../../lib/Util.php';

$type = Util::getRequestParameter('type');
$year = Util::getRequestParameter('year');

$actType = ActType::get_by_shortName($type);
$acts = Model::factory('Act')->where('actTypeId', $actType->id)->where('year', $year)->find_many();

$results = array();
foreach ($acts as $a) {
  $results[] = array($a->id, $a->number . '. ' . StringUtil::shortenString($a->name, 50));
}

print json_encode($results);


?>
