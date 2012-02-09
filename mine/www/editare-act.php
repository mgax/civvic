<?php

require_once '../lib/Util.php';
Util::requireAdmin();

$id = Util::getRequestParameter('id');
$deleteId = Util::getRequestParameter('deleteId');
$name = Util::getRequestParameter('name');
$year = Util::getRequestParameter('year');
$actTypeId = Util::getRequestParameter('actTypeId');
$status = Util::getRequestParameter('status');
$submitButton = Util::getRequestParameter('submitButton');

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
} else {
  $act = Model::factory('Act')->create();
}

if ($submitButton) {
  $act->name = $name;
  $act->year = $year;
  $act->actTypeId = $actTypeId;
  if ($act->validate()) {
    $act->save();
    FlashMessage::add('Datele au fost salvate.', 'info');
    Util::redirect('acte');
  }
}

SmartyWrap::assign('act', $act);
SmartyWrap::assign('actTypes', Model::factory('ActType')->order_by_asc('name')->find_many());
SmartyWrap::assign('pageTitle', $act->id ? "Act: $act->name" : 'Act');
SmartyWrap::display('editare-act.tpl');

?>
