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

$out->records = Model::factory('RawText')->count();
$out->total = (int)($out->records / $rows) + ($out->records % $rows > 0 ? 1 : 0);
$out->page = max(1, min($out->total, $page));
$out->rows = array();

$offset = ($out->page - 1) * $rows;

$data = Model::factory('RawText')
  ->raw_query(sprintf("select raw_text.id, number, year, pageCount, difficulty, progress, identity " .
                      "from raw_text left outer join user on raw_text.userId = user.id " .
                      "order by %s %s limit %s offset %s", $ORDERS[$sidx], $sord, $rows, $offset), null)
  ->find_many();

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
