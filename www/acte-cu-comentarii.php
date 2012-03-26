<?php

require_once '../lib/Util.php';
Util::requireAdmin();

$acts = Model::factory('Act')->where_not_equal('comment', '')->order_by_asc('year')->find_many();

SmartyWrap::assign('acts', $acts);
SmartyWrap::assign('pageTitle', "Acte cu comentarii");
SmartyWrap::display('acte-cu-comentarii.tpl');

?>
