<?php

require_once '../lib/Util.php';
Util::requireAdmin();

$ci = Model::factory('CroppedImage')->create();
$ci->name = Util::getRequestParameter('cropName');
$ci->monitorNumber = Util::getRequestParameter('number');
$ci->monitorYear = Util::getRequestParameter('year');
$ci->monitorPage = Util::getRequestParameter('page');
$ci->zoom = Util::getRequestParameter('zoom');
$ci->x0 = Util::getRequestParameter('x0');
$ci->y0 = Util::getRequestParameter('y0');
$ci->width = Util::getRequestParameter('width');
$ci->height = Util::getRequestParameter('height');

$imageName = Util::getRequestParameter('imageName');
$pageGrabButton = Util::getRequestParameter('pageGrabButton');
$cropButton = Util::getRequestParameter('cropButton');

if ($pageGrabButton) {
  $imageName = PdfUtil::convertPageToPng($ci);
}

if ($cropButton) {
  if ($ci->validate()) {
    $ci->cropFrom($imageName);
    $ci->save();
    FlashMessage::add('Imaginea a fost salvată.', 'info');
    Util::redirect('imagini');
  }
}

SmartyWrap::addCss('jcrop');
SmartyWrap::addJs('jcrop');
SmartyWrap::assign('ci', $ci);
SmartyWrap::assign('imageName', $imageName);
SmartyWrap::assign('pageTitle', 'Decupează o imagine');
SmartyWrap::display('decupare-imagine.tpl');

?>
