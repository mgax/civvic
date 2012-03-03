<?php

require_once '../lib/Util.php';
Util::requireAdmin();

$refs = Model::factory('Reference')
  ->raw_query('select * from reference where referredActId is null order by year, cast(number as unsigned)', null)->find_many();
$acts = array();

foreach ($refs as $ref) {
  $av = ActVersion::get_by_id($ref->actVersionId);
  $acts[] = Act::get_by_id($av->actId);
}

SmartyWrap::assign('refs', $refs);
SmartyWrap::assign('acts', $acts);
SmartyWrap::assign('actTypes', ActType::mapById());
SmartyWrap::assign('pageTitle', "Acte inexistente");
SmartyWrap::display('acte-inexistente.tpl');

?>
