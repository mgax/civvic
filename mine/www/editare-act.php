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
$submitButton = Util::getRequestParameter('submitButton');

$versionPlacement = Util::getRequestParameter('versionPlacement');
$otherVersionNumber = Util::getRequestParameter('otherVersionNumber');
$addVersionButton = Util::getRequestParameter('addVersionButton');

if ($deleteId) {
  $act = Act::get_by_id($deleteId);
  if ($act) {
    $act->delete();
    FlashMessage::add('Actul a fost șters.', 'info');
  } else {
    FlashMessage::add('Actul cerut nu există.', 'warning');
  }
  Util::redirect('acte');
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
    $av = Model::factory('ActVersion')->create();
    // Populate as much information as possible
    if (!$numVersions) {
      $av->actId = $act->id;
      $av->modifyingActId = $act->id;
      $av->status = ACT_STATUS_VALID;
      $av->contents = '';
      $av->diff = '';
      $av->versionNumber = 1;
      $av->current = true;
    } else {
      // ******************* Populate this version, shift the order of other versions, copy the diff etc.
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
  if ($act->validate()) {
    $act->save();
    FlashMessage::add('Datele au fost salvate.', 'info');
    Util::redirect('acte');
  }
}

if ($act->id) {
  SmartyWrap::assign('numVersions', $numVersions);
}

SmartyWrap::assign('act', $act);
SmartyWrap::assign('actTypes', Model::factory('ActType')->order_by_asc('name')->find_many());
SmartyWrap::assign('actVersions', Model::factory('ActVersion')->where('actId', $act->id)->order_by_asc('versionNumber')->find_many());
SmartyWrap::assign('pageTitle', $act->id ? "Act: $act->name" : 'Act');
SmartyWrap::display('editare-act.tpl');

?>
