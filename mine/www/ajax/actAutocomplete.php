<?php

require_once '../../lib/Util.php';
Util::requireAdmin();

$term = Util::getRequestParameter('term');

$matches = array();
if (preg_match("/^(?P<before>[^0-9]*)\\s*(?P<number>\\d+)\\s*\\/\\s*(?P<year>\\d*)\\s*(?P<after>.*)$/", $term, $matches)) {
  $query = Model::factory('Act')->where('number', $matches['number'])->where_like('year', $matches['year'] . '%');
  if ($matches['before']) {
    $query = $query->where_like('name', '%' . trim($matches['before']) . '%');
  }
  if ($matches['after']) {
    $query = $query->where_like('name', '%' . trim($matches['after']) . '%');
  }
} else {
  $query = Model::factory('Act')->where_like('name', "%{$term}%");
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
