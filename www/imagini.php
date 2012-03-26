<?php

require_once '../lib/Util.php';
Util::requireAdmin();

// Jumping through some hoops so as not to store all the contents in memory at once
$imageIds = Model::factory('CroppedImage')->select('id')->order_by_asc('name')->find_many();
$images = array();
foreach ($imageIds as $image) {
  $i = CroppedImage::get_by_id($image->id);
  $i->ensureFile();
  $i->contents = '';
  $images[] = $i;
}

SmartyWrap::assign('images', $images);
SmartyWrap::assign('croppedDir', Config::get('pdfImage.croppedImageDir'));
SmartyWrap::assign('pageTitle', 'Imagini');
SmartyWrap::display('imagini.tpl');

?>
