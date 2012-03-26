<?php

class PdfUtil {
  static $defaultWidth = 800;

  static function getPageCount($fileName) {
    return exec("pdfinfo '$fileName' | grep ^Pages | awk '{print $2}'");
  }

  static function getAspectRatio($fileName) {
    $text = exec("pdfinfo '$fileName' | grep ^Page\\ size | awk '{print $3,$5;}'");
    list($width, $height) = explode(' ', $text);
    return $width / $height;
  }

  /* Takes a CroppedImage object and generates a PNG file. */
  /* Returns the image file name (without the .png extension), or false on all errors. */
  static function convertPageToPng($ci) {
    if (!ctype_digit($ci->zoom) || $ci->zoom < 10 || $ci->zoom > 500) {
      FlashMessage::add('Valorile pentru zoom sunt între 10% și 500%.');
      return false;
    }

    $pdfFileName = Monitor::getArchiveFileName($ci->monitorNumber, $ci->monitorYear);
    if (!$pdfFileName) {
      FlashMessage::add('Monitorul cerut nu există.');
      return false;
    }

    $pageCount = PdfUtil::getPageCount($pdfFileName);
    if (!ctype_digit($ci->monitorPage) || $ci->monitorPage < 1 || $ci->monitorPage > $pageCount) {
      FlashMessage::add("Pagina trebuie să fie între 1 și {$pageCount}.");
      return false;
    }

    $dir = Config::get('pdfImage.tmpImageDir');
    @mkdir($dir, 0755, true);
    $imgName = StringUtil::randomCapitalLetters(10);
    $aspectRatio = self::getAspectRatio($pdfFileName);
    $width = (int)(self::$defaultWidth * $ci->zoom / 100);
    $height = (int)($width / $aspectRatio);

    $output = array();
    $returnCode = false;
    $cmd = "pdftoppm -f {$ci->monitorPage} -l {$ci->monitorPage} -png -scale-to-x {$width} -scale-to-y {$height} '{$pdfFileName}' {$dir}/$imgName";
    exec($cmd, $output, $returnCode);
    if ($returnCode) {
      FlashMessage::add("Comanda de conversie a dat eroare: {$cmd}");
      return false;
    }
    exec("mv {$dir}/{$imgName}*.png {$dir}/{$imgName}.png");
    return $imgName;
  }

}

?>
