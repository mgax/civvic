<?php

require_once '../../lib/Util.php';
Util::requireLoggedIn();

SmartyWrap::assign('pageTitle', 'Contul meu');
SmartyWrap::display('auth/contul-meu.tpl');


?>
