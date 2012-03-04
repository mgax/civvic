<?php

require_once '../lib/Util.php';
Util::requireAdmin();

$number = Util::getRequestParameter('number');
$year = Util::getRequestParameter('year');
$previewedNumber = Util::getRequestParameter('previewedNumber');
$previewedYear = Util::getRequestParameter('previewedYear');
$submitButton = Util::getRequestParameter('submitButton');

if ($submitButton) {
  $data = MediaWikiParser::importMonitor($number, $year);
  if ($data) {
    $monitor = $data['monitor'];
    $acts = $data['acts'];
    $actVersions = $data['actVersions'];

    if ($previewedNumber == $number && $previewedYear == $year) {
      $monitor->save();
      foreach ($acts as $i => $act) {
        $act->monitorId = $monitor->id();
        $act->save();
        $av = $actVersions[$i];
        $av->actId = $av->modifyingActId = $act->id;
        $av->save();
      }
      MediaWikiParser::maybeProtectMonitor($number, $year);
      FlashMessage::add('Monitorul a fost importat.', 'info');
      Util::redirect("monitor?id={$monitor->id}");
    }

    $authors = array();
    foreach ($acts as $act) {
      $authors[] = Author::get_by_id($act->authorId);
    }

    foreach ($actVersions as $av) {
      $av->htmlContents = MediaWikiParser::wikiToHtml($av->contents);
    }

    SmartyWrap::assign('monitor', $monitor);
    SmartyWrap::assign('acts', $acts);
    SmartyWrap::assign('actVersions', $actVersions);
    SmartyWrap::assign('authors', $authors);
    FlashMessage::add("Această pagină este o previzualizare. Dacă totul arată bine, apăsați din nou butonul 'Importă'.", 'warning');
  }
}

SmartyWrap::assign('number', $number);
SmartyWrap::assign('year', $year);
SmartyWrap::assign('pageTitle', 'Importare Monitor Oficial');
SmartyWrap::display('importare-monitor.tpl');

?>
