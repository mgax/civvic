<?php

require_once '../lib/Util.php';
Util::requireAdmin();

$id = Util::getRequestParameter('id');
$deleteId = Util::getRequestParameter('deleteId');
$name = Util::getRequestParameter('name');
$submitButton = Util::getRequestParameter('submitButton');

if ($deleteId) {
  $place = Place::get_by_id($deleteId);
  if ($place) {
    if ($place->delete()) {
      FlashMessage::add('Locul a fost șters.', 'info');
      Util::redirect('locuri');
    } else {
      Util::redirect("editare-loc?id={$place->id}");
    }
  } else {
    FlashMessage::add('Locul cerut nu există.', 'warning');
    Util::redirect('locuri');
  }
}

if ($id) {
  $place = Place::get_by_id($id);
} else {
  $place = Model::factory('Place')->create();
}

if ($submitButton) {
  $place->name = $name;
  if ($place->validate()) {
    $place->save();
    FlashMessage::add('Datele au fost salvate.', 'info');
    Util::redirect('locuri');
  }
}

SmartyWrap::assign('place', $place);
SmartyWrap::assign('pageTitle', $place->id ? "Loc: $place->name" : 'Loc');
SmartyWrap::display('editare-loc.tpl');

?>
