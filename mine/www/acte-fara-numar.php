<?php

require_once '../lib/Util.php';
Util::requireAdmin();

$acts = Model::factory('Act')->where_raw("number = '' or number is null or number like 'FN%'")
  ->order_by_asc('id')->find_many();

SmartyWrap::assign('acts', $acts);
SmartyWrap::assign('pageTitle', "Acte fără număr");
SmartyWrap::display('acte-fara-numar.tpl');

?>
