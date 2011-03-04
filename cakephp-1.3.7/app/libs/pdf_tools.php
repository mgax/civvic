<?php

define('PDF_ANALOG_SCRIPT_VERSION', '1.2');
define('PDF_TYPE_ANALOG', 1);
define('PDF_TYPE_DIGITAL', 2);

// Decide whether we need to use the analog script (OCR) or the digital script (pdftotext).
// See how much text pdftotext extracts.
function pdf_getDocumentType($fullName) {
  $numPages = pdf_getPageCount($fullName);
  sys_executeAndAssert("pdftotext $fullName /tmp/output.txt");
  $filesize = filesize("/tmp/output.txt");
  $bytesPerPage = $filesize / $numPages;
  print "$numPages pages, $filesize text bytes, $bytesPerPage bytes per page\n";
  return ($bytesPerPage > 300) ? PDF_TYPE_DIGITAL : PDF_TYPE_ANALOG;
}

function pdf_getPageCount($fullName) {
  $output = sys_executeAndReturnOutput("pdfinfo $fullName |grep Pages|awk '{print $2}'");
  return $output[0];
}

function pdf_getYearAndIssueFromFileName($fullPath) {
  $result = array();
  $parts = preg_split('/\//', $fullPath);
  foreach($parts as $part) {
    if (is_numeric($part) && ($part >= 1900) && ($part <= 2100)) {
      $result['year'] = $part;
    }
  }
  $fileNameParts = preg_split('/\./', array_pop($parts));
  $issue = $fileNameParts[0];
  while (string_startsWith($issue, '0')) {
    $issue = substr($issue, 1);
  }
  $result['issue'] = $issue;
  return $result;
}

function pdf_ocr($pdfFilename) {
  $result = '';
  $ppmBaseName = tempnam(CONF_TMP_DIR, 'mo_');
  $command = "pdftoppm '$pdfFilename' $ppmBaseName";
  print "Command: $command\n";
  sys_executeAndAssert($command);

  // pdftoppm does not do any zero-padding in the resulting file names, so we do it here.
  sys_executeAndAssert("rename 's/-([0-9])\.ppm/-000$1.ppm/' {$ppmBaseName}*");
  sys_executeAndAssert("rename 's/-([0-9][0-9])\.ppm/-00$1.ppm/' {$ppmBaseName}*");
  sys_executeAndAssert("rename 's/-([0-9][0-9][0-9])\.ppm/-0$1.ppm/' {$ppmBaseName}*");

  $files = scandir(CONF_TMP_DIR);
  foreach ($files as $file) {
    $file = CONF_TMP_DIR . $file;
    if (string_startsWith($file, $ppmBaseName)) {
      if (string_endsWith($file, '.ppm')) {
        // print "Running tesseract on {$file}\n";
        $command = "TESSDATA_PREFIX='" . CONF_TESSDATA_PREFIX . "' " . CONF_TESSERACT_BINARY . " $file $file -l ron";
        print "Command: $command\n";
        sys_executeAndAssert($command);
        $result .= file_get_contents("{$file}.txt");
        unlink("{$file}.txt");
      }
      unlink($file);
    }
  }
  return $result;
}


?>
