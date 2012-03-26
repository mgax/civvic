<?php

require_once '../../lib/Util.php';
Util::requireAdmin();

$name = Util::getRequestParameter('name');
if (!ctype_upper($name)) {
  Util::redirect(Util::$wwwRoot);
}

$dir = Config::get('pdfImage.tmpImageDir');

header('Content-Type: image/png');
@readfile("$dir/{$name}.png");


?>
