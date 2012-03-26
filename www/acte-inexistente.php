<?php

require_once '../lib/Util.php';
Util::requireAdmin();

$refs = Model::factory('Reference')
  ->raw_query('select * from reference where referredActId is null group by actTypeId, number, year order by year, cast(number as unsigned)', null)->find_many();
$acts = array();

SmartyWrap::assign('refs', $refs);
SmartyWrap::assign('actTypes', ActType::mapById());
SmartyWrap::assign('pageTitle', "Acte inexistente");
SmartyWrap::display('acte-inexistente.tpl');

?>
