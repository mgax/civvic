<?php

require_once '../lib/Util.php';
Util::requireAdmin();

$id = Util::getRequestParameter('id');
$plain = Util::getRequestParameter('plain');

$rt = RawText::get_by_id($id);

if ($plain) {
  header('Content-type: text/plain');
  print($rt->extractedText);
  exit;
}

SmartyWrap::assign('rawText', $rt);
SmartyWrap::assign('owner', User::get_by_id($rt->userId));
SmartyWrap::assign('difficulty', RawText::$difficulties[$rt->difficulty]);
SmartyWrap::assign('progress', RawText::$progresses[$rt->progress]);
SmartyWrap::assign('pageTitle', "Text OCR: {$rt->number}/{$rt->year}");
SmartyWrap::display('text-ocr.tpl');

?>
