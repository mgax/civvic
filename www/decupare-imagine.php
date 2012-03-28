<?php

require_once '../lib/Util.php';
Util::requireAdmin();

$ci = array();
$ci['name'] = Util::getRequestParameter('cropName');
$ci['monitorNumber'] = Util::getRequestParameter('number');
$ci['monitorYear'] = Util::getRequestParameter('year');
$ci['monitorPage'] = Util::getRequestParameter('page');
$ci['zoom'] = Util::getRequestParameter('zoom');
$ci['x0'] = Util::getRequestParameter('x0');
$ci['y0'] = Util::getRequestParameter('y0');
$ci['width'] = Util::getRequestParameter('width');
$ci['height'] = Util::getRequestParameter('height');

$imageName = Util::getRequestParameter('imageName');
$pageGrabButton = Util::getRequestParameter('pageGrabButton');
$cropButton = Util::getRequestParameter('cropButton');

if ($pageGrabButton) {
  $imageName = PdfUtil::convertPageToPng($ci);
}

if ($cropButton && validate($ci) && cropFrom($imageName, $ci)) {
  FlashMessage::add('Imaginea a fost salvată.', 'info');
  Util::redirect('imagini');
}

SmartyWrap::addCss('jcrop');
SmartyWrap::addJs('jcrop');
SmartyWrap::assign('ci', $ci);
SmartyWrap::assign('imageName', $imageName);
SmartyWrap::assign('pageTitle', 'Decupează o imagine');
SmartyWrap::display('decupare-imagine.tpl');

/*************************************************************************/

function validate($ci) {
  if (mb_strlen($ci['name']) < 3) {
    FlashMessage::add('Numele imaginii trebuie să aibă minim 3 caractere.');
  }
  $invalid = false;
  for ($i = 0; $i < mb_strlen($ci['name']); $i++) {
    $char = mb_substr($ci['name'], $i, 1);
    if (!ctype_alnum($char) && strpos('-._/', $char) === false) {
      $invalid = true;
    }
  }
  if ($invalid) {
    FlashMessage::add('Numele imaginii poate conține doar litere, cifre și semnele .-_');
  }
  if (!$ci['width'] || !$ci['height']) {
    FlashMessage::add('Trebuie să selectați o regiune din imagine.');
  }
  return !FlashMessage::getMessage();
}

function cropFrom($pdfImageName, $ci) {
  $tmpDir = Config::get('pdfImage.tmpImageDir');
  $pdfFileName = "{$tmpDir}/{$pdfImageName}.png";
  $src = imagecreatefrompng($pdfFileName);
  $dst = imagecreatetruecolor($ci['width'], $ci['height']);
  imagecopy($dst, $src, 0, 0, $ci['x0'], $ci['y0'], $ci['width'], $ci['height']);
  $filename = sprintf("%s/www/img/%s/%s.png", Util::$rootPath, Config::get('pdfImage.croppedImageDir'), $ci['name']);
  if (@file_exists($filename)) {
    FlashMessage::add("Există deja un fișier cu numele {$ci['name']}.");
    return false;
  }
  if (!@imagepng($dst, $filename, 9)) {
    FlashMessage::add('Nu pot salva imaginea în fișier.');
    return false;
  }
  exec("optipng {$filename}");
  return true;
}

?>
