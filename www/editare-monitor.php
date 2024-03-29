<?php

require_once '../lib/Util.php';
Util::requireAdmin();

$id = Util::getRequestParameter('id');
$deleteId = Util::getRequestParameter('deleteId');
$year = Util::getRequestParameter('year');
$number = Util::getRequestParameter('number');
$issueDate = Util::getRequestParameter('issueDate');
$submitButton = Util::getRequestParameter('submitButton');

if ($deleteId) {
  $m = Monitor::get_by_id($deleteId);
  if ($m) {
    if ($m->delete()) {
      FlashMessage::add('Monitorul a fost șters.', 'info');
      Util::redirect('monitoare');
    } else {
      Util::redirect("editare-monitor?id={$m->id}");
    }
  } else {
    FlashMessage::add('Monitorul cerut nu există.', 'warning');
    Util::redirect('monitoare');
  }
}

if ($id) {
  $m = Monitor::get_by_id($id);
} else {
  $m = Model::factory('Monitor')->create();
}

if ($submitButton) {
  // Avoid dirtying the fields unless necessary
  if ($year != $m->year) {
    $m->year = $year;
  }
  if ($number != $m->number) {
    $m->number = $number;
  }
  $m->issueDate = $issueDate;
  if ($m->validate()) {
    $m->save();
    FlashMessage::add('Datele au fost salvate.', 'info');
    Util::redirect('monitoare');
  }
}

SmartyWrap::assign('monitor', $m);
SmartyWrap::assign('pageTitle', $m->id ? "Monitorul Oficial {$m->number} / {$m->year}" : 'Monitor Oficial');
SmartyWrap::display('editare-monitor.tpl');

?>
