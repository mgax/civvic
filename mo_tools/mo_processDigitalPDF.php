<?php

require_once('phplib/cmdLine.php');

$args = cl_getArguments(array("input:", "output:"), array("input", "output"));
if (!$args) {
  usage();
}

$contents = file_get_contents($args['input']) or die("Cannot find specified input file.\n");
$contents = fixCharset($contents);
file_put_contents($args['output'], $contents) or die("Cannot write specified output file.\n");

/*************************************************************************/

function usage() {
  print "Required arguments:\n";
  print "  --input       Input file name (should contain pdftotext output)\n";
  print "  --output      Output file name\n";
  exit();
}

function fixCharset($s) {
  return str_replace(array('Ñ', 'Ð', 'ã', 'Ã', 'º', 'ª', 'þ', 'Þ'),
                     array('-', '-', 'ă', 'Ă', 'ș', 'Ș', 'ț', 'Ț'),
                     $s);
}

?>
