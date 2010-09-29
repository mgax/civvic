<?
/**
 * Copyright 2010 Cătălin Frâncu <cata@francu.com>
 *
 * This file is part of Civvic.
 *
 * Civvic is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Civvic is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Civvic.  If not, see <http://www.gnu.org/licenses/>.
 **/

/**
 * Parse the MO archive and extract text from PDFs. If the PDFs do contain text, then build a dictionary from the words.
 **/

define('PDF_HAS_TEXT_THRESHOLD', 1024); // If pdftotext produces fewer than PDF_HAS_TEXT_THRESHOLD bytes, then it probably contains images, not text
define('SAVE_EVERY', 100);      // Checkpoint the word map after every SAVE_EVERY PDF files
mb_internal_encoding("UTF-8");

$opts = parseOptions();
$wordMap = isset($opts['resume-wordmap']) ? loadWordMap($opts['resume-wordmap']) : array();
$saveCount = 0;
recursiveScan('', $saveCount, $opts);
saveWordMap($wordMap, $opts['output-dir']);

/*************************************************************************/

function parseOptions() {
  $opts = getopt(null, array('root:', 'resume-wordmap:', 'resume-pdf:', 'output-dir:', 'broken:', 'save-every:'));
  if (!isset($opts['root']) || !isset($opts['output-dir']) || !isset($opts['broken'])) {
    usage();
  }
  if (!isset($opts['save-every'])) {
    $opts['save-every'] = SAVE_EVERY;
  }
  return $opts;
}

function usage() {
  print "Required arguments:\n";
  print "--broken          Move broken PDFs to this directory\n";
  print "--output-dir      Output directory (files are numbered sequentially)\n";
  print "--root            Root of PDF directory\n";
  print "\n";
  print "Optional arguments:\n";
  print "--resume-pdf      Parse PDFs beginning with this file (alphabetically)\n";
  print "--resume-wordmap  Load word map from this file\n";
  print "--save-every      Save wordmap after every n PDF files processed\n";
  exit(1);
}

function recursiveScan($relPath, &$saveCount, &$opts) {
  global $wordMap;
  $root = $opts['root'];

  $files = scandir("$root/$relPath");
  foreach ($files as $file) {
    if ($file == '.' || $file == '..') {
      continue;
    }
    if (isset($opts['resume-pdf']) && ("$relPath/$file" < $opts['resume-pdf']) && !startsWith($opts['resume-pdf'], "$relPath/$file")) {
      continue;
    }

    $filePath = "$root/$relPath/$file";
    if (is_dir($filePath)) {
      recursiveScan("$relPath/$file", $saveCount, $opts);
    } else if (substr($file, -4) == '.pdf') {
      $saveCount++;
      $text = getTextFromPdf($root, $opts['broken'], $relPath, $file);
      if ($text) {
        processWords($text);
        print "File: $root/$relPath/$file [$saveCount/" . $opts['save-every'] . "] wordmap has " . count($wordMap) . " entries\n";
      }
      if ($saveCount == $opts['save-every']) {
        saveWordMap($wordMap, $opts['output-dir']);
        $saveCount = 0;
      }
    } else {
      // Unknown file type
    }
  }
}

function getTextFromPdf($root, $broken, $relPath, $filename) {
  $returnCode = executeAndReturnCode("pdftotext '$root/$relPath/$filename' /tmp/pdf.txt");
  if ($returnCode) {
    // Something went wrong; move the file to the broken file directory
    print "$root/$relPath/$filename is broken, copying to $broken/$relPath/$filename\n";
    exec("mkdir -p '$broken/$relPath'");
    rename("$root/$relPath/$filename", "$broken/$relPath/$filename");
    return null;
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

function saveWordMap(&$wordMap, $outputDir) {
  asort($wordMap);
  exec("mkdir -p '$outputDir'");

  $files = scandir("$outputDir");
  $lastFile = $files[count($files) - 1];
  $file = ($lastFile == '..') ? '00000' : sprintf("%05d", $lastFile + 1);

  $f = fopen("$outputDir/$file", 'w');
  foreach ($wordMap as $word => $count) {
    fwrite($f, "$word $count\n");
  }
  fclose($f);
  print "Saved wordmap to $outputDir/$file\n";
}

function executeAndReturnCode($command) {
  // print "Executing: $command\n";
  $output = array();
  $returnCode = 0;
  exec($command, $output, $returnCode);
  return $returnCode;
}

function startsWith($str, $substr) {
  return (substr($str, 0, strlen($substr)) == $substr);
}

?>