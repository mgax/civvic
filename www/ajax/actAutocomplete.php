<?php

require_once '../../lib/Util.php';

define('AUTOCOMPLETE_LIMIT', 10);

$term = Util::getRequestParameter('term');
$ref = Util::getRequestParameter('ref');

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
$acts = $query->limit(AUTOCOMPLETE_LIMIT)->find_many();

$results = array();
foreach ($acts as $a) {
  if ($a->number && $a->year) {
    $str = sprintf("%s/%s %s", $a->number, $a->year, $a->name);
  } else {
    $str = sprintf("%s %s", $a->issueDate, $a->name);
  }
  $results[] = array('id' => $a->id, 'label' => $str);
}

if ($ref && (count($results) < AUTOCOMPLETE_LIMIT)) {
  // Get acts which we don't have, but to which other acts refer
  $clauses = array();
  if (count($numbers)) {
    $clauses[] = sprintf("(act_reference.number = '%s')", $numbers[0]);
  }
  if (count($numbers) > 1) {
    $clauses[] = sprintf("(act_reference.year like '%s%%')", $numbers[1]);
  }
  foreach ($other as $word) {
    $clauses[] = "(act_type.name like '%{$word}%' or act_type.artName like '%{$word}%' or act_type.genArtName like '%{$word}%')";
  }
  $query = sprintf("select distinct act_type.id, act_type.artName, act_reference.number, act_reference.year " .
                   "from act_type, act_reference, act_version, act " .
                   "where act_type.id = act_reference.actTypeId and act_reference.actVersionId = act_version.id and act_version.actId = act.id " .
                   "and referredActId is null and %s order by act.issueDate limit %d",
                   implode(" and ", $clauses), AUTOCOMPLETE_LIMIT - count($results));
  $dbResult = Db::execute($query, PDO::FETCH_ASSOC);
  foreach ($dbResult as $row) {
    $results[] = array('id' => 0,
                       'label' => sprintf("%s %s/%s (inexistent)", $row['artName'], $row['number'], $row['year']),
                       'ref' => sprintf("%s:%s:%s", $row['id'], $row['number'], $row['year']));
  }
}

print json_encode($results);

?>
