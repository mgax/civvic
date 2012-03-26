<?php 

require_once '../../lib/Util.php';
Util::requireNotLoggedIn();

$openid = Util::getRequestParameter('openid');

switch ($openid) {
case 'google': $openid = "https://www.google.com/accounts/o8/id"; break;
case 'yahoo': $openid = "http://yahoo.com/"; break;
}

if ($openid) {
  $success = OpenID::beginAuth($openid, null);
  if (!$success) {
    Util::redirect('login');
  }
  SmartyWrap::display('auth/beginAuth.tpl');
  exit;
}

SmartyWrap::assign('openid', $openid);
SmartyWrap::assign('pageTitle', 'Autentificare cu OpenID');
SmartyWrap::display('auth/login.tpl');

?>
