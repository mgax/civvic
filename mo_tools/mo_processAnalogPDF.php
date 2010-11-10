<?php

require_once('phplib/cmdLine.php');
require_once('phplib/string.php');
require_once('phplib/sys.php');

define('TMP_DIR', '/tmp/');
define('TESSDATA_PREFIX', '/home/cata/Desktop/tesseract-3.0/');
define('TESSERACT_BINARY', '/home/cata/Desktop/tesseract-3.0/api/tesseract');

$args = cl_getArguments(array("input:", "output:"), array("input", "output"));
if (!$args) {
  usage();
}

$text = pdfToText($args['input']);
$text = fixCharset($text);
file_put_contents($args['output'], $text) or die("Cannot write specified output file.\n");

/*************************************************************************/

function usage() {
  print "Required arguments:\n";
  print "  --input       Input file name (should be a PDF file)\n";
  print "  --output      Output file name\n";
  exit();
}

function fixCharset($s) {
  $s = str_replace(array('ä', 'Ä', 'ã', 'Ã', 'ş', 'Ş', 'ţ', 'Ţ'),
                   array('ă', 'Ă', 'ă', 'Ă', 'ș', 'Ș', 'ț', 'Ț'),
                   $s);
  $s = preg_replace("/\b(S|s)(i|î)nt(em|eti|eți)?\b/", "$1unt$3", $s);
  return $s;
}

function pdfToText($pdfFilename) {
  $result = '';
  $ppmBaseName = tempnam(TMP_DIR, 'mo_');
  sys_executeAndAssert("pdftoppm '$pdfFilename' $ppmBaseName");

  $files = scandir(TMP_DIR);
  foreach ($files as $file) {
    $file = TMP_DIR . $file;
    if (string_startsWith($file, $ppmBaseName)) {
      if (string_endsWith($file, '.ppm')) {
        print "Running tesseract on {$file}\n";
        sys_executeAndAssert("TESSDATA_PREFIX='" . TESSDATA_PREFIX . "' " . TESSERACT_BINARY . " $file $file -l ron");
        $result .= file_get_contents("{$file}.txt");
        unlink("{$file}.txt");
      }
      unlink($file);
    }
  }
  return $result;
}

?>
