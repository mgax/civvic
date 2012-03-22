<?php

require_once '../lib/Util.php';
Util::hideRequestParameters('submitButton');

$id = Util::getRequestParameter('id');
$version = Util::getRequestParameter('version');

$act = Act::get_by_id($id);
if ($version) {
  $shownAv = Model::factory('ActVersion')->where('actId', $id)->where('versionNumber', $version)->find_one();
} else {
  $shownAv = Model::factory('ActVersion')->where('actId', $id)->where('current', true)->find_one();
}
$actType = ActType::get_by_id($act->actTypeId);

SmartyWrap::assign('act', $act);
SmartyWrap::assign('shownAv', $shownAv);
SmartyWrap::assign('modifyingAct', Act::get_by_id($shownAv->modifyingActId));
SmartyWrap::assign('versions', $shownAv = Model::factory('ActVersion')->where('actId', $id)->order_by_asc('versionNumber')->find_many());
SmartyWrap::assign('actType', $actType);
SmartyWrap::assign('monitor', Monitor::get_by_id($act->monitorId));
SmartyWrap::assign('authors', Author::getForActId($act->id));
SmartyWrap::assign('pageTitle', "{$actType->artName} {$act->number} / {$act->year}");
SmartyWrap::display('act.tpl');

?>
