<?php

require_once '../../lib/Util.php';
Util::requireAdmin();

$ORDERS = array('id' => 'id',
                'number' => 'cast(number as unsigned)',
                'year' => 'year',
                'pageCount' => 'pageCount',
                'difficulty' => 'difficulty',
                'progress' => 'progress',
                'openId' => 'identity');

$rows = Util::getRequestParameter('rows');
$page = Util::getRequestParameter('page');
$sidx = Util::getRequestParameter('sidx');
$sord = Util::getRequestParameter('sord');
$filters = Util::getRequestParameter('filters');

// Where clause
$whereClauses = array(); // Start with a true clause
if ($filters) {
  $filters = json_decode($filters);
  foreach ($filters->rules as $rule) {
    switch ($rule->field) {
    case 'number':
      $whereClauses[] = "number like '%{$rule->data}%'"; break;
    case 'year':
      $whereClauses[] = "year like '%{$rule->data}%'"; break;
    case 'pageCount':
      $whereClauses[] = "pageCount like '%{$rule->data}%'"; break;
    case 'difficulty':
      $values = array(-1); // So we never have an empty set
      foreach (RawText::$difficulties as $value => $text) {
        if (strpos($text, $rule->data) !== false) {
          $values[] = $value;
        }
      }
      $whereClauses[] = 'difficulty in (' . implode(',', $values) . ')';
      break;
    case 'progress':
      $values = array(-1); // So we never have an empty set
      foreach (RawText::$progresses as $value => $text) {
        if (strpos($text, $rule->data) !== false) {
          $values[] = $value;
        }
      }
      $whereClauses[] = 'progress in (' . implode(',', $values) . ')';
      break;
    case 'openId':
      $whereClauses[] = "identity like '%{$rule->data}%'"; break;
    }
  }
}

$query = Model::factory('RawText')->left_outer_join('user', 'raw_text.userId = user.id');
if ($whereClauses) {
  $query = $query->where_raw(implode(" and ", $whereClauses));
}
$out->records = $query->count();
$out->total = (int)($out->records / $rows) + ($out->records % $rows > 0 ? 1 : 0);
$out->page = max(1, min($out->total, $page));
$out->rows = array();

$offset = ($out->page - 1) * $rows;

$query = Model::factory('RawText')->select('raw_text.id')->select('number')->select('year')->select('pageCount')->select('difficulty')
  ->select('progress')->select('identity')->left_outer_join('user', 'raw_text.userId = user.id');
if ($whereClauses) {
  $query = $query->where_raw(implode(" and ", $whereClauses));
}
$query = ($sord == 'asc') ? $query->order_by_asc($ORDERS[$sidx]) : $query->order_by_desc($ORDERS[$sidx]);
$data = $query->limit($rows)->offset($offset)->find_many();

foreach ($data as $rt) {
  $row = array();
  $row['id'] = $rt->id;
  $row['number'] = $rt->number;
  $row['year'] = $rt->year;
  $row['pageCount'] = $rt->pageCount;
  $row['difficulty'] = RawText::$difficulties[$rt->difficulty];
  $row['progress'] = RawText::$progresses[$rt->progress];
  $row['openId'] = $rt->identity;
  $out->rows[] = $row;
}

print json_encode($out);

?>
