<?php

/**
 * This script applies data migration patches, including SQL code and PHP scripts.
 * Overview:
 * - Looks in the Variable table for Schema.version, which is a 5-digit number;
 * - Reads files of the form "patches/%d%d%d%d%d.extension" in increasing numberical order;
 * - Ignores files older than, or equal to, Schema.version;
 * - Files with the .sql extension are piped into SQL;
 * - Files with the .php extension are executed within this script;
 * - Files with other extensions are ignored.
 *
 * Use with the --dry-run to see what the script would do without changing anything.
 **/

require_once __DIR__ . '/../lib/Util.php';

define('PATCH_DIR', realpath(__DIR__ . '/../patches/'));
define('PATCH_REGEXP', '/^\d{5}\./');

$dryRun = false;
foreach ($argv as $i => $arg) {
  if ($i) {
    switch ($arg) {
    case '--dry-run': $dryRun = true; break;
    default: die("Unknown flag $arg\n");
    }
  }
}

if ($dryRun) {
  print "---- DRY RUN ONLY ----\n";
}
$schemaVersion = Db::tableExists('variable') ? Variable::peek('Schema.version', '00000') : '00000';
print "Current schema version is <$schemaVersion>\n";

$patchFiles = getPatches(PATCH_DIR, $schemaVersion);
$numPatches = 0;
foreach ($patchFiles as $fileName) {
  runPatch(PATCH_DIR . '/' . $fileName, $dryRun);
  $numPatches++;
  $schemaVersion = stripExtension($fileName);
  if (!$dryRun) {
    // Update after each patch, in case one of the patches terminates with error.
    Variable::poke('Schema.version', $schemaVersion);
  }
}
print "$numPatches patches applied.\n";
print "New schema version is <$schemaVersion>\n";

/*****************************************************************/

function getPatches($dir, $after) {
  $result = array();
  if ($dirHandle = opendir($dir)) {
    while (($fileName = readdir($dirHandle)) !== false) {
      if (preg_match(PATCH_REGEXP, $fileName) && stripExtension($fileName) > $after && !StringUtil::endsWith($fileName, '~')) {
        $result[] = $fileName;
      }
    }
    closedir($dirHandle);
    sort($result);
  }
  return $result;
}

function runPatch($fileName, $dryRun) {
  $extension = strrchr($fileName, '.');
  if ($extension == '.sql') {
    print "$fileName -- executing with MySQL\n";
    if (!$dryRun) {
      Db::executeSqlFile($fileName);
    }
  } else if ($extension == '.php') {
    print "$fileName -- executing with PHP\n";
    if (!$dryRun) {
      require $fileName;
    }
  } else {
    print "$fileName -- unknown extension, ignoring\n";
  }
}

function stripExtension($fileName) {
  $dot = strrpos($fileName, '.');
  return ($dot === false) ? $fileName : substr($fileName, 0, $dot);
}

?>
