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

// Simple wrapper that breaks a pdf into png images, then calls mo_split.php
// to extract the blocks from each page. Given a $directory, deposits the blocks in
// $directory/year(4-digits)/issue/page(3-digits)/blockno_....png
// and each individual page in $directory/year(4-digits)/issue/page(3-digits).png
// Assumes that the original file structure ends with /year/issue.pdf

define('TMP_DIR_NAME', '/tmp/mo_split_batch');


$opts = getopt(null, array('file:', 'output-dir:'));

if (!isset($opts['file']) || !isset($opts['output-dir'])) {
  usage();
}

$filename = $opts['file'];
$outputDir = $opts['output-dir'];
list($year, $issue) = analyzeFileName($filename);
$issueDir = "$outputDir/$year/$issue";
print "Year is $year; issue is $issue\n";
executeAndAssert("rm -rf " . TMP_DIR_NAME);
executeAndAssert("mkdir -p " . TMP_DIR_NAME);
if (file_exists($issueDir)) {
  print "Directory $issueDir already exists, so skipping this issue.\n";
  wrapUp();
}
executeAndAssert("mkdir -p $issueDir");
executeAndAssert("pdftoppm -r 300 -png $filename " . TMP_DIR_NAME . "/page");

$files = scandir(TMP_DIR_NAME);
$count = 0;
foreach ($files as $file) {
  if ($file[0] != '.') {
    $count++;
    $fullFilename = TMP_DIR_NAME . "/$file";

    // Extract the blocks from the file
    $blockPrefix = sprintf("%s/blocks%03d/", $issueDir, $count);
    executeAndAssert("mkdir -p $blockPrefix");
    executeAndAssert("mo_split.php --file $fullFilename --block-prefix $blockPrefix");
    executeAndAssert("for i in $blockPrefix/*.png; do optipng \$i; done");

    // Move the file to the output directory
    executeAndAssert(sprintf("mv %s %s/page%03d.png", $fullFilename, $issueDir, $count));
    executeAndAssert(sprintf("optipng %s/page%03d.png", $issueDir, $count));
  }
}
wrapUp();

/***************************************************************************/

function usage() {
  print "Required arguments:\n";
  print "--file           PDF file to split\n";
  print "--output-dir     Output directory\n";
  exit(1);
}

function analyzeFileName($filename) {
  $fullPath = realPath($filename);
  $components = preg_split('/\//', $fullPath);
  $basename = basename(array_pop($components), '.pdf');
  $year = array_pop($components);
  return array($year, $basename);
}

function wrapUp() {
  executeAndAssert("rm -rf " . TMP_DIR_NAME);
  exit(0);
}

function executeAndAssert($command) {
  print "Executing: $command\n";
  $output = array();
  $returnCode = 0;
  exec($command, $output, $returnCode);
  if ($returnCode) {
    print "Command exited unsuccessfully. Output follows:\n";
    print_r($output);
    exit(1);
  }
}

?>
