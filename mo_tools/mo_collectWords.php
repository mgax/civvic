#!/usr/bin/php
<?

/**
 * Parse the MO archive and extract text from PDFs. If the PDFs do contain
 * text, then build a dictionary from the words.
 **/

// If pdftotext produces fewer than PDF_HAS_TEXT_THRESHOLD bytes, then it probably contains images, not text
define('PDF_HAS_TEXT_THRESHOLD', 1024);

mb_internal_encoding("UTF-8");

$wordMap = array();

$opts = getopt(null, array('root:', 'resume:', 'output:', 'broken:'));
if (!isset($opts['root']) || !isset($opts['output']) || !isset($opts['broken'])) {
  usage();
}

if (isset($opts['resume'])) {
  $wordMap = loadWordMap($opts['resume']);
}
recursiveScan($opts['root'], $opts['broken']);
saveWordMap($wordMap, $opts['output']);

function usage() {
  print "Required arguments:\n";
  print "--root           Root of PDF directory\n";
  print "--resume         Load word map from this file\n";
  print "--output         Output file\n";
  print "--broken         Move broken PDFs to this directory\n";
  exit(1);
}

function recursiveScan($path, $brokenPath) {
  global $wordMap;

  $files = scandir($path);
  foreach ($files as $file) {
    $full = "$path/$file";
    if ($file == '.' || $file == '..') {
      // Skip these
    } else if (is_dir($full)) {
      recursiveScan($full);
    } else if (substr($full, -4) == '.pdf') {
      $text = getTextFromPdf($path, $file, $brokenPath);
      if ($text) {
        processWords($text);
        print "Processed $full... word map has " . count($wordMap) . " entries\n";
      }
    } else {
      // Unknown file type
    }
  }
}

function getTextFromPdf($path, $pdfFilename, $brokenPath) {
  $returnCode = executeAndReturnCode("pdftotext '$path/$pdfFilename' /tmp/pdf.txt");
  if ($returnCode) {
    // Something went wrong; move the file to the broken file directory
    print "$path/$pdfFilename is broken, moving to $brokenPath/$pdfFilename\n";
    rename("$path/$pdfFilename", "$brokenPath/$pdfFilename");
  } else {
    $lines = file("/tmp/pdf.txt");
    unlink("/tmp/pdf.txt");
    $result = postProcessing($lines);
    return $result;
  }
}

function postProcessing(&$lines) {
  $i = 0;
  while ($i < count($lines)) {
    if (stripos($lines[$i], 'pdfcompressor') && stripos($lines[$i], 'cvision')) {
      array_splice($lines, $i, 1);
    } else {
      $i++;
    }
  }
  $result = implode("\n", $lines);
  $result = trim($result);
  $result = str_replace(array('ş', 'ţ', 'Ş', 'Ţ', 'º', 'þ', 'ª', 'Þ'),
                        array('ș', 'ț', 'Ș', 'Ț', 'ș', 'ț', 'Ș', 'Ț'), $result);
  $result = mb_strtolower($result);
  return $result;
}

function processWords($text) {
  global $wordMap;
  $words = preg_split("/\s+/", $text);
  foreach ($words as $word) {
    $word = stripPunctuation($word);
    if ($word && isRomanianText($word)) {
      $prev = isset($wordMap[$word]) ? $wordMap[$word] : 0;
      $wordMap[$word] = $prev + 1;
    }
  }
}

function stripPunctuation($word) {
  while ($word && strpos('.,;:"”)]', $word[strlen($word) - 1]) !== false) {
    $word = substr($word, 0, strlen($word) - 1);
  }
  while ($word && strpos('([„', $word[0]) !== false) {
    $word = substr($word, 1);
  }
  return $word;
}

// Can't get setlocale and ctype_alpha to work.
function isRomanianText($s) {
  $len = mb_strlen($s);
  for ($i = 0; $i < $len; $i++) {
    $char = mb_substr($s, $i, 1);
    if (!ctype_alpha($char) && (mb_strpos('ăâîșțĂÂÎȘȚ', $char) === false)) {
      return false;
    }
  }
  return true;
}

function loadWordMap($filename) {
  $result = array();
  $lines = file($filename, FILE_IGNORE_NEW_LINES);
  foreach ($lines as $line) {
    $parts = preg_split('/\s+/', $line);
    assert(count($parts) == 2);
    $result[$parts[0]] = $parts[1];
  }
  return $result;
}

function saveWordMap(&$wordMap, $output) {
  asort($wordMap);

  $f = fopen($output, 'w');
  foreach ($wordMap as $word => $count) {
    fwrite($f, "$word $count\n");
  }
  fclose($f);
}

function executeAndReturnCode($command) {
  // print "Executing: $command\n";
  $output = array();
  $returnCode = 0;
  exec($command, $output, $returnCode);
  return $returnCode;
}

?>