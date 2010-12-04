<?php

require_once('phplib/config.php');
require_once('phplib/cmdLine.php');
require_once('phplib/db.php');
require_once('phplib/activeRecords.php');
require_once('phplib/string.php');
require_once('phplib/sys.php');

$I_PREFIXES = array('ne', 're', 'micro', 'sub', 'bine', 'dez', 'nemai', 'pre', 'semi', 'supra');

db_init(CONF_DATABASE_TOOLS);

$args = cl_getArguments(array("input:", "output:"), array("input", "output"));
if (!$args) {
  usage();
}

$text = pdfToText($args['input']);
$text = fixDocument($text);
file_put_contents($args['output'], $text) or die("Cannot write specified output file.\n");

/*************************************************************************/

function usage() {
  print "Required arguments:\n";
  print "  --input       Input file name (should be a PDF file)\n";
  print "  --output      Output file name\n";
  exit();
}

function fixDocument($s) {
  $s = str_replace(array('ä', 'Ä', 'ã', 'Ã', 'å', 'Å', 'ş', 'Ş', 'ţ', 'Ţ', '~', '_', 'í', 'ì', '“'),
                   array('ă', 'Ă', 'ă', 'Ă', 'ă', 'Ă', 'ș', 'Ș', 'ț', 'Ț', '-', '-', 'i', 'i', '”'),
                   $s);
  $s = str_replace('--', '-', $s);
  $s = preg_replace("/\b-\n\b/", '', $s);
  $s = preg_replace("/\b(S|s)(i|î)nt(em|eti|eți)?\b/", "$1unt$3", $s);
  $s = preg_replace("/\bin\b/", 'în', $s);
  $s = preg_replace("/\bIn\b/", 'În', $s);
  $s = preg_replace("/\bsi\b/", 'și', $s);
  $s = preg_replace("/\bSi\b/", 'Și', $s);
  $s = preg_replace("/ind\b/", 'ând', $s);
  $s = preg_replace("/privând\b/", 'privind', $s);
  $s = preg_replace("/\n-/", "\n*", $s);
  $s = preg_replace("/ ([;:?!.,])/", "$1", $s);
  $s = fixWords($s);
  return $s;
}

/**
 * Tokenize the string and try to improve each word individually.
 **/
function fixWords($s) {
  $len = mb_strlen($s);
  $result = '';
  $token = '';
  for ($i = 0; $i < $len; $i++) {
    $c = string_getCharAt($s, $i);
    if (string_isUnicodeLetter($c)) {
      $token .= $c;
    } else {
      if ($token !== '') {
        $result .= processToken($token);
        $token = '';
      }
      $result .= $c;
    }
  }
  return $result;
}

function processToken($token) {
  global $I_PREFIXES;

  if (mb_strlen($token) <= 1) {
    return $token; // Ignore single letters
  }

  $origToken = $token;
  // Replace î with â except (1) at the beginning or end of the token; (2) when prefixed by bine-, dez-, micro-, ne-, nemai-, pre-, re-, semi-, sub-, supra-
  $i = 0;
  $pos = false;
  $len = mb_strlen($token);
  while (($pos = mb_strpos($token, 'î', $i)) !== false) {
    if (($pos > 0) && ($pos < $len - 1) && !in_array(mb_substr($token, 0, $pos), $I_PREFIXES)) {
      $token = mb_substr($token, 0, $pos) . 'â' . mb_substr($token, $pos + 1);
    }
    $i = $pos + 1;
  }
  if ($token != $origToken) {
    print "Replacing [$origToken] with [$token]\n";
  }

  //print "Token: [$token]\n";
  $words = db_find(new Word(), "form = '{$token}'");
  if (count($words)) {
    return $token; // It's a valid word
  }
  $words = db_find(new Word(), "formUtf8General = '{$token}'");
  if (count($words)) {
    // TODO: try to find a frequent one
    print "Replacing [{$token}] with [" . $words[0]->form . "]\n";
    return $words[0]->form;
  }
  // Try to find a (preferably frequent) word at Levenshtein distance 1
  $words = db_find(new Word(), "dist2('{$token}', formUtf8General) order by frequent desc");
  if (count($words)) {
    print "       Replacing [{$token}] with [" . $words[0]->form . "]\n";
    return $words[0]->form;
  }
  return $token; // No better replacement
}

function pdfToText($pdfFilename) {
  $result = '';
  $ppmBaseName = tempnam(CONF_TMP_DIR, 'mo_');
  $command = "pdftoppm '$pdfFilename' $ppmBaseName";
  print "Command: $command\n";
  sys_executeAndAssert($command);

  $files = scandir(CONF_TMP_DIR);
  foreach ($files as $file) {
    $file = CONF_TMP_DIR . $file;
    if (string_startsWith($file, $ppmBaseName)) {
      if (string_endsWith($file, '.ppm')) {
        print "Running tesseract on {$file}\n";
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
