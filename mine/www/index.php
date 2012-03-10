<?php

require_once '../lib/Util.php';

SmartyWrap::assign('lawYears', Act::listYears('Lege'));
SmartyWrap::assign('decreeYears', Act::listYears('Decret'));
SmartyWrap::assign('dlYears', Act::listYears('Decret-lege'));
SmartyWrap::assign('ordYears', Act::listYears('Hotărâre'));
SmartyWrap::assign('pageTitle', 'Legislația României');
SmartyWrap::display('index.tpl');

?>
