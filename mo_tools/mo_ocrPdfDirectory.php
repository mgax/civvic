<?php

require_once('phplib/config.php');
require_once('phplib/cmdLine.php');
require_once('phplib/db.php');
require_once('phplib/activeRecords.php');
require_once('phplib/string.php');
require_once('phplib/sys.php');

define('TYPE_ANALOG', 1);
define('TYPE_DIGITAL', 2);
define('ANALOG_SCRIPT', './mo_tools/mo_processAnalogPDF.php');
define('ANALOG_SCRIPT_VERSION', getAnalogScriptVersion());
db_init(CONF_DATABASE_TOOLS);

$args = cl_getArguments(array("dir:"), array("dir"));
if (!$args) {
  usage();
}

recursiveScan($args['dir']);                                                          
                                                                                
/*************************************************************************/

function getAnalogScriptVersion() {
  $output = sys_executeAndReturnOutput('php ' . ANALOG_SCRIPT . ' --version');
  return $output[0];
}

function recursiveScan($path) {                                         
  $files = scandir($path);                                                      
                                                                                
  foreach ($files as $file) {
    if ($file == '.' || $file == '..') {
      continue;
    }
    $full = "$path/$file";

    if (is_dir($full)) {
      recursiveScan($full);
    }

    if (string_endsWith($full, '.pdf') || string_endsWith($full, '.PDF')) {
      $md5Sum = md5_file($full);
      $rawText = RawText::get("pdfMd5 = '$md5Sum'");
      if ($rawText) {
	print "$full already parsed as {$rawText->scriptType}, {$rawText->issue}/{$rawText->year}\n";
      } else {
	print "Processing $full\n";
	$arr = getYearAndIssueFromFileName($full);
	$type = getDocumentType($full);
	if ($type == TYPE_ANALOG) {
	  $text = getOcrText($full);
	  $rawText = new RawText();
	  $rawText->year = $arr['year'];
	  $rawText->issue = $arr['issue'];
	  $rawText->pdfMd5 = $md5Sum;
	  $rawText->extractedText = $text;
	  $rawText->scriptType = 'analog';
	  $rawText->scriptVersion = ANALOG_SCRIPT_VERSION;
	  $rawText->save();

	  $pdfDocument = new PdfDocument();
	  $pdfDocument->rawTextId = $rawText->id;
	  $pdfDocument->contents = file_get_contents($full);
	  $pdfDocument->save();
	  print "$full saved as analog {$rawText->issue}/{$rawText->year} md5=$md5Sum\n";
	} else {
	  print "Cannot handle digital documents yet, skipping\n";
	}
      }
    }
  }
}

function getYearAndIssueFromFileName($fullPath) {
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

// Decide whether we need to use the analog script (OCR) or the digital script (pdftotext)
// See how much text pdftotext extracts
function getDocumentType($fullName) {
  $output = sys_executeAndReturnOutput("pdfinfo $fullName |grep Pages|awk '{print $2}'");
  $numPages = $output[0];
  sys_executeAndAssert("pdftotext $fullName /tmp/output.txt");
  $filesize = filesize("/tmp/output.txt");
  $bytesPerPage = $filesize / $numPages;
  return ($bytesPerPage > 300) ? TYPE_DIGITAL : TYPE_ANALOG;
}

function getOcrText($fullName) {
  sys_executeAndAssert('php ' . ANALOG_SCRIPT . " --input {$fullName} --output /tmp/analogOutput.txt");
  return file_get_contents('/tmp/analogOutput.txt');
}

function usage() {
  print "Required arguments:\n";
  print "  --dir         Directory containing PDF files\n";
  exit();
}

?>