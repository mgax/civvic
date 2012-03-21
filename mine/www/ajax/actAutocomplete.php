<?php

require_once '../../lib/Util.php';
Util::requireAdmin();

$term = Util::getRequestParameter('term');

// Split the term into words and separate up to two numbers
$words = preg_split("/(\\s|\\/)+/", $term);
$numbers = array();
$other = array();
foreach ($words as $w) {
  if (count($numbers) < 2 && ctype_digit($w)) {
    $numbers[] = $w;
  } else {
    $other[] = $w;
  }
}

$query = Model::factory('Act')->select('act.*')->join('act_type', 'actTypeId = act_type.id');
if (count($numbers)) {
  $query->where('number', $numbers[0]);
}
if (count($numbers) > 1) {
  $query->where_like('year', $numbers[1] . '%');
}
foreach ($other as $word) {
  $query->where_raw("(act.name like '%{$word}%' or act_type.name like '%{$word}%' or artName like '%{$word}%' or genArtName like '%{$word}%')");
}
$acts = $query->limit(10)->find_many();

$results = array();
foreach ($acts as $a) {
  if ($a->number && $a->year) {
    $str = sprintf("%s/%s %s", $a->number, $a->year, $a->name);
  } else {
    $str = sprintf("%s %s", $a->issueDate, $a->name);
  }
  $results[] = array('id' => $a->id, 'label' => $str);
}

print json_encode($results);

?>
