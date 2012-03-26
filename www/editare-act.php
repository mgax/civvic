<?php

require_once '../lib/Util.php';
Util::requireAdmin();

$id = Util::getRequestParameter('id');
$deleteId = Util::getRequestParameter('deleteId');
$name = Util::getRequestParameter('name');
$year = Util::getRequestParameter('year');
$number = Util::getRequestParameter('number');
$issueDate = Util::getRequestParameter('issueDate');
$actTypeId = Util::getRequestParameter('actTypeId');
$authors = Util::getRequestParameter('authors');
$monitorId = Util::getRequestParameter('monitorId');
$placeId = Util::getRequestParameter('placeId');
$submitButton = Util::getRequestParameter('submitButton');

$versionPlacement = Util::getRequestParameter('versionPlacement');
$otherVersionNumber = Util::getRequestParameter('otherVersionNumber');
$addVersionButton = Util::getRequestParameter('addVersionButton');

if ($deleteId) {
  $act = Act::get_by_id($deleteId);
  if ($act) {
    if ($act->delete()) {
      FlashMessage::add('Actul a fost șters.', 'info');
      Util::redirect('acte');
    } else {
      Util::redirect("editare-act?id={$act->id}");
    }
  } else {
    FlashMessage::add('Actul cerut nu există.', 'warning');
    Util::redirect('acte');
  }
}

if ($id) {
  $act = Act::get_by_id($id);
  $numVersions = $act->countVersions();
} else {
  $act = Model::factory('Act')->create();
}

if ($addVersionButton) {
  if ($numVersions > 0 && !StringUtil::isNumberBetween($otherVersionNumber, 1, $numVersions)) {
    FlashMessage::add("Numărul versiunii trebuie să fie între 1 și $numVersions");
  } else if (!$numVersions && $otherVersionNumber != '0') {
    FlashMessage::add("Numărul versiunii trebuie să fie 0, deoarece încă nu există versiuni.");
  } else {
    if ($numVersions) {
      $av = ActVersion::insertVersion($act, ($versionPlacement == 'before'), $otherVersionNumber);
    } else {
      $av = ActVersion::createVersionOne($act);
    }
    $av->save();
    Util::redirect("editare-act?id={$act->id}");
    exit;
  }
}

if ($submitButton) {
  $act->name = $name;
  $act->year = $year;
  $act->number = $number;
  $act->issueDate = $issueDate;
  $act->actTypeId = $actTypeId;
  $act->monitorId = $monitorId;
  $act->placeId = $placeId;
  if ($act->validate()) {
    $act->save();
    ActAuthor::saveAuthors($act->id, $authors);
    FlashMessage::add('Datele au fost salvate.', 'info');
    Util::redirect("editare-act?id={$act->id}");
  }
}

if ($act->id) {
  SmartyWrap::assign('numVersions', $numVersions);
}

SmartyWrap::assign('act', $act);
SmartyWrap::assign('actTypes', Model::factory('ActType')->order_by_asc('name')->find_many());
SmartyWrap::assign('actVersions', Model::factory('ActVersion')->where('actId', $act->id)->order_by_asc('versionNumber')->find_many());
SmartyWrap::assign('authors', $actAuthors = Author::getForActId($act->id));
SmartyWrap::assign('monitors', Model::factory('Monitor')->order_by_asc('year')->order_by_asc('number')->find_many());
SmartyWrap::assign('places', Model::factory('Place')->order_by_asc('name')->find_many());
SmartyWrap::assign('pageTitle', $act->id ? "Act: $act->name" : 'Act');
SmartyWrap::display('editare-act.tpl');

?>
