<?php

require_once '../lib/Util.php';
Util::requireAdmin();

$id = Util::getRequestParameter('id');
$deleteId = Util::getRequestParameter('deleteId');
$modifyingActId = Util::getRequestParameter('modifyingActId');
$status = Util::getRequestParameter('status');
$contents = Util::getRequestParameter('contents');
$submitButton = Util::getRequestParameter('submitButton');
$previewButton = Util::getRequestParameter('previewButton');

if ($deleteId) {
  $av = ActVersion::get_by_id($deleteId);
  if ($av) {
    if ($av->delete()) {
      FlashMessage::add('Versiunea a fost ștearsă.', 'info');
      Util::redirect("editare-act?id={$av->actId}");
    } else {
      Util::redirect("editare-versiune-act?id={$av->id}");
    }
  } else {
    FlashMessage::add('Versiunea cerută nu există.', 'warning');
    Util::redirect("editare-act?id={$av->actId}");
  }
}

$av = ActVersion::get_by_id($id);

if ($submitButton || $previewButton) {
  $av->modifyingActId = $modifyingActId;
  $av->status = $status;
  $av->contents = StringUtil::cleanupUserInput($contents);
}

if ($previewButton) {
  $previousAv = Model::factory('ActVersion')->where('actId', $av->actId)->where('versionNumber', $av->versionNumber - 1)->find_one();
  $av->annotate($previousAv);
  $av->htmlContents = MediaWikiParser::wikiToHtml($av);
  $av->validate();
}

if ($submitButton) {
  if ($av->validate()) {
    $av->save();
    FlashMessage::add('Datele au fost salvate.', 'info');
    Util::redirect("editare-act?id={$av->actId}");
  }
}

SmartyWrap::assign('av', $av);
SmartyWrap::assign('act', Act::get_by_id($av->actId));
SmartyWrap::assign('modifyingAct', Act::get_by_id($av->modifyingActId));
SmartyWrap::assign('actStatuses', Act::$statuses);
SmartyWrap::assign('actTypes', ActType::mapById());
SmartyWrap::assign('preview', $previewButton);
SmartyWrap::assign('pageTitle', "Versiune: $av->versionNumber");
SmartyWrap::display('editare-versiune-act.tpl');

?>
