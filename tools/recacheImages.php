<?php

require_once __DIR__ . '/../lib/Util.php';

exec(sprintf("rm -rf %s/www/img/%s/*.png", Util::$rootPath, Config::get('pdfImage.croppedImageDir')));

$cis = Model::factory('CroppedImage')->find_many();
foreach ($cis as $ci) {
  print "Caching image {$ci->name}\n";
  $ci->ensureFile();
}

?>
