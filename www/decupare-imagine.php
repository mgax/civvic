<?php

require_once '../lib/Util.php';
Util::requireAdmin();

$number = Util::getRequestParameter('number');
$year = Util::getRequestParameter('year');
$page = Util::getRequestParameter('page');
$pageGrabButton = Util::getRequestParameter('pageGrabButton');

if ($pageGrabButton) {
  $moPath = Monitor::getArchiveFileName($number, $year);
  if (!$moPath) {
    FlashMessage::add('Monitorul cerut nu există.');
  } else {
    $pageCount = PdfUtil::getPageCount($moPath);
    if (!ctype_digit($page) || $page < 1 || $page > $pageCount) {
      FlashMessage::add("Pagina trebuie să fie între 1 și {$pageCount}.");
    } else {
      $imageName = PdfUtil::convertPageToPng($moPath, $page);
      if (!$imageName) {
        FlashMessage::add("Nu pot genera imaginea PNG.");
      } else {
        SmartyWrap::assign('imageName', $imageName);
      }
    }
  }
}

SmartyWrap::assign('number', $number);
SmartyWrap::assign('year', $year);
SmartyWrap::assign('page', $page);
SmartyWrap::assign('pageTitle', 'Decupează o imagine');
SmartyWrap::display('decupare-imagine.tpl');

?>
