<?php

class PdfUtil {

  static function getPageCount($fileName) {
    return exec("pdfinfo '$fileName' | grep ^Pages | awk '{print $2}'");
  }

  static function getPageSize($fileName) {
    $text = exec("pdfinfo '$fileName' | grep ^Page\\ size | awk '{print $3,$5;}'");
    return explode(' ', $text);
  }

  /* Returns the image file name (without the .png extension), or false on all errors. */
  static function convertPageToPng($pdfFileName, $page, $width = 800) {
    $dir = Config::get('pdfImage.tmpImageDir');
    @mkdir($dir, 0755, true);
    $imgName = StringUtil::randomCapitalLetters(10);
    list($w0, $h0) = self::getPageSize($pdfFileName);
    $height = (int)($width * $h0 / $w0);

    $output = array();
    $returnCode = false;
    $cmd = "pdftoppm -f {$page} -l {$page} -png -scale-to-x {$width} -scale-to-y {$height} '{$pdfFileName}' {$dir}/$imgName";
    exec($cmd, $output, $returnCode);
    if ($returnCode) {
      return false;
    }
    exec("mv {$dir}/{$imgName}*.png {$dir}/{$imgName}.png");
    return $imgName;
  }

}

?>
