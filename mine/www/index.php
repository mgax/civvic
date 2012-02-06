<?php

require_once '../lib/Util.php';

SmartyWrap::assign('pageTitle', 'Legislația României');
SmartyWrap::display('index.tpl');

$u = User::get_by_id(1);
$u->email = "eeeeee" . rand(1000, 2000);
$u->save();

?>
