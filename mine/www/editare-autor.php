<?php

require_once '../lib/Util.php';
Util::requireAdmin();

$id = Util::getRequestParameter('id');
$deleteId = Util::getRequestParameter('deleteId');
$institution = Util::getRequestParameter('institution');
$position = Util::getRequestParameter('position');
$title = Util::getRequestParameter('title');
$name = Util::getRequestParameter('name');
$submitButton = Util::getRequestParameter('submitButton');

if ($deleteId) {
  $author = Author::get_by_id($deleteId);
  if ($author) {
    $author->delete();
    FlashMessage::add('Autorul a fost șters.', 'info');
  } else {
    FlashMessage::add('Autorul cerut nu există.', 'warning');
  }
  Util::redirect('autori');
}

if ($id) {
  $author = Author::get_by_id($id);
} else {
  $author = Model::factory('Author')->create();
}

if ($submitButton) {
  $author->institution = $institution;
  $author->position = $position;
  $author->title = $title;
  $author->name = $name;
  if ($author->validate()) {
    $author->save();
    FlashMessage::add('Datele au fost salvate.', 'info');
    Util::redirect('autori');
  }
}

SmartyWrap::assign('author', $author);
SmartyWrap::assign('pageTitle', $author->id ? ('Autor: ' . $author->getDisplayName()) : 'Autor');
SmartyWrap::display('editare-autor.tpl');

?>
