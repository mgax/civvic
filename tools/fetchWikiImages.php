<?php

require_once __DIR__ . '/../lib/Util.php';

$xml = MediaWikiParser::getAllImages();

foreach ($xml->img as $i) {
  $name = (string)$i['name'];
  $url = (string)$i['url'];
  $dir = sprintf("%s/www/img/%s", Util::$rootPath, Config::get('pdfImage.croppedImageDir'));
  if (StringUtil::endsWith($name, '.png')) {
    $filename = "{$dir}/{$name}";
    if (!file_exists($filename)) {
      $contents = file_get_contents($url);
      file_put_contents($filename, $contents);
      print("Saved $name to $filename\n");
    }
  } else if (StringUtil::endsWith($name, '.jpg')) {
    $filename = "{$dir}/" . preg_replace("/.jpg$/", ".png", $name);
    $tmpFile = '/tmp/temp.jpg';
    @unlink($tmpFile);
    if (!file_exists($filename)) {
      $contents = file_get_contents($url);
      file_put_contents($tmpFile, $contents);
      exec("convert $tmpFile $filename");
      exec("optipng $filename");
      print("Saved $name to $filename\n");
    }
  } else {
    die ("Cannot handle extension {$name}\n");
  }
}

?>
