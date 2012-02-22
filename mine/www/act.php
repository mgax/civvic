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

SmartyWrap::assign('act', $act);
SmartyWrap::assign('shownAv', $shownAv);
SmartyWrap::assign('versions', $shownAv = Model::factory('ActVersion')->where('actId', $id)->order_by_asc('versionNumber')->find_many());
SmartyWrap::assign('actType', ActType::get_by_id($act->actTypeId));
SmartyWrap::assign('monitor', Monitor::get_by_id($act->monitorId));
SmartyWrap::assign('author', Author::get_by_id($act->authorId));
SmartyWrap::assign('pageTitle', "Act: $act->name");
SmartyWrap::display('act.tpl');

?>
