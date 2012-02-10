<?php

require_once '../lib/Util.php';
Util::requireAdmin();

$id = Util::getRequestParameter('id');
$deleteId = Util::getRequestParameter('deleteId');
$modifyingActId = Util::getRequestParameter('modifyingActId');
$status = Util::getRequestParameter('status');
$contents = Util::getRequestParameter('contents');
$submitButton = Util::getRequestParameter('submitButton');

if ($deleteId) {
  $av = ActVersion::get_by_id($deleteId);
  if ($av) {
    $av->delete();
    FlashMessage::add('Versiunea a fost ștearsă.', 'info');
  } else {
    FlashMessage::add('Versiunea cerută nu există.', 'warning');
  }
  Util::redirect("editare-act?id={$av->actId}");
}

$av = ActVersion::get_by_id($id);

if ($submitButton) {
  $av->modifyingActId = $modifyingActId;
  $av->status = $status;
  $av->contents = $contents;
  if ($av->validate()) {
    $av->save();
    FlashMessage::add('Datele au fost salvate.', 'info');
    Util::redirect("editare-act?id={$av->actId}");
  }
}

SmartyWrap::assign('av', $av);
SmartyWrap::assign('act', Act::get_by_id($av->actId));
SmartyWrap::assign('acts', Model::factory('act')->order_by_asc('year')->order_by_asc('number')->find_many());
SmartyWrap::assign('actStatuses', Act::$statuses);
SmartyWrap::assign('actTypes', ActType::mapById());
SmartyWrap::assign('pageTitle', "Versiune: $av->versionNumber");
SmartyWrap::display('editare-versiune-act.tpl');

?>
