<?php

require_once '../lib/Util.php';

$actId = Util::getRequestParameter('actId');
$act = Act::get_by_id($actId);

if ($act) {
  Util::redirect("act?id={$act->id}");
}

FlashMessage::add('Actul cerut nu există în baza noastră de date.');
Util::redirect('index');

?>
